@php
    $name = $locale === 'en' ? ($course->name_en ?: $course->name_ar) : $course->name_ar;
    $img = $course->main_image ? asset('storage/'.$course->main_image) : asset('assets/images/logo.webp');
    $avg = $course->academy_avg_rating ?? null;
    $levelMap = collect(\App\Models\Course::levelOptions())->keyBy('key');
    $levelKeys = $course->levelKeys();
    $owned = (bool) ($course->academy_owned ?? false);
    $payment = $course->academy_payment ?? null;
    $pathPercent = (int) ($course->academy_path_percent ?? 0);
    $wishlisted = (bool) ($course->academy_wishlisted ?? false);
    $wishlistToggleUrl = route('academy.wishlist.toggle', $course);
    $wishlistLoginUrl = \App\Support\AuthUi::loginUrl(['redirect' => url()->current()]);

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

    $showApplyDeadline = $course->hasRegistrationDeadline();
    $registrationClosed = $showApplyDeadline && $course->isRegistrationClosed();
    $applyEndedBadge = $registrationClosed && ! $owned;
    if ($applyEndedBadge) {
        $showPrimary = false;
    }
    $applyUntilLabel = $showApplyDeadline
        ? $course->last_date->copy()->locale($locale)->translatedFormat($locale === 'ar' ? 'j M Y' : 'M j, Y')
        : null;
@endphp
<article class="soni-card">
    <div class="soni-card-media">
        <a href="{{ $detailsUrl }}" class="soni-card-media-link">
            <img src="{{ $img }}" alt="{{ $name }}">
        </a>
        @if(count($levelKeys) || $showFreeBadge)
        <div class="soni-card-badges">
            @foreach($levelKeys as $levelKey)
            @php
                $levelOpt = $levelMap->get($levelKey);
                $levelLabel = $levelOpt
                    ? ($locale === 'en' ? $levelOpt['label_en'] : $levelOpt['label_ar'])
                    : $levelKey;
            @endphp
            <span class="soni-badge is-{{ $levelKey }}">{{ $levelLabel }}</span>
            @endforeach
            @if($showFreeBadge)
            <span class="soni-badge soni-badge-free">{{ __('messages.academy_free') }}</span>
            @endif
        </div>
        @endif
        <button type="button"
            class="soni-wish-btn {{ $wishlisted ? 'is-on' : '' }}"
            data-wishlist-toggle
            data-url="{{ $wishlistToggleUrl }}"
            data-login-url="{{ $wishlistLoginUrl }}"
            data-wishlisted="{{ $wishlisted ? '1' : '0' }}"
            aria-pressed="{{ $wishlisted ? 'true' : 'false' }}"
            aria-label="{{ $wishlisted ? __('messages.academy_wishlist_remove') : __('messages.academy_wishlist_add') }}"
            title="{{ $wishlisted ? __('messages.academy_wishlist_remove') : __('messages.academy_wishlist_add') }}">
            <i class="{{ $wishlisted ? 'fas' : 'far' }} fa-heart" aria-hidden="true"></i>
        </button>
    </div>
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
        @if($showApplyDeadline)
        <div class="soni-apply-by {{ $registrationClosed ? 'is-ended' : '' }}" title="{{ __('messages.academy_apply_until') }}: {{ $applyUntilLabel }}">
            <i class="fas fa-calendar-day" aria-hidden="true"></i>
            <span class="soni-apply-by__label">{{ __('messages.academy_apply_until') }}</span>
            <strong>{{ $applyUntilLabel }}</strong>
        </div>
        @endif
        <div class="soni-card-footer">
            <div class="soni-card-actions {{ ($showPrimary || $applyEndedBadge) ? '' : 'is-single' }}">
                @if($applyEndedBadge)
                <span class="soni-apply-ended" tabindex="0" role="status"
                    aria-label="{{ __('messages.academy_apply_ended') }}. {{ __('messages.academy_apply_ended_hint') }}">
                    <span class="soni-apply-ended__label">
                        <i class="fas fa-ban" aria-hidden="true"></i>
                        {{ __('messages.academy_apply_ended') }}
                    </span>
                    <span class="soni-apply-ended__tip" role="tooltip">{{ __('messages.academy_apply_ended_hint') }}</span>
                </span>
                @elseif($showPrimary)
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
