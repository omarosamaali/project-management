@extends('layouts.app')

@section('title', 'الشركاء')

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

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.partners.index') }}" second="الشركاء" third="إضافة شريك" />
    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            @foreach ($errors->all() as $error)
            {{ $error }}
            @endforeach
<form method="POST" action="{{ route('dashboard.partners.store') }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="role" value="partner">

    <!-- الاسم -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل اسم الشريك هنا" required
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('name')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- البريد الإلكتروني -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني:</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@domain.com" required
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('email')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- كلمة المرور -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور:</label>
        <input type="text" id="password" name="password" placeholder="********" required
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('password')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
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
                    in_array($system->id, old('systems_id', [])) ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="system_{{ $system->id }}" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                    {{ $system->name_ar }}
                </label>
            </div>
            @endforeach
        </div>
        @error('systems_id')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
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
                <input type="checkbox" id="service_{{ $service->id }}" name="services_id[]" value="{{ $service->id }}"
                    {{ in_array($service->id, old('services_id', [])) ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="service_{{ $service->id }}" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                    {{ $service->name_ar }}
                </label>
            </div>
            @endforeach
        </div>
        @error('services_id')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
        <p class="mt-2 text-sm text-gray-500">
            <i class="fas fa-info-circle text-blue-500"></i>
            اختر خدمة واحدة أو أكثر يعمل بها الشريك.
        </p>
    </div>

    <!-- نسبة الشريك -->
    <div>
        <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">نسبة الشريك (%):</label>
        <input type="number" id="percentage" name="percentage" value="{{ old('percentage') }}" placeholder="مثل: 15.5"
            step="0.01" required
            class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
        @error('percentage')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- شاشة الخدمات متوفرة -->
    <div>
        <label for="services_screen_available" class="block text-sm font-medium text-gray-700 mb-1">
            هل شاشة الخدمات متوفرة لهذا الشريك <span class="text-red-500">*</span>
        </label>
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="services_screen_available" value="1" class="sr-only peer" {{
                old('services_screen_available') ? 'checked' : '' }}>
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 select-none">متاح</span>
        </label>
        @error('services_screen_available')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- هل هو موظف -->
    <div>
        <label for="is_employee" class="block text-sm font-medium text-gray-700 mb-1">
            هل هذا الشريك موظف <span class="text-red-500">*</span>
        </label>
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_employee" id="is_employee" value="1" class="sr-only peer" {{
                old('is_employee') ? 'checked' : '' }}>
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 select-none">متاح</span>
        </label>
        @error('is_employee')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- الحقول الإضافية للموظف -->
    <div id="employee_extra_fields" class="{{ old('is_employee') ? '' : 'hidden' }} border-t border-gray-200 pt-6 mt-6">
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
                    <input type="number" step="0.01" name="salary_amount" value="{{ old('salary_amount') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                        placeholder="0.00">
                </div>

                <!-- العملة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العملة:</label>
                    <input type="text" name="salary_currency" value="{{ old('salary_currency') }}"
                        placeholder="مثلاً: جنيه مصري، USD، ريال"
                        class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out">
                </div>

                <!-- تاريخ التعيين -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ التعيين:</label>
                    <input type="date" name="hiring_date" value="{{ old('hiring_date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- اختيار السنة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">سنة صرف الراتب:</label>
                    <select name="salary_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @php $currentYear = now()->year; @endphp
                        @for($i = $currentYear - 2; $i <= $currentYear + 2; $i++) <option value="{{ $i }}" {{
                            old('salary_year')==$i ? 'selected' : '' }}>
                            {{ $i }}
                            </option>
                            @endfor
                    </select>
                </div>

                <!-- اختيار الشهر -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">شهر صرف الراتب:</label>
                    <select name="salary_month" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @foreach([1=>'يناير', 2=>'فبراير', 3=>'مارس', 4=>'أبريل', 5=>'مايو', 6=>'يونيو',
                        7=>'يوليو', 8=>'أغسطس', 9=>'سبتمبر', 10=>'أكتوبر', 11=>'نوفمبر', 12=>'ديسمبر'] as $num => $name)
                        <option value="{{ $num }}" {{ old('salary_month')==$num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- مرفق مستند الراتب -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">مرفق مستند الراتب
                        (إيصال/كشف):</label>
                    <input type="file" name="salary_attachment"
                        class="w-full px-3 py-1.5 border border-gray-300 rounded-lg bg-white">
                </div>

                <!-- ملاحظات إضافية -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات للشريك:</label>
                    <textarea name="salary_notes" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('salary_notes') }}</textarea>
                </div>
            </div>

            <!-- الصلاحيات والنظام المالي -->
            <div class="space-y-6 mt-6">
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
                        'apply_working_hours' => 'تطبيق أوقات العمل',
                        'can_request_meetings' => 'إمكانية طلب اجتماع',
                        'services_screen_available' => 'شاشة الخدمات متوفرة'
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
                    </div>
                </div>

                <div class="bg-green-50 p-6 rounded-xl border border-green-200 shadow-sm">
                    <h3 class="text-md font-bold mb-4 text-green-800 flex items-center gap-2">
                        <i class="fas fa-calculator"></i> نظام الرواتب وساعات العمل
                    </h3>
                    <div class="flex flex-col space-y-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="apply_salary_scale" id="apply_salary_scale" value="1"
                                class="sr-only peer" {{ old('apply_salary_scale') ? 'checked' : '' }}>
                            <div
                                class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-green-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                            </div>
                            <span class="ms-3 text-sm font-medium text-gray-700">تفعيل حساب الراتب (26 يوم / 8
                                ساعات)</span>
                        </label>

                        <div id="salary_details"
                            class="{{ old('apply_salary_scale') ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded-lg border border-green-100">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">الراتب الأساسي</label>
                                <input type="number" name="salary_amount" value="{{ old('salary_amount') }}"
                                    placeholder="0.00" class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">العملة</label>
                                <input type="text" name="salary_currency" value="{{ old('salary_currency') }}"
                                    placeholder="EGP" class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">تاريخ التعيين</label>
                                <input type="date" name="hiring_date" value="{{ old('hiring_date') }}"
                                    class="w-full px-3 py-2 border rounded-md">
                            </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التحكم في إظهار/إخفاء الحقول الإضافية للموظف
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

        // التحكم في إظهار/إخفاء تفاصيل الراتب
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
        </div>
    </div>
</section>

@endsection