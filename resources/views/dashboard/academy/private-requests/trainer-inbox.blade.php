@extends('layouts.app')

@section('title', 'طلبات الدورات الخاصة')

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
    .pr-table { width: 100%; min-width: 720px; border-collapse: collapse; text-align: right; }
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
    .pr-date { color: var(--ac-muted, #5a6d82); font-weight: 600; font-size: .85rem; }
    .pr-empty {
        padding: 2.75rem 1.25rem; text-align: center; color: var(--ac-muted, #5a6d82);
    }
    .pr-empty i { font-size: 1.75rem; color: #94a3b8; margin-bottom: .65rem; display: block; }
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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="طلبات الدورات الخاصة" />
    @else
    <div class="mb-5">
        <h1 class="text-xl font-extrabold text-slate-900">صندوق طلبات الدورات الخاصة</h1>
        <p class="text-sm text-slate-500 mt-1">طلبات المتدربين على دوراتك الخاصة</p>
    </div>
    @endunless

    <div class="pr-index-card">
        <div class="pr-index-head">
            <div>
                <h1>صندوق طلبات الدورات الخاصة</h1>
                <p>{{ $requests->total() }} طلب · اضغط معالجة لعرض التفاصيل واتخاذ إجراء</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pr-table">
                <thead>
                    <tr>
                        <th>المتدرب</th>
                        <th>الدورة</th>
                        <th>الحالة</th>
                        <th>المواعيد</th>
                        <th class="text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <div class="pr-course-name">{{ $req->trainee?->name ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="pr-course-name">{{ $req->sourceCourse?->name_ar ?? '—' }}</div>
                            @if($req->created_at)
                            <div class="pr-course-meta"><i class="fas fa-calendar-alt ml-1"></i>{{ $req->created_at->format('Y-m-d') }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="pr-badge {{ $statusTone($req->status) }}">
                                {{ $req->statusLabel() }}
                            </span>
                        </td>
                        <td>
                            @if($req->proposed_start_at)
                            <span class="pr-date">{{ $req->proposed_start_at->format('Y-m-d H:i') }}</span>
                            @else
                            <span class="pr-date">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('private-requests.show', $req) }}" class="ac-btn ac-btn-primary ac-btn-sm">
                                <i class="fas fa-tasks"></i>
                                معالجة
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="pr-empty">
                                <i class="fas fa-inbox"></i>
                                <p class="font-bold text-slate-700 mb-1">لا توجد طلبات حالياً</p>
                                <p class="text-sm">عندما يطلب متدرب دورة خاصة على دوراتك ستظهر هنا.</p>
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
