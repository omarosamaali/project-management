@extends('layouts.app')

@section('title', 'الأنظمة')

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

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
<section class="!px-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.systems.index') }}" second="الأنظمة" third="إضافة نظام" />
    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">

            {{-- display all errors --}}
            @foreach ($errors->all() as $error)
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            </div>
            @endforeach

            <form action="{{ route('dashboard.systems.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                <!-- معلومات أساسية -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        المعلومات الأساسية
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- اسم النظام -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                إسم النظام (بالعربي) <span class="text-black">*</span>
                            </label>
                            <input type="text" id="name_ar" name="name_ar" required
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="الإسم">
                            @error('name_ar')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- اسم النظام بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                System Name (English)
                            </label>
                            <input required type="text" id="name_en" name="name_en" dir="ltr"
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Name">
                            @error('name_en')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- السعر الكلي -->
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 mb-2">
                                السعر الكلي (<img src="{{ asset('assets/images/drhm-icon.svg') }}" />) <span
                                    class="text-black">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="price" required min="0" step="1"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="999">
                                @error('price')
                                <span class="text-black text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- مدة التنفيذ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                مدة التنفيذ <span class="text-black">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <input type="number" name="execution_days_from" required min="0"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="10">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">من</span>
                                    @error('execution_days_from')
                                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="relative">
                                    <input type="number" name="execution_days_to" required min="0"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="15">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">إلى</span>
                                    @error('execution_days_to')
                                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">يوم عمل</p>
                        </div>

                        {{-- مدة الدعم الفني --}}
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 mb-2">
                                مدة الدعم الفني (بالايام) <span class="text-black">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="support_days" required min="0" step="1"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="365 يوم">
                                @error('support_days')
                                <span class="text-black text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- نوع الخدمة --}}
                        <div class="mb-4">
                            <x-input-label for="service_id" :value="__('نوع الخدمة')" />
                            <select id="service_id" name="service_id"
                                class="mt-2 px-4 py-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm block w-full" required>
                                <option class="text-gray-500">-- اختر نوع الخدمة --</option>
                                @foreach ($services as $service)
                                <option value="{{ $service->id }}">
                                    {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                        </div>

                        {{-- مدة الدعم الفني --}}
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 mb-2">
                                بداية العداد <span class="text-black">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="counter" required min="0" step="1"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('counter')
                                <span class="text-black text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الوصف -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-align-right text-blue-600"></i>
                        الوصف
                    </h2>

                    <div class="space-y-4">
                        <!-- الوصف بالعربي -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                الوصف بالعربي <span class="text-black">*</span>
                            </label>
                            <textarea name="description_ar" id="description_ar" required rows="4"
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="نظام متكامل لإدارة المبيعات والمخزون، والعطاءات مع نظام محاسبي مبسط وواجهة سهلة الاستخدام"></textarea>
                            @error('description_ar')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الوصف بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                Description (English)
                            </label>
                            <textarea required name="description_en" id="description_en" rows="4" dir="ltr"
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Integrated system for sales and inventory management..."></textarea>
                            @error('description_en')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- المتطلبات -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-list-check text-blue-600"></i>
                        المتطلبات
                    </h2>

                    <div id="requirements-container" class="space-y-3">
                        <div class="flex gap-2 requirement-row">
                            <input type="text" name="requirements_ar[]"
                                class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="متطلب جديد">
                            <input type="text" name="requirements_en[]" dir="ltr"
                                class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="New Requirement">
                            @error('requirements_ar.*')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                            <button type="button"
                                class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-black">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                    </div>

                    <button type="button"
                        class="add-requirement-btn mt-4 flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus"></i>
                        إضافة متطلب
                    </button>
                </div>

                <!-- جميع المميزات -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-star text-blue-600"></i>
                        جميع المميزات
                    </h2>

                    <div id="features-container" class="space-y-3">
                        <!-- Feature 1 -->
                        <div class="flex gap-2 feature-row">
                            <input type="text" name="features_ar[]"
                                class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="لوحة تحكم احترافية">
                            <input type="text" name="features_en[]" dir="ltr"
                                class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Professional Dashboard">
                            @error('features_ar.*')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                            <button type="button"
                                class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-black">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="button"
                        class="add-feature-btn mt-4 flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus"></i>
                        إضافة ميزة
                    </button>
                </div>

                <!-- أزرار النظام -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-link text-blue-600"></i>
                        أزرار الإجراءات
                    </h2>

                    <div id="buttons-container" class="space-y-4">
                        <!-- Button 1 -->
                        <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid md:grid-cols-2 gap-4 mb-3">
                                <!-- محتوى الزر بالعربي -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        محتوى الزر (عربي)
                                    </label>
                                    <input type="text" name="buttons_text_ar[]"
                                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="اطلب الآن">
                                </div>

                                <!-- محتوى الزر بالإنجليزي -->
                                <div>
                                    <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                        Button Text (English)
                                    </label>
                                    <input type="text" name="buttons_text_en[]" dir="ltr"
                                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Order Now">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- اللينك -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        رابط الزر
                                    </label>
                                    <input type="url" name="buttons_link[]" dir="ltr"
                                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="https://example.com">
                                </div>

                                <!-- اللون -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        لون الزر
                                    </label>
                                    <div class="flex gap-2">
                                        <input type="color" name="buttons_color[]" value="#3B82F6"
                                            class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" name="buttons_color_hex[]" value="#3B82F6" dir="ltr"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                            placeholder="#3B82F6" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- زر الحذف -->
                            <div class="flex justify-end mt-3">
                                <button type="button"
                                    class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-black flex items-center gap-2">
                                    <i class="fas fa-trash"></i>
                                    حذف الزر
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="add-button-btn mt-4 flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus"></i>
                        إضافة زر جديد
                    </button>
                </div>

                <!-- الصور -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-image text-blue-600"></i>
                        الصور
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- الصورة الرئيسية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                الصورة الرئيسية <span class="text-black">*</span>
                            </label>

                            <div
                                class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                                <input id="main_image_input" type="file" name="main_image" accept="image/*" required
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">اضغط أو اسحب الصورة هنا</p>
                            </div>
                            @error('main_image')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                            <!-- المعاينة -->
                            <div id="main_preview_container" class="mt-3 hidden relative w-full h-56">
                                <img id="main_image_preview" class="w-full h-full object-cover rounded-lg border" />
                                <!-- زر حذف -->
                                <button onclick="removeMainImage()"
                                    class="absolute top-1 right-1 bg-black text-white w-7 h-7 flex items-center justify-center rounded-full shadow hover:bg-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <!-- صور إضافية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                صور إضافية
                            </label>

                            <div
                                class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                                <input id="extra_images_input" type="file" name="images[]" accept="image/*" multiple
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <i class="fas fa-images text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">يمكنك اختيار عدة صور</p>
                            </div>
                            @error('images.*')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                            <!-- معاينة الصور المتعددة -->
                            <div id="extra_images_preview" class="mt-3 flex flex-wrap gap-3"></div>
                        </div>
                    </div>

                </div>

                <div class="mb-4">
                    <label for="system_external" class="block text-sm font-medium text-gray-700 mb-1">
                        هل النظام خارجي <span class="text-black">*</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="system_external_toggle" name="system_external" value="1"
                            class="sr-only peer" {{ old('system_external') ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 select-none">نعم</span>
                    </label>
                    @error('system_external')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div id="external_url_container" class="{{ old('system_external') ? '' : 'hidden' }} mt-4 mb-6">
                    <label for="external_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        رابط النظام الخارجي <span class="text-black">*</span>
                    </label>
                    <input type="url" name="external_url" id="external_url" value="{{ old('external_url') }}"
                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="https://example.com">
                    @error('external_url')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="evorq_onwer" class="block text-sm font-medium text-gray-700 mb-1">
                        هل تملك Evorq النظام <span class="text-black">*</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">

                        <input type="checkbox" id="evorq_onwer_toggle" name="evorq_onwer" value="1" class="sr-only peer"
                            {{ old('evorq_onwer') ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 select-none">لا</span>
                    </label>
                    @error('evorq_onwer')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div id="onwer_system_container" class="{{ old('evorq_onwer') ? '' : 'hidden' }} mt-4 mb-6">
                    <label for="onwer_system" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        اسم مالك النظام <span class="text-black">*</span>
                    </label>
                    <input type="text" name="onwer_system" id="onwer_system" value="{{ old('onwer_system') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('onwer_system')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        الحالة
                    </h2>

                    <div class="flex gap-4">
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-green-300 bg-green-50 rounded-lg cursor-pointer">
                            <input type="radio" name="status" value="active" checked class="w-5 h-5 text-green-600">
                            <span class="font-medium text-green-700">نشط</span>
                        </label>
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="status" value="inactive" class="w-5 h-5 text-gray-600">
                            <span class="font-medium text-gray-700">غير نشط</span>
                        </label>
                    </div>
                    @error('status')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-save ml-2"></i>
                        حفظ النظام
                    </button>
                    <button type="reset"
                        class="px-8 bg-gray-200 text-gray-700 py-4 rounded-lg font-bold hover:bg-gray-300 transition">
                        <i class="fas fa-redo ml-2"></i>
                        إعادة تعيين
                    </button>
                </div>
            </form>

<script>
    /**
 * نسخة محسّنة من سكريبت الترجمة التلقائية
 * الحل: دالة واحدة فقط + معالجة أفضل للأخطاء
 */

(function() {
    'use strict';

    /**
     * دالة الترجمة باستخدام Google Translate API (غير رسمي)
     * مع معالجة محسّنة للنصوص الطويلة والأسطر المتعددة
     */
// في حالة CORS blocking، استخدم LibreTranslate أو MyMemory API بدل Google:

async function translateText(text, sourceLang, targetLang) {
const url =
`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=${sourceLang}|${targetLang}`;

try {
const response = await fetch(url);
const data = await response.json();
return data.responseData.translatedText || text;
} catch (error) {
console.error('Translation error:', error);
return text;
}
}
    /**
     * إعداد الترجمة التلقائية لحقول النصوص
     */
    function setupFieldTranslation(sourceId, targetId, fromLang, toLang, delay = 1000) {
        const sourceInput = document.getElementById(sourceId);
        const targetInput = document.getElementById(targetId);
        
        if (!sourceInput || !targetInput) {
            console.warn(`Translation setup failed: ${sourceId} -> ${targetId}`);
            return;
        }

        let translationTimer = null;
        let lastTranslatedValue = '';

        sourceInput.addEventListener('input', function(e) {
            const currentValue = e.target.value.trim();
            
            // إلغاء المؤقت السابق
            if (translationTimer) {
                clearTimeout(translationTimer);
            }

            // عدم الترجمة إذا كان النص فارغًا أو نفس القيمة السابقة
            if (!currentValue || currentValue === lastTranslatedValue) {
                return;
            }

            // تأخير الترجمة حتى يتوقف المستخدم عن الكتابة
            translationTimer = setTimeout(async () => {
                try {
                    const translatedText = await translateText(currentValue, fromLang, toLang);
                    
                    // تحديث الحقل المستهدف فقط إذا كانت الترجمة مختلفة
                    if (translatedText && translatedText !== targetInput.value) {
                        targetInput.value = translatedText;
                        lastTranslatedValue = currentValue;
                        
                        // إطلاق حدث input للحقل المترجم (للتوافق مع Laravel Livewire إن وُجد)
                        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                } catch (error) {
                    console.error('Translation failed:', error);
                }
            }, delay);
        });
    }

    /**
     * إعداد الترجمة للحقول الديناميكية (المميزات والمتطلبات)
     */
    function setupDynamicTranslation(containerId, rowClass, arName, enName) {
        const container = document.getElementById(containerId);
        
        if (!container) {
            console.warn(`Container not found: ${containerId}`);
            return;
        }

        // استخدام Event Delegation لدعم العناصر المضافة ديناميكيًا
        container.addEventListener('input', function(e) {
            const isArabic = e.target.name === arName;
            const isEnglish = e.target.name === enName;

            if (!isArabic && !isEnglish) return;

            const row = e.target.closest(rowClass);
            if (!row) return;

            const targetInput = row.querySelector(
                `input[name="${isArabic ? enName : arName}"]`
            );

            if (!targetInput) return;

            // إلغاء المؤقت السابق لهذا العنصر
            if (e.target.translationTimer) {
                clearTimeout(e.target.translationTimer);
            }

            const currentValue = e.target.value.trim();
            if (!currentValue) return;

            // تأخير الترجمة
            e.target.translationTimer = setTimeout(async () => {
                try {
                    const translated = await translateText(
                        currentValue,
                        isArabic ? 'ar' : 'en',
                        isArabic ? 'en' : 'ar'
                    );
                    
                    if (translated && translated !== targetInput.value) {
                        targetInput.value = translated;
                        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                } catch (error) {
                    console.error('Dynamic translation failed:', error);
                }
            }, 1000);
        });
    }

    /**
     * إعداد Toggle للحقول المشروطة
     */
    function setupToggle(toggleId, containerId, inputId) {
        const toggle = document.getElementById(toggleId);
        const container = document.getElementById(containerId);
        const input = document.getElementById(inputId);
        
        if (!toggle || !container || !input) return;

        function updateVisibility(isChecked) {
            if (isChecked) {
                container.classList.remove('hidden');
                input.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                input.removeValue('required');
            }
        }

        toggle.addEventListener('change', (e) => updateVisibility(e.target.checked));
        updateVisibility(toggle.checked); // الحالة الأولية
    }

    /**
     * تهيئة كل شيء عند تحميل الصفحة
     */
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Translation system initialized');

        // 1. ترجمة حقول الاسم
        setupFieldTranslation('name_ar', 'name_en', 'ar', 'en', 800);
        setupFieldTranslation('name_en', 'name_ar', 'en', 'ar', 800);

        // 2. ترجمة حقول الوصف (مهلة أطول للنصوص الطويلة)
        setupFieldTranslation('description_ar', 'description_en', 'ar', 'en', 1500);
        setupFieldTranslation('description_en', 'description_ar', 'en', 'ar', 1500);

        // 3. ترجمة المميزات (Features)
        setupDynamicTranslation(
            'features-container',
            '.feature-row',
            'features_ar[]',
            'features_en[]'
        );

        // 4. ترجمة المتطلبات (Requirements)
        setupDynamicTranslation(
            'requirements-container',
            '.requirement-row',
            'requirements_ar[]',
            'requirements_en[]'
        );

        // 5. إعداد Toggles
        setupToggle('system_external_toggle', 'external_url_container', 'external_url');
        setupToggle('evorq_onwer_toggle', 'onwer_system_container', 'onwer_system');
    });

})();
</script>        </div>
    </div>
</section>

@endsection