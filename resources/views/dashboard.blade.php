@extends('layouts.app')

@section('title', 'قُمرة القيادة')

@section('content')

<section class="!pl-0 p-3 sm:p-5 space-y-6">

    {{-- ====== كروت المهام ====== --}}
    <div>
        <h2 class="text-lg font-bold text-gray-700 dark:text-white mb-3 flex items-center gap-2">
            <i class="fas fa-tasks text-indigo-600"></i>
            إحصائيات {{ $taskStats['scope_label'] }}
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="rounded-lg bg-indigo-900 text-white p-4 flex flex-col justify-between min-h-[100px]">
                <span class="text-xs opacity-80">الإجمالي</span>
                <span class="text-2xl font-black">{{ $taskStats['total'] }}</span>
            </div>
            <div class="rounded-lg bg-green-700 text-white p-4 flex flex-col justify-between min-h-[100px]">
                <span class="text-xs opacity-80">منتهية</span>
                <span class="text-2xl font-black">{{ $taskStats['completed'] }}</span>
            </div>
            <div class="rounded-lg bg-slate-700 text-white p-4 flex flex-col justify-between min-h-[100px]">
                <span class="text-xs opacity-80">متبقية</span>
                <span class="text-2xl font-black">{{ $taskStats['remaining'] }}</span>
            </div>
            <div class="rounded-lg bg-blue-600 text-white p-4 flex flex-col justify-between min-h-[100px]">
                <span class="text-xs opacity-80">قيد الإنجاز</span>
                <span class="text-2xl font-black">{{ $taskStats['in_progress'] }}</span>
            </div>
            <div class="rounded-lg bg-red-600 text-white p-4 flex flex-col justify-between min-h-[100px]">
                <span class="text-xs opacity-80">متأخرة</span>
                <span class="text-2xl font-black">{{ $taskStats['late'] }}</span>
            </div>
            <div class="rounded-lg bg-amber-600 text-white p-4 flex flex-col justify-between min-h-[100px]">
                <span class="text-xs opacity-80">بالانتظار</span>
                <span class="text-2xl font-black">{{ $taskStats['waiting'] }}</span>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">متوسط سرعة الإنجاز (أيام)</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ $taskStats['avg_completion_days'] !== null ? $taskStats['avg_completion_days'] . ' يوم' : '—' }}
                </p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">نسبة الإنجاز في الموعد</p>
                <p class="text-xl font-bold text-green-600 mt-1">
                    {{ $taskStats['on_time_rate'] !== null ? $taskStats['on_time_rate'] . '%' : '—' }}
                </p>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">ساعات العمل على المهام (تتبع الوقت)</p>
                <p class="text-xl font-bold text-indigo-600 mt-1">{{ $taskStats['total_tracked_hours'] }} ساعة</p>
            </div>
        </div>
    </div>

    @if($attendanceStats)
    {{-- ====== كروت الحضور والرواتب ====== --}}
    <div>
        <h2 class="text-lg font-bold text-gray-700 dark:text-white mb-1 flex items-center gap-2">
            <i class="fas fa-clock text-orange-600"></i>
            الحضور والدوام
            <span class="text-sm font-normal text-gray-500">({{ $attendanceStats['period_label'] }})</span>
        </h2>
        <p class="text-xs text-gray-500 mb-3">اليوم: {{ $attendanceStats['status_today'] }} — {{ $attendanceStats['worked_hours_today'] }} ساعة</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('dashboard.work-times.calendar') }}"
                class="rounded-lg bg-orange-700 text-white p-4 hover:bg-orange-800 transition min-h-[100px] flex flex-col justify-between">
                <span class="text-xs opacity-90"><i class="fas fa-business-time ml-1"></i>ساعات العمل</span>
                <span class="text-2xl font-black">{{ $attendanceStats['worked_hours'] }}<span class="text-sm font-normal"> س</span></span>
            </a>
            <div class="rounded-lg bg-yellow-600 text-white p-4 min-h-[100px] flex flex-col justify-between">
                <span class="text-xs opacity-90">دقائق التأخير</span>
                <span class="text-2xl font-black">{{ $attendanceStats['late_minutes'] }}</span>
            </div>
            <div class="rounded-lg bg-teal-700 text-white p-4 min-h-[100px] flex flex-col justify-between">
                <span class="text-xs opacity-90">قيمة الإضافي</span>
                <span class="text-xl font-black">{{ number_format($attendanceStats['overtime_amount'], 0) }}</span>
            </div>
            <div class="rounded-lg bg-red-700 text-white p-4 min-h-[100px] flex flex-col justify-between">
                <span class="text-xs opacity-90">خصم حضور</span>
                <span class="text-xl font-black">{{ number_format($attendanceStats['attendance_deduction'], 0) }}</span>
            </div>
            <a href="{{ route('dashboard.adjustments.index') }}"
                class="rounded-lg bg-green-600 text-white p-4 hover:bg-green-700 transition min-h-[100px] flex flex-col justify-between">
                <span class="text-xs opacity-90"><i class="fas fa-gift ml-1"></i>مكافآت</span>
                <span class="text-xl font-black">{{ number_format($attendanceStats['bonus_total'], 0) }}</span>
            </a>
            <a href="{{ route('dashboard.adjustments.index') }}"
                class="rounded-lg bg-rose-700 text-white p-4 hover:bg-rose-800 transition min-h-[100px] flex flex-col justify-between">
                <span class="text-xs opacity-90"><i class="fas fa-minus-circle ml-1"></i>خصومات</span>
                <span class="text-xl font-black">{{ number_format($attendanceStats['adjustment_deduction'], 0) }}</span>
            </a>
        </div>
        <p class="text-xs text-gray-500 mt-2">أيام حضور مسجّلة هذا الشهر: {{ $attendanceStats['attendance_days'] }}</p>
    </div>
    @endif

    {{-- ====== جميع المهام والمراحل ====== --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-tasks text-indigo-600"></i>
                    جميع المهام
                    <span class="text-sm font-normal text-gray-500">({{ $allTasks->count() }})</span>
                </h2>
            </div>
            <div class="max-h-[420px] overflow-y-auto">
                @forelse($allTasks as $task)
                @php
                    $projectName = $task->special_request_id
                        ? ($task->specialRequest?->title ?? 'مشروع #' . $task->special_request_id)
                        : ($task->request?->system?->name_ar ?? 'طلب #' . $task->request_id);
                    $projectUrl = $task->special_request_id
                        ? route('dashboard.special-request.show', $task->special_request_id)
                        : route('dashboard.requests.show', $task->request_id);
                    $stageName = $task->stage?->title ?? $task->requestStage?->title ?? '—';
                    $statusClasses = [
                        'منتهية' => 'bg-green-100 text-green-700',
                        'قيد الإنجاز' => 'bg-blue-100 text-blue-700',
                        'متأخرة' => 'bg-red-100 text-red-700',
                        'بالانتظار' => 'bg-gray-100 text-gray-600',
                    ];
                @endphp
                <a href="{{ $projectUrl }}"
                    class="block px-4 py-3 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 dark:text-white truncate">{{ $task->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">
                                <i class="fas fa-folder-open ml-1"></i>{{ $projectName }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $task->user?->display_name ?? '—' }} · {{ $stageName }}
                            </p>
                        </div>
                        <span class="text-[10px] px-2 py-1 rounded-full font-bold whitespace-nowrap {{ $statusClasses[$task->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $task->status }}
                        </span>
                    </div>
                </a>
                @empty
                <p class="p-8 text-center text-gray-400 text-sm">لا توجد مهام حالياً</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-layer-group text-purple-600"></i>
                    جميع المراحل
                    <span class="text-sm font-normal text-gray-500">({{ $allStages->count() }})</span>
                </h2>
            </div>
            <div class="max-h-[420px] overflow-y-auto">
                @forelse($allStages as $stage)
                @php
                    $stageStatusLabels = [
                        'completed' => 'منتهية',
                        'pending' => 'بالانتظار',
                        'in_progress' => 'قيد التنفيذ',
                    ];
                    $stageLabel = $stageStatusLabels[$stage['status']] ?? $stage['status'];
                    $stageStatusClasses = [
                        'completed' => 'bg-green-100 text-green-700',
                        'منتهية' => 'bg-green-100 text-green-700',
                        'pending' => 'bg-gray-100 text-gray-600',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                    ];
                @endphp
                <a href="{{ $stage['url'] }}"
                    class="block px-4 py-3 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 dark:text-white truncate">{{ $stage['title'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">
                                <i class="fas fa-folder-open ml-1"></i>{{ $stage['project_name'] }}
                            </p>
                            @if($stage['end_date'])
                            <p class="text-xs text-gray-400 mt-0.5">موعد: {{ $stage['end_date'] }}</p>
                            @endif
                        </div>
                        <span class="text-[10px] px-2 py-1 rounded-full font-bold whitespace-nowrap {{ $stageStatusClasses[$stage['status']] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $stageLabel }}
                        </span>
                    </div>
                </a>
                @empty
                <p class="p-8 text-center text-gray-400 text-sm">لا توجد مراحل حالياً</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ====== الإشعارات ====== --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-700 dark:text-white flex items-center gap-2">
                <i class="fas fa-bell text-yellow-500"></i> الإشعارات
                @if($notifications->count() > 0)
                <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $notifications->count() }}</span>
                @endif
            </h2>
            @if($notifications->count() > 0)
            <button onclick="markAllRead()"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                <i class="fas fa-check-double"></i> تعليم الكل مقروءة
            </button>
            @endif
        </div>

        @if($notifications->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
            <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-400 dark:text-gray-500">لا توجد إشعارات جديدة</p>
        </div>
        @else
        <div class="space-y-2" id="notifications-container">
            @foreach($notifications as $notification)
            <div id="notif-{{ $notification->id }}"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-start justify-between hover:shadow-md transition-shadow cursor-pointer group"
                onclick="handleNotifClick({{ $notification->id }}, '{{ $notification->url ?? '' }}')">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        {{ $notification->type === 'success' ? 'bg-green-100' : ($notification->type === 'warning' ? 'bg-yellow-100' : 'bg-blue-100') }}">
                        <i class="fas {{ $notification->icon }}
                            {{ $notification->type === 'success' ? 'text-green-600' : ($notification->type === 'warning' ? 'text-yellow-600' : 'text-blue-600') }}"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 dark:text-white text-sm">{{ $notification->title }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">{{ $notification->message }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <button onclick="event.stopPropagation(); markRead({{ $notification->id }})"
                    class="text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity text-xs px-2 py-1 rounded hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</section>

<script>
    function markRead(id) {
        fetch(`/dashboard/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        }).then(() => {
            const el = document.getElementById(`notif-${id}`);
            if (el) {
                el.style.transition = 'opacity 0.3s ease, max-height 0.3s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            }
        }).catch(console.error);
    }

    function handleNotifClick(id, url) {
        markRead(id);
        if (url && url.trim() !== '') {
            setTimeout(() => { window.location.href = url; }, 200);
        }
    }

    function markAllRead() {
        fetch('/dashboard/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        }).then(() => {
            const container = document.getElementById('notifications-container');
            if (container) {
                container.innerHTML = '<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center"><i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i><p class="text-gray-400">لا توجد إشعارات جديدة</p></div>';
            }
        }).catch(console.error);
    }
</script>

@endsection
