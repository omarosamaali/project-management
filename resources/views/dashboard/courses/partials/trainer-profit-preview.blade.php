{{-- Live trainer profit preview under course price (AED + EGP). --}}
@php
    $trainerProfitPct = (float) ($trainerProfitPercentage ?? \App\Models\Setting::academyTrainerProfitPercentage());
    $platformFeePct = max(0, round(100 - $trainerProfitPct, 2));
    $fmtPct = fn ($n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
@endphp
<div id="trainer_profit_preview"
    class="hidden mt-3 rounded-xl border border-emerald-200 bg-emerald-50/80 text-sm text-slate-700"
    style="padding: 1rem 1.15rem;"
    data-trainer-pct="{{ $trainerProfitPct }}"
    data-platform-pct="{{ $platformFeePct }}"
    data-rate-url="{{ route('dashboard.academy.currency.aed-egp') }}"
    aria-live="polite">
    <div class="flex items-start gap-3">
        <i class="fas fa-calculator text-emerald-600 mt-1 shrink-0"></i>
        <div class="min-w-0 flex-1 space-y-3">
            <p class="text-xs font-bold text-emerald-800">ربح المحاضر لكل اشتراك</p>

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-slate-600">السعر</span>
                    <span class="inline-flex items-center gap-1 font-semibold text-slate-800 tabular-nums" dir="ltr">
                        <span id="tp_price">—</span>
                        <x-drhm-icon width="12" height="14" />
                    </span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span class="text-slate-600">رسوم المنصة <span class="text-slate-400">({{ $fmtPct($platformFeePct) }}%)</span></span>
                    <span class="inline-flex items-center gap-1 font-semibold text-slate-800 tabular-nums" dir="ltr">
                        <span id="tp_fee">—</span>
                        <x-drhm-icon width="12" height="14" />
                    </span>
                </div>
                <div class="flex items-center justify-between gap-4 pt-2 border-t border-emerald-200/80">
                    <span class="font-bold text-emerald-800">ربحك</span>
                    <span class="inline-flex items-center gap-1 font-extrabold text-emerald-700 tabular-nums text-base" dir="ltr">
                        <span id="tp_profit">—</span>
                        <x-drhm-icon width="13" height="15" color="#047857" />
                    </span>
                </div>
            </div>

            <div class="space-y-1.5 pt-1 border-t border-emerald-200/60">
                <div class="flex items-center justify-between gap-4 text-xs">
                    <span class="text-slate-500">بالجنيه المصري</span>
                    <span id="tp_egp" class="font-semibold text-slate-800 tabular-nums" dir="ltr">—</span>
                </div>
                <div class="flex items-center justify-between gap-4 text-[11px] text-slate-400">
                    <span>سعر الصرف</span>
                    <span class="inline-flex items-center gap-1 tabular-nums" dir="ltr">
                        <span>1</span>
                        <x-drhm-icon width="11" height="12" class="opacity-70" />
                        <span>=</span>
                        <span id="tp_rate">—</span>
                    </span>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 leading-relaxed pt-0.5">
                نسبة ربحك
                <bdi dir="ltr">{{ $fmtPct($trainerProfitPct) }}%</bdi>
                <span class="mx-1 text-slate-300">·</span>
                رسوم المنصة
                <bdi dir="ltr">{{ $fmtPct($platformFeePct) }}%</bdi>
            </p>
        </div>
    </div>
</div>
