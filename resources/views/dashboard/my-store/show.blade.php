@extends('layouts.app')

@section('title', 'عرض الخدمة')

@section('content')
<section class="!px-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my-store.index') }}" second="متجري" third="عرض الخدمة" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">

            <!-- Header -->
            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $system->name_ar }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300" dir="ltr">
                            {{ $system->name_en }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.my-store.edit', $system->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-edit ml-2"></i>
                            تعديل
                        </a>
                        <a href="{{ route('dashboard.my-store.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-arrow-right ml-2"></i>
                            رجوع
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-8">

                <!-- معلومات أساسية -->
                <div class="border-b pb-6">
                   <h2 class="mt-2 text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    المعلومات الأساسية
                </h2>
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <!-- معلومات مُدخل الخدمة -->
                    <div class="md:col-span-2 bg-gradient-to-r from-blue-50 to-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-user-circle"></i>
                            معلومات مُدخل الخدمة
                        </h3>
                        <div class="grid md:grid-cols-3 gap-4">
                            <!-- اسم المُدخل -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    الاسم
                                </label>
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-user text-blue-600"></i>
                                    <span class="font-semibold text-gray-800">
                                        {{ $system->user->name ?? 'غير محدد' }}
                                    </span>
                                </div>
                            </div>
                
                            <!-- البريد الإلكتروني -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    البريد الإلكتروني
                                </label>
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                    <span class="font-semibold text-gray-800" dir="ltr">
                                        {{ $system->user->email ?? 'غير محدد' }}
                                    </span>
                                </div>
                            </div>
                
                            <!-- تاريخ الإضافة -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    تاريخ إضافة الخدمة
                                </label>
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-calendar-plus text-blue-600"></i>
                                    <span class="font-semibold text-gray-800">
                                        {{ $system->created_at->format('Y-m-d') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- اسم الخدمة -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                إسم الخدمة (بالعربي)
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                {{ $system->name_ar }}
                            </div>
                        </div>

                        <!-- اسم الخدمة بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                Serivce Name (English)
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" dir="ltr">
                                {{ $system->name_en }}
                            </div>
                        </div>

                        <!-- مدة التنفيذ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                مدة التنفيذ
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                {{ $system->execution_days }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">يوم عمل</p>
                        </div>

                        {{-- مدة الدعم الفني --}}
                        <div>
                            <label class="flex text-sm font-medium text-gray-700 mb-2">
                                مدة الدعم الفني (بالايام)
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                {{ $system->support_days }}
                            </div>
                        </div>

                        <!-- السعر الأصلي -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                السعر الذي تم عرضه للعميل (قبل خصم العمولة)
                            </label>
                            <div class="flex items-center justify-center">
                                <div class="w-full px-4 py-3 border border-gray-300 rounded-s-lg bg-gray-50">
                                    {{ number_format($system->original_price ?? $system->price, 0) }}
                                </div>
                                <div
                                    class="bg-gray-200 flex items-center justify-center px-3 h-[50px] py-3 border border-gray-300 rounded-l-lg">
                                    <x-drhm-icon width="16" height="16" />
                                </div>
                            </div>
                        </div>

                        <!-- نوع الخدمة -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                نوع الخدمة
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                @if($system?->service)
                                {{ app()->getLocale() == 'ar' ? $system->service->name_ar : $system->service->name_en }}
                                @if($system->service->evork_commission > 0)
                                <span class="text-sm text-gray-500">(عمولة {{ $system->service->evork_commission
                                    }}%)</span>
                                @endif
                                @else
                                -
                                @endif
                            </div>
                        </div>

                        <!-- السعر النهائي -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                السعر النهائي (بعد خصم عمولة المنصة)
                            </label>
                            <div class="flex items-center">
                                <div
                                    class="w-full px-4 py-3 border border-gray-300 rounded-s-lg bg-blue-50 text-blue-700 font-bold">
                                    {{ number_format($system->price, 2) }}
                                </div>
                                <div
                                    class="bg-gray-200 flex items-center justify-center px-3 h-[50px] py-3 border border-gray-300 rounded-l-lg">
                                    <x-drhm-icon width="16" height="16" />
                                </div>
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
                                الوصف بالعربي
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 min-h-[100px]">
                                {{ $system->description_ar }}
                            </div>
                        </div>

                        <!-- الوصف بالإنجليزية -->
                        <div>
                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                Description (English)
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 min-h-[100px]"
                                dir="ltr">
                                {{ $system->description_en }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المتطلبات -->
                @if(!empty($system->requirements) && isset($system->requirements[0][app()->getLocale()]) &&
                $system->requirements[0][app()->getLocale()] !== null &&
                $system->requirements[0][app()->getLocale()] !== '')
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-list-check text-blue-600"></i>
                        المتطلبات
                    </h2>

                    <div class="space-y-3">
                        @foreach($system->requirements as $requirement)
                        <div class="flex gap-2">
                            <div class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                {{ $requirement['ar'] }}
                            </div>
                            <div class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" dir="ltr">
                                {{ $requirement['en'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- جميع المميزات -->
                @if(!empty($system->features) && isset($system->features[0][app()->getLocale()]) &&
                $system->features[0][app()->getLocale()] !== null &&
                $system->features[0][app()->getLocale()] !== '')
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-star text-blue-600"></i>
                        جميع المميزات
                    </h2>

                    <div class="space-y-3">
                        @foreach($system->features as $feature)
                        <div class="flex gap-2">
                            <div class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                                {{ $feature['ar'] }}
                            </div>
                            <div class="flex-1 px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" dir="ltr">
                                {{ $feature['en'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- أزرار الخدمة -->
                @if(!empty($system->buttons))
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-link text-blue-600"></i>
                        أزرار الإجراءات
                    </h2>

                    <div class="space-y-4">
                        @foreach($system->buttons as $button)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid md:grid-cols-2 gap-4 mb-3">
                                <!-- محتوى الزر بالعربي -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        محتوى الزر (عربي)
                                    </label>
                                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                                        {{ $button['text_ar'] ?? '-' }}
                                    </div>
                                </div>

                                <!-- محتوى الزر بالإنجليزي -->
                                <div>
                                    <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                        Button Text (English)
                                    </label>
                                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white" dir="ltr">
                                        {{ $button['text_en'] ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- اللينك -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        رابط الزر
                                    </label>
                                    <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white" dir="ltr">
                                        <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                                            class="text-blue-600 hover:underline">
                                            {{ $button['link'] ?? '-' }}
                                        </a>
                                    </div>
                                </div>

                                <!-- اللون -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        لون الزر
                                    </label>
                                    <div class="flex gap-2">
                                        <div class="w-16 h-10 border border-gray-300 rounded"
                                            style="background-color: {{ $button['color'] ?? '#3B82F6' }}"></div>
                                        <div class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-white"
                                            dir="ltr">
                                            {{ $button['color'] ?? '#3B82F6' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- معاينة الزر -->
                            <div class="mt-3 pt-3 border-t">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    معاينة الزر
                                </label>
                                <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                                    class="inline-block px-6 py-3 rounded-lg text-white font-semibold hover:opacity-90 transition"
                                    style="background-color: {{ $button['color'] ?? '#3B82F6' }}">
                                    {{ $button['text_ar'] }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <span class="font-bold text-xs text-gray-600">ينصح باضافة روابط لاختبار الخدمة او الخدمة</span>
                </div>
                @endif

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
                                الصورة الرئيسية
                            </label>
                            <div class="relative border-2 border-gray-300 rounded-lg p-2 bg-gray-50">
                                <img src="{{ asset($system->main_image) }}" alt="{{ $system->name_ar }}"
                                    class="w-full h-56 object-cover rounded-lg">
                            </div>
                        </div>

                        <!-- صور إضافية -->
                        @if($system->images && count($system->images) > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                صور إضافية
                            </label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($system->images as $image)
                                <div class="relative border-2 border-gray-300 rounded-lg p-1 bg-gray-50">
                                    <img onclick="openImage('{{ asset($image) }}')" src="{{ asset($image) }}" alt="صورة إضافية"
                                        class="w-24 h-24 object-cover rounded cursor-pointer hover:scale-105 transition">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <style>
                        .swal2-image {
                        border-radius: 20px !important;
                        border: 2px solid #ddd;
                        }
                    </style>
                    <script>
                        function openImage(image) {
                            Swal.fire({
                            imageUrl: image,
                            imageWidth: 400,
                            imageHeight: 200,
                            imageAlt: "image",
                            confirmButtonText: "أغلق",
                            });
                        }
                    </script>
                </div>
                @if(Auth::user()->role == 'admin')
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-tasks text-blue-600"></i>
                            إدارة حالة الخدمة
                        </h2>
                        <span class="px-4 py-1 rounded-full text-sm font-bold 
                            {{ $system->status == 'نشط' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $system->status == 'مرفوض' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $system->status == 'قيد المراجعة' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            الحالة الحالية: {{ $system->status }}
                        </span>
                    </div>
                
                    <div class="flex flex-wrap gap-4 justify-center">
                        <form action="{{ route('dashboard.my-store.update-status', $system->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="نشط">
                            <button type="submit" @disabled($system->status == 'نشط')
                                class="flex items-center gap-2 px-6 py-3 rounded-lg transition shadow-md border-2
                                {{ $system->status == 'نشط'
                                ? 'bg-green-50 border-green-600 text-green-700 cursor-not-allowed opacity-80'
                                : 'bg-green-600 border-transparent text-white hover:bg-green-700' }}">
                                <i class="fas {{ $system->status == 'نشط' ? 'fa-check-double' : 'fa-check-circle' }}"></i>
                                {{ $system->status == 'نشط' ? 'تم القبول بنجاح' : 'قبول الخدمة (نشط)' }}
                            </button>
                        </form>
                
                        <form action="{{ route('dashboard.my-store.update-status', $system->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="مرفوض">
                            <button type="submit" @disabled($system->status == 'مرفوض')
                                class="flex items-center gap-2 px-6 py-3 rounded-lg transition shadow-md border-2
                                {{ $system->status == 'مرفوض'
                                ? 'bg-red-50 border-red-600 text-red-700 cursor-not-allowed opacity-80'
                                : 'bg-red-600 border-transparent text-white hover:bg-red-700' }}">
                                <i class="fas {{ $system->status == 'مرفوض' ? 'fa-ban' : 'fa-times-circle' }}"></i>
                                {{ $system->status == 'مرفوض' ? 'الخدمة مرفوضة حالياً' : 'رفض الخدمة' }}
                            </button>
                        </form>
                
                        <form action="{{ route('dashboard.my-store.update-status', $system->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="قيد المراجعة">
                            <button type="submit" @disabled($system->status == 'قيد المراجعة')
                                class="flex items-center gap-2 px-6 py-3 rounded-lg transition shadow-md border-2
                                {{ $system->status == 'قيد المراجعة'
                                ? 'bg-yellow-50 border-yellow-500 text-yellow-700 cursor-not-allowed opacity-80'
                                : 'bg-yellow-500 border-transparent text-white hover:bg-yellow-600' }}">
                                <i class="fas fa-clock"></i>
                                {{ $system->status == 'قيد المراجعة' ? 'قيد الانتظار حالياً' : 'إرجاع لقيد المراجعة' }}
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection