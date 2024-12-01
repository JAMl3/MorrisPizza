@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Order Header -->
        <div class="bg-red-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Order #{{ $order->id }}</h1>
                <div class="text-right">
                    <div class="text-sm">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($order->status) {
                            'completed' => 'bg-emerald-100 text-emerald-800',
                            'cancelled' => 'bg-rose-100 text-rose-800',
                            'processing' => 'bg-sky-100 text-sky-800',
                            'out_for_delivery' => 'bg-indigo-100 text-indigo-800',
                            default => 'bg-amber-100 text-amber-800'
                        } }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Progress -->
        <div class="px-6 py-4">
            <div class="relative">
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                    @php
                        $progress = match($order->status) {
                            'pending' => 25,
                            'processing' => 50,
                            'out_for_delivery' => 75,
                            'completed' => 100,
                            default => 0
                        };
                    @endphp
                    <div style="width:{{ $progress }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-500"></div>
                </div>
                <div class="flex justify-between text-sm">
                    <div class="text-center {{ $progress >= 25 ? 'text-emerald-600 font-medium' : 'text-gray-500' }}">Order Received</div>
                    <div class="text-center {{ $progress >= 50 ? 'text-sky-600 font-medium' : 'text-gray-500' }}">Preparing</div>
                    <div class="text-center {{ $progress >= 75 ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">Out for Delivery</div>
                    <div class="text-center {{ $progress >= 100 ? 'text-emerald-600 font-medium' : 'text-gray-500' }}">Delivered</div>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <!-- Customer Details -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Customer Details</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Name:</span> {{ $order->customer_name }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->customer_email }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->customer_phone }}</p>
                        @if($order->order_type === 'delivery')
                            <p><span class="font-medium">Delivery Address:</span> {{ $order->delivery_address }}</p>
                        @else
                            <p><span class="font-medium">Pickup Time:</span> {{ $order->pickup_time->format('M d, Y h:i A') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between">
                                <div>
                                    <div class="font-medium">{{ $item->menuItem->item_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        Quantity: {{ $item->quantity }}
                                        @if($item->special_instructions)
                                            <br>Note: {{ $item->special_instructions }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div>£{{ number_format($item->subtotal, 2) }}</div>
                                    <div class="text-sm text-gray-500">£{{ number_format($item->unit_price, 2) }} each</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Total -->
                    <div class="border-t mt-4 pt-4">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>£{{ number_format($order->subtotal, 2) }}</span>
                            </div>

                            @if($order->discount_code)
                            <div class="flex justify-between text-green-600">
                                <span>Discount ({{ $order->discount_code }})</span>
                                <span>-£{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif

                            <div class="flex justify-between">
                                <span class="text-gray-600">Delivery Fee</span>
                                <span>£{{ number_format($order->total_amount - $order->subtotal + ($order->discount_amount ?? 0), 2) }}</span>
                            </div>

                            <div class="flex justify-between font-bold border-t border-gray-200 pt-2">
                                <span>Total</span>
                                <span>£{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($order->notes)
            <!-- Order Notes -->
            <div class="border-t border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold mb-2">Order Notes</h3>
                <p class="text-gray-700">{{ $order->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection 