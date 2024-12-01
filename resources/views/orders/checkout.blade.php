@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            <div class="space-y-4">
                @php
                    $total = 0;
                @endphp
                
                @foreach($cartItems as $item)
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium">{{ $item->menuItem->item_name }}</h3>
                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                            @if($item->special_instructions)
                                <p class="text-sm text-gray-600">Note: {{ $item->special_instructions }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-medium">£{{ number_format($item->menuItem->price * $item->quantity, 2) }}</p>
                        </div>
                    </div>
                    @php
                        $total += $item->menuItem->price * $item->quantity;
                    @endphp
                @endforeach

                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center font-bold">
                        <span>Total</span>
                        <span>£{{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Delivery Details</h2>
            
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="order_type" class="block text-sm font-medium text-gray-700">Order Type</label>
                    <select name="order_type" id="order_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                        <option value="delivery">Delivery</option>
                        <option value="pickup">Pickup</option>
                    </select>
                </div>

                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="customer_name" id="customer_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required value="{{ auth()->user()->name ?? old('customer_name') }}">
                </div>

                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="customer_email" id="customer_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required value="{{ auth()->user()->email ?? old('customer_email') }}">
                </div>

                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" name="customer_phone" id="customer_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required value="{{ old('customer_phone') }}">
                </div>

                <div id="delivery_address_container">
                    <label for="delivery_address" class="block text-sm font-medium text-gray-700">Delivery Address</label>
                    <textarea name="delivery_address" id="delivery_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('delivery_address') }}</textarea>
                </div>

                <div id="pickup_time_container" style="display: none;">
                    <label for="pickup_time" class="block text-sm font-medium text-gray-700">Pickup Time</label>
                    <input type="datetime-local" name="pickup_time" id="pickup_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}">
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Order Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150">
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const orderTypeSelect = document.getElementById('order_type');
    const deliveryAddressContainer = document.getElementById('delivery_address_container');
    const pickupTimeContainer = document.getElementById('pickup_time_container');
    const deliveryAddressInput = document.getElementById('delivery_address');
    const pickupTimeInput = document.getElementById('pickup_time');

    orderTypeSelect.addEventListener('change', function() {
        if (this.value === 'delivery') {
            deliveryAddressContainer.style.display = 'block';
            pickupTimeContainer.style.display = 'none';
            deliveryAddressInput.required = true;
            pickupTimeInput.required = false;
        } else {
            deliveryAddressContainer.style.display = 'none';
            pickupTimeContainer.style.display = 'block';
            deliveryAddressInput.required = false;
            pickupTimeInput.required = true;
        }
    });
</script>
@endpush
@endsection 