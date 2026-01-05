@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('superadmin.voucher-templates.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Templates
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Voucher Template এডিট করুন</h1>

        <form action="{{ route('superadmin.voucher-templates.update', $voucherTemplate) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Owner Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Owner *</label>
                <select name="owner_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ $voucherTemplate->owner_id == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }} ({{ $owner->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Company Information -->
            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">কোম্পানির তথ্য</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">কোম্পানির নাম *</label>
                        <input type="text" name="company_name" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               value="{{ old('company_name', $voucherTemplate->company_name) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ফোন নম্বর</label>
                        <input type="text" name="company_phone" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               value="{{ old('company_phone', $voucherTemplate->company_phone) }}">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ঠিকানা</label>
                    <textarea name="company_address" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('company_address', $voucherTemplate->company_address) }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Header Text</label>
                    <input type="text" name="header_text" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           value="{{ old('header_text', $voucherTemplate->header_text) }}">
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Footer Text</label>
                    <input type="text" name="footer_text" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           value="{{ old('footer_text', $voucherTemplate->footer_text) }}">
                </div>
            </div>

            <!-- Design Settings -->
            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">ডিজাইন সেটিংস</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="primary_color" 
                                   value="{{ old('primary_color', $voucherTemplate->primary_color ?? '#1e40af') }}"
                                   class="h-10 w-20 border border-gray-300 rounded">
                            <input type="text" name="primary_color_text" 
                                   value="{{ old('primary_color', $voucherTemplate->primary_color ?? '#1e40af') }}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                                   readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="secondary_color" 
                                   value="{{ old('secondary_color', $voucherTemplate->secondary_color ?? '#3b82f6') }}"
                                   class="h-10 w-20 border border-gray-300 rounded">
                            <input type="text" name="secondary_color_text" 
                                   value="{{ old('secondary_color', $voucherTemplate->secondary_color ?? '#3b82f6') }}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                                   readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Font Size</label>
                        <select name="font_size" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="11px" {{ ($voucherTemplate->font_size ?? '13px') == '11px' ? 'selected' : '' }}>11px - Very Small</option>
                            <option value="12px" {{ ($voucherTemplate->font_size ?? '13px') == '12px' ? 'selected' : '' }}>12px - Small</option>
                            <option value="13px" {{ ($voucherTemplate->font_size ?? '13px') == '13px' ? 'selected' : '' }}>13px - Default</option>
                            <option value="14px" {{ ($voucherTemplate->font_size ?? '13px') == '14px' ? 'selected' : '' }}>14px - Medium</option>
                            <option value="15px" {{ ($voucherTemplate->font_size ?? '13px') == '15px' ? 'selected' : '' }}>15px - Large</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Page Margin</label>
                        <select name="page_margin" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="3mm" {{ ($voucherTemplate->page_margin ?? '5mm') == '3mm' ? 'selected' : '' }}>3mm - Minimal</option>
                            <option value="5mm" {{ ($voucherTemplate->page_margin ?? '5mm') == '5mm' ? 'selected' : '' }}>5mm - Default</option>
                            <option value="8mm" {{ ($voucherTemplate->page_margin ?? '5mm') == '8mm' ? 'selected' : '' }}>8mm - Standard</option>
                            <option value="10mm" {{ ($voucherTemplate->page_margin ?? '5mm') == '10mm' ? 'selected' : '' }}>10mm - Large</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo URL (Optional)</label>
                        <input type="url" name="logo_url" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               value="{{ old('logo_url', $voucherTemplate->logo_url) }}">
                    </div>
                </div>
            </div>

            <!-- Watermark Settings -->
            <div class="border-t pt-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Watermark সেটিংস</h3>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="show_watermark" id="show_watermark" value="1"
                           {{ $voucherTemplate->show_watermark ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="show_watermark" class="ml-2 text-sm text-gray-700">
                        Watermark দেখান
                    </label>
                </div>

                <div id="watermark_text_div" style="display: {{ $voucherTemplate->show_watermark ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Watermark Text</label>
                    <input type="text" name="watermark_text" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           value="{{ old('watermark_text', $voucherTemplate->watermark_text ?? 'PAID') }}">
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                    আপডেট করুন
                </button>
                <a href="{{ route('superadmin.voucher-templates.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-semibold">
                    বাতিল
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('show_watermark').addEventListener('change', function() {
    document.getElementById('watermark_text_div').style.display = this.checked ? 'block' : 'none';
});

// Sync color inputs
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    const textInput = colorInput.nextElementSibling;
    
    colorInput.addEventListener('input', function() {
        textInput.value = this.value;
    });
});
</script>
@endsection
