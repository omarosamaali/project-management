<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');

        $partners = User::where('role', 'partner')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', '%' . $search . '%');
            })
            ->with('systems')
            ->withCount([
                'systems as partner_requests_count' => function ($query) {
                    $query->join('requests', 'systems.id', '=', 'requests.system_id');
                }
            ])
            ->latest()
            ->paginate(8);
        $services = Service::all();

        return view('dashboard.partners.index', compact('partners', 'services'));
    }

    // Show Method
    public function show(User $partner)
    {
        $partner->load('systems');

        $partner->loadCount([
            'systems as partner_requests_count' => function ($query) {
                $query->join('requests', 'systems.id', '=', 'requests.system_id');
            }
        ]);

        return view('dashboard.partners.show', compact('partner'));
    }

    // Create Method
    public function create()
    {
        $services = Service::all();
        $systems = System::orderBy('name_ar')->get();
        if ($systems->isEmpty()) {
            return redirect()->route('dashboard.partners.index')
                ->with('error', 'لا توجد أنظمة متاحة. يجب إضافة أنظمة أولاً');
        }

        return view('dashboard.partners.create', compact('systems', 'services'));
    }

    // Store Method
    public function store(Request $request)
    {
        // 1. التحقق من البيانات (Validation)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'percentage' => 'required|numeric|min:0|max:100',
            'systems_id' => 'nullable|array',
            'services_id' => 'required|array',
            'first_country' => 'nullable|string',
            // التحقق من الحقول الجديدة (اختياري لكن مفضل)
            'salary_amount' => 'nullable|numeric',
            'work_start_time' => 'nullable|string',
            'work_end_time' => 'nullable|string',
            'daily_work_hours' => 'nullable|numeric',
            'vacation_days' => 'nullable|array',
        ]);

        try {
            // 2. معالجة حقول الـ Boolean والبيانات الإضافية
            $data = $request->all();

            // تشفير كلمة المرور
            $data['password'] = Hash::make($request->password);
            $data['role'] = 'partner';
            $data['orders'] = $request->orders ?? 0;

            // معالجة قيم الـ Switchers/Checkboxes لضمان تخزينها كـ 0 أو 1
            $data['is_employee'] = $request->has('is_employee');
            $data['can_view_projects'] = $request->has('can_view_projects');
            $data['can_view_notes'] = $request->has('can_view_notes');
            $data['can_propose_quotes'] = $request->has('can_propose_quotes');
            $data['can_enter_knowledge_bank'] = $request->has('can_enter_knowledge_bank');
            $data['apply_working_hours'] = $request->has('apply_working_hours');
            $data['can_request_meetings'] = $request->has('can_request_meetings');
            $data['services_screen_available'] = $request->has('services_screen_available');
            $data['apply_salary_scale'] = $request->has('apply_salary_scale');
            $data['is_visible_to_employee'] = $request->has('is_visible_to_employee');
            $data['first_country'] = $request->first_country;
            $data['country'] = $request->country;
            // 3. إنشاء الشريك
            $partner = User::create($data);

            // 4. ربط الأنظمة والخدمات
            if ($request->has('systems_id')) {
                $partner->systems()->attach($request->systems_id);
            }
            $partner->services()->sync($request->services_id);

            return redirect()->route('dashboard.partners.index')->with('success', 'تم إضافة الشريك بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    // Edit Method
    public function edit(User $partner)
    {
        $systems = System::all();
        $services = Service::all();
        $partner->load('systems');

        return view('dashboard.partners.edit', compact('partner', 'systems', 'services'));
    }

    // Update Method
    public function update(Request $request, User $partner)
    {
        // 1. التحقق من البيانات الأساسية
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $partner->id,
            'percentage' => 'required|numeric|min:0|max:100',
            'systems_id' => 'nullable|array',
            'services_id' => 'required|array',
            // يمكنك إضافة قواعد تحقق للملفات هنا
            'note_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'salary_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'first_country' => 'nullable|string',
        ]);

        // 2. تجميع البيانات الأساسية والصلاحيات
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'percentage' => $validated['percentage'],
            'is_employee' => $request->boolean('is_employee'),

            // الصلاحيات (استخدام boolean لضمان تخزين 0 أو 1)
            'can_view_projects'         => $request->boolean('can_view_projects'),
            'can_view_notes'            => $request->boolean('can_view_notes'),
            'can_propose_quotes'        => $request->boolean('can_propose_quotes'),
            'can_enter_knowledge_bank'  => $request->boolean('can_enter_knowledge_bank'),
            'apply_working_hours'       => $request->boolean('apply_working_hours'),
            'can_request_meetings'      => $request->boolean('can_request_meetings'),
            'services_screen_available' => $request->boolean('services_screen_available'),

            // بيانات الملاحظة الإدارية الجديدة
            'note_title'                => $request->note_title,
            'note_date'                 => $request->note_date,
            'note_details'              => $request->note_details,
            'is_visible_to_employee'    => $request->boolean('is_visible_to_employee'),

            // بيانات دليل الرواتب
            'apply_salary_scale'        => $request->boolean('apply_salary_scale'),
            'salary_year'               => $request->salary_year,
            'salary_month'              => $request->salary_month,
            'salary_notes'              => $request->salary_notes,
            'salary_amount'             => $request->salary_amount,
            'salary_currency'           => $request->salary_currency,
            'hiring_date'               => $request->hiring_date,
            'first_country'             => $request->first_country,
            'country'                   => $request->country,
        ];

        // 3. معالجة كلمة المرور
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 4. معالجة رفع الملفات (المرفقات)
        if ($request->hasFile('note_attachment')) {
            // سيتم تخزين الملف في storage/app/public/attachments/notes
            $data['note_attachment'] = $request->file('note_attachment')->store('attachments/notes', 'public');
        }

        if ($request->hasFile('salary_attachment')) {
            // سيتم تخزين الملف في storage/app/public/attachments/salaries
            $data['salary_attachment'] = $request->file('salary_attachment')->store('attachments/salaries', 'public');
        }

        // 5. تنفيذ التحديث في قاعدة البيانات
        $partner->update($data);

        // 6. تحديث علاقات Many-to-Many (الأنظمة والخدمات)
        $partner->systems()->sync($request->systems_id);
        $partner->services()->sync($request->services_id);

        return redirect()->route('dashboard.partners.index')->with('success', 'تم تحديث بيانات الشريك والموظف بنجاح');
    }

    // Destroy Method
    public function destroy(User $partner)
    {
        $partner->delete();
        return redirect()->route('dashboard.partners.index')
            ->with('success', 'تم حذف الشريك بنجاح');
    }
}
