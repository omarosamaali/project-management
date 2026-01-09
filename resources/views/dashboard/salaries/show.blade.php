@extends('layouts.app')

@section('title', 'عرض تفاصيل الراتب')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.salaries.index') }}" second="رواتب الموظفين"
        third="عرض التفاصيل" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                            سجل راتب: {{ $salary->user->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            شهر {{ $salary->month }} لعام {{ $salary->year }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard.salaries.edit', $salary) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                        <a href="{{ route('dashboard.salaries.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-8">

                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-600"></i>
                        البيانات العامة والمرفق
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6 items-center">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">سند التحويل /
                                المرفق</label>
                            @if($salary->attachment)
                            <img class="w-full max-w-xs cursor-pointer h-48 object-cover rounded-lg border shadow-sm hover:opacity-90 transition"
                                onclick="openModal('{{ asset('storage/' . $salary->attachment) }}')"
                                src="{{ asset('storage/' . $salary->attachment) }}" alt="سند الراتب">
                            @else
                            <div
                                class="w-full max-w-xs h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center rounded-lg border-2 border-dashed">
                                <span class="text-gray-400">لا يوجد مرفق</span>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">اسم
                                    الموظف</label>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $salary->user->name
                                    }}</span>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">الدولة</label>
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{
                                    $salary->user->country_name ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-calculator text-blue-600"></i>
                        التفاصيل المالية
                    </h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-l-4 border-blue-500">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">قيمة
                                الإضافي</label>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{
                                number_format($salary->overtime_value, 2) }}</span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-l-4 border-purple-500">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">المترحل</label>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{
                                number_format($salary->carried_forward, 2) }}</span>
                        </div>

                        <div class="bg-red-50 dark:bg-gray-700 p-4 rounded-lg border-l-4 border-red-500">
                            <label class="block text-sm font-medium text-red-600 dark:text-red-400">الخصومات (-)</label>
                            <span class="text-xl font-bold text-red-700 dark:text-red-400">{{
                                number_format($salary->deduction_value, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 bg-blue-600 p-6 rounded-xl shadow-lg text-white flex justify-between items-center">
                        <div>
                            <p class="text-blue-100 text-sm">إجمالي الراتب المستحق النهائي</p>
                            <h3 class="text-3xl font-black">{{ number_format($salary->total_due, 2) }}</h3>
                        </div>
                        <i class="fas fa-money-check-alt text-5xl opacity-30"></i>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">تاريخ
                                الإنشاء</label>
                            <span class="text-md font-semibold text-gray-900 dark:text-white">
                                <i class="far fa-calendar-alt ml-1"></i> {{ $salary->created_at->format('Y-m-d - h:i A')
                                }}
                            </span>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">آخر تحديث
                                للبيانات</label>
                            <span class="text-md font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-history ml-1"></i> {{ $salary->updated_at->format('Y-m-d - h:i A') }}
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
                imageAlt: 'سند الراتب',
                showCloseButton: true,
                showConfirmButton: false,
                background: '#fff',
                width: 'auto',
                customClass: {
                    image: 'rounded-lg shadow-md'
                }
            });
        }
    </script>
</section>
@endsection