@extends('layouts.app')

@section('title', 'عرض الخدمة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.services.index') }}" second="الخدمة" third="عرض الخدمة" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-user-tag text-blue-600"></i>
                            {{ $service->name_ar }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            تفاصيل الخدمة
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.services.edit', $service) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                        <a href="{{ route('dashboard.services.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i>
                            رجوع للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-8">

                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-id-card-alt text-blue-600"></i>
                        بيانات الخدمة
                    </h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                            صورة الخدمة
                        </label>
                        <div>
                            <img class="w-64 cursor-pointer h-56 rounded"
                                onclick="openModal('{{ asset('storage/' . $service->image) }}')"
                                src="{{ asset('storage/' . $service->image) }}" alt="">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الاسم
                                (العربي)</label>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $service->name_ar }}</span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                                الاسم (الانجليزي)
                            </label>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $service->name_en
                                }}</span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                                نسبة شركة ايفورك
                            </label>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $service->evork_commission
                                }}</span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                                هل الخدمة متوفرة لشاشة الشركاء
                            </label>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $service->show_in_partner_screen == 1 ? 'نعم' : 'لا'
                                }}</span>
                        </div>

                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الحالة</label>
                            <div class="flex items-center gap-1">
                                <span class="text-2xl font-bold text-green-700 dark:text-green-400">
                                    {{ $service->status == 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="border-t pt-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i>
                        تاريخ الإضافة
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                                الإضافة</label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $service->created_at->format('Y-m-d H:i') }}
                            </span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">آخر
                                تحديث</label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $service->updated_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
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