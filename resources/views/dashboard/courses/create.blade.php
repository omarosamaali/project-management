@extends('layouts.app')

@section('title', 'إضافة دورة')

@section('content')
<style>
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    /* Tab Styles */
    .tab-button {
        position: relative;
        padding: 1rem 1.5rem;
        font-weight: 500;
        color: #6B7280;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        white-space: nowrap;
    }

    .tab-button:hover {
        color: #3B82F6;
        background-color: #EFF6FF;
    }

    .tab-button.active {
        color: #3B82F6;
        border-bottom-color: #3B82F6;
        background-color: #EFF6FF;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
</style>

<section class="!px-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.courses.index') }}" second="الدورات" third="إضافة دورة" />

    <div class="mx-auto max-w-5xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-xl overflow-hidden">

            <!-- Error Messages -->
            @if($errors->any())
            <div class="p-4 m-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-medium">يوجد بعض الأخطاء:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('dashboard.courses.store') }}" method="POST" enctype="multipart/form-data"
                id="courseForm">
                @csrf

                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 bg-gray-50 overflow-x-auto">
                    <nav class="flex gap-2 px-4" id="tabs-nav">
                        <button type="button" class="tab-button active" data-tab="basic-info">
                            <i class="fas fa-info-circle ml-2"></i>
                            المعلومات الأساسية
                        </button>
                        <button type="button" class="tab-button" data-tab="content">
                            <i class="fas fa-align-right ml-2"></i>
                            المحتوى والوصف
                        </button>
                        <button type="button" class="tab-button" data-tab="features">
                            <i class="fas fa-star ml-2"></i>
                            المميزات والمتطلبات
                        </button>
                        <button type="button" class="tab-button" data-tab="actions">
                            <i class="fas fa-link ml-2"></i>
                            الأزرار والصور
                        </button>
                        <button type="button" class="tab-button" data-tab="settings">
                            <i class="fas fa-cog ml-2"></i>
                            الإعدادات النهائية
                        </button>
                    </nav>
                </div>

                <!-- Tab Contents -->
                <div class="p-6">

                    <!-- Tab 1: Basic Info -->
                    <div class="tab-content active" id="basic-info">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            المعلومات الأساسية
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Course Name AR -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    اسم الدورة (بالعربي) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="name_ar" name="name_ar" required value="{{ old('name_ar') }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="مثال: دورة البرمجة المتقدمة">
                                @error('name_ar')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Course Name EN -->
                            <div>
                                <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                    Course Name (English) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="name_en" name="name_en" required dir="ltr"
                                    value="{{ old('name_en') }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Ex: Advanced Programming Course">
                                @error('name_en')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    السعر الكلي <span class="text-red-600">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="price" required min="0" step="0.01"
                                        value="{{ old('price') }}"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-20"
                                        placeholder="999.00">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">
                                        <x-drhm-icon width="12" height="14" />
                                    </span>
                                </div>
                                @error('price')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Service Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    نوع الخدمة
                                </label>
                                <select id="service_id" name="service_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- اختر نوع الخدمة --</option>
                                    @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id')==$service->id ? 'selected' :
                                        '' }}>
                                        {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    الحد الأقصى لعدد المشتركين في الدورة <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="counter" required min="0" step="1"
                                    value="{{ old('counter', 0) }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0">
                                @error('counter')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    كم شخص يمكن تسجيله في هذه الدورة كحد أقصى؟
                                </p>
                            </div>

                        </div>

                        <!-- Dates Section -->
<!-- Dates Section -->
<div class="mt-8 pt-6 border-t">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-calendar-alt text-blue-600"></i>
        التواريخ وأيام الدورة
    </h3>

    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <!-- تاريخ البداية -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                تاريخ ووقت البداية <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" id="start_date" name="start_date"
                value="{{ old('start_date', isset($course) ? $course->start_date?->format('Y-m-d\TH:i') : '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror"
                required>
            @error('start_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- تاريخ النهاية -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                تاريخ ووقت النهاية <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" id="end_date" name="end_date"
                value="{{ old('end_date', isset($course) ? $course->end_date?->format('Y-m-d\TH:i') : '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror"
                required>
            @error('end_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- آخر موعد للتسجيل -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                آخر موعد للتسجيل <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" id="last_date" name="last_date"
                value="{{ old('last_date', isset($course) ? $course->last_date?->format('Y-m-d\TH:i') : '') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('last_date') border-red-500 @enderror"
                required>
            @error('last_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- أيام الراحة -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            أيام الراحة (اختياري)
        </label>
        <p class="text-xs text-gray-500 mb-3" id="rest-days-hint">
            اختر تاريخ البداية والنهاية أولاً لعرض الأيام المتاحة
        </p>

        <div id="rest-days-container" class="flex flex-wrap gap-2 p-4 bg-gray-50 rounded-lg border border-gray-200"
            style="display: none !important;">
            <!-- سيتم إنشاء الـ checkboxes ديناميكياً بواسطة JavaScript -->
        </div>
    </div>

    <!-- عرض الحسابات -->
    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                إجمالي الأيام بين التاريخين
            </label>
            <input type="text" id="total_days_display" readonly value="0"
                class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                عدد أيام الراحة
            </label>
            <input type="text" id="rest_days_count_display" readonly value="0"
                class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                عدد أيام الدورة الفعلية <span class="text-red-600">*</span>
            </label>
            <input type="number" id="count_days" name="count_days"
                value="{{ old('count_days', isset($course) ? $course->count_days : 0) }}" readonly
                class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-lg text-blue-700 font-semibold @error('count_days') border-red-500 @enderror"
                required>
            @error('count_days')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<!-- JavaScript لحساب الأيام تلقائياً -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const restDaysContainer = document.getElementById('rest-days-container');
    const restDaysHint = document.getElementById('rest-days-hint');
    const totalDaysDisplay = document.getElementById('total_days_display');
    const restDaysCountDisplay = document.getElementById('rest_days_count_display');
    const countDaysInput = document.getElementById('count_days');

    // أسماء الأيام بالعربية والإنجليزية
    const daysMap = {
        'sunday': 'الأحد',
        'monday': 'الإثنين',
        'tuesday': 'الثلاثاء',
        'wednesday': 'الأربعاء',
        'thursday': 'الخميس',
        'friday': 'الجمعة',
        'saturday': 'السبت'
    };

    // الأيام المحددة مسبقاً (للتحرير)
    const preSelectedDays = @json(old('rest_days', isset($course) ? $course->rest_days : []));

    // دالة للحصول على اسم اليوم بالإنجليزية من تاريخ
    function getDayName(date) {
        const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        return days[date.getDay()];
    }

    // دالة لإنشاء checkboxes بناءً على الأيام الموجودة
    function generateRestDaysCheckboxes(startDate, endDate, recalculate = true) {
        // الحصول على الأيام الفريدة بين التاريخين
        const uniqueDays = new Set();
        let currentDate = new Date(startDate);
        const end = new Date(endDate);

        while (currentDate <= end) {
            const dayName = getDayName(currentDate);
            uniqueDays.add(dayName);
            currentDate.setDate(currentDate.getDate() + 1);
        }

        // تحويل Set إلى Array مرتب حسب ترتيب أيام الأسبوع
        const daysOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        const sortedDays = daysOrder.filter(day => uniqueDays.has(day));

        // حفظ الاختيارات الحالية قبل المسح
        const currentSelections = [];
        const currentCheckboxes = restDaysContainer.querySelectorAll('.rest-day-checkbox:checked');
        currentCheckboxes.forEach(cb => currentSelections.push(cb.value));

        // مسح المحتوى القديم
        restDaysContainer.innerHTML = '';

        // إنشاء checkbox لكل يوم
        sortedDays.forEach(dayValue => {
            const isChecked = currentSelections.includes(dayValue) || preSelectedDays.includes(dayValue);
            
            const checkboxHtml = `
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition ${isChecked ? 'border-blue-500 bg-blue-50' : ''}">
                    <input class="rest-day-checkbox w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" 
                           type="checkbox" 
                           name="rest_days[]" 
                           value="${dayValue}" 
                           id="rest_day_${dayValue}"
                           ${isChecked ? 'checked' : ''}>
                    <span class="text-sm font-medium text-gray-700 ${isChecked ? 'text-blue-700' : ''}">
                        ${daysMap[dayValue]}
                    </span>
                </label>
            `;
            
            restDaysContainer.insertAdjacentHTML('beforeend', checkboxHtml);
        });

        // إضافة event listeners للـ checkboxes الجديدة
        const newCheckboxes = restDaysContainer.querySelectorAll('.rest-day-checkbox');
        newCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // تحديث ستايل الـ label
                const label = this.closest('label');
                if (this.checked) {
                    label.classList.add('border-blue-500', 'bg-blue-50');
                    label.querySelector('span').classList.add('text-blue-700');
                } else {
                    label.classList.remove('border-blue-500', 'bg-blue-50');
                    label.querySelector('span').classList.remove('text-blue-700');
                }
                recalculateRestDays();
            });
        });

        // إظهار الـ container
        restDaysContainer.style.display = 'flex';
        restDaysHint.textContent = `حدد أيام الأسبوع التي لن تكون فيها دورة (سيتم خصمها من إجمالي الأيام)`;
        
        if (recalculate) {
            recalculateRestDays();
        }
    }

    // دالة منفصلة لإعادة حساب أيام الراحة فقط
    function recalculateRestDays() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!startDate || !endDate) {
            return;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);

        // حساب إجمالي الأيام
        const diffTime = Math.abs(end - start);
        const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        // الحصول على أيام الراحة المحددة
        const restDayCheckboxes = restDaysContainer.querySelectorAll('.rest-day-checkbox');
        const selectedRestDays = Array.from(restDayCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        // حساب عدد أيام الراحة ضمن الفترة
        let restDaysCount = 0;
        let currentDate = new Date(start);

        while (currentDate <= end) {
            const dayName = getDayName(currentDate);
            if (selectedRestDays.includes(dayName)) {
                restDaysCount++;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }

        // حساب أيام الدورة الفعلية
        const actualCourseDays = totalDays - restDaysCount;

        // تحديث الحقول
        totalDaysDisplay.value = totalDays;
        restDaysCountDisplay.value = restDaysCount;
        countDaysInput.value = actualCourseDays;
    }

    // دالة لحساب عدد الأيام
    function calculateDays() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!startDate || !endDate) {
            totalDaysDisplay.value = '0';
            restDaysCountDisplay.value = '0';
            countDaysInput.value = '0';
            restDaysContainer.style.display = 'none';
            restDaysHint.textContent = 'اختر تاريخ البداية والنهاية أولاً لعرض الأيام المتاحة';
            return;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);

        if (end < start) {
            alert('تاريخ النهاية يجب أن يكون بعد تاريخ البداية');
            totalDaysDisplay.value = '0';
            restDaysCountDisplay.value = '0';
            countDaysInput.value = '0';
            restDaysContainer.style.display = 'none';
            return;
        }

        // إنشاء checkboxes بناءً على الأيام الموجودة
        generateRestDaysCheckboxes(start, end, true);
    }

    // إضافة مستمعات الأحداث
    startDateInput.addEventListener('change', calculateDays);
    endDateInput.addEventListener('change', calculateDays);

    // حساب عند تحميل الصفحة (للتحرير)
    if (startDateInput.value && endDateInput.value) {
        calculateDays();
    }
});
</script>

