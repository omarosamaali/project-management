@extends('layouts.user')

@section('title', $category->title($locale) . ' — ' . __('messages.academy'))

@section('content')
@include('academy.partials.styles')

<x-hero-section variant="academy" />

<div class="academy-page">
    <section class="academy-section reveal is-in" id="category-courses" data-academy-listing>
        <a href="{{ route('academy.index') }}" class="academy-back">
            <i class="fas fa-arrow-{{ $locale === 'ar' ? 'right' : 'left' }}"></i>
            {{ __('messages.academy_back_home') }}
        </a>

        <div class="flex items-start gap-3 mb-2">
            <img src="{{ $category->iconUrl() }}" alt="" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_category_kicker') }}</p>
                <h1 class="academy-h2 display">{{ $category->title($locale) }}</h1>
                <p class="academy-sub" style="margin-bottom:0">
                    {{ __('messages.academy_category_sub', ['count' => $courses->total()]) }}
                </p>
            </div>
        </div>

        <div class="academy-toolbar mt-6">
            <form method="GET" action="{{ route('academy.category', $category) }}" class="academy-search" style="margin-bottom:0">
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
                    $typeUrl = function (?string $t) use ($category, $q, $price) {
                        $params = ['category' => $category];
                        if ($q !== '') {
                            $params['q'] = $q;
                        }
                        if ($price !== '') {
                            $params['price'] = $price;
                        }
                        if ($t) {
                            $params['type'] = $t;
                        }

                        return route('academy.category', $params);
                    };
                    $priceUrl = function (?string $p) use ($category, $q, $type) {
                        $params = ['category' => $category];
                        if ($q !== '') {
                            $params['q'] = $q;
                        }
                        if ($type !== '') {
                            $params['type'] = $type;
                        }
                        if ($p) {
                            $params['price'] = $p;
                        }

                        return route('academy.category', $params);
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
            <p class="font-bold text-lg mb-1">{{ __('messages.academy_category_empty_title') }}</p>
            <p class="text-sm text-[var(--muted)] mb-4">{{ __('messages.academy_category_empty_sub') }}</p>
            <a href="{{ route('academy.category', $category) }}" class="academy-chip is-active">{{ __('messages.academy_clear_filters') }}</a>
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

@include('academy.partials.wishlist-script')
@include('academy.partials.filter-ajax-script')
@endsection
