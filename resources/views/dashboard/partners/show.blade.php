@extends('layouts.app')

@section('title', !empty($isOwnProfile) ? 'ملفي الوظيفي' : 'عرض تفاصيل الشريك')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb
        first="الرئيسية"
        :link="!empty($isOwnProfile) ? route('dashboard.my-profile') : route('dashboard.partners.index')"
        :second="!empty($isOwnProfile) ? 'ملفي الوظيفي' : 'الشركاء'"
        :third="$partner->name" />

    <div class="mx-auto max-w-6xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            {{-- الهيدر العلوي --}}
            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-user-tag text-blue-600"></i>
                            {{ $partner->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ !empty($isOwnProfile) ? 'جميع بياناتك: الراتب، الدوام، الحضور والإنصراف' : 'الملف الشخصي والبيانات المالية والصلاحيات' }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        @if(!empty($isAdminView))
                        <a href="{{ route('dashboard.partners.edit', $partner->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1 shadow-md">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        @endif
                        <a href="{{ !empty($isOwnProfile) ? route('dashboard.performance.show') : route('dashboard.partners.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
            </div>

            {{-- ملخص سريع: هذا الشهر + حالة اليوم --}}
            @if($partner->is_employee || $partner->apply_working_hours || $partner->salary_amount)
            <div class="p-6 bg-gray-50 dark:bg-gray-900/40 border-b">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-indigo-600"></i>
                    ملخص {{ $profileStats['month_label'] }}
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm text-center">
                        <p class="text-xs text-gray-500 mb-1">حالة اليوم</p>
                        <p class="font-bold text-indigo-600 text-sm">{{ $profileStats['today_status_label'] }}</p>
                        @if($profileStats['today_worked_seconds'] > 0)
                        <p class="text-[10px] text-gray-400 mt-1">
                            {{ floor($profileStats['today_worked_seconds'] / 3600) }}س
                            {{ floor(($profileStats['today_worked_seconds'] % 3600) / 60) }}د عمل
                        </p>
                        @endif
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm text-center">
                        <p class="text-xs text-gray-500 mb-1">أيام حضور</p>
                        <p class="text-2xl font-black text-green-600">{{ $profileStats['attendance_days'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm text-center">
                        <p class="text-xs text-gray-500 mb-1">أيام انصراف</p>
                        <p class="text-2xl font-black text-gray-700 dark:text-gray-200">{{ $profileStats['checkout_days'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm text-center">
                        <p class="text-xs text-gray-500 mb-1">جلسات استراحة</p>
                        <p class="text-2xl font-black text-orange-500">{{ $profileStats['break_sessions'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm text-center">
                        <p class="text-xs text-gray-500 mb-1">تأخير الشهر</p>
                        <p class="text-2xl font-black text-red-600">{{ $profileStats['total_late_minutes'] }}</p>
                        <p class="text-[10px] text-gray-400">دقيقة</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm text-center">
                        <p class="text-xs text-gray-500 mb-1">مكافآت / خصومات</p>
                        <p class="text-sm font-bold text-green-600">+{{ number_format($profileStats['total_bonuses'], 2) }}</p>
                        <p class="text-sm font-bold text-red-600">-{{ number_format($profileStats['total_deductions'], 2) }}</p>
                    </div>
                </div>
            </div>
            @endif

<div class="p-6 space-y-8">

    {{-- القسم 1: بيانات التواصل والتعاقد الأساسية --}}
    <div class="border-b pb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-id-card-alt text-blue-600"></i> بيانات التواصل والتعاقد
        </h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">الاسم</label>
                <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $partner->name }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
                <span class="text-lg font-bold text-blue-600">{{ $partner->email }}</span>
            </div>
            @if($partner->phone)
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">الهاتف</label>
                <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $partner->phone }}</span>
            </div>
            @endif
            @if(empty($isOwnProfile))
            <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg border border-green-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">نسبة الشريك (%)</label>
                <span class="text-2xl font-black text-green-600">{{ number_format($partner->percentage, 2) }}%</span>
            </div>
            @endif
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">الدولة الأساسية</label>
                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                    {{ $partner->first_country }}
                </span>
            </div>

            <div class="bg-yellow-50 dark:bg-gray-700 p-4 rounded-lg border border-yellow-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">إجمالي المشاريع</label>
                <span class="text-xl font-bold text-yellow-700">{{ $partner->partner_requests_count ?? 0 }} طلب</span>
            </div>
            <div class="bg-purple-50 dark:bg-gray-700 p-4 rounded-lg border border-purple-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">حالة الموظف</label>
                <span
                    class="px-4 py-2 {{ $partner->is_employee ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }} rounded-full text-sm font-bold">
                    <i class="fas {{ $partner->is_employee ? 'fa-user-tie' : 'fa-handshake' }}"></i>
                    {{ $partner->is_employee ? 'موظف' : 'شريك فقط' }}
                </span>
            </div>
        </div>
    </div>

    {{-- القسم 2: تفاصيل الرواتب --}}
    @if($partner->is_employee || $partner->salary_amount || $partner->hiring_date || $partner->apply_salary_scale)
    <div class="pb-6 border-b">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-money-check-alt text-green-600"></i> البيانات المالية (الرواتب)
        </h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-4 bg-white dark:bg-gray-700 border rounded-lg shadow-sm">
                <label class="text-xs text-gray-400 block mb-1">الراتب الأساسي</label>
                <span class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ $partner->salary_amount ? number_format($partner->salary_amount, 2) : '0.00' }}
                    {{ $partner->salary_currency ?? 'غير محدد' }}
                </span>
            </div>
            <div class="p-4 bg-white dark:bg-gray-700 border rounded-lg shadow-sm">
                <label class="text-xs text-gray-400 block mb-1">تاريخ التعيين</label>
                <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $partner->hiring_date ?? '---'
                    }}</span>
            </div>
            <div class="p-4 bg-white dark:bg-gray-700 border rounded-lg shadow-sm">
                <label class="text-xs text-gray-400 block mb-1">قيمة العمل الإضافي/ساعة</label>
                <span class="text-lg font-bold text-green-600">
                    {{ $partner->overtime_hourly_rate ? number_format($partner->overtime_hourly_rate, 2) : '0.00' }}
                </span>
            </div>
        </div>

        @if($partner->salary_attachment)
        <div
            class="mt-4 p-3 bg-blue-50 dark:bg-gray-700 border border-blue-100 rounded-lg flex justify-between items-center">
            <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">
                <i class="fas fa-file-contract"></i> مستند العقد المرفق
            </span>
            <a href="{{ asset('storage/' . $partner->salary_attachment) }}" target="_blank"
                class="px-3 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-download"></i> عرض الملف
            </a>
        </div>
        @endif

        @if($partner->salary_notes)
        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100">
            <label class="text-xs text-gray-400 block mb-1">ملاحظات الراتب:</label>
            <p class="text-sm text-gray-700 dark:text-gray-300 italic">{{ $partner->salary_notes }}</p>
        </div>
        @endif
    </div>
    @endif

    {{-- القسم: تفاصيل الدوام وساعات العمل (يظهر لكل موظف) --}}
    @if($partner->is_employee || $partner->apply_working_hours || $partner->work_start_time || $partner->work_end_time || $partner->daily_work_hours)
    <div class="pb-6 border-b">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-user-clock text-indigo-600"></i> تفاصيل الدوام والعمل
        </h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($partner->country && $partner->country != $partner->first_country)
            <div class="bg-purple-50 dark:bg-gray-700 p-4 rounded-lg border border-purple-100">
                <label class="block text-sm font-medium text-gray-500 mb-1">الدولة (دوام العمل)</label>
                <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-xs font-bold">
                    {{ $partner->country_name }}
                </span>
            </div>
            @endif
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">وقت الحضور</label>
                <span class="text-lg font-bold text-gray-700 dark:text-gray-200">
                    <i class="fas fa-sign-in-alt text-green-500"></i> {{ $partner->work_start_time ?? '—' }}
                </span>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">وقت الانصراف</label>
                <span class="text-lg font-bold text-gray-700 dark:text-gray-200">
                    <i class="fas fa-sign-out-alt text-red-500"></i> {{ $partner->work_end_time ?? '—' }}
                </span>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">ساعات العمل اليومية</label>
                <span class="text-lg font-bold text-gray-700 dark:text-gray-200">
                    <i class="fas fa-business-time text-blue-500"></i> {{ $partner->daily_work_hours ?? '0' }} ساعة
                </span>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">مدة الاستراحة</label>
                <span class="text-lg font-bold text-gray-700 dark:text-gray-200">
                    <i class="fas fa-coffee text-orange-500"></i> {{ $partner->break_minutes ?? '0' }} دقيقة
                </span>
            </div>
            @if($partner->allowed_late_minutes)
            <div class="p-4 bg-orange-50 dark:bg-gray-700 rounded-lg border border-orange-100">
                <label class="text-xs text-orange-600 block mb-1">تأخير مسموح</label>
                <span class="text-lg font-bold text-gray-700 dark:text-gray-200">
                    <i class="fas fa-hourglass-half"></i> {{ $partner->allowed_late_minutes }} دقيقة
                </span>
            </div>
            @endif
        </div>

        @if($partner->vacation_days && count($partner->vacation_days) > 0)
        <div class="mt-4">
            <label class="text-xs text-gray-500 dark:text-gray-400 block mb-2 font-medium">
                <i class="fas fa-calendar-times"></i> أيام الإجازة الأسبوعية:
            </label>
            <div class="flex flex-wrap gap-2">
                @php
                $daysMapping = [
                    'saturday' => 'السبت', 'sunday' => 'الأحد', 'monday' => 'الاثنين',
                    'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة',
                ];
                @endphp
                @foreach($partner->vacation_days as $day)
                @php $cleanDay = trim(strtolower($day)); @endphp
                <span class="px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-100 rounded-md text-xs font-bold">
                    <i class="fas fa-calendar-day"></i> {{ $daysMapping[$cleanDay] ?? $day }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($isOwnProfile) || !empty($isAdminView))
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('dashboard.work-times.index') }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700">
                <i class="fas fa-clock"></i> سجل الحضور
            </a>
            <a href="{{ route('dashboard.work-times.calendar') }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700">
                <i class="fas fa-calendar-alt"></i> تقويم الحضور
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- القسم 3: الأنظمة والخدمات --}}
    @if(empty($isOwnProfile))
    <div class="grid md:grid-cols-2 gap-8 pb-6 border-b">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-boxes text-blue-600"></i> الأنظمة المرتبطة
            </h2>
            <div class="flex flex-wrap gap-2">
                @forelse($partner->systems as $system)
                <span
                    class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-sm font-medium">
                    {{ $system->name_ar }}
                </span>
                @empty
                <span class="text-gray-400">لا توجد أنظمة.</span>
                @endforelse
            </div>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-concierge-bell text-indigo-600"></i> الخدمات المرتبطة
            </h2>
            <div class="flex flex-wrap gap-2">
                @forelse($partner->services as $service)
                <span
                    class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-lg text-sm font-medium">
                    {{ $service->name_ar }}
                </span>
                @empty
                <span class="text-gray-400">لا توجد خدمات.</span>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- القسم 4: جميع الصلاحيات --}}
    @if(empty($isOwnProfile))
    <div class="pb-6 border-b">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-user-shield text-black"></i> صلاحيات الوصول والإدارة
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
            $all_perms = [
            'can_view_projects' => ['الاطلاع على المشاريع', 'fa-project-diagram'],
            'can_view_notes' => ['الاطلاع على الملاحظات', 'fa-sticky-note'],
            'can_propose_quotes' => ['تقديم عرض سعر', 'fa-file-invoice-dollar'],
            'can_enter_knowledge_bank' => ['إدخال بنك معلومات', 'fa-database'],
            'apply_working_hours' => ['تطبيق الحضور والإنصراف', 'fa-clock'],
            'can_request_meetings' => ['إمكانية طلب اجتماع', 'fa-calendar-check'],
            'services_screen_available' => ['شاشة الخدمات', 'fa-th-large'],
            'apply_salary_scale' => ['سلم الرواتب (26 يوم)', 'fa-chart-line']
            ];
            @endphp

            @foreach($all_perms as $key => $data)
            <div
                class="flex flex-col items-center p-3 rounded-lg border {{ $partner->$key ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700' : 'bg-gray-50 dark:bg-gray-700 border-gray-100 dark:border-gray-600 opacity-60' }}">
                <i
                    class="fas {{ $partner->$key ? 'fa-check-circle text-green-600' : 'fa-times-circle text-gray-400' }} mb-1 text-lg"></i>
                <span class="text-[11px] font-bold text-center text-gray-700 dark:text-gray-300">{{ $data[0] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- القسم 6: الخصومات والمدد المسموحة --}}
    @if($partner->allowed_late_minutes || $partner->morning_late_deduction || $partner->break_late_deduction ||
    $partner->early_leave_deduction)
    <div class="pb-6 border-b">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-calculator text-orange-600"></i> نظام الخصومات والتأخير
        </h2>
        <div class="grid md:grid-cols-4 gap-4">
            <div class="p-3 bg-orange-50 dark:bg-gray-700 rounded-lg border border-orange-100 dark:border-orange-700">
                <label class="text-xs text-orange-600 dark:text-orange-400 block mb-1">تأخير مسموح (دقيقة)</label>
                <span class="font-bold text-gray-700 dark:text-gray-200">
                    <i class="fas fa-hourglass-half"></i> {{ $partner->allowed_late_minutes ?? 0 }}
                </span>
            </div>
            <div class="p-3 bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 rounded-lg">
                <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">خصم التأخير الصباحي</label>
                <span class="font-bold text-black">
                    <i class="fas fa-minus-circle"></i> {{ $partner->morning_late_deduction ?
                    number_format($partner->morning_late_deduction, 2) : '0.00' }}
                </span>
            </div>
            <div class="p-3 bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 rounded-lg">
                <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">خصم تأخير الاستراحة</label>
                <span class="font-bold text-black">
                    <i class="fas fa-minus-circle"></i> {{ $partner->break_late_deduction ?
                    number_format($partner->break_late_deduction, 2) : '0.00' }}
                </span>
            </div>
            <div class="p-3 bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600 rounded-lg">
                <label class="text-xs text-gray-500 dark:text-gray-400 block mb-1">خصم الخروج المبكر</label>
                <span class="font-bold text-black">
                    <i class="fas fa-minus-circle"></i> {{ $partner->early_leave_deduction ?
                    number_format($partner->early_leave_deduction, 2) : '0.00' }}
                </span>
            </div>
        </div>
    </div>
    @endif

    {{-- القسم 7: الملاحظات الإدارية --}}
    @if(($partner->note_title || $partner->note_details) && (empty($isOwnProfile) || $partner->is_visible_to_employee))
    <div class="pb-6 border-b">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-sticky-note text-yellow-500"></i> ملاحظة إدارية إضافية
        </h2>
        <div
            class="bg-yellow-50 dark:bg-gray-700 p-5 rounded-xl border border-yellow-100 dark:border-yellow-700 relative">
            @if($partner->is_visible_to_employee)
            <span
                class="absolute top-2 left-2 px-2 py-0.5 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 text-[10px] rounded-full font-bold">
                <i class="fas fa-eye"></i> مرئي للموظف
            </span>
            @else
            <span
                class="absolute top-2 left-2 px-2 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-[10px] rounded-full font-bold">
                <i class="fas fa-eye-slash"></i> مخفي عن الموظف
            </span>
            @endif

            <div class="flex justify-between mb-2 mt-4">
                <h3 class="font-bold text-gray-800 dark:text-white">{{ $partner->note_title ?? 'بدون عنوان' }}</h3>
                @if($partner->note_date)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-calendar"></i> {{ $partner->note_date }}
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $partner->note_details }}</p>

            @if($partner->note_attachment)
            <div class="mt-4 pt-3 border-t border-yellow-200 dark:border-yellow-700 flex items-center justify-between">
                <span class="text-xs font-medium text-yellow-800 dark:text-yellow-300 italic">
                    <i class="fas fa-paperclip"></i> يوجد ملف مرفق مع الملاحظة
                </span>
                <a href="{{ asset('storage/' . $partner->note_attachment) }}" target="_blank"
                    class="text-xs bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded transition">
                    <i class="fas fa-download"></i> تحميل المرفق
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- القسم 8: بيانات سلم الرواتب (Scale) --}}
    @if($partner->apply_salary_scale)
    <div class="pb-6 border-b bg-blue-50/30 dark:bg-blue-900/10 p-4 rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line text-blue-700"></i> تفاصيل سلم الرواتب (نظام الـ 26 يوم)
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">قيمة الراتب</label>
                <span class="text-xl font-bold text-blue-800 dark:text-blue-300">
                    {{ $partner->salary_amount_scale ? number_format($partner->salary_amount_scale, 2) : '0.00' }}
                    {{ $partner->salary_currency_scale ?? 'غير محدد' }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ التعيين</label>
                <span class="text-lg font-bold text-gray-700 dark:text-gray-200">
                    <i class="fas fa-calendar-plus text-blue-600"></i> {{ $partner->hiring_date_scale ?? '---' }}
                </span>
            </div>
            <div class="flex items-center">
                <div
                    class="px-4 py-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg text-sm font-bold border border-green-200 dark:border-green-700">
                    <i class="fas fa-check-double"></i> نظام الـ 26 يوم مفعل
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- سجل الحضور والإنصراف --}}
    @if($partner->is_employee || $partner->apply_working_hours)
    <div class="pb-6 border-b">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-history text-green-600"></i> سجل الحضور والإنصراف
            </h2>
            @if(!empty($isOwnProfile))
            <a href="{{ route('dashboard.work-times.index') }}"
                class="text-sm text-blue-600 hover:underline font-medium">
                عرض الكل <i class="fas fa-external-link-alt text-xs"></i>
            </a>
            @endif
        </div>
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-600">
            <table class="w-full text-sm text-right text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">الوقت</th>
                        <th class="px-4 py-3">الدولة</th>
                        <th class="px-4 py-3">المصدر</th>
                        <th class="px-4 py-3">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentWorkTimes as $record)
                    @php
                        $typeColors = [
                            'حضور' => 'bg-green-100 text-green-800',
                            'انصراف' => 'bg-gray-200 text-gray-800',
                            'خروج للاستراحة' => 'bg-orange-100 text-orange-800',
                            'دخول من الاستراحة' => 'bg-blue-100 text-blue-800',
                        ];
                        $typeClass = $typeColors[$record->type] ?? 'bg-gray-100 text-gray-600';
                        $isLateAttendance = (bool) ($record->is_late_for_display ?? false);
                        $workStartLabel = $record->work_start_label ?? '09:00';
                    @endphp
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium">{{ $record->date?->format('Y-m-d') ?? $record->date }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $typeClass }}">{{ $record->type }}</span>
                        </td>
                        <td class="px-4 py-3 tabular-nums font-bold">
                            {{ \Illuminate\Support\Str::limit($record->start_time, 8, '') }}
                            @if($isLateAttendance)
                            <p class="text-[10px] text-indigo-600 font-normal mt-0.5">يُحسب من {{ $workStartLabel }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $record->display_country ?? $record->country }}</td>
                        <td class="px-4 py-3 text-xs">{{ $record->sourceLabel() }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500 max-w-[200px] truncate">{{ $record->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">لا توجد سجلات حضور بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- سجل الرواتب --}}
    @if($partner->is_employee || $partner->salary_amount || $recentSalaries->isNotEmpty())
    <div class="pb-6 border-b">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-wallet text-blue-600"></i> سجل الرواتب الشهرية
            </h2>
            @if(!empty($isOwnProfile))
            <a href="{{ route('dashboard.salaries.index') }}"
                class="text-sm text-blue-600 hover:underline font-medium">
                عرض الكل <i class="fas fa-external-link-alt text-xs"></i>
            </a>
            @endif
        </div>
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-600">
            <table class="w-full text-sm text-right text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3">الفترة</th>
                        <th class="px-4 py-3">إضافي</th>
                        <th class="px-4 py-3">خصم</th>
                        <th class="px-4 py-3">مترحّل</th>
                        <th class="px-4 py-3">المستحق</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSalaries as $salary)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-3 font-bold">{{ $salary->month }}/{{ $salary->year }}</td>
                        <td class="px-4 py-3 text-green-600">+{{ number_format($salary->overtime_value, 2) }}</td>
                        <td class="px-4 py-3 text-red-600">-{{ number_format($salary->deduction_value, 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($salary->carried_forward, 2) }}</td>
                        <td class="px-4 py-3 font-black text-blue-700 dark:text-blue-300">{{ number_format($salary->total_due, 2) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('dashboard.salaries.show', $salary) }}"
                                class="text-blue-600 hover:underline text-xs">تفاصيل</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">لا توجد سجلات رواتب مسجّلة.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- المكافآت والخصومات --}}
    @if($partner->is_employee || $recentAdjustments->isNotEmpty())
    <div class="pb-6 border-b">
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-percent text-purple-600"></i> المكافآت والخصومات
            </h2>
            @if(!empty($isOwnProfile))
            <a href="{{ route('dashboard.adjustments.index') }}"
                class="text-sm text-blue-600 hover:underline font-medium">
                عرض الكل <i class="fas fa-external-link-alt text-xs"></i>
            </a>
            @endif
        </div>
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-600">
            <table class="w-full text-sm text-right text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAdjustments as $adj)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-3">{{ $adj->date?->format('Y-m-d') ?? $adj->date }}</td>
                        <td class="px-4 py-3">
                            @if($adj->type === 'bonus')
                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-bold">مكافأة</span>
                            @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs font-bold">خصم</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold {{ $adj->type === 'bonus' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $adj->type === 'bonus' ? '+' : '-' }}{{ number_format($adj->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $adj->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">لا توجد مكافآت أو خصومات.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- القسم الأخير: التواريخ --}}
    @if(empty($isOwnProfile))
    <div class="border-t pt-6 grid grid-cols-2 gap-4 text-xs text-gray-400">
        <div>
            <i class="fas fa-clock"></i> تاريخ الإضافة:
            <span class="font-semibold">{{ $partner->created_at->format('Y-m-d H:i') }}</span>
        </div>
        <div class="text-left">
            <i class="fas fa-sync"></i> آخر تحديث:
            <span class="font-semibold">{{ $partner->updated_at->format('Y-m-d H:i') }}</span>
        </div>
    </div>
    @endif

</div>
        </div>
    </div>
</section>
@endsection