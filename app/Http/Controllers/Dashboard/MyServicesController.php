<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MyServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AvailableService;

class MyServicesController extends Controller
{
    private function getAvailableServices()
    {
        // جلب الخدمات من قاعدة البيانات
        return AvailableService::active()->ordered()->get()->map(function ($service) {
            return [
                'id' => $service->slug,
                'name' => $service->name,
                'description' => $service->description,
                'icon' => $service->icon,
                'color' => $service->color
            ];
        })->toArray();
    }

    // صفحة اختيار الخدمات
    public function index()
    {
        $availableServices = $this->getAvailableServices();
        $myService = MyServices::where('user_id', Auth::id())->first();
        $selectedServices = $myService ? $myService->selected_services : [];
        return view('dashboard.my_service.index', compact('availableServices', 'selectedServices'));
    }

    // حفظ/تحديث الخدمات المختارة
    public function store(Request $request)
    {
        $request->validate([
            'services' => 'required|array|min:1',
            'services.*' => 'string'
        ], [
            'services.required' => 'يجب اختيار خدمة واحدة على الأقل',
            'services.min' => 'يجب اختيار خدمة واحدة على الأقل'
        ]);

        MyServices::updateOrCreate(
            ['user_id' => Auth::id()],
            ['selected_services' => $request->services]
        );

        return redirect()
            ->route('dashboard.my_service.index')
            ->with('success', 'تم حفظ خدماتك بنجاح! ✓');
    }

    // عرض الخدمات المحفوظة
    public function show()
    {
        $myService = MyServices::where('user_id', Auth::id())->first();

        if (!$myService || empty($myService->selected_services)) {
            return redirect()
                ->route('dashboard.my_services.index')
                ->with('info', 'لم تقم باختيار أي خدمات بعد');
        }

        $allServices = $this->getAvailableServices();
        $selectedServiceIds = $myService->selected_services;

        // فلترة الخدمات المختارة
        $selectedServices = array_filter($allServices, function ($service) use ($selectedServiceIds) {
            return in_array($service['id'], $selectedServiceIds);
        });

        return view('dashboard.my_service.show', compact('selectedServices'));
    }
}
