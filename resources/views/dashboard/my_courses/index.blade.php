@extends('layouts.app')

@section('title', 'دوراتي التدريبية')

@section('content')

<section class="p-3 sm:p-5">
    @unless(auth()->user()->usesAcademyShell())
    {{-- Breadcrumb --}}
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_courses.index') }}" second="دوراتي التدريبية" />
    @else
    <div class="mb-5">
        <h1 class="text-xl font-extrabold text-slate-900">دوراتي التدريبية</h1>
        <p class="text-sm text-slate-500 mt-1">تابع اشتراكاتك وتقدّمك في الدورات</p>
    </div>
    @endunless

    @if(auth()->user()->usesAcademyShell())
    <style>
        .ac-filter-strip { display: grid; gap: .7rem; grid-template-columns: repeat(2, minmax(0,1fr)); margin-bottom: 1.25rem; }
        @media (min-width: 768px) { .ac-filter-strip { grid-template-columns: repeat(4, minmax(0,1fr)); } }
        .ac-filter-chip {
            display: flex; flex-direction: column; gap: .35rem;
            position: relative;
            background: #fff; border-radius: 1.15rem; padding: 1rem 1.05rem;
            text-decoration: none; color: inherit;
            box-shadow: 0 8px 22px rgba(6,21,37,.06);
            border: 1px solid transparent;
            border-inline-start: 4px solid #0b8f7f;
            transition: transform .2s, box-shadow .2s, background .2s, border-color .2s;
        }
        .ac-filter-chip:nth-child(2) { border-inline-start-color: #d4a017; }
        .ac-filter-chip:nth-child(3) { border-inline-start-color: #0e3a5c; }
        .ac-filter-chip:nth-child(4) { border-inline-start-color: #e85d4c; }
        .ac-filter-chip:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(6,21,37,.1); }
        .ac-filter-chip.is-on {
            background: linear-gradient(145deg, #f3fbf9 0%, #ffffff 55%);
            border-color: rgba(11, 143, 127, .22);
            border-inline-start-width: 5px;
            box-shadow:
                0 12px 28px rgba(11, 143, 127, .14),
                0 2px 8px rgba(6, 21, 37, .04);
            transform: translateY(-1px);
        }
        .ac-filter-chip:nth-child(2).is-on {
            background: linear-gradient(145deg, #fff9eb 0%, #ffffff 55%);
            border-color: rgba(212, 160, 23, .28);
            box-shadow: 0 12px 28px rgba(212, 160, 23, .16), 0 2px 8px rgba(6, 21, 37, .04);
        }
        .ac-filter-chip:nth-child(3).is-on {
            background: linear-gradient(145deg, #f0f6fb 0%, #ffffff 55%);
            border-color: rgba(14, 58, 92, .22);
            box-shadow: 0 12px 28px rgba(14, 58, 92, .14), 0 2px 8px rgba(6, 21, 37, .04);
        }
        .ac-filter-chip:nth-child(4).is-on {
            background: linear-gradient(145deg, #fff4f2 0%, #ffffff 55%);
            border-color: rgba(232, 93, 76, .24);
            box-shadow: 0 12px 28px rgba(232, 93, 76, .14), 0 2px 8px rgba(6, 21, 37, .04);
        }
        .ac-filter-chip.is-on::after {
            content: '';
            position: absolute;
            inset-block-start: .7rem;
            inset-inline-end: .75rem;
            width: .55rem; height: .55rem;
            border-radius: 999px;
            background: currentColor;
            color: #0b8f7f;
            box-shadow: 0 0 0 4px rgba(11, 143, 127, .15);
        }
        .ac-filter-chip:nth-child(2).is-on::after { color: #d4a017; box-shadow: 0 0 0 4px rgba(212, 160, 23, .18); }
        .ac-filter-chip:nth-child(3).is-on::after { color: #0e3a5c; box-shadow: 0 0 0 4px rgba(14, 58, 92, .15); }
        .ac-filter-chip:nth-child(4).is-on::after { color: #e85d4c; box-shadow: 0 0 0 4px rgba(232, 93, 76, .16); }
        .ac-filter-chip .lbl { font-size: .75rem; font-weight: 700; color: #5a6d82; }
        .ac-filter-chip.is-on .lbl { color: #061525; }
        .ac-filter-chip .val { font-size: 1.45rem; font-weight: 800; color: #061525; font-family: 'Noto Kufi Arabic', sans-serif; }
    </style>
    <div class="ac-filter-strip">
        <a href="{{ route('dashboard.my_courses.index') }}" class="ac-filter-chip {{ !$filter ? 'is-on' : '' }}">
            <span class="lbl">إجمالي الدورات</span>
            <span class="val">{{ $totalCourses ?? $myPayments->count() }}</span>
        </a>
        <a href="{{ route('dashboard.my_courses.index') }}?filter=active" class="ac-filter-chip {{ $filter === 'active' ? 'is-on' : '' }}">
            <span class="lbl">نشطة</span>
            <span class="val">{{ $activeCourses }}</span>
        </a>
        <a href="{{ route('dashboard.my_courses.index') }}?filter=upcoming" class="ac-filter-chip {{ $filter === 'upcoming' ? 'is-on' : '' }}">
            <span class="lbl">قادمة</span>
            <span class="val">{{ $upcomingCourses }}</span>
        </a>
        <a href="{{ route('dashboard.my_courses.index') }}?filter=ended" class="ac-filter-chip {{ $filter === 'ended' ? 'is-on' : '' }}">
            <span class="lbl">منتهية</span>
            <span class="val">{{ $endedCourses }}</span>
        </a>
    </div>
    @else
    {{-- كروت فيلتر الدورات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('dashboard.my_courses.index') }}"
            class="flex bg-black justify-between rounded-lg overflow-hidden transition-transform hover:-translate-y-1 {{ !$filter ? 'ring-4 ring-white ring-offset-2 ring-offset-gray-100' : '' }}">
            <div class="p-4 pr-6 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white whitespace-nowrap flex items-center gap-2">
                    @if(!$filter)<i class="fas fa-check-circle text-yellow-400 text-xs"></i>@endif
                    إجمالي الدورات
                </h1>
                <p class="text-2xl flex items-center text-white">
                    {{ $totalCourses ?? $myPayments->count() }} <span class="text-sm mr-1">دورة</span>
                </p>
            </div>
            <div class="p-5 bg-[#181818]">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-30" alt="">
            </div>
        </a>
        <a href="{{ route('dashboard.my_courses.index') }}?filter=active"
            class="flex bg-green-700 justify-between rounded-lg overflow-hidden transition-transform hover:-translate-y-1 {{ $filter === 'active' ? 'ring-4 ring-white ring-offset-2 ring-offset-gray-100' : '' }}">
            <div class="p-4 pr-6 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white whitespace-nowrap flex items-center gap-2">
                    @if($filter === 'active')<i class="fas fa-check-circle text-yellow-400 text-xs"></i>@endif
                    دورات نشطة
                </h1>
                <p class="text-2xl text-white">{{ $activeCourses }} <span class="text-sm">دورة</span></p>
            </div>
            <div class="p-5 bg-green-800">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-30" alt="">
            </div>
        </a>
        <a href="{{ route('dashboard.my_courses.index') }}?filter=upcoming"
            class="flex bg-blue-600 justify-between rounded-lg overflow-hidden transition-transform hover:-translate-y-1 {{ $filter === 'upcoming' ? 'ring-4 ring-white ring-offset-2 ring-offset-gray-100' : '' }}">
            <div class="p-4 pr-6 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white whitespace-nowrap flex items-center gap-2">
                    @if($filter === 'upcoming')<i class="fas fa-check-circle text-yellow-400 text-xs"></i>@endif
                    دورات قادمة
                </h1>
                <p class="text-2xl text-white">{{ $upcomingCourses }} <span class="text-sm">دورة</span></p>
            </div>
            <div class="p-5 bg-blue-700">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-30" alt="">
            </div>
        </a>
        <a href="{{ route('dashboard.my_courses.index') }}?filter=ended"
            class="flex bg-[#808080] justify-between rounded-lg overflow-hidden transition-transform hover:-translate-y-1 {{ $filter === 'ended' ? 'ring-4 ring-white ring-offset-2 ring-offset-gray-100' : '' }}">
            <div class="p-4 pr-6 flex flex-col justify-between">
                <h1 class="text-md font-bold text-white whitespace-nowrap flex items-center gap-2">
                    @if($filter === 'ended')<i class="fas fa-check-circle text-yellow-400 text-xs"></i>@endif
                    دورات منتهية
                </h1>
                <p class="text-2xl text-white">{{ $endedCourses }} <span class="text-sm">دورة</span></p>
            </div>
            <div class="p-5 bg-[#6b6b6b]">
                <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-30" alt="">
            </div>
        </a>
    </div>
    @endif

    {{-- شارة الفلتر النشط --}}
    @if($filter || ($type ?? null))
    <div class="mb-4 flex items-center gap-2 flex-wrap">
        <span class="text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-filter ml-1"></i>
            عرض:
            <strong>
                @if($filter)
                    {{ ['active' => 'الدورات النشطة', 'upcoming' => 'الدورات القادمة', 'ended' => 'الدورات المنتهية'][$filter] ?? '' }}
                @endif
                @if(($type ?? null) === 'private')
                    {{ $filter ? ' · ' : '' }}الدورات الخاصة
                @endif
            </strong>
        </span>
        <a href="{{ route('dashboard.my_courses.index') }}"
            class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1">
            <i class="fas fa-times"></i> إلغاء الفلتر
        </a>
    </div>
    @endif

    <div class="mx-auto w-full">
            @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

        @if(auth()->user()->usesAcademyShell())
        {{-- Academy shell: responsive card grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($myPayments as $payment)
            @php
                $course = $payment->course;
                $now = \Carbon\Carbon::now();
                $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date) : null;
                $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date) : null;
                $isLiveMeetingCourse = in_array($course->location_type, ['online', 'private'], true);
                $showLink = $isLiveMeetingCourse
                    && $course->hasLiveMeetingAccess()
                    && $startDate && $endDate
                    && $now->greaterThanOrEqualTo($startDate->copy()->subMinutes(30))
                    && $now->lessThanOrEqualTo($endDate);
                $isFinished = $payment->isCourseEndedForLearner($now);
                $pathCompletion = $course->isRecorded() ? $course->pathCompletionForUser($payment->user_id) : null;
                $canCertificate = $course && $course->userCanGetCertificate($payment->user_id);
                $runningDayExam = $course && $course->usesDayExams() ? $course->runningDayExam() : null;
                $needsRating = $course && $course->userNeedsRating($payment->user_id);
                $typeLabel = match($course->location_type) {
                    'online' => __('messages.academy_type_online'),
                    'recorded' => __('messages.academy_type_recorded'),
                    'on_site' => __('messages.academy_type_onsite'),
                    'private' => 'خاصة',
                    default => $course->location_type ?: '—',
                };
            @endphp
            <article class="bg-white border border-slate-200 rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-md transition">
                <div class="relative ac-media-wm-host">
                    <img src="{{ $course->main_image ? asset('storage/'.$course->main_image) : asset('assets/images/logo.webp') }}"
                        alt="" class="w-full aspect-video object-cover bg-slate-100">
                    <x-media-watermark brand="academy" size="sm" />
                    <span class="absolute top-3 right-3 text-[11px] font-bold px-2.5 py-1 rounded-full text-white z-[4]
                        {{ $course->location_type === 'online' ? 'bg-blue-600' : ($course->location_type === 'recorded' ? 'bg-violet-600' : 'bg-orange-500') }}">{{ $typeLabel }}</span>
                </div>
                <div class="p-4 flex flex-col gap-2 flex-1">
                    <h3 class="font-extrabold text-slate-900 leading-snug">{{ $course->name_ar }}</h3>
                    <p class="text-xs text-slate-500">
                        @if($isFinished)
                            منتهية
                        @elseif($course->isRecorded() && $pathCompletion)
                            الإنجاز {{ $pathCompletion['percent'] }}%
                        @else
                            اشتراك: {{ $payment->created_at->format('Y-m-d') }}
                        @endif
                    </p>

                    <div class="ac-card-actions-auto">
                        @if($isLiveMeetingCourse && $showLink)
                        <x-course-lecture-link :course="$course" :payment="$payment"
                            label="دخول المحاضرة"
                            classes="ac-btn ac-btn-primary ac-btn-sm"
                            style="background-color:#0D2444;color:#fff;">
                            <i class="fas fa-video"></i> دخول المحاضرة
                        </x-course-lecture-link>
                        @elseif($isLiveMeetingCourse && $course->isCanceled())
                        <span class="text-xs font-bold text-red-600">ملغاة — بانتظار الاسترداد</span>
                        @elseif($isLiveMeetingCourse && ! $course->hasLiveMeetingAccess() && $startDate && $now->greaterThanOrEqualTo($startDate))
                        <span class="text-xs font-bold text-red-600">انتهى الموعد بدون رابط — جاري الإلغاء</span>
                        @elseif($isLiveMeetingCourse && ! $course->hasLiveMeetingAccess())
                        <span class="text-xs font-bold text-amber-700">بانتظار رابط الاجتماع</span>
                        @elseif($course->location_type == 'recorded')
                        <a href="{{ route('dashboard.my_courses.path', $payment->id) }}"
                            class="ac-btn ac-btn-sm {{ $isFinished ? 'ac-btn-ghost' : 'ac-btn-amber' }}"
                            style="{{ $isFinished ? 'background-color:#e8eef5;color:#0D2444;' : 'background-color:#b8893d;color:#fff;' }}">
                            <i class="fas fa-route"></i> {{ $isFinished ? 'مراجعة' : 'المسار' }}
                        </a>
                        @endif

                        <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                            class="ac-btn ac-btn-ghost ac-btn-sm"
                            style="background-color:#e8eef5;color:#0D2444;">
                            التفاصيل
                        </a>

                        @if($canCertificate)
                        <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                            target="_blank" rel="noopener"
                            class="ac-btn ac-btn-primary ac-btn-sm"
                            style="background-color:#0D2444;color:#fff;">
                            <i class="fas fa-certificate"></i> الشهادة
                        </a>
                        <a href="{{ route('dashboard.courses.certificate', ['payment' => $payment->id, 'pdf' => 1]) }}"
                            target="_blank" rel="noopener"
                            class="ac-btn ac-btn-sm"
                            style="background-color:#059669;color:#fff;">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        @elseif($needsRating)
                        <a href="{{ route('dashboard.courses.rating', $course) }}"
                            class="ac-btn ac-btn-amber ac-btn-sm"
                            style="background-color:#b8893d;color:#fff;">
                            <i class="fas fa-star"></i> أكمل التقييم
                        </a>
                        @elseif($runningDayExam)
                        <a href="{{ route('dashboard.courses.exam.take', [$course, $runningDayExam]) }}"
                            class="ac-btn ac-btn-primary ac-btn-sm"
                            style="background-color:#0D2444;color:#fff;">
                            <i class="fas fa-clipboard-list"></i> الاختبار
                        </a>
                        @endif
                        @if($payment->specialCertificate)
                        <a href="{{ route('dashboard.courses.special-certificate.download', $payment->id) }}"
                            class="ac-btn ac-btn-sm"
                            style="background-color:#7c3aed;color:#fff;">
                            <i class="fas fa-award"></i> {{ __('messages.special_certificate_download') }}
                        </a>
                        @endif
                    </div>
                    @if($needsRating && !$canCertificate)
                    <p class="text-[11px] text-amber-700">يجب إكمال التقييم للحصول على الشهادة</p>
                    @endif
                </div>
            </article>
            @empty
            <div class="col-span-full text-center bg-white border border-dashed border-slate-200 rounded-2xl text-slate-400"
                style="padding: 3.5rem 2rem;">
                <p class="text-sm sm:text-base" style="margin:0;">لم تشترك في أي دورة بعد.</p>
                <div style="margin-top: 1.25rem;">
                    <a href="{{ route('academy.index') }}" class="ac-btn ac-btn-primary" style="background-color:#0D2444;color:#fff;">تصفح الأكاديمية</a>
                </div>
            </div>
            @endforelse
        </div>
        @if($myPayments->hasPages())
        <div class="ac-pagination mt-6">{{ $myPayments->links() }}</div>
        @endif
        @else
        <div class="bg-white dark:bg-gray-800 relative shadow-md rounded-lg overflow-hidden">

            @forelse($myPayments as $payment)
            @php
                $course = $payment->course;
                $now = \Carbon\Carbon::now();
                $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date) : null;
                $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date) : null;
                $isLiveMeetingCourse = in_array($course->location_type, ['online', 'private'], true);
                $showLink = $isLiveMeetingCourse
                    && $course->hasLiveMeetingAccess()
                    && $startDate && $endDate
                    && $now->greaterThanOrEqualTo($startDate->copy()->subMinutes(30))
                    && $now->lessThanOrEqualTo($endDate);
                $isFinished = $payment->isCourseEndedForLearner($now);
                $pathCompletion = $course->isRecorded() ? $course->pathCompletionForUser($payment->user_id) : null;
                $canCertificate = $course && $course->userCanGetCertificate($payment->user_id);
                $runningDayExam = $course && $course->usesDayExams() ? $course->runningDayExam() : null;
                $needsRating = $course && $course->userNeedsRating($payment->user_id);
                $typeLabel = match($course->location_type) {
                    'online' => __('messages.academy_type_online'),
                    'recorded' => __('messages.academy_type_recorded'),
                    'on_site' => __('messages.academy_type_onsite'),
                    'private' => 'خاصة',
                    default => $course->location_type ?: '—',
                };
            @endphp

            {{-- ===== Mobile cards ===== --}}
            <div class="md:hidden border-b border-gray-100 dark:border-gray-700 p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-gray-900 dark:text-white leading-snug">{{ $course->name_ar }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $payment->invoiceNumber() }}</p>
                        <span class="inline-flex mt-1.5 text-[11px] font-bold px-2 py-0.5 rounded-full text-white
                            {{ $course->location_type === 'online' ? 'bg-blue-600' : ($course->location_type === 'recorded' ? 'bg-violet-600' : 'bg-orange-500') }}">{{ $typeLabel }}</span>
                    </div>
                    <span class="shrink-0 px-2 py-1 rounded-full text-[11px] font-medium bg-green-100 text-green-800">مدفوع</span>
                </div>

                <div class="text-xs text-gray-500 flex flex-wrap gap-x-3 gap-y-1">
                    <span><i class="far fa-calendar-alt ml-1"></i>{{ $payment->created_at->format('Y-m-d') }}</span>
                    @if($startDate)
                    <span><i class="far fa-clock ml-1"></i>{{ $startDate->format('Y-m-d h:i A') }}</span>
                    @endif
                </div>

                @if($isLiveMeetingCourse)
                    @if($course->isCanceled())
                    <p class="text-red-600 text-xs font-semibold">ملغاة — بانتظار الاسترداد</p>
                    @elseif(! $course->hasLiveMeetingAccess() && $startDate && $now->greaterThanOrEqualTo($startDate))
                    <p class="text-red-600 text-xs font-semibold">انتهى الموعد بدون رابط — جاري الإلغاء</p>
                    @elseif(! $course->hasLiveMeetingAccess())
                    <p class="text-amber-700 text-xs font-semibold">بانتظار رابط الاجتماع</p>
                    @elseif($showLink)
                    <x-course-lecture-link :course="$course" :payment="$payment"
                        label="دخول المحاضرة"
                        classes="inline-flex items-center justify-center w-full px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <i class="fas fa-video ml-2"></i> دخول المحاضرة
                    </x-course-lecture-link>
                    @elseif($isFinished)
                    <p class="text-red-500 text-xs font-semibold">الدورة انتهت</p>
                    @if($course->canAccessLectureChat())
                    <a href="{{ route('dashboard.courses.chat-archive', $course) }}"
                        class="inline-flex items-center justify-center w-full px-3 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800 text-sm font-medium">
                        <i class="fas fa-comments ml-2"></i> أرشيف النقاش
                    </a>
                    @endif
                    @else
                    <p class="text-gray-400 text-xs">
                        الرابط سيظهر في:
                        <strong>{{ $startDate?->format('Y-m-d h:i A') ?? '—' }}</strong>
                    </p>
                    @endif
                @elseif($course->location_type == 'recorded')
                    @if($isFinished)
                    <p class="text-green-700 text-xs font-semibold">مكتملة {{ $pathCompletion['percent'] ?? 100 }}%</p>
                    @elseif($pathCompletion)
                    <p class="text-gray-500 text-xs">الإنجاز {{ $pathCompletion['percent'] }}%</p>
                    @endif
                    <a href="{{ route('dashboard.my_courses.path', $payment->id) }}"
                        class="inline-flex items-center justify-center w-full px-3 py-2 {{ $isFinished ? 'bg-gray-600 hover:bg-gray-700' : 'bg-amber-500 hover:bg-amber-600' }} text-white rounded-lg text-sm font-medium">
                        <i class="fas fa-route ml-2"></i>
                        {{ $isFinished ? 'مراجعة المسار' : 'المسار التعليمي' }}
                    </a>
                @else
                <p class="text-gray-500 text-xs">حضور شخصي (مقر)</p>
                @endif

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium">
                        <i class="fas fa-eye"></i> التفاصيل
                    </a>
                    @if((float) ($course->price ?? 0) > 0)
                    <a href="{{ route('dashboard.payment.invoice', $payment->id) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium">
                        <i class="fas fa-file-invoice"></i> الفاتورة
                    </a>
                    @endif
                    @if($canCertificate)
                    <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-medium">
                        <i class="fas fa-certificate"></i> الشهادة
                    </a>
                    <a href="{{ route('dashboard.courses.certificate', ['payment' => $payment->id, 'pdf' => 1]) }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-medium">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    @elseif($needsRating)
                    <a href="{{ route('dashboard.courses.rating', $course) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-medium">
                        <i class="fas fa-star"></i> أكمل التقييم
                    </a>
                    @elseif($runningDayExam)
                    <a href="{{ route('dashboard.courses.exam.take', [$course, $runningDayExam]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium">
                        <i class="fas fa-clipboard-list"></i> الاختبار
                    </a>
                    @endif
                    @if($payment->specialCertificate)
                    <a href="{{ route('dashboard.courses.special-certificate.download', $payment->id) }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-medium">
                        <i class="fas fa-award"></i> {{ __('messages.special_certificate_download') }}
                    </a>
                    @endif
                </div>
            </div>

            @empty
            <div class="md:hidden px-4 py-10 text-center text-gray-400">
                لم تشترك في أي دورة بعد.
            </div>
            @endforelse

            {{-- ===== Desktop table ===== --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-4 text-right">رقم الطلب</th>
                            <th scope="col" class="px-4 py-4">اسم الدورة</th>
                            <th scope="col" class="px-4 py-4">تاريخ الاشتراك</th>
                            <th scope="col" class="px-4 py-4">الحالة</th>
                            <th scope="col" class="px-4 py-4 text-center">المحاضرة / اللينك</th>
                            <th scope="col" class="px-4 py-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myPayments as $payment)
                        @php
                        $course = $payment->course;
                        $now = \Carbon\Carbon::now();
                            $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date) : null;
                            $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date) : null;
                            $isLiveMeetingCourse = in_array($course->location_type, ['online', 'private'], true);
                            $showLink = $isLiveMeetingCourse
                                && $course->hasLiveMeetingAccess()
                                && $startDate && $endDate
                                && $now->greaterThanOrEqualTo($startDate->copy()->subMinutes(30))
                                && $now->lessThanOrEqualTo($endDate);
                            $isFinished = $payment->isCourseEndedForLearner($now);
                            $pathCompletion = $course->isRecorded() ? $course->pathCompletionForUser($payment->user_id) : null;
                            $canCertificate = $course && $course->userCanGetCertificate($payment->user_id);
                            $runningDayExam = $course && $course->usesDayExams() ? $course->runningDayExam() : null;
                            $needsRating = $course && $course->userNeedsRating($payment->user_id);
                            $typeLabel = match($course->location_type) {
                                'online' => __('messages.academy_type_online'),
                                'recorded' => __('messages.academy_type_recorded'),
                                'on_site' => __('messages.academy_type_onsite'),
                                'private' => 'خاصة',
                                default => $course->location_type ?: '—',
                            };
                        @endphp
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $payment->invoiceNumber() }}
                            </td>
                            <td class="px-4 py-4 mobile-wrap">
                                <div class="font-bold text-black dark:text-white">{{ $course->name_ar }}</div>
                                <span class="inline-flex mt-1 text-[11px] font-bold px-2 py-0.5 rounded-full text-white
                                    {{ $course->location_type === 'online' ? 'bg-blue-600' : ($course->location_type === 'recorded' ? 'bg-violet-600' : 'bg-orange-500') }}">{{ $typeLabel }}</span>
                                <div class="text-xs text-gray-400 mt-1">{{ Str::limit($course->description_ar, 40) }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                {{ $payment->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-4 text-sm">
                                @if($canCertificate)
                                    <span class="px-2 py-1 rounded-full text-[11px] font-medium bg-emerald-100 text-emerald-800">الشهادة جاهزة</span>
                                @elseif($needsRating)
                                    <span class="px-2 py-1 rounded-full text-[11px] font-medium bg-amber-100 text-amber-800">بانتظار التقييم</span>
                                @elseif($isFinished)
                                    <span class="px-2 py-1 rounded-full text-[11px] font-medium bg-gray-200 text-gray-700">منتهية</span>
                                @else
                                مدفوع
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    @if($isLiveMeetingCourse)
                                        @if($course->isCanceled())
                                        <span class="text-red-600 font-semibold text-xs">ملغاة — بانتظار الاسترداد</span>
                                        @elseif(! $course->hasLiveMeetingAccess() && $startDate && $now->greaterThanOrEqualTo($startDate))
                                        <span class="text-red-600 font-semibold text-xs">انتهى الموعد بدون رابط — جاري الإلغاء</span>
                                        @elseif(! $course->hasLiveMeetingAccess())
                                        <span class="text-amber-700 font-semibold text-xs">بانتظار رابط الاجتماع</span>
                                        @elseif($showLink)
                                        <x-course-lecture-link :course="$course" :payment="$payment"
                                            label="دخول المحاضرة"
                                            classes="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 animate-pulse">
                                    <i class="fas fa-video ml-2"></i> دخول المحاضرة
                                        </x-course-lecture-link>
                                @elseif($isFinished)
                                <span class="text-red-500 font-semibold italic text-xs">الدورة انتهت</span>
                                        @if($course->canAccessLectureChat())
                                        <a href="{{ route('dashboard.courses.chat-archive', $course) }}"
                                            class="inline-flex items-center px-3 py-1 bg-slate-700 text-white rounded-lg hover:bg-slate-800 text-xs">
                                            <i class="fas fa-comments ml-1"></i> أرشيف النقاش
                                        </a>
                                        @endif
                                @else
                                <div class="text-gray-400 text-xs flex flex-col">
                                    <span>الرابط سيظهر في:</span>
                                            <span class="font-bold">{{ $startDate?->format('Y-m-d h:i A') ?? '—' }}</span>
                                </div>
                                @endif
                                    @elseif($course->location_type == 'recorded')
                                        @if($isFinished)
                                        <span class="text-green-700 font-semibold text-xs">
                                            مكتملة {{ $pathCompletion['percent'] ?? 100 }}%
                                        </span>
                                        @elseif($pathCompletion)
                                        <span class="text-gray-500 text-xs">الإنجاز {{ $pathCompletion['percent'] }}%</span>
                                        @endif
                                        <a href="{{ route('dashboard.my_courses.path', $payment->id) }}"
                                            class="inline-flex items-center px-3 py-1 {{ $isFinished ? 'bg-gray-600 hover:bg-gray-700' : 'bg-amber-500 hover:bg-amber-600' }} text-white rounded-lg">
                                            <i class="fas fa-route ml-2"></i>
                                            {{ $isFinished ? 'مراجعة المسار' : 'المسار التعليمي' }}
                                        </a>
                                @else
                                <span class="text-gray-500 text-xs italic">حضور شخصي (مقر)</span>
                                @endif

                                    @if($canCertificate)
                                    <div class="flex flex-wrap items-center justify-center gap-1.5">
                                        <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                                            target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#0D2444] text-white rounded-lg hover:bg-[#163a66] text-xs font-bold">
                                            <i class="fas fa-certificate"></i> الشهادة
                                        </a>
                                        <a href="{{ route('dashboard.courses.certificate', ['payment' => $payment->id, 'pdf' => 1]) }}"
                                            target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-xs font-bold">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        @if($payment->specialCertificate)
                                        <a href="{{ route('dashboard.courses.special-certificate.download', $payment->id) }}"
                                            target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-600 text-white rounded-lg hover:bg-violet-700 text-xs font-bold">
                                            <i class="fas fa-award"></i> الخاصة
                                        </a>
                                        @endif
                                    </div>
                                    @elseif($needsRating)
                                    <a href="{{ route('dashboard.courses.rating', $course) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 text-xs font-bold">
                                        <i class="fas fa-star"></i> أكمل التقييم
                                    </a>
                                    @elseif($runningDayExam)
                                    <a href="{{ route('dashboard.courses.exam.take', [$course, $runningDayExam]) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-xs font-bold">
                                        <i class="fas fa-clipboard-list"></i> الاختبار
                                    </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-left">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('dashboard.my_courses.show', $payment->id) }}"
                                        class="text-gray-600 hover:text-blue-600" title="عرض التفاصيل">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    @if((float) ($payment->course->price ?? 0) > 0)
                                    <a href="{{ route('dashboard.payment.invoice', $payment->id) }}" class="btn-style" title="الفاتورة">
                                        <i class="fas fa-file-invoice"></i> 
                                                                            </a>
                                                                            @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                لم تشترك في أي دورة بعد.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($myPayments->hasPages())
            <div class="p-4">{{ $myPayments->links() }}</div>
            @endif
        </div>
        @endif
    </div>
</section>

@endsection
