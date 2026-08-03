@extends('layouts.app')

@section('title', 'طلباتي الخاصة')

@section('content')
@php
    use App\Models\PrivateCourseRequest;

    $statusTone = function (string $status): string {
        return match ($status) {
            PrivateCourseRequest::STATUS_PENDING_TRAINER => 'is-wait',
            PrivateCourseRequest::STATUS_DATES_PROPOSED,
            PrivateCourseRequest::STATUS_DATES_CHANGE_REQUESTED => 'is-info',
            PrivateCourseRequest::STATUS_AWAITING_PAYMENT => 'is-warn',
            PrivateCourseRequest::STATUS_PAID => 'is-ok',
            PrivateCourseRequest::STATUS_REJECTED,
            PrivateCourseRequest::STATUS_BLOCKED,
            PrivateCourseRequest::STATUS_EXPIRED_UNPAID,
            PrivateCourseRequest::STATUS_EXPIRED_BUSY,
            PrivateCourseRequest::STATUS_CANCELED_NO_MEETING => 'is-bad',
            default => 'is-wait',
        };
    };
@endphp

<style>
    .pr-index-card {
        background: #fff;
        border: 1px solid var(--ac-line, #d4e0ec);
        border-radius: 1.25rem;
        box-shadow: 0 10px 28px rgba(6,21,37,.05);
        overflow: hidden;
    }
    .pr-index-head {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid var(--ac-line, #d4e0ec);
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem;
    }
    .pr-index-head h1 {
        margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--ac-ink, #061525);
        font-family: var(--ac-display, inherit);
    }
    .pr-index-head p { margin: .2rem 0 0; font-size: .82rem; color: var(--ac-muted, #5a6d82); }
    .pr-table { width: 100%; min-width: 680px; border-collapse: collapse; text-align: right; }
    .pr-table thead th {
        padding: .85rem 1.1rem; font-size: .75rem; font-weight: 800;
        color: var(--ac-muted, #5a6d82); background: #f8fafc;
        border-bottom: 1px solid var(--ac-line, #d4e0ec); white-space: nowrap;
    }
    .pr-table tbody td {
        padding: 1rem 1.1rem; font-size: .9rem; color: var(--ac-ink, #061525);
        border-bottom: 1px solid #eef2f6; vertical-align: middle;
    }
    .pr-table tbody tr:last-child td { border-bottom: 0; }
    .pr-table tbody tr:hover td { background: #f3fbf9; }
    .pr-course-name { font-weight: 800; line-height: 1.35; }
    .pr-course-meta { margin-top: .2rem; font-size: .75rem; color: var(--ac-muted, #5a6d82); font-weight: 600; }
    .pr-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .35rem .7rem; border-radius: 999px; font-size: .72rem; font-weight: 800;
        white-space: nowrap; border: 1px solid transparent;
    }
    .pr-badge.is-wait { background: #f0f6fb; color: #0e3a5c; border-color: rgba(14,58,92,.18); }
    .pr-badge.is-info { background: #e4f6f3; color: #0b8f7f; border-color: rgba(11,143,127,.25); }
    .pr-badge.is-warn { background: #fff7ed; color: #c2410c; border-color: rgba(194,65,12,.2); }
    .pr-badge.is-ok { background: #ecfdf5; color: #047857; border-color: rgba(4,120,87,.2); }
    .pr-badge.is-bad { background: #fef2f2; color: #b91c1c; border-color: rgba(185,28,28,.18); }
    .pr-price { font-weight: 800; font-variant-numeric: tabular-nums; direction: ltr; display: inline-block; }
    .pr-date { color: var(--ac-muted, #5a6d82); font-weight: 600; font-size: .85rem; }
    .pr-empty {
        padding: 2.75rem 1.25rem; text-align: center; color: var(--ac-muted, #5a6d82);
    }
    .pr-empty i { font-size: 1.75rem; color: #94a3b8; margin-bottom: .65rem; display: block; }
    .pr-refund-card {
        margin-bottom: 1.15rem; border-radius: 1.15rem; padding: 1rem 1.1rem;
        background: #fff7ed; border: 1px solid #fed7aa;
    }
    .pr-refund-card h2 { margin: 0 0 .85rem; font-size: .98rem; font-weight: 800; color: #9a3412; }
    /* Match academy shell action buttons when this page is outside .academy-shell */
    .pr-index-page .ac-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
        padding: .55rem 1rem; border-radius: 999px; border: 0;
        font-size: .875rem; font-weight: 800; line-height: 1.2;
        text-decoration: none !important; cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
        font-family: inherit; -webkit-appearance: none; appearance: none;
    }
    .pr-index-page .ac-btn:hover { transform: translateY(-1px); }
    .pr-index-page .ac-btn-sm { padding: .5rem .85rem; font-size: .8rem; min-height: 2.25rem; }
    .pr-index-page .ac-btn-primary {
        background: linear-gradient(135deg, #061525, #0D2444) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 18px rgba(6,21,37,.16);
    }
    .pr-index-page .ac-btn-primary:hover,
    .pr-index-page .ac-btn-primary:focus {
        background: #0b8f7f !important;
        color: #ffffff !important;
    }
</style>

<section class="pr-index-page p-3 sm:p-5">
    @unless(auth()->user()->usesAcademyShell())
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="طلباتي الخاصة" />
    @else
    <div class="mb-5">
        <h1 class="text-xl font-extrabold text-slate-900">طلباتي الخاصة</h1>
        <p class="text-sm text-slate-500 mt-1">تابع طلبات الدورات الخاصة وحالاتها خطوة بخطوة</p>
    </div>
    @endunless

    @if($pendingRefunds->isNotEmpty())
    <div class="pr-refund-card">
        <h2><i class="fas fa-money-bill-wave ml-1"></i> تأكيدات استرداد</h2>
        <div class="space-y-3">
            @foreach($pendingRefunds as $refund)
            <div class="bg-white rounded-xl border border-orange-100 p-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-bold text-gray-900">{{ $refund->request?->sourceCourse?->name_ar ?? '—' }}</p>
                    <p class="text-sm text-gray-500 mt-0.5 inline-flex items-center gap-1" dir="ltr">
                        <x-drhm-icon width="12" height="14" />
                        {{ number_format((float) $refund->amount, 2) }}
                    </p>
                    @if($refund->screenshotUrl())
                    <a href="{{ $refund->screenshotUrl() }}" target="_blank" class="text-sm text-teal-700 font-bold hover:underline">عرض إثبات التحويل</a>
                    @endif
                </div>
                <form method="POST" action="{{ route('dashboard.academy.private-refunds.confirm', $refund) }}">
                    @csrf
                    <button type="submit" class="ac-btn ac-btn-primary ac-btn-sm">
                        <i class="fas fa-check"></i>
                        {{ __('messages.private_refund_confirm') }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="pr-index-card">
        <div class="pr-index-head">
            <div>
                <h1>طلباتي الخاصة</h1>
                <p>{{ $requests->total() }} طلب · اضغط متابعة لعرض الخطوات والحالة</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pr-table">
                <thead>
                    <tr>
                        <th>الدورة</th>
                        <th>الحالة</th>
                        <th>السعر</th>
                        <th>التاريخ</th>
                        <th class="text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <div class="pr-course-name">{{ $req->sourceCourse?->name_ar ?? '—' }}</div>
                            @if($req->trainer)
                            <div class="pr-course-meta"><i class="fas fa-chalkboard-teacher ml-1"></i>{{ $req->trainer->name }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="pr-badge {{ $statusTone($req->status) }}">
                                {{ $req->statusLabel() }}
                            </span>
                        </td>
                        <td>
                            <span class="pr-price inline-flex items-center gap-1">
                                <x-drhm-icon width="12" height="14" />
                                {{ number_format((float) $req->private_price, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="pr-date">{{ $req->created_at->format('Y-m-d') }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('private-requests.show', $req) }}" class="ac-btn ac-btn-primary ac-btn-sm">
                                <i class="fas fa-eye"></i>
                                متابعة
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="pr-empty">
                                <i class="fas fa-user-lock"></i>
                                <p class="font-bold text-slate-700 mb-1">لا توجد طلبات بعد</p>
                                <p class="text-sm mb-4">عند طلب دورة خاصة من صفحة الدورة ستظهر هنا.</p>
                                <a href="{{ route('academy.index') }}" class="ac-btn ac-btn-primary ac-btn-sm">
                                    <i class="fas fa-compass"></i>
                                    تصفح الأكاديمية
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $requests->links() }}</div>
        @endif
    </div>
</section>
@endsection
