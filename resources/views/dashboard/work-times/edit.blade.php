@extends('layouts.app')

@section('title', 'تعديل وقت العمل')

@section('content')
<section class="p-3 sm:p-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.work-times.index') }}" second="الحضور والإنصراف"
        third="تعديل سجل: {{ $workTime->user->name }}" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="p-6 bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 rounded-xl">

            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-edit text-orange-500"></i>
                تعديل بيانات السجل
            </h2>

            <form method="POST" action="{{ route('dashboard.work-times.update', $workTime->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <input type="hidden" name="timezone" id="user_timezone" value="{{ $workTime->timezone }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- الموظف --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">اسم
                            الموظف</label>
                        <select name="user_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $workTime->user_id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- البلد --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">البلد</label>
                        <input type="text" name="country" value="{{ $workTime->country }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                {{-- نوع الحركة --}}
                <div>
                    <label class="block mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">نوع الحركة</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(['حضور', 'انصراف', 'خروج للاستراحة', 'دخول من الاستراحة'] as $status)
                        <label
                            class="flex items-center justify-center gap-2 p-3 border-2 {{ $workTime->type == $status ? 'border-blue-500 bg-blue-50 dark:bg-gray-700' : 'border-gray-100 dark:border-gray-700' }} rounded-xl cursor-pointer hover:bg-blue-50 transition-all group">
                            <input type="radio" name="type" value="{{ $status }}" class="w-4 h-4 text-blue-600" {{
                                $workTime->type == $status ? 'checked' : '' }} required>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $status }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- التاريخ والوقت --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">التاريخ</label>
                        <input type="date" name="date" value="{{ $workTime->date }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">وقت
                            البدء</label>
                        <input type="time" name="start_time" value="{{ $workTime->start_time }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white"
                            required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">وقت
                            الانتهاء</label>
                        <input type="time" name="end_time" value="{{ $workTime->end_time }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                {{-- الملاحظات --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">ملاحظات
                        إضافية</label>
                    <textarea name="notes" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">{{ $workTime->notes }}</textarea>
                </div>

                {{-- الأزرار --}}
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg px-5 py-3 text-center transition-all shadow-lg">
                        تحديث البيانات
                    </button>
                    <a href="{{ route('dashboard.work-times.index') }}"
                        class="flex-1 text-gray-700 bg-gray-100 hover:bg-gray-200 font-bold rounded-lg px-5 py-3 text-center transition-all">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    // تفعيل اختيار الوقت عند الضغط
    document.querySelectorAll('input[type="time"], input[type="date"]').forEach(input => {
        input.addEventListener('click', function() {
            if ('showPicker' in HTMLInputElement.prototype) this.showPicker();
        });
    });
</script>
@endsection