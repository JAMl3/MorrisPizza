@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Profile Settings</h1>

        @if ($errors->any())
            <div class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="default_name" class="block text-sm font-medium text-gray-700">Default Name</label>
                    <input type="text" name="default_name" id="default_name" 
                           value="{{ old('default_name', $profile->default_name) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('default_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="default_email" class="block text-sm font-medium text-gray-700">Default Email</label>
                    <input type="email" name="default_email" id="default_email" 
                           value="{{ old('default_email', $profile->default_email) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('default_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="default_phone" class="block text-sm font-medium text-gray-700">Default Phone Number</label>
                    <input type="tel" name="default_phone" id="default_phone" 
                           value="{{ old('default_phone', $profile->default_phone) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('default_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Default Delivery Address</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="default_address_line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                            <input type="text" name="default_address_line1" id="default_address_line1" 
                                   value="{{ old('default_address_line1', $profile->default_address_line1) }}"
                                   placeholder="House number and street name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            @error('default_address_line1')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="default_address_line2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                            <input type="text" name="default_address_line2" id="default_address_line2" 
                                   value="{{ old('default_address_line2', $profile->default_address_line2) }}"
                                   placeholder="Apartment, suite, unit, etc."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div>
                            <label for="default_city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="default_city" id="default_city" 
                                   value="{{ old('default_city', $profile->default_city) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            @error('default_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="default_postcode" class="block text-sm font-medium text-gray-700">Postcode</label>
                            <input type="text" name="default_postcode" id="default_postcode" 
                                   value="{{ old('default_postcode', $profile->default_postcode) }}"
                                   class="mt-1 block w-full uppercase rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            @error('default_postcode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const postcodeInput = document.getElementById('default_postcode');
        
        // Format postcode to uppercase
        postcodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
</script>
@endpush

@endsection 