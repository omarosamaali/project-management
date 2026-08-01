@extends('layouts.user')

@section('title', $trainer->name . ' — ' . __('messages.academy'))

@section('content')
@include('academy.partials.styles')

@php
    $trScore = (float) ($trainer->academy_rating ?? 0);
    $coursesCount = (int) ($trainer->academy_courses_count ?? $courses->count());
    $learnersCount = (int) ($trainer->academy_learners_count ?? 0);
    $skills = collect($trainer->skills ?? [])->filter()->values();
@endphp

<div class="academy-page">
    <section class="academy-section is-tight">
        <a href="{{ route('academy.trainers.index') }}" class="academy-back">
            <i class="fas fa-{{ $locale === 'ar' ? 'arrow-right' : 'arrow-left' }}"></i>
            {{ __('messages.academy_all_trainers') }}
        </a>

        <div class="trainer-profile">
            <div class="trainer-profile-photo">
                <img src="{{ $trainer->avatarUrl() }}" alt="{{ $trainer->name }}"
                    onerror="this.src='{{ asset('assets/images/logo.webp') }}'">
                @if($trainer->countryFlagUrl())
                <img src="{{ $trainer->countryFlagUrl() }}" alt="{{ $trainer->country_name }}" class="trainer-profile-flag" loading="lazy">
                @endif
            </div>

            <div class="trainer-profile-body">
                <p class="academy-kicker">{{ $trainer->academy_category_label ?: __('messages.academy_trainer_fallback') }}</p>
                <h1 class="trainer-profile-name display">{{ $trainer->name }}</h1>

                <div class="trainer-profile-stats">
                    <div class="trainer-stat">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>{{ trans_choice('messages.academy_trainer_courses_count', $coursesCount, ['count' => $coursesCount]) }}</span>
                    </div>
                    <div class="trainer-stat">
                        <i class="fas fa-users"></i>
                        <span>{{ trans_choice('messages.academy_trainer_learners_count', $learnersCount, ['count' => number_format($learnersCount)]) }}</span>
                    </div>
                    <div class="trainer-stat">
                        <i class="fas fa-star"></i>
                        <span>{{ number_format($trScore, 1) }} / 5</span>
                    </div>
                </div>

                <div class="trainer-profile-stars" aria-hidden="true">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star" style="{{ $i <= round($trScore) ? '' : 'opacity:.28' }}"></i>
                    @endfor
                </div>

                @if($skills->isNotEmpty())
                <div class="trainer-skill-chips">
                    @foreach($skills as $skill)
                    <span class="trainer-skill-chip">{{ is_array($skill) ? ($skill['name'] ?? $skill['title'] ?? '') : $skill }}</span>
                    @endforeach
                </div>
                @endif

                <p class="trainer-profile-lead">
                    {{ __('messages.academy_trainer_profile_lead', [
                        'name' => $trainer->name,
                        'category' => $trainer->academy_category_label ?: __('messages.academy_trainer_fallback'),
                    ]) }}
                </p>

                @if($coursesCount > 0)
                <a href="#trainer-courses" class="academy-cta">
                    <i class="fas fa-layer-group"></i>
                    {{ __('messages.academy_trainer_view_courses', ['count' => $coursesCount]) }}
                </a>
                @endif
            </div>
        </div>
    </section>

    <section class="academy-section" id="trainer-courses">
        <div class="academy-sec-head">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_latest_kicker') }}</p>
                <h2 class="academy-h2 display">{{ __('messages.academy_trainer_courses_title', ['name' => $trainer->name]) }}</h2>
            </div>
            <p class="academy-sub">{{ __('messages.academy_trainer_courses_sub') }}</p>
        </div>

        @if($courses->isNotEmpty())
        <div class="soni-grid">
            @foreach($courses as $course)
                @include('academy.partials.course-card', ['course' => $course, 'locale' => $locale])
            @endforeach
        </div>
        @else
        <div class="academy-empty">
            <p>{{ __('messages.academy_trainer_no_courses') }}</p>
            <a href="{{ route('academy.courses') }}" class="academy-more-btn">{{ __('messages.academy_view_all_courses') }}</a>
        </div>
        @endif
    </section>
</div>
@endsection
