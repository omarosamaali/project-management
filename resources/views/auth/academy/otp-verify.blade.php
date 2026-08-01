@extends('layouts.user')

@section('title', 'تأكيد البريد الإلكتروني')

@section('content')
<x-auth.academy-shell
    :narrow="true"
    visual-title="تأكيد بريدك"
    visual-subtitle="أدخل رمز التحقق المرسل إلى بريدك الإلكتروني لتفعيل حساب المتدرب."
    visual-badge="تحقق OTP">

    @if(session('success'))
    <div class="academy-auth-alert academy-auth-alert--ok" role="alert">
        <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
    </div>
    @endif

    <div class="academy-auth-form__head">
        <span class="academy-auth-kicker" style="color:#0b8f7f;">{{ __('messages.academy') }}</span>
        <h1>تأكيد البريد الإلكتروني</h1>
        <p>
            أرسلنا رمزاً مكوّناً من 4 أرقام إلى
            <strong dir="ltr">{{ auth()->user()->email }}</strong>
        </p>
    </div>

    @if(auth()->user()->email_verified_at)
    <div class="academy-auth-alert academy-auth-alert--ok text-center">
        تم تأكيد بريدك بنجاح ✅
    </div>
    <a href="{{ route('academy.index') }}" class="academy-auth-submit mt-4 inline-flex justify-center">
        متابعة إلى الأكاديمية
    </a>
    @else
    <form method="POST" action="{{ route('otp.email.check') }}" class="space-y-4">
        @csrf
        <div>
            <label class="academy-auth-label" for="otp">رمز التحقق</label>
            <input
                id="otp"
                type="text"
                name="otp"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="4"
                placeholder="0000"
                required
                class="text-center text-2xl tracking-[0.6em] font-bold"
                dir="ltr">
            @error('email_otp')
            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="academy-auth-submit w-full">تأكيد الرمز</button>
    </form>

    <div class="mt-4 text-center">
        <button
            type="button"
            id="resend-email"
            onclick="handleResendEmail()"
            class="academy-auth-link text-sm font-semibold">
            إعادة إرسال الكود <span id="timer-email"></span>
        </button>
    </div>
    @endif

    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
        @csrf
        <button type="submit" class="text-slate-400 hover:text-red-600 text-sm transition">
            {{ __('messages.logout') }}
        </button>
    </form>
</x-auth.academy-shell>

<script>
async function handleResendEmail() {
    const btn = document.getElementById('resend-email');
    const timerSpan = document.getElementById('timer-email');
    if (!btn) return;

    let timeLeft = 30;
    btn.disabled = true;

    try {
        const baseUrl = @json(route('otp.resend', ['type' => '__TYPE__']));
        const url = baseUrl.replace('__TYPE__', 'email');
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'تعذر الإرسال');
        }
        alert(result.message);
        const interval = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(interval);
                btn.disabled = false;
                timerSpan.textContent = '';
            } else {
                timerSpan.textContent = `(${timeLeft}ث)`;
                timeLeft--;
            }
        }, 1000);
    } catch (error) {
        alert('فشل الإرسال: ' + error.message);
        btn.disabled = false;
    }
}
</script>
@endsection
