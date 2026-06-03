@props(['record'])

@php
    $typeClass = match($record->type) {
        'حضور', 'دخول من الاستراحة' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'انصراف' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'خروج للاستراحة' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
    $fromWeb = $record->isFromWeb();
@endphp

<span class="inline-flex items-center gap-1.5 flex-wrap">
    <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $typeClass }}">{{ $record->type }}</span>
    @if($fromWeb)
    <span
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 dark:bg-indigo-900/50 dark:text-indigo-200 dark:border-indigo-700"
        title="تسجيل عبر الموقع — أزرار الهيدر">
        <i class="fas fa-globe"></i>
        ويب
    </span>
    @else
    <span
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400"
        title="تسجيل يدوي من الإدارة">
        <i class="fas fa-hand-paper text-[9px]"></i>
        يدوي
    </span>
    @endif
</span>
