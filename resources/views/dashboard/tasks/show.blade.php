@extends('layouts.app')

@section('title', 'عرض تفاصيل الطلب')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.requests.index') }}" second="الطلبات" third="عرض الطلب" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-user-tag text-blue-600"></i>
                            {{ $userRequest->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            تفاصيل الطلب الأساسية والأداء
                        </p>
                    </div>
                    <div class="flex gap-2">
                        @if(Auth::user()->role == 'admin')
                        <a href="{{ route('dashboard.requests.edit', $userRequest->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                        @endif
                        <a href="{{ route('dashboard.requests.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i>
                            رجوع للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-8">

                {{-- بيانات الطلب --}}
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        تفاصيل الطلب
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">

                        {{-- رقم الطلب --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">رقم
                                الطلب</label>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $userRequest->order_number }}
                            </span>
                        </div>

                        {{-- تاريخ الطلب --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                                الطلب</label>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($userRequest->created_at)->format('Y-m-d') }}
                            </span>
                        </div>

                        {{-- النظام --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">النظام</label>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                {{ $userRequest->system?->name_ar ?? '—' }}
                            </span>
                        </div>

                        {{-- الشريك --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">اسم
                                الشريك</label>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $userRequest ->system->partners->pluck('name')->implode(' - ') }}
                            </span>
                        </div>

                        {{-- العميل --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">اسم
                                العميل</label>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $userRequest->client?->name ?? '-' }}
                            </span>
                        </div>

                        {{-- مدة التنفيذ / الحالة --}}
                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">مدة
                                التنفيذ</label>
                            <span class="text-lg font-bold text-green-700 dark:text-green-400">
                                {{ $userRequest->system->execution_days_from }} - {{
                                $userRequest->system->execution_days_to }} يوم عمل
                            </span>
                        </div>

                        {{-- حالة الطلب --}}
                        <div class="bg-yellow-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label
                                class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الحالة</label>
                            <span class="text-lg font-bold text-yellow-700 dark:text-yellow-400">
                                {{ $userRequest->status_label }}
                            </span>
                        </div>

                    </div>
                </div>

                {{-- التواريخ --}}
                <div class="border-t pt-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i>
                        تاريخ الإضافة والتحديث
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                                الإضافة</label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($userRequest->created_at)->format('Y-m-d') }}
                            </span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">آخر
                                تحديث</label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($userRequest->updated_at)->format('Y-m-d H:i') }}
                            </span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection