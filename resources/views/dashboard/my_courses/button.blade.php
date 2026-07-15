@extends('layouts.app')

@section('title', $buttonTitle . ' - ' . ($course->name_ar ?? 'دوراتي'))

@section('content')
<section class="p-2 sm:p-4">
    <x-breadcrumb
        first="دوراتي التدريبية"
        link="{{ route('dashboard.my_courses.show', $payment->id) }}"
        second="تفاصيل الدورة"
        third="{{ $buttonTitle }}"
    />

    <div class="mt-3 flex flex-wrap items-center gap-3 mb-3">
        <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
            class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition shrink-0">
            <i class="fas fa-arrow-right"></i>
            رجوع
        </a>
        <div class="min-w-0">
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate">
                {{ $buttonTitle }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $course->name_ar }}</p>
        </div>
    </div>

    {{-- Explicit height so the iframe never collapses (flex parents in layout use h-auto) --}}
    <div class="w-full rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800"
        style="height: calc(100vh - 11rem); min-height: 420px;">
        <iframe
            src="{{ $button['link'] }}"
            title="{{ $buttonTitle }}"
            class="block w-full h-full border-0"
            style="width: 100%; height: 100%; border: 0;"
            loading="eager"
            referrerpolicy="no-referrer-when-downgrade"
            allow="fullscreen; clipboard-read; clipboard-write; autoplay"
            allowfullscreen
        ></iframe>
    </div>
</section>
@endsection
