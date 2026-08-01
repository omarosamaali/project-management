@extends('layouts.user')

@section('title', __('messages.academy_wishlist_title') . ' — ' . __('messages.academy'))

@section('content')
@include('academy.partials.styles')

<x-hero-section variant="academy" />

<div class="academy-page" data-wishlist-page>
    <section class="academy-section reveal is-in" id="wishlist">
        <a href="{{ route('academy.courses') }}" class="academy-back">
            <i class="fas fa-arrow-{{ $locale === 'ar' ? 'right' : 'left' }}"></i>
            {{ __('messages.academy_view_all_courses') }}
        </a>

        <div class="academy-sec-head">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_wishlist_kicker') }}</p>
                <h1 class="academy-h2 display">{{ __('messages.academy_wishlist_title') }}</h1>
            </div>
            <p class="academy-sub">{{ __('messages.academy_wishlist_sub', ['count' => $courses->total()]) }}</p>
        </div>

        @if($courses->isEmpty())
        <div class="text-center py-14 border border-dashed border-[var(--line)] rounded-3xl bg-white/70">
            <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-[#fff1f2] text-[#e11d48] flex items-center justify-center text-xl">
                <i class="far fa-heart"></i>
            </div>
            <p class="font-bold text-lg mb-1">{{ __('messages.academy_wishlist_empty_title') }}</p>
            <p class="text-sm text-[var(--muted)] mb-5">{{ __('messages.academy_wishlist_empty_sub') }}</p>
            <a href="{{ route('academy.courses') }}" class="soni-btn-primary" style="display:inline-flex;width:auto;padding-inline:1.4rem;">
                {{ __('messages.academy_explore_courses') }}
            </a>
        </div>
        @else
        <div class="soni-grid" data-wishlist-grid>
            @foreach($courses as $course)
                @include('academy.partials.course-card', ['course' => $course, 'locale' => $locale])
            @endforeach
        </div>
        <div class="ac-pagination mt-8">{{ $courses->links() }}</div>
        @endif
    </section>
</div>

@include('academy.partials.wishlist-script')
@endsection
