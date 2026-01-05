@extends('layouts.app')

@section('title', 'মালিক পরিচালনা')

@section('content')
<div class="min-h-screen w-full px-2 sm:px-4 lg:px-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">মালিক পরিচালনা</h1>
        <a href="{{ route('superadmin.owners.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm sm:text-base">
            নতুন মালিক যোগ করুন
        </a>
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

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">আইডি</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">নাম</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">কোম্পানি</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ফোন</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">পাসওয়ার্ড</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বকেয়া সিস্টেম</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">তারিখ</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">কাজ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($owners as $owner)
                    <tr>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">{{ $owner->id }}</td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">{{ $owner->name }}</td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm hidden sm:table-cell">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $owner->business ? $owner->business->name : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                            <span class="font-mono font-semibold text-gray-900">{{ $owner->phone }}</span>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm hidden lg:table-cell">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">
                                ডিফল্ট: password
                            </span>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('superadmin.owners.toggle-due-system', $owner) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $owner->due_system_enabled ? 'bg-green-600' : 'bg-gray-200' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $owner->due_system_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <span class="ml-2 text-xs sm:text-sm {{ $owner->due_system_enabled ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $owner->due_system_enabled ? 'চালু' : 'বন্ধ' }}
                                </span>
                            </form>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden md:table-cell">{{ $owner->created_at->format('Y-m-d') }}</td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                            <a href="{{ route('superadmin.owners.edit', $owner) }}" class="text-blue-600 hover:text-blue-900 mr-2 sm:mr-3">সম্পাদনা</a>
                            <form action="{{ route('superadmin.owners.destroy', $owner) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('আপনি কি নিশ্চিত?')">মুছুন</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $owners->links() }}
    </div>
</div>
@endsection
