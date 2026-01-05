@extends('layouts.app')
@section('content')
<div class="p-5 pb-0">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.logos.index') }}" second="شعارات الشركات" />
</div>

<section class="p-5 max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
        <h2 class="text-xl font-bold mb-4 dark:text-white text-right">إضافة شعار جديد</h2>
        {{-- displaye errors --}}
        @if ($errors->any())
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-300">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('dashboard.logos.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-4 text-right" dir="rtl">
            @csrf
            <div>
                <label class="block mb-1 font-bold dark:text-white">اسم الشركة (اختياري)</label>
                <input type="text" name="name"
                    class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
            </div>

            <div>
                <label class="block mb-1 font-bold dark:text-white">اختر صورة الشعار</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full text-gray-400 font-semibold text-sm bg-gray-50 dark:bg-gray-900 file:cursor-pointer cursor-pointer file:border-0 file:py-2.5 file:px-4 file:bg-gray-100 file:hover:bg-gray-200 file:text-gray-700 rounded-lg border border-gray-300"
                    required>
                <p class="text-xs text-gray-500 mt-1">يفضل أن تكون الخلفية شفافة (SVG or PNG)</p>
            </div>

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg w-full font-bold hover:bg-blue-700 transition-colors">
                حفظ الشعار
            </button>
        </form>
    </div>
</section>
@endsection