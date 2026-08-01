<x-guest-layout>
    <div class="academy-auth-form__head">
        <span class="academy-auth-kicker" style="color:#0b8f7f;">{{ __('messages.academy') }}</span>
        <h1>تأكيد كلمة المرور</h1>
        <p>هذه منطقة آمنة. يرجى تأكيد كلمة المرور للمتابعة.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('messages.password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="academy-auth-submit">
            تأكيد
        </button>
    </form>
</x-guest-layout>
