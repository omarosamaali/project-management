@extends('layouts.app')

@section('title', 'عرض تفاصيل العميل')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.clients.index') }}" second="العملاء" third="عرض العميل" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-user-tag text-blue-600"></i>
                            {{ $client->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            تفاصيل العميل الأساسية والأداء
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.clients.edit', $client->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                        <a href="{{ route('dashboard.clients.index') }}"
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
                        بيانات التواصل والتعاقد
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الاسم</label>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $client->name }}</span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">البريد
                                الإلكتروني</label>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $client->email
                                }}</span>
                        </div>

                        <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الحالة</label>
                            <div class="flex items-center gap-1">
                                <span class="text-2xl font-bold text-green-700 dark:text-green-400">
                                    {{ $client->status == 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                                رقم الهاتف</label>
                            <div class="flex items-center gap-1">
                                <span class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">
                                    {{ $client->phone }}
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
                                {{ $client->created_at->format('Y-m-d H:i') }}
                            </span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">آخر
                                تحديث</label>
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $client->updated_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection