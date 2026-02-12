@extends('layouts.app')

@section('title', 'متجري')

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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my-store.index') }}" second="متجري" third="إضافة متجر" />
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

            <form action="{{ route('dashboard.my-store.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                <!-- معلومات أساسية -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        المعلومات الأساسية
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- اسم المتجر -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان الخدمة او النظام (بالعربي) <span class="text-black">*</span>
                            </label>
                            <input type="text" id="name_ar" name="name_ar" required
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="الإسم">
                            @error('name_ar')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- اسم المتجر بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                Title of the system (English)
                            </label>
                            <input required type="text" id="name_en" name="name_en" dir="ltr"
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Name">
                            @error('name_en')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- مدة التنفيذ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                مدة التنفيذ <span class="text-black">*</span>
                            </label>
                            <div class="grid grid-cols-1 gap-2">
                                <div class="relative">
                                    <input type="number" name="execution_days" required min="0"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="10">
                                    @error('execution_days')
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

                        <!-- 1. حقل السعر الأصلي اللي المستخدم يكتبه بنفسه -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                السعر الذي تريد عرضه للعميل (قبل خصم العمولة) <span class="text-red-600">*</span>
                            </label>
                            <div class="flex items-center">
                                <input type="number" id="original_price_input" name="original_price" required min="0"
                                step="1"
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-s-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="اكتب السعر هنا (مثال: 10000)">
                                <div class="bg-gray-200 flex items-center justify-center px-3 h-[50px] py-3 border border-gray-300 rounded-l-lg">
                                    <x-drhm-icon width="16" height="16" />
                                </div>
                            </div>
                            @error('original_price')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 2. اختيار نوع الخدمة -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                نوع الخدمة <span class="text-red-600">*</span>
                            </label>
                            <select id="service_id" name="service_id" required
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option class="placeholder-gray-400" value="">-- اختر نوع الخدمة --</option>
                                @foreach ($services as $service)
                                <option value="{{ $service->id }}"
                                    data-commission="{{ $service->evork_commission ?? 0 }}">
                                    {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                                </option>
                                @endforeach
                            </select>
                            @error('service_id')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 3. عرض النتيجة بعد الاختيار -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                السعر النهائي (بعد خصم عمولة المنصة)
                                <span id="commission_text" class="text-sm text-gray-500"></span>
                            </label>
                            <div class="flex items-center">
                            <input type="number" id="final_price_display" readonly
                                class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-s-lg bg-gray-50 text-gray-700 font-medium"
                                value="" placeholder="سيظهر هنا بعد اختيار النوع">
                                <div class="bg-gray-200 flex items-center justify-center px-3 h-[50px] py-3 border border-gray-300 rounded-l-lg">
                                                                    <x-drhm-icon width="16" height="16" />
                                                                </div>
                                                                </div>
                        </div>

                        <!-- حقل مخفي لإرسال السعر النهائي للداتابيز -->
                        <input type="hidden" name="price" id="final_price_hidden">

                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                            const originalInput   = document.getElementById('original_price_input');
                                const serviceSelect   = document.getElementById('service_id');
                                const finalDisplay    = document.getElementById('final_price_display');
                                const finalHidden     = document.getElementById('final_price_hidden');
                                const commissionSpan  = document.getElementById('commission_text');
    
        if (!originalInput || !serviceSelect || !finalDisplay || !finalHidden || !commissionSpan) {
            console.error('خطأ في السعر النهائي: واحد أو أكتر من العناصر مش موجودة بالـ ID الصحيح');
            return;
        }
    
        function updateFinalPrice() {
            const price = parseFloat(originalInput.value) || 0;
            const option = serviceSelect.options[serviceSelect.selectedIndex];
            const commission = option.value ? parseFloat(option.dataset.commission) || 0 : 0;
    
            if (price <= 0 || !option.value) {
                finalDisplay.value = '';
                finalHidden.value = '';
                commissionSpan.textContent = '';
                return;
            }
    
            const final = price * (1 - commission / 100);
            finalDisplay.value = final.toFixed(2);
            finalHidden.value = final.toFixed(2);
            
            commissionSpan.textContent = commission > 0 
                ? `(خصم ${commission}% عمولة)`
                : '(بدون خصم)';
        }
    
        // ربط الحدث على كل حاجة ممكن تتغير
        originalInput.addEventListener('input', updateFinalPrice);
        originalInput.addEventListener('change', updateFinalPrice);
        serviceSelect.addEventListener('change', updateFinalPrice);
    
        // تشغيل أولي (مهم جدًا لو فيه قيم قديمة)
        updateFinalPrice();
    });
                        </script>

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
                                placeholder="متجر متكامل لإدارة المبيعات والمخزون، والعطاءات مع متجر محاسبي مبسط وواجهة سهلة الاستخدام"></textarea>
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

                <!-- أزرار المتجر -->
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

                    <span class="font-bold text-xs text-gray-600">ينصح باضافة روابط لاختبار المتجر او الخدمة</span>
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

                <!-- الأزرار -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-save ml-2"></i>
                        حفظ
                    </button>
                    <button type="reset"
                        class="px-8 bg-gray-200 text-gray-700 py-4 rounded-lg font-bold hover:bg-gray-300 transition">
                        <i class="fas fa-redo ml-2"></i>
                        إعادة تعيين
                    </button>
                </div>
            </form>

