@extends('layouts.app')

@section('title', 'تعديل سجل الخصم/المكافأة')

@section('content')
<section class="p-3 sm:p-5">
    {{-- إضافة Breadcrumb للرجوع السهل --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.adjustments.index') }}" second="التسويات المالية"
        third="تعديل السجل" />

    <div
        class="mx-auto max-w-2xl bg-white dark:bg-gray-800 p-6 shadow-xl rounded-xl border border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-2 mb-6 border-b pb-4">
            <i class="fas fa-edit text-blue-600"></i>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل سجل: {{ $adjustment->user->name }}</h2>
        </div>

        <form action="{{ route('dashboard.adjustments.update', $adjustment->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT') {{-- مهم جداً لعملية التحديث --}}

            {{-- اختيار الموظف --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">اختر الموظف</label>
                <select name="user_id" required
                    class="w-full border border-gray-300 rounded-lg p-2.5 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ $adjustment->user_id == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- النوع --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">النوع</label>
                    <select name="type" required
                        class="w-full border border-gray-300 rounded-lg p-2.5 dark:bg-gray-700 dark:text-white focus:ring-blue-500">
                        <option value="bonus" {{ $adjustment->type == 'bonus' ? 'selected' : '' }}>مكافأة (+)</option>
                        <option value="deduction" {{ $adjustment->type == 'deduction' ? 'selected' : '' }}>خصم (-)
                        </option>
                    </select>
                </div>

                {{-- المبلغ --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">المبلغ</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $adjustment->amount) }}"
                            required
                            class="w-full border border-gray-300 rounded-lg p-2.5 dark:bg-gray-700 dark:text-white focus:ring-blue-500">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">EGP</span>
                    </div>
                </div>
            </div>

            {{-- التاريخ --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">التاريخ</label>
                <input type="date" name="date" value="{{ old('date', $adjustment->date->format('Y-m-d')) }}" required
                    class="w-full border border-gray-300 rounded-lg p-2.5 dark:bg-gray-700 dark:text-white focus:ring-blue-500">
            </div>

            {{-- ملاحظات --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">ملاحظات</label>
                <textarea name="notes" rows="3" placeholder="اكتب تفاصيل المكافأة أو الخصم هنا..."
                    class="w-full border border-gray-300 rounded-lg p-2.5 dark:bg-gray-700 dark:text-white focus:ring-blue-500">{{ old('notes', $adjustment->notes) }}</textarea>
            </div>

            {{-- أزرار التحكم --}}
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition duration-200 shadow-md flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
                <a href="{{ route('dashboard.adjustments.index') }}"
                    class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-lg font-bold hover:bg-gray-200 text-center transition duration-200">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</section>
@endsection