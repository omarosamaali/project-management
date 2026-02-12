@extends('layouts.app')
@section('content')
<div class="p-5 pb-0">
    <x-breadcrumb first="المصادر" link="{{ route('dashboard.educational_resources.index') }}" second="تعديل فيديو" />
</div>

<section class="p-5 max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
        <h2 class="text-xl font-bold mb-4 dark:text-white text-right">تعديل المصدر التعليمي</h2>

        <form action="{{ route('dashboard.educational_resources.update', $educational_resource) }}" method="POST"
            class="space-y-4 text-right" dir="rtl">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-bold dark:text-white">عنوان الفيديو</label>
                <input type="text" name="title" value="{{ $educational_resource->title }}"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-bold dark:text-white">لغة الفيديو</label>
                    <select name="language" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white"
                        required>
                        <option value="ar" {{ $educational_resource->language == 'ar' ? 'selected' : '' }}>العربية
                        </option>
                        <option value="en" {{ $educational_resource->language == 'en' ? 'selected' : '' }}>الإنجليزية
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-bold dark:text-white">من سيشاهد الفيديو</label>
                    <select name="users[]" multiple
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
                        <option value="partner" {{ in_array('partner', $educational_resource->users ?? []) ? 'selected'
                            : '' }}>
                            الشركاء الموظفين
                        </option>
                        <option value="independent_partner" {{ in_array('independent_partner', $educational_resource->
                            users ?? []) ? 'selected' : '' }}>
                            الشركاء المستقلين
                        </option>
                        <option value="client" {{ in_array('client', $educational_resource->users ?? []) ? 'selected' :
                            '' }}>
                            العملاء
                        </option>
                    </select>
                    <span class="text-xs font-bold text-gray-500">يمكنك اختيار اكثر من خيار (Ctrl + Click)</span>
                </div>
            </div>

            <div>
                <label class="block mb-1 font-bold dark:text-white">رابط اليوتيوب</label>
                <input type="url" name="youtube_url" value="{{ $educational_resource->youtube_url }}"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
            </div>

            <div>
                <label class="block mb-1 font-bold dark:text-white">الحالة</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                    <option value="1" {{ $educational_resource->status ? 'selected' : '' }}>فعال</option>
                    <option value="0" {{ !$educational_resource->status ? 'selected' : '' }}>غير فعال</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                    <i class="fas fa-save ml-1"></i>
                    تحديث المصدر
                </button>

                <a href="{{ route('dashboard.educational_resources.index') }}"
                    class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-600 transition text-center">
                    <i class="fas fa-times ml-1"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</section>
@endsection