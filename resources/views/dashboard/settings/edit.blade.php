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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.settings.index') }}" second="الاعدادات" third="تعديل شريك" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                تعديل بيانات الشريك: {{ $setting->name }}
            </h2>
            <form method="POST" action="{{ route('dashboard.settings.update', $setting) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم:</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $setting->name) }}"
                        placeholder="أدخل اسم الشريك هنا" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('name')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني:</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $setting->email) }}"
                        placeholder="example@domain.com" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('email')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور (اتركها فارغة
                        إذا لم ترد
                        التغيير):</label>
                    <input type="text" id="password" name="password" placeholder="********"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('password')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
<!-- Role -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        الدور: <span class="text-black">*</span>
    </label>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">

        <div class="flex items-center">
            <input type="radio" id="role_admin" name="role" value="admin" {{ old('role', $setting->role) == 'admin' ?
            'checked' : '' }}
            class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
            <label for="role_admin" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                مدير
            </label>
        </div>

        <div class="flex items-center">
            <input type="radio" id="role_client" name="role" value="client" {{ old('role', $setting->role) == 'client' ?
            'checked' : '' }}
            class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
            <label for="role_client" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                عميل
            </label>
        </div>

        <div class="flex items-center">
            <input type="radio" id="role_partner" name="role" value="partner" {{ old('role', $setting->role) ==
            'partner' ? 'checked' : '' }}
            class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
            <label for="role_partner" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                شريك مطور أنظمة
            </label>
        </div>

        <div class="flex items-center">
            <input type="radio" id="role_design_partner" name="role" value="design_partner" {{ old('role',
                $setting->role) == 'design_partner' ? 'checked' : '' }}
            class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
            <label for="role_design_partner" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                شريك مصمم
            </label>
        </div>

        <div class="flex items-center">
            <input type="radio" id="role_advertising_partner" name="role" value="advertising_partner" {{ old('role',
                $setting->role) == 'advertising_partner' ? 'checked' : '' }}
            class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
            <label for="role_advertising_partner" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                شريك معلن
            </label>
        </div>

    </div>
    @error('role')
    <span class="text-black text-xs mt-1">{{ $message }}</span>
    @enderror
    <p class="mt-2 text-sm text-gray-500">
        <i class="fas fa-info-circle text-blue-500"></i>
        اختر الدور.
    </p>
</div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الأنظمة المسؤول عنها:</label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        @foreach($systems as $system)
                        <div class="flex items-center">
                            <input type="checkbox" id="system_{{ $system->id }}" name="systems_id[]"
                                value="{{ $system->id }}" {{ $setting->systems->contains($system->id) ? 'checked' : ''
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
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">اختر نظامًا واحدًا أو أكثر.</p>
                </div>

                <div>
                    <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">نسبة الشريك
                        (%):</label>
                    <input type="number" id="percentage" name="percentage"
                        value="{{ old('percentage', $setting->percentage) }}" placeholder="مثل: 15.5" step="0.01"
                        required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('percentage')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- طرق سحب الأرباح -->
                <div class="space-y-6">
                    <!-- طرق سحب الأرباح -->
                    <div class="border border-gray-300 rounded-lg p-6 bg-white">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">طرق سحب الأرباح</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- محفظة إلكترونية -->
                            <div
                                class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                                <input {{ old('withdrawal_method', $setting->withdrawal_method) == 'wallet' ? 'checked'
                                : '' }}
                                type="radio" id="withdrawal_wallet" name="withdrawal_method" value="wallet"
                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <label for="withdrawal_wallet"
                                    class="mr-3 text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                                    <svg class="w-5 h-5 ml-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                        <path fill-rule="evenodd"
                                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    محفظة إلكترونية
                                </label>
                            </div>

                            <!-- PayPal -->
                            <div
                                class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                                <input {{ old('withdrawal_method', $setting->withdrawal_method) == 'paypal' ? 'checked'
                                : '' }} type="radio" id="withdrawal_paypal" name="withdrawal_method" value="paypal"
                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <label for="withdrawal_paypal"
                                    class="mr-3 text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                                    <svg class="w-5 h-5 ml-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    PayPal
                                </label>
                            </div>
                        </div>

                        <!-- حقل البريد الإلكتروني -->
                        <div class="mt-4">
                            <label for="withdrawal_email" class="block text-sm font-medium text-gray-700 mb-2">
                                البريد الإلكتروني <span class="text-black">*</span>
                            </label>
                            <input value="{{ old('withdrawal_email', $setting->withdrawal_email) }}" type="text"
                                id="withdrawal_email" name="withdrawal_email"
                                placeholder="أدخل البريد الإلكتروني للمحفظة أو PayPal"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">سيتم إرسال الأرباح إلى هذا البريد الإلكتروني</p>
                        </div>

                        <!-- معلومات إضافية (اختياري) -->
                        <div class="mt-4">
                            <label for="withdrawal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                ملاحظات إضافية (اختياري)
                            </label>
                            <textarea id="withdrawal_notes" name="withdrawal_notes" rows="3"
                                placeholder="أي معلومات إضافية تساعدنا في معالجة السحب"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('withdrawal_notes', $setting->withdrawal_notes) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- <div>
                    <label for="orders" class="block text-sm font-medium text-gray-700 mb-1">عدد المشاريع:</label>
                    <input type="number" id="orders" name="orders" value="{{ old('orders', $setting->orders) }}"
                        placeholder="30"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('orders')
                    <span class="text-black text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div> --}}

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