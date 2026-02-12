@extends('layouts.app')

@section('title', 'فاتورة الدورة - ' . ($course->name_ar ?? ''))

@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-4xl">
        {{-- أزرار التحكم --}}
        <div class="mb-4 flex justify-between items-center no-print">
            <a href="javascript:history.back()"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
            <button onclick="window.print()"
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-print"></i> طباعة الفاتورة
            </button>
        </div>

        {{-- جسم الفاتورة --}}
        <div class="bg-white shadow-2xl rounded-xl overflow-hidden border-2 border-gray-200 print-container">

            {{-- الهيدر --}}
            <div class="grid lg:grid-cols-3 md:grid-cols-1 text-white p-8 border-b border-t">
                <div class="bg-white p-2 rounded-lg w-fit mx-auto lg:order-2">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="Logo" class="h-24 w-24 object-contain">
                </div>

                <div class="text-right lg:order-1">
                    <h1 class="text-xl text-gray-700 font-bold mb-1">ايفورك للتكنولوجيا</h1>
                    <p class="text-sm opacity-90 text-gray-700">الامارات العربية المتحدة, دبي</p>
                    <p class="text-sm opacity-90 text-gray-700">شركة مرخصة من دائرة الاقتصاد والسياحة</p>
                </div>

                <div class="text-left lg:order-3">
                    <h1 class="text-xl text-gray-700 font-bold mb-1">EVORQ TECHNOLOGIES</h1>
                    <p class="text-sm opacity-90 text-gray-700">UAE, Dubai, Al Warqa 2</p>
                    <p class="text-sm opacity-90 text-gray-700">Licensed by Dubai Economy & Tourism</p>
                </div>
            </div>

            {{-- بيانات العميل --}}
            <div class="p-8 grid md:grid-cols-2 gap-8 border-b">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-gray-600"></i> بيانات العميل / Customer Details
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-gray-600 text-sm">{{ Auth::user()->phone ?? '-' }}</p>
                        <p class="text-gray-600 text-sm">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            {{-- تفاصيل الدفع والكورس --}}
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-list-alt text-gray-600"></i> تفاصيل الفاتورة / Invoice Details
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="text-right py-3 px-4 font-bold text-gray-700">البيان</th>
                                <th class="text-center py-3 px-4 font-bold text-gray-700">التفاصيل / Details</th>
                                <th class="py-3 px-4 font-bold text-gray-700 text-left">Statement</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            {{-- --}}
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">رقم العملية</td>
                                <td class="text-center py-3 px-4 text-gray-900">#{{ $payment->payment_id }}</td>
                                <td class="py-3 px-4 text-gray-900 text-left">Transaction ID</td>
                            </tr>
                            {{-- اسم الكورس --}}
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">الدورة التدريبية</td>
                                <td class="text-center py-3 px-4 text-gray-600 font-semibold">{{
                                    $payment->course->name_ar }}</td>
                                <td class="py-3 px-4 font-bold text-gray-700 text-left">Course Name</td>
                            </tr>
                            {{-- تاريخ الدفع --}}
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">تاريخ الدفع</td>
                                <td class="text-center py-3 px-4 text-gray-900">{{ $payment->created_at->format('Y-m-d
                                    h:i') }}</td>
                                <td class="py-3 px-4 font-bold text-gray-700 text-left">Payment Date</td>
                            </tr>
                            {{-- الحسابات المالية --}}
                            @php
                            $total = floatval($payment->amount);
                            $base = round(($total - 2) / 1.079, 2);
                            $fees = round($total - $base, 2);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">المبلغ الأساسي</td>
                                <td
                                    class="flex items-center gap-1 mx-auto justify-center text-center py-3 px-4 text-gray-600 font-semibold">
                                    {{ number_format($base, 2)
                                    }}
                                    <x-drhm-icon width="12" height="14" />
                                </td>
                                <td class="py-3 px-4 font-bold text-gray-700 text-left">Base Price</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">رسوم بوابة الدفع</td>
                                <td
                                    class="flex items-center gap-1 mx-auto justify-center text-center py-3 px-4 text-gray-600 font-semibold">
                                    {{ number_format($fees, 2)
                                    }}
                                    <x-drhm-icon width="12" height="14" />
                                </td>
                                <td class="py-3 px-4 font-bold text-gray-700 text-left">Payment gateway fees</td>
                            </tr>
                            {{-- الإجمالي --}}
                            <tr class="bg-gray-50">
                                <td class="py-3 px-4 font-bold text-black text-lg">الإجمالي المدفوع</td>
                                <td
                                    class="flex items-center gap-1 mx-auto justify-center text-center py-3 px-4 text-black font-bold text-xl">
                                    {{ number_format($total,
                                    2) }}
                                    <x-drhm-icon width="12" height="14" />
                                </td>
                                <td class="py-3 px-4 font-bold text-black text-left text-lg">Total Paid</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 p-8 border-t-2 border-gray-200">
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">ملاحظات</h4>
                        <p class="text-gray-600 text-sm">تم إنشاء وإصدار هذه الفاتورة إلكترونياً ولا تحتاج لتوقيع.</p>
                    </div>
                    <div>
                        <h4 class="text-left font-bold text-gray-800 mb-2">Notes</h4>
                        <p class="text-left text-gray-600 text-sm">Computer-generated invoice, no signature required.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .print-container,
        .print-container * {
            visibility: visible;
        }

        .print-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }

        @page {
            margin: 1cm;
            size: A4;
        }
    }
</style>
@endsection