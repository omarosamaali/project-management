@extends('layouts.app')

@section('title', 'طلبات التحويل')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.withdrawal-requests.index') }}" second="طلبات التحويل" />
    
    <div class="mx-auto w-full">
        <!-- إحصائيات الأرباح -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- إجمالي الأرباح -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي الأرباح</p>
                        <p class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ number_format(auth()->user()->total_earnings, 2) }}
                            <x-drhm-icon color="000" />
                        </p>

                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- الرصيد المتاح -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">الرصيد المتاح للسحب</p>
                        <p class="flex items-center text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                            {{ number_format(auth()->user()->available_balance, 2) }}
                            <x-drhm-icon color="2563eb" />
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- المبلغ المسحوب -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">المبلغ المسحوب</p>
                        <p class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ number_format(auth()->user()->withdrawn_balance, 2) }}
                            <x-drhm-icon color="000" />
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- زر طلب سحب جديد -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">طلب سحب أرباح</h3>
                    <!-- علامة الاستفهام مع التلميح -->
                    <div class="relative group">
                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-help"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <!-- التلميح -->
                        <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block w-64 z-10">
                            <div class="bg-gray-900 text-white text-sm rounded-lg py-2 px-3 shadow-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <p>لا يمكن سحب المبلغ إذا كان أقل من 200 <x-drhm-icon color="000" /></p>
                                </div>
                                <div class="absolute top-full right-4 -mt-1">
                                    <div class="border-8 border-transparent border-t-gray-900"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button
                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    طلب سحب جديد
                </button>
            </div>
        </div>

        <!-- جدول طلبات التحويل -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">المبلغ</th>
                            <th scope="col" class="px-4 py-3">تاريخ الطلب</th>
                            <th scope="col" class="px-4 py-3">الحالة</th>
                            <th scope="col" class="px-4 py-3">تاريخ التحويل</th>
                            <th scope="col" class="px-4 py-3">ملاحظات</th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">الإجراءات</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawalRequests as $request)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $request->id }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white flex items-center">
                                {{ number_format($request->amount, 2) }}
                                <x-drhm-icon color="000" />
                            </td>
                            <td class="px-4 py-3">
                                {{ $request->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="{{ $request->status_class }}">
                                    {{ $request->status_name }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $request->completed_at ? $request->completed_at->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $request->notes ?? '-' }}
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end gap-2">
                                @if($request->status === 'pending')
                                <form action="{{ route('dashboard.withdrawal-requests.destroy', $request) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-black hover:text-red-900">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('dashboard.withdrawal-requests.show', $request) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                لا توجد طلبات سحب حتى الآن
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $withdrawalRequests->links() }}
            </div>
        </div>
    </div>
</section>

@endsection