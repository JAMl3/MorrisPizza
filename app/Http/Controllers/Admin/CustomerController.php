<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::withCount(['orders' => function($query) {
                $query->where('status', '!=', 'cancelled')
                    ->orWhere(function($q) {
                        $q->where('status', '!=', 'cancelled')
                          ->whereRaw('LOWER(customer_email) = LOWER(users.email)');
                    });
            }])
            ->withSum(['orders' => function($query) {
                $query->where('status', '!=', 'cancelled')
                    ->orWhere(function($q) {
                        $q->where('status', '!=', 'cancelled')
                          ->whereRaw('LOWER(customer_email) = LOWER(users.email)');
                    });
            }], 'total_amount')
            ->orderByDesc('orders_count')
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        $orders = Order::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
            })
            ->with('items')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_orders' => Order::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
                })
                ->where('status', '!=', 'cancelled')
                ->count(),
            'total_spent' => Order::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
                })
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'average_order' => Order::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
                })
                ->where('status', '!=', 'cancelled')
                ->avg('total_amount') ?? 0,
            'first_order' => Order::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
                })
                ->where('status', '!=', 'cancelled')
                ->oldest()
                ->first()?->created_at,
            'last_order' => Order::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
                })
                ->where('status', '!=', 'cancelled')
                ->latest()
                ->first()?->created_at,
        ];

        Order::whereNull('user_id')
            ->whereRaw('LOWER(customer_email) = ?', [strtolower($user->email)])
            ->update(['user_id' => $user->id]);

        return view('admin.customers.show', compact('user', 'orders', 'stats'));
    }
} 