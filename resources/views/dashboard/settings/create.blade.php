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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.settings.index') }}" second="الشركاء" third="إضافة شريك" />
    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            @foreach ($errors->all() as $error)
            {{ $error }}
            @endforeach
            <form method="POST" action="{{ route('dashboard.settings.store') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        الاسم: <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="أدخل اسم الشريك هنا"
                        required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('name')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        البريد الإلكتروني: <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="example@domain.com" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        كلمة المرور: <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="password" name="password" placeholder="********" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Systems -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الأنظمة المسؤول عنها: <span class="text-red-500">*</span>
                    </label>
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
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        اختر نظامًا واحدًا أو أكثر.
                    </p>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الدور: <span class="text-red-500">*</span>
                    </label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                        <div class="flex items-center">
                            <input type="radio" id="role_admin" name="role" value="admin"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
                            <label for="role_admin" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                مدير
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="radio" id="role_client" name="role" value="client"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
                            <label for="role_client" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                عميل
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="radio" id="role_partner" name="role" value="partner"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
                            <label for="role_partner" class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                شريك مطور أنظمة
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="radio" id="role_design_partner" name="role" value="design_partner"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
                            <label for="role_design_partner"
                                class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                شريك مصمم
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="radio" id="role_advertising_partner" name="role" value="advertising_partner"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded-full focus:ring-blue-500">
                            <label for="role_advertising_partner"
                                class="mr-3 text-sm font-medium text-gray-700 cursor-pointer">
                                شريك معلن
                            </label>
                        </div>
                    </div>
                    @error('systems_id')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        اختر الدور.
                    </p>
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
                                <input type="radio" id="withdrawal_wallet" name="withdrawal_method" value="wallet"
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
                                <input type="radio" id="withdrawal_paypal" name="withdrawal_method" value="paypal"
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
                                البريد الإلكتروني <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="withdrawal_email" name="withdrawal_email" required
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Percentage -->
                <div>
                    <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">
                        نسبة الشريك (%) (يمكن إدخال كسور عشرية): <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="percentage" name="percentage" value="{{ old('percentage') }}"
                        placeholder="مثل: 15.5" step="0.01" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('percentage')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between gap-4 pt-4 border-t">
                    <a href="{{ route('dashboard.settings.index') }}"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-right ml-2"></i>
                        رجوع
                    </a>

                    <button type="submit"
                        class="flex-1 flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save"></i>
                        حفظ بيانات الشريك
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const withdrawalMethodInputs = document.querySelectorAll('input[name="withdrawal_method"]');
        const emailInput = document.getElementById('withdrawal_email');
        
        withdrawalMethodInputs.forEach(input => {
            input.addEventListener('change', function() {
                if(this.value === 'paypal') {
                    emailInput.placeholder = 'أدخل البريد الإلكتروني الخاص بحساب PayPal';
                } else if(this.value === 'wallet') {
                    emailInput.placeholder = 'أدخل رقم الهاتف للمحفظة';
                }
            });
        });
    });
    </script>
</section>

@endsection