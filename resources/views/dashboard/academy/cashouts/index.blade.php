@extends('layouts.app')

@section('title', __('messages.trainer_cashout_admin_title'))

@section('content')
@php
    use App\Models\TrainerCashoutRequest;
    $statusLabel = fn ($s) => match ($s) {
        TrainerCashoutRequest::STATUS_PENDING_ADMIN => __('messages.trainer_cashout_status_pending_admin'),
        TrainerCashoutRequest::STATUS_PROCESSING => __('messages.trainer_cashout_status_processing'),
        TrainerCashoutRequest::STATUS_PENDING_TRAINER_CONFIRM => __('messages.trainer_cashout_status_pending_trainer_confirm'),
        TrainerCashoutRequest::STATUS_PAID => __('messages.trainer_cashout_status_paid'),
        TrainerCashoutRequest::STATUS_REJECTED => __('messages.trainer_cashout_status_rejected'),
        TrainerCashoutRequest::STATUS_FAILED => __('messages.trainer_cashout_status_failed'),
        default => $s,
    };
@endphp
<section class="p-3 sm:p-5">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('messages.trainer_cashout_admin_title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('messages.trainer_cashout_admin_hint') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm text-right">
            <thead class="bg-slate-50 text-slate-500 text-xs">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">{{ __('messages.course_trainer_label') }}</th>
                    <th class="px-4 py-3">{{ __('messages.ziina_total_label') }}</th>
                    <th class="px-4 py-3">{{ __('messages.status') ?? 'Status' }}</th>
                    <th class="px-4 py-3">{{ __('messages.trainer_payment_config_tab') }}</th>
                    <th class="px-4 py-3">Screenshots</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($cashouts ?? []) as $cashout)
                <tr class="border-t border-slate-100 align-top">
                    <td class="px-4 py-3 font-bold">{{ $cashout->id }}</td>
                    <td class="px-4 py-3">
                        <div class="font-bold">{{ $cashout->user?->name }}</div>
                        <div class="text-xs text-slate-400" dir="ltr">{{ $cashout->user?->email }}</div>
                    </td>
                    <td class="px-4 py-3 font-black text-green-700" dir="ltr">{{ number_format((float)$cashout->amount, 2) }} AED</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">{{ $statusLabel($cashout->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @php $snap = $cashout->payout_snapshot ?? []; @endphp
                        @if(($snap['type'] ?? '') === 'bank')
                            <div>{{ $snap['bank_account_name'] ?? '' }}</div>
                            <div dir="ltr">{{ $snap['bank_iban'] ?? '' }}</div>
                            <div>{{ $snap['bank_name'] ?? '' }}</div>
                        @else
                            <div class="font-semibold">{{ $snap['method_name'] ?? $cashout->payoutMethod?->title(app()->getLocale()) }}</div>
                            @foreach(($snap['field_values'] ?? []) as $k => $v)
                            <div><span class="text-slate-400">{{ $k }}:</span> {{ is_scalar($v) ? $v : json_encode($v) }}</div>
                            @endforeach
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach($cashout->screenshots as $shot)
                            <a href="{{ route('dashboard.academy.cashouts.screenshot-file', $shot) }}" target="_blank" class="block w-14">
                                <img src="{{ route('dashboard.academy.cashouts.screenshot-file', $shot) }}" class="w-14 h-14 object-cover rounded-lg border" alt="">
                                <span class="block text-[10px] text-center font-bold mt-0.5">{{ $shot->kind }}</span>
                            </a>
                            @endforeach
                        </div>
                        @if($cashout->canUploadScreenshots())
                        <form action="{{ route('dashboard.academy.cashouts.upload-screenshot', $cashout) }}" method="post" enctype="multipart/form-data" class="mt-2 space-y-1">
                            @csrf
                            <select name="kind" class="w-full text-xs rounded border-slate-300">
                                <option value="pending">pending</option>
                                <option value="success">success</option>
                                <option value="fail">fail</option>
                            </select>
                            <input type="file" name="screenshot" accept="image/*" required class="w-full text-xs">
                            <button class="w-full text-xs font-bold px-2 py-1.5 rounded-lg bg-slate-900 text-white">Upload</button>
                        </form>
                        @endif
                    </td>
                    <td class="px-4 py-3 space-y-2">
                        @if($cashout->canMarkReadyForTrainer())
                        <form method="post" action="{{ route('dashboard.academy.cashouts.mark-ready', $cashout) }}">
                            @csrf
                            <button class="w-full text-xs font-bold px-2 py-1.5 rounded-lg bg-teal-600 text-white">{{ __('messages.trainer_cashout_mark_ready') }}</button>
                        </form>
                        @endif
                        @if($cashout->canReject())
                        <form method="post" action="{{ route('dashboard.academy.cashouts.reject', $cashout) }}" onsubmit="return confirm('Reject?')">
                            @csrf
                            <button class="w-full text-xs font-bold px-2 py-1.5 rounded-lg bg-rose-600 text-white">{{ __('messages.trainer_cashout_reject') }}</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">{{ __('messages.trainer_cashout_empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($cashouts) && method_exists($cashouts, 'hasPages') && $cashouts->hasPages())
    <div class="mt-4">{{ $cashouts->links() }}</div>
    @endif
</section>
@endsection
