@extends('layouts.app')

@section('title', 'تصنيفات الدورات')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.course-categories.index') }}" second="تصنيفات الدورات" />

    <div class="bg-white shadow-md sm:rounded-lg overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 p-4">
            <form action="{{ route('dashboard.course-categories.index') }}" method="GET" class="w-full md:w-1/2">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="بحث عن تصنيف..."
                        class="w-full border border-gray-300 rounded-lg pl-3 pr-3 py-2 text-sm">
                </div>
            </form>
            <a href="{{ route('dashboard.course-categories.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg"
                style="background:#0D2444;">
                <i class="fas fa-plus"></i> إضافة تصنيف
            </a>
        </div>

        @if(session('success'))
        <div class="mx-4 mb-3 p-3 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mx-4 mb-3 p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3">التصنيف</th>
                        <th class="px-4 py-3">دورات</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="border-t">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $category->iconUrl() }}" alt=""
                                    class="w-10 h-10 rounded-full object-cover border border-gray-200 bg-slate-100">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $category->title_ar }}</p>
                                    <p class="text-xs text-slate-500 dir-ltr text-left">{{ $category->title_en }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $category->courses_count }}</td>
                        <td class="px-4 py-3">
                            @if($category->is_active)
                            <span class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs">نشط</span>
                            @else
                            <span class="text-slate-600 bg-slate-100 px-2 py-1 rounded text-xs">موقوف</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('dashboard.course-categories.edit', $category) }}"
                                    class="px-3 py-1.5 text-xs rounded-lg bg-blue-600 text-white">تعديل</a>
                                <form action="{{ route('dashboard.course-categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('حذف هذا التصنيف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-400">لا توجد تصنيفات بعد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $categories->links() }}</div>
    </div>
</section>
@endsection
