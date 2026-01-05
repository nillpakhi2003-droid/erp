<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $products = Product::where('business_id', $businessId)->latest()->get();
        $stockEntries = StockEntry::whereHas('product', fn($q) => $q->where('business_id', $businessId))->with(['product', 'user'])->latest()->paginate(15);
        return view('manager.stock.index', compact('products', 'stockEntries'));
    }

    public function store(Request $request)
    {
        // Check if creating a new product
        if ($request->has('create_new_product')) {
            $businessId = auth()->user()->business_id;
            
            $validated = $request->validate([
                'new_product_name' => ['required', 'string', 'max:255'],
                'new_product_sku' => [
                    'required', 
                    'string', 
                    'max:255',
                    \Illuminate\Validation\Rule::unique('products', 'sku')->where(function ($query) use ($businessId) {
                        return $query->where('business_id', $businessId);
                    })
                ],
                'new_product_price' => ['required', 'numeric', 'min:0'],
                'quantity' => ['required', 'integer', 'min:1'],
                'purchase_price' => ['required', 'numeric', 'min:0'],
            ]);

            // Create new product
            $product = Product::create([
                'business_id' => auth()->user()->business_id,
                'name' => $validated['new_product_name'],
                'sku' => $validated['new_product_sku'],
                'sell_price' => $validated['new_product_price'],
                'current_stock' => 0,
                'purchase_price' => 0,
            ]);

            // Add stock
            $product->addStock(
                $validated['quantity'],
                $validated['purchase_price'],
                auth()->id()
            );

            return redirect()->route('manager.stock.index')->with('success', 'নতুন পণ্য তৈরি এবং স্টক যোগ করা হয়েছে।');
        }

        // Existing product stock addition
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->addStock(
            $validated['quantity'],
            $validated['purchase_price'],
            auth()->id()
        );

        return redirect()->route('manager.stock.index')->with('success', 'স্টক সফলভাবে যোগ করা হয়েছে।');
    }
}
