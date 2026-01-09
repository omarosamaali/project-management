@extends('layouts.app')

@section('title', 'تعديل النظام')

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

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.systems.index') }}" second="الأنظمة"
        third="تعديل النظام" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <form action="{{ route('dashboard.systems.update', $system->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- معلومات أساسية -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        المعلومات الأساسية
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- اسم النظام بالعربي -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                إسم النظام (بالعربي) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name_ar" name="name_ar" required
                                value="{{ old('name_ar', $system->name_ar) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="الإسم">
                            @error('name_ar')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- اسم النظام بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 dark:text-gray-300 mb-2">
                                System Name (English) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name_en" name="name_en" required dir="ltr"
                                value="{{ old('name_en', $system->name_en) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Name">
                            @error('name_en')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- السعر -->
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                السعر الكلي <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price" required min="0" step="0.01"
                                value="{{ old('price', $system->price) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="999">
                            @error('price')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- مدة التنفيذ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                مدة التنفيذ <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <input type="number" name="execution_days_from" required min="0"
                                        value="{{ old('execution_days_from', $system->execution_days_from) }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="10">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">من</span>
                                </div>
                                <div class="relative">
                                    <input type="number" name="execution_days_to" required min="0"
                                        value="{{ old('execution_days_to', $system->execution_days_to) }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="15">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">إلى</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">يوم عمل</p>
                        </div>

                        {{-- مدة الدعم الفني --}}
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 mb-2">
                                مدة الدعم الفني (بالايام) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input value="{{ old('support_days', $system->support_days) }}" type="number"
                                    name="support_days" required min="0" step="1"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="365 يوم">
                                @error('support_days')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- نوع الخدمة --}}
                        <div class="mb-4">
                            <x-input-label for="service_id" :value="__('نوع الخدمة')" />
                            <select id="service_id" name="service_id"
                                class="mt-2 px-4 py-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                                        focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm block w-full"
                                required>
                                <option class="text-gray-500">-- اختر نوع الخدمة --</option>
                                @foreach ($services as $service)
                                <option {{ $service->id == $system->service_id ? 'selected' : '' }} value="{{
                                    $service->id }}">
                                    {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                        </div>

                        {{-- مدة الدعم الفني --}}
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 mb-2">
                                بداية العداد <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input value="{{ $service->counter > 0 ? $service->counter : '0' }}" type="number"
                                    name="counter" required min="0" step="1"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('counter')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الوصف -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-align-right text-blue-600"></i>
                        الوصف
                    </h2>

                    <div class="space-y-4">
                        <!-- الوصف بالعربي -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الوصف بالعربي <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description_ar" name="description_ar" required rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="نظام متكامل...">{{ old('description_ar', $system->description_ar) }}</textarea>
                            @error('description_ar')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الوصف بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description (English) <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description_en" name="description_en" required rows="4" dir="ltr"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Integrated system...">{{ old('description_en', $system->description_en) }}</textarea>
                            @error('description_en')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- المتطلبات -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-list-check text-blue-600"></i>
                        المتطلبات
                    </h2>

                    <div id="requirements-container" class="space-y-3">
                        @foreach($system->requirements as $requirement)
                        <div class="flex gap-2 requirement-row">
                            <input type="text" name="requirements_ar[]" value="{{ $requirement['ar'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="متطلب جديد">
                            <input type="text" name="requirements_en[]" dir="ltr" value="{{ $requirement['en'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="New Requirement">
                            <button type="button"
                                class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <button type="button"
                        class="add-requirement-btn mt-4 flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus"></i>
                        إضافة متطلب
                    </button>
                </div>

                <!-- المميزات -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-star text-blue-600"></i>
                        جميع المميزات
                    </h2>

                    <div id="features-container" class="space-y-3">
                        @foreach($system->features as $feature)
                        <div class="flex gap-2 feature-row">
                            <input type="text" name="features_ar[]" value="{{ $feature['ar'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="ميزة جديدة">
                            <input type="text" name="features_en[]" dir="ltr" value="{{ $feature['en'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="New Feature">
                            <button type="button"
                                class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <button type="button"
                        class="add-feature-btn mt-4 flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus"></i>
                        إضافة ميزة
                    </button>
                </div>

                <!-- ازرار الإجراءات -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-link text-blue-600"></i>
                        أزرار الإجراءات
                    </h2>

                    <div id="buttons-container" class="space-y-4">
                        @php
                        $systemButtons = $system->buttons ?? [];
                        if (empty($systemButtons)) {
                        $systemButtons[] = (object)['text_ar' => '', 'text_en' => '', 'link' => '', 'color' =>
                        '#3B82F6'];
                        }
                        @endphp

                        @forelse ($systemButtons as $button)
                        @php
                        $isObject = is_object($button);

                        $textAr = $isObject ? $button->text_ar ?? '' : ($button['text_ar'] ?? '');
                        $textEn = $isObject ? $button->text_en ?? '' : ($button['text_en'] ?? '');
                        $link = $isObject ? $button->link ?? '' : ($button['link'] ?? '');
                        $color = $isObject ? $button->color ?? '#3B82F6' : ($button['color'] ?? '#3B82F6');
                        @endphp

                        <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        محتوى الزر (عربي)
                                    </label>
                                    <input type="text" name="buttons_text_ar[]"
                                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="اطلب الآن" value="{{ $textAr }}">
                                </div>

                                <div>
                                    <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                        Button Text (English)
                                    </label>
                                    <input type="text" name="buttons_text_en[]" dir="ltr"
                                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Order Now" value="{{ $textEn }}">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        رابط الزر
                                    </label>
                                    <input type="url" name="buttons_link[]" dir="ltr"
                                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="https://example.com" value="{{ $link }}">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        لون الزر
                                    </label>
                                    <div class="flex gap-2">
                                        <input type="color" name="buttons_color[]" value="{{ $color }}"
                                            class="button-color-picker w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" name="buttons_color_hex[]" value="{{ $color }}" dir="ltr"
                                            class="button-color-text flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                            placeholder="#3B82F6" readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- زر الحذف --}}
                            <div class="flex justify-end mt-3">
                                <button type="button"
                                    class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2">
                                    <i class="fas fa-trash"></i>
                                    حذف الزر
                                </button>
                            </div>
                        </div>
                        @empty
                        @endforelse
                    </div>

                    <button type="button"
                        class="add-button-btn mt-4 flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus"></i>
                        إضافة زر جديد
                    </button>
                </div>

                <!-- الصور -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-image text-blue-600"></i>
                        الصور
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">

                        <!-- الصورة الرئيسية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الصورة الرئيسية
                            </label>

                            <!-- الصورة الحالية -->
                            <div class="mb-3">
                                <img src="{{ asset($system->main_image) }}"
                                    class="w-full h-48 object-cover rounded-lg border shadow-sm" alt="الصورة الحالية">
                                <p class="text-xs text-gray-500 mt-1">الصورة الحالية</p>
                            </div>

                            <div
                                class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                                <input id="main_image_input" type="file" name="main_image" accept="image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">اضغط لتغيير الصورة</p>
                            </div>

                            <!-- المعاينة -->
                            <div id="main_preview_container" class="mt-3 hidden relative w-full h-56">
                                <img id="main_image_preview" class="w-full h-full object-cover rounded-lg border" />
                                <button type="button" onclick="removeMainImage()"
                                    class="absolute top-1 right-1 bg-red-600 text-white w-7 h-7 flex items-center justify-center rounded-full shadow hover:bg-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- صور إضافية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                صور إضافية
                            </label>

                            <!-- الصور الحالية -->
                            @if($system->images && count($system->images) > 0)
                            <div class="mb-3 grid grid-cols-3 gap-2" id="existing-images-container">
                                @foreach($system->images as $index => $image)
                                <div class="relative existing-image-item" data-index="{{ $index }}">
                                    <img src="{{ asset($image) }}" class="w-full h-20 object-cover rounded border"
                                        alt="صورة إضافية">
                                    <button type="button" onclick="deleteImage({{ $index }}, this)"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white w-6 h-6 flex items-center justify-center rounded-full shadow hover:bg-red-700">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    <input type="hidden" name="keep_images[]" value="{{ $index }}"
                                        class="keep-image-input">
                                </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mb-2">الصور الحالية</p>
                            @endif

                            <div
                                class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                                <input id="extra_images_input" type="file" name="images[]" accept="image/*" multiple
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <i class="fas fa-images text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">إضافة صور جديدة</p>
                            </div>

                            <div id="extra_images_preview" class="mt-3 flex flex-wrap gap-3"></div>
                        </div>

                    </div>
                </div>
<div class="mb-4">
    <label for="system_external" class="block text-sm font-medium text-gray-700 mb-1">
        هل النظام خارجي <span class="text-red-500">*</span>
    </label>
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox" id="system_external_toggle" name="system_external" value="1" class="sr-only peer" {{
            old('system_external', $system->system_external) ? 'checked' : '' }}>
        <div
            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
        </div>
        <span class="ms-3 text-sm font-medium text-gray-900 select-none">نعم</span>
    </label>
    @error('system_external')
    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>

<div id="external_url_container"
    class="{{ old('system_external', $system->system_external) ? '' : 'hidden' }} mt-4 mb-6">
    <label for="external_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        رابط النظام الخارجي <span class="text-red-500">*</span>
    </label>
    <input type="url" name="external_url" id="external_url" value="{{ old('external_url', $system->external_url) }}"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        placeholder="https://example.com">
    @error('external_url')
    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('system_external_toggle');
        const urlContainer = document.getElementById('external_url_container');
        const urlInput = document.getElementById('external_url');

        const handleToggle = (isChecked) => {
            if (isChecked) {
                urlContainer.classList.remove('hidden');
                urlInput.setAttribute('required', 'required');
            } else {
                urlContainer.classList.add('hidden');
                urlInput.removeAttribute('required');
                // لا تحذف القيمة هنا في صفحة الـ Edit لكي لا يفقد المستخدم البيانات إذا أغلقها بالخطأ
                // سيتم التعامل مع مسح البيانات في الـ Controller إذا لزم الأمر
            }
        };

        // تشغيل الوظيفة عند تحميل الصفحة لضبط الحالة الابتدائية (مهم جداً في الـ Edit)
        handleToggle(toggle.checked);

        toggle.addEventListener('change', function () {
            handleToggle(this.checked);
        });
    });
