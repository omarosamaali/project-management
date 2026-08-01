<x-guest-layout>
    <div class="academy-auth-form__head">
        <span class="academy-auth-kicker" style="color:#0b8f7f;">{{ __('messages.academy') }}</span>
        <h1>تأكيد البريد الإلكتروني</h1>
        <p>
            شكراً لتسجيلك! يرجى تأكيد بريدك عبر الرابط الذي أرسلناه إليك.
            إن لم تصلك الرسالة يمكننا إرسال رابط جديد.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="academy-auth-alert academy-auth-alert--ok">
            تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني.
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="academy-auth-submit">
                إعادة إرسال رابط التحقق
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="academy-auth-link text-sm">
                {{ __('messages.logout') }}
            </button>
        </form>
    </div>
</x-guest-layout>
