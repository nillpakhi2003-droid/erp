@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">নতুন মালিক যোগ করুন</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $business->name }} এর জন্য নতুন মালিক তৈরি করুন</p>
        </div>

        @if(session('owner_created'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-500 rounded-lg p-6 shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-bold text-green-900 mb-3">✅ নতুন মালিক সফলভাবে তৈরি হয়েছে!</h3>
                        <div class="bg-white rounded-lg p-4 border border-green-300">
                            <p class="text-sm font-semibold text-gray-700 mb-3">লগইন তথ্য (এই তথ্য সংরক্ষণ করুন):</p>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                    <span class="text-sm font-medium text-gray-600">নাম:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ session('owner_created')['name'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded">
                                    <span class="text-sm font-medium text-gray-600">ফোন (Username):</span>
                                    <span class="text-lg font-mono font-bold text-blue-700">{{ session('owner_created')['phone'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded">
                                    <span class="text-sm font-medium text-gray-600">পাসওয়ার্ড:</span>
                                    <span class="text-lg font-mono font-bold text-yellow-700">{{ session('owner_created')['password'] }}</span>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                                <p class="text-xs text-red-800 font-semibold">⚠️ গুরুত্বপূর্ণ: এই পাসওয়ার্ড শুধুমাত্র এখন একবার দেখানো হচ্ছে। অনুগ্রহ করে নোট করে রাখুন।</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-8">
            <form action="{{ route('superadmin.businesses.store-owner', $business) }}" method="POST">
                @csrf

                <!-- Company Info Display -->
                <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-indigo-900">কোম্পানি</p>
                            <p class="text-lg font-bold text-indigo-700">{{ $business->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Owner Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            মালিকের নাম <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="মালিকের পূর্ণ নাম লিখুন">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            ফোন নম্বর <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="01711111111">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">১০-১৫ সংখ্যার ফোন নম্বর দিন (শুধু সংখ্যা)</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            পাসওয়ার্ড <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="নিরাপদ পাসওয়ার্ড দিন">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">কমপক্ষে ৮ অক্ষরের পাসওয়ার্ড ব্যবহার করুন</p>
                    </div>

                    <!-- Due System -->
                    <div class="pt-4 border-t">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="due_system_enabled" value="1" {{ old('due_system_enabled', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-3">
                                <span class="text-sm font-medium text-gray-900">বকেয়া সিস্টেম চালু রাখুন</span>
                                <span class="block text-xs text-gray-500">মালিক বকেয়া বিক্রয় ব্যবস্থাপনা করতে পারবেন</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-8 pt-6 border-t">
                    <a href="{{ route('superadmin.businesses.edit', $business) }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                        বাতিল করুন
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md">
                        মালিক যোগ করুন
                    </button>
                </div>
            </form>
        </div>

        <!-- Existing Owners -->
        @if($business->owners->count() > 0)
            <div class="mt-8 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">এই কোম্পানির বর্তমান মালিকগণ</h3>
                <div class="space-y-3">
                    @foreach($business->owners as $owner)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $owner->name }}</p>
                                <p class="text-sm text-gray-500">{{ $owner->phone }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                    Owner
                                </span>
                                @if($owner->due_system_enabled)
                                    <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                        বকেয়া চালু
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
