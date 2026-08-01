@props([
    'variant' => 'default',
])

@php
    $isAcademy = $variant === 'academy';
    $isRtl = app()->getLocale() === 'ar';
    $trustUnderImage = ! $isRtl;

    $badge = $isAcademy ? __('messages.academy_trusted') : __('messages.trusted_by_100');
    $title = $isAcademy ? __('messages.academy_hero_title') : __('messages.hero_title');
    $highlight = $isAcademy ? __('messages.academy_hero_highlight') : __('messages.hero_highlight');
    $withBrand = $isAcademy ? __('messages.academy_with_evorq') : __('messages.with_evorq');
    $description = $isAcademy ? __('messages.academy_hero_description') : __('messages.hero_description');
    $features = $isAcademy
        ? [
            __('messages.academy_feature_1'),
            __('messages.academy_feature_2'),
            __('messages.academy_feature_3'),
            __('messages.academy_feature_4'),
        ]
        : [
            __('messages.feature_1'),
            __('messages.feature_2'),
            __('messages.feature_3'),
            __('messages.feature_4'),
        ];
    $image = $isAcademy
        ? \App\Models\Setting::academyHeroImageUrl()
        : 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80';
    $imageAlt = $isAcademy ? __('messages.academy_hero_image_alt') : 'Dashboard Preview';

    $floatTopLabel = $isAcademy ? __('messages.academy_float_certified') : __('messages.success_rate');
    $floatTopValue = $isAcademy ? __('messages.academy_float_certified_value') : '99.9%';
    $floatBottomLabel = $isAcademy ? __('messages.academy_float_courses') : __('messages.avg_delivery');
    $floatBottomValue = $isAcademy
        ? __('messages.academy_float_courses_value')
        : '7 '.__('messages.days');

    $waveFill = $isAcademy ? '#f4f7fb' : '#ffffff';
    $floatTopNum = (int) filter_var($floatTopValue, FILTER_SANITIZE_NUMBER_INT);
    $floatBottomNum = (int) filter_var($floatBottomValue, FILTER_SANITIZE_NUMBER_INT);
@endphp

