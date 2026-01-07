@extends('layouts.app')

@section('title', 'পণ্য সম্পাদনা')

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
@endphp
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">পণ্য সম্পাদনা</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route($routePrefix . '.products.update', $product) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">পণ্যের নাম</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="sku" class="block text-gray-700 text-sm font-bold mb-2">পণ্য কোড (SKU)</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('sku') border-red-500 @enderror" required>
                @error('sku')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="purchase_price" class="block text-gray-700 text-sm font-bold mb-2">ক্রয় মূল্য</label>
                <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('purchase_price') border-red-500 @enderror" required>
                @error('purchase_price')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="sell_price" class="block text-gray-700 text-sm font-bold mb-2">বিক্রয় মূল্য</label>
                <input type="number" step="0.01" name="sell_price" id="sell_price" value="{{ old('sell_price', $product->sell_price) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('sell_price') border-red-500 @enderror" required>
                @error('sell_price')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <label class="block text-gray-700 text-sm font-bold mb-2">বর্তমান স্টক</label>
                <p class="text-2xl font-bold text-blue-600">{{ $product->current_stock }} টি</p>
            </div>

            @if(auth()->user()->hasRole('owner'))
            <div class="mb-6">
                <label for="add_stock" class="block text-gray-700 text-sm font-bold mb-2">স্টক যোগ করুন (ঐচ্ছিক)</label>
                <input type="number" step="1" min="0" name="add_stock" id="add_stock" value="{{ old('add_stock', 0) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('add_stock') border-red-500 @enderror" placeholder="নতুন স্টক পরিমাণ লিখুন">
                <p class="text-sm text-gray-600 mt-1">এখানে সংখ্যা লিখলে তা বর্তমান স্টকের সাথে যোগ হবে</p>
                @error('add_stock')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route($routePrefix . '.products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    বাতিল
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    পণ্য আপডেট করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
