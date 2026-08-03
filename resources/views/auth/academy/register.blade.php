@extends('layouts.user')

@section('title', __('messages.register_title'))

@section('content')
<style>
    .select2-container, .iti { width: 100%; }
</style>

<x-auth.academy-shell
    :wide="true"
    :visual-title="__('messages.create_account')"
    :visual-subtitle="__('messages.register_subtitle')">

    <div class="academy-auth-form__head">
        <span class="academy-auth-kicker" style="color:#0b8f7f;">{{ __('messages.academy') }}</span>
        <h1>{{ __('messages.register_title') }}</h1>
        <p>{{ __('messages.register_description') }}</p>
    </div>

    <form class="space-y-5" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ui" value="academy">
        @if(request('redirect'))
        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        @if($errors->any())
        <div class="academy-auth-alert academy-auth-alert--err" role="alert">
            <div class="flex items-center gap-2 mb-2 font-bold">
                <i class="fas fa-exclamation-circle"></i>
                <span>تعذر إنشاء الحساب — يرجى تصحيح الأخطاء التالية:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <input type="hidden" name="account_type" value="personal">
        <input type="hidden" name="role" value="trainee">

        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 leading-relaxed">
            {{ __('messages.become_trainer_trainee_register_hint') }}
            <a href="{{ route('academy.become-trainer') }}" class="academy-auth-link font-bold underline underline-offset-2">
                {{ __('messages.become_trainer_nav') }}
            </a>
        </div>

        <div>
            <x-input-label for="name" :value="__('messages.name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('messages.phone')" />
            <x-text-input id="phone" class="placeholder-gray-500 block mt-1 w-full rtl:text-right" type="tel"
                name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="country-select2-host">
            <x-input-label for="country_select2" :value="__('messages.country')" />
            <select id="country_select2" name="country" class="block mt-1 w-full rtl:text-right" required>
                <option value="" disabled selected>اختر دولتك</option>
            </select>
            <x-input-error :messages="$errors->get('country')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('messages.password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="academy-auth-submit">
            <i class="fas fa-user-plus"></i>
            {{ __('messages.register_button') }}
        </button>
    </form>

    <div class="academy-auth-foot">
        <p>
            {{ __('messages.already_registered') }}
            <a href="{{ \App\Support\AuthUi::loginUrl(array_filter(['ui' => 'academy', 'redirect' => request('redirect')])) }}" class="academy-auth-link">
                {{ __('messages.login_here') }}
            </a>
        </p>
    </div>
</x-auth.academy-shell>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('partials.country-select2', [
    'selector' => '#country_select2',
    'oldCountry' => old('country', ''),
    'variant' => 'academy',
])
@endsection
