<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SalesmanController extends Controller
{
    public function index()
    {
        $salesmen = User::role('salesman')->where('created_by', auth()->id())->with('creator')->latest()->paginate(15);
        return view('manager.salesmen.index', compact('salesmen'));
    }

    public function create()
    {
        return view('manager.salesmen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $salesman = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'created_by' => auth()->id(),
            'business_id' => auth()->user()->business_id,
        ]);

        $salesman->assignRole('salesman');

        return redirect()->route('manager.salesmen.index')->with('success', 'Salesman created successfully.');
    }

    public function edit(User $salesman)
    {
        return view('manager.salesmen.edit', compact('salesman'));
    }

    public function update(Request $request, User $salesman)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone,'.$salesman->id],
            'password' => ['nullable', Rules\Password::defaults()],
        ]);

        $salesman->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $salesman->password,
        ]);

        return redirect()->route('manager.salesmen.index')->with('success', 'Salesman updated successfully.');
    }

    public function destroy(User $salesman)
    {
        $salesman->delete();
        return redirect()->route('manager.salesmen.index')->with('success', 'Salesman deleted successfully.');
    }
}
