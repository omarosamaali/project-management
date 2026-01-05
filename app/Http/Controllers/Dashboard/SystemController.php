<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System;
use App\Models\Service;

class SystemController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $systems = System::where('name_ar', 'like', '%' . $search . '%')
            ->orWhere('name_en', 'like', '%' . $search . '%')->latest()->paginate(8);
        } else {
            $systems = System::latest()->paginate(8);
        }
        return view('dashboard.systems.index', compact('systems'));
    }

    // Create Method
    public function create()
    {
        $services = Service::where('status', 'active')->get();
        return view('dashboard.systems.create', compact('services'));
    }

    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'price' => 'required|numeric',
            'execution_days_from' => 'required|numeric',
            'execution_days_to' => 'required|numeric',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'requirements_ar' => 'nullable|array|min:1',
            'requirements_ar.*' => 'nullable|string',
            'requirements_en' => 'nullable|array|min:1',
            'requirements_en.*' => 'nullable|string',
            'features_ar' => 'nullable|array|min:1',
            'features_ar.*' => 'nullable|string',
            'features_en' => 'nullable|array|min:1',
            'features_en.*' => 'nullable|string',
            'buttons_text_ar' => 'nullable|array',
            'buttons_text_ar.*' => 'nullable|string',
            'buttons_text_en' => 'nullable|array',
            'buttons_text_en.*' => 'nullable|string',
            'buttons_link' => 'nullable|array',
            'buttons_link.*' => 'nullable|url',
            'buttons_color' => 'nullable|array',
            'buttons_color.*' => 'nullable|string',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:9048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'support_days' => 'required|numeric',
            'service_id' => 'required|exists:services,id',
            'counter' => 'required|numeric',
        ]);

        $requirements = [];
        foreach ($request->requirements_ar as $index => $req_ar) {
            $requirements[] = [
                'ar' => $req_ar,
                'en' => $request->requirements_en[$index] ?? ''
            ];
        }

        $features = [];
        foreach ($request->features_ar as $index => $feat_ar) {
            $features[] = [
                'ar' => $feat_ar,
                'en' => $request->features_en[$index] ?? ''
            ];
        }

        $buttons = [];
        if ($request->has('buttons_text_ar') && is_array($request->buttons_text_ar)) {
            foreach ($request->buttons_text_ar as $index => $text_ar) {
                if (!empty($text_ar)) {
                    $buttons[] = [
                        'text_ar' => $text_ar,
                        'text_en' => $request->buttons_text_en[$index] ?? '',
                        'link' => $request->buttons_link[$index] ?? '',
                        'color' => $request->buttons_color[$index] ?? '#3B82F6'
                    ];
                }
            }
        }

        $data = [
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'price' => $request->price,
            'execution_days_from' => $request->execution_days_from,
            'execution_days_to' => $request->execution_days_to,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'requirements' => $requirements,
            'features' => $features,
            'buttons' => $buttons,
            'status' => $request->status,
            'support_days' => $request->support_days,
            'service_id' => $request->service_id,
            'counter' => $request->counter,
            'system_external' => $request->has('system_external') ? 1 : 0,
            'external_url' => $request->has('system_external') ? $request->external_url : null
        ];

        if ($request->hasFile('main_image')) {
            $mainImage = $request->file('main_image');
            $mainImageName = time() . '_main.' . $mainImage->getClientOriginalExtension();
            $mainImage->move(public_path('uploads/systems'), $mainImageName);
            $data['main_image'] = 'uploads/systems/' . $mainImageName;
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $key => $image) {
                $imageName = time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/systems'), $imageName);
                $images[] = 'uploads/systems/' . $imageName;
            }
            $data['images'] = $images;
        }

        System::create($data);

        return redirect()->route('dashboard.systems.index')->with('success', 'تم إضافة النظام بنجاح');
    }

    // Show Method
    public function show(string $id)
    {
        $system = System::findOrFail($id);
        return view('dashboard.systems.show', compact('system'));
    }

    // Edit Method
    public function edit(string $id)
    {
        $system = System::findOrFail($id);
        $services = Service::where('status', 'active')->get();
        return view('dashboard.systems.edit', compact('system', 'services'));
    }

    // Update Method
    public function update(Request $request, System $system)
    {
        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'price' => 'required|numeric',
            'execution_days_from' => 'required|numeric',
            'execution_days_to' => 'required|numeric',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'requirements_ar' => 'required|array|min:1',
            'requirements_en' => 'required|array|min:1',
            'features_ar' => 'required|array|min:1',
            'features_en' => 'required|array|min:1',
            'buttons_text_ar' => 'nullable|array',
            'buttons_text_ar.*' => 'nullable|string',
            'buttons_text_en' => 'nullable|array',
            'buttons_text_en.*' => 'nullable|string',
            'buttons_link' => 'nullable|array',
            'buttons_link.*' => 'nullable|url',
            'buttons_color' => 'nullable|array',
            'buttons_color.*' => 'nullable|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'support_days' => 'required|numeric',
            'service_id' => 'required|exists:services,id',
            'counter' => 'required|numeric'
        ]);

        $requirements = [];
        foreach ($request->requirements_ar as $index => $req_ar) {
            $requirements[] = [
                'ar' => $req_ar,
                'en' => $request->requirements_en[$index] ?? ''
            ];
        }

        $features = [];
        foreach ($request->features_ar as $index => $feat_ar) {
            $features[] = [
                'ar' => $feat_ar,
                'en' => $request->features_en[$index] ?? ''
            ];
        }

        $buttons = [];
        if ($request->has('buttons_text_ar') && is_array($request->buttons_text_ar)) {
            foreach ($request->buttons_text_ar as $index => $text_ar) {
                if (!empty($text_ar)) {
                    $buttons[] = [
                        'text_ar' => $text_ar,
                        'text_en' => $request->buttons_text_en[$index] ?? '',
                        'link' => $request->buttons_link[$index] ?? '',
                        'color' => $request->buttons_color[$index] ?? '#3B82F6'
                    ];
                }
            }
        }

        $data = [
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'price' => $request->price,
            'execution_days_from' => $request->execution_days_from,
            'execution_days_to' => $request->execution_days_to,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'requirements' => $requirements,
            'features' => $features,
            'buttons' => $buttons,
            'status' => $request->status,
            'support_days' => $request->support_days,
            'service_id' => $request->service_id,
            'counter' => $request->counter,
            'system_external' => $request->has('system_external') ? 1 : 0,
            'external_url' => $request->has('system_external') ? $request->external_url : null

        ];

        if ($request->hasFile('main_image')) {
            if ($system->main_image && !filter_var($system->main_image, FILTER_VALIDATE_URL)) {
                if (file_exists(public_path($system->main_image))) {
                    unlink(public_path($system->main_image));
                }
            }

            $mainImage = $request->file('main_image');
            $mainImageName = time() . '_main.' . $mainImage->getClientOriginalExtension();
            $mainImage->move(public_path('uploads/systems'), $mainImageName);
            $data['main_image'] = 'uploads/systems/' . $mainImageName;
        }

        $existingImages = $system->images ?? [];
        $keepImages = $request->input('keep_images', []);

        $newExistingImages = [];
        foreach ($existingImages as $index => $imagePath) {
            if (in_array($index, $keepImages)) {
                $newExistingImages[] = $imagePath;
            } else {
                if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                    if (file_exists(public_path($imagePath))) {
                        unlink(public_path($imagePath));
                    }
                }
            }
        }

        $existingImages = $newExistingImages;

        if ($request->hasFile('images')) {
            $newImages = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/systems'), $imageName);
                $newImages[] = 'uploads/systems/' . $imageName;
            }
            $existingImages = array_merge($existingImages, $newImages);
        }

        $data['images'] = $existingImages;
        $system->update($data);

        return redirect()->route('dashboard.systems.index')->with('success', 'تم تحديث النظام بنجاح');
    }

    // Delete Method
    public function destroy(string $id)
    {
        $system = System::findOrFail($id);
        $system->delete();
        return redirect()->route('dashboard.systems.index')->with('success', 'تم حذف النظام بنجاح');
    }


    public function payments($id)
    {
        $system = System::with(['payments' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('dashboard.systems.payments', compact('system'));
    }
}
