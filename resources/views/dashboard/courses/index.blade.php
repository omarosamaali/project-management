@extends('layouts.app')

@section('title', 'الدورات')

@section('content')
<section class="!pl-0 p-3 sm:p-5">
    @unless(auth()->user()->usesAcademyShell())
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.courses.index') }}" second="الدورات" />
    @else
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">إدارة الدورات</h1>
            <p class="text-sm text-slate-500 mt-1">أنشئ وعدّل دوراتك من هنا</p>
        </div>
        <a href="{{ route('dashboard.courses.create') }}"
            class="ac-btn ac-btn-primary">
            <i class="fas fa-plus"></i> إضافة دورة
        </a>
    </div>
    @endunless

    <div class="mx-auto w-full">
        @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center gap-2">
            <i class="fas fa-times-circle"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        @endif

        <form action="{{ route('dashboard.courses.index') }}" method="GET" class="mb-5">
            <div class="relative max-w-xl">
                <input value="{{ request()->search }}" type="text" name="search"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-10 text-sm"
                    placeholder="بحث في الدورات...">
                <button type="submit" class="absolute inset-y-0 left-0 px-3 text-slate-400">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        @if(auth()->user()->usesAcademyShell())
        <style>
            .course-manage-card {
                display: flex;
                flex-direction: column;
                height: 100%;
                min-height: 100%;
            }
            .course-manage-media {
                aspect-ratio: 16 / 9;
                width: 100%;
                overflow: hidden;
                background: #f1f5f9;
                flex-shrink: 0;
            }
            .course-manage-media img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
                display: block;
            }
            .course-manage-body {
                display: flex;
                flex-direction: column;
                flex: 1 1 auto;
                gap: .5rem;
                padding: 1rem;
                min-height: 0;
            }
            .course-manage-title {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                min-height: 2.6em;
                line-height: 1.3;
            }
            .course-manage-meta {
                min-height: 1.25rem;
            }
        </style>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 items-stretch">
            @forelse ($courses as $course)
            <article class="course-manage-card bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="course-manage-media">
                    <img src="{{ $course->main_image ? asset('storage/'.$course->main_image) : asset('assets/images/logo.webp') }}"
                        alt="{{ $course->name_ar }}">
                </div>
                <div class="course-manage-body">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="course-manage-title font-extrabold text-slate-900">{{ $course->name_ar }}</h3>
                        <span class="shrink-0 text-[11px] font-bold px-2 py-1 rounded-lg {{ $course->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $course->status === 'active' ? 'نشطة' : 'غير نشطة' }}
                        </span>
                    </div>
                    <p class="course-manage-meta text-xs text-slate-500">
                        {{ match($course->location_type) { 'online' => 'أونلاين', 'recorded' => 'مسجّلة', default => 'حضوري' } }}
                        · <span class="inline-flex items-center gap-1">{{ number_format((float)$course->price, 0) }} <x-drhm-icon width="12" height="14" /></span>
                        · {{ $course->students?->count() ?? 0 }} مشترك
                    </p>
                    <div class="ac-card-actions">
                        <a href="{{ route('dashboard.courses.show', $course) }}"
                            class="ac-btn ac-btn-primary ac-btn-sm">عرض</a>
                        <a href="{{ route('dashboard.courses.edit', $course->id) }}"
                            class="ac-btn ac-btn-ghost ac-btn-sm">تعديل</a>
                        <form action="{{ route('dashboard.courses.destroy', $course->id) }}" method="POST"
                            onsubmit="return confirm('هل أنت متأكد من حذف هذا الكورس.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ac-btn ac-btn-danger ac-btn-sm">
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-14 bg-white border border-dashed border-slate-200 rounded-2xl text-slate-400">
                لا يوجد دورات لعرضها حالياً
                <div class="mt-3">
                    <a href="{{ route('dashboard.courses.create') }}" class="ac-btn ac-btn-primary" style="background-color:#0D2444;color:#fff;">إضافة دورة</a>
                </div>
            </div>
            @endforelse
        </div>
        @if($courses->hasPages())
        <div class="ac-pagination mt-6">{{ $courses->links() }}</div>
        @endif
        @else
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
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
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">الإسم</th>
                            <th scope="col" class="px-4 py-3">المحاضر</th>
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
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $course->trainer?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 flex items-center">
                                <div class="flex items-center gap-1">
                                    {{ number_format($course->price) }}
                                    <x-drhm-icon width="12" height="14" />
                                </div>
                            </td>
                           <td class="px-4 py-3 text-sm text-gray-600">
                            @if($course->actual_course_days > 0)
                            {{ $course->actual_course_days }}
                            <span class="text-xs">يوم</span>
                            @else
                            @php
                            $start = \Carbon\Carbon::parse($course->start_date);
                            $end = \Carbon\Carbon::parse($course->end_date);
                            $hours = $start->diffInHours($end);
                            $minutes = $start->diffInMinutes($end) % 60;
                            @endphp
                            <div class="flex flex-col">
                                <span class="font-semibold text-blue-600">
                                    @if($hours > 0)
                                    {{ $hours }} ساعة
                                    @endif
                                    @if($minutes > 0)
                                    {{ $minutes }} دقيقة
                                    @endif
                                    @if($hours == 0 && $minutes == 0)
                                    أقل من دقيقة
                                    @endif
                                </span>
                                <span class="text-[10px] text-gray-400">(دورة يوم واحد)</span>
                            </div>
                            @endif
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
                                <a href="{{ route('dashboard.courses.edit', $course->id) }}"
                                    class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                    <i class="fas fa-user-edit"></i>
                                </a>
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
                            <td colspan="8"
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
        @endif
    </div>
</section>
@endsection
