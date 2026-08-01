@php
    $name = $locale === 'en' ? ($course->name_en ?: $course->name_ar) : $course->name_ar;
    $img = $course->main_image ? asset('storage/'.$course->main_image) : asset('assets/images/logo.webp');
    $avg = $course->academy_avg_rating ?? null;
    $levels = $course->levelLabels($locale);
    $owned = (bool) ($course->academy_owned ?? false);
    $payment = $course->academy_payment ?? null;
    $pathPercent = (int) ($course->academy_path_percent ?? 0);

    $typeLabel = match ($course->location_type) {
        'online' => __('messages.academy_type_online'),
        'recorded' => __('messages.academy_type_recorded'),
        'on_site' => __('messages.academy_type_onsite'),
        default => $course->location_type,
    };
    $categoryName = $course->category?->title($locale) ?: null;

    $detailsUrl = route('courses.show', $course);
    $primaryUrl = $detailsUrl;
    $primaryLabel = $course->isFree() ? __('messages.academy_start_now') : __('messages.academy_subscribe_now');
    $showPrimary = true;
    $showPrice = true;
    $showFreeBadge = $course->isFree();

    if ($owned && $payment) {
        $showFreeBadge = false;
        $showPrice = false;
        if ($course->isOnline()) {
            $primaryUrl = route('dashboard.my_courses.lecture', $payment->id);
            $primaryLabel = __('messages.academy_join_course');
        } elseif ($course->isRecorded()) {
            $primaryUrl = route('dashboard.my_courses.path', $payment->id);
            $primaryLabel = $pathPercent > 0 ? __('messages.academy_continue') : __('messages.academy_start');
        } elseif ($course->isOnSite()) {
            $showPrimary = false;
        }
    }
@endphp
<article class="soni-card">
    <a href="{{ $detailsUrl }}" class="soni-card-media">
        <img src="{{ $img }}" alt="{{ $name }}">
        @if(count($levels) || $showFreeBadge)
        <div class="soni-card-badges">
            @foreach($levels as $level)
            <span class="soni-badge">{{ $level }}</span>
            @endforeach
            @if($showFreeBadge)
            <span class="soni-badge soni-badge-free">{{ __('messages.academy_free') }}</span>
            @endif
        </div>
        @endif
    </a>
    <div class="soni-card-body">
        <h3 class="soni-card-title">
            <a href="{{ $detailsUrl }}">{{ $name }}</a>
        </h3>
        <div class="soni-card-status">
            <span class="soni-type-badge">{{ $typeLabel }}</span>
            @if($categoryName)
            <span class="soni-category" title="{{ $categoryName }}">{{ $categoryName }}</span>
            @else
            <span class="soni-category is-empty">—</span>
            @endif
        </div>
        <div class="soni-card-meta">
            <span class="soni-stars" @if($avg !== null) title="{{ number_format($avg, 1) }}" @endif>
                @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star{{ ($avg !== null && $i <= round($avg)) ? '' : ' is-empty' }}"></i>
                @endfor
                @if($avg !== null)
                <strong>{{ number_format($avg, 1) }}</strong>
                @endif
            </span>
        </div>
        <div class="soni-card-footer">
            <div class="soni-card-actions {{ $showPrimary ? '' : 'is-single' }}">
                @if($showPrimary)
                <a href="{{ $primaryUrl }}" class="soni-btn-primary">{{ $primaryLabel }}</a>
                @endif
                <a href="{{ $detailsUrl }}" class="soni-btn-ghost">{{ __('messages.academy_course_details') }}</a>
            </div>
            <div class="soni-card-price-row">
                @if($owned)
                <span class="soni-owned">{{ __('messages.academy_you_own') }}</span>
                @elseif($showPrice)
                <p class="soni-card-price">
                    @if($course->isFree())
                        {{ __('messages.academy_free') }}
                    @else
                        <span class="inline-flex items-center gap-1">
                            {{ number_format((float) $course->price, 2) }}
                            <x-drhm-icon width="14" height="16" />
                        </span>
                    @endif
                </p>
                @else
                <p class="soni-card-price is-empty">—</p>
                @endif
            </div>
        </div>
    </div>
</article>
