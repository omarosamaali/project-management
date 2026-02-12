@props(['SpecialRequest'])
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-green-600"></i> ميزانية المشروع
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة ميزانية المشروع والدفعات</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <button onclick="openEditBudgetModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all">
            <i class="fas fa-edit"></i> تعديل الميزانية
        </button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
            class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-6 rounded-xl border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">إجمالي الميزانية</p>
                    <p class="text-3xl font-bold text-blue-700 dark:text-blue-300 mt-2 flex items-center gap-1">
                        {{ number_format($SpecialRequest->price, 2) }}
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.2" width="20" height="20"
                            viewBox="0 0 1000 1000">
                            <style>
                                .s0 {
                                    fill: #2563eb
                                }
                            </style>
                            <path class="s0"
                                d="m88.3 1c0.4 0.6 2.6 3.3 4.7 5.9 15.3 18.2 26.8 47.8 33 85.1 4.1 24.5 4.3 32.2 4.3 125.6v87h-41.8c-38.2 0-42.6-0.2-50.1-1.7-11.8-2.5-24-9.2-32.2-17.8-6.5-6.9-6.3-7.3-5.9 13.6 0.5 17.3 0.7 19.2 3.2 28.6 4 14.9 9.5 26 17.8 35.9 11.3 13.6 22.8 21.2 39.2 26.3 3.5 1 10.9 1.4 37.1 1.6l32.7 0.5v43.3 43.4l-46.1-0.3-46.3-0.3-8-3.2c-9.5-3.8-13.8-6.6-23.1-14.9l-6.8-6.1 0.4 19.1c0.5 17.7 0.6 19.7 3.1 28.7 8.7 31.8 29.7 54.5 57.4 61.9 6.9 1.9 9.6 2 38.5 2.4l30.9 0.4v89.6c0 54.1-0.3 94-0.8 100.8-0.5 6.2-2.1 17.8-3.5 25.9-6.5 37.3-18.2 65.4-35 83.6l-3.4 3.7h169.1c101.1 0 176.7-0.4 187.8-0.9 19.5-1 63-5.3 72.8-7.4 3.1-0.6 8.9-1.5 12.7-2.1 8.1-1.2 21.5-4 40.8-8.9 27.2-6.8 52-15.3 76.3-26.1 7.6-3.4 29.4-14.5 35.2-18 3.1-1.8 6.8-4 8.2-4.7 3.9-2.1 10.4-6.3 19.9-13.1 4.7-3.4 9.4-6.7 10.4-7.4 4.2-2.8 18.7-14.9 25.3-21 25.1-23.1 46.1-48.8 62.4-76.3 2.3-4 5.3-9 6.6-11.1 3.3-5.6 16.9-33.6 18.2-37.8 0.6-1.9 1.4-3.9 1.8-4.3 2.6-3.4 17.6-50.6 19.4-60.9 0.6-3.3 0.9-3.8 3.4-4.3 1.6-0.3 24.9-0.3 51.8-0.1 53.8 0.4 53.8 0.4 65.7 5.9 6.7 3.1 8.7 4.5 16.1 11.2 9.7 8.7 8.8 10.1 8.2-11.7-0.4-12.8-0.9-20.7-1.8-23.9-3.4-12.3-4.2-14.9-7.2-21.1-9.8-21.4-26.2-36.7-47.2-44l-8.2-3-33.4-0.4-33.3-0.5 0.4-11.7c0.4-15.4 0.4-45.9-0.1-61.6l-0.4-12.6 44.6-0.2c38.2-0.2 45.3 0 49.5 1.1 12.6 3.5 21.1 8.3 31.5 17.8l5.8 5.4v-14.8c0-17.6-0.9-25.4-4.5-37-7.1-23.5-21.1-41-41.1-51.8-13-7-13.8-7.2-58.5-7.5-26.2-0.2-39.9-0.6-40.6-1.2-0.6-0.6-1.1-1.6-1.1-2.4 0-0.8-1.5-7.1-3.5-13.9-23.4-82.7-67.1-148.4-131-197.1-8.7-6.7-30-20.8-38.6-25.6-3.3-1.9-6.9-3.9-7.8-4.5-4.2-2.3-28.3-14.1-34.3-16.6-3.6-1.6-8.3-3.6-10.4-4.4-35.3-15.3-94.5-29.8-139.7-34.3-7.4-0.7-17.2-1.8-21.7-2.2-20.4-2.3-48.7-2.6-209.4-2.6-135.8 0-169.9 0.3-169.4 1zm330.7 43.3c33.8 2 54.6 4.6 78.9 10.5 74.2 17.6 126.4 54.8 164.3 117 3.5 5.8 18.3 36 20.5 42.1 10.5 28.3 15.6 45.1 20.1 67.3 1.1 5.4 2.6 12.6 3.3 16 0.7 3.3 1 6.4 0.7 6.7-0.5 0.4-100.9 0.6-223.3 0.5l-222.5-0.2-0.3-128.5c-0.1-70.6 0-129.3 0.3-130.4l0.4-1.9h71.1c39 0 78 0.4 86.5 0.9zm297.5 350.3c0.7 4.3 0.7 77.3 0 80.9l-0.6 2.7-227.5-0.2-227.4-0.3-0.2-42.4c-0.2-23.3 0-42.7 0.2-43.1 0.3-0.5 97.2-0.8 227.7-0.8h227.2zm-10.2 171.7c0.5 1.5-1.9 13.8-6.8 33.8-5.6 22.5-13.2 45.2-20.9 62-3.8 8.6-13.3 27.2-15.6 30.7-1.1 1.6-4.3 6.7-7.1 11.2-18 28.2-43.7 53.9-73 72.9-10.7 6.8-32.7 18.4-38.6 20.2-1.2 0.3-2.5 0.9-3 1.3-0.7 0.6-9.8 4-20.4 7.8-19.5 6.9-56.6 14.4-86.4 17.5-19.3 1.9-22.4 2-96.7 2h-76.9v-129.7-129.8l220.9-0.4c121.5-0.2 221.6-0.5 222.4-0.7 0.9-0.1 1.8 0.5 2.1 1.2z" />
                        </svg>
                    </p>
                </div>
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-6 rounded-xl border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">المبلغ المدفوع</p>
                    <p class="flex items-center gap-2 text-3xl font-bold text-green-700 dark:text-green-300 mt-2">
                        {{ number_format($SpecialRequest->total_paid, 2) }} <span class="text-lg">
                            {{-- <x-drhm-icon width="12" height="14" /> --}}
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.2" width="20" height="20" viewBox="0 0 1000 1000">
                                <style>
                                    .s0 {
                                        fill: rgb(0 0 0) !important
                                        }
                                </style>
                                <path class="s0"
                                    d="m88.3 1c0.4 0.6 2.6 3.3 4.7 5.9 15.3 18.2 26.8 47.8 33 85.1 4.1 24.5 4.3 32.2 4.3 125.6v87h-41.8c-38.2 0-42.6-0.2-50.1-1.7-11.8-2.5-24-9.2-32.2-17.8-6.5-6.9-6.3-7.3-5.9 13.6 0.5 17.3 0.7 19.2 3.2 28.6 4 14.9 9.5 26 17.8 35.9 11.3 13.6 22.8 21.2 39.2 26.3 3.5 1 10.9 1.4 37.1 1.6l32.7 0.5v43.3 43.4l-46.1-0.3-46.3-0.3-8-3.2c-9.5-3.8-13.8-6.6-23.1-14.9l-6.8-6.1 0.4 19.1c0.5 17.7 0.6 19.7 3.1 28.7 8.7 31.8 29.7 54.5 57.4 61.9 6.9 1.9 9.6 2 38.5 2.4l30.9 0.4v89.6c0 54.1-0.3 94-0.8 100.8-0.5 6.2-2.1 17.8-3.5 25.9-6.5 37.3-18.2 65.4-35 83.6l-3.4 3.7h169.1c101.1 0 176.7-0.4 187.8-0.9 19.5-1 63-5.3 72.8-7.4 3.1-0.6 8.9-1.5 12.7-2.1 8.1-1.2 21.5-4 40.8-8.9 27.2-6.8 52-15.3 76.3-26.1 7.6-3.4 29.4-14.5 35.2-18 3.1-1.8 6.8-4 8.2-4.7 3.9-2.1 10.4-6.3 19.9-13.1 4.7-3.4 9.4-6.7 10.4-7.4 4.2-2.8 18.7-14.9 25.3-21 25.1-23.1 46.1-48.8 62.4-76.3 2.3-4 5.3-9 6.6-11.1 3.3-5.6 16.9-33.6 18.2-37.8 0.6-1.9 1.4-3.9 1.8-4.3 2.6-3.4 17.6-50.6 19.4-60.9 0.6-3.3 0.9-3.8 3.4-4.3 1.6-0.3 24.9-0.3 51.8-0.1 53.8 0.4 53.8 0.4 65.7 5.9 6.7 3.1 8.7 4.5 16.1 11.2 9.7 8.7 8.8 10.1 8.2-11.7-0.4-12.8-0.9-20.7-1.8-23.9-3.4-12.3-4.2-14.9-7.2-21.1-9.8-21.4-26.2-36.7-47.2-44l-8.2-3-33.4-0.4-33.3-0.5 0.4-11.7c0.4-15.4 0.4-45.9-0.1-61.6l-0.4-12.6 44.6-0.2c38.2-0.2 45.3 0 49.5 1.1 12.6 3.5 21.1 8.3 31.5 17.8l5.8 5.4v-14.8c0-17.6-0.9-25.4-4.5-37-7.1-23.5-21.1-41-41.1-51.8-13-7-13.8-7.2-58.5-7.5-26.2-0.2-39.9-0.6-40.6-1.2-0.6-0.6-1.1-1.6-1.1-2.4 0-0.8-1.5-7.1-3.5-13.9-23.4-82.7-67.1-148.4-131-197.1-8.7-6.7-30-20.8-38.6-25.6-3.3-1.9-6.9-3.9-7.8-4.5-4.2-2.3-28.3-14.1-34.3-16.6-3.6-1.6-8.3-3.6-10.4-4.4-35.3-15.3-94.5-29.8-139.7-34.3-7.4-0.7-17.2-1.8-21.7-2.2-20.4-2.3-48.7-2.6-209.4-2.6-135.8 0-169.9 0.3-169.4 1zm330.7 43.3c33.8 2 54.6 4.6 78.9 10.5 74.2 17.6 126.4 54.8 164.3 117 3.5 5.8 18.3 36 20.5 42.1 10.5 28.3 15.6 45.1 20.1 67.3 1.1 5.4 2.6 12.6 3.3 16 0.7 3.3 1 6.4 0.7 6.7-0.5 0.4-100.9 0.6-223.3 0.5l-222.5-0.2-0.3-128.5c-0.1-70.6 0-129.3 0.3-130.4l0.4-1.9h71.1c39 0 78 0.4 86.5 0.9zm297.5 350.3c0.7 4.3 0.7 77.3 0 80.9l-0.6 2.7-227.5-0.2-227.4-0.3-0.2-42.4c-0.2-23.3 0-42.7 0.2-43.1 0.3-0.5 97.2-0.8 227.7-0.8h227.2zm-10.2 171.7c0.5 1.5-1.9 13.8-6.8 33.8-5.6 22.5-13.2 45.2-20.9 62-3.8 8.6-13.3 27.2-15.6 30.7-1.1 1.6-4.3 6.7-7.1 11.2-18 28.2-43.7 53.9-73 72.9-10.7 6.8-32.7 18.4-38.6 20.2-1.2 0.3-2.5 0.9-3 1.3-0.7 0.6-9.8 4-20.4 7.8-19.5 6.9-56.6 14.4-86.4 17.5-19.3 1.9-22.4 2-96.7 2h-76.9v-129.7-129.8l220.9-0.4c121.5-0.2 221.6-0.5 222.4-0.7 0.9-0.1 1.8 0.5 2.1 1.2z" />
                            </svg>
                        </span>
                    </p>
                </div>
                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-6 rounded-xl border border-orange-200 dark:border-orange-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">المبلغ المتبقي</p>
                    <p class="flex items-center gap-1 text-3xl font-bold text-orange-700 dark:text-orange-300 mt-2">
                        {{ number_format($SpecialRequest->remaining_amount, 2) }} <span class="text-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.2" width="20" height="20" viewBox="0 0 1000 1000">
                                <style>
                                    .s0 {
                                        fill: rgb(194 65 12)
                                    }
                                </style>
                                <path class="s0"
                                    d="m88.3 1c0.4 0.6 2.6 3.3 4.7 5.9 15.3 18.2 26.8 47.8 33 85.1 4.1 24.5 4.3 32.2 4.3 125.6v87h-41.8c-38.2 0-42.6-0.2-50.1-1.7-11.8-2.5-24-9.2-32.2-17.8-6.5-6.9-6.3-7.3-5.9 13.6 0.5 17.3 0.7 19.2 3.2 28.6 4 14.9 9.5 26 17.8 35.9 11.3 13.6 22.8 21.2 39.2 26.3 3.5 1 10.9 1.4 37.1 1.6l32.7 0.5v43.3 43.4l-46.1-0.3-46.3-0.3-8-3.2c-9.5-3.8-13.8-6.6-23.1-14.9l-6.8-6.1 0.4 19.1c0.5 17.7 0.6 19.7 3.1 28.7 8.7 31.8 29.7 54.5 57.4 61.9 6.9 1.9 9.6 2 38.5 2.4l30.9 0.4v89.6c0 54.1-0.3 94-0.8 100.8-0.5 6.2-2.1 17.8-3.5 25.9-6.5 37.3-18.2 65.4-35 83.6l-3.4 3.7h169.1c101.1 0 176.7-0.4 187.8-0.9 19.5-1 63-5.3 72.8-7.4 3.1-0.6 8.9-1.5 12.7-2.1 8.1-1.2 21.5-4 40.8-8.9 27.2-6.8 52-15.3 76.3-26.1 7.6-3.4 29.4-14.5 35.2-18 3.1-1.8 6.8-4 8.2-4.7 3.9-2.1 10.4-6.3 19.9-13.1 4.7-3.4 9.4-6.7 10.4-7.4 4.2-2.8 18.7-14.9 25.3-21 25.1-23.1 46.1-48.8 62.4-76.3 2.3-4 5.3-9 6.6-11.1 3.3-5.6 16.9-33.6 18.2-37.8 0.6-1.9 1.4-3.9 1.8-4.3 2.6-3.4 17.6-50.6 19.4-60.9 0.6-3.3 0.9-3.8 3.4-4.3 1.6-0.3 24.9-0.3 51.8-0.1 53.8 0.4 53.8 0.4 65.7 5.9 6.7 3.1 8.7 4.5 16.1 11.2 9.7 8.7 8.8 10.1 8.2-11.7-0.4-12.8-0.9-20.7-1.8-23.9-3.4-12.3-4.2-14.9-7.2-21.1-9.8-21.4-26.2-36.7-47.2-44l-8.2-3-33.4-0.4-33.3-0.5 0.4-11.7c0.4-15.4 0.4-45.9-0.1-61.6l-0.4-12.6 44.6-0.2c38.2-0.2 45.3 0 49.5 1.1 12.6 3.5 21.1 8.3 31.5 17.8l5.8 5.4v-14.8c0-17.6-0.9-25.4-4.5-37-7.1-23.5-21.1-41-41.1-51.8-13-7-13.8-7.2-58.5-7.5-26.2-0.2-39.9-0.6-40.6-1.2-0.6-0.6-1.1-1.6-1.1-2.4 0-0.8-1.5-7.1-3.5-13.9-23.4-82.7-67.1-148.4-131-197.1-8.7-6.7-30-20.8-38.6-25.6-3.3-1.9-6.9-3.9-7.8-4.5-4.2-2.3-28.3-14.1-34.3-16.6-3.6-1.6-8.3-3.6-10.4-4.4-35.3-15.3-94.5-29.8-139.7-34.3-7.4-0.7-17.2-1.8-21.7-2.2-20.4-2.3-48.7-2.6-209.4-2.6-135.8 0-169.9 0.3-169.4 1zm330.7 43.3c33.8 2 54.6 4.6 78.9 10.5 74.2 17.6 126.4 54.8 164.3 117 3.5 5.8 18.3 36 20.5 42.1 10.5 28.3 15.6 45.1 20.1 67.3 1.1 5.4 2.6 12.6 3.3 16 0.7 3.3 1 6.4 0.7 6.7-0.5 0.4-100.9 0.6-223.3 0.5l-222.5-0.2-0.3-128.5c-0.1-70.6 0-129.3 0.3-130.4l0.4-1.9h71.1c39 0 78 0.4 86.5 0.9zm297.5 350.3c0.7 4.3 0.7 77.3 0 80.9l-0.6 2.7-227.5-0.2-227.4-0.3-0.2-42.4c-0.2-23.3 0-42.7 0.2-43.1 0.3-0.5 97.2-0.8 227.7-0.8h227.2zm-10.2 171.7c0.5 1.5-1.9 13.8-6.8 33.8-5.6 22.5-13.2 45.2-20.9 62-3.8 8.6-13.3 27.2-15.6 30.7-1.1 1.6-4.3 6.7-7.1 11.2-18 28.2-43.7 53.9-73 72.9-10.7 6.8-32.7 18.4-38.6 20.2-1.2 0.3-2.5 0.9-3 1.3-0.7 0.6-9.8 4-20.4 7.8-19.5 6.9-56.6 14.4-86.4 17.5-19.3 1.9-22.4 2-96.7 2h-76.9v-129.7-129.8l220.9-0.4c121.5-0.2 221.6-0.5 222.4-0.7 0.9-0.1 1.8 0.5 2.1 1.2z" />
                            </svg>
                        </span>
                    </p>
                </div>
                <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">نسبة الدفع</span>
            <span class="text-lg font-bold text-blue-600">{{ $SpecialRequest->payment_progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-4 rounded-full transition-all duration-500"
                style="width: {{ $SpecialRequest->payment_progress }}%"></div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list"></i> الدفعات ({{ $SpecialRequest->installments()->count() }})
            </h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($SpecialRequest->installments()->get() as $payment)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $payment->payment_name }}</h4>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold @if($payment->status == 'paid') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 @endif">
                                @if($payment->status == 'paid') مدفوعة @elseif($payment->status == 'pending') قيد
                                المراجعة @else غير مدفوعة @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-money-bill-wave"></i>
                                <strong class="flex items-center gap-1">{{ number_format($payment->amount, 2) }}
                                    <x-drhm-icon width="12" height="14" />
                                </strong>
                            </span>
                            @if($payment->due_date)
                            <span class="flex items-center gap-1">
                                <i class="far fa-calendar-alt"></i> الاستحقاق: {{
                                \Carbon\Carbon::parse($payment->due_date)->format('Y/m/d') }}
                            </span>
                            @endif
                            @if($payment->paid_at)
                            <span class="flex items-center gap-1 text-green-600">
                                <i class="fas fa-check-circle"></i> دُفعت في: {{
                                \Carbon\Carbon::parse($payment->paid_at)->format('Y/m/d') }}
                            </span>
                            @endif
                        </div>
                        @if($payment->notes)
                        <div
                            class="mt-2 p-2 bg-gray-50 dark:bg-gray-900 rounded text-xs text-gray-600 dark:text-gray-400">
                            <i class="fas fa-sticky-note ml-1"></i> {{ $payment->notes }}
                        </div>
                        @endif
                    </div>
                    @if(auth()->user()->role === 'client' && $payment->status !== 'paid')
                    <div class="flex flex-col items-end">
                        @php
                        $baseAmount = $payment->amount;
                        $fees = round(($baseAmount * 0.079) + 2, 2);
                        $totalWithFees = $baseAmount + $fees;
                        @endphp
                        <div class="text-right mb-3">
                            <p class="flex items-center gap-1 text-lg font-bold text-gray-800 dark:text-white">المبلغ الأساسي: {{
                                number_format($baseAmount, 2) }}
                                <x-drhm-icon width="12" height="14" />
                            </p>
                            <p class="flex items-center gap-1 text-sm text-orange-600 font-medium">+ رسوم بوابة الدفع: {{ number_format($fees,
                                2) }}
                                <x-drhm-icon color="rgb(234 88 12)" width="12" height="14" />
                            </p>
                            <p class="flex items-center gap-1 text-xl font-bold text-emerald-600 mt-1">الإجمالي: {{
                                number_format($totalWithFees, 2) }}
                                <x-drhm-icon color="rgb(5 150 105)" width="12" height="14" />
                            </p>
                        </div>
                        <form id="ziina-form-{{ $payment->id }}"
                            action="{{ route('ziina.installment.pay', $payment->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="button" onclick="payWithZiina({{ $payment->id }}, {{ $totalWithFees }})"
                                class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-3 rounded-lg font-bold flex items-center gap-2 shadow-lg transition-all transform hover:scale-105">
                                <i class="fas fa-credit-card"></i> دفع {{ number_format($totalWithFees, 2) }}
                                <x-drhm-icon color="#fffff" width="12" height="14" />
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="flex flex-col items-end">
                        @if($payment->status == 'paid')
                        @php
                        // البحث عن آخر دفعة مكتملة للطلب الخاص
                        $paidPayment = \App\Models\Payment::where('special_request_id', $SpecialRequest->id)
                        ->where('status', 'completed')
                        ->latest()
                        ->first();
                        @endphp
                        @if($paidPayment)
                        <a href="{{ route('special-request.payment.invoice', ['specialRequest' => $SpecialRequest->id, 'payment' => $paidPayment->id]) }}"
                            target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">
                            <i class="fas fa-file-invoice ml-1"></i> معاينة الفاتورة
                        </a>
                        @else
                        <span class="text-gray-500 dark:text-gray-400 text-sm">لا توجد فاتورة متاحة</span>
                        @endif
                        @else
                        <span class="text-gray-400 dark:text-gray-500 text-sm italic">في انتظار الدفع</span>
                        @endif
                    </div>
                    @endif
                </div>
                <script>
                    function payWithZiina(paymentId, totalAmount) {
                        const button = document.querySelector(`#ziina-form-${paymentId} button`);
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحويل إلى Ziina...';
                        fetch(`/payments/${paymentId}/ziina-pay`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.payment_url) {
                                window.location.href = data.payment_url;
                            } else {
                                alert('حدث خطأ: ' + (data.message || 'فشل في إنشاء الدفع'));
                                button.disabled = false;
                                button.innerHTML = `<i class="fas fa-credit-card"></i> دفع ${totalAmount.toFixed(2)} <x-drhm-icon width="12" height="14" /> الآن عبر Ziina`;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('فشل في الاتصال، حاول مرة أخرى');
                            button.disabled = false;
                            button.innerHTML = `<i class="fas fa-credit-card"></i> دفع ${totalAmount.toFixed(2)} <x-drhm-icon width="12" height="14" /> الآن عبر Ziina`;
                        });
                    }
                </script>
            </div>
            @empty
            <div class="p-10 text-center">
                <i class="fas fa-receipt text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-400 dark:text-gray-500">لا توجد دفعات محددة</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<div id="editBudgetModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div
        class="bg-white dark:bg-gray-800 w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700 sticky top-0">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> تعديل ميزانية المشروع
            </h3>
            <button onclick="closeEditBudgetModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form action="{{ route('requests.update-budget', $SpecialRequest->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-wallet ml-1"></i>
                    ميزانية المشروع</label>
                <input type="number" name="price" id="budget_price" value="{{ $SpecialRequest->price }}" step="0.01"
                    min="0" required
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    oninput="calculateTotal()">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-credit-card ml-1"></i>
                    نوع الدفع</label>
                <select name="payment_type" id="payment_type"
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    onchange="toggleInstallments()">
                    <option value="single" {{ $SpecialRequest->payment_type == 'single' ? 'selected' : '' }}>دفعة واحدة
                    </option>
                    <option value="installments" {{ $SpecialRequest->payment_type == 'installments' ? 'selected' : ''
                        }}>دفعات (تقسيط)</option>
                </select>
            </div>
            <div id="installments_section"
                class="{{ $SpecialRequest->payment_type == 'installments' ? '' : 'hidden' }} space-y-3">
                <div class="flex justify-between items-center">
                    <label class="block text-sm font-medium dark:text-gray-300"><i class="fas fa-list ml-1"></i> تقسيم
                        الدفعات</label>
                    <button type="button" onclick="addInstallment()"
                        class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"><i
                            class="fas fa-plus ml-1"></i> إضافة دفعة</button>
                </div>
                <div id="installments_wrapper" class="space-y-2">
                    @php $installments = $SpecialRequest->installments()->get() ?? collect(); @endphp
                    @foreach($installments as $index => $payment)
                    <div class="installment-row flex gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <input type="text" name="installments[{{ $index }}][name]" value="{{ $payment->payment_name }}"
                            placeholder="اسم الدفعة" required
                            class="flex-1 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
                        <input type="number" name="installments[{{ $index }}][amount]" value="{{ $payment->amount }}"
                            placeholder="المبلغ" step="0.01" min="0" required
                            class="w-32 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm installment-amount"
                            oninput="calculateTotal()">
                        <input type="date" name="installments[{{ $index }}][due_date]"
                            value="{{ $payment->due_date ? \Carbon\Carbon::parse($payment->due_date)->format('Y-m-d') : '' }}"
                            class="w-40 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
                        <button type="button" onclick="removeInstallment(this)"
                            class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex justify-between text-sm"><span>مجموع الدفعات:</span><span id="total_installments"
                            class="font-bold">0.00</span></div>
                    <div class="flex justify-between text-sm mt-1"><span>الميزانية:</span><span id="budget_display"
                            class="font-bold">0.00</span></div>
                    <div id="difference_msg" class="mt-2 text-xs hidden"></div>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700"><i
                        class="fas fa-save ml-1"></i> حفظ التعديلات</button>
                <button type="button" onclick="closeEditBudgetModal()"
                    class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endif

@if(auth()->user()->role === 'client')
<div id="paymentProofModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-upload text-green-600"></i> رفع إثبات الدفع
            </h3>
        </div>
        <form id="paymentProofForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-file-upload ml-1"></i>
                    إثبات الدفع (صورة أو PDF)</label>
                <input type="file" name="payment_proof" required accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="text-xs text-gray-500 mt-1">الحد الأقصى: 5 ميجابايت</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-sticky-note ml-1"></i>
                    ملاحظات (اختياري)</label>
                <textarea name="payment_notes" rows="3"
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                    placeholder="أي ملاحظات إضافية..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-green-600 text-white py-2.5 rounded-lg font-bold hover:bg-green-700"><i
                        class="fas fa-paper-plane ml-1"></i> إرسال</button>
                <button type="button" onclick="closePaymentProofModal()"
                    class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-2.5 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endif

@if(auth()->user()->role === 'admin')
<div id="rejectModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 bg-gradient-to-r from-red-50 to-orange-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-times-circle text-black"></i> رفض الدفعة
            </h3>
        </div>
        <form id="rejectForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">سبب الرفض</label>
                <textarea name="rejection_notes" rows="3" required
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                    placeholder="اكتب سبب رفض إثبات الدفع..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-black text-white py-2.5 rounded-lg font-bold hover:bg-red-700">تأكيد الرفض</button>
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-2.5 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    if (typeof installmentCounter === 'undefined') {
        window.installmentCounter = {{ $SpecialRequest->installments->count() }};
    }

    function calculateTotal() {
        const budgetInput = document.getElementById('budget_price');
        if(!budgetInput) return;

        const budget = parseFloat(budgetInput.value) || 0;
        let total = 0;

        document.querySelectorAll('.installment-amount').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        document.getElementById('total_installments').textContent = total.toFixed(2);
        document.getElementById('budget_display').textContent = budget.toFixed(2);

        const msg = document.getElementById('difference_msg');
        const diff = budget - total;

        if (Math.abs(diff) > 0.01) {
            msg.classList.remove('hidden');
            msg.innerHTML = `تنبيه: هناك فرق ${diff.toFixed(2)} بين الميزانية والدفعات`;
        } else {
            msg.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });

    window.openEditBudgetModal = function() {
        const modal = document.getElementById('editBudgetModal');
        if (modal) {
            modal.classList.remove('hidden');
            if (typeof calculateTotal === 'function') calculateTotal();
        }
    };

    window.closeEditBudgetModal = function() {
        const modal = document.getElementById('editBudgetModal');
        if (modal) modal.classList.add('hidden');
    };

    window.toggleInstallments = function() {
        const paymentType = document.getElementById('payment_type').value;
        const installmentsSection = document.getElementById('installments_section');
        if (installmentsSection) {
            installmentsSection.classList.toggle('hidden', paymentType !== 'installments');
        }
    };

    window.addInstallment = function() {
        const wrapper = document.getElementById('installments_wrapper');
        const index = window.installmentCounter++;
        const newRow = `
            <div class="installment-row flex gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <input type="text" name="installments[${index}][name]" placeholder="اسم الدفعة" required
                    class="flex-1 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
                <input type="number" name="installments[${index}][amount]" placeholder="المبلغ" step="0.01" min="0" required
                    class="w-32 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm installment-amount" oninput="calculateTotal()">
                <input type="date" name="installments[${index}][due_date]"
                    class="w-40 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
                <button type="button" onclick="removeInstallment(this)"
                    class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', newRow);
    };

    window.removeInstallment = function(button) {
        button.closest('.installment-row').remove();
        calculateTotal();
    };
</script>