<?php

namespace App\Http\Controllers;

use App\Models\Logo;
use Illuminate\Support\Facades\Auth;
use App\Models\Requests;
use App\Models\Service;
use App\Models\System;
use App\Models\Course; // ← أضف هذا السطر

class SystemController extends Controller
{
    public function index()
    {
        // 1. جلب الأنظمة مع عد المدفوعات
        $systems = System::where('status', 'active')
            ->withCount('payments')
            ->with(['service'])
            ->get();

        // 2. جلب الدورات مع عد المدفوعات (العلاقة اسمها payments في موديل Course)
        $courses = Course::where('status', 'active')
            ->withCount('payments')
            ->with(['service'])
            ->get();

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
        })->merge($courses->map(function ($course) {

            // --- الحسبة الصحيحة (عملية طرح) ---
            $total_capacity = $course->counter ?? 0; // القيمة 5 من قاعدة البيانات
            $current_payments = $course->payments_count ?? 0; // القيمة 1 من جدول المدفوعات

            // تم إضافة الـ $ المفقودة هنا لتعريف المتغير بشكل صحيح
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
                // نرسل النتيجة النهائية (4) لتظهر في المتصفح
                'total_participants' => $remaining > 0 ? $remaining : 0,
                'count_days' => $course->count_days ?? 0,
                'route' => route('courses.show', $course),
            ];
        }));

        $logos = \App\Models\Logo::all();
        $services = \App\Models\Service::where('status', 'active')->get();

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

        return view('system.show', compact('system', 'is_purchased', 'remaining_seats'));
    }

    
}
