<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index');
    }

    public function show(Order $order, Request $request)
    {
        if ($order->guest_token && $request->query('token') !== $order->guest_token) {
            abort(403, 'Unauthorized access to order.');
        }

        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:delivery,pickup',
            'payment_method' => 'required|in:cash_on_delivery,cash_on_pickup',
            'delivery_address_line1' => 'required_if:order_type,delivery|nullable|string|max:255',
            'delivery_city' => 'required_if:order_type,delivery|nullable|string|max:255',
            'delivery_postcode' => 'required_if:order_type,delivery|nullable|string|max:10',
            'delivery_address_line2' => 'nullable|string|max:255',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string|max:500',
            'pickup_time' => [
                'required_if:order_type,pickup',
                'nullable',
                'date',
                'after:' . now()->addMinutes(29)->format('Y-m-d H:i:s')
            ],
            'notes' => 'nullable|string|max:500'
        ];

        // Add password validation if creating account
        if (!auth()->check() && $request->checkout_type === 'create_account') {
            $validationRules['password'] = ['required', 'string', 'min:8', 'confirmed'];
            $validationRules['customer_email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email'];
        }

        $request->validate($validationRules, [
            'pickup_time.after' => 'Pickup time must be at least 30 minutes from now.',
            'delivery_address.required_if' => 'Please provide a delivery address.',
            'pickup_time.required_if' => 'Please select a pickup time.',
        ]);

        $cart = Cart::getCurrent();
        
        if ($cart->items->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        // Validate payment method matches order type
        if ($request->order_type === 'delivery' && $request->payment_method === 'cash_on_pickup') {
            return back()->withErrors(['payment_method' => 'Cash on pickup is not available for delivery orders.']);
        }
        if ($request->order_type === 'pickup' && $request->payment_method === 'cash_on_delivery') {
            return back()->withErrors(['payment_method' => 'Cash on delivery is not available for pickup orders.']);
        }

        // Create user account if requested
        if (!auth()->check() && $request->checkout_type === 'create_account') {
            try {
                $user = \App\Models\User::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'password' => bcrypt($request->password)
                ]);

                // Create user profile with address
                if ($request->order_type === 'delivery') {
                    $user->profile()->create([
                        'default_name' => $request->customer_name,
                        'default_email' => $request->customer_email,
                        'default_phone' => $request->customer_phone,
                        'default_address_line1' => $request->delivery_address_line1,
                        'default_address_line2' => $request->delivery_address_line2,
                        'default_city' => $request->delivery_city,
                        'default_postcode' => $request->delivery_postcode,
                    ]);
                }
                
                // Manually authenticate the user
                auth()->login($user);
            } catch (\Exception $e) {
                return back()->withErrors(['email' => 'Unable to create account. Please try again.']);
            }
        }

        // Get or create guest token
        $guestToken = auth()->check() ? null : (session('guest_token', Str::random(32)));
        if (!auth()->check()) {
            session(['guest_token' => $guestToken]);
        }

        // Create the order
        $order = Order::create([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'order_type' => $request->order_type,
            'delivery_address' => $request->order_type === 'delivery' ? $request->delivery_address : null,
            'pickup_time' => $request->order_type === 'pickup' ? $request->pickup_time : null,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'notes' => $request->notes,
            'status' => 'pending',
            'subtotal' => $cart->subtotal,
            'total_amount' => $cart->total_amount,
            'guest_token' => $guestToken,
            'user_id' => auth()->id()
        ]);

        // If discount code was applied
        if ($request->filled('discount_code')) {
            $discountCode = \App\Models\DiscountCode::where('code', $request->discount_code)
                ->where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where('valid_until', '>=', now())
                ->first();

            if ($discountCode) {
                $discountAmount = ($cart->subtotal * $discountCode->discount_percentage) / 100;
                $order->update([
                    'discount_code' => $discountCode->code,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $cart->subtotal - $discountAmount + $cart->delivery_fee
                ]);
            }
        }

        foreach ($cart->items as $cartItem) {
            $order->items()->create([
                'menu_item_id' => $cartItem->menu_item_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->menuItem->price,
                'subtotal' => $cartItem->subtotal,
                'special_instructions' => $cartItem->special_instructions
            ]);
        }

        $cart->clear();

        // Send order confirmation email
        $order->notify(new \App\Notifications\OrderConfirmation($order));

        return redirect()
            ->route('orders.show', ['order' => $order, 'token' => $order->guest_token])
            ->with('success', 'Order placed successfully! You will receive a confirmation email shortly.');
    }

    public function history()
    {
        if (auth()->check()) {
            // For logged-in users, show orders by email
            $orders = Order::where('customer_email', auth()->user()->email)
                          ->latest()
                          ->paginate(10);
        } else {
            // For guests, show orders by guest token or email
            $guestToken = session('guest_token');
            $guestEmail = session('guest_email');
            
            $orders = Order::when($guestToken, function($query) use ($guestToken) {
                            return $query->where('guest_token', $guestToken);
                        })
                        ->when($guestEmail, function($query) use ($guestEmail) {
                            return $query->orWhere('customer_email', $guestEmail);
                        })
                        ->latest()
                        ->paginate(10);

            // Store the email from the latest order for future reference
            if ($orders->isNotEmpty() && !session()->has('guest_email')) {
                session(['guest_email' => $orders->first()->customer_email]);
            }
        }
        
        return view('orders.history', compact('orders'));
    }
} 