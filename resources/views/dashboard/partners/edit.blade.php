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

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم:</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $partner->name) }}"
                        placeholder="أدخل اسم الشريك هنا" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('name')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني:</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $partner->email) }}"
                        placeholder="example@domain.com" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور (اتركها فارغة
                        إذا لم ترد
                        التغيير):</label>
                    <input type="text" id="password" name="password" placeholder="********"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الأنظمة المسؤول عنها:</label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        @foreach($systems as $system)
                        <div class="flex items-center">
                            <input type="checkbox" id="system_{{ $system->id }}" name="systems_id[]"
                                value="{{ $system->id }}" {{ $partner->systems->contains($system->id) ? 'checked' : ''
                            }}
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="system_{{ $system->id }}"
                                class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
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

                <!-- Services -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الخدمات التي يعمل بها الشريك: <span class="text-red-500">*</span>
                    </label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        @foreach($services as $service)
                        <div class="flex items-center">
                            <input type="checkbox" id="service_{{ $service->id }}" name="services_id[]" {{
                                $partner->services->contains($service->id) ? 'checked' : '' }}
                            value="{{ $service->id }}" {{ in_array($service->id, old('services_id', [])) ? 'checked'
                            :
                            '' }}
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">

                            {{-- تحديث الـ 'for' في الـ label --}}
                            <label for="service_{{ $service->id }}"
                                class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $service->name_ar }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    {{-- تحديث رسالة الخطأ لتستهدف services_id --}}
                    @error('services_id')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        اختر خدمة واحدة أو أكثر يعمل بها الشريك.
                    </p>
                </div>

                {{-- Percentage --}}
                <div>
                    <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">نسبة الشريك
                        (%):</label>
                    <input type="number" id="percentage" name="percentage"
                        value="{{ old('percentage', $partner->percentage) }}" placeholder="مثل: 15.5" step="0.01"
                        required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('percentage')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Services Screen -->
                <div>
                    <label for="services_screen_available" class="block text-sm font-medium text-gray-700 mb-1">
                        هل شاشة الخدمات متوفرة لهذا الشريك <span class="text-red-500">*</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="services_screen_available" value="1" class="sr-only peer" {{
                            old('services_screen_available', $partner->services_screen_available) ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 select-none">متاح</span>
                    </label>
                    @error('services_screen_available')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Services Screen -->
                <div>
                    <label for="is_employee" class="block text-sm font-medium text-gray-700 mb-1">
                        هل هذا الشريك موظف <span class="text-red-500">*</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_employee" value="1" class="sr-only peer" {{ old('is_employee',
                            $partner->is_employee) ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                        <span class="ms-3 text-sm font-medium text-gray-900 select-none">متاح</span>
                    </label>
                    @error('is_employee')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

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
                            {{-- الراتب الأساسي --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ الراتب الأساسي:</label>
                                <input type="number" step="0.01" name="salary_amount"
                                    value="{{ old('salary_amount', $partner->salary_amount) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                    placeholder="0.00">
                            </div>

                            {{-- العملة (كتابة نصية) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">العملة:</label>
                                <input type="text" name="salary_currency"
                                    value="{{ old('salary_currency', $partner->salary_currency) }}"
                                    placeholder="مثلاً: جنيه مصري، USD، ريال"
                                    class="placeholder-gray-400 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition duration-150 ease-in-out">
                            </div>

                            {{-- تاريخ التعيين --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ التعيين:</label>
                                <input type="date" name="hiring_date"
                                    value="{{ old('hiring_date', $partner->hiring_date) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- اختيار السنة --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">سنة صرف الراتب:</label>
                                <select name="salary_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    @php $currentYear = now()->year; @endphp
                                    @for($i = $currentYear - 2; $i <= $currentYear + 2; $i++) <option value="{{ $i }}"
                                        {{ old('salary_year', $partner->salary_year) == $i ? 'selected' : '' }}>{{ $i }}
                                        </option>
                                        @endfor
                                </select>
                            </div>

                            {{-- اختيار الشهر --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">شهر صرف الراتب:</label>
                                <select name="salary_month" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    @foreach([1=>'يناير', 2=>'فبراير', 3=>'مارس', 4=>'أبريل', 5=>'مايو', 6=>'يونيو',
                                    7=>'يوليو', 8=>'أغسطس',
                                    9=>'سبتمبر', 10=>'أكتوبر', 11=>'نوفمبر', 12=>'ديسمبر'] as $num => $name)
                                    <option value="{{ $num }}" {{ old('salary_month', $partner->salary_month) == $num ?
                                        'selected' : ''
                                        }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">مرفق مستند الراتب
                                    (إيصال/كشف):</label>
                                <input type="file" name="salary_attachment"
                                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg bg-white">
                                @if($partner->salary_attachment)
                                <div
                                    class="mt-2 p-2 bg-green-50 rounded border border-green-100 flex items-center justify-between">
                                    <span class="text-xs text-green-700 font-medium">يوجد ملف مرفق حالياً</span>
                                    <a href="{{ asset('storage/' . $partner->salary_attachment) }}" target="_blank"
                                        class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                        <i class="fas fa-download"></i> عرض/تحميل
                                    </a>
                                </div>
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات إضافية على
                                    الراتب:</label>
                                <textarea name="salary_notes" rows="2"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('salary_notes', $partner->salary_notes) }}</textarea>
                            </div>
                        </div>
                        <div class="space-y-6">
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
                                            <input type="checkbox" name="{{ $name }}" value="1" class="sr-only peer" {{
                                                old($name, $partner->$name) ? 'checked' : '' }}>
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
                                        <input type="checkbox" name="apply_salary_scale" id="apply_salary_scale"
                                            value="1" class="sr-only peer" {{ old('apply_salary_scale',
                                            $partner->apply_salary_scale)
                                        ? 'checked' : '' }}>
                                        <div
                                            class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-green-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                        </div>
                                        <span class="ms-3 text-sm font-medium text-gray-700">تفعيل حساب الراتب (26 يوم /
                                            8
                                            ساعات)</span>
                                    </label>

                                    <div id="salary_details"
                                        class="{{ old('apply_salary_scale', $partner->apply_salary_scale) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded-lg border border-green-100">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">الراتب
                                                الأساسي</label>
                                            <input type="number" name="salary_amount"
                                                value="{{ old('salary_amount', $partner->salary_amount) }}"
                                                placeholder="0.00" class="w-full px-3 py-2 border rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">العملة</label>
                                            <input type="text" name="salary_currency"
                                                value="{{ old('salary_currency', $partner->salary_currency) }}"
                                                placeholder="EGP" class="w-full px-3 py-2 border rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">تاريخ
                                                التعيين</label>
                                            <input type="date" name="hiring_date"
                                                value="{{ old('hiring_date', $partner->hiring_date ? $partner->hiring_date->format('Y-m-d') : '') }}"
                                                class="w-full px-3 py-2 border rounded-md">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- الصلاحيات الجديدة (شيك بوكس) --}}

                    <script>
                        // إظهار وإخفاء تفاصيل الراتب بناءً على اختيار السويتش
    document.getElementById('apply_salary_scale').addEventListener('change', function() {
        document.getElementById('salary_details').classList.toggle('hidden', !this.checked);
    });
                    </script>

                    <script>
                        document.querySelector('input[name="is_employee"]').addEventListener('change', function() {
                        const extraFields = document.getElementById('employee_extra_fields');
                        if(this.checked) {
                            extraFields.classList.remove('hidden');
                        } else {
                            extraFields.classList.add('hidden');
                        }
                    });
                    </script>

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


</section>

@endsection