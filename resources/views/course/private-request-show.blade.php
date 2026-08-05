@extends('layouts.app')

@section('title', __('messages.private_request_show_title'))

@section('content')
@php
    use App\Models\PrivateCourseRequest;

    $locale = app()->getLocale();
    $source = $privateRequest->sourceCourse;
    $courseName = $source
        ? ($locale === 'en' ? ($source->name_en ?: $source->name_ar) : $source->name_ar)
        : '—';
    $eventLabel = fn (string $action) => __('messages.private_event_'.$action) !== 'messages.private_event_'.$action
        ? __('messages.private_event_'.$action)
        : $action;

    $status = $privateRequest->status;
    $isTerminal = in_array($status, [
        PrivateCourseRequest::STATUS_REJECTED,
        PrivateCourseRequest::STATUS_EXPIRED_UNPAID,
        PrivateCourseRequest::STATUS_EXPIRED_BUSY,
        PrivateCourseRequest::STATUS_CANCELED_NO_MEETING,
        PrivateCourseRequest::STATUS_BLOCKED,
    ], true);

    $activeStep = match ($status) {
        PrivateCourseRequest::STATUS_PENDING_TRAINER => 2,
        PrivateCourseRequest::STATUS_DATES_PROPOSED,
        PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED => 3,
        PrivateCourseRequest::STATUS_AWAITING_PAYMENT => 4,
        PrivateCourseRequest::STATUS_PAID => 5,
        default => 1,
    };
    if ($isTerminal) {
        $activeStep = 1;
    }

    $proposedStartLocal = $privateRequest->proposed_start_at
        ? $privateRequest->proposed_start_at->format('Y-m-d\TH:i')
        : '';
    $proposedEndLocal = $privateRequest->proposed_end_at
        ? $privateRequest->proposed_end_at->format('Y-m-d\TH:i')
        : '';

    $statusTone = match ($status) {
        PrivateCourseRequest::STATUS_PAID => 'ok',
        PrivateCourseRequest::STATUS_AWAITING_PAYMENT => 'warn',
        PrivateCourseRequest::STATUS_PENDING_TRAINER,
        PrivateCourseRequest::STATUS_DATES_PROPOSED,
        PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED => 'info',
        default => 'bad',
    };

    $backUrl = $isAdmin
        ? route('dashboard.academy.private-requests.admin-index')
        : ($isTrainer
            ? route('dashboard.academy.private-requests.trainer-inbox')
            : route('dashboard.academy.private-requests.trainee-index'));
@endphp