</script>

<div class="mb-4">
    <label for="evorq_onwer" class="block text-sm font-medium text-gray-700 mb-1">
        هل تملك Evorq النظام<span class="text-red-500">*</span>
    </label>
    <label class="inline-flex items-center cursor-pointer">
<input type="hidden" name="evorq_onwer" value="0">

<input type="checkbox" id="evorq_onwer_toggle" name="evorq_onwer" value="1" class="sr-only peer" {{ old('evorq_onwer',
    $system->evorq_onwer) ? 'checked' : '' }}>        <div
            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
        </div>
        <span class="ms-3 text-sm font-medium text-gray-900 select-none">نعم</span>
    </label>
    @error('evorq_onwer')
    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>

<div id="onwer_system_container"
    class="{{ old('evorq_onwer', $system->evorq_onwer) ? '' : 'hidden' }} mt-4 mb-6">
    <label for="onwer_system" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        اسم مالك النظام <span class="text-red-500">*</span>
    </label>
    <input type="text" name="onwer_system" id="onwer_system" value="{{ old('onwer_system', $system->onwer_system) }}"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        >
    @error('onwer_system')
    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>
                <!-- الحالة -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        الحالة
                    </h2>

                    <div class="flex gap-4">
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                            {{ $system->status === 'active' ? 'border-green-300 bg-green-50' : 'border-gray-300' }}">
                            <input type="radio" name="status" value="active" {{ $system->status === 'active' ? 'checked'
                            : '' }}
                            class="w-5 h-5 text-green-600">
                            <span
                                class="font-medium {{ $system->status === 'active' ? 'text-green-700' : 'text-gray-700' }}">نشط</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                            {{ $system->status === 'inactive' ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                            <input type="radio" name="status" value="inactive" {{ $system->status === 'inactive' ?
                            'checked' : '' }}
                            class="w-5 h-5 text-red-600">
                            <span
                                class="font-medium {{ $system->status === 'inactive' ? 'text-red-700' : 'text-gray-700' }}">غير
                                نشط</span>
                        </label>
                    </div>
                </div>

                <!-- الأزرار -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('dashboard.systems.index') }}"
                        class="px-8 bg-gray-200 text-gray-700 py-4 rounded-lg font-bold hover:bg-gray-300 transition text-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('evorq_onwer_toggle');
    
    // FIX: Match the ID used in your HTML div
    const urlContainer = document.getElementById('onwer_system_container');
    
    const urlInput = document.getElementById('onwer_system');
    
    const handleToggle = (isChecked) => {
    if (urlContainer) { // Added a safety check
    if (isChecked) {
    urlContainer.classList.remove('hidden');
    urlInput.setAttribute('required', 'required');
    } else {
    urlContainer.classList.add('hidden');
    urlInput.removeAttribute('required');
    }
    }
    };
    
    toggle.addEventListener('change', function() {
    handleToggle(this.checked);
    });
    });
    function deleteImage(index, button) {
    if (confirm('هل أنت متأكد من حذف هذه الصورة؟')) {
        button.closest('.existing-image-item').remove();
    }
}
</script>

@endsection