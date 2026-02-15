<?php

namespace App\Http\Controllers;

use App\Models\Logo;
use Illuminate\Support\Facades\Auth;
use App\Models\Requests;
use App\Models\MyStore;
use App\Models\System;
use App\Models\Service;
use App\Models\Course;

class SystemController extends Controller
{
    public function index()
    {
        // 1. جلب البيانات من الداتابيز
        $systems = System::where('status', 'active')
            ->withCount('payments')
            ->with(['service'])
            ->get();

        $courses = Course::where('status', 'active')
            ->withCount('payments')
            ->with(['service'])
            ->get();

        $stores = MyStore::where('status', 'نشط')
            ->withCount('payments')
            ->with(['service'])
            ->get();

        // 2. معالجة الأنظمة
        $items = $systems->map(function ($system) {
            return (object) [
                'type' => 'system',
                'id' => $system->id,
                'service_id' => $system->service_id,
                'name_ar' => $system->name_ar,
                'name_en' => $system->name_en,
                'description_ar' => $system->description_ar,
                'description_en' => $system->description_en,
                'main_image' => $system->main_image,
                'price' => $system->price,
                'service_name_ar' => $system->service?->name_ar,
                'total_participants' => ($system->payments_count ?? 0) + ($system->counter ?? 0),
                'execution_days_to' => $system->execution_days_to ?? null,
                'counter' => $system->counter ?? 0,
                'route' => route('system.show', $system),
            ];
        })
            // 3. دمج ومعالجة الدورات
            ->concat($courses->map(function ($course) {
                $total_capacity = $course->counter ?? 0;
                $current_payments = $course->payments_count ?? 0;
                $remaining = $total_capacity - $current_payments;
                return (object) [
                    'type' => 'course',
                    'id' => $course->id,
                    'service_id' => $course->service_id,
                    'name_ar' => $course->name_ar,
                    'name_en' => $course->name_en,
                    'description_ar' => $course->description_ar,
                    'description_en' => $course->description_en,
                    'main_image' => $course->main_image,
                    'price' => $course->price,
                    'service_name_ar' => $course->service?->name_ar,
                    'total_participants' => $remaining > 0 ? $remaining : 0,
                    'count_days' => $course->count_days ?? 0,
                    'start_date' => $course->start_date,
                    'end_date' => $course->end_date,
                    'route' => route('courses.show', $course),
                ];
            }))
            // 4. دمج ومعالجة المتاجر (الإضافة الجديدة)
            // ... الكود السابق للأنظمة والدورات ...

            ->concat($stores->map(function ($store) {


                $obj = new \stdClass();
                $obj->type = 'store';
                $obj->id = $store->id;
                $obj->service_id = $store->service_id;
                $obj->name_ar = $store->name_ar;
                $obj->name_en = $store->name_en;
                $obj->description_ar = $store->description_ar;
                $obj->description_en = $store->description_en;
                $obj->main_image = $store->main_image;
                $obj->price = $store->price;
                $obj->original_price = $store->original_price;
                $obj->service_name_ar = $store->service?->name_ar;
                $obj->total_participants = $store->payments_count ?? 0;
                $obj->execution_days = $store->execution_days;
                $obj->support_days = $store->support_days;
                $obj->route = route('stores.show', $store->id);

                return $obj;
            }));

        $logos = Logo::all();
        $services = Service::where('status', 'active')->get();

        return view('system.index', compact('items', 'logos', 'services'));
    }
    // في SystemController.php
    public function show(System $system)
    {
        $system->load(['partners', 'service']);
        
        // حساب المقاعد المتبقية
        $capacity = $system->counter ?? 0;
        $enrolled = \App\Models\Payment::where('system_id', $system->id)->count();
        $remaining_seats = $capacity - $enrolled;
        $is_purchased = Requests::where('client_id', Auth::id())
        ->where('system_id', $system->id)
        ->exists();
        
        $related_systems = System::where('service_id', $system->service_id)
            ->where('id', '!=', $system->id)
            ->where('status', 'active')
            ->limit(6)
        ->get();
        return view('system.show', compact('system', 'is_purchased', 'remaining_seats', 'related_systems'));
    }

    
}