<style>
    #rest-days-container {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    #rest-days-container .form-check {
        background: white;
        padding: 10px 15px;
        border-radius: 6px;
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    #rest-days-container .form-check:hover {
        border-color: #0d6efd;
    }

    #rest-days-container .form-check-input:checked~.form-check-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .bg-light {
        background-color: #e9ecef !important;
    }
</style>

                        <!-- Location Section -->
                        <div class="mt-8 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                                مكان الحضور
                            </h3>

                            <!-- Location Type -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    نوع المكان <span class="text-red-600">*</span>
                                </label>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                        <input type="radio" name="location_type" value="online" {{
                                            old('location_type')=='online' ? 'checked' : '' }}
                                            class="w-5 h-5 text-blue-600">
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                <i class="fas fa-wifi text-blue-600 ml-2"></i>
                                                أونلاين
                                            </div>
                                            <div class="text-xs text-gray-500">عبر الإنترنت</div>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-green-50 transition">
                                        <input type="radio" name="location_type" value="on_site" {{
                                            old('location_type')=='on_site' ? 'checked' : '' }}
                                            class="w-5 h-5 text-green-600">
                                        <div>
                                            <div class="font-medium text-gray-800">
                                                <i class="fas fa-building text-green-600 ml-2"></i>
                                                حضوري
                                            </div>
                                            <div class="text-xs text-gray-500">في موقع محدد</div>
                                        </div>
                                    </label>
                                </div>
                                @error('location_type')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Online Link -->
                            <div id="online_link_container" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رابط الدورة الأونلاين <span class="text-red-600">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-link absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="url" name="online_link" id="online_link" dir="ltr"
                                        value="{{ old('online_link') }}"
                                        class="placeholder-gray-400 w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="https://zoom.us/j/123456789">
                                </div>
                                @error('online_link')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Venue Details -->
                            <div id="venue_container" class="hidden space-y-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <!-- Venue Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            اسم المكان <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" name="venue_name" id="venue_name"
                                            value="{{ old('venue_name') }}"
                                            class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="قاعة المؤتمرات - فندق الريتز">
                                        @error('venue_name')
                                        <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Map URL -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            رابط الخريطة
                                        </label>
                                        <div class="relative">
                                            <i
                                                class="fas fa-map-marked-alt absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            <input type="url" name="venue_map_url" id="venue_map_url" dir="ltr"
                                                value="{{ old('venue_map_url') }}"
                                                class="placeholder-gray-400 w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="https://maps.google.com/...">
                                        </div>
                                        @error('venue_map_url')
                                        <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Venue Details -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        تفاصيل المكان
                                    </label>
                                    <textarea name="venue_details" id="venue_details" rows="3"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="تفاصيل إضافية عن المكان...">{{ old('venue_details') }}</textarea>
                                    @error('venue_details')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-8">
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 2: Content & Description -->
                    <div class="tab-content" id="content">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-align-right text-blue-600"></i>
                            المحتوى والوصف
                        </h2>

                        <div class="space-y-6">
                            <!-- Description AR -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    الوصف بالعربي <span class="text-red-600">*</span>
                                </label>
                                <textarea name="description_ar" id="description_ar" required rows="6"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="اكتب وصفاً تفصيلياً للدورة بالعربي...">{{ old('description_ar') }}</textarea>
                                @error('description_ar')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Description EN -->
                            <div>
                                <label class="block text-sm text-left font-medium text-gray-700 mb-2">
                                    Description (English) <span class="text-red-600">*</span>
                                </label>
                                <textarea required name="description_en" id="description_en" rows="6" dir="ltr"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Write detailed course description in English...">{{ old('description_en') }}</textarea>
                                @error('description_en')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 3: Features & Requirements -->
                    <div class="tab-content" id="features">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-star text-blue-600"></i>
                            المميزات والمتطلبات
                        </h2>

                        <!-- Requirements -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-list-check text-green-600"></i>
                                المتطلبات <span class="text-red-600 text-sm">*</span>
                            </h3>

                            <div id="requirements-container" class="space-y-3 mb-4">
                                @if(old('requirements_ar'))
                                @foreach(old('requirements_ar') as $index => $req_ar)
                                <div class="flex gap-2 requirement-row">
                                    <input type="text" name="requirements_ar[]" value="{{ $req_ar }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="متطلب بالعربي">
                                    <input type="text" name="requirements_en[]" dir="ltr"
                                        value="{{ old('requirements_en')[$index] ?? '' }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Requirement in English">
                                    <button type="button"
                                        class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                                @else
                                <div class="flex gap-2 requirement-row">
                                    <input type="text" name="requirements_ar[]"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="متطلب بالعربي">
                                    <input type="text" name="requirements_en[]" dir="ltr"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Requirement in English">
                                    <button type="button"
                                        class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <button type="button"
                                class="add-requirement-btn flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                                إضافة متطلب جديد
                            </button>
                        </div>

                        <!-- Features -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-star text-yellow-500"></i>
                                المميزات <span class="text-red-600 text-sm">*</span>
                            </h3>

                            <div id="features-container" class="space-y-3 mb-4">
                                @if(old('features_ar'))
                                @foreach(old('features_ar') as $index => $feat_ar)
                                <div class="flex gap-2 feature-row">
                                    <input type="text" name="features_ar[]" value="{{ $feat_ar }}"
                                        class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="ميزة بالعربي">
                                    <input type="text" name="features_en[]" dir="ltr"
                                        value="{{ old('features_en')[$index] ?? '' }}"
                                        class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Feature in English">
                                    <button type="button"
                                        class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                                @else
                                <div class="flex gap-2 feature-row">
                                    <input type="text" name="features_ar[]"
                                        class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="ميزة بالعربي">
                                    <input type="text" name="features_en[]" dir="ltr"
                                        class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Feature in English">
                                    <button type="button"
                                        class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <button type="button"
                                class="add-feature-btn flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                                إضافة ميزة جديدة
                            </button>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 4: Buttons & Images -->
                    <div class="tab-content" id="actions">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-link text-blue-600"></i>
                            الأزرار والصور
                        </h2>

                        <!-- Buttons -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                أزرار الإجراءات
                            </h3>

                            <div id="buttons-container" class="space-y-4 mb-4">
                                @if(old('buttons_text_ar'))
                                @foreach(old('buttons_text_ar') as $index => $btn_text)
                                <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر
                                                (عربي)</label>
                                            <input type="text" name="buttons_text_ar[]" value="{{ $btn_text }}"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="اطلب الآن">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">Button
                                                Text (English)</label>
                                            <input type="text" name="buttons_text_en[]" dir="ltr"
                                                value="{{ old('buttons_text_en')[$index] ?? '' }}"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Order Now">
                                        </div>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط
                                                الزر</label>
                                            <input type="url" name="buttons_link[]" dir="ltr"
                                                value="{{ old('buttons_link')[$index] ?? '' }}"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="https://example.com">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="buttons_color[]"
                                                    value="{{ old('buttons_color')[$index] ?? '#3B82F6' }}"
                                                    class="w-16 h-10 border border-gray-300 rounded cursor-pointer button-color-picker">
                                                <input type="text" name="buttons_color_hex[]"
                                                    value="{{ old('buttons_color')[$index] ?? '#3B82F6' }}" dir="ltr"
                                                    class="button-color-hex flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    placeholder="#3B82F6" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-3">
                                        <button type="button"
                                            class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2 transition">
                                            <i class="fas fa-trash"></i>
                                            حذف الزر
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر
                                                (عربي)</label>
                                            <input type="text" name="buttons_text_ar[]"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="اطلب الآن">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-left font-medium text-gray-700 mb-2">Button
                                                Text (English)</label>
                                            <input type="text" name="buttons_text_en[]" dir="ltr"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Order Now">
                                        </div>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط
                                                الزر</label>
                                            <input type="url" name="buttons_link[]" dir="ltr"
                                                class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="https://example.com">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="buttons_color[]" value="#3B82F6"
                                                    class="w-16 h-10 border border-gray-300 rounded cursor-pointer button-color-picker">
                                                <input type="text" name="buttons_color_hex[]" value="#3B82F6" dir="ltr"
                                                    class="button-color-hex flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    placeholder="#3B82F6" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-3">
                                        <button type="button"
                                            class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2 transition">
                                            <i class="fas fa-trash"></i>
                                            حذف الزر
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <button type="button"
                                class="add-button-btn flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                                إضافة زر جديد
                            </button>
                        </div>

                        <!-- Images -->
                        <div class="mt-8 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-image text-purple-600"></i>
                                الصور
                            </h3>

                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Main Image -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        الصورة الرئيسية <span class="text-red-600">*</span>
                                    </label>
                                    <div
                                        class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer">
                                        <input id="main_image_input" type="file" name="main_image" accept="image/*"
                                            required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">اضغط أو اسحب الصورة هنا</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (Max 2MB)</p>
                                    </div>
                                    @error('main_image')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                    <div id="main_preview_container" class="mt-3 hidden relative w-full h-56">
                                        <img id="main_image_preview"
                                            class="w-full h-full object-cover rounded-lg border" />
                                        <button type="button" onclick="removeMainImage()"
                                            class="absolute top-2 right-2 bg-red-600 text-white w-8 h-8 flex items-center justify-center rounded-full shadow hover:bg-red-700 transition">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Extra Images -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        صور إضافية
                                    </label>
                                    <div
                                        class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer">
                                        <input id="extra_images_input" type="file" name="images[]" accept="image/*"
                                            multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <i class="fas fa-images text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">يمكنك اختيار عدة صور</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (Max 2MB each)</p>
                                    </div>
                                    @error('images.*')
                                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                    <div id="extra_images_preview" class="mt-3 flex flex-wrap gap-3"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                التالي <i class="fas fa-arrow-left mr-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 5: Settings -->
                    <div class="tab-content" id="settings">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-cog text-blue-600"></i>
                            الإعدادات النهائية
                        </h2>

                        <div class="space-y-6">
                            <!-- Status -->
                            <div class="border rounded-lg p-5 bg-gray-50">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    حالة الدورة <span class="text-red-600">*</span>
                                </label>
                                <div class="flex gap-4">
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 border-green-300 bg-white rounded-lg cursor-pointer hover:bg-green-50 transition flex-1">
                                        <input type="radio" name="status" value="active" {{ old('status', 'active'
                                            )=='active' ? 'checked' : '' }} class="w-5 h-5 text-green-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                            <span class="font-medium text-green-700">نشط</span>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 border-gray-300 bg-white rounded-lg cursor-pointer hover:bg-gray-50 transition flex-1">
                                        <input type="radio" name="status" value="inactive" {{ old('status')=='inactive'
                                            ? 'checked' : '' }} class="w-5 h-5 text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-times-circle text-gray-600"></i>
                                            <span class="font-medium text-gray-700">غير نشط</span>
                                        </div>
                                    </label>
                                </div>
                                @error('status')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Summary Box -->
                            <div class="border-2 border-blue-200 rounded-lg p-6 bg-blue-50">
                                <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    ملخص الدورة
                                </h3>
                                <p class="text-sm text-blue-700">
                                    تأكد من مراجعة جميع البيانات قبل الحفظ. يمكنك العودة للتبويبات السابقة للتعديل.
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="submit"
                                class="px-8 py-3 bg-green-600 text-white rounded-lg font-bold text-lg hover:bg-green-700 transition shadow-lg hover:shadow-xl">
                                <i class="fas fa-save ml-2"></i>
                                حفظ الدورة
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>
<script>
    (function() {
    'use strict';

    let currentTab = 0;
    const tabs = document.querySelectorAll('.tab-content');
    const tabButtons = document.querySelectorAll('.tab-button');

    // ========== Tab Navigation ==========
    function showTab(index) {
        tabs.forEach((tab, i) => {
            tab.classList.toggle('active', i === index);
        });
        tabButtons.forEach((btn, i) => {
            btn.classList.toggle('active', i === index);
        });
        currentTab = index;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    tabButtons.forEach((btn, index) => {
        btn.addEventListener('click', () => showTab(index));
    });

    document.addEventListener('click', (e) => {
        if (e.target.closest('.next-tab')) {
            e.preventDefault();
            if (currentTab < tabs.length - 1) showTab(currentTab + 1);
        }
        if (e.target.closest('.prev-tab')) {
            e.preventDefault();
            if (currentTab > 0) showTab(currentTab - 1);
        }
    });

    // ========== Translation Functions ==========
    async function translateText(text, sourceLang, targetLang) {
        if (!text || !text.trim()) return "";
        const cleanText = text.trim();
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${sourceLang}&tl=${targetLang}&dt=t&q=${encodeURIComponent(cleanText)}`;
        try {
            const response = await fetch(url);
            if (!response.ok) return text;
            const data = await response.json();
            if (data && data[0] && Array.isArray(data[0])) {
                let translatedText = '';
                for (let i = 0; i < data[0].length; i++) {
                    if (data[0][i] && data[0][i][0]) translatedText += data[0][i][0];
                }
                return translatedText.trim() || text;
            }
            return text;
        } catch (error) {
            return text;
        }
    }

    function setupTranslation(sourceId, targetId, fromLang, toLang, delay = 1000) {
        const source = document.getElementById(sourceId);
        const target = document.getElementById(targetId);
        if (!source || !target) return;
        let timer = null;
        source.addEventListener('input', (e) => {
            const val = e.target.value;
            if (timer) clearTimeout(timer);
            if (!val.trim()) return;
            timer = setTimeout(async () => {
                const translated = await translateText(val, fromLang, toLang);
                if (translated && translated !== target.value) {
                    target.value = translated;
                }
            }, delay);
        });
    }

    function setupDynamicTranslation(containerId, rowClass, arName, enName) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.addEventListener('input', (e) => {
            const isAr = e.target.name === arName;
            const isEn = e.target.name === enName;
            if (!isAr && !isEn) return;
            const row = e.target.closest(rowClass);
            const target = row ? row.querySelector(`input[name="${isAr ? enName : arName}"]`) : null;
            if (!target) return;
            if (e.target.timer) clearTimeout(e.target.timer);
            const val = e.target.value.trim();
            if (!val) return;
            e.target.timer = setTimeout(async () => {
                const res = await translateText(val, isAr ? 'ar' : 'en', isAr ? 'en' : 'ar');
                if (res && res !== target.value) {
                    target.value = res;
                }
            }, 1000);
        });
    }

    // ========== Location Type Toggle ==========
    function setupLocationTypeToggle() {
        const locationInputs = document.querySelectorAll('input[name="location_type"]');
        const onlineContainer = document.getElementById('online_link_container');
        const venueContainer = document.getElementById('venue_container');
        const onlineLink = document.getElementById('online_link');
        const venueName = document.getElementById('venue_name');

        function updateLocationFields(type) {
            if (type === 'online') {
                onlineContainer.classList.remove('hidden');
                venueContainer.classList.add('hidden');
                onlineLink.setAttribute('required', 'required');
                venueName.removeAttribute('required');
            } else if (type === 'on_site') {
                onlineContainer.classList.add('hidden');
                venueContainer.classList.remove('hidden');
                onlineLink.removeAttribute('required');
                venueName.setAttribute('required', 'required');
            } else {
                onlineContainer.classList.add('hidden');
                venueContainer.classList.add('hidden');
                onlineLink.removeAttribute('required');
                venueName.removeAttribute('required');
            }
        }

        locationInputs.forEach(input => {
            input.addEventListener('change', (e) => updateLocationFields(e.target.value));
            if (input.checked) updateLocationFields(input.value);
        });
    }

    // ========== Dynamic Add/Remove - النسخة المُحسنة ==========
    function setupDynamicRows(containerId, addBtnClass, removeBtnClass, rowClass, createRowFn) {
        const container = document.getElementById(containerId);
        if (!container) return;

        // متغير للتأكد من عدم إضافة الـ listener أكثر من مرة
        if (container.dataset.initialized) return;
        container.dataset.initialized = 'true';

        // البحث عن زر الإضافة
        const addButton = document.querySelector(`.${addBtnClass}`);
        
        if (addButton && !addButton.dataset.hasListener) {
            addButton.dataset.hasListener = 'true';
            addButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                container.insertAdjacentHTML('beforeend', createRowFn());
            });
        }

        // حذف الصفوف
        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest(`.${removeBtnClass}`);
            if (removeBtn) {
                e.preventDefault();
                e.stopPropagation();
                const row = removeBtn.closest(`.${rowClass}`);
                const allRows = container.querySelectorAll(`.${rowClass}`);
                if (row && allRows.length > 1) {
                    row.remove();
                }
            }
        });
    }

    // ========== Image Previews ==========
    function setupImagePreviews() {
        const mainImageInput = document.getElementById('main_image_input');
        const mainPreviewContainer = document.getElementById('main_preview_container');
        const mainImagePreview = document.getElementById('main_image_preview');

        if (mainImageInput && mainPreviewContainer && mainImagePreview) {
            mainImageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        mainImagePreview.src = event.target.result;
                        mainPreviewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        const extraImagesInput = document.getElementById('extra_images_input');
        const extraImagesPreview = document.getElementById('extra_images_preview');

        if (extraImagesInput && extraImagesPreview) {
            extraImagesInput.addEventListener('change', (e) => {
                extraImagesPreview.innerHTML = '';
                Array.from(e.target.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const div = document.createElement('div');
                        div.className = 'relative w-24 h-24';
                        div.innerHTML = `
                            <img src="${event.target.result}" class="w-full h-full object-cover rounded-lg border" />
                            <button type="button" onclick="removeExtraImage(${index})"
                                class="absolute -top-2 -right-2 bg-red-600 text-white w-6 h-6 flex items-center justify-center rounded-full shadow hover:bg-red-700">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        `;
                        extraImagesPreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            });
        }
    }

    // ========== Color Picker Sync ==========
    function setupColorPickers() {
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('button-color-picker')) {
                const row = e.target.closest('.button-row');
                const hexInput = row.querySelector('.button-color-hex');
                if (hexInput) hexInput.value = e.target.value;
            }
        });
    }

    const createFeatureRow = () => `
        <div class="flex gap-2 feature-row">
            <input type="text" name="features_ar[]"
                class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="ميزة بالعربي">
            <input type="text" name="features_en[]" dir="ltr"
                class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Feature in English">
            <button type="button"
                class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    const createButtonRow = () => `
        <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="grid md:grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر (عربي)</label>
                    <input type="text" name="buttons_text_ar[]"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="اطلب الآن">
                </div>
                <div>
                    <label class="block text-sm text-left font-medium text-gray-700 mb-2">Button Text (English)</label>
                    <input type="text" name="buttons_text_en[]" dir="ltr"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Order Now">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رابط الزر</label>
                    <input type="url" name="buttons_link[]" dir="ltr"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="https://example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                    <div class="flex gap-2">
                        <input type="color" name="buttons_color[]" value="#3B82F6"
                            class="w-16 h-10 border border-gray-300 rounded cursor-pointer button-color-picker">
                        <input type="text" name="buttons_color_hex[]" value="#3B82F6" dir="ltr"
                            class="button-color-hex flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="#3B82F6" readonly>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-3">
                <button type="button"
                    class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2 transition">
                    <i class="fas fa-trash"></i>
                    حذف الزر
                </button>
            </div>
        </div>
    `;

    // ========== Initialize ==========
    document.addEventListener('DOMContentLoaded', () => {
        setupTranslation('name_ar', 'name_en', 'ar', 'en', 800);
        setupTranslation('name_en', 'name_ar', 'en', 'ar', 800);
        setupTranslation('description_ar', 'description_en', 'ar', 'en', 1500);
        setupTranslation('description_en', 'description_ar', 'en', 'ar', 1500);
        setupDynamicTranslation('features-container', '.feature-row', 'features_ar[]', 'features_en[]');
        setupDynamicTranslation('requirements-container', '.requirement-row', 'requirements_ar[]', 'requirements_en[]');
        
        setupLocationTypeToggle();
        
        setupDynamicRows('requirements-container', 'add-requirement-btn', 'remove-requirement-btn', 'requirement-row', createRequirementRow);
        setupDynamicRows('features-container', 'add-feature-btn', 'remove-feature-btn', 'feature-row', createFeatureRow);
        setupDynamicRows('buttons-container', 'add-button-btn', 'remove-button-btn', 'button-row', createButtonRow);
        
        setupImagePreviews();
        setupColorPickers();
    });

    // ========== Global Functions ==========
    window.removeMainImage = function() {
        const input = document.getElementById('main_image_input');
        const container = document.getElementById('main_preview_container');
        if (input) input.value = '';
        if (container) container.classList.add('hidden');
    };

    window.removeExtraImage = function(index) {
        const input = document.getElementById('extra_images_input');
        if (!input) return;
        const dt = new DataTransfer();
        const files = input.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) dt.items.add(files[i]);
        }
        input.files = dt.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    };
})();
</script>

@endsection