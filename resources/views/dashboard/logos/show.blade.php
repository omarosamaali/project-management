@extends('layouts.app')

@section('content')
<div class="p-5 pb-0">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.logos.index') }}" second="تفاصيل الشعار" />
</div>

<section class="p-5 max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 text-right"
        dir="rtl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold dark:text-white">تفاصيل الشعار</h2>
            <a href="{{ route('dashboard.logos.index') }}"
                class="text-sm text-gray-500 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left ml-1"></i> العودة للقائمة
            </a>
        </div>

        {{-- منطقة معاينة الشعار --}}
        <div
            class="bg-gray-50 dark:bg-gray-900 rounded-xl p-10 flex justify-center items-center mb-6 border-2 border-dashed border-gray-200 dark:border-gray-700">
            <img src="{{ asset('storage/' . $logo->image_path) }}" class="max-h-48 w-auto drop-shadow-md"
                alt="{{ $logo->name }}">
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                <span class="font-bold dark:text-gray-300">اسم الشركة:</span>
                <span class="text-gray-600 dark:text-gray-400">{{ $logo->name ?? 'غير محدد' }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                <span class="font-bold dark:text-gray-300">مسار الملف:</span>
                <span class="text-xs text-gray-500 font-mono">{{ $logo->image_path }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                <span class="font-bold dark:text-gray-300">تاريخ الرفع:</span>
                <span class="text-gray-600 dark:text-gray-400">{{ $logo->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="mt-8 flex gap-3">
            <form action="{{ route('dashboard.logos.destroy', $logo->id) }}" method="POST" class="flex-1"
                onsubmit="return confirm('سيتم حذف الشعار نهائياً، هل أنت متأكد؟')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full bg-red-100 text-red-600 hover:bg-red-600 hover:text-white py-2 rounded-lg font-bold transition-all">
                    حذف الشعار
                </button>
            </form>
        </div>
    </div>
</section>
@endsection