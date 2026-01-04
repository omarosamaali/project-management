@extends('layouts.app')

@section('title', 'تعديل التصنيف')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="التصنيفات" link="{{ route('dashboard.kb_categories.index') }}" second="تعديل التصنيف" />

    <div class="max-w-2xl mx-auto mt-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-6 dark:text-white"><i class="fas fa-edit text-blue-500 ml-2"></i>تعديل
                التصنيف</h2>

            <form action="{{ route('dashboard.kb_categories.update', $kbCategory->id) }}" method="POST"
                class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-gray-300">أيقونة التصنيف</label>
                    <input type="text" name="icon" value="{{ $kbCategory->icon }}"
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-gray-300">العنوان</label>
                    <input type="text" name="title" value="{{ $kbCategory->title }}"
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-gray-300">الحالة</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                        <option value="1" {{ $kbCategory->status == 1 ? 'selected' : '' }}>فعال</option>
                        <option value="0" {{ $kbCategory->status == 0 ? 'selected' : '' }}>غير فعال</option>
                    </select>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold">تحديث</button>
                    <a href="{{ route('dashboard.kb_categories.index') }}"
                        class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-bold">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection