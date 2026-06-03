@extends('layouts.app')
@section('title', 'الحضور والإنصراف')
@section('content')
<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.work-times.index') }}" second="الحضور والإنصراف" />

    <div class="mb-3">
        <a href="{{ route('dashboard.work-times.calendar') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="fas fa-calendar-alt"></i>
            {{ !empty($isEmployeeView) ? 'عرض التقويم' : 'تقويم الحضور والإنصراف' }}
        </a>
    </div>

    <div class="mb-3 flex flex-wrap items-center gap-3 text-xs text-gray-600 dark:text-gray-400">
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-indigo-50 text-indigo-800 border border-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-200">
            <i class="fas fa-globe"></i> ويب — تسجيل من أزرار الهيدر في الموقع
        </span>
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
            <i class="fas fa-hand-paper"></i> يدوي — إدخال من الإدارة
        </span>
    </div>

    @if(!empty($isEmployeeView) && !empty($attendanceWidget['show']))
    <div class="mb-4 p-4 rounded-xl bg-white dark:bg-gray-800 shadow-md border border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-3">تسجيل الدوام — اليوم</h2>
        <x-attendance-widget :widget="$attendanceWidget" :compact="true" />
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">يُحفظ كل ضغط تلقائياً في السجل أدناه ويظهر للإدارة.</p>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="flex bg-black justify-between rounded-lg p-4">
            <div class="text-white">
                <h1 class="text-md font-bold">{{ !empty($isEmployeeView) ? 'سجلاتي' : 'إجمالي السجلات' }}</h1>
                <p class="text-2xl">{{ $allCount }} سجل</p>
            </div>
            <i class="fas fa-history text-white opacity-50 text-3xl"></i>
        </div>
        <div class="flex bg-green-700 justify-between rounded-lg p-4">
            <div class="text-white">
                <h1 class="text-md font-bold">تسجيلات الحضور</h1>
                <p class="text-2xl">{{ $attendanceCount }}</p>
            </div>
            <i class="fas fa-sign-in-alt text-white opacity-50 text-3xl"></i>
        </div>
        @if(empty($isEmployeeView))
        <div class="flex bg-red-700 justify-between rounded-lg p-4">
            <div class="text-white">
                <h1 class="text-md font-bold">تسجيلات الانصراف</h1>
                <p class="text-2xl">{{ $leaveCount }}</p>
            </div>
            <i class="fas fa-sign-out-alt text-white opacity-50 text-3xl"></i>
        </div>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 p-4">
            @if(empty($isEmployeeView))
            <form action="{{ route('dashboard.work-times.index') }}" method="GET" class="w-full md:w-1/2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الموظف..."
                    class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </form>
            <a href="{{ route('dashboard.work-times.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm text-center whitespace-nowrap">
                + إضافة سجل وقت
            </a>
            @else
            <p class="text-sm text-gray-600 dark:text-gray-300">سجل حضورك وانصرافك واستراحاتك لهذا اليوم والأيام السابقة.</p>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        @if(empty($isEmployeeView))
                        <th class="px-4 py-3">الموظف</th>
                        @endif
                        <th class="px-4 py-3">البلد</th>
                        <th class="px-4 py-3">النوع / المصدر</th>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الوقت</th>
                        <th class="px-4 py-3">الملاحظات</th>
                        @if(empty($isEmployeeView))
                        <th class="px-4 py-3 text-center">إجراءات</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($workTimes as $time)
                    @php
                        $dateFormatted = $time->date instanceof \Carbon\Carbon
                            ? $time->date->format('Y-m-d')
                            : \App\Support\WorkTimeMoment::dateKey($time->date);
                        $isLateAttendance = $time->type === 'حضور'
                            && $time->user
                            && \App\Support\WorkHoursCalculator::isLateCheckIn($time->user, $time->date, $time->start_time);
                        $workStartLabel = $time->user
                            ? \App\Support\WorkHoursCalculator::scheduledStartLabel($time->user)
                            : '09:00';
                    @endphp
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        @if(empty($isEmployeeView))
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $time->user->name ?? '—' }}</td>
                        @endif
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <img src="https://flagcdn.com/w40/{{ strtolower($time->country) }}.png"
                                    class="w-6 h-auto rounded-sm shadow-sm" alt="{{ $time->country_name }}">
                                <span class="font-medium text-gray-700 dark:text-gray-200">{{ $time->country_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <x-work-time-type-badge :record="$time" />
                        </td>
                        <td class="px-4 py-3">{{ $dateFormatted }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ \Carbon\Carbon::parse($time->start_time)->format('g:i A') }}</span>
                            @if($isLateAttendance)
                            <p class="text-[10px] text-indigo-600 dark:text-indigo-300 mt-0.5">
                                يُحسب من {{ $workStartLabel }}
                            </p>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $time->notes }}</td>
                        @if(empty($isEmployeeView))
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('dashboard.work-times.edit', $time->id) }}"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-100 p-2 rounded-lg transition"
                                    title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dashboard.work-times.destroy', $time->id) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-700 hover:text-red-900 bg-red-100 p-2 rounded-lg transition"
                                        title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ empty($isEmployeeView) ? 7 : 5 }}" class="px-4 py-8 text-center text-gray-500">
                            لا توجد سجلات بعد.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">
                {{ $workTimes->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
