@extends('layouts.app')

@section('title', 'فاتورة الطلب')

@section('content')
<section class="p-3 sm:p-5">
    <div class="mx-auto max-w-4xl">
        <!-- أزرار الطباعة والرجوع -->
        <div class="mb-4 flex justify-between items-center no-print">
            <a href="{{ route('dashboard.requests.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
            <button onclick="window.print()"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
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
                        and Tourism
                    </p>
                </div>
            </div>

            <!-- معلومات الشركة والعميل -->
            <div class="p-8 grid md:grid-cols-2 gap-8 border-b">
                <!-- معلومات الشركة -->
                {{-- <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-building text-blue-600"></i>
                        من / From
                    </h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <p class="font-bold text-gray-900 text-lg">شركة ايفورك</p>
                        <p class="text-gray-600 text-sm">الامارات العربية المتحدة</p>
                        <p class="text-gray-600 text-sm">إمارة دبي</p>
                    </div>
                </div> --}}

                <!-- معلومات العميل -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        بيانات العميل / Customer Details
                    </h3>
                    <div class="bg-blue-50 p-4 rounded-lg space-y-2">
                        <p class="font-bold text-gray-900">{{ $userRequest->client?->name ?? 'عميل' }}</p>
                        <p class="text-gray-600 text-sm">{{ $userRequest->client?->phone?? '' }}</p>
                        <p class="text-gray-600 text-sm">{{ $userRequest->client?->email ?? ''}}</p>
                        <p class="text-gray-600 text-sm">مصر</p>
                    </div>
                </div>
            </div>

            <!-- تفاصيل الطلب -->
            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-list-alt text-blue-600"></i>
                    تفاصيل الطلب / Order Details
                </h3>

                <!-- جدول التفاصيل -->
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
                                <td class="py-3 px-4 font-medium text-gray-700">رقم الطلب </td>
                                <td class="text-center py-3 px-4 text-gray-900">{{ $userRequest->order_number }}</td>
                                <td class="py-3 px-4 text-gray-900 text-left">Order Number</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">تاريخ الطلب</td>
                                <td class="text-center py-3 px-4 text-gray-900">{{
                                    \Carbon\Carbon::parse($userRequest->created_at)->format('Y-m-d H:i') }}
                                </td>
                                <th class="py-3 px-4 font-bold text-gray-700 text-left">Order Date</th>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">النظام</td>
                                <td class="text-center py-3 px-4 text-blue-600 font-semibold">{{ $userRequest->system?->name_ar ??
                                    '-' }}
                                </td>
                                <th class="py-3 px-4 font-bold text-gray-700 text-left">System</th>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">مدة التنفيذ</td>
                                <td class="text-center py-3 px-4 text-green-600 font-semibold">
                                    {{ $userRequest->system->execution_days_from }} - {{
                                    $userRequest->system->execution_days_to }} يوم عمل
                                </td>
                                <th class="py-3 px-4 font-bold text-gray-700 text-left">Execution Period</th>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">عدد ايام الدعم الفني</td>
                                <td class="text-center py-3 px-4">
                                    <span
                                        class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold">
                                        {{ $userRequest->system->support_days }} يوم يوم
                                    </span>
                                </td>
                                <th class="py-3 px-4 font-bold text-gray-700 text-left">Support Days</th>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-700">السعر</td>
                                <td class="justify-center py-3 px-4 text-green-600 font-semibold flex items-center">
                                    {{ $userRequest->system->price }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path d="M8 7V17H12C14.8 17 17 14.8 17 12C17 9.2 14.8 7 12 7H8Z" stroke="green"
                                            stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path d="M6.5 11H18.5" stroke="green" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M6.5 13H12.5H18.5" stroke="green" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round">
                                        </path>
                                    </svg>
                                </td>
                                <th class="py-3 px-4 font-bold text-gray-700 text-left">Price</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 p-8 border-t-2 border-gray-200">
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">ملاحظات / Notes:</h4>
                    <div class="grid md:grid-cols-2 gap-8">
                        <p class="text-gray-600 text-sm">تم إنشاء وإصدار هذه الفاتوره إلكترونيا ولا تحتاج لتصديق
                        </p>
                        <p class="text-left text-gray-600 text-sm">This invoice was created and issued electronically
                            and does not
                            require authentication.</p>
                    </div>
                </div>
            </div>

            <!-- الختم والتوقيع -->
            {{-- <div class="p-8 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex justify-between items-end">
                    <div class="text-center">
                        <div class="h-20 border-b-2 border-gray-300 w-48 mb-2"></div>
                        <p class="text-sm text-gray-600 font-semibold">شركة ايفورك للتكنولوجيا</p>
                        <p class="text-xs text-gray-500">iFork Technology Company</p>
                    </div>
                </div>
            </div> --}}

        </div>

        <!-- معلومات إضافية -->
        <div class="mt-4 text-center text-gray-500 text-sm no-print">
            <p>هذه فاتورة إلكترونية صادرة من نظام إدارة الطلبات</p>
        </div>
    </div>
</section>

<style>
    @media print {

        /* إخفاء كل عناصر الصفحة عند الطباعة */
        body * {
            visibility: hidden;
        }

        /* إظهار الفاتورة فقط */
        .print-container,
        .print-container * {
            visibility: visible;
        }

        /* وضع الفاتورة في أعلى الصفحة */
        .print-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* إخفاء الأزرار والعناصر غير المطلوبة */
        .no-print {
            display: none !important;
        }

        /* إزالة خلفية الصفحة */
        body {
            background: white !important;
        }

        /* إزالة الـ padding والـ margin */
        section {
            padding: 0 !important;
        }

        /* ضبط الهوامش */
        @page {
            margin: 1cm;
            size: A4;
        }

        /* التأكد من عدم قطع المحتوى */
        .print-container {
            page-break-inside: avoid;
        }
    }
</style>
@endsection