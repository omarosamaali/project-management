<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailableService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AvailableServiceController extends Controller
{
    // عرض كل الخدمات
    public function index()
    {
        $services = AvailableService::ordered()->paginate(20);
        return view('dashboard.available_services.index', compact('services'));
    }

    // صفحة إضافة خدمة
    public function create()
    {

        $icons = [
            'fa-laptop-code' => 'تطوير المواقع',
            'fa-mobile-alt' => 'تطبيقات الجوال',
            'fa-paint-brush' => 'التصميم',
            'fa-bullhorn' => 'التسويق',
            'fa-video' => 'الفيديو',
            'fa-headset' => 'الدعم الفني',
            'fa-pen' => 'الكتابة',
            'fa-search' => 'SEO',
            'fa-pencil-ruler' => 'UI/UX',
            'fa-microphone' => 'التعليق الصوتي',
            'fa-language' => 'الترجمة',
            'fa-keyboard' => 'إدخال البيانات',
            'fa-camera' => 'التصوير',
            'fa-chart-line' => 'التحليل',
            'fa-code' => 'البرمجة',
            'fa-shopping-cart' => 'التجارة الإلكترونية',
            'fa-database' => 'قواعد البيانات'
        ];

        return view('dashboard.available_services.create', compact('icons'));
    }

    // حفظ الخدمة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'icon' => 'required|string',
            'is_active' => 'boolean',
            'order' => 'integer|min:0'
        ], [
            'name.required' => 'اسم الخدمة مطلوب',
            'description.required' => 'وصف الخدمة مطلوب',
            'icon.required' => 'يجب اختيار أيقونة',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $request->order ?? 0;

        AvailableService::create($validated);

        return redirect()
            ->route('dashboard.available_services.index')
            ->with('success', 'تم إضافة الخدمة بنجاح!');
    }

    // صفحة التعديل
    public function edit(AvailableService $availableService)
    {
        $icons = [
            'fa-laptop-code' => 'تطوير المواقع',
            'fa-mobile-alt' => 'تطبيقات الجوال',
            'fa-paint-brush' => 'التصميم',
            'fa-bullhorn' => 'التسويق',
            'fa-video' => 'الفيديو',
            'fa-headset' => 'الدعم الفني',
            'fa-pen' => 'الكتابة',
            'fa-search' => 'SEO',
            'fa-pencil-ruler' => 'UI/UX',
            'fa-microphone' => 'التعليق الصوتي',
            'fa-language' => 'الترجمة',
            'fa-keyboard' => 'إدخال البيانات',
            'fa-camera' => 'التصوير',
            'fa-chart-line' => 'التحليل',
            'fa-code' => 'البرمجة',
            'fa-shopping-cart' => 'التجارة الإلكترونية',
            'fa-database' => 'قواعد البيانات'
        ];

        return view('dashboard.available_services.edit', compact('availableService',  'icons'));
    }

    // تحديث الخدمة
    public function update(Request $request, AvailableService $availableService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'icon' => 'required|string',
            'is_active' => 'boolean',
            'order' => 'integer|min:0'
        ], [
            'name.required' => 'اسم الخدمة مطلوب',
            'description.required' => 'وصف الخدمة مطلوب',
            'icon.required' => 'يجب اختيار أيقونة',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $request->order ?? 0;

        $availableService->update($validated);

        return redirect()
            ->route('dashboard.available_services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح!');
    }

    // حذف الخدمة
    public function destroy(AvailableService $availableService)
    {
        $availableService->delete();

        return redirect()
            ->route('dashboard.available_services.index')
            ->with('success', 'تم حذف الخدمة بنجاح!');
    }
}
