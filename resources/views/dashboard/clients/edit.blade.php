@extends('layouts.app')

@section('title', 'تعديل العميل')

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
</style>

<section class="p-3 sm:p-5">
    {{-- تفترض أن لديك مكون breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.clients.index') }}" second="العملاء"
        third="تعديل العميل" />

    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                تعديل بيانات العميل: {{ $client->name }}
            </h2>

            <form method="POST" action="{{ route('dashboard.clients.update', $client->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- حقل الاسم --}}
                <div>
                    <label for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم:</label>
                    <input type="text" id="name" name="name" placeholder="أدخل اسم العميل هنا" required
                        value="{{ old('name', $client->name) }}"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    @error('name')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- حقل البريد الإلكتروني --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البريد
                        الإلكتروني:</label>
                    <input type="email" id="email" name="email" placeholder="example@domain.com" required
                        value="{{ old('email', $client->email) }}"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال:</label>
                    <x-text-input id="phone" class="placeholder-gray-500 block mt-1 w-full rtl:text-right" type="text"
                        name="phone" required value="{{ old('phone', $client->phone) }}" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    @error('phone')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">كلمة
                        المرور:</label>
                    <input type="password" id="password" name="password" placeholder="******"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        الحالة
                    </h2>

                    <div class="flex gap-4">
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-green-300 bg-green-50 rounded-lg cursor-pointer">
                            <input type="radio" name="status" value="active" checked class="w-5 h-5 text-green-600">
                            <span class="font-medium text-green-700">نشط</span>
                        </label>
                        <label
                            class="flex items-center gap-3 p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="status" value="inactive" class="w-5 h-5 text-gray-600">
                            <span class="font-medium text-gray-700">غير نشط</span>
                        </label>
                    </div>
                    @error('status')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- زر الحفظ والإرسال --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                    <a href="{{ route('dashboard.clients.index') }}"
                        class="mt-3 w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                        إلغاء والعودة
                    </a>
                </div>
            </form>
        </div>
    </div>


</section>

@endsection