@extends('layouts.app')

@section('title', 'عرض تفاصيل الشريك')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.settings.index') }}" second="الاعدادات" third="عرض الشريك" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-user-tag text-blue-600"></i>
                            {{ $setting->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            تفاصيل الشريك الأساسية والأداء
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.settings.edit', $setting->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                        <a href="{{ route('dashboard.settings.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i>
                            رجوع للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-8">

                <!-- Contact -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-id-card-alt text-blue-600"></i>
                        بيانات التواصل والتعاقد
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الاسم</label>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $setting->name }}</span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">البريد
                                الإلكتروني</label>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $setting->email
                                }}</span>
                        </div>

                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">نسبة
                                الشريك</label>
                            <div class="flex items-center gap-1">
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($setting->percentage, 2) }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-400 text-lg">%</span>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">إجمالي عدد
                                الطلبات</label>
                            <div class="flex items-center gap-1">
                                <span class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">
                                    {{ number_format($setting->orders ?? 0) }}
                                </span>
                                <span class="text-gray-600 dark:text-gray-400">طلب</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- الأنظمة المرتبطة -->
                <div class="pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-boxes text-blue-600"></i>
                        الأنظمة المرتبطة
                    </h2>
                    @if($setting->systems->isNotEmpty())
                    {{ Str::limit(implode(' - ', $setting->systems->pluck('name_ar')->toArray()), 60) }}
                    @else
                    لا توجد أنظمة مرتبطة بهذا الشريك حاليًا.
                    @endif
                </div>

                <!-- طرق سحب الأرباح -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-wallet text-blue-600"></i>
                        طرق سحب الأرباح
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- طريقة السحب -->
                        <div class="bg-purple-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">طريقة
                                السحب</label>
                            @if($setting->withdrawal_method)
                            <div class="flex items-center gap-2">
                                @if($setting->withdrawal_method == 'wallet')
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                    <path fill-rule="evenodd"
                                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">محفظة
                                    إلكترونية</span>
                                @elseif($setting->withdrawal_method == 'paypal')
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">PayPal</span>
                                @endif
                            </div>
                            @else
                            <span class="text-gray-500 dark:text-gray-400 italic">لم يتم تحديد طريقة السحب</span>
                            @endif
                        </div>

                        <!-- البريد الإلكتروني للسحب -->
                        <div class="bg-blue-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">البريد
                                الإلكتروني
                                للسحب او رقم الهاتف</label>
                            @if($setting->withdrawal_email)
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-blue-600"></i>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{
                                    $setting->withdrawal_email }}</span>
                            </div>
                            @else
                            <span class="text-gray-500 dark:text-gray-400 italic">لم يتم تحديد بريد إلكتروني</span>
                            @endif
                        </div>
                    </div>

                    <!-- الملاحظات الإضافية -->
                    @if($setting->withdrawal_notes)
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <label
                            class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-yellow-600"></i>
                            ملاحظات إضافية
                        </label>
                        <p class="text-gray-900 dark:text-white leading-relaxed">{{ $setting->withdrawal_notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- تاريخ النظام -->
                <div class="border-t pt-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i>
                        تاريخ النظام
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                                الإضافة</label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $setting->created_at->format('Y-m-d H:i') }}
                            </span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">آخر
                                تحديث</label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $setting->updated_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection