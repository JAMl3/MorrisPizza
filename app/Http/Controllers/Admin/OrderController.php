<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'user'])
            ->latest();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $date = Carbon::parse($request->date);
            $query->whereDate('created_at', $date);
        }

        $orders = $query->paginate(20);

        // Get stats for today
        $today = Carbon::today();
        $stats = [
            'total_orders' => Order::where('status', '!=', 'cancelled')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'today_revenue' => Order::whereDate('created_at', $today)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'today_orders' => Order::whereDate('created_at', $today)
                ->where('status', '!=', 'cancelled')
                ->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['items.menuItem', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    public function receipt(Order $order)
    {
        $order->load(['items.menuItem']);
        return view('orders.receipt', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,out_for_delivery,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        // If order is cancelled, restore stock for menu items
        if ($request->status === 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->menuItem) {
                    $item->menuItem->increment('stock', $item->quantity);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }
} 