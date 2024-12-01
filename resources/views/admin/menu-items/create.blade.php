@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Add New Menu Item</h1>
            <a href="{{ route('admin.menu-items.index') }}" class="text-red-600 hover:text-red-500 flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Menu Items
            </a>
        </div>

        <div class="mt-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Item Details</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Add a new item to your menu. All fields marked with * are required.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="{{ route('admin.menu-items.store') }}" method="POST" enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        <div class="shadow sm:rounded-md sm:overflow-hidden">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <!-- Item Name -->
                                <div>
                                    <label for="item_name" class="block text-sm font-medium text-gray-700">
                                        Item Name *
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" 
                                               name="item_name" 
                                               id="item_name" 
                                               :disabled="loading"
                                               class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('item_name') border-red-500 @else border-gray-300 @enderror" 
                                               value="{{ old('item_name') }}"
                                               required>
                                    </div>
                                    @error('item_name')
                                        <p class="mt-1 text-sm text-red-600 transition-all duration-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">
                                        Category *
                                    </label>
                                    <div class="mt-1">
                                        <select name="category_id" 
                                                id="category_id" 
                                                class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('category_id') border-red-500 @else border-gray-300 @enderror" 
                                                required>
                                            <option value="">Select a category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Price -->
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">
                                        Price (£) *
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">£</span>
                                        </div>
                                        <input type="number" 
                                               name="price" 
                                               id="price" 
                                               class="pl-7 mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('price') border-red-500 @else border-gray-300 @enderror" 
                                               value="{{ old('price') }}" 
                                               step="0.01" 
                                               required>
                                    </div>
                                    @error('price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>
                                    <div class="mt-1">
                                        <textarea name="description" 
                                                  id="description" 
                                                  rows="3" 
                                                  class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @else border-gray-300 @enderror">{{ old('description') }}</textarea>
                                    </div>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Image Upload -->
                                <div x-data="{ previewUrl: null }">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Item Image
                                    </label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-300">
                                        <div class="space-y-1 text-center">
                                            <template x-if="previewUrl">
                                                <img :src="previewUrl" class="mx-auto h-32 w-32 object-cover rounded-md transition-all duration-300 hover:scale-105" />
                                            </template>
                                            <template x-if="!previewUrl">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </template>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload a file</span>
                                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*" @change="previewUrl = URL.createObjectURL($event.target.files[0])" :disabled="loading">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock -->
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700">
                                        Stock
                                    </label>
                                    <div class="mt-1">
                                        <input type="number" 
                                               name="stock" 
                                               id="stock" 
                                               class="mt-1 block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('stock') border-red-500 @else border-gray-300 @enderror" 
                                               value="{{ old('stock') }}" 
                                               min="0">
                                    </div>
                                    @error('stock')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Availability -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" 
                                               name="is_available" 
                                               id="is_available" 
                                               class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded" 
                                               value="1" 
                                               {{ old('is_available', true) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_available" class="font-medium text-gray-700">Available</label>
                                        <p class="text-gray-500">Make this item available for ordering</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <button type="submit" 
                                        class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 transition-all duration-300"
                                        :disabled="loading">
                                    <span x-show="!loading">Create Item</span>
                                    <span x-show="loading" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Creating...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Preview image before upload
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.className = 'mt-2 mx-auto h-20 w-20 object-cover rounded-md';
            
            const existingPreview = document.querySelector('.image-preview');
            if (existingPreview) {
                existingPreview.remove();
            }
            
            preview.classList.add('image-preview');
            document.querySelector('.space-y-1').appendChild(preview);
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection 