@extends('layouts.app')

@section('title', 'المشاريع')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.requests.index') }}"
        second="{{ url()->current() == route('dashboard.tasks.index') ? 'مهامي' : 'المشاريع' }}" />
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4">
        {{-- All Requests --}}
        <a href="{{ url()->current() }}" class="flex bg-black justify-between rounded-lg">
            <div class="p-4 pr-6 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white whitespace-nowrap">جميع المشاريع</h1>
                <p class="text-2xl flex items-center text-white">
                    {{ $allRequestsCount }} @if($requests->count() >= 10) طلب @else طلبات @endif
                </p>
            </div>

            <div class="p-5 bg-[#181818] rounded-lg">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-28 h-28 opacity-50" alt="">
            </div>
        </a>

        {{-- New Requests --}}
        <a href="{{ url()->current() }}?status=جديد" class="flex bg-[#333333] justify-between rounded-lg">
            <div class="p-4 pr-4 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white whitespace-nowrap">طلبات جديدة</h1>
                <p class="text-xl flex items-center text-white">
                    {{ $newRequestsCount }} @if($requests->count() >= 10) طلب @else طلبات
                    @endif
                </p>
            </div>

            <div class="p-5 bg-[#202020] rounded-lg">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-28 h-28 opacity-50" alt="">
            </div>
        </a>

        {{-- Under Process Requests --}}
        <a href="{{ url()->current() }}?status=تحت الاجراء" class="flex bg-[#595959] justify-between rounded-lg">
            <div class="p-4 pr-4 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white">طلبات تحت الإجراء</h1>
                <p class="text-xl flex items-center text-white">
                    {{ $underProcessRequestsCount }} @if($requests->count() >= 10) طلب @else
                    طلبات
                    @endif
                </p>
            </div>

            <div class="p-5 bg-[#4b4b4b] rounded-lg">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-28 h-28 opacity-50" alt="">
            </div>
        </a>

        {{-- Pending Requests --}}
        <a href="{{ url()->current() }}?status=معلقة" class="flex bg-[#808080] justify-between rounded-lg">
            <div class="p-4 pr-4 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white">طلبات معلقة</h1>
                <p class="text-xl flex items-center text-white">
                    {{ $pendingRequestsCount }} @if($requests->count() >= 10) طلب @else طلبات
                    @endif
                </p>
            </div>

            <div class="p-5 bg-[#6b6b6b] rounded-lg">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-28 h-28 opacity-50" alt="">
            </div>
        </a>

        {{-- Closed Requests --}}
        <a href="{{ url()->current() }}?status=منتهية" class="flex bg-[#999999] justify-between rounded-lg">
            <div class="p-4 pr-4 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white">طلبات منتهية</h1>
                <p class="text-xl flex items-center text-white">
                    {{ $closedRequestsCount }} @if($requests->count() >= 10) طلب @else طلبات
                    @endif
                </p>
            </div>

            <div class="p-5 bg-[#858585] rounded-lg">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-28 h-28 opacity-50" alt="">
            </div>
        </a>
    </div>

    <div class="mx-auto w-full">
        <div
            class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg {{ Auth::user()->role != 'admin' ? '!mt-4' : '' }} overflow-hidden">

            @if(Auth::user()->role == 'admin')
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.requests.index') }}" method="GET" class="flex items-center">
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
                                placeholder="إبحث بإسم الشريك" required="">
                        </div>
                    </form>
                </div>
                <div class="!ml-0">
                    <a href="{{ route('dashboard.requests.create-request') }}"
                        class="text-xs flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        إضافة طلب جديد ( للأدمن فقط )
                    </a>
                </div>
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
                {{-- Table --}}
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">رقم الطلب</th>
                            <th scope="col" class="px-4 py-3">العميل</th>
                            <th scope="col" class="px-4 py-3">النظام</th>
                            <th scope="col" class="px-4 py-3">تاريخ الطلب</th>
                            <th scope="col" class="px-4 py-3">الوصف</th>
                            <th scope="col" class="px-4 py-3">الحالة</th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>

                    @foreach($specialRequestss as $request)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $request->order_number }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $request->user->name ?? 'غير محدد' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-green-900 text-sm rounded-xl px-1.5 text-white">نظام خاص</span>
                            {{ Str::limit($request->title, 20) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $request->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3">
                            {{ Str::limit($request->description, 30) }}
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
                            @if(Auth::user()->role == 'admin')
                            <form action="{{ route('dashboard.special-request.destroy-special-request', $request) }}"
                                method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')"
                                    class="block w-full text-right py-2 px-2 text-sm text-black hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    @if(Auth::user()->role == 'partner')
                    @foreach ($specialRequests as $specialRequest)
                    <tbody>
                        <tr class="border-b dark:border-gray-700">
                            <td scope="row"
                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $specialRequest->request->order_number }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $specialRequest->partner?->name ?? 'غير محدد' }} </td>
                            <td class="px-4 py-3">
                                <span class="bg-gray-900 text-white text-sm rounded-xl px-1.5">
                                    {{ $specialRequest->request?->is_project ? 'مشروع' : 'نظام جاهز' }}
                                </span>&nbsp;{{ $specialRequest->request?->system->name_ar }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $specialRequest->created_at->format('Y-m-d') }}
                            </td>
                            <td class="text-right px-4 py-3">
                                {{ Str::limit($specialRequest->request?->system->description_ar, 30) }}
                            </td>
                            <td class="text-right px-4 py-3">
                                طلب خاص
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <a href="{{ route('dashboard.requests.show', $specialRequest->request->id) }}"
                                    class="block py-2 px-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->role == 'admin')
                                <form action="{{ route('dashboard.requests.destroy', $specialRequest->id) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')"
                                        class="block w-full text-right py-2 px-2 text-sm text-black hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                    @endforeach
                    @endif

                    @foreach($requests as $request)
                    <tbody>
                        <tr class="border-b dark:border-gray-700">
                            <td scope="row"
                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $request->order_number }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $request->client->name }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-gray-900 text-sm rounded-xl px-1.5 text-white">نظام جاهز</span> {{
                                $request->system->name_ar }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $request->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ Str::limit($request->system->description_ar, 40) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $request->status_label }}
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <a href="{{ route('dashboard.requests.show', $request->id) }}"
                                    class="block py-2 px-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->role == 'admin')
                                <form action="{{ route('dashboard.requests.destroy', $request->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')"
                                        class="block w-full text-right py-2 px-2 text-sm text-black hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                    @endforeach
                </table>
                <div class="p-4">
                    {{ $requests->links() }}
                </div>
            </div>

        </div>
    </div>
</section>

@endsection