@extends('layouts.user')

@section('title', 'دورة - ' . ($course->name_ar ?? $course->name_en))

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $locale = app()->getLocale();
    $courseName = $locale === 'en' ? ($course->name_en ?: $course->name_ar) : $course->name_ar;
    $description = $locale === 'en' ? ($course->description_en ?: $course->description_ar) : $course->description_ar;
    $hasVideo = !empty($course->video);
    $heroMediaUrl = $hasVideo ? $course->videoUrl() : Storage::url($course->main_image);
    $isRecorded = $course->isRecorded();
    $featuredRatings = $course->featuredRatings;
    $typeLabel = match ($course->location_type) {
        'online' => __('messages.academy_type_online'),
        'recorded' => __('messages.academy_type_recorded'),
        'on_site' => __('messages.academy_type_onsite'),
        default => $course->location_type,
    };
    $typeTone = match ($course->location_type) {
        'online' => 'online',
        'recorded' => 'recorded',
        'on_site' => 'onsite',
        default => 'recorded',
    };
    $levelKeys = $course->levelKeys();
    $levelMap = collect(\App\Models\Course::levelOptions())->keyBy('key');
    $categoryName = $course->category?->title($locale);

    $current_enrolled = \App\Models\Payment::where('course_id', $course->id)
        ->where('status', '!=', 'failed')
        ->count();
    $actual_remaining = ($course->counter ?? 0) - $current_enrolled;
    $enrollmentPayment = auth()->check()
        ? \App\Models\Payment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->whereIn('status', ['completed', 'success', 'paid', 'active', 'pending'])
            ->latest('id')
            ->first()
        : null;
    $is_already_in = (bool) $enrollmentPayment;
    $canEnroll = auth()->check() && auth()->user()->canLearnCourses();

    $visibleButtons = collect($course->buttons ?? [])->filter(fn ($b) => empty($b['needs_login']));
@endphp