@if($isAcademy)
{{-- Academy cinematic hero: full-bleed image + vertical glass overlay --}}
<section class="academy-cinematic-hero" aria-label="{{ __('messages.academy') }}">
    <div class="ach-bg" aria-hidden="true">
        <img src="{{ $image }}" alt="" class="ach-bg-img" loading="eager" fetchpriority="high">
        <div class="ach-bg-veil"></div>
    </div>

    {{-- True vertical half overlay (full height, soft vertical edge) --}}
    <div class="ach-overlay" aria-hidden="true"></div>

    <div class="ach-orbs" aria-hidden="true">
        <span class="ach-orb ach-orb-a"></span>
        <span class="ach-orb ach-orb-b"></span>
    </div>

    <div class="ach-inner">
        <div class="ach-copy">
            <p class="ach-badge">
                <span class="ach-badge-dot" aria-hidden="true"></span>
                {{ $badge }}
            </p>

            <p class="ach-kicker">{{ $title }}</p>
            <h1 class="ach-title">
                <span class="ach-title-main">{{ $highlight }}</span>
                <span class="ach-title-brand">{{ $withBrand }}</span>
            </h1>

            <p class="ach-sub">{{ $description }}</p>

            <div class="ach-actions">
                <a href="#latest" class="ach-btn-primary">
                    {{ __('messages.academy_explore_courses') }}
                    <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i>
                </a>
            </div>

            <div class="ach-stats" role="list">
                <div class="ach-stat" role="listitem">
                    <strong data-count="{{ $floatTopNum }}" data-suffix="+">{{ $floatTopValue }}</strong>
                    <span>{{ $floatTopLabel }}</span>
                </div>
                <div class="ach-stat" role="listitem">
                    <strong data-count="{{ $floatBottomNum }}" data-suffix="+">{{ $floatBottomValue }}</strong>
                    <span>{{ $floatBottomLabel }}</span>
                </div>
                <div class="ach-stat" role="listitem">
                    <strong>24/7</strong>
                    <span>{{ __('messages.academy_feature_4') }}</span>
                </div>
            </div>
        </div>

        {{-- Image-side highlight panel (no overlapping floats) --}}
        <div class="ach-stage">
            <div class="ach-stage-card">
                <div class="ach-stage-glow"></div>
                <p class="ach-stage-label">{{ __('messages.academy') }}</p>
                <ul class="ach-stage-list">
                    @foreach($features as $i => $feature)
                    <li style="--i: {{ $i }}">
                        <i class="fas fa-check"></i>
                        <span>{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="ach-stage-metrics">
                    <div class="ach-metric">
                        <i class="fas fa-certificate"></i>
                        <div>
                            <b data-count="{{ $floatTopNum }}" data-suffix="+">{{ $floatTopValue }}</b>
                            <small>{{ $floatTopLabel }}</small>
                        </div>
                    </div>
                    <div class="ach-metric">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <div>
                            <b data-count="{{ $floatBottomNum }}" data-suffix="+">{{ $floatBottomValue }}</b>
                            <small>{{ $floatBottomLabel }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="#latest" class="ach-scroll" aria-label="{{ __('messages.academy_next') }}">
        <span></span>
    </a>

    <div class="ach-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 90" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 60C240 20 480 0 720 18C960 36 1200 70 1440 50V90H0V60Z" fill="{{ $waveFill }}"/>
        </svg>
    </div>
</section>

<style>
    .academy-cinematic-hero {
        --ach-ink: #061525;
        --ach-teal: #0b8f7f;
        --ach-gold: #d4a017;
        position: relative;
        isolation: isolate;
        min-height: min(92dvh, 56rem);
        display: flex;
        align-items: stretch;
        color: #fff;
        overflow: clip;
        font-family: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
        padding-bottom: 3.25rem;
    }
    .ach-bg {
        position: absolute; inset: 0; z-index: 0;
    }
    .ach-bg-img {
        width: 100%; height: 100%; object-fit: cover; object-position: center;
        display: block;
        transform: scale(1.04);
        animation: achKen 22s ease-in-out infinite alternate;
    }
    @keyframes achKen {
        from { transform: scale(1.04) translate3d(0,0,0); }
        to { transform: scale(1.1) translate3d(-1.5%, 1%, 0); }
    }
    .ach-bg-veil {
        position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(6,21,37,.28) 0%, transparent 35%, transparent 70%, rgba(6,21,37,.35) 100%);
        pointer-events: none;
    }

    /* Vertical half overlay — full height, fades only on the vertical edge */
    .ach-overlay {
        position: absolute;
        top: 0;
        bottom: 0;
        inset-inline-start: 0;
        width: 52%;
        z-index: 1;
        pointer-events: none;
        background: linear-gradient(
            to right,
            rgba(6, 21, 37, .9) 0%,
            rgba(6, 21, 37, .82) 58%,
            rgba(6, 21, 37, .45) 82%,
            rgba(6, 21, 37, 0) 100%
        );
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        -webkit-mask-image: linear-gradient(to right, #000 0%, #000 70%, transparent 100%);
        mask-image: linear-gradient(to right, #000 0%, #000 70%, transparent 100%);
    }
    [dir="rtl"] .ach-overlay {
        background: linear-gradient(
            to left,
            rgba(6, 21, 37, .9) 0%,
            rgba(6, 21, 37, .82) 58%,
            rgba(6, 21, 37, .45) 82%,
            rgba(6, 21, 37, 0) 100%
        );
        -webkit-mask-image: linear-gradient(to left, #000 0%, #000 70%, transparent 100%);
        mask-image: linear-gradient(to left, #000 0%, #000 70%, transparent 100%);
    }
    @media (max-width: 899px) {
        .ach-overlay {
            width: 100%;
            background: linear-gradient(
                to bottom,
                rgba(6, 21, 37, .88) 0%,
                rgba(6, 21, 37, .78) 55%,
                rgba(6, 21, 37, .62) 100%
            ) !important;
            -webkit-mask-image: none !important;
            mask-image: none !important;
            backdrop-filter: blur(10px);
        }
    }

    .ach-orbs { position: absolute; inset: 0; z-index: 1; pointer-events: none; overflow: hidden; }
    .ach-orb {
        position: absolute; border-radius: 999px; filter: blur(40px);
        opacity: .4; animation: achPulse 8s ease-in-out infinite;
    }
    .ach-orb-a {
        width: 16rem; height: 16rem; top: 14%; inset-inline-start: 6%;
        background: rgba(11,143,127,.4);
    }
    .ach-orb-b {
        width: 12rem; height: 12rem; bottom: 22%; inset-inline-end: 10%;
        background: rgba(212,160,23,.3); animation-delay: 2s;
    }
    @keyframes achPulse {
        0%, 100% { transform: scale(1); opacity: .3; }
        50% { transform: scale(1.12); opacity: .5; }
    }

    .ach-inner {
        position: relative; z-index: 2;
        width: 100%;
        max-width: min(92rem, 100%);
        margin: 0 auto;
        padding: clamp(4.25rem, 8vw, 7rem) clamp(1.1rem, 3.5vw, 2.25rem) clamp(3.5rem, 6vw, 5rem);
        display: grid;
        gap: 2rem;
        align-items: center;
        min-width: 0;
    }
    @media (min-width: 900px) {
        .ach-inner {
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .9fr);
            gap: clamp(1.5rem, 4vw, 3.5rem);
        }
    }

    .ach-copy { min-width: 0; max-width: 36rem; }
    .ach-badge {
        display: inline-flex; align-items: center; gap: .55rem;
        margin: 0 0 1rem; padding: .45rem .95rem;
        border-radius: 999px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.18);
        font-size: .8rem; font-weight: 700;
        backdrop-filter: blur(8px);
    }
    .ach-badge-dot {
        width: .55rem; height: .55rem; border-radius: 999px;
        background: var(--ach-teal);
        box-shadow: 0 0 0 0 rgba(11,143,127,.55);
        animation: achPing 1.8s ease-out infinite;
    }
    @keyframes achPing {
        0% { box-shadow: 0 0 0 0 rgba(11,143,127,.55); }
        70% { box-shadow: 0 0 0 10px rgba(11,143,127,0); }
        100% { box-shadow: 0 0 0 0 rgba(11,143,127,0); }
    }
    .ach-kicker {
        margin: 0 0 .35rem;
        font-size: clamp(1rem, 2vw, 1.2rem);
        font-weight: 700;
        color: rgba(255,255,255,.78);
    }
    .ach-title {
        margin: 0 0 1rem;
        font-family: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif;
        font-weight: 800;
        line-height: 1.25;
        font-size: clamp(2rem, 5.2vw, 3.65rem);
    }
    .ach-title-main {
        display: block;
        background: linear-gradient(120deg, #fff 20%, #f7e7b8 55%, #fff 90%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        animation: achShine 6s linear infinite;
    }
    .ach-title-brand {
        display: block;
        color: #fff;
        font-size: .72em;
        margin-top: .15rem;
        opacity: .95;
    }
    @keyframes achShine {
        to { background-position: 200% center; }
    }
    .ach-sub {
        margin: 0 0 1.5rem;
        color: rgba(255,255,255,.82);
        font-size: clamp(.95rem, 1.5vw, 1.12rem);
        line-height: 1.7;
        max-width: 32rem;
    }
    .ach-actions {
        display: flex; flex-wrap: wrap; gap: .7rem;
        margin-bottom: 1.6rem;
    }
    .ach-btn-primary,
    .ach-btn-ghost {
        display: inline-flex; align-items: center; justify-content: center; gap: .55rem;
        padding: .95rem 1.4rem; border-radius: 999px;
        font-weight: 800; font-size: .95rem; text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .ach-btn-primary {
        background: linear-gradient(135deg, #fff 0%, #f7e7b8 100%);
        color: var(--ach-ink);
        box-shadow: 0 14px 34px rgba(0,0,0,.28);
    }
    .ach-btn-primary:hover { transform: translateY(-3px) scale(1.02); }
    .ach-btn-ghost {
        color: #fff;
        border: 1.5px solid rgba(255,255,255,.35);
        background: rgba(255,255,255,.08);
        backdrop-filter: blur(8px);
    }
    .ach-btn-ghost:hover { background: rgba(255,255,255,.16); transform: translateY(-2px); }

    .ach-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .65rem;
        max-width: 28rem;
    }
    .ach-stat {
        padding: .75rem .7rem;
        border-radius: 1rem;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        backdrop-filter: blur(10px);
        min-width: 0;
    }
    .ach-stat strong {
        display: block;
        font-family: 'Noto Kufi Arabic', sans-serif;
        font-size: clamp(1.15rem, 2vw, 1.45rem);
        font-weight: 800;
        line-height: 1.1;
        color: #f7e7b8;
    }
    .ach-stat span {
        margin-top: .25rem;
        font-size: .68rem;
        font-weight: 700;
        color: rgba(255,255,255,.7);
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        white-space: normal;
    }
    @media (min-width: 768px) and (max-width: 1399px) {
        .ach-title { font-size: clamp(1.75rem, 4.2vw, 2.75rem); }
        .ach-inner {
            padding-inline: clamp(1rem, 2.5vw, 1.75rem);
        }
        .ach-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .55rem;
        }
        .ach-stat { padding: .65rem .55rem; }
        .ach-stat strong { font-size: clamp(1rem, 1.8vw, 1.25rem); }
        .ach-stat span { font-size: .62rem; }
    }

    .ach-stage {
        position: relative;
        display: none;
        justify-content: flex-end;
    }
    @media (min-width: 900px) {
        .ach-stage { display: flex; }
    }
    .ach-stage-card {
        position: relative;
        width: min(100%, 23.5rem);
        padding: 1.35rem 1.25rem 1.15rem;
        border-radius: 1.5rem;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.24);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 24px 50px rgba(0,0,0,.28);
        overflow: hidden;
    }
    .ach-stage-glow {
        position: absolute; inset: auto -20% -40% auto;
        width: 12rem; height: 12rem; border-radius: 999px;
        background: radial-gradient(circle, rgba(11,143,127,.45), transparent 70%);
        pointer-events: none;
    }
    .ach-stage-label {
        position: relative;
        margin: 0 0 1rem;
        font-size: .72rem; font-weight: 800;
        letter-spacing: .14em; text-transform: uppercase;
        color: var(--ach-gold);
    }
    .ach-stage-list {
        position: relative;
        list-style: none; margin: 0; padding: 0;
        display: flex; flex-direction: column; gap: .55rem;
    }
    .ach-stage-list li {
        display: flex; align-items: center; gap: .7rem;
        padding: .7rem .8rem;
        border-radius: .9rem;
        background: rgba(6,21,37,.35);
        border: 1px solid rgba(255,255,255,.08);
        font-size: .9rem; font-weight: 700;
        animation: achRise .7s ease both;
        animation-delay: calc(var(--i) * .12s + .2s);
    }
    .ach-stage-list i {
        width: 1.7rem; height: 1.7rem; border-radius: 999px;
        display: inline-flex; align-items: center; justify-content: center;
        background: var(--ach-teal); color: #fff; font-size: .65rem; flex-shrink: 0;
    }
    @keyframes achRise {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: none; }
    }

    .ach-stage-metrics {
        position: relative;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .55rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,.14);
    }
    .ach-metric {
        display: flex; align-items: center; gap: .55rem;
        min-width: 0;
        padding: .55rem .6rem;
        border-radius: .85rem;
        background: rgba(255,255,255,.92);
        color: var(--ach-ink);
    }
    .ach-metric i {
        width: 2rem; height: 2rem; border-radius: .65rem;
        display: inline-flex; align-items: center; justify-content: center;
        color: #fff; flex-shrink: 0; font-size: .75rem;
        background: var(--ach-ink);
    }
    .ach-metric:first-child i { background: var(--ach-gold); }
    .ach-metric b {
        display: block;
        font-family: 'Noto Kufi Arabic', sans-serif;
        font-size: 1rem; font-weight: 800; line-height: 1.1;
    }
    .ach-metric small {
        display: block;
        font-size: .62rem; font-weight: 700; color: #5a6d82;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 6.5rem;
    }

    .ach-scroll {
        position: absolute; z-index: 5;
        left: 50%;
        bottom: calc(clamp(3.25rem, 6vw, 5rem) + .35rem);
        transform: translateX(-50%);
        width: 1.75rem; height: 2.6rem;
        border-radius: 999px;
        border: 1.5px solid rgba(255,255,255,.7);
        background: rgba(6,21,37,.35);
        backdrop-filter: blur(6px);
        display: flex; justify-content: center;
        padding-top: .4rem;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(0,0,0,.2);
    }
    .ach-scroll span {
        width: .4rem; height: .4rem; border-radius: 999px;
        background: #fff;
        animation: achScroll 1.6s ease-in-out infinite;
    }
    @keyframes achScroll {
        0% { transform: translateY(0); opacity: 1; }
        100% { transform: translateY(12px); opacity: 0; }
    }

    .ach-wave {
        position: absolute; inset-inline: 0; bottom: 0; z-index: 3;
        line-height: 0; pointer-events: none;
    }
    .ach-wave svg { display: block; width: 100%; height: clamp(2.5rem, 5vw, 4.5rem); }

    @media (max-width: 899px) {
        .academy-cinematic-hero { min-height: auto; padding-bottom: 3.75rem; }
        .ach-inner { padding-top: 3.75rem; padding-bottom: 3.25rem; }
        .ach-stats { max-width: none; }
        .ach-scroll { bottom: 3.1rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .ach-bg-img, .ach-orb, .ach-badge-dot, .ach-title-main,
        .ach-stage-list li, .ach-scroll span {
            animation: none !important;
        }
        .ach-bg-img { transform: none; }
    }
</style>

@else
<!-- Hero Section (default / non-academy) -->
<section class="relative text-white overflow-x-clip bg-gradient-to-br from-gray-900 via-black to-gray-900">
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0"
                style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-black rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-gray-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-40 w-72 h-72 bg-black rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <div class="container mx-auto px-4 py-16 md:py-32 relative z-10 max-w-full">
        <div class="grid md:grid-cols-2 gap-10 md:gap-12 items-center min-w-0">
            <div class="space-y-6 md:space-y-8 min-w-0">
                <div class="inline-flex max-w-full items-center gap-2 bg-black/20 border border-gray-500/30 rounded-full px-4 py-2 backdrop-blur-sm">
                    <span class="relative flex h-3 w-3 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-500"></span>
                    </span>
                    <span class="text-sm font-medium truncate">{{ $badge }}</span>
                </div>

                <h1 style="line-height: 1.45 !important;" class="text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-bold leading-tight break-words">
                    {{ $title }} {{ $highlight }} {{ $withBrand }}
                </h1>

                <p class="text-base md:text-xl text-gray-300 leading-relaxed break-words">
                    {{ $description }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($features as $feature)
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex-shrink-0 w-10 h-10 bg-black/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-gray-200 break-words">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <a href="https://wa.me/971501774477" target="_blank"
                        class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border-2 border-white/20 text-white px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl font-bold text-base sm:text-lg hover:bg-white/20 transition-all hover:scale-[1.02]">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        {{ __('messages.talk_to_expert') }}
                    </a>
                </div>

                @unless($trustUnderImage)
                <div class="pt-4 min-w-0">
                    @include('components.partials.hero-trust')
                </div>
                @endunless
            </div>

            <div class="relative min-w-0 w-full max-w-full">
                <div class="relative min-w-0 w-full overflow-visible md:overflow-visible">
                    <div class="relative z-10 min-w-0">
                        <div class="bg-gradient-to-br from-gray-600/10 to-transparent backdrop-blur-xs border border-black rounded-2xl p-3 sm:p-6 md:p-8 shadow-2xl overflow-hidden">
                            <img src="{{ $image }}" alt="{{ $imageAlt }}"
                                class="w-full max-w-full h-auto rounded-lg shadow-2xl md:hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>

                    <div class="absolute z-20 bg-white rounded-xl shadow-2xl p-3 sm:p-4 animate-float
                        top-3 right-3 md:top-10 md:-right-6 lg:-right-10"
                        style="color:#0D2444; max-width:min(12rem, calc(100% - 1.5rem));">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center"
                                style="background:#22c55e;color:#fff;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-bold truncate" style="margin:0;color:#6b7280;">{{ $floatTopLabel }}</p>
                                <p class="text-xl sm:text-2xl font-bold" style="margin:.15rem 0 0;line-height:1;color:#16a34a;">{{ $floatTopValue }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute z-20 bg-white rounded-xl shadow-2xl p-3 sm:p-4 animate-float animation-delay-2000
                        bottom-3 left-3 md:-bottom-5 md:-left-6 lg:-left-10"
                        style="color:#0D2444; max-width:min(12rem, calc(100% - 1.5rem));">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center"
                                style="background:#3b82f6;color:#fff;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-bold truncate" style="margin:0;color:#6b7280;">{{ $floatBottomLabel }}</p>
                                <p class="text-xl sm:text-2xl font-bold" style="margin:.15rem 0 0;line-height:1;color:#2563eb;">{{ $floatBottomValue }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($trustUnderImage)
                <div class="relative z-10 mt-8 flex justify-end min-w-0">
                    @include('components.partials.hero-trust')
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0 z-0 leading-none pointer-events-none">
        <svg class="block w-full h-auto" viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path
                d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                fill="{{ $waveFill }}" />
        </svg>
    </div>
</section>

<style>
    @keyframes blob {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(20px, -50px) scale(1.1); }
        50% { transform: translate(-20px, 20px) scale(0.9); }
        75% { transform: translate(50px, 50px) scale(1.05); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    .animate-blob { animation: blob 7s infinite; }
    .animate-float { animation: float 3s ease-in-out infinite; }
    .animation-delay-2000 { animation-delay: 2s; }
    .animation-delay-4000 { animation-delay: 4s; }
    @media (max-width: 767px) {
        .animate-float { animation: none; }
    }
</style>
@endif
