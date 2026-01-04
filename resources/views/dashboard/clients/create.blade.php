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
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.clients.index') }}" second="الشركاء" third="إضافة شريك" />
    <div class="mx-auto max-w-4xl w-full rounded-xl">
        <div class="p-3 bg-white dark:bg-gray-800 relative shadow-xl border rounded-xl overflow-hidden">
            @foreach ($errors->all() as $error)
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            </div>
            @endforeach
            <form method="POST" action="{{ route('dashboard.clients.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="role" value="client">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم:</label>
                    <input type="text" id="name" name="name" placeholder="أدخل اسم العميل هنا" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('name')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني:</label>
                    <input type="email" id="email" name="email" placeholder="example@domain.com" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال:</label>
                    <x-text-input id="phone" class="placeholder-gray-500 block mt-1 w-full rtl:text-right" type="text"
                        name="phone" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    @error('phone')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور:</label>
                    <input type="text" id="password" name="password" placeholder="********" required
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
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

@endsection