<style>
    .course-public { font-family: "Cairo", sans-serif; color: #111827; }
    .course-hero {
        position: relative;
        width: 100%;
        min-height: min(72vh, 620px);
        height: min(72vh, 620px);
        background: #0a0a0a;
        overflow: hidden;
    }
    .course-hero video,
    .course-hero img.hero-media {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .course-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,.25) 0%, rgba(0,0,0,.75) 100%);
        display: flex;
        align-items: flex-end;
        pointer-events: none;
        z-index: 1;
    }
    .course-hero video {
        z-index: 0;
        position: absolute;
    }
    .course-hero .ac-media-wm {
        position: absolute;
        top: 1rem;
        inset-inline-end: 1rem;
        inset-inline-start: auto;
        width: min(16%, 9rem);
        height: auto;
        max-height: 18%;
        opacity: {{ config('watermark.opacity', 0.38) }};
        pointer-events: none;
        z-index: 4;
        user-select: none;
    }
    .course-side-card .ac-media-wm-host,
    .course-side-card { position: relative; }
    .course-side-card .ac-media-wm {
        position: absolute;
        top: .65rem;
        inset-inline-end: .65rem;
        inset-inline-start: auto;
        width: min(32%, 5.5rem);
        height: auto;
        opacity: {{ config('watermark.opacity', 0.38) }};
        pointer-events: none;
        z-index: 2;
        user-select: none;
    }
    .proof-grid .ac-media-wm-host { position: relative; display: block; overflow: hidden; border-radius: .75rem; }
    .proof-grid .ac-media-wm {
        position: absolute;
        top: .5rem;
        inset-inline-end: .5rem;
        inset-inline-start: auto;
        width: min(36%, 4.5rem);
        height: auto;
        opacity: {{ config('watermark.opacity', 0.38) }};
        pointer-events: none;
        z-index: 2;
        user-select: none;
    }
    .course-hero-play {
        position: absolute;
        inset: 0;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 0;
        cursor: pointer;
        padding: 0;
    }
    .course-hero-play:focus-visible {
        outline: 2px solid #fff;
        outline-offset: -12px;
    }
    .course-hero-play-icon {
        width: clamp(4.5rem, 10vw, 6.5rem);
        height: clamp(4.5rem, 10vw, 6.5rem);
        color: #fff;
        filter: drop-shadow(0 4px 18px rgba(0,0,0,.45));
        transition: transform .2s ease, opacity .2s ease;
    }
    .course-hero-play:hover .course-hero-play-icon {
        transform: scale(1.06);
    }
    .course-hero.is-playing .course-hero-play {
        opacity: 0;
        pointer-events: none;
    }
    .course-hero-overlay .hero-copy {
        pointer-events: auto;
        width: 100%;
    }
    .course-hero-title {
        font-size: clamp(1.75rem, 4vw, 3rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.25;
        max-width: 52rem;
        text-shadow: 0 2px 16px rgba(0,0,0,.45);
    }
    .course-meta-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .55rem;
        margin-bottom: 1.35rem;
    }
    .course-type-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .95rem;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: .01em;
    }
    .course-type-badge.is-online {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 8px 18px rgba(37, 99, 235, .28);
    }
    .course-type-badge.is-recorded {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        box-shadow: 0 8px 18px rgba(109, 40, 217, .28);
    }
    .course-type-badge.is-onsite {
        background: linear-gradient(135deg, #ff6b3d, #e85d04);
        box-shadow: 0 8px 18px rgba(232, 93, 4, .28);
    }
    .course-level-chip {
        display: inline-flex;
        align-items: center;
        padding: .4rem .8rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        color: #fff;
        border: 0;
        background: #061525;
    }
    .course-level-chip.is-beginner {
        background: linear-gradient(135deg, #12c8a0, #0b8f7f);
        box-shadow: 0 6px 14px rgba(11, 143, 127, .22);
    }
    .course-level-chip.is-intermediate {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 6px 14px rgba(37, 99, 235, .22);
    }
    .course-level-chip.is-advanced {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        box-shadow: 0 6px 14px rgba(109, 40, 217, .22);
    }
    .course-level-chip.is-all {
        background: linear-gradient(135deg, #f0a202, #d97706);
        box-shadow: 0 6px 14px rgba(217, 119, 6, .22);
    }
    .course-category-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .4rem .85rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
        color: #0e3a5c;
        background: #eef5fb;
        border: 1px solid #d4e0ec;
    }
    .course-category-chip img {
        width: 1rem;
        height: 1rem;
        border-radius: 999px;
        object-fit: cover;
        flex-shrink: 0;
    }
    .course-hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-top: .85rem;
    }
    .course-hero-meta .course-type-badge {
        box-shadow: 0 8px 20px rgba(0,0,0,.25);
    }
    .course-layout {
        display: grid;
        gap: 2rem;
    }
    @media (min-width: 1024px) {
        .course-layout {
            grid-template-columns: minmax(0, 1fr) 340px;
            align-items: start;
        }
        .course-side {
            position: sticky;
            top: 5.5rem;
        }
    }
    .course-side-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,.06);
    }
    .course-side-card img.side-cover {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }
    .include-row {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: .55rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: .9rem;
        color: #374151;
    }
    .include-row:last-child { border-bottom: 0; }
    .include-row i { width: 1.25rem; text-align: center; color: #111; }
    .section-block { margin-bottom: 2.5rem; }
    .section-title {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: #111827;
    }
    .proof-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (min-width: 640px) {
        .proof-grid { grid-template-columns: 1fr 1fr; }
    }
    .proof-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .9rem;
        padding: 1.25rem 1.35rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .chip-grid, .list-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: .75rem;
    }
    @media (min-width: 640px) {
        .chip-grid, .list-grid { grid-template-columns: 1fr 1fr; }
    }
    .audience-chip {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: .85rem;
        padding: .9rem 1rem;
        display: flex;
        align-items: center;
        gap: .65rem;
        font-weight: 600;
    }
    .learn-item, .need-item {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .75rem;
        padding: .85rem 1rem;
        display: flex;
        gap: .65rem;
        align-items: flex-start;
    }
    .path-unit {
        border: 1px solid #e5e7eb;
        border-radius: .85rem;
        overflow: hidden;
        margin-bottom: .75rem;
        background: #fff;
    }
    .path-unit-h {
        background: #f9fafb;
        padding: .85rem 1rem;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        gap: .5rem;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        user-select: none;
        width: 100%;
        text-align: inherit;
    }
    .path-unit-h:hover { background: #f3f4f6; }
    .path-unit-h .path-chevron {
        color: #9ca3af;
        transition: transform .2s ease;
        margin-inline-start: .35rem;
    }
    .path-unit.open .path-unit-h .path-chevron { transform: rotate(180deg); }
    .path-unit-body { display: none; }
    .path-unit.open .path-unit-body { display: block; }
    .path-unit.open .path-unit-h { border-bottom-color: #e5e7eb; }
    .path-unit:not(.open) .path-unit-h { border-bottom: 0; }
    .path-lesson {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        padding: .7rem 1rem;
        border-bottom: 1px solid #f3f4f6;
        font-size: .9rem;
    }
    .path-lesson:last-child { border-bottom: 0; }
    .review-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: .85rem;
        background: linear-gradient(165deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e8eef4;
        border-inline-start: 3px solid #0b8f7f;
        border-radius: 1.1rem;
        padding: 1.35rem 1.4rem 1.4rem;
        box-shadow: 0 10px 28px rgba(6, 21, 37, .05);
        min-height: 100%;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .review-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 34px rgba(6, 21, 37, .08);
        border-color: #d7e3ee;
    }
    .review-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .85rem;
    }
    .review-card-person {
        display: flex;
        align-items: center;
        gap: .7rem;
        min-width: 0;
    }
    .review-card-avatar {
        width: 2.55rem;
        height: 2.55rem;
        border-radius: 999px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, #12c8a0, #0b8f7f);
        color: #fff;
        font-size: .85rem;
        font-weight: 800;
        box-shadow: 0 6px 14px rgba(11, 143, 127, .25);
    }
    .review-card-name {
        margin: 0;
        font-size: .95rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .review-card-role {
        margin: .15rem 0 0;
        font-size: .72rem;
        font-weight: 600;
        color: #64748b;
    }
    .review-card-score {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        flex-shrink: 0;
        padding: .35rem .65rem;
        border-radius: 999px;
        background: #fff7ed;
        border: 1px solid #ffedd5;
        color: #b45309;
        font-size: .78rem;
        font-weight: 800;
    }
    .review-card-score .stars {
        font-size: .72rem;
        letter-spacing: .5px;
    }
    .review-card-body {
        margin: 0;
        padding-top: .15rem;
        color: #475569;
        font-size: .9rem;
        line-height: 1.75;
        position: relative;
    }
    .review-card-quote {
        position: absolute;
        inset-block-start: -.15rem;
        inset-inline-end: 0;
        color: rgba(11, 143, 127, .12);
        font-size: 1.6rem;
        line-height: 1;
        pointer-events: none;
    }
    .stars { color: #f59e0b; letter-spacing: 1px; }
    .cta-primary {
        display: block;
        width: 100%;
        text-align: center;
        background: #111;
        color: #fff;
        font-weight: 800;
        padding: .95rem 1rem;
        border-radius: .75rem;
        transition: background .2s;
    }
    .cta-primary:hover { background: #000; }
    .cta-disabled {
        display: block;
        width: 100%;
        text-align: center;
        font-weight: 700;
        padding: .95rem 1rem;
        border-radius: .75rem;
        border: 1px solid;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn .2s ease-out; }
    .animate-slideUp { animation: slideUp .28s ease-out; }
</style>

<div class="course-public">
    {{-- Hero --}}
    <section class="course-hero" id="courseHero">
        @if($hasVideo)
        <div data-ac-video-protect class="contents">
        <video id="courseHeroVideo" class="hero-media" src="{{ $heroMediaUrl }}" playsinline preload="metadata"
            controlsList="nodownload noplaybackrate" disablePictureInPicture oncontextmenu="return false;"
            poster="{{ $course->main_image ? Storage::url($course->main_image) : '' }}"></video>
        </div>
        <button type="button" id="courseHeroPlayBtn" class="course-hero-play" aria-label="تشغيل الفيديو">
            <svg class="course-hero-play-icon" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="48" cy="48" r="44" stroke="currentColor" stroke-width="3.5"/>
                <path d="M40 30.5v35l28-17.5L40 30.5z" stroke="currentColor" stroke-width="3.5" stroke-linejoin="round" fill="none"/>
            </svg>
        </button>
        @else
        <img class="hero-media" src="{{ $heroMediaUrl }}" alt="{{ $courseName }}">
        @endif
        <x-media-watermark brand="academy" size="lg" />
        <div class="course-hero-overlay">
            <div class="hero-copy max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pb-8 pt-24">
                <h1 class="course-hero-title">{{ $courseName }}</h1>
                @if($typeLabel)
                <div class="course-hero-meta">
                    <span class="course-type-badge is-{{ $typeTone }}">
                        <i class="fas fa-{{ $typeTone === 'online' ? 'video' : ($typeTone === 'onsite' ? 'map-marker-alt' : 'play-circle') }}"></i>
                        {{ $typeLabel }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 pb-8">
        <button type="button" onclick="history.back()"
            class="mb-5 inline-flex items-center gap-2 text-gray-700 hover:text-black text-sm font-medium transition">
            <i class="fa fa-{{ $locale == 'ar' ? 'arrow-right' : 'arrow-left' }}"></i>
            العودة
        </button>

        @if (session('success'))
        <div class="mb-6 bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg">
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        <div class="course-layout">
            <div class="course-main min-w-0">
                @if($typeLabel || count($levelKeys) || $categoryName)
                <div class="course-meta-row">
                    @if($typeLabel)
                    <span class="course-type-badge is-{{ $typeTone }}">
                        <i class="fas fa-{{ $typeTone === 'online' ? 'video' : ($typeTone === 'onsite' ? 'map-marker-alt' : 'play-circle') }}"></i>
                        {{ $typeLabel }}
                    </span>
                    @endif
                    @foreach($levelKeys as $levelKey)
                    @php
                        $levelOpt = $levelMap->get($levelKey);
                        $levelLabel = $levelOpt
                            ? ($locale === 'en' ? $levelOpt['label_en'] : $levelOpt['label_ar'])
                            : $levelKey;
                    @endphp
                    <span class="course-level-chip is-{{ $levelKey }}">{{ $levelLabel }}</span>
                    @endforeach
                    @if($categoryName)
                    <span class="course-category-chip" title="{{ $categoryName }}">
                        @if($course->category?->iconUrl())
                        <img src="{{ $course->category->iconUrl() }}" alt="">
                        @else
                        <i class="fas fa-folder-open" aria-hidden="true"></i>
                        @endif
                        {{ $categoryName }}
                    </span>
                    @endif
                </div>
                @endif

                {{-- Description --}}
                <div class="section-block">
                    <h2 class="section-title">وصف الدورة</h2>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        اطلع على الوصف الكامل للدورة لمعرفة تفاصيل المحتوى والأهداف.
                    </p>
                    <button type="button" onclick="openDescriptionModal()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-black text-white rounded-lg hover:bg-gray-900 transition font-semibold text-sm">
                        <i class="fas fa-book-open"></i>
                        قراءة الوصف بالكامل
                    </button>
                </div>

                {{-- Social proof --}}
                <div class="section-block">
                    <div class="proof-grid">
                        <div class="proof-card">
                            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">متوسط التقييم</div>
                                @if($featuredAverage !== null)
                                <div class="text-2xl font-extrabold text-gray-900">
                                    {{ number_format($featuredAverage, 1) }}
                                    <span class="text-sm font-semibold text-gray-500">/ 5</span>
                                </div>
                                <div class="stars text-sm mt-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= round($featuredAverage) ? '' : ' text-gray-300' }}"></i>
                                    @endfor
                                    @if($featuredCount)
                                    <span class="text-gray-400 text-xs mr-1">({{ $featuredCount }})</span>
                                    @endif
                                </div>
                                @else
                                <div class="text-lg font-bold text-gray-400">قريباً</div>
                                @endif
                            </div>
                        </div>
                        <div class="proof-card">
                            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-900 flex items-center justify-center text-xl">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">شهادة معتمدة</div>
                                <div class="text-lg font-extrabold text-gray-900 leading-snug">
                                    شهادة مضمونة عند اجتياز الدورة
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Target audience --}}
                @if (!empty($course->suitable_for))
                <div class="section-block">
                    <h2 class="section-title">هذا الكورس مناسب لك إذا كنت</h2>
                    <div class="chip-grid">
                        @foreach ($course->suitable_for as $item)
                        <div class="audience-chip">
                            <i class="fas fa-user-check text-black"></i>
                            <span>{{ $locale === 'en' ? ($item['en'] ?? $item['ar'] ?? '') : ($item['ar'] ?? $item['en'] ?? '') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- What you'll learn = features --}}
                @if (!empty($course->features))
                <div class="section-block">
                    <h2 class="section-title">ماذا ستتعلم في هذه الدورة؟</h2>
                    <div class="list-grid">
                        @foreach ($course->features as $feature)
                        <div class="learn-item">
                            <i class="fas fa-check text-black mt-1"></i>
                            <span class="text-gray-700">{{ $locale === 'en' ? ($feature['en'] ?? '') : ($feature['ar'] ?? '') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Prerequisites = requirements --}}
                @if (!empty($course->requirements))
                <div class="section-block">
                    <h2 class="section-title">ماذا تحتاج لبدء الدورة؟</h2>
                    <div class="space-y-2">
                        @foreach ($course->requirements as $req)
                        <div class="need-item">
                            <i class="fas fa-circle text-[6px] text-gray-400 mt-2"></i>
                            <span class="text-gray-700">{{ $locale === 'en' ? ($req['en'] ?? '') : ($req['ar'] ?? '') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Learning path / curriculum (recorded courses) --}}
                @php
                    $pathUnits = $isRecorded
                        ? $course->units->filter(fn ($unit) => $unit->items->isNotEmpty())->values()
                        : collect();
                @endphp
                @if ($pathUnits->isNotEmpty())
                <div class="section-block" id="course-curriculum">
                    <h2 class="section-title">مسار التعلم</h2>
                    @foreach ($pathUnits as $uIndex => $unit)
                    @php
                        $lessons = $unit->items->where('type', 'lesson');
                        $exams = $unit->items->where('type', 'exam');
                        $unitLessonCount = $lessons->count();
                        $unitOpen = $uIndex === 0;
                    @endphp
                    <div class="path-unit {{ $unitOpen ? 'open' : '' }}">
                        <button type="button" class="path-unit-h" aria-expanded="{{ $unitOpen ? 'true' : 'false' }}">
                            <span class="flex items-center gap-2 min-w-0">
                                <i class="fas fa-chevron-down path-chevron text-xs"></i>
                                <span class="truncate">{{ $unit->localizedTitle() }}</span>
                            </span>
                            <span class="text-xs font-semibold text-gray-500 shrink-0">
                                {{ $unitLessonCount }} {{ $unitLessonCount == 1 ? 'درس' : 'دروس' }}
                                @if($exams->count())
                                 · {{ $exams->count() }} اختبار
                                @endif
                            </span>
                        </button>
                        <div class="path-unit-body">
                            @foreach ($unit->items as $item)
                            <div class="path-lesson">
                                <span class="flex items-center gap-2 min-w-0">
                                    <i class="fas {{ $item->isExam() ? 'fa-clipboard-list text-violet-600' : 'fa-play-circle text-gray-700' }}"></i>
                                    <span class="truncate">{{ $item->localizedTitle() }}</span>
                                </span>
                                @if($item->isLesson() && $item->video_duration_seconds)
                                <span class="text-xs text-gray-400 shrink-0">{{ $item->formattedDuration() }}</span>
                                @elseif($item->isExam())
                                <span class="text-xs text-violet-500 shrink-0">{{ $item->formattedExamDuration() }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Student reviews --}}
                @if ($featuredRatings->isNotEmpty())
                <div class="section-block">
                    <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
                        <h2 class="section-title mb-0">ماذا قال المشتركين السابقين؟</h2>
                        @if($featuredAverage)
                        <div class="text-sm text-gray-600">
                            <span class="font-extrabold text-gray-900 text-lg">{{ number_format($featuredAverage, 1) }}/5</span>
                            <span class="stars mr-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= round($featuredAverage) ? '' : ' text-gray-300' }}"></i>
                                @endfor
                            </span>
                            ({{ $featuredCount }})
                        </div>
                        @endif
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach ($featuredRatings as $rating)
                        @php
                            $reviewerName = $rating->user->name ?? 'متدرب';
                            $reviewerInitial = mb_substr(trim($reviewerName), 0, 1) ?: 'م';
                            $reviewScore = $rating->overallScore();
                        @endphp
                        <article class="review-card">
                            <div class="review-card-head">
                                <div class="review-card-person">
                                    <span class="review-card-avatar" aria-hidden="true">{{ $reviewerInitial }}</span>
                                    <div class="min-w-0">
                                        <h3 class="review-card-name">{{ $reviewerName }}</h3>
                                        <p class="review-card-role">مشترك في الدورة</p>
                                    </div>
                                </div>
                                @if($reviewScore !== null)
                                <div class="review-card-score" title="{{ number_format($reviewScore, 1) }}/5">
                                    <span class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= round($reviewScore) ? '' : ' text-orange-200' }}"></i>
                                        @endfor
                                    </span>
                                    <span>{{ number_format($reviewScore, 1) }}</span>
                                </div>
                                @endif
                            </div>
                            <p class="review-card-body">
                                <i class="fas fa-quote-left review-card-quote" aria-hidden="true"></i>
                                {{ $rating->feedbackText() ?: 'تقييم إيجابي للدورة' }}
                            </p>
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Gallery (keep useful existing media) --}}
                @if (!empty($course->images))
                <div class="section-block">
                    <h2 class="section-title">صور من الدورة</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ($course->images as $image)
                        <div class="ac-media-wm-host proof-grid" style="position:relative;overflow:hidden;border-radius:.5rem;">
                            <img onclick="openModal('{{ Storage::url($image) }}')"
                                src="{{ Storage::url($image) }}" alt="صورة إضافية"
                                class="w-full h-36 object-cover rounded-lg border cursor-pointer hover:opacity-90 transition">
                            <x-media-watermark brand="academy" size="sm" />
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Sticky side card --}}
            <aside class="course-side">
                <div class="course-side-card">
                    @if($course->main_image)
                    <div class="ac-media-wm-host" style="position:relative;">
                        <img class="side-cover" src="{{ Storage::url($course->main_image) }}" alt="{{ $courseName }}">
                        <x-media-watermark brand="academy" size="sm" />
                    </div>
                    @endif
                    <div class="p-5">
                        <div class="mb-4 pb-4 border-b border-gray-100">
                            @if($course->price > 0)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm text-gray-500">السعر</span>
                                <span class="text-2xl font-extrabold text-gray-900 flex items-center gap-1.5">
                                    {{ number_format($course->price) }}
                                    <img src="{{ asset('assets/images/drhm-icon.svg') }}" class="w-6" alt="">
                                </span>
                            </div>
                            @else
                            <div class="text-xl font-extrabold text-green-700">
                                <i class="fas fa-check ml-1"></i> دورة مجانية
                            </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <div class="text-xs font-bold text-gray-500 mb-1">تشمل هذه الدورة</div>
                            <div class="include-row">
                                <i class="fas fa-certificate"></i>
                                <span>شهادة إتمام الدورة</span>
                            </div>
                            <div class="include-row">
                                <i class="fas fa-infinity"></i>
                                <span>وصول مدى الحياة للدورة</span>
                            </div>
                            @if($isRecorded)
                            <div class="include-row">
                                <i class="fas fa-list-ol"></i>
                                <span>{{ $lessonCount }} {{ $lessonCount == 1 ? 'درس' : 'دروس' }}</span>
                            </div>
                            @if(($examCount ?? 0) > 0)
                            <div class="include-row">
                                <i class="fas fa-clipboard-list"></i>
                                <span>{{ $examCount }} {{ $examCount == 1 ? 'اختبار' : 'اختبارات' }}</span>
                            </div>
                            @endif
                            @if(($contentDurationSeconds ?? 0) > 0)
                            <div class="include-row">
                                <i class="fas fa-clock"></i>
                                <span>مدة المحتوى: {{ $course->formattedTotalContentDurationArabic() }}</span>
                            </div>
                            @endif
                            @endif
                        </div>

                        <div class="space-y-2">
                            @php
                                $wishlisted = (bool) ($course->academy_wishlisted ?? false);
                            @endphp
                            <button type="button"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-bold transition
                                    {{ $wishlisted ? 'border-rose-200 bg-rose-50 text-rose-600' : 'border-gray-200 bg-white text-gray-700 hover:border-rose-300 hover:text-rose-600' }}"
                                data-wishlist-toggle
                                data-url="{{ route('academy.wishlist.toggle', $course) }}"
                                data-login-url="{{ \App\Support\AuthUi::loginUrl(['redirect' => url()->current()]) }}"
                                data-wishlisted="{{ $wishlisted ? '1' : '0' }}"
                                aria-pressed="{{ $wishlisted ? 'true' : 'false' }}">
                                <i class="{{ $wishlisted ? 'fas' : 'far' }} fa-heart"></i>
                                <span data-wishlist-label>
                                    {{ $wishlisted ? __('messages.academy_wishlist_remove') : __('messages.academy_wishlist_add') }}
                                </span>
                            </button>
                            @auth
                                @if (!$canEnroll)
                                <div class="cta-disabled bg-amber-50 border-amber-300 text-amber-800 text-sm">
                                    الاشتراك متاح لحسابات المتدرب والمحاضر والإدارة فقط
                                </div>
                                @elseif ($is_already_in && $enrollmentPayment)
                                <a href="{{ route('dashboard.my_courses.show', $enrollmentPayment) }}"
                                    class="cta-primary inline-flex items-center justify-center gap-2">
                                    <i class="fas fa-play-circle"></i>
                                    الذهاب إلى دورتي
                                </a>
                                @elseif($actual_remaining <= 0)
                                <div class="cta-disabled bg-red-50 border-red-300 text-red-700 text-sm">
                                    عذراً، اكتمل العدد ولا توجد مقاعد شاغرة
                                </div>
                                @else
                                <button type="button"
                                    onclick="handlePayment({{ $course->id }}, {{ $course->price }}, 'course', 'تأكيد الاشتراك')"
                                    class="cta-primary">
                                    التحق بالدورة
                                </button>
                                <p class="text-[11px] text-center text-gray-400">المقاعد المتاحة: {{ $actual_remaining }}</p>
                                @endif
                            @else
                                @php
                                    $enrollReturnUrl = route('courses.show', [$course, 'enroll' => 1]);
                                @endphp
                                <a href="{{ \App\Support\AuthUi::loginUrl(['ui' => 'academy', 'redirect' => $enrollReturnUrl]) }}"
                                    class="cta-primary">سجل دخول للاشتراك</a>
                            @endauth
                        </div>

                        @if ($visibleButtons->isNotEmpty())
                        <div class="mt-4 space-y-2">
                            @foreach ($visibleButtons as $button)
                            <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                                class="block text-center px-4 py-2.5 rounded-lg text-white text-sm font-semibold hover:opacity-90"
                                style="background-color: {{ $button['color'] ?? '#111111' }}">
                                {{ $locale == 'ar' ? ($button['text_ar'] ?? '') : ($button['text_en'] ?? '') }}
                            </a>
                            @endforeach
                        </div>
                        @endif

                        @if ($course->category)
                        <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg">
                                <img src="{{ $course->category->iconUrl() }}" class="w-4 h-4 rounded-full object-cover" alt="">
                                {{ $course->category->title($locale) }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- Related courses --}}
    @if($related_courses && $related_courses->count() > 0)
    @include('academy.partials.styles')
    <section class="academy-page">
        <div class="academy-section is-tight">
            <h2 class="academy-h2 display text-center" style="margin-bottom:1.5rem;">دورات مقترحة</h2>
            <div class="soni-grid">
                @foreach($related_courses as $item)
                    @include('academy.partials.course-card', ['course' => $item, 'locale' => $locale])
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>

