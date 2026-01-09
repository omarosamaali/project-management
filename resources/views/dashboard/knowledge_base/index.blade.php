@extends('layouts.app')

@section('title', 'بنك المعلومات - قائمة المقالات')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_services.index') }}" second="بنك المعلومات" />

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold dark:text-white">بنك المعلومات</h1>
        @if(Auth::user()->is_employee == 1 &&
        Auth::user()->can_enter_knowledge_bank == 1)
        <a href="{{ route('dashboard.kb.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold">
            <i class="fas fa-plus"></i> إضافة معلومة جديدة
        </a>
        @endif
    </div>

    <div
        class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <table class="w-full text-right">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                <tr>
                    <th class="p-4">العنوان</th>
                    <th class="p-4">التصنيف</th>
                    <th class="p-4">بواسطة</th>
                    <th class="p-4">المرفقات</th>
                    <th class="p-4">تاريخ الإضافة</th>
                    <th class="p-4">العمليات</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @foreach($knowledges as $kb)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                    <td class="p-4 font-bold dark:text-white">{{ $kb->title }}</td>
                    <td class="p-4">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                            {{ $kb->category->title }}
                        </span>
                    </td>
                    <td class="p-4 dark:text-gray-300">{{ $kb->user->name }}</td>
                    <td class="p-4 text-center">
                        @if($kb->attachments)
                        <a href="{{ asset('storage/' . $kb->attachments) }}" target="_blank" class="text-green-500">
                            <i class="fas fa-paperclip"></i>
                        </a>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 dark:text-gray-400">{{ $kb->created_at->format('Y-m-d') }}</td>
                    <td class="p-4 flex gap-3">
                        <a href="{{ route('dashboard.kb.edit', $kb->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <form action="{{ route('dashboard.kb.destroy', $kb->id) }}" method="POST"
                            onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $knowledges->links() }}</div>
</section>
@endsection