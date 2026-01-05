<?php

namespace App\Http\Controllers\Salesman;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\ProfitRealization;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;
        $businessUserIds = \App\Models\User::where('business_id', $businessId)->pluck('id');
        
        // If salesman, show only their sales
        if ($user->hasRole('salesman')) {
            $sales = Sale::where('user_id', $user->id)->with('product')->latest()->paginate(15);
        } 
        // If manager or owner, show all business sales
        else {
            $sales = Sale::whereIn('user_id', $businessUserIds)->with('product')->latest()->paginate(15);
        }
        
        return view('salesman.sales.index', compact('sales'));
    }

    public function create()
    {
        $businessId = auth()->user()->business_id;
        $products = Product::where('business_id', $businessId)->where('current_stock', '>', 0)->get();
        $baseRoute = $this->resolveBaseRoute();
        return view('salesman.sales.create', compact('products', 'baseRoute'));
    }

    public function store(Request $request)
    {
        // Check if due system is enabled for this user
        if ($request->has('is_credit') && $request->is_credit && !auth()->user()->isDueSystemEnabled()) {
            return back()->withErrors(['is_credit' => 'বাকি সিস্টেম বন্ধ আছে'])->withInput();
        }

        // Base validation rules
        $rules = [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'is_credit' => ['nullable'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'expected_clear_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];

        // If credit sale, customer fields are required
        if ($request->has('is_credit') && $request->is_credit) {
            $rules['customer_name'] = ['required', 'string', 'max:255'];
            $rules['customer_phone'] = ['required', 'string', 'max:20'];
        } else {
            $rules['customer_name'] = ['nullable', 'string', 'max:255'];
            $rules['customer_phone'] = ['nullable', 'string', 'max:20'];
        }

        $validated = $request->validate($rules);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->current_stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'পর্যাপ্ত স্টক নেই'])->withInput();
        }

        $totalAmount = $validated['quantity'] * $validated['sell_price'];
        
        // Determine paid amount - if credit not selected, full payment
        $paidAmount = isset($validated['is_credit']) && $validated['is_credit'] 
            ? ($validated['paid_amount'] ?? 0)
            : $totalAmount;
        
        if ($paidAmount > $totalAmount) {
            return back()->withErrors(['paid_amount' => 'পরিশোধিত টাকা মোট টাকার চেয়ে বেশি হতে পারে না'])->withInput();
        }

        // Generate unique voucher number with microseconds for uniqueness
        $voucherNumber = 'V-' . date('YmdHis') . '-' . substr(uniqid(), -4);

        $sale = Sale::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'quantity' => $validated['quantity'],
            'sell_price' => $validated['sell_price'],
            'customer_name' => $validated['customer_name'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'paid_amount' => $paidAmount,
            'expected_clear_date' => $validated['expected_clear_date'] ?? null,
            'voucher_number' => $voucherNumber,
        ]);

        $product->reduceStock($validated['quantity']);

        // Record initial profit realization if payment was made
        if ($paidAmount > 0) {
            $profitRatio = $sale->profit / $sale->total_amount;
            $initialProfit = $paidAmount * $profitRatio;

            ProfitRealization::create([
                'sale_id' => $sale->id,
                'payment_date' => now(),
                'payment_amount' => $paidAmount,
                'profit_amount' => $initialProfit,
                'recorded_by' => auth()->id(),
                'notes' => 'Initial sale payment',
            ]);
        }

        $baseRoute = $this->resolveBaseRoute();

        return redirect()->route($baseRoute . '.sales.index')
            ->with('success', 'বিক্রয় সফলভাবে তৈরি হয়েছে। ভাউচার: ' . $voucherNumber);
    }

    private function resolveBaseRoute(): string
    {
        $user = auth()->user();

        if ($user->hasRole('owner')) {
            return 'owner';
        }

        if ($user->hasRole('manager')) {
            return 'manager';
        }

        return 'salesman';
    }
}
