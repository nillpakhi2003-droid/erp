@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Super Admin Dashboard</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Total Users</div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Total Owners</div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalOwners }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Total Sales</div>
            <div class="text-3xl font-bold text-green-600">৳{{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Total Profit</div>
            <div class="text-3xl font-bold text-blue-600">৳{{ number_format($totalProfit, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('superadmin.businesses.index') }}" class="block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                    Manage Companies
                </a>
                <a href="{{ route('superadmin.owners.index') }}" class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                    Manage Owners
                </a>
                <a href="{{ route('superadmin.voucher-templates.index') }}" class="block bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                    Voucher Templates (Old)
                </a>
                <a href="{{ route('superadmin.reports') }}" class="block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                    View Reports
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Sales</h2>
            <div class="overflow-auto" style="max-height: 300px;">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-sm">Product</th>
                            <th class="text-left py-2 text-sm">Salesman</th>
                            <th class="text-left py-2 text-sm">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr class="border-b">
                            <td class="py-2 text-sm">{{ $sale->product->name }}</td>
                            <td class="py-2 text-sm">{{ $sale->user->name }}</td>
                            <td class="py-2 text-sm">৳{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
