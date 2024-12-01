@extends('layouts.app')

@section('content')
<div class="space-y-16">
    <!-- Hero Section -->
    <div class="relative h-[500px] rounded-xl overflow-hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-white space-y-6 max-w-4xl mx-auto px-4">
                <h1 class="text-5xl font-bold">Welcome to Morris Pizza</h1>
                <p class="text-xl">Authentic Italian Pizza, Made Fresh Daily</p>
                <a href="{{ route('menu.index') }}" class="inline-block bg-red-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-red-700 transition duration-300">
                    Order Now
                </a>
            </div>
        </div>
        <div class="h-full w-full bg-[url('https://images.unsplash.com/photo-1513104890138-7c749659a591')] bg-cover bg-center"></div>
    </div>

    <!-- Features Section -->
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="text-center">
                <div class="text-4xl mb-4">🍕</div>
                <h3 class="text-xl font-semibold mb-2">Fresh Ingredients</h3>
                <p class="text-gray-600">We use only the finest and freshest ingredients in our pizzas</p>
            </div>
            <div class="text-center">
                <div class="text-4xl mb-4">🚚</div>
                <h3 class="text-xl font-semibold mb-2">Fast Delivery</h3>
                <p class="text-gray-600">Quick and reliable delivery to your doorstep</p>
            </div>
            <div class="text-center">
                <div class="text-4xl mb-4">⭐</div>
                <h3 class="text-xl font-semibold mb-2">Best Quality</h3>
                <p class="text-gray-600">Authentic Italian recipes and cooking methods</p>
            </div>
        </div>
    </div>

    <!-- Special Offers -->
    <div class="bg-gray-100 py-16 rounded-xl">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Special Offers</h2>
            <div class="flex justify-center">
                <div class="bg-white rounded-lg shadow-lg p-6 max-w-3xl w-full">
                    <img src="{{ asset('images/discountimg.jpg') }}" alt="10% Discount on all items - Use discount code: MORRIS" class="w-full rounded-lg">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
