<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's stats
        $today = Carbon::today();
        $todayStats = [
            'orders' => Order::whereDate('created_at', $today)
                ->where('status', '!=', 'cancelled')
                ->count(),
            'revenue' => Order::whereRaw('date(created_at) = ?', [$today->format('Y-m-d')])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'pending_orders' => Order::whereRaw('date(created_at) = ?', [$today->format('Y-m-d')])
                ->where('status', 'pending')
                ->count(),
        ];

        // Weekly stats
        $weekStart = Carbon::now()->startOfWeek();
        $weeklyStats = [
            'orders' => Order::where('created_at', '>=', $weekStart)
                ->where('status', '!=', 'cancelled')
                ->count(),
            'revenue' => Order::where('created_at', '>=', $weekStart)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'avg_order_value' => Order::where('created_at', '>=', $weekStart)
                ->where('status', '!=', 'cancelled')
                ->avg('total_amount') ?? 0,
        ];

        // Popular items (excluding cancelled orders)
        $popularItems = MenuItem::select('menu_items.*', DB::raw('COUNT(order_items.id) as order_count'))
            ->leftJoin('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function($query) {
                $query->where('orders.status', '!=', 'cancelled')
                      ->orWhereNull('orders.status');
            })
            ->groupBy('menu_items.id')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['items', 'user'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'items_count' => $order->items->count(),
                    'created_at' => $order->created_at,
                    'user' => $order->user ? [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ] : null,
                ];
            });

        // Low stock alerts
        $lowStockItems = MenuItem::where('stock', '<', 10)
            ->where('is_available', true)
            ->get();

        // Customer stats
        $customerStats = [
            'total_customers' => User::count(),
            'new_customers_today' => User::whereDate('created_at', $today)->count(),
            'new_customers_week' => User::where('created_at', '>=', $weekStart)->count(),
        ];

        // Sales by hour (last 24 hours)
        $salesByHour = Order::where('created_at', '>=', now()->subDay())
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy(function($order) {
                return $order->created_at->format('H:00');
            })
            ->map(function($orders) {
                return $orders->count();
            })
            ->toArray();

        // Initialize missing hours with 0
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            if (!isset($salesByHour[$hour])) {
                $salesByHour[$hour] = 0;
            }
        }
        ksort($salesByHour);

        // Order status distribution (excluding cancelled)
        $orderStatusDistribution = Order::where('status', '!=', 'cancelled')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'todayStats',
            'weeklyStats',
            'popularItems',
            'recentOrders',
            'lowStockItems',
            'customerStats',
            'orderStatusDistribution',
            'salesByHour'
        ));
    }

    public function analytics(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30));
        $endDate = $request->input('end_date', Carbon::now());

        // Sales trend
        $salesTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->get();

        // Category performance
        $categoryPerformance = MenuItem::select(
                'categories.name',
                DB::raw('COUNT(order_items.id) as items_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as revenue')
            )
            ->join('categories', 'menu_items.category_id', '=', 'categories.id')
            ->leftJoin('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return view('admin.analytics', compact('salesTrend', 'categoryPerformance'));
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:sales,inventory,customers',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Generate report based on type
        switch ($request->report_type) {
            case 'sales':
                return $this->generateSalesReport($request->start_date, $request->end_date);
            case 'inventory':
                return $this->generateInventoryReport();
            case 'customers':
                return $this->generateCustomerReport($request->start_date, $request->end_date);
        }
    }

    protected function generateSalesReport($startDate, $endDate)
    {
        $orders = Order::with(['items', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Implementation for CSV generation
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report.csv"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Date', 'Customer', 'Items', 'Total', 'Status']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->created_at,
                    $order->customer_name,
                    $order->items->count(),
                    $order->total_amount,
                    $order->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 