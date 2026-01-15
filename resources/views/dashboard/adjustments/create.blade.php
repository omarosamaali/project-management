@extends('layouts.app')
@section('title', 'إضافة سجل جديد')
@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-2xl bg-white p-6 shadow-xl rounded-xl">
        <h2 class="text-2xl font-bold mb-6 border-b pb-4">إضافة خصم أو مكافأة</h2>
        <form action="{{ route('dashboard.adjustments.store') }}" method="POST" class="space-y-4">
            @csrf
          <div>
            <label class="block mb-2 text-sm font-medium">اختر الموظف</label>
            <select name="user_id" id="employee_select" required class="w-full border rounded-lg p-2.5">
                <option value="" disabled selected>اختر موظفاً...</option>
                @foreach($employees as $emp)
                {{-- هنا نحدد العملة بناءً على النظام المستخدم للموظف --}}
                @php
                $currency = $emp->apply_salary_scale ? ($emp->salary_currency_scale ?? 'EGP') : ($emp->salary_currency ??
                'EGP');
                @endphp
                <option value="{{ $emp->id }}" data-currency="{{ $currency }}">
                    {{ $emp->name }}
                </option>
                @endforeach
            </select>
        </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium">النوع</label>
                    <select name="type" required class="w-full border rounded-lg p-2.5">
                        <option value="bonus">مكافأة (+)</option>
                        <option value="deduction">خصم (-)</option>
                    </select>
                </div>
               <div>
                <label class="block mb-2 text-sm font-medium">المبلغ</label>
                <div class="relative">
                    <input type="number" step="0.01" name="amount" required class="w-full border rounded-lg p-2.5 pl-12">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span id="currency_display" class="text-gray-500 font-bold">---</span>
                    </div>
                </div>
            </div>
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium">التاريخ</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                    class="w-full border rounded-lg p-2.5">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full border rounded-lg p-2.5"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700">حفظ
                السجل
            </button>
        </form>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.getElementById('employee_select');
        const currencyDisplay = document.getElementById('currency_display');

        employeeSelect.addEventListener('change', function() {
            // جلب الـ Option المختار حالياً
            const selectedOption = this.options[this.selectedIndex];
            
            // جلب العملة من خاصية data-currency
            const currency = selectedOption.getAttribute('data-currency');
            
            // تحديث النص الظاهر للمستخدم
            if (currency) {
                currencyDisplay.textContent = currency;
            } else {
                currencyDisplay.textContent = '---';
            }
        });
        
        // لتفعيل العملة في حالة صفحة التعديل (Edit) عند تحميل الصفحة لأول مرة
        if(employeeSelect.value) {
            employeeSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection