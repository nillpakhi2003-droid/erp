<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopSettingController extends Controller
{
    public function edit()
    {
        $setting = auth()->user()->shopSetting ?? new ShopSetting([
            'shop_name' => 'My Shop',
            'primary_color' => '#3b82f6',
            'secondary_color' => '#10b981',
            'accent_color' => '#f59e0b',
            'text_color' => '#1f2937',
            'font_family' => 'Inter',
        ]);

        return view('owner.shop-settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_logo' => 'nullable|image|max:2048',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'font_family' => 'required|string|max:100',
            'custom_css' => 'nullable|string',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
        ]);

        $setting = auth()->user()->shopSetting;
        
        if (!$setting) {
            $setting = new ShopSetting();
            $setting->owner_id = auth()->id();
        }

        // Handle logo upload
        if ($request->hasFile('shop_logo')) {
            // Delete old logo if exists
            if ($setting->shop_logo) {
                Storage::disk('public')->delete($setting->shop_logo);
            }
            
            $path = $request->file('shop_logo')->store('shop-logos', 'public');
            $validated['shop_logo'] = $path;
        }

        $setting->fill($validated);
        $setting->save();

        return redirect()->route('owner.shop-settings.edit')
            ->with('success', 'দোকানের সেটিংস সফলভাবে আপডেট হয়েছে!');
    }
}
