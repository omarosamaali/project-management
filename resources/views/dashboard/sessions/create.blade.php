@extends('layouts.app')
@section('content')
<div class="p-5 pb-0">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.sessions.index') }}" second="الاجتماعات" />
</div>

<section class="p-5 max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
        <h2 class="text-xl font-bold mb-4 dark:text-white text-right">طلب اجتماع جديد</h2>
        <form action="{{ route('dashboard.sessions.store') }}" method="POST" class="space-y-4 text-right" dir="rtl">
            @csrf
            <div>
                <label class="block mb-1 font-bold">عنوان الاجتماع</label>
                <input type="text" name="title" class="w-full rounded-lg border-gray-300 dark:bg-gray-900" required>
            </div>
            <div>
                <label class="block mb-1 font-bold">سبب الاجتماع</label>
                <input type="text" name="reason" class="w-full rounded-lg border-gray-300 dark:bg-gray-900" required>
            </div>
            <div>
                <label class="block mb-1 font-bold">التفاصيل</label>
                <textarea name="details" rows="4" class="w-full rounded-lg border-gray-300 dark:bg-gray-900"></textarea>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg w-full font-bold">إرسال
                الطلب</button>
        </form>
    </div>
</section>
@endsection