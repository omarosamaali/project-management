@extends('layouts.user')

@section('title', __('messages.login_title'))

@section('content')

<x-auth-session-status class="mb-4" :status="session('status')" />

@if(session('success'))
<div class="mx-auto max-w-4xl mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
    <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
</div>
@endif

@if(session('error') || $errors->has('csrf'))
<div class="mx-auto max-w-4xl mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
    <i class="fas fa-exclamation-circle ml-1"></i>{{ session('error') ?: $errors->first('csrf') }}
</div>
@endif

<div class="my-10 mx-auto max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden grid md:grid-cols-2">

    <div class="hidden md:block relative">
        <img src="{{ asset('assets/images/login.png') }}"
            alt="{{ __('messages.login_title') }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 flex items-center justify-center p-8">
            <div class="text-white text-center">
                <i class="fas fa-box-open text-8xl mb-6"></i>
                <h2 class="text-4xl font-bold mb-4">{{ __('messages.welcome_back') }}</h2>
                <p class="text-xl">{{ __('messages.login_subtitle') }}</p>
            </div>
        </div>
    </div>

    <div class="p-8 md:p-12">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-800 mb-3 ltr:text-left rtl:text-right">
                {{ __('messages.login_title') }}
            </h1>
            <p class="text-gray-600 ltr:text-left rtl:text-right">
                {{ __('messages.login_description') }}
            </p>
        </div>

        <form class="space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="ui" value="classic">
            @if(request('redirect'))
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

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

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input name="remember" id="remember_me" type="checkbox"
                        class="w-5 h-5 text-black rounded focus:ring-black">
                    <span class="text-sm text-gray-600">{{ __('messages.remember_me') }}</span>
                </label>
                <a href="{{ \App\Support\AuthUi::passwordRequestUrl(['ui' => 'classic']) }}" class="text-black hover:text-black font-semibold">
                    {{ __('messages.forgot_password') }}
                </a>
            </div>

            <button type="submit" class="w-full bg-black text-white py-4 rounded-lg font-bold text-lg
                 hover:bg-gray-800 transition-all shadow-lg hover:shadow-xl">
                {{ __('messages.login_button') }}
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-gray-600">
                {{ __('messages.no_account') }}
                <a href="{{ \App\Support\AuthUi::registerUrl(['ui' => 'classic']) }}" class="text-black hover:text-black font-bold">
                    {{ __('messages.register_now') }}
                </a>
            </p>
        </div>
    </div>
</div>

@endsection
