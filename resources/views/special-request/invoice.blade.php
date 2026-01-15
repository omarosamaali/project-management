@extends('layouts.app')

@section('title', 'فاتورة الدفع - طلب خاص')

@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-4xl">
        <!-- أزرار الطباعة والرجوع -->
        <div class="mb-4 flex justify-between items-center no-print">
            <a href="{{ route('dashboard.special-request.show', $specialRequest->id) }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                رجوع إلى تفاصيل الطلب
            </a>
            <button onclick="window.print()"
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-print"></i>
                طباعة الفاتورة
            </button>
        </div>

        <!-- الفاتورة -->
        <div class="bg-white shadow-2xl rounded-xl overflow-hidden border-2 border-gray-200 print-container">

            <!-- Header الفاتورة مع الشعار -->
            <div class="grid lg:grid-cols-3 md:grid-cols-1 text-white p-8 border-b border-t">
                <div class="bg-white p-2 rounded-lg w-fit mx-auto lg:order-2">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="iFork Logo" class="h-24 w-24 object-contain">
                </div>

                <div class="text-right lg:order-1">
                    <h1 class="text-xl text-gray-700 font-bold mb-1">ايفورك للتكنولوجيا</h1>
                    <p class="text-sm opacity-90 text-gray-700">الامارات العربية المتحدة, إمارة دبي</p>
                    <p class="text-sm opacity-90 text-gray-700">منطقة الورقاء2</p>
                    <p class="text-sm opacity-90 text-gray-700">شركة مرخصة من دائرة الاقتصاد والسياحة بدبي</p>
                </div>

                <div class="text-left lg:order-3">
                    <h1 class="text-xl text-gray-700 font-bold mb-1">EVORQ TECHNOLOGIES</h1>
                    <p class="text-sm opacity-90 text-gray-700">United Arab Emirates, Dubai</p>
                    <p class="text-sm opacity-90 text-gray-700">Al Warqa 2</p>
                    <p class="text-sm opacity-90 text-gray-700">A company licensed by the Dubai Department of Economy
                        and Tourism</p>
                </div>
            </div>

            <!-- معلومات العميل -->
            <div class="p-8 grid md:grid-cols-2 gap-8 border-b">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-gray-600"></i>
                        بيانات العميل / Customer Details
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-gray-600 text-sm">{{ Auth::user()->phone ?? '-' }}</p>
                        <p class="text-gray-600 text-sm">{{ Auth::user()->email }}</p>
                        <p class="text-gray-600 text-sm">{{ Auth::user()->country_name ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-receipt text-gray-600"></i>
                        تفاصيل الدفع / Payment Details
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <p class="text-gray-600 text-sm"><strong>رقم الفاتورة:</strong> INV-{{ $payment->id }}-{{
                            $specialRequest->id }}</p>
                        <p class="text-gray-600 text-sm"><strong>تاريخ الدفع:</strong> {{ now()->format('Y-m-d H:i') }}
                        </p>
                        <p class="text-gray-600 text-sm"><strong>طريقة الدفع:</strong> Ziina (بوابة دفع إلكترونية)</p>
                        @if($installment)
                        <p class="text-gray-600 text-sm"><strong>نوع الدفع:</strong> دفعة جزئية - {{
                            $installment->payment_name }}</p>
                        @else
                        <p class="text-gray-600 text-sm"><strong>نوع الدفع:</strong> دفع كامل للطلب الخاص</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- تفاصيل الطلب والدفع -->
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-list-alt text-gray-600"></i>
                    تفاصيل الدفع / Payment Details
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="text-right py-3 px-4 font-bold text-gray-700">البيان</th>
                                <th class="text-center py-3 px-4 font-bold text-gray-700">المبلغ</th>
                                <th class="py-3 px-4 font-bold text-gray-700 text-left">Statement</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">المبلغ الأساسي</td>
                                <td class="text-center py-3 px-4 text-gray-900 font-bold flex items-center gap-1">
                                    {{ number_format($payment->original_price, 2) }}
                                    <x-drhm-icon width="12" height="14" />
                                </td>
                                <td class="py-3 px-4 text-gray-900 text-left">Base Amount</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">رسوم بوابة الدفع (7.9% + 2 درهم)</td>
                                <td class="text-center py-3 px-4 text-orange-600 font-bold flex items-center gap-1">
                                    {{ number_format($payment->fees, 2) }}
                                    <x-drhm-icon width="12" height="14" />
                                </td>
                                <td class="py-3 px-4 text-orange-600 text-left">Payment Gateway Fees</td>
                            </tr>
                            <tr class="bg-emerald-50">
                                <td class="py-4 px-4 font-bold text-gray-800">الإجمالي المدفوع</td>
                                <td class="text-center py-4 px-4 text-emerald-600 font-bold text-xl flex items-center gap-1">
                                    {{ number_format($payment->amount, 2) }}
                                    <x-drhm-icon width="12" height="14" />
                                </td>
                                <td class="py-4 px-4 text-emerald-600 font-bold text-left">Total Paid</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($installment)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800 font-medium">
                        <i class="fas fa-info-circle ml-2"></i>
                        هذه دفعة جزئية من إجمالي الطلب الخاص ({{ number_format($specialRequest->price, 2) }}
                        <x-drhm-icon width="12" height="14" />)
                        <br>
                        المتبقي: {{ number_format($specialRequest->remaining_amount, 2) }}
                        <x-drhm-icon width="12" height="14" />
                    </p>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 p-8 border-t-2 border-gray-200">
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-bold text-gray-800 mb-2">ملاحظات</h4>
                        <p class="text-gray-600 text-sm">
                            تم إنشاء وإصدار هذه الفاتورة إلكترونيًا ولا تحتاج لتصديق.<br>
                            شكرًا لثقتك بنا، ونتطلع لتقديم أفضل خدمة لك.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-left font-bold text-gray-800 mb-2">Notes</h4>
                        <p class="text-left text-gray-600 text-sm">
                            This invoice was created and issued electronically and does not require authentication.<br>
                            Thank you for your trust. We look forward to providing you with the best service.
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

        body {
            background: white !important;
        }

        section {
            padding: 0 !important;
        }

        @page {
            margin: 1cm;
            size: A4;
        }

        .print-container {
            page-break-inside: avoid;
        }
    }
</style>
@endsection