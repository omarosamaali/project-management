<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Service;
use App\Models\Payment;
use App\Models\MyStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('service')
            ->latest()
            ->paginate(10);

        return view('dashboard.courses.index', compact('courses'));
    }

    public function create()
    {
        $services = Service::all();
        return view('dashboard.courses.create', compact('services'));
    }

    protected function prepareJsonFields(Request $request, array &$data)
    {
        // المتطلبات
        $requirements = [];
        if ($request->filled('requirements_ar') && $request->filled('requirements_en')) {
            foreach ($request->requirements_ar as $index => $ar) {
                $en = $request->requirements_en[$index] ?? '';
                if (trim($ar) || trim($en)) {
                    $requirements[] = [
                        'ar' => trim($ar),
                        'en' => trim($en),
                    ];
                }
            }
        }
        $data['requirements'] = $requirements;

        // المميزات
        $features = [];
        if ($request->filled('features_ar') && $request->filled('features_en')) {
            foreach ($request->features_ar as $index => $ar) {
                $en = $request->features_en[$index] ?? '';
                if (trim($ar) || trim($en)) {
                    $features[] = [
                        'ar' => trim($ar),
                        'en' => trim($en),
                    ];
                }
            }
        }
        $data['features'] = $features;

        // الأزرار
        $buttons = [];
        if ($request->filled('buttons_text_ar')) {
            foreach ($request->buttons_text_ar as $index => $text_ar) {
                $text_en = $request->buttons_text_en[$index] ?? '';
                $link = $request->buttons_link[$index] ?? '';
                $color = $request->buttons_color[$index] ?? '#3B82F6';

                if (trim($text_ar) || trim($text_en)) {
                    $buttons[] = [
                        'text_ar' => trim($text_ar),
                        'text_en' => trim($text_en),
                        'link' => $link,
                        'color' => $color,
                    ];
                }
            }
        }
        $data['buttons'] = $buttons;

        // أيام الراحة - الجديد
        $data['rest_days'] = $request->input('rest_days', []);
    }

    public function store(Request $request)
    {
        $data = $this->validateCourse($request);
        $this->prepareJsonFields($request, $data);

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('courses/main', 'public');
        }

        if ($request->hasFile('images')) {
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('courses/gallery', 'public');
            }
            $data['images'] = $imagesPaths;
        }

        Course::create($data);

        return redirect()->route('dashboard.courses.index')->with('success', 'تم إضافة الدورة بنجاح.');
    }

    public function show(Course $course)
    {
        $course->load(['payments.user']);
        return view('dashboard.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $services = Service::all();
        return view('dashboard.courses.edit', compact('course', 'services'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $this->validateCourse($request, $course->id);
        $this->prepareJsonFields($request, $data);

        if ($request->hasFile('main_image')) {
            if ($course->main_image) {
                Storage::disk('public')->delete($course->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('courses/main', 'public');
        }

        if ($request->hasFile('images')) {
            if ($course->images) {
                foreach ($course->images as $old_img) {
                    Storage::disk('public')->delete($old_img);
                }
            }
            $imagesPaths = [];
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('courses/gallery', 'public');
            }
            $data['images'] = $imagesPaths;
        }

        $course->update($data);

        return redirect()->route('dashboard.courses.index')->with('success', 'تم تحديث بيانات الدورة بنجاح.');
    }

    public function destroy(Course $course)
    {
        if ($course->main_image) {
            Storage::disk('public')->delete($course->main_image);
        }

        if ($course->images) {
            foreach ($course->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $course->delete();

        return redirect()->route('dashboard.courses.index')->with('success', 'تم حذف الدورة وملفاتها بنجاح.');
    }

    protected function validateCourse(Request $request, $id = null)
    {
        return $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'counter' => 'required|integer|min:0',
            'count_days' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'last_date' => 'required|date|before_or_equal:start_date',
            'location_type' => 'required|in:online,on_site',
            'online_link' => 'required_if:location_type,online|nullable|url',
            'venue_name' => 'required_if:location_type,on_site|nullable|string|max:255',
            'venue_map_url' => 'nullable|url',
            'venue_details' => 'nullable|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',

            // المتطلبات والمميزات والأزرار
            'requirements_ar.*' => 'required|string|max:255',
            'requirements_en.*' => 'required|string|max:255',
            'features_ar.*' => 'required|string|max:255',
            'features_en.*' => 'required|string|max:255',
            'buttons_text_ar.*' => 'nullable|string|max:100',
            'buttons_text_en.*' => 'nullable|string|max:100',
            'buttons_link.*' => 'nullable|url|max:500',
            'buttons_color.*' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/i',

            // أيام الراحة - الجديد
            'rest_days' => 'nullable|array',
            'rest_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',

            'service_id' => 'nullable|exists:services,id',
            'status' => 'required|in:active,inactive',
            'main_image' => ($id ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'requirements_ar.*.required' => 'كل متطلب بالعربية مطلوب',
            'features_ar.*.required' => 'كل ميزة بالعربية مطلوبة',
            'main_image.required' => 'الصورة الرئيسية مطلوبة عند الإضافة',
            'rest_days.*.in' => 'يوم الراحة المحدد غير صحيح',
        ]);
    }

    public function payments(Course $course)
    {
        $payments = $course->students()->get();
        return view('dashboard.courses.payments', compact('course', 'payments'));
    }

    public function userShow(Course $course)
    {
        $serivce_id = $course->service_id;
        $related_courses = Course::where('service_id', $serivce_id)
            ->where('id', '!=', $course->id)
            ->where('status', 'active')
            ->limit(6)
            ->get();

        $is_enrolled = $course->isUserEnrolled();

        return view('course.show', compact('course', 'is_enrolled', 'related_courses'));
    }

    public function userShowStore(MyStore $store)
    {
        $serivce_id = $store->service_id;
        $related_stores = MyStore::where('service_id', $serivce_id)
            ->where('id', '!=', $store->id)
            ->where('status', 'نشط')
            ->limit(6)
            ->get();

        $is_enrolled = $store->isUserEnrolled();

        return view('store.show', compact('store', 'is_enrolled', 'related_stores'));
    }

    public function toggleAttendance($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->is_attended = !$payment->is_attended;
        $payment->save();

        return back()->with('success', 'تم تحديث حالة الحضور بنجاح');
    }

    public function showCertificate($paymentId)
    {
        $payment = Payment::with(['user', 'course'])->findOrFail($paymentId);

        if (!$payment->is_attended) {
            return back()->with('error', 'لا يمكن استخراج شهادة لمن لم يحضر');
        }

        return view('dashboard.courses.certificate', compact('payment'));
    }
}
