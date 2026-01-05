<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $businessUserIds = \App\Models\User::where('business_id', $businessId)->pluck('id');
        
        $query = Expense::whereIn('user_id', $businessUserIds);
        
        // Date filtering
        if (request('start_date')) {
            $query->whereDate('expense_date', '>=', request('start_date'));
        }
        
        if (request('end_date')) {
            $query->whereDate('expense_date', '<=', request('end_date'));
        }
        
        // Category filtering
        if (request('category')) {
            $query->where('category', request('category'));
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20)->withQueryString();
        
        // Calculate totals
        $statsQuery = Expense::whereIn('user_id', $businessUserIds);
        if (request('start_date')) {
            $statsQuery->whereDate('expense_date', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $statsQuery->whereDate('expense_date', '<=', request('end_date'));
        }
        if (request('category')) {
            $statsQuery->where('category', request('category'));
        }
        
        $totalExpense = $statsQuery->sum('amount');
        $categories = Expense::categories();
        
        return view('owner.expenses.index', compact('expenses', 'totalExpense', 'categories'));
    }

    public function create()
    {
        $categories = Expense::categories();
        return view('owner.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = auth()->id();

        Expense::create($validated);

        return redirect()->route('owner.expenses.index')->with('success', 'খরচ সফলভাবে যোগ করা হয়েছে।');
    }

    public function edit(Expense $expense)
    {
        $businessId = auth()->user()->business_id;
        $businessUserIds = \App\Models\User::where('business_id', $businessId)->pluck('id');
        
        // Make sure owner can only edit their business expenses
        if (!$businessUserIds->contains($expense->user_id)) {
            abort(403);
        }

        $categories = Expense::categories();
        return view('owner.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $businessId = auth()->user()->business_id;
        $businessUserIds = \App\Models\User::where('business_id', $businessId)->pluck('id');
        
        // Make sure owner can only update their business expenses
        if (!$businessUserIds->contains($expense->user_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'category' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $expense->update($validated);

        return redirect()->route('owner.expenses.index')->with('success', 'খরচ সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Expense $expense)
    {
        // Make sure owner can only delete their own expenses
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('owner.expenses.index')->with('success', 'খরচ সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
