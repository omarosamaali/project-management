@extends('layouts.app')

@section('title', 'فاتورة الطلب')

@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-4xl">
        <div class="mb-4 flex justify-between items-center no-print">
            <a href="{{ route('dashboard.requests.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
            <button onclick="window.print()"
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                <i class="fas fa-print"></i>
                طباعة الفاتورة
            </button>
        </div>

        <div class="bg-white shadow-2xl rounded-xl overflow-hidden border-2 border-gray-200 print-container">

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
                        and Tourism
                    </p>
                </div>
            </div>

            <div class="p-8 grid md:grid-cols-2 gap-8 border-b">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-gray-600"></i>
                        بيانات العميل / Customer Details
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <p class="font-bold text-gray-900">{{ $userRequest->user?->name ?? 'عميل' }}</p>
                        <p class="text-gray-600 text-sm">{{ $userRequest->user?->phone?? '' }}</p>
                        <p class="text-gray-600 text-sm">{{ $userRequest->user?->email ?? ''}}</p>
                        <p class="text-gray-600 text-sm">
                            {{ $userRequest->user?->country_name }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-list-alt text-gray-600"></i>
                    تفاصيل الطلب / Order Details
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
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-700">رقم الطلب</td>
                                <td class="text-center py-3 px-4 text-gray-900 font-mono">{{ $userRequest->order_number
                                    }}</td>
                                <td class="font-bold py-3 px-4 text-gray-900 text-left">Order Number</td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-700">تاريخ الطلب</td>
                                <td class="text-center py-3 px-4 text-gray-900">
                                    {{ \Carbon\Carbon::parse($userRequest->created_at)->format('Y-m-d H:i') }}
                                </td>
                                <td class="font-bold py-3 px-4 text-gray-900 text-left">Order Date</td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-700">النظام</td>
                                <td class="text-center py-3 px-4 text-gray-600 font-semibold">{{ $userRequest->title }}
                                </td>
                                <td class="font-bold py-3 px-4 text-gray-900 text-left">System</td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-700">مدة التنفيذ (بالأيام)</td>
                                <td class="text-center py-3 px-4 text-gray-600">{{ $userRequest->deadline }}</td>
                                <td class="font-bold py-3 px-4 text-gray-900 text-left">Execution Period (By days)</td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-700">السعر</td>
                                <td class="text-center py-3 px-4 text-gray-600 text-sm leading-relaxed">
                                    <div class="flex items-center justify-center gap-1">
                                        {{ $userRequest->budget }}
                                        <x-drhm-icon width="12" height="14" />
                                    </div>
                                </td>
                                <td class="font-bold py-3 px-4 text-gray-900 text-left">Price</td>
                            </tr>

                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-700">رسوم بوابة الدفع</td>
                                <td class="text-center py-3 px-4 text-gray-600 text-sm">
                                    <div class="flex items-center justify-center gap-1">
                                        {{ number_format($userRequest->budget * 0.079, 2) }}
                                        <x-drhm-icon width="12" height="14" />
                                    </div>
                                </td>
                                <td class="font-bold py-3 px-4 text-gray-900 text-left">Payment Gateway Fee</td>
                            </tr>


                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-700">السعر الإجمالي</td>
                                <td class="text-center py-3 px-4 text-black font-semibold">
                                    <div class="flex items-center justify-center gap-1">
                                        {{ $userRequest->budget + $userRequest->budget * 0.079 }}
                                        <x-drhm-icon width="12" height="14" />
                                    </div>
                                </td>
                                <td class="font-bold py-3 px-4 text-gray-900 text-left">Total Price</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 p-8 border-t-2 border-gray-200">
                <div>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-bold text-gray-800 mb-2">ملاحظات</h4>
                            <p class="text-gray-600 text-sm">تم إنشاء وإصدار هذه الفاتوره إلكترونيا ولا تحتاج لتصديق
                            </p>
                        </div>
                        <div>
                            <h4 class="text-left font-bold text-gray-800 mb-2">Notes</h4>
                            <p class="text-left text-gray-600 text-sm">This invoice was created and issued
                                electronically
                                and does not
                                require authentication.</p>
                        </div>
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