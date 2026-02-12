@props(['SpecialRequest'])
<div class="p-6 space-y-8">
    {{-- المدة والتواريخ --}}
    <div class="border-b pb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-alt text-blue-600"></i>
            تفاصيل المشروع
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                    بدء
                    المشروع</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $SpecialRequest->created_at->format('Y-m-d') }}
                </span>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الموعد
                    النهائي
                    المتوقع</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $SpecialRequest->deadline ?? 'غير محدد' }}
                </span>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                    الطلب</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $SpecialRequest->created_at->format('Y-m-d H:i') }}
                </span>
            </div>
        </div>
    </div>

    {{-- الساعات --}}
    <div class="grid md:grid-cols-2 gap-6">
        {{-- الساعات المتوقعة --}}
        <div
            class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-5 rounded-lg border-2 border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                    عدد الساعات المتوقعة
                </label>
            </div>
            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ number_format($SpecialRequest->expected_hours) }} ساعة
            </span>
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-info-circle"></i>
                (من {{ $SpecialRequest->created_at->format('Y-m-d') }} إلى {{ $SpecialRequest->deadline ?
                $SpecialRequest->deadline->format('Y-m-d') : '---' }})
            </p>
            @if($SpecialRequest->deadline)
            <p class="text-xs text-gray-500 mt-1">
                = {{
                number_format(\Carbon\Carbon::parse($SpecialRequest->created_at)->diffInDays(\Carbon\Carbon::parse($SpecialRequest->deadline)),
                2) }} يوم × 24 ساعة
            </p>
            @endif
        </div>

        {{-- الساعات المستغرقة --}}
        <div
            class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-5 rounded-lg border-2 border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">
                    <i class="fas fa-stopwatch text-green-600"></i>
                    عدد الساعات المستغرقة حتى الآن
                </label>
            </div>
            <span class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($SpecialRequest->spent_hours) }} ساعة
            </span>
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-info-circle"></i>
                @if(in_array($SpecialRequest->status, ['completed', 'canceled']))
                (من {{ $SpecialRequest->created_at->format('Y-m-d') }} إلى {{
                $SpecialRequest->updated_at->format('Y-m-d') }})
                @else
                (من {{ $SpecialRequest->created_at->format('Y-m-d') }} حتى الآن)
                @endif
            </p>

            {{-- Progress Bar --}}
            @if($SpecialRequest->expected_hours > 0)
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-600 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-500 {{ $SpecialRequest->progress_percentage >= 100 ? 'bg-red-600' : 'bg-green-600' }}"
                        style="width: {{ min($SpecialRequest->progress_percentage, 100) }}%"></div>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span
                        class="text-sm font-semibold {{ $SpecialRequest->progress_percentage >= 100 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $SpecialRequest->progress_percentage }}% مكتمل
                    </span>
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                        متبقي: {{ number_format($SpecialRequest->remaining_hours) }} ساعة
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
    {{-- الإحصائيات العامة --}}
    <div class="border-b pb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar text-blue-600"></i>
            إحصائيات المشروع
        </h2>

        <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">عدد الملفات</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">عدد المهام</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">عدد مراحل المشروع</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">عدد الملاحظات</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">عدد الأنشطة</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">المهام المنجزة</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">0</p>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">قيد الإنجاز</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">0</p>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">المهام المتأخرة</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">0</p>
            </div>
        </div>
    </div>
    {{-- معلومات العميل (مخفي عند الشريك) --}}
    @if (auth()->user()->role !== 'partner')
    <div class="border-b pb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-user-tie text-blue-600"></i>
            معلومات العميل
        </h2>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">اسم
                العميل</label>
            <span class="text-lg font-bold text-gray-900 dark:text-white">
                {{ $SpecialRequest->user->name ?? $SpecialRequest->client->name }}
            </span>
        </div>
    </div>
    @endif
</div>