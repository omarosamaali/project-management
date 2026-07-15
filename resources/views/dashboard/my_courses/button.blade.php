@extends('layouts.app')

@section('title', $buttonTitle . ' - ' . ($course->name_ar ?? 'دوراتي'))

@section('content')
<style>
    /* Reduce parent/page stealing touches from the iframe on mobile */
    .course-button-iframe-page {
        overscroll-behavior: none;
    }

    .course-button-iframe-wrap {
        position: relative;
        width: 100%;
        height: calc(100dvh - 11rem);
        min-height: 320px;
        /* overflow:auto + webkit scrolling helps iOS nested scroll */
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        touch-action: pan-y;
        border-radius: 0.75rem;
    }

    .course-button-iframe-wrap iframe {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 100%;
        border: 0;
        /* Let the iframe own vertical/horizontal pan gestures */
        touch-action: auto;
        -webkit-overflow-scrolling: touch;
        pointer-events: auto;
    }

    @media (max-width: 768px) {
        .course-button-iframe-wrap {
            height: calc(100dvh - 9.5rem);
            min-height: 280px;
            /* absolute iframe fill is more reliable on iOS Safari */
            overflow: hidden;
            touch-action: manipulation;
        }

        .course-button-iframe-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            /* critical for intermittent mobile touch scroll */
            touch-action: pan-x pan-y pinch-zoom;
        }
    }
</style>

<section class="p-2 sm:p-4 course-button-iframe-page">
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

    <div class="course-button-iframe-wrap shadow-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <iframe
            src="{{ $button['link'] }}"
            title="{{ $buttonTitle }}"
            scrolling="yes"
            loading="eager"
            referrerpolicy="no-referrer-when-downgrade"
            allow="fullscreen; clipboard-read; clipboard-write; autoplay"
            allowfullscreen
        ></iframe>
    </div>
</section>

<script>
    (function () {
        // Keep the outer dashboard from competing with iframe touch-scroll on mobile
        const isTouch = window.matchMedia('(pointer: coarse)').matches || 'ontouchstart' in window;
        if (!isTouch) return;

        const prevOverflow = document.documentElement.style.overflow;
        const prevBodyOverflow = document.body.style.overflow;
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';

        window.addEventListener('pagehide', function () {
            document.documentElement.style.overflow = prevOverflow;
            document.body.style.overflow = prevBodyOverflow;
        });
    })();
</script>
@endsection
