@extends('layouts.app')

@section('title', 'إضافة سجل راتب')

@section('content')
<style>
    /* إخفاء أسهم الزيادة والنقصان في حقول الأرقام */
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

            <form method="POST" action="{{ route('dashboard.salaries.store') }}" class="space-y-6"
                enctype="multipart/form-data">
                @csrf

                {{-- رفع صورة المرفق --}}
                <div class="border-b pb-6">
                    <label for="attachment"
                        class="block text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-blue-600"></i> صورة المرفق (سند التحويل)
                    </label>
                    <div class="relative">
                        <label for="attachment"
                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-150">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">اضغط لرفع صورة</span>
                                </p>
                            </div>
                            <input type="file" id="attachment" name="attachment" class="hidden" accept="image/*">
                        </label>
                        <div id="image-preview" class="mt-4 hidden text-center">
                            <img src="" class="max-w-xs mx-auto rounded-lg shadow-md border">
                        </div>
                    </div>
                </div>

                {{-- اختيار الموظف وعرض الدولة --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم
                            الموظف:</label>
                        <select name="user_id" id="employee_select" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option selected disabled>اختر الموظف</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-country="{{ $emp->country_name }}"
                                data-salary="{{ $emp->salary_amount ?? $emp->salary_amount_scale ?? 0 }}"
                                data-currency="{{ $emp->salary_currency ?? $emp->salary_currency_scale ?? 'USD' }}">
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
                </div>

                {{-- لوحة ملخص الحضور التلقائية --}}
                <div id="attendance_summary"
                    class="hidden p-4 bg-orange-50 dark:bg-gray-700 border border-orange-200 dark:border-orange-900 rounded-xl space-y-2">
                    <h4 class="text-orange-700 dark:text-orange-400 font-bold text-sm flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> ملخص حضور الشهر الحالي
                    </h4>
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

                {{-- الجزء الخاص بالحسابات المالية --}}
                <div
                    class="p-4 bg-blue-50 dark:bg-gray-700 rounded-xl space-y-4 border border-blue-100 dark:border-gray-600">
                    <h3 class="text-blue-700 dark:text-blue-300 font-bold flex items-center gap-2">
                        <i class="fas fa-calculator"></i> تفاصيل الراتب (الحساب تلقائي)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- الراتب الأساسي --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">الراتب الأساسي</label>
                            <div class="relative">
                                <input type="number" id="base_salary" readonly value="0"
                                    class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-bold text-blue-700">
                                <span
                                    class="currency-label absolute left-2 top-2 text-[10px] bg-blue-100 text-blue-600 px-1 rounded">--</span>
                            </div>
                        </div>

                        {{-- الإضافي --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">الإضافي (+)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="overtime_value" id="overtime_value" value="0"
                                    class="calc-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-gray-400">--</span>
                            </div>
                        </div>

                        {{-- المترحل --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">المترحل (+)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="carried_forward" id="carried_forward" value="0"
                                    class="calc-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-gray-400">--</span>
                            </div>
                        </div>

                        {{-- الخصم --}}
                        <div>
                            <label class="text-xs font-semibold text-black dark:text-red-400">الخصم (-)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="deduction_value" id="deduction_value" value="0"
                                    class="calc-input w-full px-4 py-2 border border-red-300 rounded-lg focus:border-red-500 text-black font-bold">
                                <span class="currency-label absolute left-2 top-2 text-[10px] text-red-300">--</span>
                            </div>
                        </div>
                    </div>

                    {{-- الإجمالي النهائي --}}
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
    // 1. معاينة الصورة
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

    // 2. تحديث البيانات عند اختيار الموظف
// 2. تحديث البيانات عند اختيار الموظف
document.getElementById('employee_select').addEventListener('change', function() {
const userId = this.value;
const selectedOption = this.options[this.selectedIndex];

// تحديث النصوص الأساسية
const country = selectedOption.getAttribute('data-country');
const salary = parseFloat(selectedOption.getAttribute('data-salary')) || 0;
const currency = selectedOption.getAttribute('data-currency') || 'USD';

document.getElementById('display_country').value = country || 'غير محدد';
document.getElementById('base_salary').value = salary.toFixed(2);
document.querySelectorAll('.currency-label').forEach(el => el.innerText = currency);
document.getElementById('final_currency').innerText = currency;

// طلب جلب بيانات الحضور والخصم والإضافي (AJAX)
fetch(`/dashboard/salaries/fetch-attendance/${userId}`)
.then(response => response.json())
.then(data => {
// إظهار ملخص الحضور
document.getElementById('attendance_summary').classList.remove('hidden');
document.getElementById('days_present').innerText = data.days_count + " يوم";
document.getElementById('late_minutes_count').innerText = data.late_minutes + " دقيقة";
document.getElementById('overtime_minutes_count').innerText = data.overtime_minutes + " دقيقة";

// حساب الصافي (الإضافي - الخصم)
const netAmount = data.overtime_amount - data.deduction_amount;
document.getElementById('auto_net_val').innerText = netAmount.toFixed(2) + " " + currency;

// ✅ وضع القيم في الحقول الصحيحة
document.getElementById('overtime_value').value = data.overtime_amount.toFixed(2);
document.getElementById('deduction_value').value = data.deduction_amount.toFixed(2);

// إعادة حساب الإجمالي النهائي
calculate();
})
.catch(err => {
console.error("خطأ في جلب بيانات الحضور:", err);
// في حالة الخطأ، نضع قيم افتراضية
document.getElementById('overtime_value').value = 0;
document.getElementById('deduction_value').value = 0;
calculate();
});
});

// 3. الحساب التلقائي الشامل
function calculate() {
const base = parseFloat(document.getElementById('base_salary').value) || 0;
const overtime = parseFloat(document.getElementById('overtime_value').value) || 0;
const carried = parseFloat(document.getElementById('carried_forward').value) || 0;
const deduction = parseFloat(document.getElementById('deduction_value').value) || 0;

const total = (base + overtime + carried) - deduction;
document.getElementById('total_due').value = total.toFixed(2);
}

// ربط الحقول بالعملية الحسابية
document.querySelectorAll('.calc-input').forEach(input => {
input.addEventListener('input', calculate);
});
</script>
@endsection