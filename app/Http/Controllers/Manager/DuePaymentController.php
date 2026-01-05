<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\ProfitRealization;
use Illuminate\Http\Request;

class DuePaymentController extends Controller
{
    public function index()
    {
        // Check if due system is enabled
        if (!auth()->user()->isDueSystemEnabled()) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'বাকি সিস্টেম বন্ধ আছে');
        }

        $businessId = auth()->user()->business_id;
        $businessUserIds = \App\Models\User::where('business_id', $businessId)->pluck('id');

        $dueSales = Sale::where('due_amount', '>', 0)
            ->whereIn('user_id', $businessUserIds)
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('manager.due-payments.index', compact('dueSales'));
    }

    public function update(Request $request, Sale $sale)
    {
        // Check if due system is enabled
        if (!auth()->user()->isDueSystemEnabled()) {
            return redirect()->back()->with('error', 'বাকি সিস্টেম বন্ধ আছে');
        }

        // Check if sale belongs to same business
        $businessId = auth()->user()->business_id;
        $businessUserIds = \App\Models\User::where('business_id', $businessId)->pluck('id');
        
        if (!$businessUserIds->contains($sale->user_id)) {
            abort(403, 'Unauthorized access to sale from different business.');
        }

        $request->validate([
            'payment_amount' => 'required|numeric|min:0|max:' . $sale->due_amount,
        ]);

        // Calculate profit for this payment
        $profitRatio = $sale->profit / $sale->total_amount;
        $profitForThisPayment = $request->payment_amount * $profitRatio;

        // Update paid amount
        $sale->paid_amount += $request->payment_amount;
        $sale->save();

        // Record profit realization
        ProfitRealization::create([
            'sale_id' => $sale->id,
            'payment_date' => now(),
            'payment_amount' => $request->payment_amount,
            'profit_amount' => $profitForThisPayment,
            'recorded_by' => auth()->id(),
            'notes' => 'Manager payment collection',
        ]);

        return redirect()->back()->with('success', 'পেমেন্ট সফলভাবে আপডেট হয়েছে');
    }
}
