<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $services = Service::where('name_ar', 'LIKE', '%' . $search . '%')->latest()->paginate(8);
        } else {
            $services = Service::latest()->paginate(8);
        }
        return view('dashboard.services.index', compact('services'));
    }

    // Create Method
    public function create()
    {
        return view('dashboard.services.create');
    }

    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif|max:2048',
            'name_ar' => 'required',
            'name_en' => 'required',
            'evork_commission' => 'required',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        Service::create([
            'image' => $imagePath,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'status' => $request->status,
            'evork_commission' => $request->evork_commission,
            'show_in_partner_screen' => $request->show_in_partner_screen ?? 0,
        ]);

        return redirect()->route('dashboard.services.index')
            ->with('success', 'تمت اضافة الخدمة بنجاح');
    }

    // Show Method
    public function show(Service $service)
    {
        return view('dashboard.services.show', compact('service'));
    }

    // Edit Method
    public function edit(Service $service)
    {
        return view('dashboard.services.edit', compact('service'));
    }

    // Update Method
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
            'name_ar' => 'required',
            'name_en' => 'required',
            'evork_commission' => 'required',
        ]);
        
        $imagePath = $service->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $service->update([
            'image' => $imagePath,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'status' => $request->status,
            'evork_commission' => $request->evork_commission,
            'show_in_partner_screen' => $request->show_in_partner_screen ?? 0,
        ]);

        return redirect()
            ->route('dashboard.services.index')
            ->with('success', 'تم تعديل الخدمة بنجاح');
    }


    // Destroy Method
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()
            ->route('dashboard.services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
