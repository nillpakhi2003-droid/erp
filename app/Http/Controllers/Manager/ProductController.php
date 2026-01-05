<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $products = Product::where('business_id', $businessId)->latest()->paginate(15);
        return view('manager.products.index', compact('products'));
    }

    public function create()
    {
        return view('manager.products.create');
    }

    public function store(Request $request)
    {
        $businessId = auth()->user()->business_id;
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required', 
                'string', 
                'max:255',
                \Illuminate\Validation\Rule::unique('products')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })
            ],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['business_id'] = $businessId;
        Product::create($validated);

        return redirect()->route('manager.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // Ensure product belongs to the same business
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to product from different business.');
        }
        
        return view('manager.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Ensure product belongs to the same business
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to product from different business.');
        }
        
        $businessId = auth()->user()->business_id;
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required', 
                'string', 
                'max:255',
                \Illuminate\Validation\Rule::unique('products')->where(function ($query) use ($businessId) {
                    return $query->where('business_id', $businessId);
                })->ignore($product->id)
            ],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);

        return redirect()->route('manager.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Ensure product belongs to the same business
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to product from different business.');
        }
        
        $product->delete();
        return redirect()->route('manager.products.index')->with('success', 'Product deleted successfully.');
    }
}
