<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\VoucherTemplate;
use App\Models\ProfitRealization;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function print($saleId)
    {
        $sale = Sale::with(['product', 'user.business'])->findOrFail($saleId);
        
        // Get business from the sale user
        $business = $sale->user->business;
        
        // Get voucher template for the business
        $template = $business ? $business->voucherTemplate : null;
        
        return view('voucher.print', compact('sale', 'template'));
    }

    public function paymentVoucher($profitRealizationId)
    {
        $profitRealization = ProfitRealization::with(['sale.product', 'sale.user.business', 'recordedBy'])
            ->findOrFail($profitRealizationId);
        
        $sale = $profitRealization->sale;
        
        // Get business from the sale user
        $business = $sale->user->business;
        
        // Get voucher template for the business
        $template = $business ? $business->voucherTemplate : null;
        
        return view('voucher.payment-voucher', compact('profitRealization', 'sale', 'template'));
    }
}
