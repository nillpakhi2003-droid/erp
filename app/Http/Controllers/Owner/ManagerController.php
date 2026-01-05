<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = User::role('manager')->where('created_by', auth()->id())->with('creator')->latest()->paginate(15);
        return view('owner.managers.index', compact('managers'));
    }

    public function create()
    {
        return view('owner.managers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $manager = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'created_by' => auth()->id(),
            'business_id' => auth()->user()->business_id,
        ]);

        $manager->assignRole('manager');

        return redirect()->route('owner.managers.index')->with('success', 'Manager created successfully.');
    }

    public function edit(User $manager)
    {
        return view('owner.managers.edit', compact('manager'));
    }

    public function update(Request $request, User $manager)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone,'.$manager->id],
            'password' => ['nullable', Rules\Password::defaults()],
        ]);

        $manager->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $manager->password,
        ]);

        return redirect()->route('owner.managers.index')->with('success', 'Manager updated successfully.');
    }

    public function destroy(User $manager)
    {
        $manager->delete();
        return redirect()->route('owner.managers.index')->with('success', 'Manager deleted successfully.');
    }
}
