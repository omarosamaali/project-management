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
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'password'           => 'required|string|min:8',
            'percentage'         => 'required|numeric|min:0|max:100',
            'systems_id'         => 'nullable|array',
            'services_id'        => 'required|array',
            'first_country'      => 'nullable|string',
            'phone'              => 'nullable|string|max:20',
            'salary_amount'      => 'nullable|numeric',
            'work_start_time'    => 'nullable|string',
            'work_end_time'      => 'nullable|string',
            'daily_work_hours'   => 'nullable|numeric',
            'vacation_days'      => 'nullable|array',
        ]);

        try {
            $data = $request->all();

            $data['password']                  = Hash::make($request->password);
            $data['role']                      = 'partner';
            $data['orders']                    = $request->orders ?? 0;
            $data['is_employee']               = $request->has('is_employee');
            $data['can_view_projects']         = $request->has('can_view_projects');
            $data['can_view_notes']            = $request->has('can_view_notes');
            $data['can_propose_quotes']        = $request->has('can_propose_quotes');
            $data['can_enter_knowledge_bank']  = $request->has('can_enter_knowledge_bank');
            $data['apply_working_hours']       = $request->has('apply_working_hours');
            $data['can_request_meetings']      = $request->has('can_request_meetings');
            $data['services_screen_available'] = $request->has('services_screen_available');
            $data['apply_salary_scale']        = $request->has('apply_salary_scale');
            $data['is_visible_to_employee']    = $request->has('is_visible_to_employee');
            $data['first_country']             = $request->first_country;
            $data['country']                   = $request->country;
            $data['phone']                     = $request->phone;

            $partner = User::create($data);

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
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|unique:users,email,' . $partner->id,
            'percentage'             => 'required|numeric|min:0|max:100',
            'systems_id'             => 'nullable|array',
            'services_id'            => 'required|array',
            'note_attachment'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'salary_attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'first_country'          => 'nullable|string',
            'phone'                  => 'nullable|string|max:20',
            'salary_amount'          => 'nullable|numeric',
            'work_start_time'        => 'nullable|string',
            'work_end_time'          => 'nullable|string',
            'daily_work_hours'       => 'nullable|numeric',
            'break_minutes'          => 'nullable|numeric',
            'overtime_hourly_rate'   => 'nullable|numeric',
            'allowed_late_minutes'   => 'nullable|numeric',
            'morning_late_deduction' => 'nullable|numeric',
            'break_late_deduction'   => 'nullable|numeric',
            'early_leave_deduction'  => 'nullable|numeric',
            'vacation_days'          => 'nullable|array',
            'hiring_date'            => 'nullable|date',
        ]);

        $data = [
            // البيانات الأساسية
            'name'                      => $request->name,
            'email'                     => $request->email,
            'percentage'                => $request->percentage,
            'phone'                     => $request->phone,
            'first_country'             => $request->first_country,
            'country'                   => $request->country,

            // الصلاحيات
            'is_employee'               => $request->boolean('is_employee'),
            'can_view_projects'         => $request->boolean('can_view_projects'),
            'can_view_notes'            => $request->boolean('can_view_notes'),
            'can_propose_quotes'        => $request->boolean('can_propose_quotes'),
            'can_enter_knowledge_bank'  => $request->boolean('can_enter_knowledge_bank'),
            'apply_working_hours'       => $request->boolean('apply_working_hours'),
            'can_request_meetings'      => $request->boolean('can_request_meetings'),
            'services_screen_available' => $request->boolean('services_screen_available'),

            // بيانات الملاحظة الإدارية
            'note_title'                => $request->note_title,
            'note_date'                 => $request->note_date,
            'note_details'              => $request->note_details,
            'is_visible_to_employee'    => $request->boolean('is_visible_to_employee'),

            // بيانات الرواتب
            'apply_salary_scale'        => $request->boolean('apply_salary_scale'),
            'salary_year'               => $request->salary_year,
            'salary_month'              => $request->salary_month,
            'salary_notes'              => $request->salary_notes,
            'salary_amount'             => $request->salary_amount,
            'salary_currency'           => $request->salary_currency,
            'hiring_date'               => $request->hiring_date,

            // بيانات ساعات العمل
            'work_start_time'           => $request->work_start_time,
            'work_end_time'             => $request->work_end_time,
            'daily_work_hours'          => $request->daily_work_hours,
            'break_minutes'             => $request->break_minutes,
            'overtime_hourly_rate'      => $request->overtime_hourly_rate,

            // الخصومات
            'allowed_late_minutes'      => $request->allowed_late_minutes,
            'morning_late_deduction'    => $request->morning_late_deduction,
            'break_late_deduction'      => $request->break_late_deduction,
            'early_leave_deduction'     => $request->early_leave_deduction,

            // أيام الإجازة
            'vacation_days'             => $request->vacation_days,
        ];

        // كلمة المرور (اختياري)
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // رفع المرفقات
        if ($request->hasFile('note_attachment')) {
            $data['note_attachment'] = $request->file('note_attachment')->store('attachments/notes', 'public');
        }

        if ($request->hasFile('salary_attachment')) {
            $data['salary_attachment'] = $request->file('salary_attachment')->store('attachments/salaries', 'public');
        }

        $partner->update($data);

        $partner->systems()->sync($request->systems_id ?? []);
        $partner->services()->sync($request->services_id);

        return redirect()->route('dashboard.partners.index')->with('success', 'تم تحديث بيانات الشريك بنجاح');
    }

    // Destroy Method
    public function destroy(User $partner)
    {
        $partner->delete();
        return redirect()->route('dashboard.partners.index')
            ->with('success', 'تم حذف الشريك بنجاح');
    }
}
