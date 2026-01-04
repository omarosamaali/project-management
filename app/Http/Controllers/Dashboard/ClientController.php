<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $clients = User::where('role', 'client')->where('name', 'LIKE', '%' . $search . '%')->latest()->paginate(8);
        } else {
            $clients = User::where('role', 'client')->latest()->paginate(8);
        }
        return view('dashboard.clients.index', compact('clients'));
    }

    // Create Method
    public function create()
    {
        return view('dashboard.clients.create');
    }

    // Store Method
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'client';

        User::create($validated);

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    // Show Method
    public function show(User $client)
    {
        return view('dashboard.clients.show', compact('client'));
    }

    // Edit Method
    public function edit(User $client)
    {
        return view('dashboard.clients.edit', compact('client'));
    }

    // Update Method
    public function update(Request $request, User $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$client->id},id",
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['role'] = 'client';

        if(!empty($request->password)) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $client->update($validated);

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'تم تحديث العميل بنجاح');
    }

    // Destroy Method
    public function destroy(User $client)
    {
        $client->delete();

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }
}
