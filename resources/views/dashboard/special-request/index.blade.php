@extends('layouts.app')

@section('title', 'طلباتى خاصة')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="#" second="طلباتى خاصة" />
    <div class="mx-auto w-full">
        <div
            class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg {{ Auth::user()->role != 'admin' ? '!mt-4' : '' }} overflow-hidden">

            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <!-- نموذج البحث -->
                    <form action="{{ route('dashboard.special-request.index') }}" method="GET"
                        class="flex items-center">
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
                                <a href="{{ route('dashboard.special-request.index') }}" title="مسح البحث">
                                    <i
                                        class="fa-solid fa-arrow-rotate-right w-5 h-5 text-gray-500 relative z-50 hover:text-gray-700 dark:hover:text-gray-300"></i>
                                </a>
                                @endif
                            </div>
                            <input value="{{ request()->search }}" type="text" id="search" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="إبحث بإسم العميل أو عنوان الطلب أو الوصف">
                        </div>
                    </form>

                    <!-- عرض نتائج البحث -->
                    @if(request()->search)
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-search ml-1"></i>
                        نتائج البحث عن: <span class="font-semibold text-gray-900 dark:text-white">"{{ request()->search
                            }}"</span>
                        <span class="mr-2">({{ $specialRequests->total() }} نتيجة)</span>
                    </div>
                    @endif
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row md:items-center justify-end !ml-0">
                    {{-- <a href="{{ route('special-request.index') }}"
                        class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 ml-2" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        إضافة طلب
                    </a> --}}
                </div>
            </div>

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

                @if(session('error'))
                <div class="mx-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-times-circle"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
                @endif

                {{-- Table --}}
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">رقم الطلب</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap"> العميل</th>
                            <th scope="col" class="px-4 py-3">العنوان</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">تاريخ الطلب</th>
                            <th scope="col" class="px-4 py-3">الوصف</th>
                            <th scope="col" class="px-4 py-3">الحالة</th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($specialRequests as $request)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td scope="row"
                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $request->order_number }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $request->user->name ?? 'غير محدد' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ Str::limit($request->title, 20) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $request->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ Str::limit($request->description, 40) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                {{ $request->status_name }}
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <a href="{{ route('dashboard.special-request.show', $request->id) }}"
                                    class="block py-2 px-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form
                                    action="{{ route('dashboard.special-request.destroy-special-request', $request) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')"
                                        class="block w-full text-right py-2 px-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                        title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <div class="p-4">
                    {{ $specialRequests->links() }}
                </div>
            </div>

        </div>
    </div>
</section>

@endsection