@extends('layouts.app')
@section('content')
<div class="p-5 pb-0">
    <x-breadcrumb first="المصادر" link="{{ route('dashboard.educational_resources.index') }}" second="إضافة فيديو" />
</div>

<section class="p-5 max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
        <h2 class="text-xl font-bold mb-4 dark:text-white text-right">إضافة مصدر تعليمي جديد</h2>

        <form action="{{ route('dashboard.educational_resources.store') }}" method="POST" class="space-y-4 text-right" dir="rtl">
            @csrf
            <div>
                <label class="block mb-1 font-bold dark:text-white">عنوان الفيديو</label>
                <input type="text" name="title"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-bold dark:text-white">لغة الفيديو</label>
                    <select name="language" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white"
                        required>
                        <option value="ar">العربية</option>
                        <option value="en">الإنجليزية</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-bold dark:text-white">من سيشاهد الفيديو</label>
                    <select name="users[]" multiple class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
                        <option value="partner">الشركاء الموظفين</option>
                        <option value="independent_partner">الشركاء المستقلين</option>
                        <option value="client">العملاء</option>
                    </select>
                    <span class="text-xs font-bold text-gray-500">يمكنك اختيار اكثر من خيار</span>
                    </div>
            </div>

            <div>
                <label class="block mb-1 font-bold dark:text-white">رابط اليوتيوب</label>
                <input type="url" name="youtube_url" placeholder="https://youtube.com/..."
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
            </div>

            <div>
                <label class="block mb-1 font-bold dark:text-white">الحالة</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                    <option value="1">فعال</option>
                    <option value="0">غير فعال</option>
                </select>
            </div>

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg w-full font-bold hover:bg-blue-700 transition">
                حفظ المصدر
            </button>
        </form>
    </div>
</section>
@endsection