{{-- Description modal --}}
<div id="description-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 animate-fadeIn"
    onclick="if(event.target === this) closeDescriptionModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] overflow-hidden animate-slideUp">
        <div class="flex justify-between items-center p-6 border-b bg-black text-white">
            <h3 class="text-xl font-bold">{{ $courseName }}</h3>
            <button type="button" onclick="closeDescriptionModal()" class="text-white hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <div class="p-8 overflow-y-auto max-h-[calc(85vh-160px)]">
            <div class="text-lg text-gray-700 leading-relaxed whitespace-pre-line">{{ $description }}</div>
        </div>
        <div class="p-6 border-t bg-gray-50 flex justify-end">
            <button type="button" onclick="closeDescriptionModal()"
                class="px-8 py-3 bg-black text-white rounded-lg hover:bg-gray-900 font-semibold">
                فهمت
            </button>
        </div>
    </div>
</div>

{{-- Payment modal --}}
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4" id="modalTitle">تأكيد الدفع</h3>
        <div class="space-y-3 mb-6">
            <div class="flex justify-between">
                <span id="priceLabel">السعر:</span>
                <span id="originalPrice" class="font-bold"></span>
            </div>
            <div class="flex justify-between items-center gap-3 text-sm text-gray-600">
                <span class="inline-flex items-center gap-1 whitespace-nowrap">رسوم الدفع (7.9% + 2 <x-drhm-icon width="12" height="12" />):</span>
                <span id="fees" class="whitespace-nowrap"></span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-3">
                <span>الإجمالي:</span>
                <span id="totalPrice"></span>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')"
                class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">إلغاء</button>
            <button onclick="proceedPayment()" id="payButton"
                class="flex-1 bg-black text-white py-2 rounded-lg hover:bg-gray-900">متابعة الدفع</button>
        </div>
    </div>
