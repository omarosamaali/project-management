@props([
    'visualTitle' => null,
    'visualSubtitle' => null,
    'visualBadge' => null,
    'narrow' => false,
    'wide' => false,
])

@php
    $visualTitle = $visualTitle ?? __('messages.welcome_back');
    $visualSubtitle = $visualSubtitle ?? __('messages.login_subtitle');
    $visualBadge = $visualBadge ?? __('messages.academy');
    $heroImage = \App\Models\Setting::academyHeroImageUrl();
    $shellClass = 'academy-auth-shell';
    if ($narrow) {
        $shellClass .= ' academy-auth-shell--narrow';
    }
    if ($wide) {
        $shellClass .= ' academy-auth-shell--wide';
    }
@endphp

@include('auth.partials.academy-auth-styles')

<div class="academy-auth-page">
    <div class="{{ $shellClass }}">
        @unless($narrow)
        <aside class="academy-auth-visual" aria-hidden="true">
            <img class="academy-auth-visual__bg" src="{{ $heroImage }}" alt="">
            <div class="academy-auth-visual__veil"></div>
            <div class="academy-auth-visual__content">
                <span class="academy-auth-visual__badge">
                    <i class="fas fa-graduation-cap"></i>
                    {{ $visualBadge }}
                </span>
                <span class="academy-auth-kicker">Evorq Academy</span>
                <h2>{{ $visualTitle }}</h2>
                <p>{{ $visualSubtitle }}</p>
            </div>
        </aside>
        @endunless

        <div class="academy-auth-form">
            {{ $slot }}
        </div>
    </div>
</div>
