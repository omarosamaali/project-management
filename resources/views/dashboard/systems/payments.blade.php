@extends('layouts.app')

@section('title', 'فواتير النظام - ' . $system->name_ar)

@section('content')
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    فواتير النظام: {{ $system->name_ar }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    عرض جميع الفواتير والمدفوعات الخاصة بالنظام
                </p>
            </div>
            <a href="{{ route('dashboard.systems.index') }}"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <i class="fas fa-file-invoice text-blue-600 dark:text-blue-300"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">إجمالي الفواتير</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $system->payments->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-300"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">مدفوعة</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $system->payments->where('status', 'success')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <i class="fas fa-coins text-purple-600 dark:text-purple-300"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">إجمالي المبلغ</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-1">
                            {{ number_format($system->payments->sum('amount'), 2) }}
                            <span class="text-sm "> <x-drhm-icon color="ffffff" width="14" height="14" /></span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <i class="fas fa-percentage text-orange-600 dark:text-orange-300"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">الرسوم</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-1">
                            {{ number_format($system->payments->sum('fees'), 2) }}
                            <span class="text-sm"> <x-drhm-icon color="737373" width="14" height="14" /></span>
                        </p>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">#</th>
                        <th scope="col" class="px-4 py-3">رقم الدفعة</th>
                        <th scope="col" class="px-4 py-3">المبلغ</th>
                        {{-- <th scope="col" class="px-4 py-3">الرسوم</th> --}}
                        {{-- <th scope="col" class="px-4 py-3">المبلغ الأصلي</th> --}}
                        {{-- <th scope="col" class="px-4 py-3">طريقة الدفع</th> --}}
                        {{-- <th scope="col" class="px-4 py-3">العملة</th> --}}
                        <th scope="col" class="px-4 py-3">الحالة</th>
                        <th scope="col" class="px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($system->payments as $payment)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                {{ $payment->payment_id ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center gap-1">
                            {{ number_format($payment->amount, 2) }}  <x-drhm-icon color="737373" width="12" height="14" />
                        </td>
                        {{-- <td class="px-4 py-3 text-orange-600 dark:text-orange-400"> --}}
                            {{-- <div class="flex items-center gap-1">
                                {{ number_format($payment->fees, 2) }}  <x-drhm-icon color="737373" width="12" height="14" />
                            </div>
                        </td> --}}
                        {{-- <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-1">
                                {{ number_format($payment->original_price, 2) }}  <x-drhm-icon color="737373" width="12" height="14" />
                            </div>
                        </td> --}}
                        {{-- <td class="px-4 py-3">
                            @php
                            $methods = [
                            'stripe' => ['name' => 'Stripe', 'color' => 'blue'],
                            'paypal' => ['name' => 'PayPal', 'color' => 'indigo'],
                            'visa' => ['name' => 'Visa', 'color' => 'purple'],
                            'manual' => ['name' => 'يدوي', 'color' => 'gray'],
                            ];
                            $method = $methods[$payment->payment_method] ?? ['name' => $payment->payment_method, 'color'
                            => 'gray'];
                            @endphp
                            <span
                                class="bg-{{ $method['color'] }}-100 text-{{ $method['color'] }}-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-{{ $method['color'] }}-900 dark:text-{{ $method['color'] }}-300">
                                {{ $method['name'] }}
                            </span>
                        </td> --}}
                        {{-- <td class="px-4 py-3">
                            {{ $payment->currency }}
                        </td> --}}
                        <td class="px-4 py-3">
                            @if($payment->status === 'success')
                            <span
                                class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                <i class="fas fa-check-circle ml-1"></i>
                                مدفوعة
                            </span>
                            @elseif($payment->status === 'pending')
                            <span
                                class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                                <i class="fas fa-clock ml-1"></i>
                                قيد المعالجة
                            </span>
                            @else
                            <span
                                class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                <i class="fas fa-times-circle ml-1"></i>
                                فشلت
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <div class="flex flex-col">
                                <span>{{ $payment->created_at->format('Y-m-d') }}</span>
                                <span class="text-xs text-gray-500">{{ $payment->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p class="text-lg">لا توجد فواتير لهذا النظام</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection