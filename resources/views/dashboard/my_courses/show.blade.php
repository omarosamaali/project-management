@extends('layouts.app')

@section('title', 'تفاصيل الدورة')

@section('content')

@php
$course = $payment->course;
$isRecorded = $course->isRecorded();
$startDate = \Carbon\Carbon::parse($course->start_date);
$endDate = \Carbon\Carbon::parse($course->end_date);
$now = \Carbon\Carbon::now();
$pathCompletion = $isRecorded ? $course->pathCompletionForUser(auth()->id()) : null;

// منطق ظهور الرابط (قبل 30 دقيقة) — للدروس المباشرة فقط
$showLink = $now->greaterThanOrEqualTo($startDate->copy()->subMinutes(30)) && $now->lessThanOrEqualTo($endDate);
$isFinished = $now->greaterThan($endDate);
$isUpcoming = $now->lt($startDate->copy()->subMinutes(30));

$needsRating = $course->userNeedsRating(auth()->id());
$canCertificate = $course->userCanGetCertificate(auth()->id());

if ($isRecorded) {
    $pathPercent = (int) ($pathCompletion['percent'] ?? 0);
    if ($canCertificate) {
        $courseStatus = 'completed';
        $courseStatusLabel = 'مكتملة';
        $courseStatusClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
    } elseif ($needsRating) {
        $courseStatus = 'rating';
        $courseStatusLabel = 'بانتظار التقييم';
        $courseStatusClass = 'bg-amber-100 text-amber-800 border-amber-200';
    } elseif ($pathPercent >= 100) {
        $courseStatus = 'path_done';
        $courseStatusLabel = 'المسار مكتمل';
        $courseStatusClass = 'bg-sky-100 text-sky-800 border-sky-200';
    } elseif ($pathPercent > 0) {
        $courseStatus = 'active';
        $courseStatusLabel = 'جارية';
        $courseStatusClass = 'bg-green-100 text-green-800 border-green-200';
    } else {
        $courseStatus = 'ready';
        $courseStatusLabel = 'جاهزة للبدء';
        $courseStatusClass = 'bg-indigo-100 text-indigo-800 border-indigo-200';
    }
} elseif ($isFinished) {
    if ($canCertificate) {
        $courseStatus = 'completed';
        $courseStatusLabel = 'منتهية — الشهادة جاهزة';
        $courseStatusClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
    } elseif ($needsRating) {
        $courseStatus = 'rating';
        $courseStatusLabel = 'منتهية — أكمل التقييم';
        $courseStatusClass = 'bg-amber-100 text-amber-800 border-amber-200';
    } else {
        $courseStatus = 'ended';
        $courseStatusLabel = 'منتهية';
        $courseStatusClass = 'bg-slate-200 text-slate-700 border-slate-300';
    }
} elseif ($showLink || $payment->isCourseActiveForLearner($now)) {
    $courseStatus = 'active';
    $courseStatusLabel = 'نشطة';
    $courseStatusClass = 'bg-green-100 text-green-800 border-green-200';
} else {
    $courseStatus = 'upcoming';
    $courseStatusLabel = 'قادمة';
    $courseStatusClass = 'bg-blue-100 text-blue-800 border-blue-200';
}

$typeLabel = match ($course->location_type) {
    'online' => 'أونلاين',
    'recorded' => 'مسجّلة',
    'on_site' => 'حضوري',
    default => $course->location_type,
};
@endphp

