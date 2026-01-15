@extends('layouts.app')

@section('content')
<section class="p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.logos.index') }}" second="إدارة الشعارات" />

    <div class="flex justify-between items-center mb-6 text-right" dir="rtl">
        <h1 class="text-2xl font-bold dark:text-white">شعارات الشركات (Marquee)</h1>
        <a href="{{ route('dashboard.logos.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            إضافة شعار جديد
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
        <table class="w-full text-right" dir="rtl">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="p-4 text-center">الصورة</th>
                    <th class="p-4">اسم الشركة</th>
                    <th class="p-4">تاريخ الإضافة</th>
                    <th class="p-4 text-center">العمليات</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($logos as $logo)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                    <td class="p-4">
                        <div class="flex justify-center">
                            <img src="{{ asset('storage/' . $logo->image_path) }}"
                                class="h-12 w-auto object-contain p-1 bg-gray-100 dark:bg-gray-200 rounded"
                                alt="{{ $logo->name }}">
                        </div>
                    </td>
                    <td class="p-4 dark:text-gray-300 font-medium">{{ $logo->name ?? 'بدون اسم' }}</td>
                    <td class="p-4 dark:text-gray-500 text-sm">{{ $logo->created_at->format('Y-m-d') }}</td>
                    <td class="p-4">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('dashboard.logos.show', $logo->id) }}"
                                class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <a href="{{ route('dashboard.logos.edit', $logo->id) }}" class="text-amber-600 hover:text-amber-800">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('dashboard.logos.destroy', $logo->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-black hover:text-red-800 mr-3">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-400 italic">لا توجد شعارات مضافة حالياً.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection