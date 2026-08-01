@extends('layouts.user')

@section('title', __('messages.academy') . ' — Evorq')

@section('content')
@include('academy.partials.styles')
@include('dashboard.courses.partials.certificate-document-styles')

<x-hero-section variant="academy" />

<div class="academy-page">
    @if($categories->isNotEmpty())
    <section class="academy-section reveal" id="categories">
        <div class="academy-sec-head">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_categories_kicker') }}</p>
                <h2 class="academy-h2 display">{{ __('messages.academy_categories_title') }}</h2>
            </div>
            <p class="academy-sub">{{ __('messages.academy_categories_sub') }}</p>
        </div>
        <div class="snap-slider-wrap" data-snap-slider-wrap data-autoplay="{{ $categories->count() > 1 ? '1' : '0' }}" data-natural-slides="1">
            <button type="button" class="snap-nav prev" data-snap-prev aria-label="{{ __('messages.academy_prev') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'right' : 'left' }}"></i></button>
            <button type="button" class="snap-nav next" data-snap-next aria-label="{{ __('messages.academy_next') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}"></i></button>
            <div class="snap-slider-viewport">
                <div class="snap-slider" data-snap-slider>
                    @foreach($categories as $cat)
                    <a href="{{ route('academy.courses', ['category' => $cat->id]) }}"
                        class="snap-slide cat-slide cat-tone-{{ $loop->index % 6 }}">
                        <img src="{{ $cat->iconUrl() }}" alt="">
                        <span>
                            <span class="cat-slide-title">{{ $cat->title($locale) }}</span>
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="academy-section reveal" id="latest">
        <div class="academy-sec-head">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_latest_kicker') }}</p>
                <h2 class="academy-h2 display">{{ __('messages.academy_latest_title') }}</h2>
            </div>
            <p class="academy-sub">{{ __('messages.academy_latest_sub') }}</p>
        </div>

        @if($latestCourses->isEmpty())
        <div class="text-center py-14 border border-dashed border-[var(--line)] rounded-3xl bg-white/70">
            <p class="font-bold text-lg mb-1">{{ __('messages.academy_latest_empty_title') }}</p>
            <p class="text-sm text-[var(--muted)]">{{ __('messages.academy_latest_empty_sub') }}</p>
        </div>
        @else
        <div class="soni-grid is-bento">
            @foreach($latestCourses as $course)
                @include('academy.partials.course-card', ['course' => $course, 'locale' => $locale])
            @endforeach
        </div>
        <div class="academy-more-wrap">
            <a href="{{ route('academy.courses') }}" class="academy-more-btn">
                {{ __('messages.academy_view_all_courses') }}
                <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}"></i>
            </a>
        </div>
        @endif
    </section>

    @if($reviews->isNotEmpty())
    <section class="reviews-band reveal" id="reviews">
        <div class="academy-section">
            <div class="academy-sec-head">
                <div>
                    <p class="academy-kicker">{{ __('messages.academy_reviews_kicker') }}</p>
                    <h2 class="academy-h2 display">{{ __('messages.academy_reviews_title') }}</h2>
                </div>
                <p class="academy-sub">{{ __('messages.academy_reviews_sub') }}</p>
            </div>
            <div class="snap-slider-wrap {{ $reviews->count() > 5 ? '' : 'is-nav-hidden' }}"
                data-snap-slider-wrap
                data-autoplay="{{ $reviews->count() > 5 ? '1' : '0' }}"
                data-min-slide="140"
                data-reserve-slots="5">
                <button type="button" class="snap-nav prev" data-snap-prev {{ $reviews->count() > 5 ? '' : 'hidden' }} aria-label="{{ __('messages.academy_prev') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'right' : 'left' }}"></i></button>
                <button type="button" class="snap-nav next" data-snap-next {{ $reviews->count() > 5 ? '' : 'hidden' }} aria-label="{{ __('messages.academy_next') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}"></i></button>
                <div class="snap-slider-viewport">
                    <div class="snap-slider" data-snap-slider>
                    @foreach($reviews as $rating)
                    @php
                        $score = $rating->averageScaleScore();
                        $userName = $rating->user->name ?? __('messages.academy_trainee');
                        $initials = collect(preg_split('/\s+/u', trim($userName)))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
                        $courseName = $rating->course
                            ? ($locale === 'en' ? ($rating->course->name_en ?: $rating->course->name_ar) : $rating->course->name_ar)
                            : '';
                        $avatar = $rating->user?->avatar ? $rating->user->avatarUrl() : null;
                        $initialFallback = $locale === 'en' ? 'L' : 'م';
                    @endphp
                    <article class="snap-slide review-card">
                        <div class="review-head">
                            @if($avatar)
                            <img src="{{ $avatar }}" alt="" class="review-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="review-avatar" style="display:none">{{ $initials ?: $initialFallback }}</span>
                            @else
                            <span class="review-avatar">{{ $initials ?: $initialFallback }}</span>
                            @endif
                            <div>
                                <div class="review-name">{{ $userName }}</div>
                                <div class="review-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star" style="{{ ($score === null || $i > round($score)) ? 'opacity:.25' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p class="review-text">{{ $rating->feedbackText() ?: __('messages.academy_review_fallback') }}</p>
                        @if($rating->course)
                        <a href="{{ route('courses.show', $rating->course) }}" class="review-course">
                            <i class="fas fa-play"></i>
                            <span>{{ $courseName }}</span>
                        </a>
                        @endif
                    </article>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($trainers->isNotEmpty())
    <section class="academy-section reveal" id="trainers">
        <div class="academy-sec-head">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_trainers_kicker') }}</p>
                <h2 class="academy-h2 display">{{ __('messages.academy_trainers_title') }}</h2>
            </div>
            <p class="academy-sub">{{ __('messages.academy_trainers_sub') }}</p>
        </div>
        <div class="snap-slider-wrap {{ $trainers->count() > 1 ? '' : 'is-nav-hidden' }}"
            data-snap-slider-wrap
            data-autoplay="{{ $trainers->count() > 4 ? '1' : '0' }}"
            data-fixed-slide="210">
            <button type="button" class="snap-nav prev" data-snap-prev aria-label="{{ __('messages.academy_prev') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'right' : 'left' }}"></i></button>
            <button type="button" class="snap-nav next" data-snap-next aria-label="{{ __('messages.academy_next') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}"></i></button>
            <div class="snap-slider-viewport">
                <div class="snap-slider" data-snap-slider>
                @foreach($trainers as $trainer)
                <div class="snap-slide">
                    @include('academy.partials.trainer-card', ['trainer' => $trainer, 'locale' => $locale])
                </div>
                @endforeach
                </div>
            </div>
        </div>
        <div class="academy-more-wrap">
            <a href="{{ route('academy.trainers.index') }}" class="academy-more-btn">
                {{ __('messages.academy_all_trainers') }}
                <i class="fas fa-{{ $locale === 'ar' ? 'arrow-left' : 'arrow-right' }}"></i>
            </a>
        </div>
    </section>
    @endif

    <section class="trust-showcase reveal" id="trust">
        <div class="trust-showcase-inner">
            <div class="trust-showcase-main">
                <div class="trust-cert-preview" aria-hidden="true">
                    <div class="trust-cert-live" data-trust-cert-live>
                        <div class="trust-cert-live-scale" data-trust-cert-scale>
                            @include('dashboard.courses.partials.certificate-document', [
                                'traineeName' => __('messages.academy_trust_cert_preview_name'),
                                'courseNameAr' => __('messages.academy_trust_cert_preview_course_ar'),
                                'courseNameEn' => __('messages.academy_trust_cert_preview_course_en'),
                                'courseDate' => __('messages.academy_trust_cert_preview_date'),
                                'certificateId' => 'trust-certificate-preview',
                            ])
                        </div>
                    </div>
                </div>

                <div class="trust-showcase-copy">
                    <p class="trust-showcase-kicker">{{ __('messages.academy_trust_kicker') }}</p>
                    <h2 class="trust-showcase-title display">{{ __('messages.academy_trust_title') }}</h2>
                    <p class="trust-showcase-sub">{{ __('messages.academy_trust_sub') }}</p>

                    <div class="trust-highlights">
                        <div class="trust-highlight">
                            <div class="trust-highlight-icon is-cert" aria-hidden="true">
                                <i class="fas fa-award"></i>
                            </div>
                            <div>
                                <h3>{{ __('messages.academy_trust_cert_title') }}</h3>
                                <p>{{ __('messages.academy_trust_cert_body') }}</p>
                            </div>
                        </div>
                        <div class="trust-highlight">
                            <div class="trust-highlight-icon is-guarantee" aria-hidden="true">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h3>{{ __('messages.academy_trust_guarantee_title') }}</h3>
                                <p>{{ __('messages.academy_trust_guarantee_body') }}</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('academy.courses') }}" class="trust-showcase-cta">
                        {{ __('messages.academy_subscribe_now') }}
                        <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}"></i>
                    </a>
                </div>
            </div>

            <div class="trust-feature-bar">
                <div class="trust-feature">
                    <i class="fas fa-infinity"></i>
                    <span>{{ __('messages.academy_trust_2_title') }}<small>{{ __('messages.academy_trust_2_body') }}</small></span>
                </div>
                <div class="trust-feature">
                    <i class="fas fa-laptop-code"></i>
                    <span>{{ __('messages.academy_trust_3_title') }}<small>{{ __('messages.academy_trust_3_body') }}</small></span>
                </div>
                <div class="trust-feature">
                    <i class="fas fa-gift"></i>
                    <span>{{ __('messages.academy_trust_free_title') }}<small>{{ __('messages.academy_trust_free_body') }}</small></span>
                </div>
                <div class="trust-feature">
                    <i class="fas fa-clock"></i>
                    <span>{{ __('messages.academy_trust_4_title') }}<small>{{ __('messages.academy_trust_4_body') }}</small></span>
                </div>
            </div>
        </div>
    </section>

    <section class="academy-section reveal" id="how">
        <div class="academy-sec-head">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_how_kicker') }}</p>
                <h2 class="academy-h2 display">{{ __('messages.academy_how_title') }}</h2>
            </div>
            <p class="academy-sub">{{ __('messages.academy_how_sub') }}</p>
        </div>
        <div class="academy-steps reveal-stagger">
            <div class="academy-step reveal">
                <div class="academy-step-num">01</div>
                <h3 class="font-bold mb-1">{{ __('messages.academy_how_1_title') }}</h3>
                <p class="text-sm text-[var(--muted)]">{{ __('messages.academy_how_1_body') }}</p>
            </div>
            <div class="academy-step reveal">
                <div class="academy-step-num">02</div>
                <h3 class="font-bold mb-1">{{ __('messages.academy_how_2_title') }}</h3>
                <p class="text-sm text-[var(--muted)]">{{ __('messages.academy_how_2_body') }}</p>
            </div>
            <div class="academy-step reveal">
                <div class="academy-step-num">03</div>
                <h3 class="font-bold mb-1">{{ __('messages.academy_how_3_title') }}</h3>
                <p class="text-sm text-[var(--muted)]">{{ __('messages.academy_how_3_body') }}</p>
            </div>
        </div>
    </section>

    <section class="academy-banner reveal">
        <div>
            <h2 class="display text-2xl md:text-3xl font-bold mb-2">{{ __('messages.academy_banner_title') }}</h2>
            <p class="text-white/80 text-sm md:text-base max-w-xl">{{ __('messages.academy_banner_sub') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('academy.courses') }}" class="academy-cta">{{ __('messages.academy_explore_courses') }}</a>
            <a href="{{ $myCoursesUrl }}" class="academy-cta-ghost">{{ auth()->check() && auth()->user()->canLearnCourses() ? __('messages.academy_my_courses') : __('messages.login') }}</a>
        </div>
    </section>
</div>

@include('academy.partials.snap-slider-script')
@include('academy.partials.interactions-script')
@include('academy.partials.wishlist-script')
<script>
(function () {
    var CERT_W = 900;
    function fitTrustCert() {
        document.querySelectorAll('[data-trust-cert-live]').forEach(function (frame) {
            var scaleEl = frame.querySelector('[data-trust-cert-scale]');
            if (!scaleEl) return;
            var avail = frame.getBoundingClientRect().width || 420;
            var scale = Math.max(0.05, Math.min(1, avail / CERT_W));
            scaleEl.style.transformOrigin = 'top left';
            scaleEl.style.transform = 'scale(' + scale + ')';
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fitTrustCert);
    } else {
        fitTrustCert();
    }
    window.addEventListener('resize', fitTrustCert);
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(fitTrustCert);
    }
})();
</script>
@endsection
