@extends('layouts.app')

@section('title', __('messages.private_request_show_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $source = $privateRequest->sourceCourse;
    $courseName = $source
        ? ($locale === 'en' ? ($source->name_en ?: $source->name_ar) : $source->name_ar)
        : '—';
    $eventLabel = fn (string $action) => __('messages.private_event_'.$action) !== 'messages.private_event_'.$action
        ? __('messages.private_event_'.$action)
        : $action;

    $status = $privateRequest->status;
    $activeStep = match ($status) {
        \App\Models\PrivateCourseRequest::STATUS_PENDING_TRAINER => 2,
        \App\Models\PrivateCourseRequest::STATUS_DATES_PROPOSED,
        \App\Models\PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED => 3,
        \App\Models\PrivateCourseRequest::STATUS_AWAITING_PAYMENT => 4,
        \App\Models\PrivateCourseRequest::STATUS_PAID => 5,
        default => 1,
    };
    if (in_array($status, [
        \App\Models\PrivateCourseRequest::STATUS_REJECTED,
        \App\Models\PrivateCourseRequest::STATUS_EXPIRED_UNPAID,
        \App\Models\PrivateCourseRequest::STATUS_EXPIRED_BUSY,
        \App\Models\PrivateCourseRequest::STATUS_CANCELED_NO_MEETING,
        \App\Models\PrivateCourseRequest::STATUS_BLOCKED,
    ], true)) {
        $activeStep = 1;
    }

    $proposedStartLocal = $privateRequest->proposed_start_at
        ? $privateRequest->proposed_start_at->format('Y-m-d\TH:i')
        : '';
    $proposedEndLocal = $privateRequest->proposed_end_at
        ? $privateRequest->proposed_end_at->format('Y-m-d\TH:i')
        : '';
@endphp
@push('styles')
<style>
    .pr-page { max-width: 58rem; margin: 0 auto; display: grid; gap: 1.15rem; }
    .pr-hero {
        border-radius: 1.35rem; padding: 1.25rem 1.35rem; color: #fff;
        background:
            radial-gradient(420px 160px at 100% 0%, rgba(212,160,23,.28), transparent 55%),
            linear-gradient(125deg, #061525 0%, #0a2f45 55%, #0c3d48 100%);
        box-shadow: 0 18px 40px rgba(6,21,37,.18);
        display: flex; flex-wrap: wrap; justify-content: space-between; gap: 1rem; align-items: flex-start;
    }
    .pr-hero .eyebrow { margin: 0 0 .3rem; font-size: .75rem; color: rgba(255,255,255,.65); font-weight: 700; }
    .pr-hero h1 { margin: 0; font-size: clamp(1.15rem, 2.2vw, 1.55rem); font-weight: 800; font-family: var(--ac-display, inherit); }
    .pr-status {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .4rem .85rem; border-radius: 999px; font-size: .78rem; font-weight: 800;
        background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18); color: #fff;
    }
    .pr-card {
        background: #fff; border: 1px solid var(--ac-line, #d4e0ec); border-radius: 1.25rem;
        padding: 1.15rem 1.25rem; box-shadow: 0 10px 28px rgba(6,21,37,.05);
    }
    .pr-card h2 { margin: 0 0 1rem; font-size: 1.02rem; font-weight: 800; }
    .pr-stats { display: grid; gap: .75rem; grid-template-columns: repeat(auto-fit, minmax(11rem, 1fr)); }
    .pr-stat { background: #f8fafc; border: 1px solid var(--ac-line, #d4e0ec); border-radius: 1rem; padding: .85rem .95rem; }
    .pr-stat dt { font-size: .75rem; color: var(--ac-muted, #5a6d82); font-weight: 700; }
    .pr-stat dd { margin: .25rem 0 0; font-weight: 800; color: var(--ac-ink, #061525); font-size: .95rem; }
    .pr-flow-head h2 { margin: 0 0 .25rem; font-size: 1.02rem; font-weight: 800; }
    .pr-flow-head p { margin: 0 0 1rem; color: var(--ac-muted, #5a6d82); font-size: .86rem; line-height: 1.55; }
    .pr-flow-steps { list-style: none; margin: 0; padding: 0; display: grid; gap: .65rem; }
    .pr-flow-step {
        display: grid; grid-template-columns: auto 1fr; gap: .7rem; align-items: start;
        padding: .75rem .85rem; border-radius: .95rem; border: 1px solid var(--ac-line, #d4e0ec); background: #f8fafc;
    }
    .pr-flow-step.is-active { background: #e4f6f3; border-color: rgba(11,143,127,.35); }
    .pr-flow-step.is-done { background: #fff; }
    .pr-flow-badge {
        width: 2.2rem; height: 2.2rem; border-radius: 999px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #e2e8f0; color: #475569; font-size: .85rem;
    }
    .pr-flow-step.is-active .pr-flow-badge,
    .pr-flow-step.is-done .pr-flow-badge { background: var(--ac-teal, #0b8f7f); color: #fff; }
    .pr-flow-num { display: block; font-size: .68rem; font-weight: 700; color: var(--ac-muted, #5a6d82); }
    .pr-flow-copy strong { display: block; font-size: .9rem; }
    .pr-flow-copy p { margin: .2rem 0 0; font-size: .78rem; color: var(--ac-muted, #5a6d82); line-height: 1.5; }
    .pr-action-box {
        margin-top: 1rem; padding: 1rem; border-radius: 1rem;
        border: 1px solid rgba(11,143,127,.22); background: #f3fbf9;
    }
    .pr-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        padding: .7rem 1rem; border-radius: .8rem; font-size: .875rem; font-weight: 800; border: 0; cursor: pointer;
    }
    .pr-btn-primary { background: linear-gradient(135deg, var(--ac-teal, #0b8f7f), #0a7a6d); color: #fff; }
    .pr-btn-outline { background: #fff; color: var(--ac-teal, #0b8f7f); border: 1px solid rgba(11,143,127,.35); }
    .pr-btn-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .pr-timeline { position: relative; padding-inline-start: 1.15rem; border-inline-start: 2px solid #d8efe9; }
    .pr-timeline-item { position: relative; padding-bottom: 1.1rem; }
    .pr-timeline-item::before {
        content: ''; position: absolute; inset-inline-start: -1.35rem; top: .35rem;
        width: .6rem; height: .6rem; border-radius: 50%; background: var(--ac-teal, #0b8f7f);
    }
    .pr-ended {
        padding: .85rem 1rem; border-radius: .95rem; background: #fff7ed; border: 1px solid #fed7aa;
        color: #9a3412; font-size: .88rem; font-weight: 700;
    }
</style>
@endpush

<div class="pr-page">
    <div class="pr-hero">
        <div>
            <p class="eyebrow">#{{ $privateRequest->id }} · {{ __('messages.private_request_show_title') }}</p>
            <h1>{{ $courseName }}</h1>
        </div>
        <span class="pr-status"><i class="fas fa-info-circle"></i> {{ $privateRequest->statusLabel() }}</span>
    </div>

    @if(in_array($status, [
        \App\Models\PrivateCourseRequest::STATUS_REJECTED,
        \App\Models\PrivateCourseRequest::STATUS_EXPIRED_UNPAID,
        \App\Models\PrivateCourseRequest::STATUS_EXPIRED_BUSY,
        \App\Models\PrivateCourseRequest::STATUS_CANCELED_NO_MEETING,
        \App\Models\PrivateCourseRequest::STATUS_BLOCKED,
    ], true))
    <div class="pr-ended">
        <i class="fas fa-exclamation-triangle ml-1"></i>
        {{ $privateRequest->statusLabel() }}
        @if($privateRequest->rejection_reason) — {{ $privateRequest->rejection_reason }} @endif
        @if($privateRequest->block_reason) — {{ $privateRequest->block_reason }} @endif
    </div>
    @else
    <div class="pr-card">
        @include('course.partials.private-request-flow', ['activeStep' => $activeStep, 'locale' => $locale])
    </div>
    @endif

    <div class="pr-card">
        <h2>{{ $locale === 'ar' ? 'تفاصيل الطلب' : 'Request details' }}</h2>
        <dl class="pr-stats">
            <div class="pr-stat">
                <dt>{{ $locale === 'ar' ? 'السعر' : 'Price' }}</dt>
                <dd>
                    <span class="inline-flex items-center gap-1" dir="ltr">
                        <x-drhm-icon width="14" height="16" />
                        {{ number_format((float) $privateRequest->private_price, 2) }}
                    </span>
                </dd>
            </div>
            @if($privateRequest->trainer)
            <div class="pr-stat">
                <dt>{{ $locale === 'ar' ? 'المحاضر' : 'Trainer' }}</dt>
                <dd>{{ $privateRequest->trainer->name }}</dd>
            </div>
            @endif
            @if($privateRequest->proposed_start_at)
            <div class="pr-stat">
                <dt>{{ $locale === 'ar' ? 'المواعيد المقترحة' : 'Proposed dates' }}</dt>
                <dd>
                    {{ $privateRequest->proposed_start_at->format('Y-m-d H:i') }}
                    —
                    {{ $privateRequest->proposed_end_at?->format('Y-m-d H:i') }}
                </dd>
            </div>
            @endif
            @if($privateRequest->payment_due_at && $status === \App\Models\PrivateCourseRequest::STATUS_AWAITING_PAYMENT)
            <div class="pr-stat" style="background:#fff7ed;border-color:#fed7aa;">
                <dt style="color:#9a3412;">{{ $locale === 'ar' ? 'مهلة الدفع' : 'Payment due' }}</dt>
                <dd style="color:#9a3412;">{{ $privateRequest->payment_due_at->format('Y-m-d H:i') }}</dd>
            </div>
            @endif
        </dl>

        @if($isTrainee && $status === \App\Models\PrivateCourseRequest::STATUS_DATES_PROPOSED)
        <div class="pr-action-box space-y-3">
            <p class="text-sm font-bold text-slate-700 mb-2">{{ __('messages.private_flow_step_3_title') }}</p>
            <form method="POST" action="{{ route('private-requests.accept-dates', $privateRequest) }}">
                @csrf
                <button type="submit" class="pr-btn pr-btn-primary w-full sm:w-auto">
                    <i class="fas fa-check"></i> {{ __('messages.private_request_accept_dates') }}
                </button>
            </form>
            <details class="text-sm">
                <summary class="cursor-pointer font-bold text-teal-700">{{ __('messages.private_request_request_change') }}</summary>
                <form method="POST" action="{{ route('private-requests.request-date-change', $privateRequest) }}" class="mt-3 space-y-2">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">{{ __('messages.private_request_start_at') }}</label>
                        <input type="datetime-local" name="proposed_start_at" required
                            value="{{ old('proposed_start_at', $proposedStartLocal) }}"
                            class="w-full rounded-lg border px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">{{ __('messages.private_request_end_at') }}</label>
                        <input type="datetime-local" name="proposed_end_at" required
                            value="{{ old('proposed_end_at', $proposedEndLocal) }}"
                            class="w-full rounded-lg border px-3 py-2">
                    </div>
                    <textarea name="message" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="{{ $locale === 'ar' ? 'رسالة للمحاضر' : 'Message to trainer' }}"></textarea>
                    <button type="submit" class="pr-btn pr-btn-outline">{{ __('messages.private_request_request_change') }}</button>
                </form>
            </details>
        </div>
        @endif

        @if($isTrainee && $status === \App\Models\PrivateCourseRequest::STATUS_AWAITING_PAYMENT)
        <div class="pr-action-box">
            <p class="text-sm font-bold text-slate-700 mb-3">{{ __('messages.private_flow_step_4_body') }}</p>
            <button type="button" onclick="payPrivateRequest()" class="pr-btn pr-btn-primary w-full sm:w-auto">
                <i class="fas fa-credit-card"></i> {{ __('messages.private_request_pay_now') }}
            </button>
        </div>
        @endif

        @if(($isTrainer || $isAdmin) && in_array($status, [
            \App\Models\PrivateCourseRequest::STATUS_PENDING_TRAINER,
            \App\Models\PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
        ], true))
        <div class="pr-action-box space-y-3">
            <form method="POST" action="{{ route('dashboard.academy.private-requests.approve', $privateRequest) }}" class="space-y-2">
                @csrf
                <p class="font-bold text-sm text-gray-700">{{ __('messages.private_request_approve') }}</p>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">{{ __('messages.private_request_start_at') }}</label>
                    <input type="datetime-local" name="proposed_start_at" required
                        value="{{ old('proposed_start_at', $proposedStartLocal) }}"
                        class="w-full rounded-lg border px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">{{ __('messages.private_request_end_at') }}</label>
                    <input type="datetime-local" name="proposed_end_at" required
                        value="{{ old('proposed_end_at', $proposedEndLocal) }}"
                        class="w-full rounded-lg border px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">{{ __('messages.private_request_note') }}</label>
                    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2">{{ old('note') }}</textarea>
                </div>
                <button type="submit" class="pr-btn pr-btn-primary">{{ __('messages.private_request_approve') }}</button>
            </form>
            <form method="POST" action="{{ route('dashboard.academy.private-requests.reject', $privateRequest) }}" class="space-y-2">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">{{ __('messages.private_request_rejection_reason') }}</label>
                    <textarea name="rejection_reason" required rows="2" class="w-full rounded-lg border px-3 py-2">{{ old('rejection_reason') }}</textarea>
                </div>
                <button type="submit" class="pr-btn pr-btn-danger">{{ __('messages.private_request_reject') }}</button>
            </form>
        </div>
        @endif

        @if($isAdmin && !in_array($status, [\App\Models\PrivateCourseRequest::STATUS_PAID, \App\Models\PrivateCourseRequest::STATUS_BLOCKED], true))
        <div class="pr-action-box">
            <form method="POST" action="{{ route('dashboard.academy.private-requests.block', $privateRequest) }}" class="space-y-2">
                @csrf
                <textarea name="block_reason" required rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="{{ $locale === 'ar' ? 'سبب الإيقاف' : 'Block reason' }}"></textarea>
                <button type="submit" class="pr-btn pr-btn-danger">{{ __('messages.private_request_block') }}</button>
            </form>
        </div>
        @endif

        @if($status === \App\Models\PrivateCourseRequest::STATUS_PAID && $privateRequest->privateCourse)
        <div class="mt-4">
            <a href="{{ route('dashboard.my_courses.index') }}" class="pr-btn pr-btn-primary">
                <i class="fas fa-graduation-cap"></i> {{ $locale === 'ar' ? 'الذهاب لدوراتي' : 'Go to my courses' }}
            </a>
        </div>
        @endif
    </div>

    <div class="pr-card">
        <h2>{{ $locale === 'ar' ? 'سجل التحديثات' : 'Timeline' }}</h2>
        <div class="pr-timeline">
            @forelse($privateRequest->events as $event)
            <div class="pr-timeline-item">
                <p class="font-bold text-sm text-gray-900">{{ $eventLabel($event->action) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $event->created_at->format('Y-m-d H:i') }}
                    @if($event->actor) — {{ $event->actor->name }} @endif
                </p>
                @if($event->message)<p class="text-sm text-gray-600 mt-1">{{ $event->message }}</p>@endif
            </div>
            @empty
            <p class="text-sm text-gray-500">{{ $locale === 'ar' ? 'لا أحداث بعد' : 'No events yet' }}</p>
            @endforelse
        </div>
    </div>
</div>

<script>
async function payPrivateRequest() {
    const btn = event.currentTarget;
    btn.disabled = true;
    try {
        const res = await fetch(@json(route('private-requests.pay', $privateRequest)), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        });
        const data = await res.json();
        if (data.success && data.payment_url) {
            window.location.href = data.payment_url;
        } else {
            alert(data.message || 'Error');
            btn.disabled = false;
        }
    } catch (e) {
        alert('Connection error');
        btn.disabled = false;
    }
}
</script>
@endsection
