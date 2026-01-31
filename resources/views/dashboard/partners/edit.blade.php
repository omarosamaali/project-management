@extends('layouts.app') {{-- استخدام القالب الرئيسي --}}

@section('title', 'تعديل الشريك')

@section('content')

<style>
    /* إخفاء أزرار الزيادة/النقصان من حقول الأرقام في المتصفحات */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>

<section class="p-3 sm:p-5">
    {{-- تفترض أن لديك مكون breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.partners.index') }}" second="الشركاء" third="تعديل شريك" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                تعديل بيانات الشريك: {{ $partner->name }}
            </h2>
<form method="POST" action="{{ route('dashboard.partners.update', $partner) }}" class="space-y-6"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="role" value="partner">

    <!-- الاسم -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم:</label>
        <input type="text" id="name" name="name" value="{{ old('name', $partner->name) }}"
            placeholder="أدخل اسم الشريك هنا" required
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('name')
        <span class="text-black text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- البريد الإلكتروني -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني:</label>
        <input type="email" id="email" name="email" value="{{ old('email', $partner->email) }}"
            placeholder="example@domain.com" required
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('email')
        <span class="text-black text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- الدولة الأساسية --}}
{{-- الدولة الأساسية --}}
<div>
    <x-input-label for="first_country" value="الدولة" />
    <div class="flex items-center gap-2 mt-1">
        <select id="country_select2" name="first_country"
            class="!py-3 placeholder-gray-500 block mt-1 w-full rtl:text-right" required>
            <option value="" disabled selected>... جاري تحميل الدول ...</option>
        </select>
    </div>
    <x-input-error :messages="$errors->get('first_country')" class="mt-2" />
</div>

    <!-- كلمة المرور (اختياري في التعديل) -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور (اتركها فارغة إذا لا تريد
            تغييرها):</label>
        <input type="text" id="password" name="password" placeholder="********"
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('password')
        <span class="text-black text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- الأنظمة المسؤول عنها -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الأنظمة المسؤول عنها:</label>
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
            @foreach($systems as $system)
            <div class="flex items-center">
                <input type="checkbox" id="system_{{ $system->id }}" name="systems_id[]" value="{{ $system->id }}" {{
                    in_array($system->id, old('systems_id', $partner->systems->pluck('id')->toArray())) ? 'checked' : ''
                }}
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="system_{{ $system->id }}" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                    {{ $system->name_ar }}
                </label>
            </div>
            @endforeach
        </div>
        @error('systems_id')
        <span class="text-black text-xs mt-1">{{ $message }}</span>
        @enderror
        <p class="mt-2 text-sm text-gray-500">اختر نظامًا واحدًا أو أكثر.</p>
    </div>

    <!-- الخدمات -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            الخدمات التي يعمل بها الشريك: <span class="text-black">*</span>
        </label>
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
            @foreach($services as $service)
            <div class="flex items-center">
                <input type="checkbox" id="service_{{ $service->id }}" name="services_id[]" value="{{ $service->id }}"
                    {{ in_array($service->id, old('services_id', $partner->services->pluck('id')->toArray())) ?
                'checked' : '' }}
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="service_{{ $service->id }}" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                    {{ $service->name_ar }}
                </label>
            </div>
            @endforeach
        </div>
        @error('services_id')
        <span class="text-black text-xs mt-1">{{ $message }}</span>
        @enderror
        <p class="mt-2 text-sm text-gray-500">
            <i class="fas fa-info-circle text-blue-500"></i>
            اختر خدمة واحدة أو أكثر يعمل بها الشريك.
        </p>
    </div>

    <!-- نسبة الشريك -->
    <div>
        <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">نسبة الشريك (%):</label>
        <input type="number" id="percentage" name="percentage" value="{{ old('percentage', $partner->percentage) }}"
            placeholder="مثل: 15.5" step="0.01" required
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('percentage')
        <span class="text-black text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>
    <!-- الصلاحيات والنظام المالي -->
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
            'services_screen_available' => 'شاشة الخدمات متوفرة'
            ];
            @endphp
    
            @foreach($permissions as $name => $label)
            <div class="flex flex-col space-y-2">
                <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="{{ $name }}" value="1" class="sr-only peer" {{ old($name, $partner->$name)
                    ? 'checked' : '' }}>
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                    </div>
                </label>
            </div>
            @endforeach
            <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        هل هذا الشريك موظف <span class="text-black">*</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_employee" id="is_employee" value="1" class="sr-only peer" {{ old('is_employee',
                            $partner->is_employee) ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 select-none">متاح</span>
                    </label>
                    @error('is_employee')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
        </div>
    </div>
    <!-- هل هو موظف -->


    <!-- الحقول الإضافية للموظف -->
    <div id="employee_extra_fields"
        class="{{ old('is_employee', $partner->is_employee) ? '' : 'hidden' }} border-t border-gray-200 pt-6 mt-6">
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
                    <input type="number" step="0.01" name="salary_amount" id="salary_amount"
                        value="{{ old('salary_amount', $partner->salary_amount ?? 0) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                        placeholder="0.00">
                </div>

                <!-- العملة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العملة:</label>
                    <input type="text" name="salary_currency"
                        value="{{ old('salary_currency', $partner->salary_currency) }}"
                        placeholder="مثلاً: جنيه مصري، USD، ريال"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out">
                </div>

                <!-- تاريخ التعيين -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ التعيين:</label>
                    <input type="date" name="hiring_date"
                        value="{{ old('hiring_date', $partner->hiring_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- مرفق مستند الراتب -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        العقد
                    </label>
                    <input type="file" name="salary_attachment"
                        class="w-full px-3 py-1.5 border border-gray-300 rounded-lg bg-white">
                    @if($partner->salary_attachment)
                    <p class="mt-2 text-xs text-green-700">مرفق حالي: <a
                            href="{{ asset('storage/' . $partner->salary_attachment) }}" target="_blank"
                            class="underline">عرض الملف</a></p>
                    @endif
                </div>
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

                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

                        <!-- ساعة بداية العمل -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ساعة بداية العمل:</label>
                            <input type="text" name="work_start_time"
                                value="{{ old('work_start_time', $partner->work_start_time) }}"
                                placeholder="اختر وقت البداية"
                                class="timepicker w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- ساعة نهاية العمل -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ساعة نهاية العمل:</label>
                            <input type="text" name="work_end_time"
                                value="{{ old('work_end_time', $partner->work_end_time) }}"
                                placeholder="اختر وقت النهاية"
                                class="timepicker w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <script>
                            flatpickr(".timepicker", {
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: "h:i K",
                                time_24hr: false,
                                locale: "ar",
                                minuteIncrement: 1
                            });
                        </script>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">عدد ساعات العمل:</label>
                            <input type="number" step="0.5" name="daily_work_hours" id="daily_work_hours"
                                value="{{ old('daily_work_hours', $partner->daily_work_hours ?? 8) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                placeholder="8">
                        </div>

                        <!-- ساعات الاستراحة بالدقائق -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ساعات الاستراحة
                                (بالدقائق):</label>
                            <input type="number" name="break_minutes"
                                value="{{ old('break_minutes', $partner->break_minutes) }}" placeholder="60"
                                class="w-full px-4 py-2 border placeholder:text-gray-300 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <!-- قيمة العمل الإضافي بالساعة -->
                       <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">قيمة العمل الإضافي (بالساعة):</label>
                        <input type="number" step="0.01" name="overtime_hourly_rate" id="overtime_hourly_rate"
                            value="{{ old('overtime_hourly_rate', $partner->overtime_hourly_rate ?? 0) }}"
                            class="w-full placeholder:text-gray-300 px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-gray-100"
                            placeholder="0.00" readonly>
                        <p class="text-xs text-gray-500 mt-1">(يُحسب تلقائيًا: ساعة ونصف من الساعة العادية)</p>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const salaryInput = document.getElementById('salary_amount');
                            const hoursInput = document.getElementById('daily_work_hours');
                            const overtimeInput = document.getElementById('overtime_hourly_rate');
                    
                            function calculateOvertimeRate() {
                                const salary = parseFloat(salaryInput.value) || 0;
                                const hoursPerDay = parseFloat(hoursInput.value) || 0;
                    
                                if (salary > 0 && hoursPerDay > 0) {
                                    // 1. الراتب اليومي = الراتب الشهري ÷ 26
                                    const dailySalary = salary / 26;
                                    
                                    // 2. قيمة الساعة العادية = الراتب اليومي ÷ عدد الساعات
                                    const hourlyRate = dailySalary / hoursPerDay;
                                    
                                    // 3. قيمة الساعة الإضافية = الساعة العادية × 1.5
                                    const overtimeRate = hourlyRate * 1.5;
                                    
                                    // عرض النتيجة بدقة 2 منازل عشرية
                                    overtimeInput.value = overtimeRate.toFixed(2);
                                } else {
                                    overtimeInput.value = '0.00';
                                }
                            }
                    
                            // حدث الحساب عند تغيير أي من الحقلين
                            salaryInput.addEventListener('input', calculateOvertimeRate);
                            hoursInput.addEventListener('input', calculateOvertimeRate);
                    
                            // حساب أولي عند تحميل الصفحة (لو في قيم موجودة)
                            calculateOvertimeRate();
                        });
                    </script>
                    </div>

                    <!-- الخصومات والمدد المسموحة -->
                    <div class="bg-white p-4 rounded-lg border border-purple-100 mb-4">
                        <h4 class="text-sm font-semibold mb-3 text-gray-700 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-orange-500"></i> الخصومات والمدد المسموحة
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">المدة المسموحة للتأخير
                                    (دقيقة):</label>
                                <input type="number" name="allowed_late_minutes"
                                    value="{{ old('allowed_late_minutes', $partner->allowed_late_minutes) }}"
                                    placeholder="15"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">خصم التأخير الصباحي
                                    (دقيقة):</label>
                                <input type="number" step="0.01" name="morning_late_deduction"
                                    value="{{ old('morning_late_deduction', $partner->morning_late_deduction) }}"
                                    placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">خصم التأخير من الاستراحة
                                    (دقيقة):</label>
                                <input type="number" step="0.01" name="break_late_deduction"
                                    value="{{ old('break_late_deduction', $partner->break_late_deduction) }}"
                                    placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">خصم الخروج المبكر
                                    (دقيقة):</label>
                                <input type="number" step="0.01" name="early_leave_deduction"
                                    value="{{ old('early_leave_deduction', $partner->early_leave_deduction) }}"
                                    placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                    </div>

                    <!-- أيام الإجازة -->
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
                            'friday' => 'الجمعة'
                            ];
                            $vacationDays = old('vacation_days', $partner->vacation_days ?? []);
                            @endphp

                            @foreach($weekDays as $day => $dayName)
                            <div class="flex items-center">
                                <input type="checkbox" name="vacation_days[]" value="{{ $day }}" id="day_{{ $day }}"
                                    class="w-4 h-4 text-black bg-gray-100 border-gray-300 rounded focus:ring-red-500"
                                    {{ in_array($day, $vacationDays) ? 'checked' : '' }}>
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

    <!-- مكتبات Select2 و jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- كود تحميل الدول للحقلين معاً -->
    <script>
        $(document).ready(function() {
            const countryDataUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
            const currentCountry = '{{ old('first_country', $partner->country ?? old('country', $partner->country)) }}';

            fetch(countryDataUrl)
                .then(response => response.json())
                .then(data => {
                    const selects = $('#country_select2, #employee_country_select2');

                    selects.each(function() {
                        const select = $(this);
                        select.empty();
                        select.append(new Option("اختر دولتك", "", false, false));

                        data.forEach(country => {
                            const countryName = country.translations.ara.common || country.name.common;
                            const countryCode = country.cca2;
                            const isSelected = currentCountry === countryCode;
                            select.append(new Option(countryName, countryCode, isSelected, isSelected));
                        });
                    });

                    selects.select2({
                        placeholder: "اختر دولتك",
                        allowClear: true,
                        dir: "rtl"
                    });

                    if (currentCountry) {
                        selects.val(currentCountry).trigger('change');
                    }
                })
                .catch(error => {
                    console.error('حدث خطأ أثناء تحميل قائمة الدول:', error);
                    $('#country_select2, #employee_country_select2')
                        .empty()
                        .append(new Option("تعذر تحميل الدول", "", true, true));
                });
        });
    </script>

    <!-- التحكم في إظهار/إخفاء الحقول -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // إظهار/إخفاء الحقول الإضافية للموظف
            const isEmployeeCheckbox = document.getElementById('is_employee');
            const employeeExtraFields = document.getElementById('employee_extra_fields');

            if (isEmployeeCheckbox && employeeExtraFields) {
                isEmployeeCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        employeeExtraFields.classList.remove('hidden');
                    } else {
                        employeeExtraFields.classList.add('hidden');
                    }
                });
            }

            // إظهار/إخفاء تفاصيل نظام حساب الراتب
            const applySalaryScale = document.getElementById('apply_salary_scale');
            const salaryDetails = document.getElementById('salary_details');

            if (applySalaryScale && salaryDetails) {
                applySalaryScale.addEventListener('change', function() {
                    if (this.checked) {
                        salaryDetails.classList.remove('hidden');
                    } else {
                        salaryDetails.classList.add('hidden');
                    }
                });
            }
        });
    </script>

    <!-- زر التحديث -->
    <div class="pt-4">
        <button type="submit"
            class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
            <i class="fas fa-save"></i>
            تحديث بيانات الشريك
        </button>
    </div>
</form>
        </div>
    </div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        const countryDataUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
        
        // القيمة الحالية للدولة الأساسية
        const currentMainCountry = '{{ old('first_country', $partner->first_country ?? '') }}'.trim().toUpperCase();
        
        // القيمة الحالية لدولة الموظف (لو مختلفة)
        const currentEmployeeCountry = '{{ old('country', $partner->country ?? '') }}'.trim().toUpperCase();

        fetch(countryDataUrl)
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                const mainSelect = $('#country_select2');
                const employeeSelect = $('#employee_country_select2');

                // إفراغ الحقلين وإضافة placeholder
                mainSelect.empty().append(new Option("اختر دولتك", "", false, false));
                employeeSelect.empty().append(new Option("اختر دولتك", "", false, false));

                data.forEach(country => {
                    const countryName = country.translations.ara?.common || country.name.common || country.name.official;
                    const countryCode = country.cca2.toUpperCase();

                    // إضافة الـ option للحقل الأساسي
                    const mainOption = new Option(countryName, countryCode, false, false);
                    mainSelect.append(mainOption);

                    // إضافة للحقل التاني
                    const empOption = new Option(countryName, countryCode, false, false);
                    employeeSelect.append(empOption);

                    // لو الكود مطابق للقيمة الحالية، نختاره مباشرة (بدون val)
                    if (countryCode === currentMainCountry) {
                        mainOption.selected = true;
                    }
                    if (countryCode === currentEmployeeCountry) {
                        empOption.selected = true;
                    }
                });

                // تطبيق Select2 بعد إضافة كل الـ options والـ selected
                mainSelect.select2({
                    placeholder: "اختر دولتك",
                    allowClear: true,
                    dir: "rtl"
                });

                employeeSelect.select2({
                    placeholder: "اختر دولتك",
                    allowClear: true,
                    dir: "rtl"
                });

                // لو عايز نضمن تحديث الـ UI (اختياري بس مفيد)
                mainSelect.trigger('change.select2');
                employeeSelect.trigger('change.select2');
            })
            .catch(error => {
                console.error('خطأ في تحميل الدول:', error);
                alert('تعذر تحميل قائمة الدول، جرب تاني أو تحقق من الإنترنت');
            });
    });
</script>
<style>
    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
        display: none !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
        position: relative !important;
        top: 4px !important;
    }

    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
        top: 9px !important;
    }
</style>

</section>

@endsection