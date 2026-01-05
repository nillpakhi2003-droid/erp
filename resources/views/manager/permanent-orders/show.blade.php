@extends('layouts.app')

@section('title', 'স্থায়ী অর্ডার বিস্তারিত')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">স্থায়ী অর্ডার বিস্তারিত</h1>
        <div class="flex space-x-2">
            <a href="{{ route('manager.permanent-orders.edit', $permanentOrder) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                সম্পাদনা
            </a>
            <a href="{{ route('manager.permanent-orders.voucher', $permanentOrder) }}" target="_blank" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                ভাউচার প্রিন্ট
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-white text-xl font-bold">{{ $permanentOrder->voucher_number }}</h2>
                    <p class="text-blue-100 text-sm">{{ $permanentOrder->order_date->format('d M Y, h:i A') }}</p>
                </div>
                <div>
                    @if($permanentOrder->status === 'completed')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            সম্পন্ন
                        </span>
                    @elseif($permanentOrder->status === 'partial')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            আংশিক পরিশোধ
                        </span>
                    @elseif($permanentOrder->status === 'active')
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            সক্রিয়
                        </span>
                    @else
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            বাতিল
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">গ্রাহকের তথ্য</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">নাম:</dt>
                            <dd class="font-medium text-gray-900">{{ $permanentOrder->customer_name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">ফোন:</dt>
                            <dd class="font-medium text-gray-900">{{ $permanentOrder->customer_phone }}</dd>
                        </div>
                        @if($permanentOrder->customer_address)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">ঠিকানা:</dt>
                            <dd class="font-medium text-gray-900">{{ $permanentOrder->customer_address }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">পণ্যের তথ্য</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">পণ্য:</dt>
                            <dd class="font-medium text-gray-900">{{ $permanentOrder->product->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">পণ্য কোড:</dt>
                            <dd class="font-medium text-gray-900">{{ $permanentOrder->product->sku }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">পরিমাণ:</dt>
                            <dd class="font-medium text-gray-900">{{ number_format($permanentOrder->quantity, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">একক মূল্য:</dt>
                            <dd class="font-medium text-gray-900">৳{{ number_format($permanentOrder->unit_price, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-blue-50 p-6 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">পেমেন্ট সারাংশ</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between items-center pb-3 border-b border-blue-200">
                        <dt class="text-gray-700 text-lg">মোট টাকা:</dt>
                        <dd class="font-bold text-gray-900 text-xl">৳{{ number_format($permanentOrder->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-blue-200">
                        <dt class="text-gray-700 text-lg">পরিশোধিত:</dt>
                        <dd class="font-bold text-green-600 text-xl">৳{{ number_format($permanentOrder->paid_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <dt class="text-gray-900 font-semibold text-xl">বাকি:</dt>
                        <dd class="font-bold text-2xl {{ $permanentOrder->due_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ৳{{ number_format($permanentOrder->due_amount, 2) }}
                        </dd>
                    </div>
                </dl>
            </div>

            @if($permanentOrder->delivery_date)
            <div class="bg-yellow-50 p-4 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700">ডেলিভারি তারিখ:</span>
                    <span class="ml-2 text-gray-900">{{ $permanentOrder->delivery_date->format('d M Y') }}</span>
                </div>
            </div>
            @endif

            @if($permanentOrder->notes)
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h4 class="font-semibold text-gray-700 mb-2">নোট:</h4>
                <p class="text-gray-600">{{ $permanentOrder->notes }}</p>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 mb-2">তৈরি করেছেন:</h4>
                <p class="text-gray-900">{{ $permanentOrder->creator->name }}</p>
                <p class="text-sm text-gray-500">{{ $permanentOrder->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-between">
            <a href="{{ route('manager.permanent-orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ফিরে যান
            </a>
            <form method="POST" action="{{ route('manager.permanent-orders.destroy', $permanentOrder) }}" onsubmit="return confirm('আপনি কি নিশ্চিত?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    ডিলিট করুন
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
