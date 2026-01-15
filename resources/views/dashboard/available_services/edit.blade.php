@extends('layouts.app')

@section('title', 'تعديل الخدمة: ' . $availableService->name)

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.available_services.index') }}" second="تعديل خدمة" />

    <div class="max-w-3xl mx-auto">
        <div class="bg-blue-500 rounded-lg shadow-lg p-6 mb-6 text-white">
            <h1 class="text-2xl font-bold mb-2">تعديل الخدمة</h1>
            <p class="text-blue-50-100">أنت الآن تقوم بتعديل بيانات: <strong>{{ $availableService->name }}</strong></p>
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

        <form action="{{ route('dashboard.available_services.update', $availableService->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow-md p-6 space-y-6 border border-gray-200">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        اسم الخدمة <span class="text-black">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $availableService->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="مثال: تطوير المواقع" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        وصف الخدمة <span class="text-black">*</span>
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="وصف مختصر للخدمة"
                        required>{{ old('description', $availableService->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        الأيقونة <span class="text-black">*</span>
                    </label>
                    <select name="icon"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">اختر أيقونة</option>
                        @foreach($icons as $iconClass => $iconLabel)
                        <option value="{{ $iconClass }}" {{ old('icon', $availableService->icon) == $iconClass ? 'selected' : ''
                            }}>
                            {{ $iconLabel }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        الترتيب
                    </label>
                    <input type="number" name="order" value="{{ old('order', $availableService->order) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">الرقم الأصغر يظهر أولاً</p>
                </div>

                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{
                            old('is_active', $availableService->is_active) ? 'checked' : '' }}>
                        <span class="mr-3 text-sm font-bold text-gray-700">الخدمة نشطة</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t">
                    <button type="submit"
                        class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg shadow-lg flex items-center transition-colors">
                        <i class="fas fa-sync-alt ml-2"></i>
                        تحديث البيانات
                    </button>
                    <a href="{{ route('dashboard.available_services.index') }}"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg flex items-center transition-colors">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection