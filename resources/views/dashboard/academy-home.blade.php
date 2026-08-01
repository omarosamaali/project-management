@extends('layouts.app')

@section('title', 'لوحة الأكاديمية')

@section('content')
@php
    $isTrainer = $user->isTrainer();
@endphp

<style>
    .ac-home-layout {
        display: grid; gap: 1.25rem;
    }
    @media (min-width: 1200px) {
        .ac-home-layout {
            grid-template-columns: minmax(0, 1fr) 17.5rem;
            align-items: start;
        }
    }
    .ac-home-hero {
        background:
            radial-gradient(520px 200px at 100% 0%, rgba(212,160,23,.3), transparent 60%),
            linear-gradient(135deg, #061525 0%, #0e3a5c 48%, #0b8f7f 145%);
        color: #fff;
        border-radius: 1.6rem;
        padding: 1.5rem 1.35rem;
        margin-bottom: 1.15rem;
        box-shadow: 0 20px 44px rgba(6,21,37,.18);
        position: relative; overflow: hidden;
        clip-path: polygon(0 0, 100% 0, 100% 88%, 96% 100%, 0 100%);
    }
    .ac-home-hero::after {
        content: ''; position: absolute; inset: auto -40px -50px auto;
        width: 160px; height: 160px; border-radius: 999px; background: rgba(255,255,255,.06);
    }
    .ac-home-hero h1 {
        font-size: clamp(1.35rem, 2.8vw, 1.95rem); font-weight: 800; margin: 0 0 .4rem;
        font-family: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif;
        position: relative; z-index: 1;
    }
    .ac-home-hero p { margin: 0; opacity: .88; font-size: .92rem; position: relative; z-index: 1; max-width: 36rem; }
    .ac-home-hero-actions {
        display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1.1rem; position: relative; z-index: 1;
    }

    .ac-stat-strip {
        display: grid; gap: .7rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 1.4rem;
    }
    @media (min-width: 768px) {
        .ac-stat-strip { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .ac-stat {
        background: #fff; border: 0; border-radius: 1.15rem;
        padding: 1rem 1.05rem; text-decoration: none; color: inherit;
        box-shadow: 0 8px 24px rgba(6,21,37,.06);
        border-inline-start: 4px solid #0b8f7f;
        transition: transform .2s, box-shadow .2s;
    }
    .ac-stat:nth-child(2) { border-inline-start-color: #d4a017; }
    .ac-stat:nth-child(3) { border-inline-start-color: #0e3a5c; }
    .ac-stat:nth-child(4) { border-inline-start-color: #e85d4c; }
    .ac-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(6,21,37,.1);
    }
    .ac-stat .label { font-size: .75rem; color: #5a6d82; font-weight: 700; }
    .ac-stat .value {
        font-size: 1.55rem; font-weight: 800; color: #061525; margin-top: .25rem;
        font-family: 'Noto Kufi Arabic', sans-serif;
    }

    .ac-quick {
        background: #fff; border-radius: 1.35rem; padding: 1.1rem;
        box-shadow: 0 8px 24px rgba(6,21,37,.06);
        display: flex; flex-direction: column; gap: .55rem;
    }
    .ac-quick h2 {
        margin: 0 0 .35rem; font-size: .95rem; font-weight: 800;
        font-family: 'Noto Kufi Arabic', sans-serif;
    }
    .ac-quick a {
        display: flex; align-items: center; gap: .65rem;
        padding: .75rem .85rem; border-radius: .95rem;
        text-decoration: none; color: #061525; font-weight: 700; font-size: .85rem;
        background: #f0f4f8; transition: background .18s, transform .18s;
    }
    .ac-quick a:hover { background: #e4f6f3; transform: translateX(-2px); }
    .ac-quick a i {
        width: 2rem; height: 2rem; border-radius: .7rem;
        display: inline-flex; align-items: center; justify-content: center;
        background: #061525; color: #fff; font-size: .8rem;
    }

    .ac-section-head {
        display: flex; align-items: center; justify-content: space-between;
        gap: .75rem; margin-bottom: .9rem;
    }
    .ac-section-head h2 {
        font-size: 1.05rem; font-weight: 800; margin: 0; color: #061525;
        font-family: 'Noto Kufi Arabic', sans-serif;
    }
    .ac-section-head a { font-size: .8rem; font-weight: 800; color: #0b8f7f; text-decoration: none; }
    .ac-section-head a:hover { color: #d4a017; }

    .ac-learn-list { display: flex; flex-direction: column; gap: .75rem; margin-bottom: 1.75rem; }
    .ac-learn-row {
        display: grid; gap: .85rem; align-items: center;
        background: #fff; border-radius: 1.2rem; padding: .75rem;
        box-shadow: 0 8px 24px rgba(6,21,37,.06);
        transition: transform .2s, box-shadow .2s;
    }
    @media (min-width: 640px) {
        .ac-learn-row { grid-template-columns: 7.5rem minmax(0, 1fr) auto; }
    }
    .ac-learn-row:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(6,21,37,.1); }
    .ac-learn-row img {
        width: 100%; aspect-ratio: 16/10; object-fit: cover; border-radius: .9rem;
        background: #e8eef5; display: block;
    }
    .ac-learn-title {
        font-weight: 800; font-size: .95rem; color: #061525; margin: 0 0 .25rem;
        font-family: 'Noto Kufi Arabic', sans-serif;
    }
    .ac-learn-meta { font-size: .78rem; color: #5a6d82; margin: 0; }
    .ac-progress {
        margin-top: .55rem; height: .45rem; border-radius: 99px; background: #e8eef5; overflow: hidden;
    }
    .ac-progress > span {
        display: block; height: 100%; border-radius: inherit;
        background: linear-gradient(90deg, #0b8f7f, #d4a017);
    }
    .ac-learn-actions { display: flex; flex-wrap: wrap; gap: .4rem; }

    .ac-card-grid {
        display: grid; gap: .95rem;
        grid-template-columns: 1fr;
        margin-bottom: 1.75rem;
    }
    @media (min-width: 640px) { .ac-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1400px) { .ac-card-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .ac-course-card {
        background: #fff; border: 0; border-radius: 1.25rem; overflow: hidden;
        display: flex; flex-direction: column; min-height: 100%;
        box-shadow: 0 8px 24px rgba(6,21,37,.06);
        transition: transform .25s, box-shadow .25s;
    }
    .ac-course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(6,21,37,.1);
    }
    .ac-course-card img {
        width: 100%; aspect-ratio: 16/9; object-fit: cover; background: #e8eef5; display: block;
        clip-path: polygon(0 0, 100% 0, 100% 88%, 0 100%);
        transition: transform .45s ease;
    }
    .ac-course-card:hover img { transform: scale(1.04); }
    .ac-course-body { padding: .95rem 1.05rem 1.05rem; display: flex; flex-direction: column; gap: .45rem; flex: 1; }
    .ac-course-title {
        font-weight: 800; font-size: .95rem; color: #061525; line-height: 1.35; margin: 0;
        font-family: 'Noto Kufi Arabic', sans-serif;
    }
    .ac-course-meta { font-size: .75rem; color: #5a6d82; }
    .ac-course-actions { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: auto; padding-top: .55rem; }
    .ac-empty {
        grid-column: 1 / -1; text-align: center; padding: 2rem 1rem;
        color: #5a6d82; background: #fff; border: 1px dashed #d4e0ec; border-radius: 1.25rem;
    }
    @media (max-width: 1199px) {
        .ac-quick { margin-bottom: .5rem; }
    }
</style>

<div class="ac-home-layout">
    <div>
        <div class="ac-home-hero">
            <h1>مرحباً، {{ $user->name }}</h1>
            <p>
                @if($isTrainer)
                لوحة المحاضر — تابع دوراتك وأرباحك وتقدّم المتدربين من مكان واحد.
                @else
                لوحة المتدرب — أكمل مسارك التعليمي واحصل على شهاداتك بسهولة.
                @endif
            </p>
            <div class="ac-home-hero-actions">
                <a href="{{ route('dashboard.my_courses.index') }}" class="ac-btn ac-btn-primary" style="background:#fff;color:#061525;">
                    <i class="fas fa-play"></i> متابعة التعلم
                </a>
                <a href="{{ route('academy.index') }}" class="ac-btn" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.25);">
                    تصفح الأكاديمية
                </a>
            </div>
        </div>

        <div class="ac-stat-strip">
            <a href="{{ route('dashboard.my_courses.index') }}" class="ac-stat">
                <div class="label">إجمالي دوراتي</div>
                <div class="value">{{ $courseStats['all'] ?? 0 }}</div>
            </a>
            <a href="{{ route('dashboard.my_courses.index') }}?filter=active" class="ac-stat">
                <div class="label">نشطة</div>
                <div class="value">{{ $courseStats['active'] ?? 0 }}</div>
            </a>
            <a href="{{ route('dashboard.my_courses.index') }}?filter=upcoming" class="ac-stat">
                <div class="label">قادمة</div>
                <div class="value">{{ $courseStats['upcoming'] ?? 0 }}</div>
            </a>
            <a href="{{ route('dashboard.my_courses.index') }}?filter=ended" class="ac-stat">
                <div class="label">منتهية</div>
                <div class="value">{{ $courseStats['ended'] ?? 0 }}</div>
            </a>
        </div>

        @if($needsRating->isNotEmpty())
        <div class="ac-section-head">
            <h2>أكمل التقييم للحصول على الشهادة</h2>
        </div>
        <div class="ac-card-grid">
            @foreach($needsRating as $payment)
            @php $course = $payment->course; @endphp
            @if($course)
            <article class="ac-course-card">
                <img src="{{ $course->main_image ? asset('storage/'.$course->main_image) : asset('assets/images/logo.webp') }}" alt="">
                <div class="ac-course-body">
                    <h3 class="ac-course-title">{{ $course->name_ar }}</h3>
                    <p class="ac-course-meta">يجب إكمال التقييم قبل استخراج الشهادة</p>
                    <div class="ac-course-actions">
                        <a href="{{ route('courses.rating', $course) }}" class="ac-btn ac-btn-amber">
                            <i class="fas fa-star"></i> أكمل التقييم
                        </a>
                    </div>
                </div>
            </article>
            @endif
            @endforeach
        </div>
        @endif

        <div class="ac-section-head">
            <h2>{{ $isTrainer ? 'متابعة التعلم' : 'أكمل التعلم' }}</h2>
            <a href="{{ route('dashboard.my_courses.index') }}">عرض الكل</a>
        </div>
        <div class="ac-learn-list">
            @forelse($continueLearning as $payment)
            @php
                $course = $payment->course;
                $pct = $course && $course->isRecorded() ? ($course->pathCompletionForUser($payment->user_id)['percent'] ?? 0) : null;
            @endphp
            @if($course)
            <article class="ac-learn-row">
                <img src="{{ $course->main_image ? asset('storage/'.$course->main_image) : asset('assets/images/logo.webp') }}" alt="">
                <div>
                    <h3 class="ac-learn-title">{{ $course->name_ar }}</h3>
                    <p class="ac-learn-meta">
                        @if($course->isRecorded())
                            إنجاز {{ $pct }}%
                        @elseif($course->isOnline())
                            دورة أونلاين
                        @else
                            دورة حضورية
                        @endif
                    </p>
                    @if($course->isRecorded() && $pct !== null)
                    <div class="ac-progress" aria-hidden="true"><span style="width: {{ max(0, min(100, (int)$pct)) }}%"></span></div>
                    @endif
                </div>
                <div class="ac-learn-actions">
                    @if($course->isRecorded())
                    <a href="{{ route('dashboard.my_courses.path', $payment->id) }}" class="ac-btn ac-btn-primary">
                        <i class="fas fa-play"></i> المتابعة
                    </a>
                    @elseif($course->isOnline())
                    <a href="{{ route('dashboard.my_courses.lecture', $payment->id) }}" class="ac-btn ac-btn-primary">
                        <i class="fas fa-video"></i> دخول المحاضرة
                    </a>
                    @endif
                    <a href="{{ route('dashboard.my_courses.show', $payment->id) }}" class="ac-btn ac-btn-ghost">التفاصيل</a>
                </div>
            </article>
            @endif
            @empty
            <div class="ac-empty">
                <p class="font-bold mb-1">لا توجد دورات للمتابعة حالياً</p>
                <a href="{{ route('academy.index') }}" class="ac-btn ac-btn-primary mt-2 inline-flex">تصفح الأكاديمية</a>
            </div>
            @endforelse
        </div>

        @if($isTrainer)
        <div class="ac-section-head">
            <h2>دوراتي التي أديرها</h2>
            <a href="{{ route('dashboard.courses.index') }}">إدارة الدورات</a>
        </div>
        <div class="ac-card-grid">
            @forelse($managedCourses as $course)
            <article class="ac-course-card">
                <img src="{{ $course->main_image ? asset('storage/'.$course->main_image) : asset('assets/images/logo.webp') }}" alt="">
                <div class="ac-course-body">
                    <h3 class="ac-course-title">{{ $course->name_ar }}</h3>
                    <p class="ac-course-meta">{{ $course->students_count ?? 0 }} مشترك · <span class="inline-flex items-center gap-1">{{ number_format((float)$course->price, 0) }} <x-drhm-icon width="12" height="14" /></span></p>
                    <div class="ac-course-actions">
                        <a href="{{ route('dashboard.courses.show', $course) }}" class="ac-btn ac-btn-primary">عرض</a>
                        <a href="{{ route('dashboard.courses.edit', $course) }}" class="ac-btn ac-btn-ghost">تعديل</a>
                    </div>
                </div>
            </article>
            @empty
            <div class="ac-empty">
                <p class="font-bold mb-2">لم تنشئ أي دورة بعد</p>
                <a href="{{ route('dashboard.courses.create') }}" class="ac-btn ac-btn-primary inline-flex">
                    <i class="fas fa-plus"></i> إنشاء دورة
                </a>
            </div>
            @endforelse
        </div>
        @endif
    </div>

    <aside class="ac-quick" aria-label="اختصارات سريعة">
        <h2>اختصارات سريعة</h2>
        <a href="{{ route('dashboard.my_courses.index') }}"><i class="fas fa-book-open"></i> دوراتي</a>
        <a href="{{ route('academy.courses') }}"><i class="fas fa-compass"></i> استكشف الدورات</a>
        @if($isTrainer)
        <a href="{{ route('dashboard.courses.create') }}"><i class="fas fa-plus"></i> دورة جديدة</a>
        <a href="{{ route('dashboard.courses.index') }}"><i class="fas fa-chalkboard-teacher"></i> إدارة الدورات</a>
        <a href="{{ route('dashboard.academy.my-profits') }}"><i class="fas fa-wallet"></i> أرباحي</a>
        @endif
        <a href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> حسابي</a>
    </aside>
</div>
@endsection
