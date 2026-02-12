<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyStore;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class MyStoreController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); // الفلتر الجديد

        // استعلام أساسي بناءً على الدور
        $query = MyStore::query();

        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::user()->id);
        }

        // حساب الأعداد للفلترة (قبل تطبيق فلتر الحالة الحالي)
        $countsQuery = clone $query;
        $counts = [
            'all' => $countsQuery->count(),
            'pending' => (clone $countsQuery)->where('status', 'قيد المراجعة')->count(),
            'active' => (clone $countsQuery)->where('status', 'نشط')->count(),
            'rejected' => (clone $countsQuery)->where('status', 'مرفوض')->count(),
        ];

        // تطبيق البحث
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name_ar', 'like', '%' . $search . '%')
                    ->orWhere('name_en', 'like', '%' . $search . '%');
            });
        });

        // تطبيق فلتر الحالة
        $query->when($status, function ($q) use ($status) {
            if ($status == 'pending') $q->where('status', 'قيد المراجعة');
            if ($status == 'active') $q->where('status', 'نشط');
            if ($status == 'rejected') $q->where('status', 'مرفوض');
        });

        $systems = $query->latest()->paginate(8)->withQueryString();

        return view('dashboard.my-store.index', compact('systems', 'counts'));
    }

    // Create Method
    public function create()
    {
        $services = Service::where('status', 'active')->get();
        return view('dashboard.my-store.create', compact('services'));
    }

    // Store Method
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'price' => 'required|numeric',
            'execution_days' => 'required|numeric',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'requirements_ar' => 'nullable|array|min:1',
            'requirements_ar.*' => 'nullable|string',
            'original_price' => 'nullable|numeric',
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
            'support_days' => 'required|numeric',
            'service_id' => 'required|exists:services,id',
        ], [
            'required' => 'حقل :attribute مطلوب.',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
            'string' => 'حقل :attribute يجب أن يكون نصاً.',
            'image' => 'الملف المرفق في :attribute يجب أن يكون صورة.',
            'mimes' => 'صيغة :attribute يجب أن تكون (jpeg, png, jpg, gif).',
            'max' => 'حجم :attribute لا يجب أن يتجاوز 20 ميجابايت.',
            'url' => 'الرابط في :attribute غير صحيح.',
            'exists' => 'القيمة المختارة في :attribute غير موجودة.',
            'in' => 'القيمة المختارة في :attribute غير صالحة.',
        ], [
            'name_ar' => 'الاسم بالعربية',
            'name_en' => 'الاسم بالإنجليزية',
            'price' => 'السعر',
            'execution_days_from' => 'وقت التنفيذ (من)',
            'execution_days_to' => 'وقت التنفيذ (إلى)',
            'description_ar' => 'الوصف بالعربية',
            'description_en' => 'الوصف بالإنجليزية',
            'requirements_ar' => 'المتطلبات بالعربية',
            'requirements_en' => 'المتطلبات بالإنجليزية',
            'features_ar' => 'المميزات بالعربية',
            'features_en' => 'المميزات بالإنجليزية',
            'buttons_text_ar' => 'نص الأزرار بالعربية',
            'buttons_link' => 'روابط الأزرار',
            'main_image' => 'الصورة الرئيسية',
            'images' => 'الصور الإضافية',
            'status' => 'الحالة',
            'support_days' => 'أيام الدعم',
            'service_id' => 'الخدمة',
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
            'user_id' => Auth::id(),
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'price' => $request->price,
            'execution_days' => $request->execution_days,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'requirements' => $requirements,
            'features' => $features,
            'buttons' => $buttons,
            'support_days' => $request->support_days,
            'service_id' => $request->service_id,
            'system_external' => 1,
            'onwer_system' => $request->onwer_system,
            'status' => 'قيد المراجعة',
            'original_price' => $request->original_price
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

        MyStore::create($data);

        return redirect()->route('dashboard.my-store.index')->with('success', 'تم إضافة النظام بنجاح');
    }

    // Show Method
    public function show(string $id)
    {
        $system = MyStore::findOrFail($id);
        return view('dashboard.my-store.show', compact('system'));
    }

    // Edit Method
    public function edit(string $id)
    {
        $system = MyStore::findOrFail($id);
        $services = Service::where('status', 'active')->get();
        return view('dashboard.my-store.edit', compact('system', 'services'));
    }

    // Update Method
    public function update(Request $request, $id)
    {
        $system = MyStore::findOrFail($id);

        // التحقق من أن المستخدم هو صاحب المتجر
        if ($system->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المتجر');
        }

        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'price' => 'required|numeric',
            'execution_days' => 'required|numeric',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'requirements_ar' => 'nullable|array|min:1',
            'requirements_ar.*' => 'nullable|string',
            'original_price' => 'nullable|numeric',
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
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
            'support_days' => 'required|numeric',
            'service_id' => 'required|exists:services,id',
        ], [
            'required' => 'حقل :attribute مطلوب.',
            'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
            'string' => 'حقل :attribute يجب أن يكون نصاً.',
            'image' => 'الملف المرفق في :attribute يجب أن يكون صورة.',
            'mimes' => 'صيغة :attribute يجب أن تكون (jpeg, png, jpg, gif).',
            'max' => 'حجم :attribute لا يجب أن يتجاوز 20 ميجابايت.',
            'url' => 'الرابط في :attribute غير صحيح.',
            'exists' => 'القيمة المختارة في :attribute غير موجودة.',
            'in' => 'القيمة المختارة في :attribute غير صالحة.',
        ], [
            'name_ar' => 'الاسم بالعربية',
            'name_en' => 'الاسم بالإنجليزية',
            'price' => 'السعر',
            'execution_days' => 'وقت التنفيذ',
            'description_ar' => 'الوصف بالعربية',
            'description_en' => 'الوصف بالإنجليزية',
            'requirements_ar' => 'المتطلبات بالعربية',
            'requirements_en' => 'المتطلبات بالإنجليزية',
            'features_ar' => 'المميزات بالعربية',
            'features_en' => 'المميزات بالإنجليزية',
            'buttons_text_ar' => 'نص الأزرار بالعربية',
            'buttons_link' => 'روابط الأزرار',
            'main_image' => 'الصورة الرئيسية',
            'images' => 'الصور الإضافية',
            'support_days' => 'أيام الدعم',
            'service_id' => 'الخدمة',
        ]);

        // معالجة المتطلبات
        $requirements = [];
        if ($request->has('requirements_ar') && is_array($request->requirements_ar)) {
            foreach ($request->requirements_ar as $index => $req_ar) {
                if (!empty($req_ar)) {
                    $requirements[] = [
                        'ar' => $req_ar,
                        'en' => $request->requirements_en[$index] ?? ''
                    ];
                }
            }
        }

        // معالجة المميزات
        $features = [];
        if ($request->has('features_ar') && is_array($request->features_ar)) {
            foreach ($request->features_ar as $index => $feat_ar) {
                if (!empty($feat_ar)) {
                    $features[] = [
                        'ar' => $feat_ar,
                        'en' => $request->features_en[$index] ?? ''
                    ];
                }
            }
        }

        // معالجة الأزرار
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

        // تحضير البيانات للتحديث
        $data = [
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'price' => $request->price,
            'execution_days' => $request->execution_days,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'requirements' => $requirements,
            'features' => $features,
            'buttons' => $buttons,
            'support_days' => $request->support_days,
            'service_id' => $request->service_id,
            'original_price' => $request->original_price
        ];

        // معالجة الصورة الرئيسية
        if ($request->hasFile('main_image')) {
            // حذف الصورة القديمة
            if ($system->main_image && file_exists(public_path($system->main_image))) {
                unlink(public_path($system->main_image));
            }

            $mainImage = $request->file('main_image');
            $mainImageName = time() . '_main.' . $mainImage->getClientOriginalExtension();
            $mainImage->move(public_path('uploads/systems'), $mainImageName);
            $data['main_image'] = 'uploads/systems/' . $mainImageName;
        }

        // معالجة الصور الإضافية
        if ($request->hasFile('images')) {
            $newImages = [];
            foreach ($request->file('images') as $key => $image) {
                $imageName = time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/systems'), $imageName);
                $newImages[] = 'uploads/systems/' . $imageName;
            }

            // الاحتفاظ بالصور القديمة المطلوب الاحتفاظ بها
            $keepImages = [];
            if ($request->has('keep_images') && is_array($request->keep_images)) {
                $oldImages = $system->images ?? [];
                foreach ($request->keep_images as $index) {
                    if (isset($oldImages[$index])) {
                        $keepImages[] = $oldImages[$index];
                    }
                }

                // حذف الصور القديمة التي لم يتم الاحتفاظ بها
                foreach ($oldImages as $index => $oldImage) {
                    if (!in_array($index, $request->keep_images)) {
                        if (file_exists(public_path($oldImage))) {
                            unlink(public_path($oldImage));
                        }
                    }
                }
            } else {
                // إذا لم يتم إرسال keep_images، احذف جميع الصور القديمة
                if ($system->images && is_array($system->images)) {
                    foreach ($system->images as $oldImage) {
                        if (file_exists(public_path($oldImage))) {
                            unlink(public_path($oldImage));
                        }
                    }
                }
            }

            // دمج الصور القديمة المحفوظة مع الجديدة
            $data['images'] = array_merge($keepImages, $newImages);
        } elseif ($request->has('keep_images') && is_array($request->keep_images)) {
            // في حالة عدم رفع صور جديدة، احتفظ فقط بالصور المحددة
            $keepImages = [];
            $oldImages = $system->images ?? [];

            foreach ($request->keep_images as $index) {
                if (isset($oldImages[$index])) {
                    $keepImages[] = $oldImages[$index];
                }
            }

            // حذف الصور غير المحفوظة
            foreach ($oldImages as $index => $oldImage) {
                if (!in_array($index, $request->keep_images)) {
                    if (file_exists(public_path($oldImage))) {
                        unlink(public_path($oldImage));
                    }
                }
            }

            $data['images'] = $keepImages;
        }

        // تحديث البيانات
        $system->update($data);

        return redirect()->route('dashboard.my-store.index')->with('success', 'تم تحديث المتجر بنجاح');
    }

    // 
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:نشط,مرفوض,قيد المراجعة'
        ]);

        $system = MyStore::findOrFail($id);
        $system->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة المتجر بنجاح');
    }

    // Delete Method
    public function destroy(string $id)
    {
        $system = MyStore::findOrFail($id);
        $system->delete();
        return redirect()->route('dashboard.my-store.index')->with('success', 'تم حذف النظام بنجاح');
    }

    public function payments($id)
    {
        $system = MyStore::with(['payments' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);
        return view('dashboard.my-store.payments', compact('system'));
    }
}
