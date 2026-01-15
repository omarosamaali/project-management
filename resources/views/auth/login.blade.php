@extends('layouts.user')

@section('title', 'تسجيل الدخول')

@section('content')

<x-auth-session-status class="mb-4" :status="session('status')" />

<div class="my-10 mx-auto max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden grid md:grid-cols-2">

    <!-- جانب الصورة -->
    <div class="hidden md:block relative">
        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=800&fit=crop"
            alt="{{ __('messages.login_title') }}" class="w-full h-full object-cover">
        <div
            class="absolute inset-0 bg-gradient-to-br from-red-600/90 to-green-600/90 flex items-center justify-center p-8">
            <div class="text-white text-center">
                <i class="fas fa-box-open text-8xl mb-6"></i>
                <h2 class="text-4xl font-bold mb-4">{{ __('messages.welcome_back') }}</h2>
                <p class="text-xl">{{ __('messages.login_subtitle') }}</p>
            </div>
        </div>
    </div>

    <!-- جانب النموذج -->
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

            <!-- البريد الإلكتروني -->
            <div>
                <x-input-label for="email" :value="__('messages.email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- كلمة المرور -->
            <div>
                <x-input-label for="password" :value="__('messages.password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- تذكرني + نسيت كلمة المرور -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input name="remember" id="remember_me" type="checkbox"
                        class="w-5 h-5 text-black rounded focus:ring-red-500">
                    <span class="text-sm text-gray-600">{{ __('messages.remember_me') }}</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-black hover:text-red-800 font-semibold">
                    {{ __('messages.forgot_password') }}
                </a>
            </div>

            <!-- زر تسجيل الدخول -->
            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-lg font-bold text-lg 
                hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl">
                {{ __('messages.login_button') }}
            </button>
        </form>

        <!-- ليس لديك حساب؟ -->
        <div class="mt-10 text-center">
            <p class="text-gray-600">
                {{ __('messages.no_account') }}
                <a href="{{ route('register') }}" class="text-black hover:text-red-800 font-bold">
                    {{ __('messages.register_now') }}
                </a>
            </p>
        </div>
    </div>
</div>

@endsection