<section class="p-3 sm:p-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb first="دوراتي التدريبية" link="{{ route('dashboard.my_courses.index') }}" second="تفاصيل الدورة" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        {{-- الجانب الأيمن: تفاصيل الدورة --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="relative h-48 bg-gray-200 overflow-hidden">
                    <img src="{{ asset('storage/' . $course->main_image) }}" class="w-full h-full object-cover"
                        alt="{{ $course->name_ar }}">
                    <x-media-watermark brand="academy" size="md" />
                    <div class="absolute top-4 right-4 flex flex-wrap gap-2 justify-end z-[4]">
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                            {{ $typeLabel }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $courseStatusClass }}">
                            {{ $courseStatusLabel }}
                        </span>
                    </div>
                </div>

                <div class="mt-3 p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $course->name_ar }}</h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $courseStatusClass }}">
                            <i class="fas fa-circle text-[0.45rem]"></i>
                            {{ $courseStatusLabel }}
                        </span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                        {{ $course->description_ar }}
                    </p>

                    @if($isRecorded)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-6">
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-video text-blue-500 w-5"></i>
                            <span>نوع الدورة: <strong>مسجّلة</strong></span>
                        </div>
                        @if($course->totalContentDurationSeconds() > 0)
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-clock text-blue-500 w-5"></i>
                            <span>مدة المحتوى: <strong>{{ $course->formattedTotalContentDuration() }}</strong></span>
                        </div>
                        @endif
                        @if($pathCompletion && $pathCompletion['total'] > 0)
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300 md:col-span-2">
                            <i class="fas fa-list-check text-blue-500 w-5"></i>
                            <span>خطوات المسار: <strong>{{ $pathCompletion['completed'] }} / {{ $pathCompletion['total'] }}</strong></span>
                        </div>
                        @endif
                    </div>
                    @else
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-6">
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-calendar-alt text-blue-500 w-5"></i>
                            <span>تاريخ البدء: <strong>{{ $startDate->format('Y-m-d') }}</strong></span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-clock text-blue-500 w-5"></i>
                            <span>وقت المحاضرة: <strong>{{ $startDate->format('h:i A') }}</strong></span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-map-marker-alt text-blue-500 w-5"></i>
                            <span>نوع الحضور: <strong>{{ $course->location_type == 'online' ? 'أونلاين' : 'في المقر' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-hourglass-half text-blue-500 w-5"></i>
                            <span>المدة: <strong>{{ $course->actual_course_days }} {{ $course->actual_course_days == 1 ? 'يوم' : 'أيام' }}</strong></span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- حالة الدورة + التقييم / الشهادة --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 sm:p-6 border border-slate-100 dark:border-gray-700">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-flag text-teal-600"></i>
                        حالة الدورة
                    </h3>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $courseStatusClass }}">
                        {{ $courseStatusLabel }}
                    </span>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                    @if($canCertificate)
                        تهانينا! يمكنك الآن استخراج شهادة إتمام الدورة.
                    @elseif($needsRating)
                        أكملت متطلبات الدورة. أكمل التقييم لاستخراج الشهادة.
                    @elseif($isRecorded)
                        تابع المسار التعليمي حتى الإكمال ثم التقييم للحصول على الشهادة.
                    @elseif($courseStatus === 'active')
                        الدورة نشطة حالياً — انضم للمحاضرة من القسم أدناه عند التفعيل.
                    @elseif($courseStatus === 'upcoming')
                        الدورة قادمة. سيظهر رابط المحاضرة قبل الموعد بـ 30 دقيقة.
                    @else
                        انتهت هذه الدورة.
                    @endif
                </p>

                <div class="flex flex-wrap gap-2">
                    @if($canCertificate)
                    <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0D2444] text-white text-sm font-bold hover:bg-[#163a66] transition">
                        <i class="fas fa-certificate"></i>
                        عرض الشهادة
                    </a>
                    @elseif($needsRating)
                    <a href="{{ route('dashboard.courses.rating', $course) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-bold hover:bg-amber-600 transition">
                        <i class="fas fa-star"></i>
                        أكمل التقييم
                    </a>
                    @endif

                    @if($isRecorded)
                    <a href="{{ route('dashboard.my_courses.path', $payment->id) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-bold hover:bg-teal-700 transition">
                        <i class="fas fa-play"></i>
                        {{ ($pathCompletion['percent'] ?? 0) > 0 ? 'متابعة التعلم' : 'بدء المسار' }}
                    </a>
                    @endif
                </div>
            </div>

            @if($isRecorded && $pathCompletion)
            {{-- تقدم المتدرب في المسار المسجّل --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5"
                id="coursePathProgressBar"
                data-completed="{{ $pathCompletion['completed'] }}"
                data-total="{{ $pathCompletion['total'] }}"
                data-percent="{{ $pathCompletion['percent'] }}">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        إنجازك في الدورة
                    </h3>
                    <span class="text-sm font-bold text-blue-700 dark:text-blue-300 tabular-nums">
                        <span data-progress-percent>{{ $pathCompletion['percent'] }}</span>%
                    </span>
                </div>
                <div class="h-3 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div data-progress-fill
                        class="h-full rounded-full bg-gradient-to-l from-blue-600 to-sky-400 transition-all duration-500"
                        style="width: {{ $pathCompletion['percent'] }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    أكملت <strong data-progress-completed>{{ $pathCompletion['completed'] }}</strong>
                    من أصل <strong>{{ $pathCompletion['total'] }}</strong> خطوة في المسار التعليمي
                </p>

                @php
                    $recordedNeedsRating = $needsRating;
                    $recordedCanCert = $canCertificate;
                @endphp
                @if($recordedCanCert)
                <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                    target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 px-5 py-2 mt-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">
                    <i class="fas fa-certificate"></i> استخراج الشهادة
                </a>
                @elseif($recordedNeedsRating)
                <a href="{{ route('dashboard.courses.rating', $course) }}"
                    class="inline-flex items-center gap-2 px-5 py-2 mt-3 bg-amber-500 text-white rounded-lg font-bold hover:bg-amber-600">
                    <i class="fas fa-star"></i> أكمل التقييم للحصول على الشهادة
                </a>
                <p class="text-xs text-amber-700 mt-1">يجب إكمال تقييم الدورة قبل استخراج الشهادة</p>
                @endif
            </div>
            @endif

            @if($course->isOnSite())
            {{-- قسم تفاصيل المكان (الموقع) — للحضور في المقر فقط --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-blue-600"></i>
                    معلومات الموقع
                </h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <span class="font-bold">المكان:</span> {{ $course->venue_name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <span class="font-bold">التفاصيل:</span> {{ $course->venue_details }}
                    </p>
                    @if($course->venue_map_url)
                    <a href="{{ $course->venue_map_url }}" target="_blank"
                        class="inline-flex items-center text-xs text-blue-600 hover:underline mt-2">
                        <i class="fas fa-external-link-alt ml-1"></i> عرض الموقع على الخريطة
                    </a>
                    @endif
                </div>
            </div>
            @endif
            {{-- أزرار الإجراءات: بدون تسجيل → تاب جديد | يحتاج تسجيل → iframe --}}
            @php
                $actionButtons = collect($course->buttons ?? [])
                    ->values()
                    ->filter(fn ($button) => !empty($button['link']));
            @endphp
            @if($actionButtons->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-link text-blue-600"></i>
                    أزرار الإجراءات
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($actionButtons as $buttonIndex => $button)
                        @php
                            $needsLogin = !empty($button['needs_login']);
                            $buttonLabel = app()->getLocale() == 'ar'
                                ? ($button['text_ar'] ?? $button['text_en'] ?? 'اضغط هنا')
                                : ($button['text_en'] ?? $button['text_ar'] ?? 'اضغط هنا');
                        @endphp
                        @if($needsLogin)
                        <a href="{{ route('dashboard.my_courses.button', ['payment' => $payment->id, 'button' => $buttonIndex]) }}"
                            class="px-6 py-3 rounded-lg text-center text-white font-semibold hover:opacity-90 transition"
                            style="background-color: {{ $button['color'] ?? '#3B82F6' }}">
                            {{ $buttonLabel }}
                        </a>
                        @else
                        <a href="{{ $button['link'] }}" target="_blank" rel="noopener noreferrer"
                            class="px-6 py-3 rounded-lg text-center text-white font-semibold hover:opacity-90 transition"
                            style="background-color: {{ $button['color'] ?? '#3B82F6' }}">
                            {{ $buttonLabel }}
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- اختبارات أيام الدورة --}}
            @if($course->usesDayExams() && $payment->is_attended && !$course->isRecorded())
            @php
                $runningDayExam = $course->runningDayExam();
                $passedCount = $course->userPassedDayExamCount(auth()->id());
                $requiredPass = $course->effectiveRequiredExamPassCount();
            @endphp
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-lg p-6 space-y-3">
                <h3 class="text-lg font-bold text-indigo-800 dark:text-indigo-300">
                    <i class="fas fa-clipboard-list ml-2"></i> اختبارات الدورة
                </h3>
                <p class="text-sm text-gray-600">
                    المجتاز: <strong>{{ $passedCount }}</strong> من المطلوب <strong>{{ $requiredPass }}</strong>
                </p>

                @if($runningDayExam)
                    <p class="text-sm text-indigo-700">الاختبار متاح الآن: {{ $runningDayExam->displayTitle() }}</p>
                    <a href="{{ route('dashboard.courses.exam.take', [$course, $runningDayExam]) }}"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 animate-pulse">
                        <i class="fas fa-play"></i> ابدأ الاختبار
                    </a>
                @elseif($needsRating)
                    <a href="{{ route('dashboard.courses.rating', $course) }}"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-amber-500 text-white rounded-lg font-bold hover:bg-amber-600">
                        <i class="fas fa-star"></i> أكمل التقييم للحصول على الشهادة
                    </a>
                    <p class="text-xs text-amber-700 mt-1">يجب إكمال تقييم الدورة قبل استخراج الشهادة</p>
                @elseif($canCertificate)
                    <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">
                        <i class="fas fa-certificate"></i> استخراج الشهادة
                    </a>
                @elseif($course->areAllDayExamsFinished())
                    <p class="text-sm text-gray-600">انتهت الاختبارات. لم تحقق العدد المطلوب لاجتياز الشهادة.</p>
                @else
                    <p class="text-sm text-gray-600">سيظهر الاختبار هنا فور قيام الإدارة ببدئه.</p>
                @endif

                <ul class="text-xs text-gray-500 space-y-1 pt-2 border-t border-indigo-100">
                    @foreach($course->dayExams as $exam)
                    <li>
                        اليوم {{ $exam->day_index }} — {{ $exam->displayTitle() }}:
                        <strong>{{ $exam->statusLabel() }}</strong>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- قسم المحتوى / اللينك --}}
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg p-6">
                <h3 class="text-lg font-bold text-blue-800 dark:text-blue-300 mb-3">
                    @if($course->location_type == 'recorded')
                    <i class="fas fa-route ml-2"></i> المسار التعليمي
                    @else
                    <i class="fas fa-video ml-2"></i> رابط دخول المحاضرة
                    @endif
                </h3>
                @if($course->location_type == 'online')
                @if($showLink)
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-blue-700 dark:text-blue-400">المحاضرة جارية الآن، يمكنك الانضمام مباشرة من
                        خلال الرابط:</p>
                    <x-course-lecture-link :course="$course" :payment="$payment"
                        label="انضم الآن"
                        classes="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 animate-pulse transition-all inline-flex items-center gap-2" />
                </div>
                @elseif($isFinished)
                <p class="text-red-600 font-bold italic text-sm">نعتذر، لقد انتهى موعد هذه الدورة.</p>
                @else
                <div class="flex items-center gap-3 text-amber-700 bg-amber-50 p-3 rounded-lg border border-amber-100">
                    <i class="fas fa-info-circle"></i>
                    <p class="text-sm">سوف يتم تفعيل الرابط تلقائياً يوم <strong>{{ $startDate->format('Y-m-d')
                            }}</strong> الساعة <strong>{{ $startDate->copy()->subMinutes(30)->format('h:i A') }}</strong>.</p>
                </div>
                @endif
                @if($course->canAccessLectureChat())
                <div class="mt-4 pt-3 border-t border-blue-100 dark:border-blue-800">
                    <a href="{{ route('dashboard.courses.chat-archive', $course) }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-blue-800 dark:text-blue-300 hover:underline">
                        <i class="fas fa-comments"></i>
                        {{ $isFinished ? 'أرشيف نقاش المحاضرة' : 'نقاش المحاضرة' }}
                    </a>
                </div>
                @endif
                @elseif($course->location_type == 'recorded')
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-blue-700 dark:text-blue-400">
                        دورة مسجّلة — شاهد الدروس بالترتيب
                        @if($course->totalContentDurationSeconds() > 0)
                        (المدة الكلية: {{ $course->formattedTotalContentDuration() }})
                        @endif
                    </p>
                    <a href="{{ route('dashboard.my_courses.path', $payment->id) }}"
                        class="bg-amber-500 text-white px-6 py-2 rounded-lg font-bold hover:bg-amber-600 transition-all inline-flex items-center gap-2">
                        <i class="fas fa-play"></i>
                        {{ ($pathCompletion['completed'] ?? 0) > 0 ? 'متابعة المسار التعليمي' : 'ابدأ المسار التعليمي' }}
                    </a>
                </div>
                @else
                <p class="text-gray-600 dark:text-gray-400">هذه الدورة تتطلب الحضور الشخصي لمقر الأكاديمية.</p>
                @endif
            </div>

            {{-- قسم معرض الصور الفرعية --}}
                @if($course->video)
                <div class="mt-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-video text-blue-600"></i>
                        الفيديو التعريفي
                    </h3>
                    <video controls class="w-full max-h-96 rounded-xl border border-gray-200 dark:border-gray-700 bg-black shadow-sm"
                        src="{{ asset('storage/' . $course->video) }}"></video>
                </div>
                @endif

                @php
                // جرب تستخدمه مباشرة لأن لارافل قام بفك التشفير عنه
                $gallery = $course->images;
                @endphp
                
                @if(is_array($gallery) && count($gallery) > 0)
                <div class="mt-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-images text-blue-600"></i>
                        معرض صور الدورة
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($gallery as $imagePath)
                        <div
                            class="group relative aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                            <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" class="block w-full h-full">
                                <img src="{{ asset('storage/' . $imagePath) }}" alt="صورة فرعية" class="w-full h-full object-cover">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
        </div>

        {{-- الجانب الأيسر: ملخص الفاتورة والحالة --}}
        <div class="space-y-6">
            @if((float) ($course->price ?? 0) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2">تفاصيل الاشتراك</h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">رقم الفاتورة:</span>
                        <span class="font-mono font-bold">#{{ $payment->invoiceNumber() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">تاريخ الدفع:</span>
                        <span class="text-sm">{{ $payment->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">المبلغ المدفوع:</span>
                        <span class="text-black flex items-center gap-1 font-bold">{{ $course->price }}
                            <x-drhm-icon width="12" height="14" />
                         </span>
                    </div>
                    <div class="flex justify-between items-center border-t pt-4">
                        <span class="text-gray-500 text-sm">حالة الاشتراك:</span>
                        <span
                            class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold italic">مكتمل</span>
                    </div>
                </div>

                {{-- <button onclick="window.print()"
                    class="w-full mt-6 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-lg font-bold transition-colors">
                    <i class="fas fa-print"></i> طباعة الإيصال
                </button> --}}
            </div>
            @endif

            {{-- الدعم الفني --}}
            <div class="bg-gray-900 text-white rounded-lg p-6 shadow-md">
                <h4 class="font-bold mb-2">تحتاج مساعدة؟</h4>
                <p class="text-xs text-gray-400 mb-4">
                    @if($isRecorded)
                    إذا واجهت أي مشكلة في متابعة المسار التعليمي لا تتردد في التواصل معنا.
                    @else
                    إذا واجهت أي مشكلة في الدخول للمحاضرة لا تتردد في التواصل معنا.
                    @endif
                </p>
              <a href="https://wa.me/971552908019" target="_blank"
                class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 py-2 rounded-lg text-sm font-bold">
                <i class="fab fa-whatsapp text-lg"></i> الدعم عبر واتساب
            </a>
            </div>
        </div>
    </div>
</section>

@endsection