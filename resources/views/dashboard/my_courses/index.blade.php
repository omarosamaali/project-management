@extends('layouts.app')

@section('title', 'دوراتي التدريبية')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_courses.index') }}" second="دوراتي التدريبية" />

    {{-- الاحصائيات العلوية (اختياري) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="flex bg-black justify-between rounded-lg overflow-hidden">
            <div class="p-4 pr-6 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white whitespace-nowrap">إجمالي الدورات</h1>
                <p class="text-2xl flex items-center text-white">
                    {{ $myPayments->count() }} دورة
                </p>
            </div>
            <div class="p-5 bg-[#181818]">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-30" alt="">
            </div>
        </div>
    </div>

    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

            {{-- التنبيهات --}}
            @if(session('success'))
            <div
                class="m-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-4 text-right">رقم الطلب</th>
                            <th scope="col" class="px-4 py-4">اسم الدورة</th>
                            <th scope="col" class="px-4 py-4">تاريخ الاشتراك</th>
                            <th scope="col" class="px-4 py-4">الحالة</th>
                            <th scope="col" class="px-4 py-4 text-center">المحاضرة / اللينك</th>
                            <th scope="col" class="px-4 py-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myPayments as $payment)
                        @php
                        $course = $payment->course;
                        $now = \Carbon\Carbon::now();
                        $startDate = \Carbon\Carbon::parse($course->start_date);
                        $endDate = \Carbon\Carbon::parse($course->end_date);

                        // منطق ظهور الرابط قبل 30 دقيقة وحتى نهاية الموعد
                        $showLink = $now->greaterThanOrEqualTo($startDate->copy()->subMinutes(30)) &&
                        $now->lessThanOrEqualTo($endDate);
                        $isFinished = $now->greaterThan($endDate);
                        @endphp
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 transition-colors">
                            {{-- رقم الطلب --}}
                            <td class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $payment->payment_id ?? $payment->id }}
                            </td>

                            {{-- العميل/الدورة والوصف --}}
                            <td class="px-4 py-4">
                                <div class="font-bold text-black dark:text-white">{{ $course->name_ar }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ Str::limit($course->description_ar, 40) }}
                                </div>
                            </td>

                            {{-- تاريخ الاشتراك --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                {{ $payment->created_at->format('Y-m-d') }}
                            </td>

                            {{-- حالة الدفع --}}
                            <td class="px-4 py-4 text-sm">
                                مدفوع
                            </td>

                            {{-- لينك المحاضرة --}}
                            <td class="px-4 py-4 text-center">
                                @if($course->location_type == 'online')
                                @if($showLink)
                                <a href="{{ $course->online_link }}" target="_blank"
                                    class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 animate-pulse">
                                    <i class="fas fa-video ml-2"></i> دخول المحاضرة
                                </a>
                                @elseif($isFinished)
                                <span class="text-red-500 font-semibold italic text-xs">الدورة انتهت</span>
                                @else
                                <div class="text-gray-400 text-xs flex flex-col">
                                    <span>الرابط سيظهر في:</span>
                                    <span class="font-bold">{{ $startDate->format('Y-m-d H:i') }}</span>
                                </div>
                                @endif
                                @else
                                <span class="text-gray-500 text-xs italic">حضور شخصي (مقر)</span>
                                @endif
                            </td>

                            {{-- فاتورة --}}
                            <td class="px-4 py-4 text-left">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                                        class="text-gray-600 hover:text-blue-600" title="عرض التفاصيل">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('dashboard.payment.invoice', $payment->id) }}" class="btn-style">
                                        <i class="fas fa-file-invoice"></i> 
                                    </a>
                                    @if($payment->is_attended)
                                                                            <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                                                                                class="px-3 py-.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                                                                <i class="fas fa-certificate"></i> الشهادة
                                                                            </a>
                                                                            @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                لم تشترك في أي دورة بعد.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection