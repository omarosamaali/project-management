@extends('layouts.app')

@section('title', 'تعديل سجل الراتب')

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
        third="تعديل سجل الراتب" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-6 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-user-edit text-blue-600"></i>
                تعديل راتب الموظف: {{ $salary->user->name }}
            </h2>

            <form method="POST" action="{{ route('dashboard.salaries.update', $salary->id) }}" class="space-y-6"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- المرفق الحالي --}}
                <div class="border-b pb-6">
                    <label for="attachment"
                        class="block text-xl font-semibold text-gray-800 dark:text-white mb-4 items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-blue-600"></i> صورة المرفق (سند التحويل)
                    </label>
                    <div class="relative">
                        <label for="attachment"
                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">اضغط لتغيير
                                        الصورة</span></p>
                            </div>
                            <input type="file" id="attachment" name="attachment" class="hidden" accept="image/*">
                        </label>
                        <div id="image-preview" class="mt-4 text-center">
                            @if($salary->attachment)
                            <img src="{{ asset('storage/' . $salary->attachment) }}"
                                class="max-w-xs mx-auto rounded-lg shadow-md border">
                            @else
                            <img src="" class="max-w-xs mx-auto rounded-lg shadow-md border hidden">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- اختيار الموظف --}}
                    <div>
                        <label for="user_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الموظف:</label>
                        <select name="user_id" id="employee_select" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-white">
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-country="{{ $emp->country_name }}"
                                data-salary="{{ $emp->salary_amount ?? $emp->salary_amount_scale ?? 0 }}" {{ $salary->
                                user_id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- الدولة --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الدولة:</label>
                        <input type="text" id="display_country" readonly value="{{ $salary->user->country_name }}"
                            class="bg-gray-100 dark:bg-gray-600 w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-500">
                    </div>
                </div>

                {{-- الحسابات المالية --}}
                <div class="p-4 bg-blue-50 dark:bg-gray-700 rounded-xl space-y-4 border border-blue-100">
                    <h3 class="text-blue-700 dark:text-blue-300 font-bold flex items-center gap-2">
                        <i class="fas fa-calculator"></i> الحسابات المالية (تحديث تلقائي)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- الراتب الأساسي (عرض فقط) --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">الراتب الأساسي</label>
                            <div class="relative">
                                <input type="number" id="base_salary" readonly
                                    value="{{ $salary->user->salary_amount ?? $salary->user->salary_amount_scale ?? 0 }}"
                                    class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-bold text-blue-700">
                                <span class="absolute left-3 top-2 text-xs text-gray-400">EGP</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">قيمة الإضافي
                                (+)</label>
                            <input type="number" step="0.01" name="overtime_value"
                                value="{{ old('overtime_value', $salary->overtime_value) }}"
                                class="calc-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500">
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">المترحل (+)</label>
                            <input type="number" step="0.01" name="carried_forward"
                                value="{{ old('carried_forward', $salary->carried_forward) }}"
                                class="calc-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500">
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-black dark:text-red-400">الخصم (-)</label>
                            <input type="number" step="0.01" name="deduction_value"
                                value="{{ old('deduction_value', $salary->deduction_value) }}"
                                class="calc-input w-full px-4 py-2 border border-red-300 rounded-lg focus:border-red-500">
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-sm font-bold text-blue-800 dark:text-blue-200 mb-1">إجمالي الراتب
                            المستحق النهائي:</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="total_due" id="total_due" readonly
                                value="{{ $salary->total_due }}"
                                class="w-full px-4 py-4 bg-blue-600 text-white text-2xl font-black rounded-lg shadow-inner cursor-not-allowed text-center">
                            <i class="fas fa-coins absolute right-4 top-5 text-blue-300 opacity-50"></i>
                        </div>
                    </div>
                </div>

                {{-- أزرار التحكم --}}
                <div class="pt-4 flex flex-col gap-3">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-lg shadow-sm text-lg font-bold text-white bg-blue-600 hover:bg-blue-700 transition">
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('dashboard.salaries.index') }}"
                        class="w-full text-center py-3 px-4 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                        إلغاء والعودة
                    </a>
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
                const img = document.getElementById('image-preview').querySelector('img');
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // 2. تحديث الدولة والراتب الأساسي عند تغيير الموظف
    document.getElementById('employee_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        // تحديث الدولة
        const country = selectedOption.getAttribute('data-country');
        document.getElementById('display_country').value = country || 'غير محدد';
        
        // تحديث الراتب الأساسي المظبوط في بيانات الموظف
        const salary = selectedOption.getAttribute('data-salary');
        document.getElementById('base_salary').value = parseFloat(salary).toFixed(2);
        
        calculate(); // إعادة الحساب فور تغيير الموظف
    });

    // 3. الحساب التلقائي الاحترافي
    const inputs = document.querySelectorAll('.calc-input');
    const totalInput = document.getElementById('total_due');
    const baseSalaryInput = document.getElementById('base_salary');

    function calculate() {
        let base = parseFloat(baseSalaryInput.value) || 0;
        let overtime = parseFloat(document.querySelector('[name="overtime_value"]').value) || 0;
        let carried = parseFloat(document.querySelector('[name="carried_forward"]').value) || 0;
        let deduction = parseFloat(document.querySelector('[name="deduction_value"]').value) || 0;
        
        // المعادلة: الأساسي + الإضافي + المترحل - الخصومات
        let total = (base + overtime + carried) - deduction;
        totalInput.value = total.toFixed(2);
    }

    inputs.forEach(input => input.addEventListener('input', calculate));
    
    // تشغيل الحساب لأول مرة عند تحميل الصفحة
    window.onload = calculate;
</script>

@endsection