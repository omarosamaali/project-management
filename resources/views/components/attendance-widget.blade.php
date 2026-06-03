@props(['widget' => [], 'compact' => false])

@if(!empty($widget['show']))
<div class="attendance-widget-root flex flex-wrap items-center gap-2 {{ $compact ? 'w-full' : 'max-w-full' }}"
    data-status="{{ $widget['status'] ?? 'off' }}"
    data-seconds="{{ (int)($widget['worked_seconds'] ?? 0) }}">
    <span
        class="hidden sm:inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-200 dark:border-indigo-800"
        title="التسجيل يُحفظ في شاشة الحضور بوسم الموقع (ويب)">
        <i class="fas fa-globe"></i>
        تسجيل ويب
    </span>
    <button type="button" data-action="check_in"
        class="attendance-btn attendance-btn-web px-3 py-1.5 rounded-lg text-xs font-bold border border-indigo-200 transition inline-flex items-center gap-1">
        <i class="fas fa-globe text-[10px] opacity-80"></i>
        حضور
    </button>
    <button type="button" data-action="check_out"
        class="attendance-btn attendance-btn-web px-3 py-1.5 rounded-lg text-xs font-bold border border-indigo-200 transition inline-flex items-center gap-1">
        <i class="fas fa-globe text-[10px] opacity-80"></i>
        انصراف
    </button>
    <button type="button" data-action="break_start"
        class="attendance-btn attendance-btn-web px-3 py-1.5 rounded-lg text-xs font-bold border border-indigo-200 transition inline-flex items-center gap-1">
        <i class="fas fa-globe text-[10px] opacity-80"></i>
        خروج للاستراحة
    </button>
    <button type="button" data-action="break_end"
        class="attendance-btn attendance-btn-web px-3 py-1.5 rounded-lg text-xs font-bold border border-indigo-200 transition inline-flex items-center gap-1">
        <i class="fas fa-globe text-[10px] opacity-80"></i>
        رجوع من الاستراحة
    </button>
    <div class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-xs font-extrabold text-gray-700 dark:text-gray-200 whitespace-nowrap">
        <span class="attendance-status-text">...</span>
        <span class="mx-1">|</span>
        <span class="attendance-timer font-mono">00:00:00</span>
    </div>
</div>
@endif