@push('styles')
<style>
    .pr-show {
        width: 100%;
        max-width: 64rem;
        margin: 0 auto;
        display: grid;
        gap: 1.1rem;
        padding-bottom: 1.5rem;
    }
    .pr-show .pr-hero {
        border-radius: 1.35rem;
        padding: 1.25rem 1.35rem;
        color: #fff;
        background:
            radial-gradient(420px 160px at 100% 0%, rgba(212,160,23,.28), transparent 55%),
            linear-gradient(125deg, #061525 0%, #0a2f45 55%, #0c3d48 100%);
        box-shadow: 0 18px 40px rgba(6,21,37,.18);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
    }
    .pr-show .pr-hero .eyebrow {
        margin: 0 0 .35rem;
        font-size: .78rem;
        color: rgba(255,255,255,.7);
        font-weight: 700;
    }
    .pr-show .pr-hero h1 {
        margin: 0;
        font-size: clamp(1.2rem, 2.4vw, 1.65rem);
        font-weight: 800;
        line-height: 1.35;
        font-family: var(--ac-display, inherit);
    }
    .pr-show .pr-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }
    .pr-show .pr-status {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .9rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        color: #fff;
        white-space: nowrap;
    }
    .pr-show .pr-status.is-ok { background: rgba(16,185,129,.22); border-color: rgba(16,185,129,.35); }
    .pr-show .pr-status.is-warn { background: rgba(245,158,11,.22); border-color: rgba(245,158,11,.35); }
    .pr-show .pr-status.is-info { background: rgba(11,143,127,.22); border-color: rgba(11,143,127,.35); }
    .pr-show .pr-status.is-bad { background: rgba(239,68,68,.22); border-color: rgba(239,68,68,.35); }
    .pr-show .pr-back {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .9rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.18);
        color: #fff !important;
        text-decoration: none !important;
    }
    .pr-show .pr-back:hover { background: rgba(255,255,255,.18); }

    .pr-show .pr-card {
        background: #fff;
        border: 1px solid var(--ac-line, #d4e0ec);
        border-radius: 1.25rem;
        padding: 1.2rem 1.3rem;
        box-shadow: 0 10px 28px rgba(6,21,37,.05);
    }
    .pr-show .pr-card h2 {
        margin: 0 0 1rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: #061525;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .pr-show .pr-card h2 i { color: #0b8f7f; }

    .pr-show .pr-stats {
        display: grid;
        gap: .75rem;
        grid-template-columns: repeat(auto-fit, minmax(12rem, 1fr));
    }
    .pr-show .pr-stat {
        background: #f8fafc;
        border: 1px solid var(--ac-line, #d4e0ec);
        border-radius: 1rem;
        padding: .9rem 1rem;
    }
    .pr-show .pr-stat dt {
        font-size: .75rem;
        color: var(--ac-muted, #5a6d82);
        font-weight: 700;
        margin: 0;
    }
    .pr-show .pr-stat dd {
        margin: .3rem 0 0;
        font-weight: 800;
        color: #061525;
        font-size: .95rem;
        line-height: 1.45;
        word-break: break-word;
    }
    .pr-show .pr-stat.is-due {
        background: #fff7ed;
        border-color: #fed7aa;
    }
    .pr-show .pr-stat.is-due dt,
    .pr-show .pr-stat.is-due dd { color: #9a3412; }

    .pr-show .pr-ended {
        padding: 1rem 1.15rem;
        border-radius: 1.1rem;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: .92rem;
        font-weight: 700;
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        line-height: 1.55;
    }
    .pr-show .pr-ended.is-bad {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .pr-show .pr-action-box {
        margin-top: 1.15rem;
        padding: 1.05rem 1.1rem;
        border-radius: 1.1rem;
        border: 1px solid rgba(11,143,127,.22);
        background: #f3fbf9;
    }
    .pr-show .pr-action-box.is-danger {
        border-color: #fecaca;
        background: #fff5f5;
    }
    .pr-show .pr-action-title {
        margin: 0 0 .75rem;
        font-size: .92rem;
        font-weight: 800;
        color: #0D2444;
    }
    .pr-show .pr-field { margin-bottom: .85rem; }
    .pr-show .pr-field:last-of-type { margin-bottom: 0; }
    .pr-show .pr-field label {
        display: block;
        font-size: .8rem;
        font-weight: 800;
        color: #0D2444;
        margin-bottom: .35rem;
    }
    .pr-show .pr-field input,
    .pr-show .pr-field textarea,
    .pr-show .pr-field select {
        width: 100%;
        border-radius: .85rem;
        border: 1px solid #d4e0ec;
        background: #fff;
        padding: .7rem .85rem;
        font-size: .875rem;
        font-family: inherit;
        color: #061525;
    }
    .pr-show .pr-field input:focus,
    .pr-show .pr-field textarea:focus {
        outline: none;
        border-color: #0b8f7f;
        box-shadow: 0 0 0 3px rgba(11,143,127,.15);
    }

    .pr-show .pr-btn,
    .pr-show .ac-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .65rem 1.1rem;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 800;
        border: 0;
        cursor: pointer;
        text-decoration: none !important;
        font-family: inherit;
        line-height: 1.2;
        min-height: 2.5rem;
        transition: transform .15s ease, background-color .15s ease, box-shadow .15s ease;
        -webkit-appearance: none;
        appearance: none;
    }
    .pr-show .pr-btn:hover,
    .pr-show .ac-btn:hover { transform: translateY(-1px); }
    .pr-show .pr-btn-primary {
        background: linear-gradient(135deg, #061525, #0D2444);
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(6,21,37,.16);
    }
    .pr-show .pr-btn-primary:hover { background: #0b8f7f; }
    .pr-show .pr-btn-outline {
        background: #fff;
        color: #0b8f7f !important;
        border: 1px solid rgba(11,143,127,.35);
    }
    .pr-show .pr-btn-danger {
        background: #b91c1c;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(185,28,28,.18);
    }
    .pr-show .pr-btn-danger:hover { background: #991b1b; }
    .pr-show .pr-btn-ghost {
        background: #e8eef5;
        color: #0D2444 !important;
    }
    .pr-show .pr-actions-row {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-top: .85rem;
    }

    .pr-show .pr-log {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }
    .pr-show .pr-log th {
        padding: .75rem .9rem;
        font-size: .72rem;
        font-weight: 800;
        color: #5a6d82;
        background: #f8fafc;
        border-bottom: 1px solid #d4e0ec;
        white-space: nowrap;
    }
    .pr-show .pr-log td {
        padding: .85rem .9rem;
        font-size: .875rem;
        color: #061525;
        border-bottom: 1px solid #eef2f6;
        vertical-align: top;
    }
    .pr-show .pr-log tr:last-child td { border-bottom: 0; }
    .pr-show .pr-log tr:hover td { background: #f3fbf9; }
    .pr-show .pr-log-action { font-weight: 800; }
    .pr-show .pr-log-msg {
        margin-top: .25rem;
        font-size: .8rem;
        color: #5a6d82;
        font-weight: 600;
        line-height: 1.45;
    }
    .pr-show .pr-log-meta {
        color: #5a6d82;
        font-size: .8rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .pr-show .pr-empty {
        padding: 1.5rem;
        text-align: center;
        color: #5a6d82;
        font-size: .9rem;
    }

    .pr-show .pr-flow-head h2 { margin: 0 0 .25rem; font-size: 1.02rem; font-weight: 800; }
    .pr-show .pr-flow-head p { margin: 0 0 1rem; color: #5a6d82; font-size: .86rem; line-height: 1.55; }
    .pr-show .pr-flow-steps { list-style: none; margin: 0; padding: 0; display: grid; gap: .65rem; }
    .pr-show .pr-flow-step {
        display: grid; grid-template-columns: auto 1fr; gap: .7rem; align-items: start;
        padding: .75rem .85rem; border-radius: .95rem; border: 1px solid #d4e0ec; background: #f8fafc;
    }
    .pr-show .pr-flow-step.is-active { background: #e4f6f3; border-color: rgba(11,143,127,.35); }
    .pr-show .pr-flow-step.is-done { background: #fff; }
    .pr-show .pr-flow-badge {
        width: 2.2rem; height: 2.2rem; border-radius: 999px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #e2e8f0; color: #475569; font-size: .85rem;
    }
    .pr-show .pr-flow-step.is-active .pr-flow-badge,
    .pr-show .pr-flow-step.is-done .pr-flow-badge { background: #0b8f7f; color: #fff; }
    .pr-show .pr-flow-num { display: block; font-size: .68rem; font-weight: 700; color: #5a6d82; }
    .pr-show .pr-flow-copy strong { display: block; font-size: .9rem; }
    .pr-show .pr-flow-copy p { margin: .2rem 0 0; font-size: .78rem; color: #5a6d82; line-height: 1.5; }

    @media (max-width: 640px) {
        .pr-show .pr-log thead { display: none; }
        .pr-show .pr-log,
        .pr-show .pr-log tbody,
        .pr-show .pr-log tr,
        .pr-show .pr-log td { display: block; width: 100%; }
        .pr-show .pr-log tr {
            padding: .85rem 0;
            border-bottom: 1px solid #eef2f6;
        }
        .pr-show .pr-log td {
            padding: .15rem 0;
            border: 0;
        }
        .pr-show .pr-log-meta { white-space: normal; }
    }
</style>
@endpush

<section class="pr-show">
    <div class="pr-hero">
        <div>
            <p class="eyebrow">#{{ $privateRequest->id }} · {{ __('messages.private_request_show_title') }}</p>
            <h1>{{ $courseName }}</h1>
        </div>
        <div class="pr-hero-actions">
            <span class="pr-status is-{{ $statusTone }}">
                <i class="fas fa-info-circle"></i>
                {{ $privateRequest->statusLabel() }}
            </span>
            <a href="{{ $backUrl }}" class="pr-back">
                <i class="fas fa-arrow-right"></i>
                {{ $locale === 'ar' ? 'رجوع' : 'Back' }}
            </a>
        </div>
    </div>

    @if($isTerminal)
    <div class="pr-ended {{ in_array($status, [PrivateCourseRequest::STATUS_CANCELED_NO_MEETING, PrivateCourseRequest::STATUS_BLOCKED, PrivateCourseRequest::STATUS_REJECTED], true) ? 'is-bad' : '' }}">
        <i class="fas fa-exclamation-triangle mt-0.5"></i>
        <div>
            <div>{{ $privateRequest->statusLabel() }}</div>
            @if($privateRequest->rejection_reason)
            <div class="mt-1 font-semibold opacity-90">{{ $privateRequest->rejection_reason }}</div>
            @endif
            @if($privateRequest->block_reason)
            <div class="mt-1 font-semibold opacity-90">{{ $privateRequest->block_reason }}</div>
            @endif
            @if($isAdmin && $privateRequest->refund)
            <div class="pr-actions-row" style="margin-top:.75rem;">
                <a href="{{ route('dashboard.academy.private-refunds.index') }}" class="pr-btn pr-btn-ghost">
                    <i class="fas fa-undo"></i>
                    {{ $locale === 'ar' ? 'عرض الاستردادات' : 'View refunds' }}
                </a>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="pr-card">
        @include('course.partials.private-request-flow', ['activeStep' => $activeStep, 'locale' => $locale])
    </div>
    @endif

    <div class="pr-card">
        <h2><i class="fas fa-clipboard-list"></i> {{ $locale === 'ar' ? 'تفاصيل الطلب' : 'Request details' }}</h2>
        <dl class="pr-stats">
            <div class="pr-stat">
                <dt>{{ $locale === 'ar' ? 'السعر' : 'Price' }}</dt>
                <dd>
                    @php
                        $prBase = (float) $privateRequest->private_price;
                        $prFeePct = (float) config('services.ziina.fee_percent', 7.9);
                        $prFeeFixed = (float) config('services.ziina.fee_fixed', 2);
                        $prFees = round(($prBase * ($prFeePct / 100)) + $prFeeFixed, 2);
                        $prTotal = round($prBase + $prFees, 2);
                    @endphp
                    <span class="inline-flex flex-col items-start gap-1" dir="ltr">
                        <span class="inline-flex items-center gap-1.5">
                            <x-drhm-icon width="14" height="16" />
                            {{ number_format($prBase, 2) }}
                        </span>
                        <span class="text-xs font-normal text-amber-700">
                            {{ __('messages.ziina_fees_label') }}: {{ number_format($prFees, 2) }}
                        </span>
                        <span class="text-sm font-bold text-slate-900">
                            {{ __('messages.ziina_total_label') }}: {{ number_format($prTotal, 2) }}
                        </span>
                    </span>
                </dd>
            </div>
            @if($privateRequest->trainee)
            <div class="pr-stat">
                <dt>{{ $locale === 'ar' ? 'المتدرب' : 'Trainee' }}</dt>
                <dd>{{ $privateRequest->trainee->name }}</dd>
            </div>
            @endif
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
            @if($privateRequest->payment_due_at && $status === PrivateCourseRequest::STATUS_AWAITING_PAYMENT)
            <div class="pr-stat is-due">
                <dt>{{ $locale === 'ar' ? 'مهلة الدفع' : 'Payment due' }}</dt>
                <dd>{{ $privateRequest->payment_due_at->format('Y-m-d H:i') }}</dd>
            </div>
            @endif
        </dl>

        @if($isTrainee && $status === PrivateCourseRequest::STATUS_DATES_PROPOSED)
        <div class="pr-action-box space-y-3">
            <p class="pr-action-title">{{ __('messages.private_flow_step_3_title') }}</p>
            <form method="POST" action="{{ route('private-requests.accept-dates', $privateRequest) }}">
                @csrf
                <button type="submit" class="pr-btn pr-btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.private_request_accept_dates') }}
                </button>
            </form>
            <details class="text-sm">
                <summary class="cursor-pointer font-bold text-teal-700">{{ __('messages.private_request_request_change') }}</summary>
                <form method="POST" action="{{ route('private-requests.request-date-change', $privateRequest) }}" class="mt-3 space-y-2">
                    @csrf
                    <div class="pr-field">
                        <label>{{ __('messages.private_request_start_at') }}</label>
                        <input type="datetime-local" name="proposed_start_at" required
                            value="{{ old('proposed_start_at', $proposedStartLocal) }}">
                    </div>
                    <div class="pr-field">
                        <label>{{ __('messages.private_request_end_at') }}</label>
                        <input type="datetime-local" name="proposed_end_at" required
                            value="{{ old('proposed_end_at', $proposedEndLocal) }}">
                    </div>
                    <div class="pr-field">
                        <textarea name="message" rows="2" placeholder="{{ $locale === 'ar' ? 'رسالة للمحاضر' : 'Message to trainer' }}"></textarea>
                    </div>
                    <button type="submit" class="pr-btn pr-btn-outline">{{ __('messages.private_request_request_change') }}</button>
                </form>
            </details>
        </div>
        @endif

        @if($isTrainee && $status === PrivateCourseRequest::STATUS_AWAITING_PAYMENT)
        <div class="pr-action-box">
            <p class="pr-action-title">{{ __('messages.private_flow_step_4_body') }}</p>
            <button type="button" onclick="payPrivateRequest()" class="pr-btn pr-btn-primary">
                <i class="fas fa-credit-card"></i> {{ __('messages.private_request_pay_now') }}
            </button>
        </div>
        @endif

        @if(($isTrainer || $isAdmin) && in_array($status, [
            PrivateCourseRequest::STATUS_PENDING_TRAINER,
            PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED,
        ], true))
        <div class="pr-action-box space-y-4">
            <form method="POST" action="{{ route('dashboard.academy.private-requests.approve', $privateRequest) }}" class="space-y-2">
                @csrf
                <p class="pr-action-title">{{ __('messages.private_request_approve') }}</p>
                <div class="pr-field">
                    <label>{{ __('messages.private_request_start_at') }}</label>
                    <input type="datetime-local" name="proposed_start_at" required
                        value="{{ old('proposed_start_at', $proposedStartLocal) }}">
                </div>
                <div class="pr-field">
                    <label>{{ __('messages.private_request_end_at') }}</label>
                    <input type="datetime-local" name="proposed_end_at" required
                        value="{{ old('proposed_end_at', $proposedEndLocal) }}">
                </div>
                <div class="pr-field">
                    <label>{{ __('messages.private_request_note') }}</label>
                    <textarea name="note" rows="2">{{ old('note') }}</textarea>
                </div>
                <div class="pr-actions-row">
                    <button type="submit" class="pr-btn pr-btn-primary">
                        <i class="fas fa-check"></i> {{ __('messages.private_request_approve') }}
                    </button>
                </div>
            </form>
            <form method="POST" action="{{ route('dashboard.academy.private-requests.reject', $privateRequest) }}">
                @csrf
                <div class="pr-field">
                    <label>{{ __('messages.private_request_rejection_reason') }}</label>
                    <textarea name="rejection_reason" required rows="2">{{ old('rejection_reason') }}</textarea>
                </div>
                <div class="pr-actions-row">
                    <button type="submit" class="pr-btn pr-btn-danger">
                        <i class="fas fa-times"></i> {{ __('messages.private_request_reject') }}
                    </button>
                </div>
            </form>
        </div>
        @endif

        @if($isAdmin && ! $isTerminal && $status !== PrivateCourseRequest::STATUS_PAID)
        <div class="pr-action-box is-danger">
            <form method="POST" action="{{ route('dashboard.academy.private-requests.block', $privateRequest) }}">
                @csrf
                <p class="pr-action-title" style="color:#b91c1c;">{{ __('messages.private_request_block') }}</p>
                <div class="pr-field">
                    <label>{{ $locale === 'ar' ? 'سبب الإيقاف' : 'Block reason' }}</label>
                    <textarea name="block_reason" required rows="2" placeholder="{{ $locale === 'ar' ? 'اكتب سبب إيقاف الطلب...' : 'Enter block reason...' }}">{{ old('block_reason') }}</textarea>
                </div>
                <div class="pr-actions-row">
                    <button type="submit" class="pr-btn pr-btn-danger">
                        <i class="fas fa-ban"></i> {{ __('messages.private_request_block') }}
                    </button>
                </div>
            </form>
        </div>
        @endif

        @if($status === PrivateCourseRequest::STATUS_PAID && $privateRequest->privateCourse)
        @php
            $privateCourse = $privateRequest->privateCourse;
            $courseStartAt = $privateCourse->start_date
                ? \Carbon\Carbon::parse($privateCourse->start_date)
                : null;
            $canEditMeetingLink = ($isTrainer || $isAdmin)
                && ! $privateCourse->isCanceled()
                && $courseStartAt
                && now()->lessThan($courseStartAt);
        @endphp
        <div class="pr-action-box space-y-3">
            <p class="pr-action-title">
                <i class="fas fa-video ml-1"></i>
                {{ __('messages.private_meeting_link_title') }}
            </p>

            @if($privateCourse->isCanceled())
            <p class="text-sm text-red-700 font-bold">
                {{ $locale === 'ar' ? 'تم إلغاء الدورة الخاصة.' : 'This private course was canceled.' }}
            </p>
            @elseif(($isTrainer || $isAdmin) && $canEditMeetingLink)
            @php
                $isYouTubeLink = \App\Support\YouTubeLive::isYouTubeUrl(old('online_link', $privateCourse->online_link));
                $defaultProvider = old('meeting_provider', $isYouTubeLink || blank($privateCourse->online_link) ? 'youtube' : 'external');
            @endphp
            @if(filled($privateCourse->online_link))
            <p class="text-sm text-slate-600 break-all" dir="ltr">
                <a href="{{ $privateCourse->online_link }}" target="_blank" class="text-teal-700 font-bold hover:underline">
                    {{ $privateCourse->online_link }}
                </a>
            </p>
            @endif
            <form method="POST" action="{{ route('dashboard.academy.private-requests.meeting-link', $privateRequest) }}" class="space-y-3" id="private-meeting-link-form">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">{{ __('messages.private_meeting_provider_label') }}</label>
                    <div class="grid sm:grid-cols-2 gap-2">
                        <label class="flex items-start gap-2 p-3 border rounded-xl cursor-pointer bg-white hover:bg-slate-50">
                            <input type="radio" name="meeting_provider" value="youtube"
                                {{ $defaultProvider === 'youtube' ? 'checked' : '' }}
                                class="mt-1 private-meeting-provider" style="accent-color:#0b8f7f;">
                            <span>
                                <span class="block text-sm font-bold text-slate-800">{{ __('messages.private_meeting_provider_youtube') }}</span>
                                <span class="block text-xs text-slate-500">{{ __('messages.private_meeting_provider_youtube_hint') }}</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-2 p-3 border rounded-xl cursor-pointer bg-white hover:bg-slate-50">
                            <input type="radio" name="meeting_provider" value="external"
                                {{ $defaultProvider === 'external' ? 'checked' : '' }}
                                class="mt-1 private-meeting-provider" style="accent-color:#0b8f7f;">
                            <span>
                                <span class="block text-sm font-bold text-slate-800">{{ __('messages.private_meeting_provider_external') }}</span>
                                <span class="block text-xs text-slate-500">{{ __('messages.private_meeting_provider_external_hint') }}</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div id="private_youtube_hint" class="{{ $defaultProvider === 'youtube' ? '' : 'hidden' }} p-3 text-sm text-teal-900 bg-teal-50 border border-teal-200 rounded-xl">
                    <i class="fas fa-info-circle ml-1"></i>
                    {{ __('messages.private_meeting_provider_youtube_help') }}
                </div>
                <div id="private_external_hint" class="{{ $defaultProvider === 'external' ? '' : 'hidden' }} p-3 text-sm text-amber-900 bg-amber-50 border border-amber-200 rounded-xl">
                    <i class="fas fa-info-circle ml-1"></i>
                    {{ __('messages.private_meeting_provider_external_help') }}
                </div>

                <div class="pr-field">
                    <label>{{ __('messages.private_meeting_link_label') }}</label>
                    <input type="url" name="online_link" id="private_online_link" required dir="ltr"
                        value="{{ old('online_link', $privateCourse->online_link) }}"
                        placeholder="{{ $defaultProvider === 'external' ? 'https://meet.google.com/...' : 'https://www.youtube.com/live/...' }}">
                    @error('online_link')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('meeting_provider')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <p class="text-xs text-slate-500">{{ __('messages.private_meeting_link_hint') }}</p>
                <button type="submit" class="pr-btn pr-btn-primary">
                    <i class="fas fa-save"></i>
                    {{ filled($privateCourse->online_link) ? __('messages.private_meeting_link_update') : __('messages.private_meeting_link_save') }}
                </button>
            </form>
            <script>
            (function () {
                const form = document.getElementById('private-meeting-link-form');
                if (!form) return;
                const input = document.getElementById('private_online_link');
                const ytHint = document.getElementById('private_youtube_hint');
                const exHint = document.getElementById('private_external_hint');
                const sync = () => {
                    const provider = (form.querySelector('input[name="meeting_provider"]:checked') || {}).value || 'youtube';
                    const isYt = provider === 'youtube';
                    if (ytHint) ytHint.classList.toggle('hidden', !isYt);
                    if (exHint) exHint.classList.toggle('hidden', isYt);
                    if (input) {
                        input.placeholder = isYt
                            ? 'https://www.youtube.com/live/...'
                            : 'https://meet.google.com/... أو Zoom / Jitsi';
                    }
                };
                form.querySelectorAll('.private-meeting-provider').forEach((el) => {
                    el.addEventListener('change', sync);
                });
                sync();
            })();
            </script>
            @elseif(($isTrainer || $isAdmin) && ! $privateCourse->isCanceled() && $courseStartAt && now()->greaterThanOrEqualTo($courseStartAt))
            <p class="text-sm text-amber-800 font-bold">{{ __('messages.private_meeting_link_deadline') }}</p>
            @elseif(filled($privateCourse->online_link))
            <p class="text-sm text-slate-600 break-all" dir="ltr">
                <a href="{{ $privateCourse->online_link }}" target="_blank" class="text-teal-700 font-bold hover:underline">
                    {{ $privateCourse->online_link }}
                </a>
            </p>
            @else
            <p class="text-sm text-amber-800">{{ __('messages.private_meeting_link_waiting') }}</p>
            @endif

            @if($isTrainee)
            @php
                $traineeCoursePaymentId = $privateRequest->payment_id
                    ?: \App\Models\Payment::query()
                        ->where('user_id', auth()->id())
                        ->where('course_id', $privateCourse->id)
                        ->whereNotNull('private_course_request_id')
                        ->latest('id')
                        ->value('id');
                $traineeCoursesUrl = $traineeCoursePaymentId
                    ? route('dashboard.my_courses.show', $traineeCoursePaymentId)
                    : route('dashboard.my_courses.index', ['type' => 'private']);
            @endphp
            <a href="{{ $traineeCoursesUrl }}" class="pr-btn pr-btn-primary inline-flex">
                <i class="fas fa-graduation-cap"></i> {{ $locale === 'ar' ? 'الذهاب لدورتي الخاصة' : 'Go to my private course' }}
            </a>
            @elseif($privateCourse->online_link && ($isTrainer || $isAdmin))
            <a href="{{ route('dashboard.courses.lecture', $privateCourse) }}" class="pr-btn pr-btn-outline inline-flex">
                <i class="fas fa-door-open"></i> {{ $locale === 'ar' ? 'غرفة المحاضرة' : 'Lecture room' }}
            </a>
            @endif
        </div>
        @endif
    </div>

    <div class="pr-card">
        <h2><i class="fas fa-history"></i> {{ $locale === 'ar' ? 'سجل التحديثات' : 'Timeline' }}</h2>
        <div class="overflow-x-auto -mx-1">
            <table class="pr-log min-w-[520px]">
                <thead>
                    <tr>
                        <th>{{ $locale === 'ar' ? 'الحدث' : 'Event' }}</th>
                        <th>{{ $locale === 'ar' ? 'بواسطة' : 'By' }}</th>
                        <th>{{ $locale === 'ar' ? 'التاريخ' : 'Date' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($privateRequest->events as $event)
                    <tr>
                        <td>
                            <div class="pr-log-action">{{ $eventLabel($event->action) }}</div>
                            @if($event->message)
                            <div class="pr-log-msg">{{ $event->message }}</div>
                            @endif
                        </td>
                        <td class="pr-log-meta">{{ $event->actor?->name ?? '—' }}</td>
                        <td class="pr-log-meta">{{ $event->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="pr-empty">{{ $locale === 'ar' ? 'لا أحداث بعد' : 'No events yet' }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
async function payPrivateRequest() {
    const btn = event.currentTarget;
    const base = {{ (float) $privateRequest->private_price }};
    const feePercent = {{ (float) config('services.ziina.fee_percent', 7.9) }};
    const feeFixed = {{ (float) config('services.ziina.fee_fixed', 2) }};
    const fees = (base * (feePercent / 100)) + feeFixed;
    const total = base + fees;

    const confirmed = await Swal.fire({
        title: @json(__('messages.ziina_total_label')),
        html: `<div style="text-align:start;line-height:1.8">
            <div>{{ __('messages.ziina_base_price_label') }}: <b>${base.toFixed(2)} AED</b></div>
            <div>{{ __('messages.ziina_fees_label') }}: <b>${fees.toFixed(2)} AED</b></div>
            <div style="margin-top:.5rem;font-size:1.1rem">{{ __('messages.ziina_total_label') }}: <b>${total.toFixed(2)} AED</b></div>
        </div>`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: @json(__('messages.ziina_confirm_pay')),
        cancelButtonText: @json(__('messages.close') === 'messages.close' ? 'Cancel' : __('messages.close')),
        confirmButtonColor: '#111111',
    });
    if (!confirmed.isConfirmed) return;

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
