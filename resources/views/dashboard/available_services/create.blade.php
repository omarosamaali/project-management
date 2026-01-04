@extends('layouts.app')

@section('title', 'إضافة خدمة جديدة')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.available_services.index') }}" second="إضافة خدمة" />

    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="bg-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <h1 class="text-2xl font-bold mb-2">إضافة خدمة جديدة</h1>
            <p class="text-blue-100">أضف خدمة جديدة للمستخدمين</p>
        </div>

        @if($errors->any())
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-300">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form -->
        <form action="{{ route('dashboard.available_services.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg shadow-md p-6 space-y-6 border border-gray-200">
                <!-- اسم الخدمة -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        اسم الخدمة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="مثال: تطوير المواقع" required>
                </div>

                <!-- الوصف -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        وصف الخدمة <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="وصف مختصر للخدمة" required>{{ old('description') }}</textarea>
                </div>

                <!-- الأيقونة -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        الأيقونة <span class="text-red-500">*</span>
                    </label>
                    <select name="icon"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">اختر أيقونة</option>
                        @foreach($icons as $iconClass => $iconLabel)
                        <option value="{{ $iconClass }}" {{ old('icon')==$iconClass ? 'selected' : '' }}>
                            {{ $iconLabel }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- الترتيب -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        الترتيب
                    </label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">الرقم الأصغر يظهر أولاً</p>
                </div>

                <!-- الحالة -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{
                            old('is_active', true) ? 'checked' : '' }}>
                        <span class="mr-3 text-sm font-bold text-gray-700">الخدمة نشطة</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 pt-4 border-t">
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg flex items-center">
                        <i class="fas fa-save ml-2"></i>
                        حفظ الخدمة
                    </button>
                    <a href="{{ route('dashboard.available_services.index') }}"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg flex items-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection