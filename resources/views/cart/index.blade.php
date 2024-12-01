@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Your Cart</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            @if($cart->items->isEmpty())
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-500 mb-4">Your cart is empty</p>
                    <a href="{{ route('menu.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        View Menu
                    </a>
                </div>
            @else
                <div class="bg-white rounded-lg shadow divide-y divide-gray-200">
                    @foreach($cart->items as $item)
                        <div class="p-6" id="cart-item-{{ $item->id }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $item->menuItem->item_name }}</h3>
                                    @if($item->special_instructions)
                                        <p class="mt-1 text-sm text-gray-500">Note: {{ $item->special_instructions }}</p>
                                    @endif
                                </div>
                                <div class="ml-4 flex items-center">
                                    <div class="flex items-center space-x-3">
                                        <button 
                                            onclick="removeFromCart({{ $item->id }})"
                                            class="text-sm font-medium text-red-600 hover:text-red-500"
                                        >
                                            <span class="sr-only">Remove item</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <span class="text-gray-500">×</span>
                                        <span class="text-gray-900">{{ $item->quantity }}</span>
                                        <span class="text-gray-500">=</span>
                                        <span class="text-gray-900 font-medium">£{{ number_format($item->subtotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                <div class="flow-root">
                    <dl class="-my-4 text-sm divide-y divide-gray-200">
                        <div class="py-4 flex items-center justify-between">
                            <dt class="text-gray-600">Subtotal</dt>
                            <dd class="font-medium text-gray-900" id="cart-subtotal">£{{ number_format($cart->subtotal, 2) }}</dd>
                        </div>
                        <div class="py-4 flex items-center justify-between">
                            <dt class="text-gray-600">Delivery</dt>
                            <dd class="font-medium text-gray-900" id="cart-delivery">£{{ number_format($cart->delivery_fee, 2) }}</dd>
                        </div>
                        <div class="py-4 flex items-center justify-between">
                            <dt class="text-base font-medium text-gray-900">Total</dt>
                            <dd class="text-base font-medium text-gray-900" id="cart-total">£{{ number_format($cart->total_amount, 2) }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="mt-6 space-y-4">
                    @if(!$cart->items->isEmpty())
                        <a href="{{ route('cart.checkout') }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Proceed to Checkout
                        </a>
                    @endif

                    <button 
                        onclick="clearCart()"
                        class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function removeFromCart(itemId) {
    if (!confirm('Are you sure you want to remove this item?')) {
        return;
    }

    fetch('{{ route('cart.remove') }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            cart_item_id: itemId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from the DOM
            const itemElement = document.getElementById(`cart-item-${itemId}`);
            if (itemElement) {
                itemElement.remove();
            }

            // Update the cart totals
            updateCartTotals(data.data);

            // Show success message
            showToast(data.message);

            // Trigger cart update event
            window.dispatchEvent(new Event('cart-updated'));

            // If cart is empty, reload the page
            if (!data.data.cartItems || data.data.cartItems.length === 0) {
                window.location.reload();
            }
        } else {
            showToast(data.message, true);
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
        showToast('Failed to remove item from cart', true);
    });
}

function clearCart() {
    if (!confirm('Are you sure you want to clear your cart?')) {
        return;
    }

    fetch('{{ route('cart.clear') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            showToast(data.message, true);
        }
    })
    .catch(error => {
        console.error('Error clearing cart:', error);
        showToast('Failed to clear cart', true);
    });
}

function updateCartTotals(cartData) {
    document.getElementById('cart-subtotal').textContent = `£${parseFloat(cartData.subtotal || 0).toFixed(2)}`;
    document.getElementById('cart-delivery').textContent = `£${parseFloat(cartData.delivery_fee || 0).toFixed(2)}`;
    document.getElementById('cart-total').textContent = `£${parseFloat(cartData.total || 0).toFixed(2)}`;
}

function showToast(message, isError = false) {
    const existingToast = document.querySelector('.toast-message');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transition-opacity duration-500 toast-message ${isError ? 'bg-red-500' : 'bg-green-500'} text-white z-50`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
</script>
@endpush
@endsection 