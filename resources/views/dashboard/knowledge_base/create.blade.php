@extends('layouts.app')

@section('title', 'إضافة معلومة جديدة - بنك المعلومات')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="بنك المعلومات" link="{{ route('dashboard.kb.index') }}" second="إضافة معلومة" />

    <div class="max-w-4xl mx-auto mt-6">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3 dark:text-white">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-lightbulb text-blue-600 dark:text-blue-300"></i>
                </div>
                إضافة محتوى لبنك المعلومات
            </h2>

            <form action="{{ route('dashboard.kb.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اختر
                            التصنيف</label>
                        <select name="category_id"
                            class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white focus:ring-blue-500"
                            required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">عنوان
                            المطلب/المعلومة</label>
                        <input type="text" name="title"
                            class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white focus:ring-blue-500"
                            placeholder="مثال: خطوات حل مشكلة الدفع" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الشرح والتفاصيل</label>
                    <textarea name="details" rows="8"
                        class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white focus:ring-blue-500"
                        placeholder="اكتب الشرح التقني أو الخطوات بالتفصيل..." required></textarea>
                </div>

                <div
                    class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المرفقات
                        والوثائق</label>
                    <input type="file" name="attachments"
                        class="block w-full text-sm text-gray-500 file:ml-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex items-center justify-between pt-4 border-t dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-medium italic">تمت الإضافة
                            بواسطة:</span>
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ auth()->user()->name
                            }}</span>
                    </div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> حفظ في القاعدة
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection