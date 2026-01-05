@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">ভাউচার টেমপ্লেট এডিট করুন</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $business->name }} এর জন্য ভাউচার টেমপ্লেট কাস্টমাইজ করুন</p>
        </div>

        <div class="bg-white shadow rounded-lg p-8">
            <form action="{{ route('superadmin.businesses.update-template', $business) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Company Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">কোম্পানির তথ্য / Company Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">কোম্পানির নাম *</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $template->company_name ?? $business->name) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ফোন নম্বর</label>
                            <input type="text" name="company_phone" value="{{ old('company_phone', $template->company_phone ?? $business->phone) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">ঠিকানা</label>
                            <textarea name="company_address" rows="2" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('company_address', $template->company_address ?? $business->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Header & Footer Text -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">হেডার ও ফুটার টেক্সট</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">হেডার টেক্সট</label>
                            <input type="text" name="header_text" value="{{ old('header_text', $template->header_text ?? '') }}" 
                                   placeholder="যেমন: সর্বোচ্চ মানের পণ্য, সেরা সেবা" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ফুটার টেক্সট</label>
                            <input type="text" name="footer_text" value="{{ old('footer_text', $template->footer_text ?? '') }}" 
                                   placeholder="যেমন: আমাদের সাথে থাকার জন্য ধন্যবাদ" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Design Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">ডিজাইন সেটিংস / Design Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">প্রাথমিক রঙ / Primary Color</label>
                            <div class="flex gap-2">
                                <input type="color" id="primary_color_picker" value="{{ old('primary_color', $template->primary_color ?? '#1e40af') }}" 
                                       class="h-10 w-16 border border-gray-300 rounded cursor-pointer">
                                <input type="text" name="primary_color" id="primary_color_hex" value="{{ old('primary_color', $template->primary_color ?? '#1e40af') }}" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">সেকেন্ডারি রঙ / Secondary Color</label>
                            <div class="flex gap-2">
                                <input type="color" id="secondary_color_picker" value="{{ old('secondary_color', $template->secondary_color ?? '#3b82f6') }}" 
                                       class="h-10 w-16 border border-gray-300 rounded cursor-pointer">
                                <input type="text" name="secondary_color" id="secondary_color_hex" value="{{ old('secondary_color', $template->secondary_color ?? '#3b82f6') }}" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ফন্ট সাইজ / Font Size</label>
                            <select name="font_size" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="11px" {{ ($template->font_size ?? '13px') == '11px' ? 'selected' : '' }}>11px - অতি ছোট</option>
                                <option value="12px" {{ ($template->font_size ?? '13px') == '12px' ? 'selected' : '' }}>12px - ছোট</option>
                                <option value="13px" {{ ($template->font_size ?? '13px') == '13px' ? 'selected' : '' }}>13px - মাঝারি (ডিফল্ট)</option>
                                <option value="14px" {{ ($template->font_size ?? '13px') == '14px' ? 'selected' : '' }}>14px - বড়</option>
                                <option value="15px" {{ ($template->font_size ?? '13px') == '15px' ? 'selected' : '' }}>15px - অতি বড়</option>
                                <option value="16px" {{ ($template->font_size ?? '13px') == '16px' ? 'selected' : '' }}>16px - সবচেয়ে বড়</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">পেজ মার্জিন / Page Margin</label>
                            <select name="page_margin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="3mm" {{ ($template->page_margin ?? '5mm') == '3mm' ? 'selected' : '' }}>3mm - অতি ছোট</option>
                                <option value="5mm" {{ ($template->page_margin ?? '5mm') == '5mm' ? 'selected' : '' }}>5mm - ছোট (ডিফল্ট)</option>
                                <option value="8mm" {{ ($template->page_margin ?? '5mm') == '8mm' ? 'selected' : '' }}>8mm - মাঝারি</option>
                                <option value="10mm" {{ ($template->page_margin ?? '5mm') == '10mm' ? 'selected' : '' }}>10mm - বড়</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">লোগো URL (Optional)</label>
                            <input type="url" name="logo_url" value="{{ old('logo_url', $template->logo_url ?? '') }}" 
                                   placeholder="https://example.com/logo.png" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">লোগোর সরাসরি URL দিন (PNG, JPG, SVG)</p>
                        </div>
                    </div>
                </div>

                <!-- Watermark Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">ওয়াটারমার্ক সেটিংস</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="show_watermark" id="show_watermark" value="1" 
                                   {{ old('show_watermark', $template->show_watermark ?? false) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="show_watermark" class="ml-2 block text-sm font-medium text-gray-700">
                                ওয়াটারমার্ক দেখান
                            </label>
                        </div>

                        <div id="watermark_text_field" style="{{ old('show_watermark', $template->show_watermark ?? false) ? '' : 'display:none;' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">ওয়াটারমার্ক টেক্সট</label>
                            <input type="text" name="watermark_text" value="{{ old('watermark_text', $template->watermark_text ?? '') }}" 
                                   placeholder="যেমন: PAID, ORIGINAL, COPY" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('superadmin.businesses.index') }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                        বাতিল করুন
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-purple-800 transition">
                        সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Color picker synchronization
    const primaryPicker = document.getElementById('primary_color_picker');
    const primaryHex = document.getElementById('primary_color_hex');
    const secondaryPicker = document.getElementById('secondary_color_picker');
    const secondaryHex = document.getElementById('secondary_color_hex');
    const showWatermark = document.getElementById('show_watermark');
    const watermarkField = document.getElementById('watermark_text_field');

    primaryPicker.addEventListener('input', (e) => {
        primaryHex.value = e.target.value;
    });

    primaryHex.addEventListener('input', (e) => {
        if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            primaryPicker.value = e.target.value;
        }
    });

    secondaryPicker.addEventListener('input', (e) => {
        secondaryHex.value = e.target.value;
    });

    secondaryHex.addEventListener('input', (e) => {
        if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            secondaryPicker.value = e.target.value;
        }
    });

    showWatermark.addEventListener('change', (e) => {
        watermarkField.style.display = e.target.checked ? 'block' : 'none';
    });
</script>
@endsection
