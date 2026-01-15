@extends('layouts.app')

@section('title', 'إدارة التصنيفات - بنك المعلومات')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.kb_categories.index') }}" second="إدارة التصنيفات" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 h-fit">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 dark:text-white">
                <i class="fas fa-plus-circle text-gray-"></i> إضافة تصنيف جديد
            </h3>
            <form action="{{ route('dashboard.kb_categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-gray-300">أيقونة التصنيف (FontAwesome)</label>
                    <input type="text" name="icon"
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white"
                        placeholder="fas fa-folder">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-gray-300">عنوان التصنيف</label>
                    <input type="text" name="title"
                        class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 dark:text-gray-300">الحالة</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white">
                        <option value="1">فعال</option>
                        <option value="0">غير فعال</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full bg-gray- bg-gray-700 text-white font-bold py-2 rounded-lg transition-all">حفظ
                    التصنيف</button>
            </form>
        </div>

        <div
            class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="p-4">الأيقونة</th>
                        <th class="p-4">العنوان</th>
                        <th class="p-4">الحالة</th>
                        <th class="p-4">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                        <td class="p-4 dark:text-white text-center"><i
                                class="{{ $category->icon }} text-xl text-blue-500"></i></td>
                        <td class="p-4 font-bold dark:text-white">{{ $category->title }}</td>
                        <td class="p-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold {{ $category->status ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $category->status ? 'فعال' : 'غير فعال' }}
                            </span>
                        </td>
                        <td class="p-4 flex gap-2">
                            <a href="{{ route('dashboard.kb_categories.edit', $category->id) }}"
                                class="text-blue-500 hover:text-gray-700"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('dashboard.kb_categories.destroy', $category->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد؟')">
                                @csrf @method('DELETE')
                                <button class="text-black"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection