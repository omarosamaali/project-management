@extends('layouts.app')

@section('title', 'قائمة ملاحظات الإدارة')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="#" second="ملاحظات الإدارة" third="كل الملاحظات" />

    <div class="mx-auto max-w-6xl w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden border">
            <div
                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 border-b">
                <div class="w-full md:w-1/2">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">سجل الملاحظات الإدارية</h2>
                </div>
                <div
                    class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <a href="{{ route('dashboard.admin_remarks.create') }}"
                        class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">
                        <i class="fas fa-plus ml-2"></i> إضافة ملاحظة جديدة
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">الموظف</th>
                            <th class="px-4 py-3">الملاحظة</th>
                            <th class="px-4 py-3 text-center">الصورة</th>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3 text-left">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remarks as $remark)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $remark->user->name }}
                            </td>
                            <td class="px-4 py-3 max-w-[200px] truncate">
                                {{ $remark->details }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($remark->image)
                                <img src="{{ asset('storage/' . $remark->image) }}"
                                    class="w-10 h-10 rounded-full border mx-auto">
                                @else
                                <span class="text-gray-300">---</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $remark->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-left">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('dashboard.admin_remarks.show', $remark) }}"
                                        class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('dashboard.admin_remarks.edit', $remark) }}"
                                        class="text-green-600 hover:text-green-800"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('dashboard.admin_remarks.destroy', $remark) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد؟')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection