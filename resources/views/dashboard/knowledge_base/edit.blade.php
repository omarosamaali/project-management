@extends('layouts.app')

@section('title', 'تعديل معلومة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="بنك المعلومات" link="{{ route('dashboard.kb.index') }}" second="تعديل معلومة" />

    <div class="max-w-4xl mx-auto mt-6">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3 dark:text-white">
                <i class="fas fa-edit text-blue-500"></i> تعديل محتوى: {{ $kb->title }}
            </h2>

            <form action="{{ route('dashboard.kb.update', $kb->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">التصنيف</label>
                        <select name="category_id"
                            class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white" required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $kb->category_id == $cat->id ? 'selected' : '' }}>{{
                                $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">العنوان</label>
                        <input type="text" name="title" value="{{ $kb->title }}"
                            class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">التفاصيل</label>
                    <textarea name="details" rows="8"
                        class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white"
                        required>{{ $kb->details }}</textarea>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl border border-dashed border-gray-300">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">تحديث المرفقات
                        (اختياري)</label>
                    <input type="file" name="attachments" class="block w-full text-sm">
                    @if($kb->attachments)
                    <p class="mt-2 text-xs text-blue-500">الملف الحالي موجود بالفعل</p>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('dashboard.kb.index') }}" class="text-gray-500">إلغاء</a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-xl font-bold shadow-lg">تحديث
                        البيانات</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection