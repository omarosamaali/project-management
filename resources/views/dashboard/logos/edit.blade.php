@extends('layouts.app')

@section('content')
<div class="p-5 pb-0">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.logos.index') }}" second="تعديل الشعار" />
</div>

<section class="p-5 max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 text-right"
        dir="rtl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold dark:text-white">تعديل بيانات الشعار</h2>
            <a href="{{ route('dashboard.logos.index') }}"
                class="text-sm text-gray-500 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left ml-1"></i> العودة للقائمة
            </a>
        </div>

        <form action="{{ route('dashboard.logos.update', $logo->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            {{-- معاينة الصورة الحالية --}}
            <div class="space-y-2">
                <label class="block font-bold dark:text-gray-300">الشعار الحالي:</label>
                <div
                    class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 flex justify-center items-center border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <img src="{{ asset('storage/' . $logo->image_path) }}" class="max-h-32 w-auto drop-shadow-sm"
                        alt="{{ $logo->name }}">
                </div>
            </div>

            {{-- تعديل الاسم --}}
            <div>
                <label class="block mb-2 font-bold dark:text-gray-300">اسم الشركة:</label>
                <input type="text" name="name" value="{{ old('name', $logo->name) }}"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- رفع صورة جديدة --}}
            <div>
                <label class="block mb-2 font-bold dark:text-gray-300">تغيير الصورة (اختياري):</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                <p class="text-[11px] text-gray-500 mt-1 italic">اتركه فارغاً إذا كنت لا تريد تغيير الصورة الحالية.</p>
            </div>

            {{-- أزرار التحكم --}}
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 dark:shadow-none">
                    حفظ التعديلات
                </button>

                <a href="{{ route('dashboard.logos.index') }}"
                    class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-center">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</section>
@endsection