@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4 py-8">
    @if ($errors->any())
        <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <strong class="font-bold">Please fix the following errors:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Checkout Form -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-6">Checkout</h1>
            
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6" id="checkout-form">
                @csrf
                
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Contact Information</h2>
                    
                    @guest
                    <div class="mb-6 border-b border-gray-200 pb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-4">Checkout Options</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="radio" name="checkout_type" id="guest_checkout" value="guest" 
                                    class="text-red-600 focus:ring-red-500"
                                    {{ old('checkout_type', 'guest') === 'guest' ? 'checked' : '' }}>
                                <label for="guest_checkout" class="ml-2 text-sm text-gray-700">
                                    Checkout as Guest
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="checkout_type" id="create_account" value="create_account" 
                                    class="text-red-600 focus:ring-red-500"
                                    {{ old('checkout_type') === 'create_account' ? 'checked' : '' }}>
                                <label for="create_account" class="ml-2 text-sm text-gray-700">
                                    Create an Account for Faster Checkout
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="password_section" class="mb-6" style="display: none;">
                        <div class="space-y-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" id="password" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    minlength="8">
                                <p class="mt-1 text-sm text-gray-500">Must be at least 8 characters</p>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    minlength="8">
                            </div>
                        </div>
                    </div>
                    @endguest

                    <div class="space-y-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="customer_name" id="customer_name" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ $userProfile->default_name ?? old('customer_name') }}" required>
                        </div>
                        
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="customer_email" id="customer_email" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ $userProfile->default_email ?? old('customer_email') }}" required>
                        </div>
                        
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" name="customer_phone" id="customer_phone" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ $userProfile->default_phone ?? old('customer_phone') }}" required>
                        </div>
                    </div>
                </div>
                
                <!-- Order Type -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Type</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="order_type" id="delivery" value="delivery" 
                                    class="text-red-600 focus:ring-red-500" checked>
                                <label for="delivery" class="ml-2 block text-sm font-medium text-gray-700">
                                    Delivery
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="order_type" id="pickup" value="pickup" 
                                    class="text-red-600 focus:ring-red-500">
                                <label for="pickup" class="ml-2 block text-sm font-medium text-gray-700">
                                    Pickup
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Address -->
                <div id="delivery-section" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Delivery Address</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="delivery_address_line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                            <input type="text" name="delivery_address_line1" id="delivery_address_line1" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ $userProfile->default_address_line1 ?? old('delivery_address_line1') }}">
                        </div>
                        
                        <div>
                            <label for="delivery_address_line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                            <input type="text" name="delivery_address_line2" id="delivery_address_line2" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ $userProfile->default_address_line2 ?? old('delivery_address_line2') }}">
                        </div>
                        
                        <div>
                            <label for="delivery_city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="delivery_city" id="delivery_city" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ $userProfile->default_city ?? old('delivery_city') }}">
                        </div>
                        
                        <div>
                            <label for="delivery_postcode" class="block text-sm font-medium text-gray-700">Postcode</label>
                            <input type="text" name="delivery_postcode" id="delivery_postcode" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ $userProfile->default_postcode ?? old('delivery_postcode') }}">
                        </div>

                        <input type="hidden" name="delivery_address" id="delivery_address">
                    </div>
                </div>

                <!-- Pickup Time (for pickup orders) -->
                <div id="pickup-section" class="bg-white rounded-lg shadow p-6" style="display: none;">
                    <h2 class="text-xl font-semibold mb-4">Pickup Time</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                            <input type="date" id="pickup_date" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                min="{{ now()->format('Y-m-d') }}"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label for="pickup_time_select" class="block text-sm font-medium text-gray-700">Pickup Time</label>
                            <select id="pickup_time_select" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <option value="">Select a time</option>
                            </select>
                        </div>
                        <input type="hidden" name="pickup_time" id="pickup_time">
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" id="cash_on_delivery" value="cash_on_delivery" 
                                    class="text-red-600 focus:ring-red-500" checked>
                                <label for="cash_on_delivery" class="ml-2 block text-sm font-medium text-gray-700">
                                    Cash on Delivery
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" id="cash_on_pickup" value="cash_on_pickup" 
                                    class="text-red-600 focus:ring-red-500">
                                <label for="cash_on_pickup" class="ml-2 block text-sm font-medium text-gray-700">
                                    Cash on Pickup
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Notes (Optional)</h2>
                    <div>
                        <textarea name="notes" id="notes" rows="3" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Any special instructions or notes for your order?">{{ old('notes') }}</textarea>
                    </div>
                </div>
                
                <!-- Discount Code Section -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Discount Code</h2>
                    <div class="space-y-4">
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label for="discount_code" class="block text-sm font-medium text-gray-700">Enter Code</label>
                                <input type="text" 
                                    id="discount_code" 
                                    name="discount_code"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Enter discount code">
                            </div>
                            <div class="flex items-end">
                                <button type="button" 
                                    onclick="validateDiscountCode()"
                                    class="h-[42px] inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Apply
                                </button>
                            </div>
                        </div>
                        <p id="discount_message" class="mt-2 text-sm"></p>
                    </div>
                </div>

                <script>
                async function validateDiscountCode() {
                    const code = document.getElementById('discount_code').value;
                    const messageEl = document.getElementById('discount_message');
                    const totalEl = document.querySelector('[data-total]');
                    const subtotal = {{ $cart->subtotal }};
                    const deliveryFee = {{ $deliveryFee }};

                    try {
                        const response = await fetch('{{ route("api.discount-codes.validate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ code })
                        });

                        const data = await response.json();
                        
                        if (data.valid) {
                            const discountAmount = (subtotal * data.discount_percentage) / 100;
                            const newTotal = subtotal - discountAmount + deliveryFee;
                            
                            messageEl.textContent = `Discount of ${data.discount_percentage}% applied successfully!`;
                            messageEl.className = 'mt-2 text-sm text-green-600';
                            totalEl.textContent = `£${newTotal.toFixed(2)}`;
                            
                            // Add hidden input for the discount code
                            let discountInput = document.querySelector('input[name="discount_code"]');
                            if (!discountInput) {
                                discountInput = document.createElement('input');
                                discountInput.type = 'hidden';
                                discountInput.name = 'discount_code';
                                document.getElementById('checkout-form').appendChild(discountInput);
                            }
                            discountInput.value = code;
                        } else {
                            messageEl.textContent = data.message || 'Invalid discount code';
                            messageEl.className = 'mt-2 text-sm text-red-600';
                            totalEl.textContent = `£${(subtotal + deliveryFee).toFixed(2)}`;
                            
                            // Remove any existing discount code input
                            const existingInput = document.querySelector('input[name="discount_code"]');
                            if (existingInput) existingInput.remove();
                        }
                    } catch (error) {
                        messageEl.textContent = 'An error occurred while validating the code. Please try again.';
                        messageEl.className = 'mt-2 text-sm text-red-600';
                    }
                }
                </script>

                <button type="submit" 
                    class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition duration-150">
                    Place Order
                </button>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:w-96">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                
                <div class="space-y-4 mb-4">
                    @foreach($cart->items as $item)
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-medium">{{ $item->menuItem->item_name }}</h3>
                                <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                @if($item->special_instructions)
                                    <p class="text-sm text-gray-500">Note: {{ $item->special_instructions }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-medium">£{{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium" data-subtotal>£{{ number_format($cart->subtotal, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery Fee</span>
                        <span class="font-medium">£{{ number_format($cart->delivery_fee, 2) }}</span>
                    </div>

                    <template x-if="discountPercentage > 0">
                        <div class="flex justify-between">
                            <span class="text-green-600">Discount</span>
                            <span class="font-medium text-green-600" x-text="`-£${(discountPercentage / 100 * cartSubtotal).toFixed(2)}`"></span>
                        </div>
                    </template>
                    
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span data-total>£{{ number_format($cart->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliverySection = document.getElementById('delivery-section');
    const pickupSection = document.getElementById('pickup-section');
    const orderTypeInputs = document.querySelectorAll('input[name="order_type"]');
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    const form = document.getElementById('checkout-form');
    
    // Address fields
    const addressLine1 = document.getElementById('delivery_address_line1');
    const addressLine2 = document.getElementById('delivery_address_line2');
    const city = document.getElementById('delivery_city');
    const postcode = document.getElementById('delivery_postcode');
    const combinedAddress = document.getElementById('delivery_address');
    
    // Pickup time fields
    const pickupDate = document.getElementById('pickup_date');
    const pickupTimeSelect = document.getElementById('pickup_time_select');
    const pickupTimeHidden = document.getElementById('pickup_time');

    function updateCombinedAddress() {
        const parts = [
            addressLine1.value,
            addressLine2.value,
            city.value,
            postcode.value
        ].filter(Boolean);
        
        combinedAddress.value = parts.join(', ');
    }

    function generateTimeSlots() {
        const now = new Date();
        const selectedDate = new Date(pickupDate.value);
        const isToday = selectedDate.toDateString() === now.toDateString();
        
        // Clear existing options
        pickupTimeSelect.innerHTML = '<option value="">Select a time</option>';
        
        // Start from 5 PM (17:00)
        const startTime = new Date(selectedDate);
        startTime.setHours(17, 0, 0);
        
        // End at midnight
        const endTime = new Date(selectedDate);
        endTime.setHours(23, 45, 0);
        
        // If today, start from 30 minutes from now
        if (isToday) {
            const thirtyMinsFromNow = new Date(now.getTime() + 30 * 60000);
            if (thirtyMinsFromNow > startTime) {
                startTime.setTime(thirtyMinsFromNow.getTime());
            }
        }
        
        // Generate 15-minute intervals
        let current = new Date(startTime);
        while (current <= endTime) {
            const timeString = current.toLocaleTimeString('en-GB', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
            
            const option = document.createElement('option');
            option.value = timeString;
            option.textContent = timeString;
            pickupTimeSelect.appendChild(option);
            
            current.setMinutes(current.getMinutes() + 15);
        }
    }

    function updatePickupTime() {
        if (pickupTimeSelect.value) {
            pickupTimeHidden.value = `${pickupDate.value} ${pickupTimeSelect.value}:00`;
        }
    }
    
    function updateFormDisplay() {
        const isDelivery = document.getElementById('delivery').checked;
        deliverySection.style.display = isDelivery ? 'block' : 'none';
        pickupSection.style.display = isDelivery ? 'none' : 'block';
        
        // Update required fields
        const deliveryFields = [addressLine1, city, postcode];
        deliveryFields.forEach(field => {
            field.required = isDelivery;
        });
        pickupTimeSelect.required = !isDelivery;
        
        // Update payment methods
        document.getElementById('cash_on_delivery').disabled = !isDelivery;
        document.getElementById('cash_on_pickup').disabled = isDelivery;
        
        // Switch to appropriate payment method
        if (isDelivery) {
            document.getElementById('cash_on_delivery').checked = true;
        } else {
            document.getElementById('cash_on_pickup').checked = true;
            generateTimeSlots();
        }
    }
    
    // Event Listeners
    orderTypeInputs.forEach(input => {
        input.addEventListener('change', updateFormDisplay);
    });
    
    [addressLine1, addressLine2, city, postcode].forEach(field => {
        field.addEventListener('input', updateCombinedAddress);
    });
    
    pickupDate.addEventListener('change', generateTimeSlots);
    pickupTimeSelect.addEventListener('change', updatePickupTime);
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const isDelivery = document.getElementById('delivery').checked;
        const createAccount = document.getElementById('create_account')?.checked;
        
        // Validate delivery fields
        if (isDelivery) {
            updateCombinedAddress();
            if (!addressLine1.value || !city.value || !postcode.value) {
                e.preventDefault();
                alert('Please fill in all required delivery address fields');
                return;
            }
        } else {
            if (!pickupTimeSelect.value) {
                e.preventDefault();
                alert('Please select a pickup time');
                return;
            }
            updatePickupTime();
        }

        // Validate account creation fields
        if (createAccount) {
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');
            
            if (!password.value || password.value.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return;
            }
            
            if (password.value !== passwordConfirm.value) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
        }
    });
    
    // Initial setup
    updateFormDisplay();

    // Handle checkout type selection
    const checkoutTypeInputs = document.querySelectorAll('input[name="checkout_type"]');
    const passwordSection = document.getElementById('password_section');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');

    function togglePasswordSection() {
        const createAccount = document.getElementById('create_account').checked;
        passwordSection.style.display = createAccount ? 'block' : 'none';
        
        if (createAccount) {
            passwordInput.setAttribute('required', 'required');
            passwordConfirmInput.setAttribute('required', 'required');
        } else {
            passwordInput.removeAttribute('required');
            passwordConfirmInput.removeAttribute('required');
        }
    }

    checkoutTypeInputs.forEach(input => {
        input.addEventListener('change', togglePasswordSection);
    });

    // Initial state
    togglePasswordSection();
});
</script>
@endpush
@endsection 