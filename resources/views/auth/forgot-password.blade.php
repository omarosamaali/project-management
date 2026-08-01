@extends('layouts.user')

@section('title', 'نسيت كلمة المرور')

@section('content')
<div class="my-10 mx-auto max-w-lg w-[96%] bg-white rounded-xl shadow-2xl overflow-hidden p-8 md:p-10">
    <h1 class="text-2xl font-bold text-gray-800 mb-3">نسيت كلمة المرور</h1>
    <p class="text-sm text-gray-600 mb-6">
        أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="ui" value="classic">

        <div>
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-bold hover:bg-gray-800 transition">
            إرسال رابط إعادة التعيين
        </button>
    </form>

    <div class="mt-8 text-center">
        <a href="{{ \App\Support\AuthUi::loginUrl(['ui' => 'classic']) }}" class="text-black font-semibold hover:underline">
            {{ __('messages.login_here') }}
        </a>
    </div>
</div>
@endsection
