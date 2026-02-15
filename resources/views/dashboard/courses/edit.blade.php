@extends('layouts.app')

@section('title', 'تعديل الدورة: ' . $course->name_ar)

@section('content')
<style>
    /* نفس الـ styles اللي كانت في صفحة الإضافة */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.courses.index') }}" second="الدورات"
        third="تعديل الدورة" />

    <div class="mx-auto max-w-5xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-xl overflow-hidden">

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

            <form action="{{ route('dashboard.courses.update', $course->id) }}" method="POST"
                enctype="multipart/form-data" id="courseForm">
                @csrf
                @method('PUT')

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

                <div class="p-6">
                    <!-- Tab 1: Basic Info -->
                    <div class="tab-content active" id="basic-info">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            المعلومات الأساسية
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    اسم الدورة (بالعربي) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="name_ar" required
                                    value="{{ old('name_ar', $course->name_ar) }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('name_ar') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Course Name (English) <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="name_en" dir="ltr" required
                                    value="{{ old('name_en', $course->name_en) }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('name_en') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    السعر الكلي <span class="text-red-600">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="price" required min="0" step="0.01"
                                        value="{{ old('price', $course->price) }}"
                                        class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-20">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">
                                        <x-drhm-icon width="12" height="14" />
                                </span>
                                </div>
                                @error('price') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    نوع الخدمة
                                </label>
                                <select name="service_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- اختر نوع الخدمة --</option>
                                    @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id', $course->service_id) ==
                                        $service->id ? 'selected' : '' }}>
                                        {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('service_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    الحد الأقصى لعدد المشتركين في الدورة <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="counter" required min="0" step="1"
                                    value="{{ old('counter', $course->counter) }}"
                                    class="placeholder-gray-400 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0">
                                @error('counter')
                                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    كم شخص مسموح لهم التسجيل في هذه الدورة كحد أقصى؟
                                </p>
                            </div>

                        </div>

<!-- Dates -->
<div class="mt-8 pt-6 border-t">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-calendar text-blue-600"></i>
        التواريخ وأيام الدورة
    </h3>

    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                تاريخ ووقت البداية <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" name="start_date" id="start_date" required
                value="{{ old('start_date', optional($course->start_date)->format('Y-m-d\TH:i')) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('start_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                تاريخ ووقت النهاية <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" name="end_date" id="end_date" required
                value="{{ old('end_date', optional($course->end_date)->format('Y-m-d\TH:i')) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('end_date')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                آخر موعد للتسجيل <span class="text-red-600">*</span>
            </label>
            <input type="datetime-local" name="last_date" id="last_date" required
                value="{{ old('last_date', optional($course->last_date)->format('Y-m-d\TH:i')) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
    <div class="grid md:grid-cols-3 gap-6">
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
            <input type="number" name="count_days" id="count_days" required min="0"
                value="{{ old('count_days', $course->count_days) }}" readonly
                class="w-full px-4 py-3 bg-blue-50 border border-blue-300 rounded-lg text-blue-700 font-semibold">
            @error('count_days')
            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

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
                                const preSelectedDays = @json(old('rest_days', $course->rest_days ?? []));

                                // دالة للحصول على اسم اليوم بالإنجليزية من تاريخ
                                function getDayName(date) {
                                    const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                    return days[date.getDay()];
                                }

                                // دالة لإنشاء checkboxes بناءً على الأيام الموجودة
                                function generateRestDaysCheckboxes(startDate, endDate, recalculate = true) {
                                    const uniqueDays = new Set();
                                    let currentDate = new Date(startDate);
                                    const end = new Date(endDate);

                                    while (currentDate <= end) {
                                        const dayName = getDayName(currentDate);
                                        uniqueDays.add(dayName);
                                        currentDate.setDate(currentDate.getDate() + 1);
                                    }

                                    const daysOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                    const sortedDays = daysOrder.filter(day => uniqueDays.has(day));

                                    const currentSelections = [];
                                    const currentCheckboxes = restDaysContainer.querySelectorAll('.rest-day-checkbox:checked');
                                    currentCheckboxes.forEach(cb => currentSelections.push(cb.value));

                                    restDaysContainer.innerHTML = '';

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

                                    const newCheckboxes = restDaysContainer.querySelectorAll('.rest-day-checkbox');
                                    newCheckboxes.forEach(checkbox => {
                                        checkbox.addEventListener('change', function() {
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

                                    const diffTime = Math.abs(end - start);
                                    const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                                    const restDayCheckboxes = restDaysContainer.querySelectorAll('.rest-day-checkbox');
                                    const selectedRestDays = Array.from(restDayCheckboxes)
                                        .filter(checkbox => checkbox.checked)
                                        .map(checkbox => checkbox.value);

                                    let restDaysCount = 0;
                                    let currentDate = new Date(start);

                                    while (currentDate <= end) {
                                        const dayName = getDayName(currentDate);
                                        if (selectedRestDays.includes(dayName)) {
                                            restDaysCount++;
                                        }
                                        currentDate.setDate(currentDate.getDate() + 1);
                                    }

                                    const actualCourseDays = totalDays - restDaysCount;

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
                                        endDateInput.value = startDateInput.value;
                                    }

                                    generateRestDaysCheckboxes(new Date(startDateInput.value), new Date(endDateInput.value), true);
                                }

                                // منع إدخال تاريخ نهاية أقل من البداية
                                startDateInput.addEventListener('change', function () {
                                    endDateInput.min = startDateInput.value;
                                    calculateDays();
                                });

                                endDateInput.addEventListener('change', function () {
                                    if (endDateInput.value < startDateInput.value) {
                                        endDateInput.value = startDateInput.value;
                                    }
                                    calculateDays();
                                });

                                // حساب عند تحميل الصفحة
                                if (startDateInput.value) {
                                    endDateInput.min = startDateInput.value;
                                }
                                
                                if (startDateInput.value && endDateInput.value) {
                                    calculateDays();
                                }
                            });
</script>

                        <!-- Location -->
                        <div class="mt-8 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                                مكان الحضور
                            </h3>

                            <div class="mb-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                        <input type="radio" name="location_type" value="online" {{ old('location_type',
                                            $course->location_type) == 'online' ? 'checked' : '' }} class="w-5 h-5
                                        text-blue-600">
                                        <div>
                                            <div class="font-medium text-gray-800"><i
                                                    class="fas fa-wifi text-blue-600 ml-2"></i>أونلاين</div>
                                            <div class="text-xs text-gray-500">عبر الإنترنت</div>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer hover:bg-green-50 transition">
                                        <input type="radio" name="location_type" value="on_site" {{ old('location_type',
                                            $course->location_type) == 'on_site' ? 'checked' : '' }} class="w-5 h-5
                                        text-green-600">
                                        <div>
                                            <div class="font-medium text-gray-800"><i
                                                    class="fas fa-building text-green-600 ml-2"></i>حضوري</div>
                                            <div class="text-xs text-gray-500">في موقع محدد</div>
                                        </div>
                                    </label>
                                </div>
                                @error('location_type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div id="online_link_container"
                                class="{{ $course->location_type == 'online' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    رابط الدورة الأونلاين <span class="text-red-600">*</span>
                                </label>
                                <input type="url" name="online_link" dir="ltr"
                                    value="{{ old('online_link', $course->online_link) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('online_link') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div id="venue_container"
                                class="{{ $course->location_type == 'on_site' ? '' : 'hidden' }} space-y-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            اسم المكان <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" name="venue_name"
                                            value="{{ old('venue_name', $course->venue_name) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('venue_name') <span class="text-red-600 text-xs mt-1">{{ $message
                                            }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">رابط الخريطة</label>
                                        <input type="url" name="venue_map_url" dir="ltr"
                                            value="{{ old('venue_map_url', $course->venue_map_url) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('venue_map_url') <span class="text-red-600 text-xs mt-1">{{ $message
                                            }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">تفاصيل المكان</label>
                                    <textarea name="venue_details" rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        {{ old('venue_details', $course->venue_details) }}
                                    </textarea>
                                    @error('venue_details') <span class="text-red-600 text-xs mt-1">{{ $message
                                        }}</span> @enderror
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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    الوصف بالعربي <span class="text-red-600">*</span>
                                </label>
                                <textarea name="description_ar" required rows="6"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    {{ old('description_ar', $course->description_ar) }}
                                </textarea>
                                @error('description_ar') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Description (English) <span class="text-red-600">*</span>
                                </label>
                                <textarea name="description_en" dir="ltr" required rows="6"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    {{ old('description_en', $course->description_en) }}
                                </textarea>
                                @error('description_en') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between gap-3 mt-8">
                            <button type="button"
                                class="prev-tab px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                <i class="fas fa-arrow-right ml-2"></i> السابق
                            </button>
                            <button type="button"
                                class="next-tab px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
                                @foreach($course->requirements ?? [] as $req)
                                <div class="flex gap-2 requirement-row">
                                    <input type="text" name="requirements_ar[]" value="{{ $req['ar'] ?? '' }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="متطلب بالعربي">
                                    <input type="text" name="requirements_en[]" dir="ltr" value="{{ $req['en'] ?? '' }}"
                                        class="placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Requirement in English">
                                    <button type="button"
                                        class="remove-requirement-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                                <!-- إذا مفيش بيانات قديمة، سطر فارغ واحد -->
                                @if(empty($course->requirements))
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
                                @foreach($course->features ?? [] as $feat)
                                <div class="flex gap-2 feature-row">
                                    <input type="text" name="features_ar[]" value="{{ $feat['ar'] ?? '' }}"
                                        class="feature-ar-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="ميزة بالعربي">
                                    <input type="text" name="features_en[]" dir="ltr" value="{{ $feat['en'] ?? '' }}"
                                        class="feature-en-input placeholder-gray-400 flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Feature in English">
                                    <button type="button"
                                        class="remove-feature-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                                @if(empty($course->features))
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
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">أزرار الإجراءات</h3>

                            <div id="buttons-container" class="space-y-4 mb-4">
                                @if(old('buttons_text_ar'))
                                @foreach(old('buttons_text_ar') as $index => $text_ar)
                                <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر
                                                (عربي)</label>
                                            <input type="text" name="buttons_text_ar[]" value="{{ $text_ar }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Button Text
                                                (English)</label>
                                            <input type="text" name="buttons_text_en[]" dir="ltr"
                                                value="{{ old('buttons_text_en')[$index] ?? '' }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط
                                                الزر</label>
                                            <input type="url" name="buttons_link[]" dir="ltr"
                                                value="{{ old('buttons_link')[$index] ?? '' }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="buttons_color[]"
                                                    value="{{ old('buttons_color')[$index] ?? '#3B82F6' }}"
                                                    class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                                <input type="text" name="buttons_color_hex[]"
                                                    value="{{ old('buttons_color')[$index] ?? '#3B82F6' }}" dir="ltr"
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-3">
                                        <button type="button"
                                            class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                @foreach($course->buttons ?? [] as $btn)
                                <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر
                                                (عربي)</label>
                                            <input type="text" name="buttons_text_ar[]"
                                                value="{{ $btn['text_ar'] ?? '' }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Button Text
                                                (English)</label>
                                            <input type="text" name="buttons_text_en[]" dir="ltr"
                                                value="{{ $btn['text_en'] ?? '' }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط
                                                الزر</label>
                                            <input type="url" name="buttons_link[]" dir="ltr"
                                                value="{{ $btn['link'] ?? '' }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="buttons_color[]"
                                                    value="{{ $btn['color'] ?? '#3B82F6' }}"
                                                    class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                                <input type="text" name="buttons_color_hex[]"
                                                    value="{{ $btn['color'] ?? '#3B82F6' }}" dir="ltr"
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-3">
                                        <button type="button"
                                            class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                                @if(empty($course->buttons))
                                <!-- سطر فارغ افتراضي -->
                                <div class="button-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الزر
                                                (عربي)</label>
                                            <input type="text" name="buttons_text_ar[]"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Button Text
                                                (English)</label>
                                            <input type="text" name="buttons_text_en[]" dir="ltr"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط
                                                الزر</label>
                                            <input type="url" name="buttons_link[]" dir="ltr"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الزر</label>
                                            <div class="flex gap-2">
                                                <input type="color" name="buttons_color[]" value="#3B82F6"
                                                    class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                                <input type="text" name="buttons_color_hex[]" value="#3B82F6" dir="ltr"
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-3">
                                        <button type="button"
                                            class="remove-button-btn px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
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
                                        الصورة الرئيسية
                                    </label>
                                    <div
                                        class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer">
                                        <input id="main_image_input" type="file" name="main_image" accept="image/*"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">اضغط أو اسحب الصورة هنا (اختياري)</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (Max 2MB)</p>
                                    </div>
                                    @error('main_image') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror

                                    @if($course->main_image)
                                    <div class="mt-4">
                                        <img src="{{ Storage::url($course->main_image) }}" alt="الصورة الرئيسية"
                                            class="w-full h-48 object-cover rounded-lg border">
                                        <p class="text-xs text-gray-500 mt-1">الصورة الحالية</p>
                                    </div>
                                    @endif
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
                                        <p class="text-sm text-gray-600">يمكنك اختيار عدة صور (اختياري)</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (Max 2MB each)</p>
                                    </div>
                                    @error('images.*') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                    @enderror

                                    @if(!empty($course->images))
                                    <div class="mt-4 grid grid-cols-3 gap-3">
                                        @foreach($course->images as $img)
                                        <div class="relative">
                                            <img src="{{ Storage::url($img) }}" alt="صورة إضافية"
                                                class="w-full h-24 object-cover rounded-lg border">
                                            <p class="text-xs text-center text-gray-500 mt-1">صورة حالية</p>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
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
                            <div class="border rounded-lg p-5 bg-gray-50">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    حالة الدورة <span class="text-red-600">*</span>
                                </label>
                                <div class="flex gap-4">
                                    <label
                                        class="flex items-center gap-3 p-4 border-2 border-green-300 bg-white rounded-lg cursor-pointer hover:bg-green-50 transition flex-1">
                                        <input type="radio" name="status" value="active" {{ old('status',
                                            $course->status) == 'active' ? 'checked' : '' }} class="w-5 h-5
                                        text-green-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                            <span class="font-medium text-green-700">نشط</span>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-center gap-3 p-4 border-2 border-gray-300 bg-white rounded-lg cursor-pointer hover:bg-gray-50 transition flex-1">
                                        <input type="radio" name="status" value="inactive" {{ old('status',
                                            $course->status) == 'inactive' ? 'checked' : '' }} class="w-5 h-5
                                        text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-times-circle text-gray-600"></i>
                                            <span class="font-medium text-gray-700">غير نشط</span>
                                        </div>
                                    </label>
                                </div>
                                @error('status') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

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
                                حفظ التعديلات
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

    // ========== Dynamic Add/Remove ==========
    function setupDynamicRows(containerId, addBtnClass, removeBtnClass, rowClass, createRowFn) {
        const container = document.getElementById(containerId);
        if (!container) return;

        document.addEventListener('click', (e) => {
            if (e.target.closest(`.${addBtnClass}`)) {
                e.preventDefault();
                container.insertAdjacentHTML('beforeend', createRowFn());
            }
        });

        container.addEventListener('click', (e) => {
            if (e.target.closest(`.${removeBtnClass}`)) {
                e.preventDefault();
                const row = e.target.closest(`.${rowClass}`);
                if (row && container.querySelectorAll(`.${rowClass}`).length > 1) {
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

    // ========== Row Templates ==========
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