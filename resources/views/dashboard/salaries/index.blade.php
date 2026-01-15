@extends('layouts.app')

@section('title', 'رواتب الموظفين')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.salaries.index') }}" second="رواتب الموظفين" />

    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

            {{-- Header: Search & Add Button --}}
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.salaries.index') }}" method="GET" class="flex items-center">
                        <label for="search" class="sr-only">بحث</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                @if(request()->search == null)
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                                @else
                                <a href="{{ route('dashboard.salaries.index') }}">
                                    <i class="fa-solid fa-arrow-rotate-right w-5 h-5 text-gray-500 relative z-50"></i>
                                </a>
                                @endif
                            </div>
                            <input value="{{ request()->search }}" type="text" id="search" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="بحث باسم الموظف..." required="">
                        </div>
                    </form>
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row md:items-center justify-end !ml-0">
                    <a href="{{ route('dashboard.salaries.create') }}"
                        class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 ml-2" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        إضافة سجل راتب
                    </a>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="overflow-x-auto">
                @if(session('success'))
                <div class="mx-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                {{-- Table --}}
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">الموظف</th>
                            <th scope="col" class="px-4 py-3">الفترة (شهر/سنة)</th>
                            <th scope="col" class="px-4 py-3">الراتب المستحق</th>
                            <th scope="col" class="px-4 py-3">المرفق</th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($salaries->isNotEmpty())
                        @foreach($salaries as $salary)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $salary->user->name }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $salary->user->country_name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ $salary->month }} / {{ $salary->year }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-green-600">
                                {{ number_format($salary->total_due, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                @if($salary->attachment)
                                <a href="{{ asset('storage/' . $salary->attachment) }}" target="_blank">
                                    <img class="w-10 h-10 rounded shadow-sm border"
                                        src="{{ asset('storage/' . $salary->attachment) }}" alt="فاتورة">
                                </a>
                                @else
                                <span class="text-gray-400 text-xs">لا يوجد</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end space-x-reverse space-x-2">
                                <a href="{{ route('dashboard.salaries.show', $salary->id) }}" title="عرض"
                                    class="p-2 text-gray-600 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.salaries.edit', $salary->id) }}" title="تعديل"
                                    class="p-2 text-gray-600 hover:text-yellow-500 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('dashboard.salaries.destroy', $salary->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('هل أنت متأكد من حذف سجل الراتب؟')"
                                        title="حذف" class="p-2 text-gray-600 hover:text-black transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6"
                                class="text-center px-4 py-8 font-medium text-gray-500 bg-gray-50 dark:bg-gray-800">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                    <span>لا توجد سجلات رواتب لعرضها حالياً.</span>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="p-4 border-t dark:border-gray-700">
                    {{ $salaries->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

@endsection