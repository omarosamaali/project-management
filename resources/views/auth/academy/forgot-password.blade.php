@extends('layouts.user')

@section('title', 'نسيت كلمة المرور')

@section('content')
@include('auth.partials.academy-auth-styles')
<div class="academy-auth-page">
    <div class="academy-auth-shell academy-auth-shell--narrow">
        <div class="academy-auth-form">
            <div class="academy-auth-form__head">
                <span class="academy-auth-kicker" style="color:#0b8f7f;">{{ __('messages.academy') }}</span>
                <h1>نسيت كلمة المرور</h1>
                <p>أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.</p>
            </div>

            <x-auth-session-status class="academy-auth-alert academy-auth-alert--ok" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="ui" value="academy">

                <div>
                    <x-input-label for="email" :value="__('messages.email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <button type="submit" class="academy-auth-submit">
                    إرسال رابط إعادة التعيين
                </button>
            </form>

            <div class="academy-auth-foot">
                <a href="{{ \App\Support\AuthUi::loginUrl(['ui' => 'academy']) }}" class="academy-auth-link">{{ __('messages.login_here') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
