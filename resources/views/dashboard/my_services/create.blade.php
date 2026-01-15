@extends('layouts.app')

@section('title', 'إضافة خدمة')

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
    <section class="p-3 sm:p-5">
        <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_services.index') }}" second="خدماتي" third="إضافة خدمة" />
        <div class="mx-auto max-w-4xl w-full rounded-xl">
            <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
                <form method="POST" action="{{ route('dashboard.my_services.store') }}" class="space-y-6"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- معلومات أساسية -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            المعلومات الأساسية
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- اسم الخدمة -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    إسم الخدمة (بالعربي) <span class="text-black">*</span>
                                </label>
                                <input type="text" id="name_ar" name="name_ar" required
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="الإسم">
                                @error('name_ar')
                                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- اسم الخدمة بالإنجليزية -->
                            <div>
                                <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                    Service Name (English)
                                </label>
                                <input required type="text" id="name_en" name="name_en" dir="ltr"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Name">
                                @error('name_en')
                                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                                @enderror
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
<div>

    <label class="flex text-sm font-medium text-gray-700 mb-2">

        السعر الكلي (<img src="{{ asset('assets/images/drhm-icon.svg') }}" />) <span class="text-black">*</span>

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
                            <!-- السعر الكلي -->
{{-- نوع الخدمة --}}
{{-- نوع الخدمة --}}
<div class="mb-4">
    <x-input-label for="service_id" :value="__('نوع الخدمة')" />
    <select id="service_id" name="service_id"
        class="mt-2 px-4 py-3 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full"
        required>
        <option value="" data-price="0" data-commission="0">-- اختر نوع الخدمة --</option>
        @foreach ($services as $service)
        <option value="{{ $service->id }}" data-price="{{ $service->price ?? 0 }}"
            data-commission="{{ $service->evork_commission ?? 0 }}">
            {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
        </option>
        @endforeach
    </select>
</div>

{{-- حقل السعر الذي سيظهر فيه السعر بعد الخصم --}}
<div>
    <label class="flex text-sm font-medium text-gray-700 mb-2">
        السعر النهائي (بعد خصم عمولة {{ $service->evork_commission ?? '' }}%)
    </label>
    <input type="number" id="total_price_input" name="price" required
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service_id');
    const priceInput = document.getElementById('total_price_input');

    serviceSelect.addEventListener('change', function () {
        // الحصول على الخيار المختار حالياً
        const selectedOption = this.options[this.selectedIndex];
        
        // جلب السعر من خاصية data-price التي أضفناها
        const servicePrice = selectedOption.getAttribute('data-price');

        // تحديث قيمة حقل السعر الكلي
        if (servicePrice) {
            priceInput.value = servicePrice;
        } else {
            priceInput.value = '';
        }
    });
});
</script>
                            {{-- مدة الدعم الفني --}}
                            {{-- <div>
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
                            </div> --}}
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
                                    placeholder="Integrated Service for sales and inventory management..."></textarea>
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
                                    <input id="main_image_input" type="file" name="main_image" accept="image/*"
                                        required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
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
                                    <input id="extra_images_input" type="file" name="images[]" accept="image/*"
                                        multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
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

                    <div id="external_url_container" class="{{ old('Service_external') ? '' : 'hidden' }} mt-4 mb-6">
                        <label for="external_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            رابط الخدمة الخارجي <span class="text-black">*</span>
                        </label>
                        <input type="url" name="external_url" id="external_url" value="{{ old('external_url') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="https://example.com">
                        @error('external_url')
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
                                <input type="radio" name="status" value="active" checked
                                    class="w-5 h-5 text-green-600">
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
                            حفظ الخدمة
                        </button>
                        <button type="reset"
                            class="px-8 bg-gray-200 text-gray-700 py-4 rounded-lg font-bold hover:bg-gray-300 transition">
                            <i class="fas fa-redo ml-2"></i>
                            إعادة تعيين
                        </button>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {

                        // ترجمة المميزات باستخدام Event Delegation
                        const featuresContainer = document.getElementById('features-container');
                        if (featuresContainer) {
                            featuresContainer.addEventListener('input', function(e) {
                                // إذا كان الإدخال في حقل عربي
                                if (e.target.name === 'features_ar[]') {
                                    const row = e.target.closest('.feature-row');
                                    const targetInput = row.querySelector('input[name="features_en[]"]');

                                    clearTimeout(e.target.timeout);
                                    e.target.timeout = setTimeout(async () => {
                                        if (e.target.value.trim()) {
                                            const translated = await translateText(e.target.value, 'ar',
                                                'en');
                                            targetInput.value = translated;
                                        }
                                    }, 1000);
                                }

                                // إذا كان الإدخال في حقل إنجليزي
                                if (e.target.name === 'features_en[]') {
                                    const row = e.target.closest('.feature-row');
                                    const targetInput = row.querySelector('input[name="features_ar[]"]');

                                    clearTimeout(e.target.timeout);
                                    e.target.timeout = setTimeout(async () => {
                                        if (e.target.value.trim()) {
                                            const translated = await translateText(e.target.value, 'en',
                                                'ar');
                                            targetInput.value = translated;
                                        }
                                    }, 1000);
                                }
                            });
                        }

                        // باقي الكود للاسم والوصف (شغال صح)
                        let nameArTimeout;
                        const nameArInput = document.getElementById('name_ar');
                        if (nameArInput) {
                            nameArInput.addEventListener('input', function(e) {
                                clearTimeout(nameArTimeout);
                                nameArTimeout = setTimeout(async () => {
                                    if (e.target.value.trim()) {
                                        const translated = await translateText(e.target.value, 'ar', 'en');
                                        document.getElementById('name_en').value = translated;
                                    }
                                }, 1000);
                            });
                        }

                        let nameEnTimeout;
                        const nameEnInput = document.getElementById('name_en');
                        if (nameEnInput) {
                            nameEnInput.addEventListener('input', function(e) {
                                clearTimeout(nameEnTimeout);
                                nameEnTimeout = setTimeout(async () => {
                                    if (e.target.value.trim()) {
                                        const translated = await translateText(e.target.value, 'en', 'ar');
                                        document.getElementById('name_ar').value = translated;
                                    }
                                }, 1000);
                            });
                        }

                        let descArTimeout;
                        const descArInput = document.getElementById('description_ar');
                        if (descArInput) {
                            descArInput.addEventListener('input', function(e) {
                                clearTimeout(descArTimeout);
                                descArTimeout = setTimeout(async () => {
                                    if (e.target.value.trim()) {
                                        const translated = await translateText(e.target.value, 'ar', 'en');
                                        document.getElementById('description_en').value = translated;
                                    }
                                }, 1000);
                            });
                        }

                        let descEnTimeout;
                        const descEnInput = document.getElementById('description_en');
                        if (descEnInput) {
                            descEnInput.addEventListener('input', function(e) {
                                clearTimeout(descEnTimeout);
                                descEnTimeout = setTimeout(async () => {
                                    if (e.target.value.trim()) {
                                        const translated = await translateText(e.target.value, 'en', 'ar');
                                        document.getElementById('description_ar').value = translated;
                                    }
                                }, 1500);
                            });
                        }

                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        const toggle = document.getElementById('Service_external_toggle');
                        const urlContainer = document.getElementById('external_url_container');
                        const urlInput = document.getElementById('external_url');
                        const handleToggle = (isChecked) => {
                            if (isChecked) {
                                urlContainer.classList.remove('hidden');
                                urlInput.setAttribute('required', 'required');
                            } else {
                                urlContainer.classList.add('hidden');
                                urlInput.removeAttribute('required');
                            }
                        };
                        toggle.addEventListener('change', function() {
                            handleToggle(this.checked);
                        });
                    });
                </script>
            </div>
        </div>
    </section>
@endsection
