@extends('layouts.user')

@section('title', __('messages.academy_all_courses_title') . ' — ' . __('messages.academy'))

@section('content')
@include('academy.partials.styles')

<x-hero-section variant="academy" />

@php
    $filterParams = function (array $extra = []) use ($q, $type, $price, $activeCategory) {
        $params = [];
        if ($q !== '') {
            $params['q'] = $q;
        }
        if ($type !== '') {
            $params['type'] = $type;
        }
        if ($price !== '') {
            $params['price'] = $price;
        }

        if (array_key_exists('category', $extra)) {
            if ($extra['category']) {
                $params['category'] = $extra['category'];
            }
        } elseif ($activeCategory) {
            $params['category'] = $activeCategory->id;
        }

        foreach ($extra as $key => $value) {
            if ($key === 'category') {
                continue;
            }
            if ($value === null || $value === '') {
                unset($params[$key]);
            } else {
                $params[$key] = $value;
            }
        }

        return $params;
    };
@endphp

<div class="academy-page">
    <section class="academy-section reveal is-in" id="all-courses" data-academy-listing>
        <a href="{{ route('academy.index') }}" class="academy-back">
            <i class="fas fa-arrow-{{ $locale === 'ar' ? 'right' : 'left' }}"></i>
            {{ __('messages.academy_back_home') }}
        </a>

        <p class="academy-kicker">{{ __('messages.academy_all_courses_kicker') }}</p>
        <h1 class="academy-h2 display">
            {{ $activeCategory ? $activeCategory->title($locale) : __('messages.academy_all_courses_title') }}
        </h1>
        <p class="academy-sub">
            {{ __('messages.academy_all_courses_sub', ['count' => $courses->total()]) }}
        </p>

        @if($categories->isNotEmpty())
        <div class="snap-slider-wrap mt-6" data-snap-slider-wrap data-autoplay="0" data-natural-slides="1">
            <button type="button" class="snap-nav prev" data-snap-prev aria-label="{{ __('messages.academy_prev') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'right' : 'left' }}"></i></button>
            <button type="button" class="snap-nav next" data-snap-next aria-label="{{ __('messages.academy_next') }}"><i class="fas fa-chevron-{{ $locale === 'ar' ? 'left' : 'right' }}"></i></button>
            <div class="snap-slider-viewport">
                <div class="snap-slider" data-snap-slider>
                    <a href="{{ route('academy.courses', $filterParams(['category' => null])) }}"
                        class="snap-slide cat-slide cat-slide-all {{ ! $activeCategory ? 'is-active' : '' }}">
                        <span>
                            <span class="cat-slide-title">{{ __('messages.academy_all_categories') }}</span>
                        </span>
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('academy.courses', $filterParams(['category' => $cat->id])) }}"
                        class="snap-slide cat-slide cat-tone-{{ $loop->index % 6 }} {{ $activeCategory && $activeCategory->id === $cat->id ? 'is-active' : '' }}">
                        <img src="{{ $cat->iconUrl() }}" alt="">
                        <span>
                            <span class="cat-slide-title">{{ $cat->title($locale) }}</span>
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="academy-toolbar mt-6">
            <form method="GET" action="{{ route('academy.courses') }}" class="academy-search" style="margin-bottom:0">
                @if($activeCategory)
                <input type="hidden" name="category" value="{{ $activeCategory->id }}">
                @endif
                @if($type !== '')
                <input type="hidden" name="type" value="{{ $type }}">
                @endif
                @if($price !== '')
                <input type="hidden" name="price" value="{{ $price }}">
                @endif
                <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('messages.academy_search_placeholder') }}" aria-label="{{ __('messages.academy_search_placeholder') }}">
                <button type="submit">{{ __('messages.academy_search') }}</button>
            </form>

            <div class="academy-filters" style="margin-bottom:0">
                @php
                    $typeUrl = function (?string $t) use ($filterParams) {
                        $params = $filterParams();
                        unset($params['type']);
                        if ($t) {
                            $params['type'] = $t;
                        }

                        return route('academy.courses', $params);
                    };
                    $priceUrl = function (?string $p) use ($filterParams) {
                        $params = $filterParams();
                        unset($params['price']);
                        if ($p) {
                            $params['price'] = $p;
                        }

                        return route('academy.courses', $params);
                    };
                @endphp

                <a href="{{ $typeUrl(null) }}" class="academy-chip {{ $type === '' ? 'is-active' : '' }}">{{ __('messages.academy_filter_all_types') }}</a>
                <a href="{{ $typeUrl('online') }}" class="academy-chip {{ $type === 'online' ? 'is-active' : '' }}">{{ __('messages.academy_type_online') }}</a>
                <a href="{{ $typeUrl('recorded') }}" class="academy-chip {{ $type === 'recorded' ? 'is-active' : '' }}">{{ __('messages.academy_type_recorded') }}</a>
                <a href="{{ $typeUrl('on_site') }}" class="academy-chip {{ $type === 'on_site' ? 'is-active' : '' }}">{{ __('messages.academy_type_onsite') }}</a>

                <span class="w-px h-6 bg-[var(--line)] self-center mx-1 hidden sm:inline-block" aria-hidden="true"></span>

                <a href="{{ $priceUrl(null) }}" class="academy-chip {{ $price === '' ? 'is-active' : '' }}">{{ __('messages.academy_filter_all_prices') }}</a>
                <a href="{{ $priceUrl('free') }}" class="academy-chip {{ $price === 'free' ? 'is-active' : '' }}">{{ __('messages.academy_free') }}</a>
                <a href="{{ $priceUrl('paid') }}" class="academy-chip {{ $price === 'paid' ? 'is-active' : '' }}">{{ __('messages.academy_filter_paid') }}</a>
            </div>
        </div>

        @if($courses->isEmpty())
        <div class="text-center py-16 border border-dashed border-[var(--line)] rounded-2xl bg-white/60">
            <p class="font-bold text-lg mb-1">{{ __('messages.academy_all_courses_empty_title') }}</p>
            <p class="text-sm text-[var(--muted)] mb-4">{{ __('messages.academy_all_courses_empty_sub') }}</p>
            <a href="{{ route('academy.courses') }}" class="academy-chip is-active">{{ __('messages.academy_clear_filters') }}</a>
        </div>
        @else
        <div class="soni-grid">
            @foreach($courses as $course)
                @include('academy.partials.course-card', ['course' => $course, 'locale' => $locale])
            @endforeach
        </div>
        <div class="mt-8">{{ $courses->links() }}</div>
        @endif
    </section>
</div>

@include('academy.partials.snap-slider-script')
@include('academy.partials.interactions-script')
@include('academy.partials.wishlist-script')
@include('academy.partials.filter-ajax-script')
@endsection
