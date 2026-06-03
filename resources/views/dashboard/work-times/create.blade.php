@extends('layouts.app')

@section('title', 'إضافة وقت عمل')

@section('content')
<style>
    /* تحسين شكل حقول الوقت والتاريخ */
    input[type="time"],
    input[type="date"] {
        appearance: none;
        -webkit-appearance: none;
        position: relative;
    }

    input[type="time"]::-webkit-calendar-picker-indicator,
    input[type="date"]::-webkit-calendar-picker-indicator {
        background: transparent;
        bottom: 0;
        color: transparent;
        cursor: pointer;
        height: auto;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        width: auto;
    }
</style>

<section class="p-3 sm:p-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.work-times.index') }}" second="الحضور والإنصراف"
        third="إضافة سجل وقت" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-xl">

            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-clock text-blue-600"></i>
                تسجيل وقت عمل جديد
            </h2>

            <form method="POST" action="{{ route('dashboard.work-times.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="timezone" id="user_timezone">
                {{-- الموظف والبلد --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">اسم الموظف</label>
    <select name="user_id" id="employee_select"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        <option selected disabled>اختر الموظف</option>
        @foreach($employees as $emp)
        <option value="{{ $emp->id }}"
            data-country-code="{{ $emp->country_code }}"
            data-country-name="{{ e($emp->country_name) }}"
            data-work-start="{{ $emp->work_start }}">
            {{ $emp->name }}
        </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الدولة</label>
    <input type="hidden" name="country" id="country_code_input" value="{{ old('country') }}">
    <div id="country_display"
        class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full px-3 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 cursor-not-allowed select-none">
        <span id="country_display_text">اختر الموظف أولاً</span>
    </div>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">تُحدَّد تلقائياً من ملف الموظف ولا يمكن تعديلها.</p>
    <x-input-error :messages="$errors->get('country')" class="mt-2" />
</div>
                </div>

                {{-- وقت الدولة المحلي --}}
                <div id="country_time_panel"
                    class="hidden p-4 rounded-xl border border-blue-200 bg-blue-50 dark:bg-gray-700 dark:border-blue-900 space-y-2">
                    <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 flex items-center gap-2">
                        <i class="fas fa-globe-americas"></i>
                        <span id="country_time_title">التوقيت المحلي للدولة</span>
                    </h4>
                    <p id="country_time_label" class="text-lg font-black text-gray-900 dark:text-white font-mono">--</p>
                    <p id="country_timezone_label" class="text-xs text-gray-600 dark:text-gray-400"></p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        بداية الدوام المعتادة:
                        <span id="country_work_start" class="font-bold">09:00</span>
                        (حسب إعدادات الموظف أو 9 صباحاً)
                    </p>
                    <p id="ip_detect_hint" class="text-xs text-amber-700 dark:text-amber-300 hidden"></p>
                    <button type="button" id="apply_country_now_btn"
                        class="text-xs px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        استخدام الوقت والتاريخ الحاليين للدولة
                    </button>
                </div>

                {{-- نوع الحركة --}}
                <div>
                    <label class="block mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">نوع الحركة</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(['حضور', 'انصراف', 'خروج للاستراحة', 'دخول من الاستراحة'] as $status)
                        <label
                            class="flex items-center justify-center gap-2 p-3 border-2 border-gray-100 dark:border-gray-700 rounded-xl cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition-all group">
                            <input type="radio" name="type" value="{{ $status }}"
                                class="w-4 h-4 text-blue-600 focus:ring-blue-500" required>
                            <span
                                class="text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-blue-700">{{
                                $status }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- التاريخ والوقت --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">التاريخ</label>
                        <div class="relative">
                            <input type="date" name="date" value="{{ date('Y-m-d') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">وقت
                            البدء</label>
                        <input type="time" id="start_time" name="start_time"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                    </div>
                    {{-- <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">وقت
                            الانتهاء</label>
                        <input type="time" id="end_time" name="end_time"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div> --}}
                </div>

                {{-- الملاحظات --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">ملاحظات
                        إضافية</label>
                    <textarea name="notes" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="اكتب أي ملاحظات هنا..."></textarea>
                    <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        يُسجَّل الوقت حسب توقيت الدولة المختارة (وليس توقيت جهازك)، مع عرض الساعة المحلية أعلاه.
                    </p>
                </div>

                {{-- زر الحفظ --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-lg text-lg px-5 py-3.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all shadow-lg">
                        <i class="fas fa-save ml-2"></i> حفظ السجل
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    let countryClockTimer = null;
    let activeTimezone = null;
    let lastCountryPayload = null;

    const tzInput = document.getElementById('user_timezone');
    const dateInput = document.querySelector('input[name="date"]');
    const timeInput = document.getElementById('start_time');
    const panel = document.getElementById('country_time_panel');
    const countryCodeInput = document.getElementById('country_code_input');
    const countryDisplayText = document.getElementById('country_display_text');

    function setEmployeeCountry(code, name) {
        if (code) {
            countryCodeInput.value = code;
            countryDisplayText.textContent = name || code;
        } else {
            countryCodeInput.value = '';
            countryDisplayText.textContent = 'اختر الموظف أولاً';
        }
    }

    function formatInTimezone(timezone) {
        return new Intl.DateTimeFormat('ar-EG', {
            timeZone: timezone,
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
        }).format(new Date());
    }

    function formatTimeOnly(timezone) {
        return new Intl.DateTimeFormat('en-GB', {
            timeZone: timezone,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }).format(new Date());
    }

    function formatDateOnly(timezone) {
        const parts = new Intl.DateTimeFormat('en-CA', {
            timeZone: timezone,
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
        }).formatToParts(new Date());
        const y = parts.find(p => p.type === 'year')?.value;
        const m = parts.find(p => p.type === 'month')?.value;
        const d = parts.find(p => p.type === 'day')?.value;
        return `${y}-${m}-${d}`;
    }

    function startCountryClock(timezone) {
        activeTimezone = timezone;
        if (countryClockTimer) clearInterval(countryClockTimer);
        const tick = () => {
            if (!activeTimezone) return;
            document.getElementById('country_time_label').textContent = formatInTimezone(activeTimezone);
        };
        tick();
        countryClockTimer = setInterval(tick, 1000);
    }

    function applyCountryFields(payload, applyToForm) {
        lastCountryPayload = payload;
        if (!payload?.timezone) return;

        panel.classList.remove('hidden');
        tzInput.value = payload.timezone;
        document.getElementById('country_timezone_label').textContent =
            `المنطقة الزمنية: ${payload.timezone}` + (payload.country_code ? ` (${payload.country_code})` : '');
        if (payload.work_start) {
            document.getElementById('country_work_start').textContent = payload.work_start;
        }
        startCountryClock(payload.timezone);

        if (applyToForm) {
            dateInput.value = payload.date || formatDateOnly(payload.timezone);
            timeInput.value = payload.time || formatTimeOnly(payload.timezone).slice(0, 5);
        }
    }

    async function fetchCountryTime(countryCode, userId, applyToForm = false) {
        if (!countryCode) {
            panel.classList.add('hidden');
            return;
        }
        const params = new URLSearchParams({ country: countryCode });
        if (userId) params.set('user_id', userId);
        const res = await fetch(`{{ route('dashboard.work-times.country-time') }}?${params}`);
        const data = await res.json();
        if (!res.ok) return;
        applyCountryFields(data, applyToForm);
    }

    document.getElementById('apply_country_now_btn').addEventListener('click', function() {
        if (lastCountryPayload) {
            applyCountryFields(lastCountryPayload, true);
        } else if (activeTimezone) {
            dateInput.value = formatDateOnly(activeTimezone);
            timeInput.value = formatTimeOnly(activeTimezone).slice(0, 5);
        }
    });

    document.getElementById('employee_select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const code = opt?.getAttribute('data-country-code');
        const name = opt?.getAttribute('data-country-name');
        const workStart = opt?.getAttribute('data-work-start');
        setEmployeeCountry(code, name);
        if (code) {
            document.getElementById('country_time_title').textContent =
                name ? `التوقيت المحلي — ${name}` : 'التوقيت المحلي للدولة';
        } else {
            panel.classList.add('hidden');
        }
        if (workStart) {
            document.getElementById('country_work_start').textContent = workStart;
        }
        fetchCountryTime(code, this.value, true);
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        if (!countryCodeInput.value) {
            e.preventDefault();
            alert('اختر الموظف أولاً لتحديد الدولة.');
        }
    });

    fetch(`{{ route('dashboard.work-times.country-time') }}?use_ip=1`)
        .then(r => r.json())
        .then(data => {
            if (data.source === 'ip' && data.country_code) {
                const hint = document.getElementById('ip_detect_hint');
                hint.textContent = `تقدير من IP: ${data.country_name || data.country_code}`;
                hint.classList.remove('hidden');
            }
        })
        .catch(() => {});

    document.querySelectorAll('input[type="time"], input[type="date"]').forEach(input => {
        input.addEventListener('click', function() {
            try {
                if ('showPicker' in HTMLInputElement.prototype) this.showPicker();
            } catch (e) {}
        });
    });
</script>
@endsection