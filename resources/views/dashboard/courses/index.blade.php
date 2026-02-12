@extends('layouts.app')

@section('title', 'الدورات')

@section('content')
<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.courses.index') }}" second="الدورات" />
    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.courses.index') }}" method="GET" class="flex items-center">
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
                                <a href="{{ route('dashboard.courses.index') }}">
                                    <i class="fa-solid fa-arrow-rotate-right w-5 h-5 text-gray-500 relative z-50"></i>
                                </a>
                                @endif
                            </div>
                            <input value="{{ request()->search }}" type="text" id="search" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="بحث" required="">
                        </div>
                    </form>
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row md:items-center justify-end !ml-0">
                    <a href="{{ route('dashboard.courses.create') }}"
                        class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 ml-2" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        إضافة دورة
                    </a>
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

                @foreach ($errors->all() as $error)
                <div class="text-red-600">{{ $error }}</div>
                @endforeach

                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">الإسم</th>
                            <th scope="col" class="px-4 py-3">السعر</th>
                            <th scope="col" class="px-4 py-3">عدد ايام الدورة</th>
                            <th scope="col" class="px-4 py-3">عدد العملاء</th>
                            <th scope="col" class="px-4 py-3">الأرباح</th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($courses as $course)
                        <tr
                            class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <th scope="row"
                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->iteration }}
                            </th>
                            <td class="px-4 py-3">
                                @if($course->evorq_onwer != 0)
                                <span class="text-xs bg-green-700 text-green-200 rounded-xl px-1">
                                    {{ $course->onwer_system }}
                                </span>
                                @endif
                                {{ Str::limit($course->name_ar, 20) }}
                            </td>
                            <td class="px-4 py-3 flex items-center">
                                <div class="flex items-center gap-1">
                                    {{ number_format($course->price) }}
                                    <x-drhm-icon width="12" height="14" />
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $course->count_days }}
                                <span class="text-xs">يوم</span>
                            </td>
                            <td class="px-4 py-3 font-medium text-blue-600">
                            {{ $course->students?->count() ?? 0 }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1 text-[12px]">
                                    {{ number_format($course->students?->sum('pivot.price_paid') ?? 0, 2) }}
                                    <x-drhm-icon  width="12" height="14" />
                                </div>
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <a href="{{ route('dashboard.courses.show', $course) }}"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="عرض التفاصيل والتحقق">
                                    <i class="fas fa-id-card text-lg"></i>
                                </a>
                                
                                {{-- زر التعديل --}}
                                <a href="{{ route('dashboard.courses.edit', $course->id) }}"
                                    class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                    <i class="fas fa-user-edit"></i>
                                </a>
                                
                                {{-- زر الحذف --}}
                                <form action="{{ route('dashboard.courses.destroy', $course->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا الكورس.')"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7"
                                class="text-center px-4 py-8 font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                لا يوجد دورات لعرضها حالياً
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
