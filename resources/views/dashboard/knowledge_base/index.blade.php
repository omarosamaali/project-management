@extends('layouts.app')

@section('title', 'بنك المعلومات - قائمة المقالات')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_services.index') }}" second="بنك المعلومات" />

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold dark:text-white">بنك المعلومات</h1>
            <p class="text-sm text-gray-500">تصفح المقالات حسب التصنيف أو ابحث عن معلومة محددة</p>
        </div>
        @if(Auth::user()->is_employee == 1 && Auth::user()->can_enter_knowledge_bank == 1 || Auth::user()->role ==
        'admin')
        <a href="{{ route('dashboard.kb.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-lg transition-all flex items-center gap-2 text-sm">
            <i class="fas fa-plus"></i> إضافة معلومة جديدة
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <a href="{{ route('dashboard.kb.index') }}"
            class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-xl border-2 transition-all {{ !request('category_id') ? 'border-blue-500 ring-2 ring-blue-100 dark:ring-blue-900/20' : 'border-transparent' }}">
            <div class="flex items-center justify-between mb-1">
                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-th-large text-gray-500"></i>
                </div>
                <span class="text-xs font-bold text-gray-400">الكل</span>
            </div>
            <h3 class="font-bold text-gray-800 dark:text-white">كافة التصنيفات</h3>
            <p class="text-[10px] text-gray-500 mt-4 italic">عرض جميع المقالات بدون تصفية</p>
        </a>

        @foreach($categories as $category)
        <a href="{{ route('dashboard.kb.index', ['category_id' => $category->id]) }}"
            class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-xl border-2 transition-all group {{ request('category_id') == $category->id ? 'border-blue-500 ring-2 ring-blue-100 dark:ring-blue-900/20' : 'border-transparent border-gray-100 dark:border-gray-700' }}">

            <div class="flex items-center justify-between mb-1">
                <div
                    class="w-10 h-10 {{ request('category_id') == $category->id ? 'bg-blue-600 text-white' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600' }} rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                </div>
                <span
                    class="text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full">
                    {{ $category->knowledges_count }} مقال
                </span>
            </div>

            <h3 class="font-bold text-gray-800 dark:text-white mb-2 line-clamp-1">{{ $category->title }}</h3>

            <div class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-700 space-y-1.5 text-[10px]">
                <div class="flex items-center justify-between text-gray-500">
                    <span class="flex items-center gap-1">
                        <i class="far fa-user text-[9px]"></i>
                        بواسطة: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $category->creator->name
                            ?? 'النظام' }}</span>
                    </span>
                </div>
                <div class="flex items-center justify-between text-gray-400">
                    <span class="flex items-center gap-1">
                        <i class="far fa-clock text-[9px]"></i>
                        {{ $category->updated_at->diffForHumans() }}
                    </span>
                    @if($category->updated_by)
                    <span class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[9px]">مُعدل</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div
        class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
            <h3 class="font-bold dark:text-white flex items-center gap-2">
                <i class="fas fa-list text-blue-500"></i>
                @if(request('category_id'))
                مقالات تصنيف: <span class="text-blue-600">{{ $categories->find(request('category_id'))->title ?? ''
                    }}</span>
                @else
                أحدث المقالات المضافة
                @endif
            </h3>
            @if(request('category_id'))
            <a href="{{ route('dashboard.kb.index') }}" class="text-xs text-black hover:underline">إلغاء التصفية</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-sm">
                    <tr>
                        <th class="p-4">المعلومة</th>
                        <th class="p-4 text-center">التصنيف</th>
                        <th class="p-4">بواسطة</th>
                        <th class="p-4 text-center">المرفقات</th>
                        <th class="p-4">تاريخ النشر</th>
                        <th class="p-4 text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-700">
                    @forelse($knowledges as $kb)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors text-sm">
                        <td class="p-4">
                            <div class="font-bold dark:text-white">{{ $kb->title }}</div>
                            <div class="text-xs text-gray-400 mt-1 line-clamp-1">
                                {{ Str::limit(strip_tags($kb->details), 60) }}
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <span
                                class="bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">
                                {{ $kb->category->title }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-7 h-7 bg-blue-100 dark:bg-gray-600 text-blue-600 dark:text-gray-300 rounded-full flex items-center justify-center text-[10px] font-bold">
                                    {{ Str::upper(Str::substr($kb->user->name, 0, 2)) }}
                                </div>
                                <span class="dark:text-gray-300">{{ $kb->user->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            @if($kb->attachments)
                            <a href="{{ asset('storage/' . $kb->attachments) }}" target="_blank"
                                class="text-green-500 hover:text-green-600 text-lg">
                                <i class="fas fa-file-download"></i>
                            </a>
                            @else
                            <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="p-4 dark:text-gray-400">
                            <span class="block">{{ $kb->created_at->format('Y-m-d') }}</span>
                            <span class="text-[10px] text-gray-500">{{ $kb->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex gap-2 justify-center">
                                {{-- @if() --}}
                                <a href="{{ route('dashboard.kb.edit', $kb->id) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded bg-blue-50 dark:bg-blue-900/30 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>

                                <form action="{{ route('dashboard.kb.destroy', $kb->id) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="w-8 h-8 flex items-center justify-center rounded bg-red-50 dark:bg-red-900/30 text-black hover:bg-black hover:text-white transition-all">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                {{-- @endif --}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <i class="fas fa-folder-open text-4xl text-gray-200 mb-4 block"></i>
                            <span class="text-gray-500">لا توجد مقالات ضمن هذا التصنيف حالياً</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $knowledges->links() }}</div>
</section>
@endsection