<?php


// app/Http/Controllers/Dashboard/LogoController.php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    public function index()
    {
        $logos = Logo::all();
        return view('dashboard.logos.index', compact('logos'));
    }

    public function create()
    {
        return view('dashboard.logos.create');
    }

    // app/Http/Controllers/Dashboard/LogoController.php

    public function edit(Logo $logo)
    {
        return view('dashboard.logos.edit', compact('logo'));
    }

    public function update(Request $request, Logo $logo)
    {
        $logo->name = $request->name;
        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($logo->image_path)) {
                Storage::disk('public')->delete($logo->image_path);
            }
            $path = $request->file('image')->store('logos', 'public');
            $logo->image_path = $path;
        }

        $logo->save();

        return redirect()->route('dashboard.logos.index')->with('success', 'تم تحديث الشعار بنجاح');
    }

    public function show(Logo $logo)
    {
        return view('dashboard.logos.show', compact('logo'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('logos', 'public');
            Logo::create([
                'name' => $request->name,
                'image_path' => $path,
            ]);
        }
        return redirect()->route('dashboard.logos.index')->with('success', 'تم إضافة الشعار بنجاح');
    }

    public function destroy(Logo $logo)
    {
        Storage::disk('public')->delete($logo->image_path);
        $logo->delete();
        return back();
    }
}
