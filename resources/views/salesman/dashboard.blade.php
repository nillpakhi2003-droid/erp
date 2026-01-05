@extends('layouts.app')

@section('title', 'সেলসম্যান ড্যাশবোর্ড')

@section('content')
<div class="min-h-screen w-full px-2 sm:px-4 lg:px-6">
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">সেলসম্যান ড্যাশবোর্ড</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="text-xs sm:text-sm text-gray-600">মোট বিক্রয়</div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalSales }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="text-xs sm:text-sm text-gray-600">আজকের বিক্রয়</div>
            <div class="text-2xl sm:text-3xl font-bold text-green-600">৳{{ number_format($todaySales, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="text-xs sm:text-sm text-gray-600">আজকের মোট লাভ</div>
            <div class="text-xl sm:text-2xl font-bold text-blue-600">৳{{ number_format($todayProfit, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">দ্রুত কাজ</h2>
            <div class="space-y-2">
                <a href="{{ route('salesman.sales.create') }}" class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center text-sm sm:text-base">
                    নতুন বিক্রয় তৈরি করুন
                </a>
                <a href="{{ route('salesman.sales.index') }}" class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center text-sm sm:text-base">
                    আমার বিক্রয় দেখুন
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-semibold mb-4">আমার সাম্প্রতিক বিক্রয়</h2>
            <div class="overflow-auto" style="max-height: 300px;">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-xs sm:text-sm">পণ্য</th>
                            <th class="text-left py-2 text-xs sm:text-sm hidden sm:table-cell">পরিমাণ</th>
                            <th class="text-left py-2 text-xs sm:text-sm">টাকা</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mySales as $sale)
                        <tr class="border-b">
                            <td class="py-2 text-xs sm:text-sm">{{ $sale->product->name }}</td>
                            <td class="py-2 text-xs sm:text-sm hidden sm:table-cell">{{ $sale->quantity }}</td>
                            <td class="py-2 text-xs sm:text-sm">৳{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500 text-xs sm:text-sm">কোন বিক্রয় নেই</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
