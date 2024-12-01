<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Morris Pizza') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-red-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('menu.index') }}" class="text-2xl font-bold text-white hover:text-gray-100">
                                Morris Pizza
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="/" 
                               class="inline-flex items-center px-1 pt-1 text-white hover:text-gray-200 {{ request()->is('/') ? 'border-b-2 border-white' : '' }}">
                                Home
                            </a>
                            <a href="{{ route('menu.index') }}" 
                               class="inline-flex items-center px-1 pt-1 text-white hover:text-gray-200 {{ request()->routeIs('menu.*') ? 'border-b-2 border-white' : '' }}">
                                Menu
                            </a>
                            @auth
                                <a href="{{ route('orders.history') }}" 
                                   class="inline-flex items-center px-1 pt-1 text-white hover:text-gray-200 {{ request()->routeIs('orders.*') ? 'border-b-2 border-white' : '' }}">
                                    Order History
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Cart Link -->
                        <a href="{{ route('cart.index') }}" class="relative group text-white hover:text-gray-200">
                            <div class="flex items-center">
                                <div class="relative">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span id="cart-count" class="absolute -top-2 -right-2 bg-yellow-400 text-gray-900 rounded-full h-5 w-5 flex items-center justify-center text-xs font-bold">0</span>
                                </div>
                                <span id="cart-total-price" class="ml-2 text-sm font-medium">£0.00</span>
                            </div>
                            <div class="absolute transform transition-all duration-300 ease-in-out opacity-0 group-hover:opacity-100 scale-95 group-hover:scale-100 right-0 mt-2 w-64 bg-white rounded-lg shadow-lg p-4 text-gray-700 z-40">
                                <div id="cart-preview" class="space-y-2">
                                    <div class="text-center text-sm text-gray-500">Your cart is empty</div>
                                </div>
                            </div>
                        </a>

                        <!-- Login/User Menu -->
                        @guest
                            <a href="{{ route('login') }}" class="text-white hover:text-gray-200">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Login</span>
                                </div>
                            </a>
                        @else
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-white hover:text-gray-200">
                                    {{ Auth::user()->name }}
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('orders.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Order History
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profile Settings
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div id="success-alert" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                        <p>Phone: <a href="tel:01132242242" class="hover:text-gray-300">0113 224 2242</a></p>
                        <p>Email: <a href="mailto:info@morrispizza.com" class="hover:text-gray-300">info@morrispizza.com</a></p>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-4">Opening Hours</h3>
                        <div class="space-y-2">
                            <p>Wednesday - Monday: 5pm - Midnight</p>
                            <p class="text-red-400">Tuesday: Closed</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                        <div class="space-x-4">
                            <a href="#" class="hover:text-gray-300">Facebook</a>
                            <a href="#" class="hover:text-gray-300">Instagram</a>
                            <a href="#" class="hover:text-gray-300">Twitter</a>
                        </div>
                    </div>
                </div>
                <div class="mt-8 text-center text-gray-400 border-t border-gray-700 pt-8">
                    <p>&copy; {{ date('Y') }} Morris Pizza. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
    <script>
    // Auto-hide success message with smooth transition
    document.addEventListener('DOMContentLoaded', function() {
        const successAlert = document.getElementById('success-alert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'all 0.5s ease-out';
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-20px)';
                setTimeout(() => successAlert.remove(), 500);
            }, 3000);
        }
    });

    function updateCartIcon(data) {
        const cartCount = document.getElementById('cart-count');
        const cartTotalPrice = document.getElementById('cart-total-price');
        const cartPreview = document.getElementById('cart-preview');
        
        // Add smooth transition for cart updates
        cartCount.style.transition = 'transform 0.3s ease';
        cartCount.style.transform = 'scale(1.2)';
        setTimeout(() => cartCount.style.transform = 'scale(1)', 300);
        
        // Update cart data
        const cartData = data.data || data;

        if (!cartData.cartItems || cartData.cartItems.length === 0) {
            cartCount.textContent = '0';
            cartTotalPrice.textContent = '£0.00';
            cartPreview.innerHTML = '<div class="text-center text-sm text-gray-500">Your cart is empty</div>';
            return;
        }

        // Update count
        const itemCount = cartData.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = itemCount.toString();

        // Update total price
        cartTotalPrice.textContent = `£${parseFloat(cartData.total || 0).toFixed(2)}`;

        // Update preview
        cartPreview.innerHTML = `
            <div class="max-h-48 overflow-y-auto space-y-2">
                ${cartData.cartItems.map(item => `
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex-1">
                            <div class="font-medium">${item.menu_item.item_name}</div>
                            <div class="text-gray-500">x${item.quantity}</div>
                        </div>
                        <div class="text-gray-700">£${(item.menu_item.price * item.quantity).toFixed(2)}</div>
                    </div>
                `).join('')}
            </div>
            <div class="border-t mt-2 pt-2">
                <div class="flex justify-between font-medium">
                    <span>Total:</span>
                    <span>£${parseFloat(cartData.total || 0).toFixed(2)}</span>
                </div>
                <a href="{{ route('cart.index') }}" class="mt-2 block w-full bg-red-600 text-white text-center py-2 rounded-md hover:bg-red-700 transition-colors">
                    View Cart
                </a>
            </div>
        `;
    }

    // Function to fetch cart data
    function fetchCartData() {
        fetch('{{ route('cart.index') }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            updateCartIcon(data.data || data);
        })
        .catch(error => console.error('Error fetching cart:', error));
    }

    // Update cart on page load
    document.addEventListener('DOMContentLoaded', fetchCartData);

    // Create a custom event for cart updates
    window.addEventListener('cart-updated', fetchCartData);
    </script>
</body>
</html> 