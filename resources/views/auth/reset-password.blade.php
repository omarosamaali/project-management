@extends('layouts.user')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="my-10 mx-auto max-w-lg w-[96%] bg-white rounded-xl shadow-2xl overflow-hidden p-8 md:p-10">
    <h1 class="text-2xl font-bold text-gray-800 mb-3">إعادة تعيين كلمة المرور</h1>
    <p class="text-sm text-gray-600 mb-6">اختر كلمة مرور جديدة لحسابك.</p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="ui" value="classic">

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

        <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-bold hover:bg-gray-800 transition">
            حفظ كلمة المرور الجديدة
        </button>
    </form>
</div>
@endsection
