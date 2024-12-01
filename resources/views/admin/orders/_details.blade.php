<!-- Customer Information -->
<div class="border-t border-gray-200 px-4 py-5 sm:px-6">
    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
        <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_name }}</dd>
        </div>
        <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Email</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_email }}</dd>
        </div>
        <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Phone</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_phone }}</dd>
        </div>
        <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Order Type</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->order_type) }}</dd>
        </div>
        @if($order->order_type === 'delivery')
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Delivery Address</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $order->delivery_address }}</dd>
            </div>
        @else
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Pickup Time</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $order->pickup_time->format('M d, Y h:i A') }}</dd>
            </div>
        @endif
    </dl>
</div>

<!-- Order Items -->
<div class="border-t border-gray-200">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
    </div>
    <div class="border-t border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->menuItem->item_name }}</div>
                            @if($item->special_instructions)
                                <div class="text-sm text-gray-500">Note: {{ $item->special_instructions }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">£{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">£{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">£{{ number_format($order->total_amount - ($order->order_type === 'delivery' ? 2.50 : 0), 2) }}</td>
                </tr>
                @if($order->order_type === 'delivery')
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Delivery Fee:</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">£2.50</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">£{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($order->notes)
    <!-- Order Notes -->
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Order Notes</h3>
        <p class="text-sm text-gray-500">{{ $order->notes }}</p>
    </div>
@endif 