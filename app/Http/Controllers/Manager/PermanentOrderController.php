<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\PermanentOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class PermanentOrderController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        
        $orders = PermanentOrder::where('business_id', $businessId)
            ->with(['product', 'creator'])
            ->latest()
            ->paginate(15);
        
        return view('manager.permanent-orders.index', compact('orders'));
    }

    public function create()
    {
        $businessId = auth()->user()->business_id;
        
        // Check if permanent orders are enabled
        $business = auth()->user()->business;
        if (!$business->enable_permanent_orders) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'স্থায়ী অর্ডার সিস্টেম সক্রিয় নয়। সুপার অ্যাডমিনকে জানান।');
        }
        
        $products = Product::where('business_id', $businessId)->get();
        
        return view('manager.permanent-orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $businessId = auth()->user()->business_id;
        $business = auth()->user()->business;
        
        // Check if permanent orders are enabled
        if (!$business->enable_permanent_orders) {
            return back()->with('error', 'স্থায়ী অর্ডার সিস্টেম সক্রিয় নয়।')->withInput();
        }
        
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'product_id' => ['required', 'exists:products,id,business_id,' . $businessId],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $totalAmount = $validated['quantity'] * $validated['unit_price'];
        $paidAmount = $validated['paid_amount'] ?? 0;

        if ($paidAmount > $totalAmount) {
            return back()->withErrors(['paid_amount' => 'পরিশোধিত টাকা মোট টাকার চেয়ে বেশি হতে পারে না'])->withInput();
        }

        // Check credit limit if enabled
        if ($business->enable_credit_system && $business->credit_limit) {
            $customerDue = PermanentOrder::where('business_id', $businessId)
                ->where('customer_phone', $validated['customer_phone'])
                ->where('status', '!=', 'completed')
                ->sum('due_amount');
            
            $newDue = $totalAmount - $paidAmount;
            
            if (($customerDue + $newDue) > $business->credit_limit) {
                return back()->withErrors(['paid_amount' => 'ক্রেডিট লিমিট অতিক্রম করা যাবে না। সর্বোচ্চ: ৳' . number_format($business->credit_limit, 2)])->withInput();
            }
        }

        PermanentOrder::create([
            'business_id' => $businessId,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'due_amount' => $totalAmount - $paidAmount,
            'order_date' => now(),
            'delivery_date' => $validated['delivery_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('manager.permanent-orders.index')
            ->with('success', 'স্থায়ী অর্ডার সফলভাবে তৈরি হয়েছে');
    }

    public function show(PermanentOrder $permanentOrder)
    {
        // Check business authorization
        if ($permanentOrder->business_id !== auth()->user()->business_id) {
            abort(403);
        }
        
        return view('manager.permanent-orders.show', compact('permanentOrder'));
    }

    public function edit(PermanentOrder $permanentOrder)
    {
        // Check business authorization
        if ($permanentOrder->business_id !== auth()->user()->business_id) {
            abort(403);
        }
        
        $businessId = auth()->user()->business_id;
        $products = Product::where('business_id', $businessId)->get();
        
        return view('manager.permanent-orders.edit', compact('permanentOrder', 'products'));
    }

    public function update(Request $request, PermanentOrder $permanentOrder)
    {
        // Check business authorization
        if ($permanentOrder->business_id !== auth()->user()->business_id) {
            abort(403);
        }
        
        $businessId = auth()->user()->business_id;
        
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string'],
            'product_id' => ['required', 'exists:products,id,business_id,' . $businessId],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'delivery_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,partial,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $totalAmount = $validated['quantity'] * $validated['unit_price'];

        if ($validated['paid_amount'] > $totalAmount) {
            return back()->withErrors(['paid_amount' => 'পরিশোধিত টাকা মোট টাকার চেয়ে বেশি হতে পারে না'])->withInput();
        }

        $permanentOrder->update([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_amount' => $totalAmount,
            'paid_amount' => $validated['paid_amount'],
            'delivery_date' => $validated['delivery_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('manager.permanent-orders.index')
            ->with('success', 'স্থায়ী অর্ডার আপডেট হয়েছে');
    }

    public function destroy(PermanentOrder $permanentOrder)
    {
        // Check business authorization
        if ($permanentOrder->business_id !== auth()->user()->business_id) {
            abort(403);
        }
        
        $permanentOrder->delete();

        return redirect()->route('manager.permanent-orders.index')
            ->with('success', 'স্থায়ী অর্ডার ডিলিট হয়েছে');
    }

    public function printVoucher(PermanentOrder $permanentOrder)
    {
        // Check business authorization
        if ($permanentOrder->business_id !== auth()->user()->business_id) {
            abort(403);
        }
        
        $permanentOrder->load(['product', 'business']);
        
        return view('manager.permanent-orders.voucher', compact('permanentOrder'));
    }
}

