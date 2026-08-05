@extends('layouts.app')

@section('title', 'أرباح دوراتي')

@section('content')
@php
    $egp = fn ($aed) => ($egpRate ?? null) ? number_format((float)$aed * (float)$egpRate, 2) : null;
@endphp
<section class="p-3 sm:p-5">
    @unless(auth()->user()->usesAcademyShell())
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="الأكاديمية" third="أرباح دوراتي" />
    @else
    <div class="mb-5">
        <h1 class="text-xl font-extrabold text-slate-900">أرباح دوراتي</h1>
        <p class="text-sm text-slate-500 mt-1">الاحتساب من الاشتراكات المكتملة أو الفعالة فقط</p>
    </div>
    @endunless

    @if(session('success'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <ul class="list-disc pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach([
                ['label' => __('messages.trainer_wallet_total'), 'aed' => $summary['total_earned'] ?? 0, 'tone' => 'text-slate-900'],
                ['label' => __('messages.trainer_wallet_available'), 'aed' => $summary['available'] ?? 0, 'tone' => 'text-green-700'],
                ['label' => __('messages.trainer_wallet_withdrawn'), 'aed' => $summary['withdrawn'] ?? 0, 'tone' => 'text-indigo-700'],
                ['label' => __('messages.trainer_wallet_pending'), 'aed' => $summary['pending'] ?? 0, 'tone' => 'text-amber-700'],
            ] as $card)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                <p class="text-2xl font-black {{ $card['tone'] }} mt-2 inline-flex items-center gap-1.5">
                    {{ number_format((float)$card['aed'], 2) }} <x-drhm-icon width="16" height="18" />
                </p>
                @if($egp($card['aed']) !== null)
                <p class="text-xs text-slate-400 mt-1" dir="ltr">≈ {{ $egp($card['aed']) }} EGP</p>
                @endif
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h2 class="text-base font-bold text-slate-900 mb-2">{{ __('messages.trainer_cashout_request_title') }}</h2>
                <p class="text-xs text-slate-500 mb-4">
                    {{ __('messages.trainer_cashout_range_hint', ['min' => number_format($cashoutMin ?? 0, 2), 'max' => number_format($cashoutMax ?? 0, 2)]) }}
                </p>
                @if(!($paymentProfile?->isComplete()))
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 mb-3">
                    {{ __('messages.trainer_cashout_profile_incomplete') }}
                    <a href="{{ route('dashboard.academy.payment-profile.edit') }}" class="font-bold underline block mt-2">{{ __('messages.trainer_payment_config_manage') }}</a>
                </div>
                @else
                <form method="post" action="{{ route('dashboard.academy.cashouts.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-slate-700">{{ __('messages.trainer_cashout_amount') }} (AED)</label>
                        <input type="number" name="amount" step="0.01" min="{{ $cashoutMin }}" max="{{ min($cashoutMax, $summary['available'] ?? 0) }}"
                            value="{{ old('amount') }}" required
                            class="mt-1 w-full rounded-xl border-slate-300">
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background:linear-gradient(135deg,#0b8f7f,#0D2444);">
                        <i class="fas fa-money-bill-wave"></i>
                        {{ __('messages.trainer_cashout_submit') }}
                    </button>
                </form>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h2 class="text-base font-bold text-slate-900 mb-3">{{ __('messages.trainer_cashout_history') }}</h2>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse(($cashouts ?? []) as $cashout)
                    <div class="rounded-xl border border-slate-100 p-3 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-bold text-slate-900" dir="ltr">{{ number_format((float)$cashout->amount, 2) }} AED</p>
                            <p class="text-xs text-slate-500">{{ $cashout->statusLabel() }} · {{ $cashout->created_at?->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($cashout->canTrainerConfirm())
                        <form method="post" action="{{ route('dashboard.academy.cashouts.confirm', $cashout) }}">
                            @csrf
                            <button class="px-3 py-1.5 rounded-lg text-xs font-bold bg-green-600 text-white">{{ __('messages.trainer_cashout_confirm_received') }}</button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 text-center py-6">{{ __('messages.trainer_cashout_empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">عدد دوراتي</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['courses_count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">إجمالي الاشتراكات</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['subscriptions_count'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">أرباح الاشتراكات</p>
                <p class="text-2xl font-black text-green-700 mt-2 inline-flex items-center gap-1.5">{{ number_format($summary['trainer_profit'], 2) }} <x-drhm-icon width="16" height="18" /></p>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-3">أرباح كل دورة</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($courses as $course)
                <article class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 leading-snug">{{ $course['name_ar'] }}</h3>
                    @if($course['name_en'])
                    <p class="text-xs text-slate-400 mt-1 dir-ltr text-left">{{ $course['name_en'] }}</p>
                    @endif
                    <div class="mt-4 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-xs text-slate-500">الاشتراكات</p>
                            <p class="text-lg font-bold text-slate-800">{{ $course['subscriptions_count'] }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-slate-500">ربحي</p>
                            <p class="text-lg font-black text-green-700 inline-flex items-center gap-1">{{ number_format($course['trainer_profit'], 2) }} <x-drhm-icon width="12" height="14" /></p>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full text-center py-12 bg-white border border-dashed border-slate-200 rounded-2xl text-slate-400">
                    لا توجد أرباح لدوراتك حتى الآن.
                </div>
                @endforelse
            </div>
            @if(method_exists($courses, 'hasPages') && $courses->hasPages())
            <div class="ac-pagination mt-6">{{ $courses->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection
