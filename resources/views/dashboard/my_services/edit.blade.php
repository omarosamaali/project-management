@extends('layouts.app')

@section('title', 'تعديل الخدمة')

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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_services.index') }}" second="الخدمة"
        third="تعديل الخدمة" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                تعديل بيانات الخدمة: {{ $myService->title }}
            </h2>

            <form method="POST" action="{{ route('dashboard.my_services.update', $myService) }}" class="space-y-6"
                enctype="multipart/form-data">
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
                                إسم النظام (بالعربي) <span class="text-black">*</span>
                            </label>
                            <input type="text" id="name_ar" name="name_ar" required value="{{ old('name_ar', $myService->name_ar) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="الإسم">
                            @error('name_ar')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <!-- اسم النظام بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 dark:text-gray-300 mb-2">
                                $myService Name (English) <span class="text-black">*</span>
                            </label>
                            <input type="text" id="name_en" name="name_en" required dir="ltr"
                                value="{{ old('name_en', $myService->name_en) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Name">
                            @error('name_en')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <!-- السعر -->
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                السعر الكلي <span class="text-black">*</span>
                            </label>
                            <input type="number" name="price" required min="0" step="0.01" value="{{ old('price', $myService->price) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="999">
                            @error('price')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <!-- مدة التنفيذ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                مدة التنفيذ <span class="text-black">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <input type="number" name="execution_days_from" required min="0"
                                        value="{{ old('execution_days_from', $myService->execution_days_from) }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="10">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">من</span>
                                </div>
                                <div class="relative">
                                    <input type="number" name="execution_days_to" required min="0"
                                        value="{{ old('execution_days_to', $myService->execution_days_to) }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="15">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">إلى</span>
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
                                <input value="{{ old('support_days', $myService->support_days) }}" type="number" name="support_days"
                                    required min="0" step="1"
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
                                                                        focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm block w-full"
                                required>
                                <option class="text-gray-500">-- اختر نوع الخدمة --</option>
                                @foreach ($services as $service)
                                <option {{ $service->id == $myService->service_id ? 'selected' : '' }} value="{{
                                    $service->id }}">
                                    {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                        </div>
                
                        {{-- مدة الدعم الفني --}}
                        {{-- <div>
                            <label class="flex text-sm font-medium text-gray-700 mb-2">
                                بداية العداد <span class="text-black">*</span>
                            </label>
                            <div class="relative">
                                <input value="{{ $service->counter > 0 ? $service->counter : '0' }}" type="number" name="counter"
                                    required min="0" step="1"
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
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-align-right text-blue-600"></i>
                        الوصف
                    </h2>
                
                    <div class="space-y-4">
                        <!-- الوصف بالعربي -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الوصف بالعربي <span class="text-black">*</span>
                            </label>
                            <textarea id="description_ar" name="description_ar" required rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="نظام متكامل...">{{ old('description_ar', $myService->description_ar) }}</textarea>
                            @error('description_ar')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <!-- الوصف بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description (English) <span class="text-black">*</span>
                            </label>
                            <textarea id="description_en" name="description_en" required rows="4" dir="ltr"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Integrated $myService...">{{ old('description_en', $myService->description_en) }}</textarea>
                            @error('description_en')
                            <span class="text-black text-xs mt-1">{{ $message }}</span>
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
                        @foreach($myService->requirements as $requirement)
                        <div class="flex gap-2 requirement-row">
                            <input type="text" name="requirements_ar[]" value="{{ $requirement['ar'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="متطلب جديد">
                            <input type="text" name="requirements_en[]" dir="ltr" value="{{ $requirement['en'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="New Requirement">
                            <button type="button"
                                class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-black">
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
                        @foreach($myService->features as $feature)
                        <div class="flex gap-2 feature-row">
                            <input type="text" name="features_ar[]" value="{{ $feature['ar'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="ميزة جديدة">
                            <input type="text" name="features_en[]" dir="ltr" value="{{ $feature['en'] }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="New Feature">
                            <button type="button"
                                class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-black">
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
                                <img src="{{ asset($myService->main_image) }}" class="w-full h-48 object-cover rounded-lg border shadow-sm"
                                    alt="الصورة الحالية">
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
                                    class="absolute top-1 right-1 bg-black text-white w-7 h-7 flex items-center justify-center rounded-full shadow hover:bg-red-700">
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
                            @if($myService->images && count($myService->images) > 0)
                            <div class="mb-3 grid grid-cols-3 gap-2" id="existing-images-container">
                                @foreach($myService->images as $index => $image)
                                <div class="relative existing-image-item" data-index="{{ $index }}">
                                    <img src="{{ asset($image) }}" class="w-full h-20 object-cover rounded border" alt="صورة إضافية">
                                    <button type="button" onclick="deleteImage({{ $index }}, this)"
                                        class="absolute -top-2 -right-2 bg-black text-white w-6 h-6 flex items-center justify-center rounded-full shadow hover:bg-red-700">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    <input type="hidden" name="keep_images[]" value="{{ $index }}" class="keep-image-input">
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

                <!-- الحالة -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        الحالة
                    </h2>
                
                    <div class="flex gap-4">
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                            {{ $myService->status === 'active' ? 'border-green-300 bg-green-50' : 'border-gray-300' }}">
                            <input type="radio" name="status" value="active" {{ $myService->status === 'active' ? 'checked'
                            : '' }}
                            class="w-5 h-5 text-green-600">
                            <span class="font-medium {{ $myService->status === 'active' ? 'text-green-700' : 'text-gray-700' }}">نشط</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                            {{ $myService->status === 'inactive' ? 'border-red-300 bg-red-50' : 'border-gray-300' }}">
                            <input type="radio" name="status" value="inactive" {{ $myService->status === 'inactive' ?
                            'checked' : '' }}
                            class="w-5 h-5 text-black">
                            <span class="font-medium {{ $myService->status === 'inactive' ? 'text-red-700' : 'text-gray-700' }}">غير
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
                    <a href="{{ route('dashboard.my_services.index') }}"
                        class="px-8 bg-gray-200 text-gray-700 py-4 rounded-lg font-bold hover:bg-gray-300 transition text-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
    </script>
</section>

@endsection