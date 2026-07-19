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

    const daysMap = {
        'sunday': 'الأحد', 'monday': 'الإثنين', 'tuesday': 'الثلاثاء',
        'wednesday': 'الأربعاء', 'thursday': 'الخميس', 'friday': 'الجمعة', 'saturday': 'السبت'
    };

    const preSelectedDays = @json(old('rest_days', isset($course) ? $course->rest_days : []));

    function getDayName(date) {
        const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        return days[date.getDay()];
    }

    // تصفير الوقت للمقارنة بالأيام فقط
    function resetTime(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function recalculateRestDays() {
        if (!startDateInput.value || !endDateInput.value) return;

        const start = resetTime(new Date(startDateInput.value));
        const end = resetTime(new Date(endDateInput.value));

        // Inclusive calendar days: same day => 1
        const totalDays = Math.max(0, Math.round((end - start) / (1000 * 60 * 60 * 24))) + 1;

        const selectedRestDays = Array.from(restDaysContainer.querySelectorAll('.rest-day-checkbox:checked'))
                                     .map(cb => cb.value);

        let restDaysCount = 0;
        let currentDate = new Date(start);
        while (currentDate <= end) {
            const dayName = getDayName(currentDate);
            if (selectedRestDays.includes(dayName)) {
                restDaysCount++;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }

        const actualCourseDays = Math.max(0, totalDays - restDaysCount);

        totalDaysDisplay.value = totalDays;
        restDaysCountDisplay.value = restDaysCount;
        countDaysInput.value = actualCourseDays;
    }

    function generateRestDaysCheckboxes(startDate, endDate) {
        const uniqueDays = new Set();
        let currentDate = resetTime(new Date(startDate));
        const end = resetTime(new Date(endDate));

        // إضافة كل الأيام في الفترة للمجموعة
        while (currentDate <= end) {
            uniqueDays.add(getDayName(currentDate));
            currentDate.setDate(currentDate.getDate() + 1);
        }

        const daysOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        const sortedDays = daysOrder.filter(day => uniqueDays.has(day));

        const currentSelections = Array.from(restDaysContainer.querySelectorAll('.rest-day-checkbox:checked'))
                                       .map(cb => cb.value);

        restDaysContainer.innerHTML = '';
        sortedDays.forEach(dayValue => {
            const isChecked = currentSelections.includes(dayValue) || preSelectedDays.includes(dayValue);
            const checkboxHtml = `
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition ${isChecked ? 'border-blue-500 bg-blue-50' : ''}">
                    <input class="rest-day-checkbox w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" 
                           type="checkbox" name="rest_days[]" value="${dayValue}" ${isChecked ? 'checked' : ''}>
                    <span class="text-sm font-medium text-gray-700 ${isChecked ? 'text-blue-700' : ''}">${daysMap[dayValue]}</span>
                </label>`;
            restDaysContainer.insertAdjacentHTML('beforeend', checkboxHtml);
        });

        restDaysContainer.querySelectorAll('.rest-day-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const label = this.closest('label');
                label.classList.toggle('border-blue-500', this.checked);
                label.classList.toggle('bg-blue-50', this.checked);
                recalculateRestDays();
            });
        });

        restDaysContainer.style.display = 'flex';
        recalculateRestDays();
    }

    function calculateDays() {
        if (!startDateInput.value || !endDateInput.value) {
            totalDaysDisplay.value = '0'; restDaysCountDisplay.value = '0'; countDaysInput.value = '0';
            restDaysContainer.style.display = 'none'; return;
        }
        const start = resetTime(new Date(startDateInput.value));
        const end = resetTime(new Date(endDateInput.value));

        if (end < start) {
            alert('تاريخ النهاية لا يمكن أن يكون قبل تاريخ البداية'); return;
        }
        generateRestDaysCheckboxes(start, end);
    }

    startDateInput.addEventListener('change', calculateDays);
    endDateInput.addEventListener('change', calculateDays);
    if (startDateInput.value && endDateInput.value) calculateDays();
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
                                    @php
                                        $needsLoginOld = (string) (old('buttons_needs_login')[$index] ?? '0') === '1';
                                    @endphp
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="buttons_needs_login[]" value="{{ $needsLoginOld ? '1' : '0' }}">
                                            <input type="checkbox" class="sr-only peer" {{ $needsLoginOld ? 'checked' : '' }}
                                                onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                            </div>
                                            <span class="ms-3 text-sm font-medium text-gray-700 select-none">يحتاج تسجيل دخول؟</span>
                                        </label>
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
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="buttons_needs_login[]" value="0">
                                            <input type="checkbox" class="sr-only peer"
                                                onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                                            <div
                                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                            </div>
                                            <span class="ms-3 text-sm font-medium text-gray-700 select-none">يحتاج تسجيل دخول؟</span>
                                        </label>
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

                            @include('dashboard.courses.partials.exam-builder')

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
    window.__courseHasOldInput = {{ $errors->any() || old('name_ar') ? 'true' : 'false' }};
