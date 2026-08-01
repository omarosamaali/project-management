@extends('layouts.user')

@section('title', __('messages.login_title'))

@section('content')

<x-auth.academy-shell
    :visual-title="__('messages.welcome_back')"
    :visual-subtitle="__('messages.login_subtitle')">

    @if(session('success'))
    <div class="academy-auth-alert academy-auth-alert--ok" role="alert">
        <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error') || $errors->has('csrf'))
    <div class="academy-auth-alert academy-auth-alert--err" role="alert">
        <i class="fas fa-exclamation-circle ml-1"></i>{{ session('error') ?: $errors->first('csrf') }}
    </div>
    @endif

    <x-auth-session-status class="academy-auth-alert academy-auth-alert--ok" :status="session('status')" />

    <div class="academy-auth-form__head">
        <span class="academy-auth-kicker" style="color:#0b8f7f;">{{ __('messages.academy') }}</span>
        <h1>{{ __('messages.login_title') }}</h1>
        <p>{{ __('messages.login_description') }}</p>
    </div>

    <form class="space-y-5" method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="ui" value="academy">

        <div>
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('messages.password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3 flex-wrap">
            <label class="flex items-center gap-2 cursor-pointer">
                <input name="remember" id="remember_me" type="checkbox">
                <span class="text-sm text-gray-600">{{ __('messages.remember_me') }}</span>
            </label>
            @if (Route::has('password.request'))
            <a href="{{ \App\Support\AuthUi::passwordRequestUrl(['ui' => 'academy']) }}" class="academy-auth-link text-sm">
                {{ __('messages.forgot_password') }}
            </a>
            @endif
        </div>

        <button type="submit" class="academy-auth-submit">
            <i class="fas fa-sign-in-alt"></i>
            {{ __('messages.login_button') }}
        </button>
    </form>

    <div class="academy-auth-foot">
        <p>
            {{ __('messages.no_account') }}
            <a href="{{ \App\Support\AuthUi::registerUrl(['ui' => 'academy']) }}" class="academy-auth-link">
                {{ __('messages.register_now') }}
            </a>
        </p>
    </div>
</x-auth.academy-shell>

@endsection
