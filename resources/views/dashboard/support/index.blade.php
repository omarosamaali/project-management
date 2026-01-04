@extends('layouts.app')

@section('title', 'الدعم')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.support.index') }}" second="المشاريع" />
    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            @if(Auth::user()->role == 'admin')
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.support.index') }}" method="GET" class="flex items-center">
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
                                <a href="{{ route('dashboard.requests.index') }}">
                                    <i class="fa-solid fa-arrow-rotate-right w-5 h-5 text-gray-500 relative z-50"></i>
                                </a>
                                @endif
                            </div>
                            <input value="{{ request()->search }}" type="text" id="search" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="إبحث باسم العميل" required="">
                        </div>
                    </form>
                </div>
                {{-- <div class="visible w-full md:w-auto flex flex-col md:flex-row md:items-center justify-end !ml-0">
                    <a href="{{ route('dashboard.requests.create') }}"
                        class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 ml-2" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        إضافة طلب
                    </a>
                </div> --}}
            </div>
            @endif
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
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">رقم المشروع</th>
                            <th scope="col" class="px-4 py-3">العميل</th>
                            <th scope="col" class="px-4 py-3">اسم النظام</th>
                            <th scope="col" class="px-4 py-3">الحالة</th>
                            <th scope="col" class="px-4 py-3">رسائل غير مقروءة</th>
                            <th scope="col" class="px-4 py-3">آخر رسالة</th>
                            <th scope="col" class="px-4 py-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supports as $support)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $support->id }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $support->subject }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{ $support->user->name }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard.requests.show', $support->request_id) }}"
                                    class="text-blue-600 hover:underline">
                                    {{ $support->request->system->name_ar }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                @if($support->status == 'open')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                    {{ $support->status_label }}
                                </span>
                                @elseif($support->status == 'in_progress')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                    {{ $support->status_label }}
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                    {{ $support->status_label }}
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($support->unreadMessages->count() > 0)
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">
                                    {{ $support->unreadMessages->count() }} جديدة
                                </span>
                                @else
                                <span class="text-gray-500">لا يوجد</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @if($support->messages->count() > 0)
                                {{ $support->last_message_at?->diffForHumans() }}
                                @else
                                لا يوجد
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard.support.show', $support->id) }}"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs">
                                    فتح المحادثة
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center px-4 py-8 text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>لا توجد تذاكر دعم</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <div class="p-4">
                        {{-- {{ $support->links() }} --}}
                    </div>
            </div>

        </div>
    </div>
</section>

@endsection