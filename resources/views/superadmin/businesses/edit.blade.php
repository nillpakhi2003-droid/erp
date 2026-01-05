@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">কোম্পানি এডিট করুন</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $business->name }} এর তথ্য আপডেট করুন</p>
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
            <form action="{{ route('superadmin.businesses.update', $business) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Company Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            কোম্পানির নাম <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $business->name) }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            ইমেইল
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $business->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="example@company.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            ফোন নম্বর
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $business->phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="01711111111">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            ঠিকানা
                        </label>
                        <textarea name="address" id="address" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                  placeholder="সম্পূর্ণ ঠিকানা লিখুন">{{ old('address', $business->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $business->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">কোম্পানি সক্রিয় রাখুন</span>
                        </label>
                    </div>

                    <!-- Owners List -->
                    <div class="pt-6 border-t">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">মালিকগণ (Owners)</h3>
                            <a href="{{ route('superadmin.businesses.add-owner', $business) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-semibold rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                নতুন মালিক যোগ করুন
                            </a>
                        </div>
                        @if($business->owners->count() > 0)
                            <div class="space-y-2">
                                @foreach($business->owners as $owner)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $owner->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $owner->phone }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                                Owner
                                            </span>
                                            @if($owner->due_system_enabled)
                                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                                    বকেয়া চালু
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">এই কোম্পানির কোনো মালিক নেই। উপরের বাটনে ক্লিক করে মালিক যোগ করুন।</p>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-8 pt-6 border-t">
                    <a href="{{ route('superadmin.businesses.index') }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                        বাতিল করুন
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-md">
                        আপডেট করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
