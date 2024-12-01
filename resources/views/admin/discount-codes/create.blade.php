@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Create Discount Code</h1>
            <a href="{{ route('admin.discount-codes.index') }}" class="text-red-600 hover:text-red-500 flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Discount Codes
            </a>
        </div>

        <div class="mt-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Discount Details</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Create a new discount code. All fields marked with * are required.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="{{ route('admin.discount-codes.store') }}" method="POST">
                        @csrf
                        <div class="shadow sm:rounded-md sm:overflow-hidden">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <!-- Code -->
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700">
                                        Code *
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" 
                                               name="code" 
                                               id="code" 
                                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('code') border-red-500 @enderror"
                                               value="{{ old('code') }}"
                                               required>
                                    </div>
                                    @error('code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Discount Percentage -->
                                <div>
                                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700">
                                        Discount Percentage *
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number"
                                               name="discount_percentage"
                                               id="discount_percentage"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md @error('discount_percentage') border-red-500 @enderror"
                                               value="{{ old('discount_percentage') }}"
                                               required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                    @error('discount_percentage')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Validity Period -->
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="valid_from" class="block text-sm font-medium text-gray-700">
                                            Valid From *
                                        </label>
                                        <div class="mt-1">
                                            <input type="datetime-local"
                                                   name="valid_from"
                                                   id="valid_from"
                                                   class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('valid_from') border-red-500 @enderror"
                                                   value="{{ old('valid_from') }}"
                                                   required>
                                        </div>
                                        @error('valid_from')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="valid_until" class="block text-sm font-medium text-gray-700">
                                            Valid Until *
                                        </label>
                                        <div class="mt-1">
                                            <input type="datetime-local"
                                                   name="valid_until"
                                                   id="valid_until"
                                                   class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('valid_until') border-red-500 @enderror"
                                                   value="{{ old('valid_until') }}"
                                                   required>
                                        </div>
                                        @error('valid_until')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Active Status -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox"
                                               name="is_active"
                                               id="is_active"
                                               class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded"
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700">Active</label>
                                        <p class="text-gray-500">Enable or disable this discount code</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Create Discount Code
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 