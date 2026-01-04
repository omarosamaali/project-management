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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.work-times.index') }}" second="أوقات العمل"
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
                <style>
                    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
                        display: none !important;
                    }

                    .select2-container,
                    .iti {
                        width: 100%;
                    }

                    .select2-container--default .select2-selection--single {
                        height: 49px !important;
                    }

                    .select2-container--default .select2-selection--single .select2-selection__placeholder,
                    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
                        position: relative !important;
                        top: 4px !important;
                    }

                    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
                        top: 9px !important;
                    }

                    .select2-container--default .select2-selection--single {
                        border: 1px solid #d1d5db !important;
                    }
                </style>
                {{-- الموظف والبلد --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">اسم
                            الموظف</label>
                        <select name="user_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option selected disabled>اختر الموظف</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الدولة</label>
                        <select id="country_select2" name="country"
                            class="!py-3 placeholder-gray-400 block mt-1 w-full rtl:text-right " required>
                            <option value="" disabled selected>... جاري تحميل الدول ...</option>
                        </select>
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">وقت
                            الانتهاء</label>
                        <input type="time" id="end_time" name="end_time"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
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
                        سيتم تسجيل التوقيت آلياً بناءً على توقيت بلدك المحلي.
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
    document.addEventListener('DOMContentLoaded', function() {
        // 1. جلب المنطقة الزمنية للمستخدم
        const tzInput = document.getElementById('user_timezone');
        if(tzInput) {
            tzInput.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
        }

        // 2. تفعيل فتح منتقي الوقت والتاريخ عند الضغط على أي مكان في الحقل
        const interactiveInputs = document.querySelectorAll('input[type="time"], input[type="date"]');
        
        interactiveInputs.forEach(input => {
            input.addEventListener('click', function() {
                try {
                    if ('showPicker' in HTMLInputElement.prototype) {
                        this.showPicker();
                    }
                } catch (error) {
                    console.warn('showPicker is not supported or failed');
                }
            });
        });
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
                const countryDataUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
                fetch(countryDataUrl)
                    .then(response => response.json())
                    .then(data => {
                        const selectElement = $('#country_select2');
                        selectElement.empty();
    
                        selectElement.append(new Option("اختر دولتك", "", true, true));
                        data.forEach(country => {
                            const countryName = country.translations.ara.common || country.name.common;
                            const countryCode = country.cca2;
                            const newOption = new Option(countryName, countryCode, false, false);
                            if ('{{ old('country') }}' === countryCode) {
                                newOption.selected = true;
                            }
    
                            selectElement.append(newOption);
                        });
    
                        selectElement.select2({
                            placeholder: "اختر دولتك",
                            allowClear: true,
                            dir: "rtl"
                        });
                    })
                    .catch(error => {
                        console.error('حدث خطأ أثناء تحميل قائمة الدول:', error);
                        $('#country_select2').empty().append(new Option("تعذر تحميل الدول", "", true, true));
                    });
            });
</script>
@endsection