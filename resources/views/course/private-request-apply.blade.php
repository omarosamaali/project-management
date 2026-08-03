@extends('layouts.app')

@section('title', __('messages.private_request_apply_title'))

@section('content')
@php
    $locale = app()->getLocale();
@endphp
@push('styles')
<style>
    .pr-page { max-width: 58rem; margin: 0 auto; display: grid; gap: 1.25rem; }
    .pr-hero {
        border-radius: 1.35rem;
        padding: 1.35rem 1.4rem;
        color: #fff;
        background:
            radial-gradient(420px 160px at 100% 0%, rgba(212,160,23,.28), transparent 55%),
            linear-gradient(125deg, #061525 0%, #0a2f45 55%, #0c3d48 100%);
        box-shadow: 0 18px 40px rgba(6,21,37,.18);
    }
    .pr-hero .eyebrow {
        margin: 0 0 .35rem; font-size: .78rem; font-weight: 700; color: rgba(255,255,255,.65);
    }
    .pr-hero h1 {
        margin: 0; font-family: var(--ac-display, inherit); font-size: clamp(1.25rem, 2.4vw, 1.7rem); font-weight: 800;
    }
    .pr-hero .meta {
        margin: .65rem 0 0; display: flex; flex-wrap: wrap; gap: .55rem;
    }
    .pr-chip {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .35rem .7rem; border-radius: 999px; font-size: .78rem; font-weight: 700;
        background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.14);
    }
    .pr-grid {
        display: grid; gap: 1.15rem;
    }
    @media (min-width: 900px) {
        .pr-grid { grid-template-columns: 1.05fr .95fr; align-items: start; }
    }
    .pr-card {
        background: #fff;
        border: 1px solid var(--ac-line, #d4e0ec);
        border-radius: 1.25rem;
        padding: 1.2rem 1.25rem;
        box-shadow: 0 10px 28px rgba(6,21,37,.05);
    }
    .pr-card h2 {
        margin: 0 0 .85rem; font-size: 1.05rem; font-weight: 800; color: var(--ac-ink, #061525);
        font-family: var(--ac-display, inherit);
    }
    .pr-flow-head h2 { margin: 0 0 .25rem; font-size: 1.05rem; font-weight: 800; }
    .pr-flow-head p { margin: 0 0 1rem; color: var(--ac-muted, #5a6d82); font-size: .88rem; line-height: 1.6; }
    .pr-flow-steps { list-style: none; margin: 0; padding: 0; display: grid; gap: .75rem; }
    .pr-flow-step {
        display: grid; grid-template-columns: auto 1fr; gap: .75rem; align-items: start;
        padding: .85rem .9rem; border-radius: 1rem; border: 1px solid var(--ac-line, #d4e0ec);
        background: #f8fafc;
    }
    .pr-flow-step.is-active {
        background: #e4f6f3; border-color: rgba(11,143,127,.35);
        box-shadow: 0 0 0 1px rgba(11,143,127,.12);
    }
    .pr-flow-step.is-done { background: #fff; opacity: .92; }
    .pr-flow-badge {
        width: 2.35rem; height: 2.35rem; border-radius: 999px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #e2e8f0; color: #475569; font-size: .9rem; flex-shrink: 0;
    }
    .pr-flow-step.is-active .pr-flow-badge { background: var(--ac-teal, #0b8f7f); color: #fff; }
    .pr-flow-step.is-done .pr-flow-badge { background: #0b8f7f; color: #fff; }
    .pr-flow-num { display: block; font-size: .7rem; font-weight: 700; color: var(--ac-muted, #5a6d82); margin-bottom: .15rem; }
    .pr-flow-copy strong { display: block; font-size: .92rem; color: var(--ac-ink, #061525); }
    .pr-flow-copy p { margin: .25rem 0 0; font-size: .8rem; color: var(--ac-muted, #5a6d82); line-height: 1.55; }
    .pr-terms {
        display: grid; gap: .55rem; margin: 0 0 1rem; padding: 0; list-style: none;
    }
    .pr-terms li {
        display: grid; grid-template-columns: auto 1fr; gap: .55rem; align-items: start;
        font-size: .86rem; color: #334155; line-height: 1.55;
    }
    .pr-terms i { color: var(--ac-teal, #0b8f7f); margin-top: .2rem; }
    .pr-check {
        display: flex; align-items: flex-start; gap: .65rem;
        padding: .85rem .9rem; border-radius: .9rem; border: 1px solid var(--ac-line, #d4e0ec);
        background: #f8fafc; font-size: .88rem; color: #334155; cursor: pointer;
    }
    .pr-check input { margin-top: .2rem; accent-color: var(--ac-teal, #0b8f7f); width: 1.05rem; height: 1.05rem; }
    .pr-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
        width: 100%; padding: .9rem 1.1rem; border-radius: .95rem; border: 0;
        font-weight: 800; color: #fff; cursor: pointer;
        background: linear-gradient(135deg, var(--ac-teal, #0b8f7f), #0a7a6d);
        box-shadow: 0 12px 28px rgba(11,143,127,.25);
    }
    .pr-btn:disabled { opacity: .45; cursor: not-allowed; box-shadow: none; }
    .pr-btn-ghost {
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        margin-top: .75rem; width: 100%; padding: .7rem; border-radius: .85rem;
        border: 1px solid var(--ac-line, #d4e0ec); background: #fff; color: var(--ac-muted, #5a6d82);
        font-weight: 700; text-decoration: none; font-size: .88rem;
    }
    .pr-btn-ghost:hover { color: var(--ac-teal, #0b8f7f); border-color: rgba(11,143,127,.35); }
    textarea.pr-input {
        width: 100%; border-radius: .9rem; border: 1px solid var(--ac-line, #d4e0ec);
        padding: .75rem .9rem; font-size: .9rem; min-height: 6.5rem; resize: vertical;
        background: #fff;
    }
    textarea.pr-input:focus {
        outline: none; border-color: var(--ac-teal, #0b8f7f);
        box-shadow: 0 0 0 3px rgba(11,143,127,.15);
    }
</style>
@endpush

<div class="pr-page">
    <div class="pr-hero">
        <p class="eyebrow">{{ __('messages.private_request_apply_title') }}</p>
        <h1>{{ $courseName }}</h1>
        <div class="meta">
            @if($course->trainer)
            <span class="pr-chip"><i class="fas fa-chalkboard-teacher"></i> {{ $course->trainer->name }}</span>
            @endif
            <span class="pr-chip"><i class="fas fa-tag"></i>
                <span class="inline-flex items-center gap-1" dir="ltr">
                    <x-drhm-icon width="12" height="14" color="#ffffff" />
                    {{ number_format((float) $privatePrice, 2) }}
                </span>
            </span>
            <span class="pr-chip"><i class="fas fa-user"></i> {{ __('messages.private_flow_one_to_one') }}</span>
        </div>
    </div>

    <div class="pr-grid">
        <div class="pr-card">
            @include('course.partials.private-request-flow', ['activeStep' => 1, 'locale' => $locale])
        </div>

        <div class="pr-card">
            <h2>{{ __('messages.private_request_terms_title') }}</h2>
            <ul class="pr-terms">
                <li><i class="fas fa-check-circle"></i><span>{{ __('messages.private_request_term_1') }}</span></li>
                <li><i class="fas fa-check-circle"></i><span>{{ __('messages.private_request_term_2') }}</span></li>
                <li><i class="fas fa-check-circle"></i><span>{{ __('messages.private_request_term_3') }}</span></li>
                <li><i class="fas fa-check-circle"></i><span>{{ __('messages.private_request_term_4') }}</span></li>
                <li><i class="fas fa-check-circle"></i><span>{{ __('messages.private_request_term_5') }}</span></li>
                <li><i class="fas fa-check-circle"></i><span>{{ __('messages.private_request_term_6') }}</span></li>
            </ul>

            <form method="POST" action="{{ route('courses.private-request.store', $course) }}" class="space-y-3" id="privateRequestForm">
                @csrf
                <div>
                    <label for="trainee_note" class="block text-sm font-bold text-slate-700 mb-2">
                        {{ __('messages.private_request_note_label') }}
                    </label>
                    <textarea name="trainee_note" id="trainee_note" class="pr-input"
                        placeholder="{{ $locale === 'ar' ? 'مثال: أفضل مواعيد مسائية...' : 'e.g. evening slots preferred...' }}">{{ old('trainee_note') }}</textarea>
                    @error('trainee_note')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <label class="pr-check">
                    <input type="checkbox" name="accept_terms" value="1" id="accept_terms" {{ old('accept_terms') ? 'checked' : '' }} required>
                    <span>{{ __('messages.private_request_terms_accept') }}</span>
                </label>
                @error('accept_terms')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror

                <button type="submit" class="pr-btn" id="submitPrivateRequest" disabled>
                    <i class="fas fa-paper-plane"></i>
                    {{ __('messages.private_request_submit') }}
                </button>
            </form>

            <a href="{{ route('courses.show', $course) }}" class="pr-btn-ghost">
                <i class="fas fa-arrow-{{ $locale === 'ar' ? 'left' : 'right' }}"></i>
                {{ $locale === 'ar' ? 'العودة للدورة' : 'Back to course' }}
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    const box = document.getElementById('accept_terms');
    const btn = document.getElementById('submitPrivateRequest');
    if (!box || !btn) return;
    const sync = () => { btn.disabled = !box.checked; };
    box.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
