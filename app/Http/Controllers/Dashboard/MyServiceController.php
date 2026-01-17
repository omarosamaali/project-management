<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MyService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyServiceController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $my_services = MyService::where('user_id', Auth::user()->id)->where('title', 'LIKE', '%' . $search . '%')->latest()->paginate(8);
        } else {
            $my_services = MyService::where('user_id', Auth::user()->id)->latest()->paginate(8);
        }
        return view('dashboard.my_services.index', compact('my_services'));
    }

    // Create Method
    public function create()
    {
        $services = Service::where('status', 'active')->get();
        return view('dashboard.my_services.create', compact('services'));
    }

    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'price' => 'required|numeric',
            'original_price' => 'nullable|numeric',
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
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'status' => 'required|in:active,inactive',
            'support_days' => 'required|numeric',
            'service_id' => 'required|exists:services,id',
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
            'original_price' => $request->original_price,
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
            'user_id' => Auth::user()->id,
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

        MyService::create($data);

        return redirect()->route('dashboard.my_services.index')->with('success', 'تم إضافة النظام بنجاح');
    }

    // Show Method
    public function show(MyService $myService)
    {
        return view('dashboard.my_services.show', compact('myService'));
    }

    // Edit Method
    public function edit(string $id)
    {
        $myService = MyService::findOrFail($id);
        $services = Service::where('status', 'active')->get();
        return view('dashboard.my_services.edit', compact('myService', 'services'));
    }

    // Update Method
    public function update(Request $request, MyService $myService)
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
            'original_price' => 'nullable|numeric',
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
            'user_id' => Auth::user()->id,
            'original_price' => $request->original_price,
        ];

        if ($request->hasFile('main_image')) {
            if ($myService->main_image && !filter_var($myService->main_image, FILTER_VALIDATE_URL)) {
                if (file_exists(public_path($myService->main_image))) {
                    unlink(public_path($myService->main_image));
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
        $myService->update($data);

        return redirect()->route('dashboard.my_services.index')->with('success', 'تم تحديث النظام بنجاح');
    }

    // Destroy Method
    public function destroy(string $id)
    {
        $my_service = MyService::findOrFail($id);
        $my_service->delete();
        return redirect()
            ->route('dashboard.my_services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
