@if($user->role === 'partner' && $user->is_employee)
@php
    $stats = $employeeStats ?? \App\Support\EmployeeProfileStats::forUser($user);
    $daysMapping = [
        'saturday' => 'السبت', 'sunday' => 'الأحد', 'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء', 'wednesday' => 'الأربعاء', 'thursday' => 'الخميس', 'friday' => 'الجمعة',
    ];
@endphp
<div class="mt-8 pt-6 border-t border-gray-200">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-id-badge text-indigo-600"></i>
        بياناتي الوظيفية
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div class="p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-500 block text-xs">وقت الحضور</span>
            <span class="font-bold">{{ $user->work_start_time ? \App\Support\CountryNames::formatWorkStart($user->work_start_time) : '—' }}</span>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-500 block text-xs">وقت الانصراف</span>
            <span class="font-bold">{{ $user->work_end_time ? \Carbon\Carbon::parse($user->work_end_time)->format('H:i') : '—' }}</span>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-500 block text-xs">ساعات العمل اليومية</span>
            <span class="font-bold">{{ $user->daily_work_hours ?? 9 }} ساعة</span>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-500 block text-xs">مدة الاستراحة</span>
            <span class="font-bold">{{ $user->break_minutes ?? 0 }} دقيقة</span>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-500 block text-xs">تأخير مسموح</span>
            <span class="font-bold">{{ $user->allowed_late_minutes ?? 0 }} دقيقة</span>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-500 block text-xs">الراتب الأساسي</span>
            <span class="font-bold">{{ number_format((float) ($user->salary_amount ?? 0), 2) }} {{ $user->salary_currency ?? '' }}</span>
        </div>
    </div>

    @if($user->vacation_days && count($user->vacation_days) > 0)
    <div class="mt-3">
        <span class="text-xs text-gray-500 block mb-1">أيام الإجازة الأسبوعية</span>
        <div class="flex flex-wrap gap-1">
            @foreach($user->vacation_days as $day)
            <span class="px-2 py-0.5 bg-red-50 text-red-700 text-xs rounded">{{ $daysMapping[trim(strtolower($day))] ?? $day }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
        <p class="text-xs text-indigo-700 font-semibold mb-2">إحصائيات {{ $stats['month_label'] }}</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
            <div><span class="text-gray-500 block">أيام حضور</span><span class="font-bold text-lg">{{ $stats['attendance_days'] }}</span></div>
            <div><span class="text-gray-500 block">تأخير (د)</span><span class="font-bold text-lg">{{ $stats['total_late_minutes'] }}</span></div>
            <div><span class="text-gray-500 block">مكافآت</span><span class="font-bold text-lg text-green-600">{{ $stats['total_bonuses'] }}</span></div>
            <div><span class="text-gray-500 block">خصومات</span><span class="font-bold text-lg text-red-600">{{ $stats['total_deductions'] }}</span></div>
        </div>
        <p class="text-xs text-gray-600 mt-2">اليوم: {{ $stats['today_status_label'] }}</p>
    </div>

    <div class="mt-3 flex flex-wrap gap-2">
        <a href="{{ route('dashboard.work-times.index') }}"
            class="text-xs font-bold text-green-700 hover:underline"><i class="fas fa-clock ml-1"></i> سجل الحضور</a>
        <a href="{{ route('dashboard.work-times.calendar') }}"
            class="text-xs font-bold text-indigo-700 hover:underline"><i class="fas fa-calendar-alt ml-1"></i> تقويم الحضور</a>
        <a href="{{ route('dashboard.my-profile') }}"
            class="text-xs font-bold text-gray-700 hover:underline"><i class="fas fa-file-alt ml-1"></i> الملف الوظيفي الكامل</a>
    </div>
</div>
@endif
