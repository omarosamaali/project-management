@extends('layouts.app')

@section('title', 'إضافة سجل راتب')

@section('content')
<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>

<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.salaries.index') }}" second="رواتب الموظفين"
        third="إضافة سجل" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-6 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">

            @if($errors->any())
            <div
                class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 dark:bg-red-900/20 dark:text-red-300">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('success'))
            <div
                class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 dark:bg-green-900/20 dark:text-green-300 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('dashboard.salaries.store') }}" class="space-y-6"
                enctype="multipart/form-data">
                @csrf

                {{-- رفع صورة المرفق --}}
                <div class="border-b pb-6">
                    <label
                        class="block text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-blue-600"></i> صورة المرفق (سند التحويل)
                    </label>
                    <label for="attachment"
                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">اضغط لرفع صورة</span></p>
                        </div>
                        <input type="file" id="attachment" name="attachment" class="hidden" accept="image/*">
                    </label>
                    <div id="image-preview" class="mt-4 hidden text-center">
                        <img src="" class="max-w-xs mx-auto rounded-lg shadow-md border">
                    </div>
                </div>

                {{-- اختيار الموظف + الدولة + الشهر + السنة --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم
                            الموظف:</label>
                        <select name="user_id" id="employee_select" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('user_id') border-red-500 @enderror">
                            <option selected disabled>اختر الموظف</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-country="{{ $emp->country_name }}"
                                data-salary="{{ $emp->salary_amount ?? $emp->salary_amount_scale ?? 0 }}"
                                data-currency="{{ $emp->salary_currency ?? $emp->salary_currency_scale ?? 'USD' }}" {{
                                old('user_id')==$emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الدولة:</label>
                        <input type="text" id="display_country" readonly placeholder="ستظهر تلقائياً"
                            class="bg-gray-100 dark:bg-gray-600 w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-500 font-bold">
                    </div>

                    {{-- ✅ الشهر --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الشهر:</label>
                        <select name="month" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('month') border-red-500 @enderror">
                            <option value="">اختر الشهر</option>
                            @foreach(['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر']
                            as $i => $m)
                            <option value="{{ $i + 1 }}" {{ old('month', now()->month) == $i + 1 ? 'selected' : '' }}>{{
                                $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ✅ السنة --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">السنة:</label>
                        <select name="year" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('year') border-red-500 @enderror">
                            @for($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}
                            </option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- ملخص الخصومات والمكافآت (من شاشة الخصومات والمكافآت) --}}
                <div id="adjustments_summary"
                    class="hidden p-4 bg-purple-50 dark:bg-gray-700 border border-purple-200 dark:border-purple-900 rounded-xl space-y-2">
                    <h4 class="text-purple-800 dark:text-purple-300 font-bold text-sm flex items-center gap-2">
                        <i class="fas fa-percent"></i> خصومات ومكافآت الشهر (تلقائي من السجلات)
                    </h4>
                    <p id="adjustments_period" class="text-xs text-gray-500"></p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">مكافآت (+)</p>
                            <p id="adjustment_bonus_total" class="font-bold text-green-600">0.00</p>
                            <p id="adjustment_bonus_count" class="text-[10px] text-gray-400">0 سجل</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">خصومات (-)</p>
                            <p id="adjustment_deduction_total" class="font-bold text-red-600">0.00</p>
                            <p id="adjustment_deduction_count" class="text-[10px] text-gray-400">0 سجل</p>
                        </div>
                    </div>
                </div>

                {{-- لوحة ملخص الحضور --}}
                <div id="attendance_summary"
                    class="hidden p-4 bg-orange-50 dark:bg-gray-700 border border-orange-200 dark:border-orange-900 rounded-xl space-y-2">
                    <h4 class="text-orange-700 dark:text-orange-400 font-bold text-sm flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> ملخص حضور فترة الراتب
                    </h4>
                    <p id="attendance_period" class="text-xs text-gray-500 dark:text-gray-400 mb-1"></p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">أيام الحضور</p>
                            <p id="days_present" class="font-bold">0</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">تأخير (دقائق)</p>
                            <p id="late_minutes_count" class="font-bold text-black">0</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">إضافي (دقائق)</p>
                            <p id="overtime_minutes_count" class="font-bold text-green-500">0</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">صافي (تلقائي)</p>
                            <p id="auto_net_val" class="font-bold text-blue-600">0.00</p>
                        </div>
                    </div>
                </div>

                {{-- تفاصيل الراتب --}}
                <div
                    class="p-4 bg-blue-50 dark:bg-gray-700 rounded-xl space-y-4 border border-blue-100 dark:border-gray-600">
                    <h3 class="text-blue-700 dark:text-blue-300 font-bold flex items-center gap-2">
                        <i class="fas fa-calculator"></i> تفاصيل الراتب (الحساب تلقائي)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">الراتب الأساسي</label>
                            <div class="relative">
                                <input type="number" id="base_salary" readonly value="0"
                                    class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-bold text-blue-700">
                                <span
                                    class="currency-label absolute left-2 top-2 text-[10px] bg-blue-100 text-blue-600 px-1 rounded">--</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">إضافي الحضور (+)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="overtime_value" id="overtime_value"
                                    value="{{ old('overtime_value', 0) }}"
                                    class="calc-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-gray-400">--</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-green-700 dark:text-green-400">مكافآت مسجّلة (+)</label>
                            <div class="relative">
                                <input type="number" step="0.01" id="adjustment_bonus_value" readonly value="0"
                                    class="w-full px-4 py-2 bg-green-50 border border-green-200 rounded-lg font-bold text-green-700">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-green-600">--</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">المترحل (+)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="carried_forward" id="carried_forward"
                                    value="{{ old('carried_forward', 0) }}"
                                    class="calc-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-gray-400">--</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-black dark:text-red-400">خصم الحضور (-)</label>
                            <div class="relative">
                                <input type="number" step="0.01" id="attendance_deduction_display" readonly value="0"
                                    class="w-full px-4 py-2 bg-red-50 border border-red-200 rounded-lg font-bold text-red-700">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-red-300">--</span>
                            </div>
                            <input type="hidden" name="attendance_deduction" id="attendance_deduction" value="0">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-black dark:text-red-400">خصومات مسجّلة (-)</label>
                            <div class="relative">
                                <input type="number" step="0.01" id="adjustment_deduction_value" readonly value="0"
                                    class="w-full px-4 py-2 bg-red-50 border border-red-200 rounded-lg font-bold text-red-700">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-red-300">--</span>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-black dark:text-red-400">إجمالي الخصم (حضور + مسجّل)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="deduction_value" id="deduction_value"
                                    value="{{ old('deduction_value', 0) }}" readonly
                                    class="w-full px-4 py-2 bg-gray-100 border border-red-300 rounded-lg font-bold text-red-800 cursor-not-allowed">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-red-300">--</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-sm font-bold text-blue-800 dark:text-blue-200 mb-1 text-center">الراتب
                            المستحق النهائي:</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="total_due" id="total_due" readonly
                                class="w-full px-4 py-5 bg-blue-600 text-white text-3xl font-black rounded-xl shadow-inner text-center cursor-not-allowed border-4 border-blue-400">
                            <span id="final_currency"
                                class="absolute left-6 top-6 text-xl text-blue-200 font-bold italic"></span>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-4 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-blue-600 hover:bg-blue-700 transition">
                        <i class="fas fa-save"></i> حفظ سجل الراتب
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    let attendanceDeduction = 0;
    let adjustmentBonus = 0;
    let adjustmentDeduction = 0;

    document.getElementById('attachment').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    function getSalaryPeriod() {
        const month = document.querySelector('[name="month"]')?.value;
        const year = document.querySelector('[name="year"]')?.value;
        return { month, year };
    }

    function applyDeductionFields() {
        document.getElementById('attendance_deduction').value = attendanceDeduction.toFixed(2);
        document.getElementById('attendance_deduction_display').value = attendanceDeduction.toFixed(2);
        document.getElementById('adjustment_bonus_value').value = adjustmentBonus.toFixed(2);
        document.getElementById('adjustment_deduction_value').value = adjustmentDeduction.toFixed(2);
        document.getElementById('deduction_value').value = (attendanceDeduction + adjustmentDeduction).toFixed(2);
    }

    function calculate() {
        const base = parseFloat(document.getElementById('base_salary').value) || 0;
        const overtime = parseFloat(document.getElementById('overtime_value').value) || 0;
        const carried = parseFloat(document.getElementById('carried_forward').value) || 0;
        const totalDeduction = attendanceDeduction + adjustmentDeduction;
        document.getElementById('total_due').value = (base + overtime + carried + adjustmentBonus - totalDeduction).toFixed(2);
    }

    function fetchAdjustments(userId, currency) {
        const { month, year } = getSalaryPeriod();
        if (!userId || !month || !year) {
            adjustmentBonus = 0;
            adjustmentDeduction = 0;
            document.getElementById('adjustments_summary').classList.add('hidden');
            applyDeductionFields();
            calculate();
            return;
        }

        fetch(`{{ url('/dashboard/salaries/fetch-adjustments') }}/${userId}?year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('adjustments_summary').classList.remove('hidden');
                document.getElementById('adjustments_period').innerText = data.period_label || '';
                adjustmentBonus = parseFloat(data.bonus_total) || 0;
                adjustmentDeduction = parseFloat(data.deduction_total) || 0;
                document.getElementById('adjustment_bonus_total').innerText = adjustmentBonus.toFixed(2) + ' ' + currency;
                document.getElementById('adjustment_deduction_total').innerText = adjustmentDeduction.toFixed(2) + ' ' + currency;
                document.getElementById('adjustment_bonus_count').innerText = (data.bonus_count || 0) + ' سجل';
                document.getElementById('adjustment_deduction_count').innerText = (data.deduction_count || 0) + ' سجل';
                applyDeductionFields();
                calculate();
            })
            .catch(() => {
                adjustmentBonus = 0;
                adjustmentDeduction = 0;
                applyDeductionFields();
                calculate();
            });
    }

    function loadSalaryData() {
        const select = document.getElementById('employee_select');
        const userId = select.value;
        const selectedOption = select.options[select.selectedIndex];
        const country = selectedOption.getAttribute('data-country');
        const salary = parseFloat(selectedOption.getAttribute('data-salary')) || 0;
        const currency = selectedOption.getAttribute('data-currency') || 'USD';

        document.getElementById('display_country').value = country || 'غير محدد';
        document.getElementById('base_salary').value = salary.toFixed(2);
        document.querySelectorAll('.currency-label').forEach(el => el.innerText = currency);
        document.getElementById('final_currency').innerText = currency;

        const { month, year } = getSalaryPeriod();
        if (!userId || !month || !year) {
            document.getElementById('attendance_summary').classList.add('hidden');
            return;
        }

        fetch(`{{ url('/dashboard/salaries/fetch-attendance') }}/${userId}?year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('attendance_summary').classList.remove('hidden');
                document.getElementById('attendance_period').innerText = data.period_label || data.range || '';
                document.getElementById('days_present').innerText = (data.days_count ?? 0) + ' يوم';
                document.getElementById('late_minutes_count').innerText = (data.late_minutes ?? 0) + ' دقيقة';
                document.getElementById('overtime_minutes_count').innerText = (data.overtime_minutes ?? 0) + ' دقيقة';
                const netAmount = parseFloat(data.net_amount ?? 0)
                    || ((parseFloat(data.overtime_amount) || 0) - (parseFloat(data.deduction_amount) || 0));
                document.getElementById('auto_net_val').innerText = netAmount.toFixed(2) + ' ' + currency;
                document.getElementById('overtime_value').value = (parseFloat(data.overtime_amount) || 0).toFixed(2);
                attendanceDeduction = parseFloat(data.deduction_amount) || 0;
                applyDeductionFields();
                fetchAdjustments(userId, currency);
            })
            .catch(() => {
                document.getElementById('attendance_summary').classList.add('hidden');
                attendanceDeduction = 0;
                document.getElementById('overtime_value').value = '0.00';
                applyDeductionFields();
                fetchAdjustments(userId, currency);
            });
    }

    document.getElementById('employee_select').addEventListener('change', loadSalaryData);
    document.querySelector('[name="month"]')?.addEventListener('change', loadSalaryData);
    document.querySelector('[name="year"]')?.addEventListener('change', loadSalaryData);

    document.querySelectorAll('.calc-input').forEach(input => {
        input.addEventListener('input', calculate);
    });
</script>
@endsection