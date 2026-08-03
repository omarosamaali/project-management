@extends('layouts.app')

@section('title', 'أرباح المحاضرين')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="الأكاديمية" third="أرباح المحاضرين" />

    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <p class="text-sm text-slate-500">عدد المحاضرين</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['trainers_count'] }}</p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <p class="text-sm text-slate-500">إجمالي الاشتراكات</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ $summary['subscriptions_count'] }}</p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <p class="text-sm text-slate-500">إجمالي المبيعات</p>
                <p class="text-2xl font-black text-slate-900 mt-2 inline-flex items-center gap-1.5">
                    {{ number_format($summary['gross_revenue'], 2) }}
                    <x-drhm-icon width="16" height="18" />
                </p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <p class="text-sm text-slate-500">أرباح المحاضرين</p>
                <p class="text-2xl font-black text-green-700 mt-2 inline-flex items-center gap-1.5">
                    {{ number_format($summary['trainer_profit'], 2) }}
                    <x-drhm-icon width="16" height="18" />
                </p>
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <p class="text-sm text-slate-500">أرباح المنصة</p>
                <p class="text-2xl font-black text-slate-900 mt-2 inline-flex items-center gap-1.5">
                    {{ number_format($summary['platform_profit'] ?? 0, 2) }}
                    <x-drhm-icon width="16" height="18" />
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="p-4 border-b bg-slate-50 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">تقرير أرباح المحاضرين</h2>
                    <p class="text-sm text-slate-500">الاحتساب من لقطات الربح المخزّنة على كل اشتراك (حسب نوع الدورة وقت الدفع).</p>
                </div>
                <a href="{{ route('dashboard.academy.settings.edit') }}" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#0D2444;">إعدادات الأكاديمية</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right min-w-[720px]">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3">المحاضر</th>
                            <th class="px-4 py-3">الدورات</th>
                            <th class="px-4 py-3">الاشتراكات</th>
                            <th class="px-4 py-3">إجمالي المبيعات</th>
                            <th class="px-4 py-3">ربح المحاضر</th>
                            <th class="px-4 py-3 text-center">تفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainers as $trainer)
                        @php $rowId = 'trainer-courses-' . $loop->index; @endphp
                        <tr class="border-t hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $trainer['name'] }}</td>
                            <td class="px-4 py-4">{{ $trainer['courses_count'] }}</td>
                            <td class="px-4 py-4">{{ $trainer['subscriptions_count'] }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1">
                                    {{ number_format($trainer['gross_revenue'], 2) }}
                                    <x-drhm-icon width="12" height="14" />
                                </span>
                            </td>
                            <td class="px-4 py-4 font-bold text-green-700">
                                <span class="inline-flex items-center gap-1">
                                    {{ number_format($trainer['trainer_profit'], 2) }}
                                    <x-drhm-icon width="12" height="14" />
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button type="button"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white transition"
                                    style="background:#0D2444;"
                                    data-profit-toggle
                                    data-target="{{ $rowId }}"
                                    aria-expanded="false">
                                    <i class="fas fa-list"></i>
                                    <span data-toggle-label>عرض التفاصيل</span>
                                </button>
                            </td>
                        </tr>
                        <tr id="{{ $rowId }}" class="hidden border-t bg-slate-50/70">
                            <td colspan="6" class="px-4 py-4">
                                <div class="rounded-xl border bg-white overflow-hidden">
                                    <div class="px-4 py-3 border-b bg-slate-50">
                                        <p class="text-sm font-bold text-slate-800">تفاصيل دورات — {{ $trainer['name'] }}</p>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-right">
                                            <thead class="bg-slate-50 text-slate-600 text-xs">
                                                <tr>
                                                    <th class="px-4 py-2.5">الدورة</th>
                                                    <th class="px-4 py-2.5">الاشتراكات</th>
                                                    <th class="px-4 py-2.5">المبيعات</th>
                                                    <th class="px-4 py-2.5">ربح المحاضر</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($trainer['courses'] as $course)
                                                <tr class="border-t">
                                                    <td class="px-4 py-3">
                                                        <p class="font-semibold text-slate-900">{{ $course['name_ar'] }}</p>
                                                        @if(!empty($course['name_en']))
                                                        <p class="text-xs text-slate-400 mt-0.5 dir-ltr text-left">{{ $course['name_en'] }}</p>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">{{ $course['subscriptions_count'] }}</td>
                                                    <td class="px-4 py-3">
                                                        <span class="inline-flex items-center gap-1">
                                                            {{ number_format($course['gross_revenue'], 2) }}
                                                            <x-drhm-icon width="12" height="14" />
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 font-bold text-green-700">
                                                        <span class="inline-flex items-center gap-1">
                                                            {{ number_format($course['trainer_profit'], 2) }}
                                                            <x-drhm-icon width="12" height="14" />
                                                        </span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="px-4 py-8 text-center text-slate-400">لا توجد دورات لهذا المحاضر.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-400">لا توجد أرباح محاضرين حتى الآن.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('[data-profit-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-target');
        const row = document.getElementById(targetId);
        const label = btn.querySelector('[data-toggle-label]');
        if (!row) return;

        const willOpen = row.classList.contains('hidden');

        // Close other open detail rows
        document.querySelectorAll('[id^="trainer-courses-"]').forEach((el) => {
            if (el !== row) el.classList.add('hidden');
        });
        document.querySelectorAll('[data-profit-toggle]').forEach((other) => {
            if (other !== btn) {
                other.setAttribute('aria-expanded', 'false');
                const otherLabel = other.querySelector('[data-toggle-label]');
                if (otherLabel) otherLabel.textContent = 'عرض التفاصيل';
            }
        });

        row.classList.toggle('hidden', !willOpen);
        btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        if (label) label.textContent = willOpen ? 'إخفاء التفاصيل' : 'عرض التفاصيل';
    });
});
</script>
@endsection
