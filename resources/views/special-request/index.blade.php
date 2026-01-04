@extends('layouts.user')

@section('title', 'طلبات خاصة')

@section('content')

<div class="max-w-4xl my-5 border mx-auto p-6 lg:p-8 bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">✨ نموذج طلب نظام إلكتروني خاص</h2>
    <form action="{{ route('special-request.store') }}" method="POST">
        @csrf

        <div class="mb-8 p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h3 class="text-xl font-semibold text-red-600  dark:text-red-400 mb-4 border-b pb-2">1. معلومات أساسية
            </h3>

            <div class="mb-4">
                <x-input-label for="title" :value="__('عنوان الطلب/المشروع (مطلوب)')" />
                <x-text-input id="title" class="block mt-1 w-full focus:border-red-500 focus:ring-red-500" type="text"
                    name="title" :value="old('title')" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="project_type" :value="__('نوع الطلب (مطلوب)')" />
                <select id="project_type" name="project_type"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 focus:ring-red-500 dark:focus:ring-red-600 rounded-md shadow-sm block mt-1 w-full"
                    required>
                    <option class="text-gray-500">-- اختر نوع الطلب --</option>
                    @foreach ($services as $service)
                    <option value="{{ $service->name_ar }}">
                        {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                    </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('project_type')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="description" :value="__('وصف مفصل للمشروع وفكرته (مطلوب)')" />
                <textarea id="description" name="description" rows="5"
                    placeholder="ما هي المشكلة التي يحلها النظام؟ وما هي أهدافه؟"
                    class="placeholder-gray-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 rounded-md shadow-sm block mt-1 w-full"
                    required>{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <div class="mb-8 p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h3 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4 border-b pb-2">2. الميزات
                والوظائف</h3>

            <div class="mb-4">
                <x-input-label for="core_features" :value="__('قائمة بالوظائف الأساسية المطلوبة (لكل مستوى مستخدم)')" />
                <textarea id="core_features" name="core_features" rows="4"
                    placeholder="مثال: 1. لوحة تحكم للمدير لإدارة العملاء. 2. خاصية الدفع عبر بطاقة الائتمان. 3. إشعارات فورية على الموبايل."
                    class="placeholder-gray-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 rounded-md shadow-sm block mt-1 w-full"
                    required>{{ old('core_features') }}</textarea>
                <x-input-error :messages="$errors->get('core_features')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="examples" :value="__('روابط لمواقع أو تطبيقات مشابهة تعجبك (اختياري)')" />
                <x-text-input id="examples"
                    class="focus:border-red-500 focus:ring-red-500 placeholder-gray-500 block mt-1 w-full" type="text"
                    name="examples" :value="old('examples')"
                    placeholder="أدخل الروابط هنا (مثال: https://example.com)" />
                <x-input-error :messages="$errors->get('examples')" class="mt-2" />
            </div>
        </div>

        <div class="mb-8 p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
            <h3 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4 border-b pb-2">3. الميزانية
                والجدول الزمني</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="budget" :value="__('الميزانية التقديرية (بالعملة المحلية أو الدولار)')" />
                    <x-text-input id="budget"
                        class="focus:border-red-500 focus:ring-red-500 placeholder-gray-500 block mt-1 w-full"
                        type="text" name="budget" :value="old('budget')" placeholder="مثال: 5000 - 8000 دولار" />
                    <x-input-error :messages="$errors->get('budget')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="deadline" :value="__('الموعد النهائي المطلوب للتسليم (اختياري)')" />
                    <input id="deadline" type="date" name="deadline"
                        class="placeholder-gray-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 rounded-md shadow-sm block mt-1 w-full">
                    <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit"
                class="w-full md:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                إرسال الطلب
            </button>
        </div>
    </form>
</div>
<script>
    function openSessionModal() {
        if("{{ session('success') }}") {
            Swal.fire({
                title: "{{ session('success') }}",
                text: "سعداء جداً بفرصة التعامل معك. سيتم التواصل معك قريباً لمناقشة التفاصيل",
                icon: "success"
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
    openSessionModal();
    });
</script>
@endsection