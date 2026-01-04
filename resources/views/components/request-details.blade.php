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
    <div class="border-b pb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-clock text-blue-600"></i>
            الساعات
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">عدد
                    الساعات
                    المتوقعة</label>
                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                    <!-- لو عندك حقل expected_hours --> 160 ساعة
                    <!-- أو غير محدد -->
                </span>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">عدد
                    الساعات المستغرقة
                    حتى الآن</label>
                <span class="text-lg font-bold text-green-600 dark:text-green-400">
                    <!-- لو عندك حقل spent_hours أو حساب من المهام --> 85 ساعة
                </span>
            </div>
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