</div>

<script>
(function () {
    const hero = document.getElementById('courseHero');
    const video = document.getElementById('courseHeroVideo');
    const playBtn = document.getElementById('courseHeroPlayBtn');
    if (!hero || !video || !playBtn) return;

    function syncPlayingState() {
        hero.classList.toggle('is-playing', !video.paused && !video.ended);
    }

    async function playHero() {
        try {
            await video.play();
        } catch (_) { /* autoplay/play blocked */ }
        syncPlayingState();
    }

    function pauseHero() {
        video.pause();
        syncPlayingState();
    }

    playBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        playHero();
    });

    hero.addEventListener('click', (e) => {
        if (e.target.closest('.hero-copy')) return;
        if (video.paused || video.ended) {
            playHero();
        } else {
            pauseHero();
        }
    });

    video.addEventListener('play', syncPlayingState);
    video.addEventListener('pause', syncPlayingState);
    video.addEventListener('ended', syncPlayingState);
})();

(function () {
    document.querySelectorAll('#course-curriculum .path-unit-h').forEach((btn) => {
        btn.addEventListener('click', () => {
            const unit = btn.closest('.path-unit');
            if (!unit) return;
            const open = !unit.classList.contains('open');
            unit.classList.toggle('open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });
})();
</script>

<script>
    function openDescriptionModal() {
        document.getElementById('description-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDescriptionModal() {
        document.getElementById('description-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDescriptionModal();
    });

    let currentItemId = null;
    let currentItemType = null;

    async function handlePayment(itemId, price, type, title = 'تأكيد الدفع') {
        currentItemId = itemId;
        currentItemType = type;

        if (price == 0) {
            const result = await Swal.fire({
                title: 'دورة مجانية!',
                text: 'هذه الدورة مجانية تماماً. هل تريد الاشتراك الآن؟',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'نعم، اشترك الآن',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#111111',
                cancelButtonColor: '#6B7280'
            });
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري الاشتراك...',
                    html: '<i class="fas fa-spinner fa-spin fa-3x"></i>',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                await proceedFreeEnrollment();
            }
            return;
        }

        const fees = (price * 0.079) + 2;
        const total = price + fees;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('priceLabel').textContent = type === 'course' ? 'سعر الدورة:' : 'سعر النظام:';
        document.getElementById('originalPrice').textContent = price.toFixed(2) + ' AED';
        document.getElementById('fees').textContent = fees.toFixed(2) + ' AED';
        document.getElementById('totalPrice').textContent = total.toFixed(2) + ' AED';
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    async function proceedFreeEnrollment() {
        try {
            const response = await fetch('{{ route('course.payment.create') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ course_id: currentItemId })
            });
            const data = await response.json();
            if (data.success && data.is_free) {
                await Swal.fire({
                    title: 'تم الاشتراك بنجاح!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'رائع!',
                    confirmButtonColor: '#111111'
                });
                window.location.reload();
            } else {
                Swal.fire({ title: 'خطأ!', text: data.message || 'حدث خطأ أثناء الاشتراك', icon: 'error' });
            }
        } catch (error) {
            Swal.fire({ title: 'خطأ!', text: 'حدث خطأ في الاتصال', icon: 'error' });
        }
    }

    async function proceedPayment() {
        const payButton = document.getElementById('payButton');
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
        let endpoint = '';
        let payload = {};
        if (currentItemType === 'course') {
            endpoint = '{{ route('course.payment.create') }}';
            payload = { course_id: currentItemId };
        } else {
            endpoint = '{{ route('payment.create') }}';
            payload = { system_id: currentItemId };
        }
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (data.success) {
                if (data.is_free) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    window.location.href = data.payment_url;
                }
            } else {
                alert(data.message || 'حدث خطأ أثناء إنشاء الدفع');
                payButton.disabled = false;
                payButton.innerHTML = 'متابعة الدفع';
            }
        } catch (error) {
            alert('حدث خطأ في الاتصال');
            payButton.disabled = false;
            payButton.innerHTML = 'متابعة الدفع';
        }
    }

    function openModal(imageUrl) {
        Swal.fire({
            imageUrl: imageUrl,
            imageAlt: 'صورة تفصيلية',
            showCloseButton: true,
            showConfirmButton: false,
            background: '#fff',
            padding: '1rem',
        });
    }

    @auth
    @if($canEnroll && !$is_already_in && $actual_remaining > 0 && request()->boolean('enroll'))
    document.addEventListener('DOMContentLoaded', function () {
        // Drop ?enroll=1 so refresh doesn't re-open the flow.
        try {
            const url = new URL(window.location.href);
            url.searchParams.delete('enroll');
            window.history.replaceState({}, '', url.pathname + url.search + url.hash);
        } catch (e) {}

        handlePayment({{ (int) $course->id }}, {{ (float) $course->price }}, 'course', 'تأكيد الاشتراك');
    });
    @endif
    @endauth
</script>
@include('academy.partials.wishlist-script')
<x-video-protect />
@endsection
