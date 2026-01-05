@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">ভাউচার টেমপ্লেট ম্যানেজমেন্ট</h1>
        <a href="{{ route('superadmin.voucher-templates.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
            + নতুন টেমপ্লেট
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Primary Color</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Font Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Watermark</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $template)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $template->owner->name }}</div>
                            <div class="text-sm text-gray-500">{{ $template->owner->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $template->company_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded" style="background: {{ $template->primary_color ?? '#1e40af' }}"></div>
                                <span class="text-sm text-gray-600">{{ $template->primary_color ?? '#1e40af' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $template->font_size ?? '13px' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($template->show_watermark)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $template->watermark_text }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('superadmin.voucher-templates.edit', $template) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('superadmin.voucher-templates.destroy', $template) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            কোন টেমপ্লেট পাওয়া যায়নি
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