</script>
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
        if (window.__saveCourseDraft) window.__saveCourseDraft();
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

    // ========== Reveal invalid required fields on hidden tabs ==========
    // Without this, an invalid required field on a hidden tab silently blocks
    // submission (the browser can't show its popup on a display:none element).
    (function setupValidationTabJump() {
        const courseForm = document.getElementById('courseForm');
        if (!courseForm) return;

        let handlingInvalid = false;
        courseForm.addEventListener('invalid', function (e) {
            if (handlingInvalid) return; // only act on the first invalid field
            handlingInvalid = true;

            const field = e.target;
            const pane = field.closest('.tab-content');
            if (pane) {
                const idx = Array.from(tabs).indexOf(pane);
                if (idx >= 0 && idx !== currentTab) showTab(idx);
            }

            setTimeout(() => {
                try { field.focus(); } catch (_) {}
                if (typeof field.reportValidity === 'function') field.reportValidity();
                handlingInvalid = false;
            }, 60);
        }, true); // capture: the invalid event does not bubble
    })();

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

    const createRequirementRow = () => `
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
    `;

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
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="hidden" name="buttons_needs_login[]" value="0">
                    <input type="checkbox" class="sr-only peer"
                        onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                    </div>
                    <span class="ms-3 text-sm font-medium text-gray-700 select-none">يحتاج تسجيل دخول؟</span>
                </label>
                <button type="button"
                    class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-2 transition">
                    <i class="fas fa-trash"></i>
                    حذف الزر
                </button>
            </div>
        </div>
    `;

    // ========== Initialize ==========
    function initCourseFormHelpers() {
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
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCourseFormHelpers);
    } else {
        initCourseFormHelpers();
    }

    // Direct bind backup (form HTML is above this script)
    (function bindAddButtonNow() {
        const container = document.getElementById('buttons-container');
        const addBtn = document.querySelector('.add-button-btn');
        if (!container || !addBtn || addBtn.dataset.hasListener === 'true' || addBtn.dataset.courseBound === '1') return;
        addBtn.dataset.courseBound = '1';
        addBtn.dataset.hasListener = 'true';
        addBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            container.insertAdjacentHTML('beforeend', createButtonRow());
        });
    })();

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

    // ========== Draft Autosave (localStorage) ==========
    (function setupDraftPersistence() {
        const DRAFT_KEY = 'course_create_draft_v1';
        const form = document.getElementById('courseForm');
        if (!form) return;

        const val = (name) => {
            const el = form.querySelector(`[name="${name}"]`);
            return el ? el.value : '';
        };
        const radioVal = (name) => {
            const el = form.querySelector(`[name="${name}"]:checked`);
            return el ? el.value : '';
        };

        function collectDraft() {
            const data = {
                scalars: {},
                rest_days: [],
                requirements: [],
                features: [],
                buttons: [],
                exam: { has_exam: false, pass_score: '', duration: '', questions: [] },
                activeTab: currentTab,
            };

            ['name_ar','name_en','price','service_id','counter',
             'start_date','end_date','last_date','count_days',
             'online_link','venue_name','venue_map_url','venue_details',
             'description_ar','description_en'].forEach(n => { data.scalars[n] = val(n); });

            data.scalars.location_type = radioVal('location_type');
            data.scalars.status = radioVal('status');

            data.rest_days = Array.from(form.querySelectorAll('input[name="rest_days[]"]:checked')).map(cb => cb.value);

            form.querySelectorAll('.requirement-row').forEach(row => {
                data.requirements.push({
                    ar: row.querySelector('input[name="requirements_ar[]"]')?.value || '',
                    en: row.querySelector('input[name="requirements_en[]"]')?.value || '',
                });
            });

            form.querySelectorAll('.feature-row').forEach(row => {
                data.features.push({
                    ar: row.querySelector('input[name="features_ar[]"]')?.value || '',
                    en: row.querySelector('input[name="features_en[]"]')?.value || '',
                });
            });

            form.querySelectorAll('.button-row').forEach(row => {
                data.buttons.push({
                    text_ar: row.querySelector('input[name="buttons_text_ar[]"]')?.value || '',
                    text_en: row.querySelector('input[name="buttons_text_en[]"]')?.value || '',
                    link: row.querySelector('input[name="buttons_link[]"]')?.value || '',
                    color: row.querySelector('input[name="buttons_color[]"]')?.value || '#3B82F6',
                    needs_login: row.querySelector('input[name="buttons_needs_login[]"]')?.value || '0',
                });
            });

            const examToggle = form.querySelector('#has_exam_toggle');
            data.exam.has_exam = !!(examToggle && examToggle.checked);
            data.exam.pass_score = val('exam_pass_score');
            data.exam.duration = val('exam_duration_minutes');
            form.querySelectorAll('.exam-question-row').forEach(row => {
                const answers = Array.from(row.querySelectorAll('input[name*="[answers]"]')).map(i => i.value);
                const radios = Array.from(row.querySelectorAll('input[type="radio"]'));
                let correct = radios.findIndex(r => r.checked);
                if (correct < 0) correct = 0;
                data.exam.questions.push({
                    question: row.querySelector('input[name*="[question]"]')?.value || '',
                    answers,
                    correct,
                });
            });

            return data;
        }

        function saveDraft() {
            try {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
            } catch (e) { /* storage full / disabled */ }
        }

        let saveTimer = null;
        function debouncedSave() {
            if (saveTimer) clearTimeout(saveTimer);
            saveTimer = setTimeout(saveDraft, 400);
        }
        window.__saveCourseDraft = debouncedSave;

        function ensureRows(container, currentSelector, createFn, targetCount) {
            if (!container) return;
            let existing = container.querySelectorAll(currentSelector).length;
            while (existing < targetCount) {
                container.insertAdjacentHTML('beforeend', createFn());
                existing++;
            }
        }

        function restoreDraft(draft) {
            // Scalars
            Object.entries(draft.scalars || {}).forEach(([name, value]) => {
                if (value === '' || value == null) return;
                if (name === 'location_type' || name === 'status') {
                    const radio = form.querySelector(`[name="${name}"][value="${value}"]`);
                    if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change', { bubbles: true })); }
                    return;
                }
                const el = form.querySelector(`[name="${name}"]`);
                if (el) el.value = value;
            });

            // Dates -> rebuild rest-day checkboxes, then restore selections
            const startEl = form.querySelector('[name="start_date"]');
            const endEl = form.querySelector('[name="end_date"]');
            if (startEl && startEl.value && endEl && endEl.value) {
                startEl.dispatchEvent(new Event('change', { bubbles: true }));
                endEl.dispatchEvent(new Event('change', { bubbles: true }));
                (draft.rest_days || []).forEach(day => {
                    const cb = form.querySelector(`input[name="rest_days[]"][value="${day}"]`);
                    if (cb && !cb.checked) { cb.checked = true; cb.dispatchEvent(new Event('change', { bubbles: true })); }
                });
            }

            // Requirements
            if ((draft.requirements || []).length) {
                const c = document.getElementById('requirements-container');
                ensureRows(c, '.requirement-row', createRequirementRow, draft.requirements.length);
                const rows = c.querySelectorAll('.requirement-row');
                draft.requirements.forEach((r, i) => {
                    if (!rows[i]) return;
                    const ar = rows[i].querySelector('input[name="requirements_ar[]"]');
                    const en = rows[i].querySelector('input[name="requirements_en[]"]');
                    if (ar) ar.value = r.ar;
                    if (en) en.value = r.en;
                });
            }

            // Features
            if ((draft.features || []).length) {
                const c = document.getElementById('features-container');
                ensureRows(c, '.feature-row', createFeatureRow, draft.features.length);
                const rows = c.querySelectorAll('.feature-row');
                draft.features.forEach((f, i) => {
                    if (!rows[i]) return;
                    const ar = rows[i].querySelector('input[name="features_ar[]"]');
                    const en = rows[i].querySelector('input[name="features_en[]"]');
                    if (ar) ar.value = f.ar;
                    if (en) en.value = f.en;
                });
            }

            // Buttons
            if ((draft.buttons || []).length) {
                const c = document.getElementById('buttons-container');
                ensureRows(c, '.button-row', createButtonRow, draft.buttons.length);
                const rows = c.querySelectorAll('.button-row');
                draft.buttons.forEach((b, i) => {
                    if (!rows[i]) return;
                    const set = (sel, v) => { const el = rows[i].querySelector(sel); if (el) el.value = v; };
                    set('input[name="buttons_text_ar[]"]', b.text_ar);
                    set('input[name="buttons_text_en[]"]', b.text_en);
                    set('input[name="buttons_link[]"]', b.link);
                    set('input[name="buttons_color[]"]', b.color);
                    set('input[name="buttons_color_hex[]"]', b.color);
                    const hidden = rows[i].querySelector('input[name="buttons_needs_login[]"]');
                    const checkbox = rows[i].querySelector('input[type="checkbox"].peer');
                    if (hidden) hidden.value = b.needs_login;
                    if (checkbox) checkbox.checked = b.needs_login === '1';
                });
            }

            // Exam
            const examData = draft.exam || {};
            if (examData.has_exam) {
                const toggle = form.querySelector('#has_exam_toggle');
                if (toggle && !toggle.checked) {
                    toggle.checked = true;
                    toggle.dispatchEvent(new Event('change', { bubbles: true }));
                }
                if (examData.duration) { const d = form.querySelector('[name="exam_duration_minutes"]'); if (d) d.value = examData.duration; }

                const container = document.getElementById('exam-questions-container');
                const addQBtn = document.getElementById('add-exam-question');
                if (container && addQBtn && (examData.questions || []).length) {
                    while (container.querySelectorAll('.exam-question-row').length < examData.questions.length) {
                        addQBtn.click();
                    }
                    const qRows = container.querySelectorAll('.exam-question-row');
                    examData.questions.forEach((q, qi) => {
                        const row = qRows[qi];
                        if (!row) return;
                        const qInput = row.querySelector('input[name*="[question]"]');
                        if (qInput) qInput.value = q.question || '';
                        const answersWrap = row.querySelector('.exam-answers');
                        const addABtn = row.querySelector('.add-exam-answer');
                        const wanted = (q.answers && q.answers.length) ? q.answers.length : 1;
                        while (addABtn && answersWrap.querySelectorAll('.exam-answer-row').length < wanted) {
                            addABtn.click();
                        }
                        const aRows = answersWrap.querySelectorAll('.exam-answer-row');
                        (q.answers || []).forEach((ans, ai) => {
                            const t = aRows[ai] && aRows[ai].querySelector('input[type="text"]');
                            if (t) t.value = ans;
                        });
                        const radios = row.querySelectorAll('input[type="radio"]');
                        if (radios[q.correct]) radios[q.correct].checked = true;
                    });
                }
                if (examData.pass_score) { const p = form.querySelector('[name="exam_pass_score"]'); if (p) p.value = examData.pass_score; }
            }

            if (typeof draft.activeTab === 'number') showTab(draft.activeTab);
        }

        function showRestoredBanner() {
            const bar = document.createElement('div');
            bar.className = 'flex items-center justify-between gap-3 m-4 p-3 text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-lg';
            bar.innerHTML = `
                <span class="flex items-center gap-2"><i class="fas fa-clock-rotate-left"></i> تم استرجاع مسودة غير محفوظة.</span>
                <button type="button" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs">
                    <i class="fas fa-trash ml-1"></i> مسح المسودة والبدء من جديد
                </button>`;
            bar.querySelector('button').addEventListener('click', () => {
                try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
                window.location.reload();
            });
            form.parentNode.insertBefore(bar, form);
        }

        // Restore on load (skip when server returned old input, to avoid duplicates)
        if (!window.__courseHasOldInput) {
            let raw = null;
            try { raw = localStorage.getItem(DRAFT_KEY); } catch (e) {}
            if (raw) {
                try {
                    const draft = JSON.parse(raw);
                    restoreDraft(draft);
                    showRestoredBanner();
                } catch (e) { /* corrupt draft */ }
            }
        }

        // Save on any change / typing
        form.addEventListener('input', debouncedSave);
        form.addEventListener('change', debouncedSave);

        // Clear draft once the course is actually submitted
        form.addEventListener('submit', () => {
            try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
        });
    })();
})();
</script>

@endsection