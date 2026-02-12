@extends('layouts.app')

@section('title', 'إنشاء طلب جديد')

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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.requests.index') }}" second="المشاريع" third="إضافة طلب" />

    <div class="mx-auto max-w-6xl w-full rounded-xl">
        <div class="p-6 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">

            {{-- رسائل الأخطاء --}}
            @foreach ($errors->all() as $error)
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            </div>
            @endforeach

            <form method="POST" action="{{ route('dashboard.requests.post-request') }}" class="space-y-8">
                @csrf

                {{-- 1. معلومات العميل --}}
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        1. معلومات العميل
                    </h2>

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">العميل:</label>
                        <select name="user_id" id="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                            <option value="">-- عميل افتراضي (ايفورك للتكنولوجيا) --</option>
                            @foreach ($clients as $client)
                            <option value="{{ $client->id }}" {{ old('user_id')==$client->id ? 'selected' : '' }}>
                                {{ $client->name }} - {{ $client->email }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle"></i>
                            اترك هذا الحقل فارغاً لاستخدام العميل الافتراضي
                        </p>
                    </div>
                </div>

                {{-- 2. معلومات أساسية --}}
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        2. معلومات أساسية
                    </h2>

                    <div class="space-y-4">
                        {{-- رقم الطلب --}}
                        @php
                        $orderNumber = 'REQ' . time() . rand(1, 9);
                        @endphp
                        <div>
                            <label for="order_number" class="block text-sm font-medium text-gray-700 mb-1">
                                رقم الطلب:
                            </label>
                            <input type="text" id="order_number" value="{{ $orderNumber }}" disabled
                                class="cursor-not-allowed bg-gray-100 w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <input type="hidden" name="order_number" value="{{ $orderNumber }}">
                        </div>

                        {{-- عنوان الطلب --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                عنوان الطلب <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                            @error('title')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- نوع الطلب --}}
                        <div>
                            <label for="project_type" class="block text-sm font-medium text-gray-700 mb-1">
                                نوع الطلب <span class="text-red-600">*</span>
                            </label>
                            <select name="project_type" id="project_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                <option value="">-- اختر نوع الطلب --</option>
                                @foreach ($services as $service)
                                <option value="{{ $service->id }}" {{ old('project_type')==$service->id ? 'selected' :
                                    '' }}>
                                    {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_type')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الوصف --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                الوصف <span class="text-red-600">*</span>
                            </label>
                            <textarea name="description" id="description" rows="5" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">{{ old('description') }}</textarea>
                            @error('description')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- 3. الميزات والوظائف --}}
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cogs text-blue-600"></i>
                        3. الميزات والوظائف
                    </h2>

                    <div class="space-y-4">
                        {{-- الميزات الأساسية --}}
                        <div>
                            <label for="core_features" class="block text-sm font-medium text-gray-700 mb-1">
                                الميزات الأساسية
                            </label>
                            <textarea name="core_features" id="core_features" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">{{ old('core_features') }}</textarea>
                        </div>

                        {{-- أمثلة أو مراجع --}}
                        <div>
                            <label for="examples" class="block text-sm font-medium text-gray-700 mb-1">
                                أمثلة أو مراجع
                            </label>
                            <textarea name="examples" id="examples" rows="3" placeholder="مثال: https://example.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">{{ old('examples') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 4. الميزانية والجدول الزمني --}}
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-dollar-sign text-blue-600"></i>
                        4. الميزانية والجدول الزمني
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- الميزانية المتوقعة --}}
                        <div>
                            <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">
                                الميزانية المتوقعة
                            </label>
                            <div class="flex items-center ">
                                <input type="number" name="budget" id="budget" step="0.01" value="{{ old('budget') }}"
                                class="w-[90%] px-4 py-2 border-s border-gray-300 rounded-s-lg focus:ring-blue-500
                                focus:border-blue-500 transition duration-150 ease-in-out">
                                <div class="px-3 py-2 w-[10%] flex border border-gray-300 items-center justify-center h-[42px] bg-[#eee] rounded-e-lg">
                                    <x-drhm-icon color="#333" width="18" height="18" />
                                </div>
                            </div>
                        </div>

                        {{-- موعد التسليم --}}
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                                موعد التسليم
                            </label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                        </div>

                        {{-- هل هو مشروع --}}
                        <div>
                            <label for="is_project" class="block text-sm font-medium text-gray-700 mb-1">
                                هل هو مشروع؟ <span class="text-red-600">*</span>
                            </label>
                            <select name="is_project" id="is_project" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                <option value="1" {{ old('is_project')=='1' ? 'selected' : '' }}>نعم</option>
                                <option value="0" {{ old('is_project')=='0' ? 'selected' : '' }}>لا</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 5. الحالة --}}
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        5. الحالة
                    </h2>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
    {{-- Active (نشط) --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-green-300 bg-green-50 rounded-lg cursor-pointer hover:bg-green-100">
        <input type="radio" name="status" value="active" checked class="w-5 h-5 text-green-600">
        <span class="font-medium text-green-700">نشط</span>
    </label>

    {{-- Pending (قيد الانتظار) --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-yellow-300 bg-yellow-50 rounded-lg cursor-pointer hover:bg-yellow-100">
        <input type="radio" name="status" value="pending" class="w-5 h-5 text-yellow-600">
        <span class="font-medium text-yellow-700">قيد الانتظار</span>
    </label>

    {{-- In Review (قيد المراجعة) --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-blue-300 bg-blue-50 rounded-lg cursor-pointer hover:bg-blue-100">
        <input type="radio" name="status" value="in_review" class="w-5 h-5 text-blue-600">
        <span class="font-medium text-blue-700">قيد المراجعة</span>
    </label>

    {{-- In Progress (قيد التنفيذ) --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-indigo-300 bg-indigo-50 rounded-lg cursor-pointer hover:bg-indigo-100">
        <input type="radio" name="status" value="in_progress" class="w-5 h-5 text-indigo-600">
        <span class="font-medium text-indigo-700">قيد التنفيذ</span>
    </label>

    {{-- Completed (مكتمل) --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-teal-300 bg-teal-50 rounded-lg cursor-pointer hover:bg-teal-100">
        <input type="radio" name="status" value="completed" class="w-5 h-5 text-teal-600">
        <span class="font-medium text-teal-700">مكتمل</span>
    </label>

    {{-- Canceled (ملغي) --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-red-300 bg-red-50 rounded-lg cursor-pointer hover:bg-red-100">
        <input type="radio" name="status" value="canceled" class="w-5 h-5 text-red-600">
        <span class="font-medium text-red-700">ملغي</span>
    </label>

    {{-- بإنتظار طلب --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-orange-300 bg-orange-50 rounded-lg cursor-pointer hover:bg-orange-100">
        <input type="radio" name="status" value="بإنتظار طلب" class="w-5 h-5 text-orange-600">
        <span class="font-medium text-orange-700">بإنتظار طلب</span>
    </label>

    {{-- بإنتظار عروض الأسعار --}}
    <label
        class="flex items-center gap-3 p-4 border-2 border-purple-300 bg-purple-50 rounded-lg cursor-pointer hover:bg-purple-100">
        <input type="radio" name="status" value="بإنتظار عروض الأسعار" class="w-5 h-5 text-purple-600">
        <span class="font-medium text-purple-700">بإنتظار عروض الأسعار</span>
    </label>
</div>                </div>

                {{-- زر الإرسال --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save"></i>
                        إرسال الطلب
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('#user_id, #project_type, #system_id').select2({
            placeholder: 'ابحث...',
            allowClear: true,
            dir: 'rtl',
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                },
                searching: function() {
                    return "جاري البحث...";
                }
            }
        });
    });
</script>
@endsection