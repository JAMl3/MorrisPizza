<div class="p-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center">
                <h3 class="text-lg font-medium text-gray-900">Order #{{ $order->id }}</h3>
                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($order->status) {
                    'completed' => 'bg-emerald-100 text-emerald-800',
                    'cancelled' => 'bg-rose-100 text-rose-800',
                    'processing' => 'bg-sky-100 text-sky-800',
                    'out_for_delivery' => 'bg-indigo-100 text-indigo-800',
                    default => 'bg-amber-100 text-amber-800'
                } }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="mt-1 text-sm text-gray-500">
                {{ $order->created_at->format('M d, Y h:i A') }} • {{ $order->customer_name }}
            </div>
        </div>
        <div class="text-right">
            <div class="text-lg font-medium text-gray-900">£{{ number_format($order->total_amount, 2) }}</div>
            <div class="mt-1">
                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-red-600 hover:text-red-500">
                    View Order →
                </a>
            </div>
        </div>
    </div>
    <div class="mt-4 text-sm text-gray-500">
        @foreach($order->items as $item)
            <div>{{ $item->quantity }}x {{ $item->menuItem->item_name }}</div>
        @endforeach
    </div>
</div> 