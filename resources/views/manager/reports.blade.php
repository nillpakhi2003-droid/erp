@extends('layouts.app')

@section('title', 'রিপোর্ট')

@section('content')
<div class="min-h-screen w-full px-2 sm:px-4 lg:px-6">
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">বিক্রয় রিপোর্ট</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('manager.reports') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="date_from" class="block text-xs sm:text-sm font-bold text-gray-700 mb-2">শুরুর তারিখ</label>
                <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700">
            </div>
            <div>
                <label for="date_to" class="block text-xs sm:text-sm font-bold text-gray-700 mb-2">শেষ তারিখ</label>
                <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" class="shadow border rounded w-full py-2 px-3 text-sm sm:text-base text-gray-700">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm sm:text-base">
                    খুঁজুন
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    @if(auth()->user()->isDueSystemEnabled())
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">মোট বিক্রয়</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">মোট লাভ</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ number_format($totalProfit, 2) }}</div>
            <div class="text-xs opacity-75 mt-1">(সব বিক্রয় সহ)</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">নগদ প্রাপ্তি</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ number_format($totalPaid, 2) }}</div>
            <div class="text-xs opacity-75 mt-1">(প্রাপ্ত টাকা)</div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">মোট বকেয়া</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ number_format($totalDue, 2) }}</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">বকেয়া আদায়</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ number_format($dueCollection, 2) }}</div>
            <div class="text-xs opacity-75 mt-1">(এই সময়ের মধ্যে)</div>
        </div>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-2 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6 lg:mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">মোট বিক্রয়</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-lg p-3 sm:p-6 text-white">
            <div class="text-xs sm:text-sm opacity-90">মোট লাভ</div>
            <div class="text-xl sm:text-3xl font-bold">৳{{ number_format($totalProfit, 2) }}</div>
            <div class="text-xs opacity-75 mt-1">(সব বিক্রয়ের লাভ)</div>
        </div>
    </div>
    @endif

    <!-- Due Customers Section -->
    @if($dueCustomers->count() > 0 && auth()->user()->isDueSystemEnabled())
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">বকেয়া গ্রাহকদের তালিকা</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">এই সময়ের মধ্যে যাদের বকেয়া আছে</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">তারিখ</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">কাস্টমার</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">ফোন</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">পণ্য</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">বকেয়া</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($dueCustomers as $customer)
                    <tr>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                            {{ $customer->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                            {{ $customer->customer_name ?? 'N/A' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                            {{ $customer->customer_phone ?? 'N/A' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                            {{ $customer->product->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-red-600 font-bold">
                            ৳{{ number_format($customer->due_amount, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">বিক্রয়ের তালিকা</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">তারিখ</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ভাউচার</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">পণ্য</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">কাস্টমার</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">ফোন</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">সেলসম্যান</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">পরিমাণ</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">বিক্রয়</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">লাভ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                            {{ $sale->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm">
                            <a href="{{ route('voucher.print', $sale->id) }}" target="_blank" 
                               class="font-mono text-blue-600 hover:text-blue-800 hover:underline font-semibold">
                                🧾 {{ $sale->voucher_number ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                            {{ $sale->product->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                            {{ $sale->customer_name ?? '-' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 hidden sm:table-cell">
                            {{ $sale->customer_phone ?? '-' }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden lg:table-cell">
                            {{ $sale->user->name }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                            {{ $sale->quantity }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 font-semibold">
                            ৳{{ number_format($sale->total_amount, 2) }}
                        </td>
                        <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-green-600 font-semibold hidden md:table-cell">
                            ৳{{ number_format($sale->profit, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500 text-sm">
                            কোন বিক্রয় নেই
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
