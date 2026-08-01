@extends('layouts.user')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
@include('auth.partials.academy-auth-styles')
<div class="academy-auth-page">
    <div class="academy-auth-shell academy-auth-shell--narrow">
        <div class="academy-auth-form">
            <div class="academy-auth-form__head">
                <span class="academy-auth-kicker" style="color:#0b8f7f;">{{ __('messages.academy') }}</span>
                <h1>إعادة تعيين كلمة المرور</h1>
                <p>اختر كلمة مرور جديدة لحسابك.</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="ui" value="academy">

                <div>
                    <x-input-label for="email" :value="__('messages.email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('messages.password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <button type="submit" class="academy-auth-submit">
                    حفظ كلمة المرور الجديدة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
