@extends('layouts.app')
@section('title', 'العطلات')
@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="العطلات" />

    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between p-4 gap-3">
                <form action="{{ route('dashboard.holidays.index') }}" method="GET" class="w-full md:w-1/2">
                    <input value="{{ request('search') }}" type="text" name="search"
                        class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="بحث باسم العطلة...">
                </form>
                <a href="{{ route('dashboard.holidays.create') }}"
                    class="text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-4 py-2 whitespace-nowrap">
                    <i class="fas fa-plus ml-1"></i> إضافة عطلة
                </a>
            </div>

            @if(session('success'))
            <div class="mx-4 mb-2 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">الاسم</th>
                            <th class="px-4 py-3">النوع</th>
                            <th class="px-4 py-3">الفترة</th>
                            <th class="px-4 py-3">الراتب</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $holiday)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $holiday->name }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded {{ $holiday->type === 'general' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $holiday->typeLabel() }}
                                    @if($holiday->type === 'private')
                                    <span class="text-gray-500">({{ $holiday->employees_count }} موظف)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $holiday->start_date->format('Y-m-d') }}
                                <span class="text-gray-400">→</span>
                                {{ $holiday->end_date->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3">{{ $holiday->salaryStatusLabel() }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded {{ $holiday->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $holiday->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('dashboard.holidays.edit', $holiday) }}"
                                    class="text-blue-600 hover:underline text-xs ml-2">تعديل</a>
                                <form action="{{ route('dashboard.holidays.destroy', $holiday) }}" method="POST" class="inline"
                                    onsubmit="return confirm('حذف هذه العطلة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center px-4 py-8 text-gray-500">لا توجد عطلات مسجّلة.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $holidays->links() }}</div>
            </div>
        </div>
    </div>
</section>
@endsection
