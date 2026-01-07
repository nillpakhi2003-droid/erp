<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Business;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $businessId = $this->getBusinessId();
        $products = Product::where('business_id', $businessId)->latest()->paginate(15);
        return view('manager.products.index', compact('products'));
    }

    public function create()
    {
        return view('manager.products.create');
    }

    public function store(Request $request)
    {
        $businessId = $this->getBusinessId();
        
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

        $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
        return redirect()->route($routePrefix . '.products.index')->with('success', 'পণ্য সফলভাবে তৈরি হয়েছে। এখন স্টক পেজ থেকে স্টক যোগ করুন।');
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
        
        $businessId = $this->getBusinessId();
        
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
            'add_stock' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Update product details
        $product->update([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'purchase_price' => $validated['purchase_price'],
            'sell_price' => $validated['sell_price'],
        ]);

        // If owner adds stock, create a stock entry and update current_stock
        if (auth()->user()->hasRole('owner') && !empty($validated['add_stock']) && $validated['add_stock'] > 0) {
            // Create stock entry
            \App\Models\StockEntry::create([
                'product_id' => $product->id,
                'quantity' => $validated['add_stock'],
                'purchase_price' => $validated['purchase_price'],
                'added_by' => auth()->id(),
                'business_id' => $businessId,
            ]);

            // Update current stock
            $product->increment('current_stock', $validated['add_stock']);
        }

        $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
        return redirect()->route($routePrefix . '.products.index')->with('success', 'পণ্য সফলভাবে আপডেট হয়েছে।');
    }

    public function destroy(Product $product)
    {
        // Ensure product belongs to the same business
        if ($product->business_id !== auth()->user()->business_id) {
            abort(403, 'Unauthorized access to product from different business.');
        }
        
        // Check if product has sales or stock
        if ($product->sales()->exists()) {
            return redirect()->back()->with('error', 'এই পণ্যের বিক্রয় রেকর্ড আছে। ডিলিট করা যাবে না।');
        }
        
        if ($product->current_stock > 0) {
            return redirect()->back()->with('error', 'পণ্যে স্টক আছে। প্রথমে স্টক শূন্য করুন।');
        }
        
        $product->delete();
        $routePrefix = auth()->user()->hasRole('owner') ? 'owner' : 'manager';
        return redirect()->route($routePrefix . '.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Resolve or provision a business id for the authenticated user so owners can create stock/products.
     */
    private function getBusinessId(): int
    {
        $user = auth()->user();

        if ($user->business_id) {
            return $user->business_id;
        }

        $business = Business::first() ?: Business::create([
            'name' => 'Default Business',
            'owner_name' => $user->name ?? 'Owner',
            'phone' => $user->phone ?? null,
            'address' => 'N/A',
        ]);

        $user->business_id = $business->id;
        $user->save();

        return $business->id;
    }
}
