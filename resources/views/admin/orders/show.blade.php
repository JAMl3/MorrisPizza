@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
        <div class="flex space-x-4">
            <a href="{{ route('admin.orders.receipt', $order) }}" target="_blank" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Receipt
            </a>
            <a href="{{ route('admin.orders.index') }}" class="text-red-600 hover:text-red-500 flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Orders
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Order Status -->
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Status</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ $order->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <select id="status-select" 
                            class="rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                            data-order-id="{{ $order->id }}">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="button" 
                            onclick="updateOrderStatus()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Update Status
                    </button>
                </div>
            </div>
        </div>

        <!-- Order Progress -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
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
                    <div class="text-center {{ $progress >= 25 ? 'text-red-600 font-medium' : 'text-gray-500' }}">Order Received</div>
                    <div class="text-center {{ $progress >= 50 ? 'text-red-600 font-medium' : 'text-gray-500' }}">Preparing</div>
                    <div class="text-center {{ $progress >= 75 ? 'text-red-600 font-medium' : 'text-gray-500' }}">Out for Delivery</div>
                    <div class="text-center {{ $progress >= 100 ? 'text-red-600 font-medium' : 'text-gray-500' }}">Delivered</div>
                </div>
            </div>
        </div>

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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">£{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Order Summary -->
                <div class="px-6 py-4 bg-gray-50">
                    <div class="w-full max-w-xs ml-auto space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">£{{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        @if($order->discount_code)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Discount ({{ $order->discount_code }})</span>
                            <span>-£{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Fee</span>
                            <span class="text-gray-900">£{{ number_format($order->total_amount - $order->subtotal + ($order->discount_amount ?? 0), 2) }}</span>
                        </div>

                        <div class="flex justify-between text-base font-medium border-t border-gray-200 pt-2">
                            <span>Total</span>
                            <span>£{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($order->notes)
            <!-- Order Notes -->
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Order Notes</h3>
                <p class="text-sm text-gray-500">{{ $order->notes }}</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateOrderStatus() {
    const select = document.getElementById('status-select');
    const orderId = select.dataset.orderId;
    const status = select.value;

    // Disable the button and select while updating
    const button = document.querySelector('button[onclick="updateOrderStatus()"]');
    button.disabled = true;
    select.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Updating...
    `;

    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Status updated successfully', 'success');
            
            // Wait for 1 second then refresh the page
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update status', 'error');
        
        // Re-enable the button and select
        button.disabled = false;
        select.disabled = false;
        button.innerHTML = 'Update Status';
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } transition-opacity duration-500`;
    notification.textContent = message;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after 3 seconds with fade effect
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 2500);
}
</script>
@endpush
@endsection 