<script>
    (function() {
    'use strict';

    (function() {
        'use strict';

        async function translateText(text, sourceLang, targetLang) {
            if (!text || !text.trim()) {
                return "";
            }

            const cleanText = text.trim();
            const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(cleanText)}`;

            try {
                const response = await fetch(url);

                if (!response.ok) {
                    console.warn('Translation API error:', response.status);
                    return text;
                }

                const data = await response.json();

                if (data && data[0] && Array.isArray(data[0])) {
                    let translatedText = '';

                    for (let i = 0; i < data[0].length; i++) {
                        const part = data[0][i];
                        if (part && part[0]) {
                            translatedText += part[0];
                        }
                    }

                    return translatedText.trim() || text;
                }

                return text;

            } catch (error) {
                console.error('Translation error:', error);
                return text;
            }
        }

        function setupTextareaTranslation(sourceId, targetId, fromLang, toLang, delay = 1500) {
            const sourceTextarea = document.getElementById(sourceId);
            const targetTextarea = document.getElementById(targetId);

            if (!sourceTextarea || !targetTextarea) {
                console.warn(`Textarea not found: ${sourceId} -> ${targetId}`);
                return;
            }

            let translationTimer = null;
            let isTranslating = false;

            sourceTextarea.addEventListener('input', async function(e) {
                const currentValue = e.target.value;

                if (translationTimer) {
                    clearTimeout(translationTimer);
                }

                if (!currentValue.trim() || isTranslating) {
                    return;
                }

                translationTimer = setTimeout(async () => {
                    isTranslating = true;

                    try {
                        targetTextarea.style.opacity = '0.5';
                        targetTextarea.placeholder = 'Translating...';

                        const translatedText = await translateText(currentValue, fromLang, toLang);

                        if (translatedText) {
                            targetTextarea.value = translatedText;

                            targetTextarea.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }

                    } catch (error) {
                        console.error('Textarea translation failed:', error);
                    } finally {
                        targetTextarea.style.opacity = '1';
                        targetTextarea.placeholder = targetTextarea.getAttribute('placeholder') || '';
                        isTranslating = false;
                    }
                }, delay);
            });

            targetTextarea.addEventListener('focus', function() {
                if (isTranslating) {
                    sourceTextarea.focus();
                }
            });
        }

        function setupInputTranslation(sourceId, targetId, fromLang, toLang, delay = 1000) {
            const sourceInput = document.getElementById(sourceId);
            const targetInput = document.getElementById(targetId);

            if (!sourceInput || !targetInput) {
                return;
            }

            let translationTimer = null;

            sourceInput.addEventListener('input', function(e) {
                const currentValue = e.target.value.trim();

                if (translationTimer) {
                    clearTimeout(translationTimer);
                }

                if (!currentValue) {
                    return;
                }

                translationTimer = setTimeout(async () => {
                    try {
                        const translatedText = await translateText(currentValue, fromLang, toLang);

                        if (translatedText && translatedText !== targetInput.value) {
                            targetInput.value = translatedText;
                            targetInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    } catch (error) {
                        console.error('Input translation failed:', error);
                    }
                }, delay);
            });
        }

        function setupDynamicTranslation(containerId, rowClass, arName, enName) {
            const container = document.getElementById(containerId);

            if (!container) {
                return;
            }

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

                if (e.target.translationTimer) {
                    clearTimeout(e.target.translationTimer);
                }

                const currentValue = e.target.value.trim();
                if (!currentValue) return;

                e.target.translationTimer = setTimeout(async () => {
                    try {
                        const translated = await translateText(
                            currentValue,
                            isArabic ? 'ar' : 'en',
                            isArabic ? 'en' : 'ar'
                        );

                        if (translated && translated !== targetInput.value) {
                            targetInput.value = translated;
                            targetInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    } catch (error) {
                        console.error('Dynamic translation failed:', error);
                    }
                }, 1000);
            });
        }

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
                    input.removeAttribute('required');
                }
            }

            toggle.addEventListener('change', (e) => updateVisibility(e.target.checked));
            updateVisibility(toggle.checked);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupInputTranslation('name_ar', 'name_en', 'ar', 'en', 800);
            setupInputTranslation('name_en', 'name_ar', 'en', 'ar', 800);

            setupTextareaTranslation('description_ar', 'description_en', 'ar', 'en', 1800);
            setupTextareaTranslation('description_en', 'description_ar', 'en', 'ar', 1800);

            setupDynamicTranslation(
                'features-container',
                '.feature-row',
                'features_ar[]',
                'features_en[]'
            );

            setupDynamicTranslation(
                'requirements-container',
                '.requirement-row',
                'requirements_ar[]',
                'requirements_en[]'
            );

            setupToggle('system_external_toggle', 'external_url_container', 'external_url');
            setupToggle('evorq_onwer_toggle', 'onwer_system_container', 'onwer_system');
        });

    })();
})();
</script>        </div>
    </div>
</section>

@endsection