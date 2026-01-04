@extends('layouts.app')
@section('title', 'أوقات العمل')
@section('content')
<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.work-times.index') }}" second="أوقات العمل" />

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        {{-- إحصائية الحضور --}}
        <div class="flex bg-black justify-between rounded-lg p-4">
            <div class="text-white">
                <h1 class="text-md font-bold">إجمالي السجلات</h1>
                <p class="text-2xl">{{ $allCount }} سجل</p>
            </div>
            <i class="fas fa-history text-white opacity-50 text-3xl"></i>
        </div>
        {{-- إحصائية الحضور --}}
        <div class="flex bg-green-700 justify-between rounded-lg p-4">
            <div class="text-white">
                <h1 class="text-md font-bold">تسجيلات الحضور</h1>
                <p class="text-2xl">{{ $attendanceCount }}</p>
            </div>
            <i class="fas fa-sign-in-alt text-white opacity-50 text-3xl"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden">
        <div class="flex justify-between items-center p-4">
            <form action="{{ route('dashboard.work-times.index') }}" method="GET" class="w-full md:w-1/2">
                <input type="text" name="search" placeholder="ابحث باسم الموظف..."
                    class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2">
            </form>
            <a href="{{ route('dashboard.work-times.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                + إضافة سجل وقت
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">الموظف</th>
                        <th class="px-4 py-3">البلد</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الوقت</th>
                        <th class="px-4 py-3">الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workTimes as $time)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $time->user->name }}</td>

                        <td class="px-4 py-3 flex items-center gap-2">
                            <img src="https://flagcdn.com/w40/{{ strtolower($time->country) }}.png"
                                class="w-6 h-auto rounded-sm shadow-sm" alt="{{ $time->country }}">
                            <span class="font-medium text-gray-700 dark:text-gray-200">
                                {{ $time->country_name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-800">{{ $time->type }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $time->date }}</td>
                        <td class="px-4 py-3">{{ $time->start_time }} - {{ $time->end_time ?? '--' }}</td>
                        <td class="px-4 py-3">{{ $time->notes }}</td>
                        <td class="px-4 py-3 text-center flex justify-center gap-2">
                            <a href="{{ route('dashboard.work-times.edit', $time->id) }}"
                                class="text-blue-600 hover:text-blue-900 bg-blue-100 p-2 rounded-lg transition" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                        
                            <form action="{{ route('dashboard.work-times.destroy', $time->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 p-2 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
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