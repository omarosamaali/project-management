@extends('layouts.app')
{{-- تم حذف: intl-tel-input CSS - كان يسبب مشكلة في حقل الهاتف --}}

@section('title', 'الشركاء')

@section('content')
<style>
    /* منع intl-tel-input من الظهور على حقل الهاتف - يمنع الظهور فوراً */
    .iti:has(#phone),
    .iti:has(#phone) .iti__flag-container,
    .iti:has(#phone) .iti__selected-flag,
    .iti:has(#phone) .iti__arrow,
    .iti:has(#phone) .iti__selected-dial-code,
    .iti:has(#phone) .iti__flag {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        pointer-events: none !important;
    }

    /* إظهار الـ input نفسه بشكل طبيعي */
    .iti:has(#phone) input#phone {
        display: block !important;
        width: 100% !important;
        padding-right: 1rem !important;
        padding-left: 1rem !important;
        position: static !important;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
        display: none !important;
    }

    .select2-container,
    .iti {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 42px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
        position: relative !important;
        top: 4px !important;
    }

    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
        top: 9px !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db !important;
    }
</style>

<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.partners.index') }}" second="الشركاء" third="إضافة شريك" />
    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">

            @foreach ($errors->all() as $error)
            <p class="text-red-600 text-xs mb-1">{{ $error }}</p>
            @endforeach

            <form method="POST" action="{{ route('dashboard.partners.store') }}" class="space-y-6"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="role" value="partner">

                <!-- الاسم -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم:</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل اسم الشريك هنا"
                        required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('name')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- البريد الإلكتروني -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="example@domain.com" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('email')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- رقم الهاتف - بدون intl-tel-input -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف:</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="مثل: 01012345678"
                        autocomplete="off"
                        class="no-intl-tel placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('phone')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الدولة -->
                <div>
                    <x-input-label for="country" value="الدولة" />
                    <div class="flex items-center gap-2 mt-1">
                        <select id="country_select2" name="first_country"
                            class="!py-3 placeholder-gray-500 block mt-1 w-full rtl:text-right" required>
                            <option value="" disabled selected>... جاري تحميل الدول ...</option>
                        </select>
                    </div>
                    <x-input-error :messages="$errors->get('first_country')" class="mt-2" />
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور:</label>
                    <input type="text" id="password" name="password" placeholder="********" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('password')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الأنظمة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الأنظمة المسؤول عنها:</label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        @foreach($systems as $system)
                        <div class="flex items-center">
                            <input type="checkbox" id="system_{{ $system->id }}" name="systems_id[]"
                                value="{{ $system->id }}" {{ in_array($system->id, old('systems_id', [])) ? 'checked' :
                            '' }}
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="system_{{ $system->id }}"
                                class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $system->name_ar }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('systems_id')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">اختر نظامًا واحدًا أو أكثر.</p>
                </div>

                <!-- الخدمات -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الخدمات التي يعمل بها الشريك: <span class="text-red-500">*</span>
                    </label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        @foreach($services as $service)
                        <div class="flex items-center">
                            <input type="checkbox" id="service_{{ $service->id }}" name="services_id[]"
                                value="{{ $service->id }}" {{ in_array($service->id, old('services_id', [])) ? 'checked'
                            : '' }}
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="service_{{ $service->id }}"
                                class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $service->name_ar }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('services_id')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- نسبة الشريك -->
                <div>
                    <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">نسبة الشريك
                        (%):</label>
                    <input type="number" id="percentage" name="percentage" value="{{ old('percentage') }}"
                        placeholder="مثل: 15.5" step="0.01" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('percentage')
                    <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الصلاحيات -->
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 shadow-sm">
                    <h3 class="text-md font-bold mb-4 text-blue-800 flex items-center gap-2">
                        <i class="fas fa-user-shield"></i> صلاحيات الوصول والإدارة
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @php
                        $permissions = [
                        'can_view_projects' => 'الاطلاع على المشاريع',
                        'can_view_notes' => 'الاطلاع على الملاحظات',
                        'can_propose_quotes' => 'تقديم عرض سعر',
                        'can_enter_knowledge_bank' => 'إدخال بنك معلومات',
                        'apply_working_hours' => 'تطبيق الحضور والإنصراف',
                        'can_request_meetings' => 'إمكانية طلب اجتماع',
                        'services_screen_available' => 'شاشة الخدمات متوفرة',
                        ];
                        @endphp

                        @foreach($permissions as $name => $label)
                        <div class="flex flex-col space-y-2">
                            <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="{{ $name }}" value="1" class="sr-only peer" {{ old($name)
                                    ? 'checked' : '' }}>
                                <div
                                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>
                        @endforeach

                        <!-- هل هو موظف -->
                        <div>
                            <label for="is_employee" class="block text-sm font-medium text-gray-700 mb-1">
                                هل هذا الشريك موظف
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_employee" id="is_employee" value="1"
                                    class="sr-only peer" {{ old('is_employee') ? 'checked' : '' }}>
                                <div
                                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                                <span class="ms-3 text-sm font-medium text-gray-900 select-none">متاح</span>
                            </label>
                            @error('is_employee')
                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- الحقول الإضافية للموظف -->
                <div id="employee_extra_fields"
                    class="{{ old('is_employee') ? '' : 'hidden' }} border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-bold mb-4 text-blue-800 flex items-center gap-2">
                        <i class="fas fa-user-tie"></i> بيانات إضافية للموظف
                    </h3>
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-semibold mb-4 text-gray-700 flex items-center gap-2">
                            <i class="fas fa-money-check-alt text-green-500"></i> البيانات المالية والرواتب:
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 pb-6 border-b border-gray-200">
                            <!-- الراتب الأساسي -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ الراتب الأساسي:</label>
                                <input type="number" step="0.01" name="salary_amount" id="salary_amount_create"
                                    value="{{ old('salary_amount') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                    placeholder="0.00">
                            </div>

                            <!-- العملة -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">العملة:</label>
                                <input type="text" name="salary_currency" value="{{ old('salary_currency') }}"
                                    placeholder="مثلاً: جنيه مصري، USD، ريال"
                                    class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>

                            <!-- تاريخ التعيين -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ التعيين:</label>
                                <input type="date" name="hiring_date" value="{{ old('hiring_date') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <!-- العقد -->
                        <div class="md:col-span-2 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">العقد</label>
                            <input type="file" name="salary_attachment"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded-lg bg-white">
                        </div>

                        <!-- نظام ساعات العمل والدوام -->
                        <div class="space-y-6 mt-6">
                            <div class="bg-purple-50 p-6 rounded-xl border border-purple-200 shadow-sm">
                                <h3 class="text-md font-bold mb-4 text-purple-800 flex items-center gap-2">
                                    <i class="fas fa-clock"></i> نظام ساعات العمل والدوام
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">

                                    <!-- الدولة -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">الدولة:</label>
                                        <select id="employee_country_select2" name="country"
                                            class="!py-3 placeholder-gray-500 w-full px-4 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 rtl:text-right">
                                            <option value="" disabled selected>... جاري تحميل الدول ...</option>
                                        </select>
                                    </div>

                                    <!-- ساعة بداية العمل -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ساعة بداية
                                            العمل:</label>
                                        <input type="text" name="work_start_time" value="{{ old('work_start_time') }}"
                                            placeholder="اختر وقت البداية"
                                            class="timepicker w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    </div>

                                    <!-- ساعة نهاية العمل -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ساعة نهاية
                                            العمل:</label>
                                        <input type="text" name="work_end_time" value="{{ old('work_end_time') }}"
                                            placeholder="اختر وقت النهاية"
                                            class="timepicker w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    </div>

                                    <!-- عدد ساعات العمل -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">عدد ساعات
                                            العمل:</label>
                                        <input type="number" step="0.5" name="daily_work_hours"
                                            id="daily_work_hours_create" value="{{ old('daily_work_hours', 8) }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                            placeholder="8">
                                    </div>

                                    <!-- ساعات الاستراحة -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ساعات الاستراحة
                                            (بالدقائق):</label>
                                        <input type="number" name="break_minutes" value="{{ old('break_minutes') }}"
                                            placeholder="60"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    </div>

                                    <!-- قيمة العمل الإضافي -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">قيمة العمل الإضافي
                                            (بالساعة):</label>
                                        <input type="number" step="0.01" name="overtime_hourly_rate"
                                            id="overtime_hourly_rate_create" value="{{ old('overtime_hourly_rate') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-gray-100"
                                            placeholder="0.00" readonly>
                                        <p class="text-xs text-gray-500 mt-1">يُحسب تلقائيًا: ساعة ونصف من قيمة الساعة
                                            العادية</p>
                                    </div>

                                </div>

                                <!-- الخصومات -->
                                <div class="bg-white p-4 rounded-lg border border-purple-100 mb-4">
                                    <h4 class="text-sm font-semibold mb-3 text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle text-orange-500"></i> الخصومات والمدد
                                        المسموحة
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">المدة المسموحة
                                                للتأخير (دقيقة):</label>
                                            <input type="number" name="allowed_late_minutes"
                                                value="{{ old('allowed_late_minutes') }}" placeholder="15"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">خصم التأخير
                                                الصباحي (دقيقة):</label>
                                            <input type="number" step="0.01" name="morning_late_deduction"
                                                value="{{ old('morning_late_deduction') }}" placeholder="0.00"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">خصم التأخير من
                                                الاستراحة (دقيقة):</label>
                                            <input type="number" step="0.01" name="break_late_deduction"
                                                value="{{ old('break_late_deduction') }}" placeholder="0.00"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">خصم الخروج
                                                المبكر (دقيقة):</label>
                                            <input type="number" step="0.01" name="early_leave_deduction"
                                                value="{{ old('early_leave_deduction') }}" placeholder="0.00"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- أيام الإجازة الأسبوعية -->
                                <div class="bg-white p-4 rounded-lg border border-purple-100">
                                    <h4 class="text-sm font-semibold mb-3 text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-calendar-times text-black"></i> أيام الإجازة الأسبوعية
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                                        @php
                                        $weekDays = [
                                        'saturday' => 'السبت',
                                        'sunday' => 'الأحد',
                                        'monday' => 'الاثنين',
                                        'tuesday' => 'الثلاثاء',
                                        'wednesday' => 'الأربعاء',
                                        'thursday' => 'الخميس',
                                        'friday' => 'الجمعة',
                                        ];
                                        @endphp

                                        @foreach($weekDays as $day => $dayName)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="vacation_days[]" value="{{ $day }}"
                                                id="day_{{ $day }}"
                                                class="w-4 h-4 text-black bg-gray-100 border-gray-300 rounded focus:ring-red-500"
                                                {{ in_array($day, old('vacation_days', [])) ? 'checked' : '' }}>
                                            <label for="day_{{ $day }}" class="mr-2 text-sm font-medium text-gray-700">
                                                {{ $dayName }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- زر الحفظ -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save"></i>
                        حفظ بيانات الشريك
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- مكتبات CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

{{-- مكتبات JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

<script>
    // =====================
    // Flatpickr - Time
    // =====================
    flatpickr(".timepicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false,
        locale: "ar",
        minuteIncrement: 1
    });

    // =====================
    // Overtime Calculator
    // =====================
    document.addEventListener('DOMContentLoaded', function () {
        const salaryInput   = document.getElementById('salary_amount_create');
        const hoursInput    = document.getElementById('daily_work_hours_create');
        const overtimeInput = document.getElementById('overtime_hourly_rate_create');

        function calculateOvertimeRate() {
            const salary      = parseFloat(salaryInput.value) || 0;
            const hoursPerDay = parseFloat(hoursInput.value) || 0;
            if (salary > 0 && hoursPerDay > 0) {
                const dailySalary  = salary / 26;
                const hourlyRate   = dailySalary / hoursPerDay;
                overtimeInput.value = (hourlyRate * 1.5).toFixed(2);
            } else {
                overtimeInput.value = '0.00';
            }
        }

        salaryInput?.addEventListener('input', calculateOvertimeRate);
        hoursInput?.addEventListener('input', calculateOvertimeRate);
        calculateOvertimeRate();
    });

    // =====================
    // Show/Hide Employee Fields
    // =====================
    document.addEventListener('DOMContentLoaded', function () {
        const isEmployeeCheckbox  = document.getElementById('is_employee');
        const employeeExtraFields = document.getElementById('employee_extra_fields');

        if (isEmployeeCheckbox && employeeExtraFields) {
            isEmployeeCheckbox.addEventListener('change', function () {
                employeeExtraFields.classList.toggle('hidden', !this.checked);
            });
        }
    });

    // =====================
    // منع intl-tel-input نهائياً على حقل الهاتف
    // =====================
    (function () {
        const phoneInput = document.getElementById('phone');
        if (!phoneInput) return;

        function stripIntlTel() {
            // لو اتحط في wrapper، نطلعه منه فوراً
            const wrapper = phoneInput.closest('.iti');
            if (wrapper) {
                wrapper.replaceWith(phoneInput);
            }
            // تدمير الـ instance
            if (typeof intlTelInputGlobals !== 'undefined') {
                const inst = intlTelInputGlobals.getInstance(phoneInput);
                if (inst) inst.destroy();
            }
        }

        // مراقبة أي تغيير في الـ DOM حول حقل الهاتف
        const observer = new MutationObserver(function () {
            stripIntlTel();
        });

        observer.observe(document.body, { childList: true, subtree: true });

        // وقف المراقبة بعد ثانيتين (بعد ما كل الـ JS يتحمل)
        setTimeout(function () {
            observer.disconnect();
            stripIntlTel(); // مرة أخيرة للتأكيد
        }, 2000);

        // عند الـ submit: تأكد من الرقم بدون كود دولي
        document.addEventListener('submit', function (e) {
            if (e.target.contains(phoneInput)) {
                phoneInput.value = phoneInput.value.replace(/^\+\d{1,4}\s*/, '').trim();
            }
        });
    })();

    // =====================
    // Select2 - Countries
    // =====================
    $(document).ready(function () {
        const countryDataUrl     = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
        const currentCountry     = '{{ old('first_country', '') }}'.trim().toUpperCase();
        const currentEmpCountry  = '{{ old('country', '') }}'.trim().toUpperCase();

        fetch(countryDataUrl)
            .then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); })
            .then(data => {
                const mainSelect = $('#country_select2');
                const empSelect  = $('#employee_country_select2');

                mainSelect.empty().append(new Option("اختر دولتك", "", false, false));
                empSelect.empty().append(new Option("اختر دولتك", "", false, false));

                data.forEach(country => {
                    const name = country.translations.ara?.common || country.name.common;
                    const code = country.cca2.toUpperCase();

                    mainSelect.append(new Option(name, code, false, code === currentCountry));
                    empSelect.append(new Option(name, code, false, code === currentEmpCountry));
                });

                mainSelect.select2({ placeholder: "اختر دولتك", allowClear: true, dir: "rtl" });
                empSelect.select2({ placeholder: "اختر دولتك", allowClear: true, dir: "rtl" });

                mainSelect.trigger('change.select2');
                empSelect.trigger('change.select2');
            })
            .catch(() => {
                $('#country_select2, #employee_country_select2')
                    .empty().append(new Option("تعذر تحميل الدول", "", true, true));
            });
    });
</script>

@endsection