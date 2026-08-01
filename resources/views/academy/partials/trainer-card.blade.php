@php
    $trScore = (float) ($trainer->academy_rating ?? 0);
    $coursesCount = (int) ($trainer->academy_courses_count ?? $trainer->trainedCourses->count());
    $learnersCount = (int) ($trainer->academy_learners_count ?? 0);
@endphp
<a href="{{ route('academy.trainers.show', $trainer) }}" class="trainer-card trainer-card-link">
    <div class="trainer-frame">
        <div class="trainer-photo-wrap">
            <img src="{{ $trainer->avatarUrl() }}" alt="{{ $trainer->name }}" class="trainer-photo"
                onerror="this.src='{{ asset('assets/images/logo.webp') }}'">
            <div class="trainer-photo-veil" aria-hidden="true"></div>
            @if($trainer->countryFlagUrl())
            <img src="{{ $trainer->countryFlagUrl() }}" alt="{{ $trainer->country_name }}" class="trainer-flag" loading="lazy">
            @endif
            <div class="trainer-score" title="{{ number_format($trScore, 1) }}">
                <i class="fas fa-star"></i>
                <span>{{ number_format($trScore, 1) }}</span>
            </div>
        </div>
        <div class="trainer-meta">
            <h3 class="trainer-name">{{ $trainer->name }}</h3>
            <p class="trainer-cat">{{ $trainer->academy_category_label ?: __('messages.academy_trainer_fallback') }}</p>
            <div class="trainer-card-stats">
                <span><i class="fas fa-book-open"></i> {{ $coursesCount }}</span>
                <span><i class="fas fa-users"></i> {{ number_format($learnersCount) }}</span>
            </div>
            <div class="trainer-stars" aria-hidden="true">
                @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star" style="{{ $i <= round($trScore) ? '' : 'opacity:.28' }}"></i>
                @endfor
            </div>
        </div>
    </div>
</a>
