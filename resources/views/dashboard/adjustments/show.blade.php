@extends('layouts.app')

@section('title', 'عرض تفاصيل الراتب')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.salaries.index') }}" second="رواتب الموظفين"
        third="عرض التفاصيل" />

    <div class="mx-auto max-w-4xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-2xl border rounded-xl overflow-hidden">

            {{-- الهيدر --}}
            <div class="p-6 border-b bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                        راتب الموظف: {{ $salary->user->name }}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">عن شهر {{ $salary->month }} لعام {{ $salary->year }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard.salaries.edit', $salary) }}"
                        class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-600 hover:text-white transition">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('dashboard.salaries.index') }}"
                        class="p-2 bg-gray-100 text-gray-600 rounded-full hover:bg-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>

            <div class="p-6 space-y-8">
                {{-- المرفق والبيانات الأساسية --}}
                <div class="grid md:grid-cols-2 gap-8 items-start">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">سند التحويل /
                            المرفق:</label>
                        @if($salary->attachment)
                        <div class="group relative">
                            <img src="{{ asset('storage/' . $salary->attachment) }}"
                                class="w-full h-56 object-cover rounded-xl border shadow-sm cursor-zoom-in group-hover:opacity-90 transition"
                                onclick="viewImage('{{ asset('storage/' . $salary->attachment) }}')">
                            <div class="absolute bottom-2 right-2 bg-black/50 text-white px-2 py-1 rounded text-xs">اضغط
                                للتكبير</div>
                        </div>
                        @else
                        <div
                            class="h-56 bg-gray-100 dark:bg-gray-700 rounded-xl border-2 border-dashed flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-image text-4xl mb-2"></i>
                            <span>لا يوجد مرفق</span>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-blue-500">
                            <span class="block text-xs text-gray-500 uppercase">اسم الموظف</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $salary->user->name
                                }}</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-green-500">
                            <span class="block text-xs text-gray-500 uppercase">الدولة</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $salary->user->country_name
                                ?? 'غير محدد' }}</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-r-4 border-yellow-500">
                            <span class="block text-xs text-gray-500 uppercase">تاريخ الإصدار</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-white">{{
                                $salary->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                {{-- تفاصيل المبالغ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-white border rounded-xl text-center shadow-sm">
                        <p class="text-gray-500 text-sm mb-1 text-xs">قيمة الإضافي</p>
                        <p class="text-xl font-bold text-green-600">+{{ number_format($salary->overtime_value, 2) }}</p>
                    </div>
                    <div class="p-4 bg-white border rounded-xl text-center shadow-sm">
                        <p class="text-gray-500 text-sm mb-1 text-xs">المترحل</p>
                        <p class="text-xl font-bold text-purple-600">+{{ number_format($salary->carried_forward, 2) }}
                        </p>
                    </div>
                    <div class="p-4 bg-white border rounded-xl text-center shadow-sm">
                        <p class="text-gray-500 text-sm mb-1 text-xs">الخصومات</p>
                        <p class="text-xl font-bold text-black">-{{ number_format($salary->deduction_value, 2) }}</p>
                    </div>
                </div>

                {{-- الإجمالي النهائي --}}
                <div
                    class="bg-gradient-to-l from-blue-600 to-blue-800 p-6 rounded-2xl shadow-xl text-white flex justify-between items-center">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">صافي الراتب المستحق</p>
                        <h2 class="text-4xl font-black">{{ number_format($salary->total_due, 2) }} <span
                                class="text-lg font-normal">EGP</span></h2>
                    </div>
                    <i class="fas fa-wallet text-5xl opacity-20"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewImage(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'سند الراتب',
            showConfirmButton: false,
            showCloseButton: true,
            width: 'auto',
            customClass: { image: 'rounded-xl' }
        });
    }
</script>
@endsection