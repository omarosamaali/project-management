@extends('layouts.user')

@section('title', __('messages.academy_trainers_list_title') . ' — ' . __('messages.academy'))

@section('content')
@include('academy.partials.styles')

<div class="academy-page">
    <section class="academy-section">
        <div class="academy-sec-head">
            <div>
                <p class="academy-kicker">{{ __('messages.academy_trainers_kicker') }}</p>
                <h1 class="academy-h2 display">{{ __('messages.academy_trainers_list_title') }}</h1>
            </div>
            <p class="academy-sub">{{ __('messages.academy_trainers_list_sub') }}</p>
        </div>

        <form method="GET" action="{{ route('academy.trainers.index') }}" class="academy-search" style="margin-bottom:1.5rem;">
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('messages.academy_trainers_search') }}" aria-label="{{ __('messages.academy_trainers_search') }}">
            <button type="submit">{{ __('messages.academy_search') }}</button>
        </form>

        @if($trainers->isNotEmpty())
        <div class="trainer-grid">
            @foreach($trainers as $trainer)
                @include('academy.partials.trainer-card', ['trainer' => $trainer, 'locale' => $locale])
            @endforeach
        </div>
        @if($trainers->hasPages())
        <div class="academy-pagination" style="margin-top:2rem;">{{ $trainers->links() }}</div>
        @endif
        @else
        <div class="academy-empty">
            <p>{{ __('messages.academy_trainers_empty') }}</p>
            <a href="{{ route('academy.index') }}" class="academy-more-btn">{{ __('messages.academy') }}</a>
        </div>
        @endif
    </section>
</div>
@endsection
