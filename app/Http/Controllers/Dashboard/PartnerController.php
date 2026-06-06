<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Models\System;
use App\Support\AttendanceRules;
use App\Support\CountryNames;
use App\Support\EmployeeProfileStats;
use App\Support\WorkAttendanceState;
use App\Support\WorkHoursCalculator;
use App\Support\WorkTimeMoment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PartnerController extends Controller
{
    // Index Method
    public function index(Request $request)
    {
        $search = $request->input('search');

        $partners = User::where('role', 'partner')
            ->notBlocked()
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
            ->paginate(8)
            ->through(function (User $partner) {
                CountryNames::sanitizeModelAttributes($partner);

                return $partner;
            });
        $services = Service::all()->each(fn ($s) => CountryNames::sanitizeModelAttributes($s));

        return view('dashboard.partners.index', compact('partners', 'services'));
    }

    public function myProfile()
    {
        $user = Auth::user();
        if (!WorkAttendanceState::isEmployeePartner($user)) {
            abort(403, 'غير مصرح لك');
        }

        return $this->show($user);
    }

    // Show Method
    public function show(User $partner)
    {
        $this->authorizePartnerView($partner);

        $partner->load(['systems', 'services']);

        $this->sanitizePartnerForView($partner);

        $partner->loadCount([
            'systems as partner_requests_count' => function ($query) {
                $query->join('requests', 'systems.id', '=', 'requests.system_id');
            }
        ]);

        $isAdminView = Auth::user()->role === 'admin';
        $isOwnProfile = (int) Auth::id() === (int) $partner->id;

        try {
            $profileStats = EmployeeProfileStats::forUser($partner);
        } catch (\Throwable $e) {
            Log::warning('[PARTNER_SHOW] profileStats failed', [
                'partner_id' => $partner->id,
                'error' => CountryNames::ensureUtf8($e->getMessage()) ?? 'unknown',
            ]);
            $profileStats = [
                'month_label' => now()->translatedFormat('F Y'),
                'attendance_days' => 0,
                'checkout_days' => 0,
                'break_sessions' => 0,
                'total_late_minutes' => 0,
                'total_bonuses' => 0.0,
                'total_deductions' => 0.0,
                'today_status' => 'off',
                'today_status_label' => WorkAttendanceState::statusLabel('off', $partner),
                'today_worked_seconds' => 0,
            ];
        }

        $workStartLabel = WorkHoursCalculator::scheduledStartLabel($partner);

        $recentWorkTimes = $partner->workTimes()
            ->latest('date')
            ->latest('start_time')
            ->limit(20)
            ->get()
            ->map(function ($record) use ($partner, $workStartLabel) {
                CountryNames::sanitizeModelAttributes($record);
                $record->setAttribute('display_country', CountryNames::ensureUtf8(
                    $record->country_name ?? (string) $record->country
                ) ?? '');
                try {
                    $record->setAttribute(
                        'is_late_for_display',
                        $record->type === 'حضور'
                            && WorkHoursCalculator::isLateCheckIn($partner, $record->date, $record->start_time)
                    );
                } catch (\Throwable) {
                    $record->setAttribute('is_late_for_display', false);
                }
                $record->setAttribute('work_start_label', $workStartLabel);

                return $record;
            });

        $recentSalaries = $partner->salaries()
            ->latest('year')
            ->latest('month')
            ->limit(12)
            ->get()
            ->each(function ($salary) {
                CountryNames::sanitizeModelAttributes($salary);
            });

        $recentAdjustments = $partner->employeeAdjustments()
            ->latest('date')
            ->limit(15)
            ->get()
            ->each(function ($adj) {
                CountryNames::sanitizeModelAttributes($adj);
            });

        return view('dashboard.partners.show', compact(
            'partner',
            'isAdminView',
            'isOwnProfile',
            'profileStats',
            'recentWorkTimes',
            'recentSalaries',
            'recentAdjustments'
        ));
    }

    private function authorizePartnerView(User $partner): void
    {
        $auth = Auth::user();
        if (!$auth) {
            abort(403);
        }
        if ($auth->role === 'admin') {
            return;
        }
        if (WorkAttendanceState::isEmployeePartner($auth) && (int) $auth->id === (int) $partner->id) {
            return;
        }
        abort(403, 'غير مصرح لك بعرض هذا الملف');
    }

    private function sanitizePartnerForView(User $partner): void
    {
        CountryNames::sanitizeModelAttributes($partner);

        if (is_array($partner->vacation_days)) {
            $partner->vacation_days = array_map(
                fn ($day) => CountryNames::ensureUtf8((string) $day) ?? (string) $day,
                $partner->vacation_days
            );
        }

        if (is_array($partner->skills)) {
            $partner->skills = array_map(function ($skill) {
                if (is_string($skill)) {
                    return CountryNames::ensureUtf8($skill) ?? '';
                }
                if (is_array($skill)) {
                    return array_map(
                        fn ($v) => is_string($v) ? (CountryNames::ensureUtf8($v) ?? '') : $v,
                        $skill
                    );
                }

                return $skill;
            }, $partner->skills);
        }

        $partner->setAttribute(
            'country_name',
            CountryNames::ensureUtf8(CountryNames::forCode($partner->country) ?? '') ?? ''
        );

        if ($partner->relationLoaded('systems')) {
            foreach ($partner->systems as $system) {
                CountryNames::sanitizeModelAttributes($system);
            }
        }

        if ($partner->relationLoaded('services')) {
            foreach ($partner->services as $service) {
                CountryNames::sanitizeModelAttributes($service);
            }
        }
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

        // إعادة حساب خصم التأخير لأي حضور اليوم لم يُحتسب له خصم بعد
        $this->recalculateTodayLateDeduction($partner->fresh());

        return redirect()->route('dashboard.partners.index')->with('success', 'تم تحديث بيانات الشريك بنجاح');
    }

    private function recalculateTodayLateDeduction(User $partner): void
    {
        try {
            $today = now()->toDateString();
            $records = AttendanceRules::dayRecords($partner, $today);
            $firstCheckIn = $records->where('type', 'حضور')->sortBy('start_time')->first();

            if (!$firstCheckIn) {
                return;
            }

            $checkInTime = WorkTimeMoment::at($today, $firstCheckIn->start_time);
            $late = AttendanceRules::lateDeductionForCheckIn($partner, $today, $checkInTime);

            if ($late['minutes'] > 0) {
                AttendanceRules::createDeductionIfNeeded(
                    $partner,
                    $today,
                    $late['amount'],
                    'خصم تأخير صباحي',
                    createIfZero: true
                );
            }
        } catch (\Throwable $e) {
            Log::error('[recalculate late] ' . $e->getMessage());
        }
    }

    // Destroy Method
    public function destroy(User $partner)
    {
        $partner->delete();
        return redirect()->route('dashboard.partners.index')
            ->with('success', 'تم حذف الشريك بنجاح');
    }
}
