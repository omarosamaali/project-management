@extends('layouts.app')

@section('title', 'رسائل الواتساب')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="رسائل الواتساب" />

    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

            {{-- بحث (اختياري للأدمن) --}}
            @if(Auth::user()->role === 'admin')
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.technical_support.index') }}" method="GET" class="flex items-center">
                        <label for="search" class="sr-only">بحث</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                @if(request()->search == null)
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                                @else
                                <a href="{{ route('dashboard.technical_support.index') }}">
                                    <i class="fa-solid fa-arrow-rotate-right w-5 h-5 text-gray-500 relative z-50"></i>
                                </a>
                                @endif
                            </div>
                            <input value="{{ request()->search }}" type="text" id="search" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="ابحث برقم الهاتف أو اسم المستخدم" />
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="overflow-x-auto">
                @if(session('success'))
                <div class="mx-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mx-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200">
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
                            <th scope="col" class="px-4 py-3">المستخدم</th>
                            <th scope="col" class="px-4 py-3">رقم الهاتف</th>
                            <th scope="col" class="px-4 py-3">المحتوى</th>
                            <th scope="col" class="px-4 py-3">النوع</th>
                            <th scope="col" class="px-4 py-3">الحالة</th>
                            <th scope="col" class="px-4 py-3">تاريخ الإرسال</th>
                            @if(Auth::user()->role === 'admin')
                            <th scope="col" class="px-4 py-3">الإجراءات</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $message->id }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $message->user ? $message->user->name : 'غير مسجل' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $message->phone }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="max-w-xs truncate" title="{{ $message->message_content }}">
                                    {{ Str::limit($message->message_content ?? '—', 60) }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($message->type === 'outgoing')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">صادر</span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">وارد</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClass = match ($message->status) {
                                        'sent'    => 'bg-green-100 text-green-800',
                                        'delivered' => 'bg-emerald-100 text-emerald-800',
                                        'failed'  => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        default   => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-3 py-1 {{ $statusClass }} rounded-full text-xs">
                                    {{ $message->status === 'sent' ? 'تم الإرسال' : ($message->status === 'failed' ? 'فشل' : $message->status) }}
                                </span>
                                @if($message->error_message)
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-1" title="{{ $message->error_message }}"></i>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $message->sent_at ? $message->sent_at->diffForHumans() : '—' }}
                            </td>
                            @if(Auth::user()->role === 'admin')
                            <td class="px-4 py-3">
                                <button class="text-blue-600 hover:text-blue-800 text-xs" onclick="showDetails({{ $message->id }})">
                                    تفاصيل
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'admin' ? 8 : 7 }}" class="text-center px-4 py-8 text-gray-500">
                                <i class="fas fa-comment-slash text-4xl mb-2"></i>
                                <p>لا توجد رسائل واتساب حتى الآن</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($messages->hasPages())
                <div class="p-4">
                    {{ $messages->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection