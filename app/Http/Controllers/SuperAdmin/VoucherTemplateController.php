<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\VoucherTemplate;
use App\Models\User;
use Illuminate\Http\Request;

class VoucherTemplateController extends Controller
{
    public function index()
    {
        $templates = VoucherTemplate::with('owner')->get();
        return view('superadmin.voucher-templates.index', compact('templates'));
    }

    public function create()
    {
        $owners = User::role('owner')->get();
        return view('superadmin.voucher-templates.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'header_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'font_size' => 'nullable|string|max:10',
            'page_margin' => 'nullable|string|max:10',
            'logo_url' => 'nullable|url',
            'show_watermark' => 'boolean',
            'watermark_text' => 'nullable|string|max:50',
        ]);

        VoucherTemplate::create($validated);

        return redirect()->route('superadmin.voucher-templates.index')
            ->with('success', 'Voucher Template তৈরি হয়েছে!');
    }

    public function edit(VoucherTemplate $voucherTemplate)
    {
        $owners = User::role('owner')->get();
        return view('superadmin.voucher-templates.edit', compact('voucherTemplate', 'owners'));
    }

    public function update(Request $request, VoucherTemplate $voucherTemplate)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'header_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'font_size' => 'nullable|string|max:10',
            'page_margin' => 'nullable|string|max:10',
            'logo_url' => 'nullable|url',
            'show_watermark' => 'boolean',
            'watermark_text' => 'nullable|string|max:50',
        ]);

        $voucherTemplate->update($validated);

        return redirect()->route('superadmin.voucher-templates.index')
            ->with('success', 'Voucher Template আপডেট হয়েছে!');
    }

    public function destroy(VoucherTemplate $voucherTemplate)
    {
        $voucherTemplate->delete();

        return redirect()->route('superadmin.voucher-templates.index')
            ->with('success', 'Voucher Template মুছে ফেলা হয়েছে!');
    }
}
