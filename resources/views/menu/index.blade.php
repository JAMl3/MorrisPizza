@extends('layouts.app')

@section('content')
<div class="space-y-12">
    <!-- Hero Section -->
    <div class="text-center max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Our Menu</h1>
        <p class="text-gray-600 mb-8">Fresh, delicious, and made with love</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Category Sidebar -->
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-lg p-4 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Categories</h2>
                <nav class="space-y-2">
                    @foreach($categories as $category)
                        <a 
                            href="#category-{{ $category->id }}"
                            class="block px-3 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-lg transition duration-150"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="flex-1 space-y-12">
            @foreach($categories as $category)
                <div id="category-{{ $category->id }}" class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-semibold text-red-600 mb-6 text-center">
                        {{ $category->name }}
                    </h2>
                    <div class="space-y-4">
                        @foreach($category->menuItems as $item)
                            <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-300">
                                <div class="flex justify-between items-center gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold">{{ $item->item_name }}</h3>
                                        @if($item->description && $item->description != 'N/A')
                                            <p class="text-gray-600 text-sm mt-1">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-lg font-bold text-red-600 whitespace-nowrap">£{{ number_format($item->price, 2) }}</span>
                                        <button 
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 whitespace-nowrap"
                                            onclick="addToCart({{ $item->id }}, '{{ str_replace("'", "\\'", $item->item_name) }}', {{ number_format($item->price, 2, '.', '') }})"
                                        >
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Order Summary -->
        <div class="lg:w-80 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-lg p-4 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Order Summary</h2>
                <div id="cart-items" class="space-y-4 mb-4">
                    <!-- Cart items will be inserted here -->
                </div>
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center text-lg font-semibold">
                        <span>Total:</span>
                        <span id="cart-total" class="text-red-600">£0.00</span>
                    </div>
                    <button 
                        onclick="checkout()"
                        class="w-full mt-4 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition duration-300 disabled:opacity-50"
                        id="checkout-button"
                        disabled
                    >
                        Proceed to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize cart on page load and handle visibility changes
    document.addEventListener('DOMContentLoaded', function() {
        updateOrderSummary();
        
        // Update cart when page becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                updateOrderSummary();
            }
        });

        // Listen for cart updates from other pages
        window.addEventListener('cart-updated', function() {
            updateOrderSummary();
        });
    });

    function addToCart(itemId, itemName, price) {
        console.log('Adding to cart:', { itemId, itemName, price });
        
        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                menu_item_id: itemId,
                quantity: 1,
                special_instructions: ''
            })
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Error adding to cart');
            }
            return data;
        })
        .then(data => {
            console.log('Cart updated:', data);
            updateOrderSummaryFromData(data);
            // Dispatch cart update event
            window.dispatchEvent(new Event('cart-updated'));
            showToast('Added to cart!');
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            showToast(error.message || 'Error adding to cart', true);
        });
    }

    function removeFromCart(itemId) {
        console.log('Removing from cart:', itemId);
        
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
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Error removing from cart');
            }
            return data;
        })
        .then(data => {
            console.log('Cart updated:', data);
            updateOrderSummary(); // Call updateOrderSummary to refresh the entire cart
            // Dispatch cart update event
            window.dispatchEvent(new Event('cart-updated'));
            showToast('Item removed from cart');
        })
        .catch(error => {
            console.error('Error removing from cart:', error);
            showToast(error.message || 'Error removing from cart', true);
        });
    }

    function updateOrderSummary() {
        console.log('Updating order summary');
        
        fetch('{{ route('cart.index') }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Error fetching cart');
            }
            return data;
        })
        .then(data => {
            console.log('Cart data:', data);
            updateOrderSummaryFromData(data);
        })
        .catch(error => {
            console.error('Error fetching cart:', error);
            // Don't show error toast on initial load
            if (error.message !== 'Error fetching cart') {
                showToast(error.message || 'Error updating cart', true);
            }
        });
    }

    function updateOrderSummaryFromData(data) {
        console.log('Updating order summary with data:', data);
        
        const cartContainer = document.getElementById('cart-items');
        const totalElement = document.getElementById('cart-total');
        const checkoutButton = document.getElementById('checkout-button');
        
        if (!cartContainer) {
            console.error('Cart container not found');
            return;
        }
        
        cartContainer.innerHTML = '';
        let total = 0;
        
        // Check if data has the new structure
        const cartData = data.data || data;
        
        if (cartData.cartItems && cartData.cartItems.length > 0) {
            cartData.cartItems.forEach(item => {
                if (!item.menu_item) {
                    console.warn('Menu item not found for cart item:', item);
                    return;
                }
                
                const itemTotal = parseFloat(item.menu_item.price) * parseInt(item.quantity);
                total += itemTotal;
                
                const itemElement = document.createElement('div');
                itemElement.className = 'flex justify-between items-center p-2 border-b hover:bg-gray-50';
                itemElement.innerHTML = `
                    <div class="flex-1">
                        <div class="font-medium">${item.menu_item.item_name}</div>
                        <div class="text-sm text-gray-600">£${parseFloat(item.menu_item.price).toFixed(2)} × ${item.quantity}</div>
                        ${item.special_instructions ? `<div class="text-sm text-gray-500">Note: ${item.special_instructions}</div>` : ''}
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-red-600 font-medium">£${itemTotal.toFixed(2)}</div>
                        <button 
                            onclick="removeFromCart(${item.id})"
                            class="text-gray-400 hover:text-red-600 transition-colors"
                            title="Remove item"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                cartContainer.appendChild(itemElement);
            });
        } else {
            cartContainer.innerHTML = `
                <div class="text-gray-500 text-center py-4">
                    Your cart is empty
                </div>
            `;
        }
        
        if (totalElement) {
            totalElement.textContent = `£${(cartData.total || 0).toFixed(2)}`;
        }
        
        if (checkoutButton) {
            checkoutButton.disabled = !cartData.cartItems || cartData.cartItems.length === 0;
        }

        // Update cart icon in navigation
        window.dispatchEvent(new Event('cart-updated'));
    }

    function showToast(message, isError = false) {
        console.log('Showing toast:', { message, isError });
        
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
        }, 2000);
    }

    function checkout() {
        window.location.href = '{{ route('cart.checkout') }}';
    }
</script>
@endpush
@endsection 