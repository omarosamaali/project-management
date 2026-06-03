@extends('layouts.app')
@section('title', !empty($isEmployeeView) ? 'تقويم حضوري' : 'تقويم الحضور والإنصراف')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet" />
<style>
    #attendance_calendar .fc-daygrid-event {
        white-space: normal;
        border-radius: 6px;
        margin-bottom: 2px;
        font-size: 11px;
        line-height: 1.35;
    }
    #attendance_calendar .fc-daygrid-day-events {
        margin-bottom: 2px;
    }
    #attendance_calendar .fc-daygrid-event-harness {
        margin-top: 1px;
    }
</style>

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb
        first="الرئيسية"
        link="{{ route('dashboard.work-times.index') }}"
        :second="!empty($isEmployeeView) ? 'تقويم حضوري' : 'تقويم الحضور والإنصراف'" />

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                    {{ !empty($isEmployeeView) ? 'تقويم حضوري' : 'تقويم الحضور والإنصراف' }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    عرض بصري لكل تسجيل: حضور، انصراف، واستراحات مع الوقت.
                </p>
            </div>

            @if(empty($isEmployeeView))
            <form method="GET" action="{{ route('dashboard.work-times.calendar') }}" class="flex flex-wrap items-end gap-3">
                <div class="min-w-[220px]">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">الموظف</label>
                    <select name="user_id" id="employee_filter"
                        class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white p-2.5">
                        <option value="">كل الموظفين</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ (string)$selectedUserId === (string)$emp->id ? 'selected' : '' }}>
                            {{ $emp->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">
                    تطبيق
                </button>
                <a href="{{ route('dashboard.work-times.calendar') }}"
                    class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200">
                    إعادة تعيين
                </a>
            </form>
            @endif
        </div>

        <div class="flex flex-wrap gap-2 text-xs mb-4">
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-white" style="background:#16a34a">حضور</span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-white" style="background:#2563eb">انصراف</span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-white" style="background:#ca8a04">خروج للاستراحة</span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-white" style="background:#059669">دخول من الاستراحة</span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                <i class="fas fa-globe"></i> ويب
            </span>
        </div>

        <div id="attendance_calendar"></div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/ar.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('attendance_calendar');
        const isEmployeeView = @json(!empty($isEmployeeView));
        const selectedUserId = @json($selectedUserId);
        const eventsUrl = @json(route('dashboard.work-times.calendar.events'));

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            direction: 'rtl',
            initialView: 'dayGridMonth',
            headerToolbar: {
                right: 'prev,next today',
                center: 'title',
                left: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
            },
            buttonText: {
                today: 'اليوم',
                month: 'شهر',
                week: 'أسبوع',
                day: 'يوم',
                list: 'قائمة',
            },
            height: 'auto',
            navLinks: true,
            nowIndicator: true,
            dayMaxEvents: 5,
            moreLinkClick: 'popover',
            views: {
                dayGridMonth: {
                    eventDisplay: 'list-item',
                    dayMaxEvents: 6,
                },
            },
            events: function(info, successCallback, failureCallback) {
                const params = new URLSearchParams({
                    start: info.startStr,
                    end: info.endStr,
                });
                if (!isEmployeeView) {
                    const filterEl = document.getElementById('employee_filter');
                    const uid = filterEl?.value || selectedUserId;
                    if (uid) {
                        params.set('user_id', uid);
                    }
                }
                fetch(`${eventsUrl}?${params}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                })
                    .then(r => {
                        if (!r.ok) {
                            throw new Error('HTTP ' + r.status);
                        }
                        return r.json();
                    })
                    .then(data => successCallback(Array.isArray(data) ? data : []))
                    .catch(err => {
                        console.error('calendar events:', err);
                        failureCallback(err);
                    });
            },
            eventClick: function(info) {
                const p = info.event.extendedProps;
                const webBadge = p.from_web
                    ? '<span class="inline-flex items-center gap-1 text-indigo-600"><i class="fas fa-globe"></i> تسجيل من الموقع</span>'
                    : '<span class="text-gray-500">تسجيل يدوي</span>';
                Swal.fire({
                    title: info.event.title.replace(' 🌐', ''),
                    html: `
                        <div class="text-right text-sm space-y-2">
                            <p><strong>الموظف:</strong> ${p.employee}</p>
                            <p><strong>النوع:</strong> ${p.type}</p>
                            <p><strong>التاريخ:</strong> ${p.date}</p>
                            <p><strong>الوقت:</strong> ${p.time}</p>
                            <p><strong>المصدر:</strong> ${webBadge}</p>
                            ${p.notes ? `<p><strong>ملاحظات:</strong> ${p.notes}</p>` : ''}
                        </div>
                    `,
                    confirmButtonText: 'حسناً',
                });
            },
        });

        calendar.render();

        @if(empty($isEmployeeView))
        document.getElementById('employee_filter')?.addEventListener('change', function() {
            const url = new URL(window.location.href);
            if (this.value) {
                url.searchParams.set('user_id', this.value);
            } else {
                url.searchParams.delete('user_id');
            }
            window.history.replaceState({}, '', url);
            calendar.refetchEvents();
        });
        @endif
    });
</script>
@endsection
