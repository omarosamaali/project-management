@extends('layouts.app')

@section('title', 'عرض الخدمة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.services.index') }}" second="الخدمة" third="عرض الخدمة" />

<div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
    
            <!-- Header -->
            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $myService->name_ar }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300" dir="ltr">
                            {{ $myService->name_en }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        @if(Auth::user()-> id == $myService->user_id)
                        <a href="{{ route('dashboard.partner_systems.edit', $myService->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-edit ml-2"></i>
                            تعديل
                        </a>
                        @endif
                        <a href="{{ route('dashboard.partner_systems.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-arrow-right ml-2"></i>
                            رجوع
                        </a>
                    </div>
                </div>
            </div>
    
            <div class="p-6 space-y-8">
    
                <!-- المعلومات الأساسية -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        المعلومات الأساسية
                    </h2>
    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- السعر -->
                        <div class="bg-blue-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                السعر الكلي
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($myService->price, 0) }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-400"></span>
                            </div>
                        </div>
    
                        <!-- مدة التنفيذ -->
                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                مدة التنفيذ
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $myService->execution_days_from }} - {{ $myService->execution_days_to }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-400">يوم عمل</span>
                            </div>
                        </div>
    
                        <!-- مدة الدعم الغني -->
                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                مدة الدعم الفني (بالايام)
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $myService->support_days }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-400">يوم عمل</span>
                            </div>
                        </div>
    
                        <!-- الخدمة -->
                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                الخدمة
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    @if($myService?->service)
                                    {{ $myService?->service->name_ar }}
                                    @endif
                                </span>
                            </div>
                        </div>
    
                        <!-- الخدمة -->
                        {{-- <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                مدة العداد
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $myService->counter }}
                                </span>
                            </div>
                        </div> --}}
    
                        <!-- الحالة -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                الحالة
                            </label>
                            @if($myService->status === 'active')
                            <span
                                class="inline-flex items-center gap-2 bg-green-100 text-green-800 text-sm font-medium px-4 py-2 rounded-lg dark:bg-green-900 dark:text-green-300">
                                <i class="fas fa-check-circle"></i>
                                نشط
                            </span>
                            @else
                            <span
                                class="inline-flex items-center gap-2 bg-red-100 text-red-800 text-sm font-medium px-4 py-2 rounded-lg dark:bg-red-900 dark:text-red-300">
                                <i class="fas fa-times-circle"></i>
                                غير نشط
                            </span>
                            @endif
                        </div>
    
                        <!-- تاريخ الإضافة -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                تاريخ الإضافة
                            </label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $myService->created_at->format('Y-m-d') }}
                            </span>
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
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                الوصف بالعربي
                            </label>
                            <p class="text-gray-800 dark:text-gray-200 leading-relaxed">
                                {{ $myService->description_ar }}
                            </p>
                        </div>
    
                        <!-- الوصف بالإنجليزية -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                                Description (English)
                            </label>
                            <p class="text-gray-800 dark:text-gray-200 leading-relaxed" dir="ltr">
                                {{ $myService->description_en }}
                            </p>
                        </div>
                    </div>
                </div>
    
                <!-- المتطلبات -->
    
                @if(!empty($myService->requirements) && isset($myService->requirements[0][app()->getLocale()]) &&
                $myService->requirements[0][app()->getLocale()] !== null &&
                $myService->requirements[0][app()->getLocale()] !== '')
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-list-check text-blue-600"></i>
                        المتطلبات
                    </h2>
    
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($myService->requirements as $requirement)
                        <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-check-circle text-blue-600 mt-1"></i>
                            <div class="flex-1">
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $requirement['ar'] }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1" dir="ltr">
                                    {{ $requirement['en'] }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
    
                <!-- المميزات -->
                @if(!empty($myService->features) && isset($myService->features[0][app()->getLocale()]) &&
                $myService->features[0][app()->getLocale()] !== null && $myService->features[0][app()->getLocale()] !==
                '')
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-star text-blue-600"></i>
                        جميع المميزات
                    </h2>
    
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($myService->features as $feature)
                        <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-star text-green-600 mt-1"></i>
                            <div class="flex-1">
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $feature['ar'] }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1" dir="ltr">
                                    {{ $feature['en'] }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
    
                <!-- الأزرار -->
                @if(!empty($myService->buttons))
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">
                    الأزرار
                </label>
                <div class="flex flex-wrap gap-3 mt-4">
                    @foreach($myService->buttons as $button)
                    <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                        class="px-6 py-3 rounded-lg text-white font-semibold hover:opacity-90 transition"
                        style="background-color: {{ $button['color'] ?? '#3B82F6' }}">
                        {{ $button['text_ar'] }}
                    </a>
                    @endforeach
                </div>
                @endif
    
                <!-- الصور -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-image text-blue-600"></i>
                        الصور
                    </h2>
    
                    <div class="space-y-4">
                        <!-- الصورة الرئيسية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">
                                الصورة الرئيسية
                            </label>
                            <div class="max-w-2xl">
                                <img src="{{ asset($myService->main_image) }}" alt="{{ $myService->name_ar }}"
                                    class="w-full h-96 object-cover rounded-lg shadow-lg border">
                            </div>
                        </div>
    
                        <!-- الصور الإضافية -->
                        @if($myService->images && count($myService->images) > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-3">
                                الصور الإضافية
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($myService->images as $image)
                                <img src="{{ asset($image) }}" alt="صورة إضافية"
                                    class="w-full h-40 object-cover rounded-lg shadow border cursor-pointer hover:scale-105 transition">
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
    
            </div>
        </div>
    </div>

    <script>
        function openModal(imageUrl) {
            Swal.fire({
            imageUrl: imageUrl,
            imageWidth: 400,
            imageHeight: 400,
            });
        }
    </script>
</section>
@endsection