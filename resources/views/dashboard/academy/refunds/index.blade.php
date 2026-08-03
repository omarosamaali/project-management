@extends('layouts.app')

@section('title', 'استردادات الدورات الخاصة')

@section('content')
@php
    use App\Models\PrivateCourseRefund;

    $statusTone = function (string $status): string {
        return match ($status) {
            PrivateCourseRefund::STATUS_REQUIRED => 'is-warn',
            PrivateCourseRefund::STATUS_PENDING_TRAINEE_CONFIRM => 'is-info',
            PrivateCourseRefund::STATUS_REFUNDED => 'is-ok',
            default => 'is-wait',
        };
    };

    $shotTone = function (string $kind): string {
        return match ($kind) {
            'success' => 'is-ok',
            'fail' => 'is-bad',
            default => 'is-warn',
        };
    };

    $filters = [
        '' => 'الكل',
        'required' => 'بانتظار المعالجة',
        'pending_trainee_confirm' => 'بانتظار المتدرب',
        'refunded' => 'مكتمل',
    ];
@endphp

<style>
    .rf-page .rf-card {
        background: #fff;
        border: 1px solid var(--ac-line, #d4e0ec);
        border-radius: 1.25rem;
        box-shadow: 0 10px 28px rgba(6,21,37,.05);
        overflow: hidden;
    }
    .rf-page .rf-head {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid var(--ac-line, #d4e0ec);
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem;
    }
    .rf-page .rf-head h1 {
        margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--ac-ink, #061525);
    }
    .rf-page .rf-head p { margin: .2rem 0 0; font-size: .82rem; color: var(--ac-muted, #5a6d82); }
    .rf-page .rf-filters { display: flex; flex-wrap: wrap; gap: .45rem; }
    .rf-page .rf-filter {
        display: inline-flex; align-items: center; padding: .45rem .9rem;
        border-radius: 999px; font-size: .8rem; font-weight: 800;
        border: 1px solid #d4e0ec; background: #fff; color: #0D2444;
        text-decoration: none !important; transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .rf-page .rf-filter:hover { background: #f3fbf9; border-color: #0b8f7f; color: #0b8f7f; }
    .rf-page .rf-filter.is-active {
        background: linear-gradient(135deg, #061525, #0D2444);
        border-color: transparent; color: #fff;
    }
    .rf-page .rf-table { width: 100%; min-width: 900px; border-collapse: collapse; text-align: right; }
    .rf-page .rf-table thead th {
        padding: .85rem 1.1rem; font-size: .75rem; font-weight: 800;
        color: var(--ac-muted, #5a6d82); background: #f8fafc;
        border-bottom: 1px solid var(--ac-line, #d4e0ec); white-space: nowrap;
    }
    .rf-page .rf-table tbody td {
        padding: .95rem 1.1rem; font-size: .9rem; color: var(--ac-ink, #061525);
        border-bottom: 1px solid #eef2f6; vertical-align: top;
    }
    .rf-page .rf-table tbody tr:last-child td { border-bottom: 0; }
    .rf-page .rf-table tbody tr:hover td { background: #f3fbf9; }
    .rf-page .rf-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .35rem .7rem; border-radius: 999px; font-size: .72rem; font-weight: 800;
        white-space: nowrap; border: 1px solid transparent;
    }
    .rf-page .rf-badge.is-wait { background: #f0f6fb; color: #0e3a5c; border-color: rgba(14,58,92,.18); }
    .rf-page .rf-badge.is-info { background: #e4f6f3; color: #0b8f7f; border-color: rgba(11,143,127,.25); }
    .rf-page .rf-badge.is-warn { background: #fff7ed; color: #c2410c; border-color: rgba(194,65,12,.2); }
    .rf-page .rf-badge.is-ok { background: #ecfdf5; color: #047857; border-color: rgba(4,120,87,.2); }
    .rf-page .rf-badge.is-bad { background: #fef2f2; color: #b91c1c; border-color: rgba(185,28,28,.18); }
    .rf-page .rf-price {
        font-weight: 800; font-variant-numeric: tabular-nums;
        direction: ltr; display: inline-flex; align-items: center; gap: .35rem;
    }
    .rf-page .rf-actions {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-start; gap: .45rem;
    }
    .rf-page .rf-shots {
        display: flex; flex-wrap: wrap; gap: .45rem; margin-top: .65rem;
    }
    .rf-page .rf-shot {
        width: 4.5rem; text-decoration: none !important; color: inherit;
    }
    .rf-page .rf-shot img {
        width: 4.5rem; height: 4.5rem; object-fit: cover;
        border-radius: .7rem; border: 1px solid #d4e0ec; background: #f8fafc;
        display: block;
    }
    .rf-page .rf-shot span {
        display: block; margin-top: .2rem; font-size: .65rem; font-weight: 800;
        text-align: center; color: #5a6d82;
    }
    .rf-page .rf-meta { font-size: .75rem; color: var(--ac-muted, #5a6d82); font-weight: 600; margin-top: .2rem; }
    .rf-page .ac-btn,
    .rf-modal .ac-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        padding: .5rem .9rem; border-radius: 999px; border: 0;
        font-size: .8rem; font-weight: 800; line-height: 1.2;
        text-decoration: none !important; cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
        font-family: inherit; -webkit-appearance: none; appearance: none;
        min-height: 2.25rem; white-space: nowrap;
    }
    .rf-page .ac-btn:hover,
    .rf-modal .ac-btn:hover { transform: translateY(-1px); }
    .rf-page .ac-btn-primary,
    .rf-modal .ac-btn-primary {
        background: linear-gradient(135deg, #061525, #0D2444) !important;
        color: #ffffff !important;
        box-shadow: 0 8px 18px rgba(6,21,37,.16);
    }
    .rf-page .ac-btn-primary:hover,
    .rf-modal .ac-btn-primary:hover { background: #0b8f7f !important; }
    .rf-page .ac-btn-ready {
        background: #0b8f7f !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(11,143,127,.2);
    }
    .rf-page .ac-btn-ready:hover { background: #09786b !important; }
    .rf-page .ac-btn-ghost,
    .rf-modal .ac-btn-ghost {
        background: #e8eef5 !important;
        color: #0D2444 !important;
    }
    .rf-page .ac-btn-outline {
        background: #fff !important;
        color: #0D2444 !important;
        border: 1px solid #d4e0ec !important;
    }
    .rf-page .rf-empty {
        padding: 2.75rem 1.25rem; text-align: center; color: var(--ac-muted, #5a6d82);
    }
    .rf-modal[hidden] { display: none !important; }
    .rf-modal {
        position: fixed; inset: 0; z-index: 80;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem;
    }
    .rf-modal__backdrop {
        position: absolute; inset: 0; background: rgba(6,21,37,.45);
        backdrop-filter: blur(2px);
    }
    .rf-modal__panel {
        position: relative; z-index: 1; width: min(100%, 30rem);
        background: #fff; border-radius: 1.25rem;
        border: 1px solid #d4e0ec;
        box-shadow: 0 24px 60px rgba(6,21,37,.22);
        padding: 1.25rem 1.35rem 1.35rem;
        max-height: min(90vh, 40rem); overflow: auto;
    }
    .rf-modal__title {
        margin: 0 0 .25rem; font-size: 1.05rem; font-weight: 800; color: #061525;
    }
    .rf-modal__sub {
        margin: 0 0 1rem; font-size: .82rem; color: #5a6d82; font-weight: 600;
    }
    .rf-modal label {
        display: block; font-size: .78rem; font-weight: 800; color: #0D2444; margin-bottom: .35rem;
    }
    .rf-modal .rf-field { margin-bottom: .9rem; }
    .rf-modal textarea,
    .rf-modal select,
    .rf-modal .rf-file-btn {
        width: 100%; border-radius: .85rem; border: 1px solid #d4e0ec;
        background: #f8fafc; color: #061525; font-family: inherit;
    }
    .rf-modal textarea, .rf-modal select {
        padding: .7rem .85rem; font-size: .875rem;
    }
    .rf-modal textarea { resize: vertical; min-height: 4rem; }
    .rf-modal textarea:focus, .rf-modal select:focus {
        outline: none; border-color: #0b8f7f; box-shadow: 0 0 0 3px rgba(11,143,127,.15);
        background: #fff;
    }
    .rf-modal .rf-file-btn {
        display: flex; align-items: center; justify-content: space-between; gap: .75rem;
        padding: .75rem .9rem; cursor: pointer; font-size: .85rem; font-weight: 700;
    }
    .rf-modal .rf-file-btn span.name {
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; text-align: right;
        color: #5a6d82;
    }
    .rf-modal .rf-file-btn span.name.has-file { color: #061525; }
    .rf-modal .rf-file-btn span.pick {
        flex-shrink: 0; padding: .35rem .7rem; border-radius: 999px;
        background: #e8eef5; color: #0D2444; font-size: .75rem; font-weight: 800;
    }
    .rf-modal input[type="file"] { position: absolute; width: 1px; height: 1px; opacity: 0; overflow: hidden; }
    .rf-modal__actions {
        display: flex; flex-wrap: wrap; gap: .5rem; justify-content: flex-start; margin-top: 1.1rem;
    }
    .rf-kinds { display: grid; grid-template-columns: repeat(3, 1fr); gap: .45rem; }
    .rf-kinds label {
        margin: 0; padding: .65rem .4rem; border-radius: .85rem; border: 1px solid #d4e0ec;
        background: #fff; text-align: center; cursor: pointer; font-size: .75rem;
    }
    .rf-kinds input { display: none; }
    .rf-kinds label.is-on { border-color: #0b8f7f; background: #e4f6f3; color: #0b8f7f; }
</style>

<section class="p-3 sm:p-5 rf-page">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="استردادات الدورات الخاصة" />

    @if(session('success'))
    <div class="mb-4 p-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200">
        <ul class="list-disc pr-5 space-y-1 mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rf-card">
        <div class="rf-head">
            <div>
                <h1>استردادات الدورات الخاصة</h1>
                <p>ارفع إثباتات التحويل (قيد / نجاح / فشل) ثم أرسل الطلب لتأكيد المتدرب</p>
            </div>
            <div class="rf-filters">
                @foreach($filters as $key => $label)
                <a href="{{ route('dashboard.academy.private-refunds.index', $key !== '' ? ['status' => $key] : []) }}"
                    class="rf-filter {{ ($status ?? '') === $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="rf-table">
                <thead>
                    <tr>
                        <th>المتدرب</th>
                        <th>الدورة</th>
                        <th>المبلغ</th>
                        <th>الحالة / الإثباتات</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refunds as $refund)
                    @php
                        $courseName = $refund->request?->sourceCourse?->name_ar ?? '—';
                        $traineeName = $refund->trainee?->name ?? '—';
                        $shots = $refund->screenshots;
                    @endphp
                    <tr>
                        <td>
                            <div class="font-bold">{{ $traineeName }}</div>
                            @if($refund->trainee?->email)
                            <div class="rf-meta">{{ $refund->trainee->email }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="font-extrabold leading-snug">{{ $courseName }}</div>
                            @if($refund->request)
                            <div class="rf-meta">طلب #{{ $refund->request->id }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="rf-price">
                                <x-drhm-icon width="12" height="14" />
                                {{ number_format((float) $refund->amount, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="rf-badge {{ $statusTone($refund->status) }}">
                                {{ __('messages.private_refund_status_'.$refund->status) }}
                            </span>
                            @if($shots->isNotEmpty())
                            <div class="rf-shots">
                                @foreach($shots as $shot)
                                <a href="{{ $shot->url() }}" target="_blank" rel="noopener" class="rf-shot" title="{{ $shot->kindLabel() }}">
                                    <img src="{{ $shot->url() }}" alt="{{ $shot->kindLabel() }}">
                                    <span class="rf-badge {{ $shotTone($shot->kind) }}" style="display:inline-flex;padding:.15rem .4rem;font-size:.6rem;">
                                        {{ $shot->kindLabel() }}
                                    </span>
                                </a>
                                @endforeach
                            </div>
                            @else
                            <div class="rf-meta">لا إثباتات بعد</div>
                            @endif
                        </td>
                        <td>
                            <div class="rf-actions">
                                @if($refund->canUploadScreenshots())
                                <button type="button"
                                    class="ac-btn ac-btn-primary js-rf-open-upload"
                                    data-action="{{ route('dashboard.academy.private-refunds.upload-screenshot', $refund) }}"
                                    data-trainee="{{ $traineeName }}"
                                    data-course="{{ $courseName }}"
                                    data-amount="{{ number_format((float) $refund->amount, 2) }}">
                                    <i class="fas fa-upload"></i>
                                    {{ $shots->isEmpty() ? __('messages.private_refund_upload_proof') : __('messages.private_refund_upload_another') }}
                                </button>
                                @endif

                                @if($refund->canMarkReadyForTrainee())
                                <form method="POST" action="{{ route('dashboard.academy.private-refunds.mark-ready', $refund) }}">
                                    @csrf
                                    <button type="submit" class="ac-btn ac-btn-ready"
                                        onclick="return confirm('إرسال طلب التأكيد للمتدرب الآن؟ سيبدأ عدّاد 24 ساعة.')">
                                        <i class="fas fa-paper-plane"></i>
                                        {{ __('messages.private_refund_mark_ready') }}
                                    </button>
                                </form>
                                @elseif($refund->status === PrivateCourseRefund::STATUS_REQUIRED && $shots->isNotEmpty())
                                <span class="rf-meta">{{ __('messages.private_refund_mark_ready_hint') }}</span>
                                @endif

                                @if($refund->request)
                                <a href="{{ route('private-requests.show', $refund->request) }}" class="ac-btn ac-btn-ghost">
                                    <i class="fas fa-external-link-alt"></i>
                                    الطلب
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="rf-empty">
                                <i class="fas fa-inbox"></i>
                                لا توجد استردادات في هذا التصفية
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($refunds->hasPages())
        <div class="p-4 border-t border-slate-100">{{ $refunds->links() }}</div>
        @endif
    </div>
</section>

<div class="rf-modal" id="rfUploadModal" hidden>
    <div class="rf-modal__backdrop" data-rf-close></div>
    <div class="rf-modal__panel" role="dialog" aria-modal="true" aria-labelledby="rfUploadTitle">
        <h2 class="rf-modal__title" id="rfUploadTitle">{{ __('messages.private_refund_upload_proof') }}</h2>
        <p class="rf-modal__sub" id="rfUploadSub"></p>

        <form method="POST" id="rfUploadForm" enctype="multipart/form-data" action="#">
            @csrf
            <div class="rf-field">
                <label>{{ __('messages.private_refund_kind_label') }}</label>
                <div class="rf-kinds" id="rfKinds">
                    <label class="is-on">
                        <input type="radio" name="kind" value="pending" checked>
                        {{ __('messages.private_refund_shot_kind_pending') }}
                    </label>
                    <label>
                        <input type="radio" name="kind" value="success">
                        {{ __('messages.private_refund_shot_kind_success') }}
                    </label>
                    <label>
                        <input type="radio" name="kind" value="fail">
                        {{ __('messages.private_refund_shot_kind_fail') }}
                    </label>
                </div>
            </div>
            <div class="rf-field">
                <label for="rfScreenshot">صورة إثبات التحويل</label>
                <input type="file" id="rfScreenshot" name="screenshot" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                <label for="rfScreenshot" class="rf-file-btn">
                    <span class="name" id="rfFileName">لم يُختر ملف</span>
                    <span class="pick"><i class="fas fa-folder-open ml-1"></i> اختيار</span>
                </label>
            </div>
            <div class="rf-field">
                <label for="rfAdminNote">ملاحظة (اختياري)</label>
                <textarea id="rfAdminNote" name="admin_note" rows="3" maxlength="2000" placeholder="مثال: تحويل قيد التنفيذ / نجح / فشل..."></textarea>
            </div>
            <div class="rf-modal__actions">
                <button type="submit" class="ac-btn ac-btn-primary">
                    <i class="fas fa-upload"></i>
                    رفع الإثبات
                </button>
                <button type="button" class="ac-btn ac-btn-ghost" data-rf-close>إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('rfUploadModal');
    const form = document.getElementById('rfUploadForm');
    const fileInput = document.getElementById('rfScreenshot');
    const fileName = document.getElementById('rfFileName');
    const sub = document.getElementById('rfUploadSub');
    const kinds = document.getElementById('rfKinds');
    if (!modal || !form) return;

    const syncKinds = () => {
        kinds.querySelectorAll('label').forEach((lab) => {
            const input = lab.querySelector('input');
            lab.classList.toggle('is-on', !!(input && input.checked));
        });
    };

    const open = (btn) => {
        form.action = btn.dataset.action || '#';
        fileInput.value = '';
        fileName.textContent = 'لم يُختر ملف';
        fileName.classList.remove('has-file');
        document.getElementById('rfAdminNote').value = '';
        const pending = form.querySelector('input[name="kind"][value="pending"]');
        if (pending) pending.checked = true;
        syncKinds();
        const amount = btn.dataset.amount || '';
        sub.textContent = [btn.dataset.trainee, btn.dataset.course, amount ? (amount + ' درهم') : '']
            .filter(Boolean).join(' — ');
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        modal.hidden = true;
        document.body.style.overflow = '';
    };

    document.querySelectorAll('.js-rf-open-upload').forEach((btn) => {
        btn.addEventListener('click', () => open(btn));
    });
    modal.querySelectorAll('[data-rf-close]').forEach((el) => el.addEventListener('click', close));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) close();
    });
    kinds.addEventListener('change', syncKinds);
    fileInput.addEventListener('change', () => {
        const file = fileInput.files && fileInput.files[0];
        if (file) {
            fileName.textContent = file.name;
            fileName.classList.add('has-file');
        } else {
            fileName.textContent = 'لم يُختر ملف';
            fileName.classList.remove('has-file');
        }
    });
})();
</script>
@endsection
