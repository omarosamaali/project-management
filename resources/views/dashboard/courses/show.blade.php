@extends('layouts.app')

@section('title', 'تفاصيل الدورة: ' . $course->name_ar)

@section('content')
<section class="!px-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.courses.index') }}" second="الدورات"
        third="تفاصيل الدورة" />

    <div class="mx-auto max-w-5xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-xl overflow-hidden">

            <!-- Header -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $course->name_ar }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ $course->name_en }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        {{-- Course status --}}
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium
                            {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas {{ $course->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            حالة الدورة: {{ $course->courseStatusLabel() }}
                        </span>

                        {{-- Exam status --}}
                        @if($course->has_exam)
                            @php $examStatus = $course->examStatus(); @endphp
                            @if($examStatus === 'not_started')
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                                <i class="fas fa-clipboard-list"></i>
                                حالة الاختبار: لم يبدأ
                            </span>
                            <button type="button" onclick="openStartExamModal()"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 font-bold">
                                <i class="fas fa-play"></i>
                                بدء الاختبار
                            </button>
                            <form id="startExamForm" action="{{ route('dashboard.courses.start-exam', $course) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @elseif($examStatus === 'running')
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-amber-100 text-amber-800">
                                <i class="fas fa-play-circle"></i>
                                حالة الاختبار: جارٍ
                                <span class="text-amber-700/80 font-normal">منذ {{ $course->exam_started_at->format('Y-m-d H:i') }}</span>
                            </span>
                            <button type="button" onclick="openEndExamModal()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2 font-bold">
                                <i class="fas fa-stop"></i>
                                إنهاء الاختبار
                            </button>
                            <form id="endExamForm" action="{{ route('dashboard.courses.end-exam', $course) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @elseif($examStatus === 'finished')
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-flag-checkered"></i>
                                حالة الاختبار: منتهٍ
                                <span class="text-red-700/80 font-normal">عند {{ $course->exam_ended_at->format('Y-m-d H:i') }}</span>
                            </span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-500">
                                <i class="fas fa-minus-circle"></i>
                                لا يوجد اختبار
                            </span>
                        @endif

                        <a href="{{ route('dashboard.courses.edit', $course->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Card: Basic Info -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-blue-600">
                            <i class="fas fa-info-circle"></i>
                            المعلومات الأساسية
                        </h3>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">السعر</dt>
                                <dd class="font-medium flex items-center gap-1">
                                    {{ number_format($course->price) }}
                                    {{-- <span class="text-xs text-gray-500"> --}}
                                        <x-drhm-icon width="12" height="14" />
                                        {{--
                                    </span> --}}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">الحد الأقصى لعدد المشتركين</dt>
                                <dd class="font-medium">
                                    {{ $course->counter }}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">نوع الخدمة</dt>
                                <dd class="font-medium">
                                    {{ $course->service ? $course->service->name_ar : 'غير محدد' }}
                                </dd>
                            </div>

                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">حالة الدورة</dt>
                                <dd>
                                    <span
                                        class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $course->courseStatusLabel() }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">حالة الاختبار</dt>
                                <dd>
                                    @php $cardExamStatus = $course->examStatus(); @endphp
                                    @if($cardExamStatus === 'none')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">بدون اختبار</span>
                                    @elseif($cardExamStatus === 'not_started')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">لم يبدأ</span>
                                    @elseif($cardExamStatus === 'running')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">جارٍ</span>
                                    @elseif($cardExamStatus === 'finished')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">منتهٍ</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

<!-- Card: Dates -->
<div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-blue-600">
        <i class="fas fa-calendar-alt"></i>
        التواريخ
    </h3>

    <dl class="space-y-3 text-sm">
        <div class="flex justify-between">
            <dt class="text-gray-600 dark:text-gray-400">تاريخ البداية</dt>
            <dd class="font-medium">{{ $course->start_date->format('Y-m-d h:i A') }}</dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-gray-600 dark:text-gray-400">تاريخ النهاية</dt>
            <dd class="font-medium">{{ $course->end_date->format('Y-m-d h:i A') }}</dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-gray-600 dark:text-gray-400">آخر موعد للتسجيل</dt>
            <dd class="font-medium">{{ $course->last_date->format('Y-m-d h:i A') }}</dd>
        </div>

        <hr class="border-gray-300 dark:border-gray-600">

        <div class="flex justify-between">
            <dt class="text-gray-600 dark:text-gray-400">عدد أيام الدورة</dt>
            <dd class="font-bold text-blue-600">{{ $course->actual_course_days }} {{ $course->actual_course_days == 1 ? 'يوم' : 'أيام' }}</dd>
        </div>

        @if ($course->rest_days && count($course->rest_days) > 0)
        <div class="pt-2">
            <dt class="text-gray-600 dark:text-gray-400 mb-2">أيام الإجازة</dt>
            <dd class="flex flex-wrap gap-1.5">
                @php
                $daysArabic = [
                'sunday' => 'الأحد',
                'monday' => 'الإثنين',
                'tuesday' => 'الثلاثاء',
                'wednesday' => 'الأربعاء',
                'thursday' => 'الخميس',
                'friday' => 'الجمعة',
                'saturday' => 'السبت'
                ];
                @endphp
                @foreach($course->rest_days as $day)
                <span
                    class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 rounded text-xs font-medium">
                    <i class="fas fa-ban text-[10px]"></i>
                    {{ $daysArabic[$day] ?? $day }}
                </span>
                @endforeach
            </dd>
        </div>
        @endif
    </dl>
</div>
                    <!-- Card: Location -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-blue-600">
                            <i class="fas fa-map-marker-alt"></i>
                            مكان الحضور
                        </h3>

                        <div class="text-sm space-y-2">
                            <p>
                                <strong>النوع:</strong>
                                <span
                                    class="{{ $course->location_type === 'online' ? 'text-blue-600' : 'text-green-600' }}">
                                    {{ $course->location_type === 'online' ? 'أونلاين' : 'حضوري' }}
                                </span>
                            </p>

                            @if($course->location_type === 'online')
                            <p>
                                <strong>رابط الدورة:</strong>
                                @if($course->online_link)
                                <a href="{{ $course->online_link }}" target="_blank"
                                    class="text-blue-600 hover:underline">
                                    {{ Str::limit($course->online_link, 40) }}
                                </a>
                                @else
                                <span class="text-gray-500">غير محدد</span>
                                @endif
                            </p>
                            @else
                            <p><strong>اسم المكان:</strong> {{ $course->venue_name ?: 'غير محدد' }}</p>
                            @if($course->venue_map_url)
                            <p>
                                <strong>رابط الخريطة:</strong>
                                <a href="{{ $course->venue_map_url }}" target="_blank"
                                    class="text-blue-600 hover:underline">
                                    عرض على الخريطة
                                </a>
                            </p>
                            @endif
                            @if($course->venue_details)
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                {{ $course->venue_details }}
                            </p>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-align-right text-blue-600"></i>
                        الوصف
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div
                            class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold mb-3 text-gray-800 dark:text-gray-200">بالعربية</h4>
                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {!! nl2br(e($course->description_ar)) !!}
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold mb-3 text-gray-800 dark:text-gray-200">بالإنجليزية</h4>
                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {!! nl2br(e($course->description_en)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements & Features -->
                <div class="mt-10 grid md:grid-cols-2 gap-8">
                    <!-- Requirements -->
                    <div>
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                            <i class="fas fa-list-check text-green-600"></i>
                            المتطلبات
                        </h3>
                        @if(!empty($course->requirements))
                        <ul class="space-y-2">
                            @foreach($course->requirements as $req)
                            <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-check-circle text-green-500 mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="font-medium">{{ $req['ar'] ?? '' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $req['en'] ?? '' }}</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 dark:text-gray-400">لا توجد متطلبات محددة</p>
                        @endif
                    </div>

                    <!-- Features -->
                    <div>
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                            <i class="fas fa-star text-yellow-500"></i>
                            المميزات
                        </h3>
                        @if(!empty($course->features))
                        <ul class="space-y-2">
                            @foreach($course->features as $feat)
                            <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-check-circle text-blue-500 mt-1 flex-shrink-0"></i>
                                <div>
                                    <div class="font-medium">{{ $feat['ar'] ?? '' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $feat['en'] ?? '' }}</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-500 dark:text-gray-400">لا توجد مميزات محددة</p>
                        @endif
                    </div>
                </div>

                <!-- Buttons & Actions -->
                @if(!empty($course->buttons))
                <div class="mt-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-link text-blue-600"></i>
                        أزرار الإجراءات
                    </h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($course->buttons as $button)
                        <div class="flex flex-col items-start gap-1">
                            <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                                class="px-6 py-3 rounded-lg font-medium text-white transition"
                                style="background-color: {{ $button['color'] ?? '#3B82F6' }};">
                                {{ $button['text_ar'] ?? 'اضغط هنا' }}
                            </a>
                            @if(!empty($button['needs_login']))
                            <span class="text-xs text-amber-600 font-medium">
                                <i class="fas fa-lock ml-1"></i>
                                يظهر بعد الشراء فقط
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Main Image -->
                @if($course->main_image)
                <div class="mt-10">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-image text-purple-600"></i>
                        الصورة الرئيسية
                    </h3>
                    <img src="{{ Storage::url($course->main_image) }}" alt="{{ $course->name_ar }}"
                        class="w-full max-h-96 object-cover rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                </div>
                @endif
                <!-- الصور الإضافية -->
                @if(!empty($course->images) && is_array($course->images) && count($course->images) > 0)
                <div class="mt-10">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-3 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-images text-purple-600"></i>
                        الصور الإضافية للدورة
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($course->images as $image)
                        <div
                            class="relative group rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow">
                            <img src="{{ Storage::url($image) }}" alt="صورة إضافية لـ {{ $course->name_ar }}"
                                class="w-full h-48 object-cover transition-transform group-hover:scale-105"
                                loading="lazy">
                            <!-- overlay خفيف عند الـ hover (اختياري) -->
                            <div
                                class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <a href="{{ Storage::url($image) }}" target="_blank"
                                    class="text-white text-sm font-medium">
                                    عرض كامل الحجم
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="mt-10 text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed">
                    <i class="fas fa-images text-5xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">
                        لا توجد صور إضافية مضافة لهذه الدورة
                    </p>
                </div>
                @endif
                <!-- المشتركين -->
                <div class="my-12">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                       <h2 class="pt-6 text-2xl font-bold flex items-center gap-3">
                        <i class="fas fa-users text-indigo-600 text-2xl"></i>
                        قائمة المشتركين في الدورة
                    </h2>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-lg font-medium text-gray-600 dark:text-gray-400">
                            إجمالي: {{ $course->payments->count() }} مشترك
                        </span>
                        @if($course->payments?->count() > 0)
                        <form id="bulkAttendanceForm" action="{{ route('dashboard.courses.bulk-attendance', $course) }}" method="POST" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <input type="hidden" name="action" id="bulkAttendanceAction" value="attend">
                            <button type="button" id="bulkAttendBtn" disabled onclick="openBulkAttendanceModal('attend')"
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-700">
                                <i class="fas fa-user-check ml-1"></i>
                                تحضير المحددين
                                <span id="bulkAttendCount" class="hidden">(0)</span>
                            </button>
                            <button type="button" id="bulkUnattendBtn" disabled onclick="openBulkAttendanceModal('unattend')"
                                class="px-4 py-2 rounded-lg bg-gray-500 text-white text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-600">
                                <i class="fas fa-user-times ml-1"></i>
                                إلغاء تحضير المحددين
                                <span id="bulkUnattendCount" class="hidden">(0)</span>
                            </button>
                        </form>
                        @endif
                    </div>
                    </div>

                    @if($course->payments?->count() > 0)
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                                        <input type="checkbox" id="selectAllAttendance"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            title="تحديد الكل">
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        الاسم
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        البريد الإلكتروني
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        تاريخ الاشتراك
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        الحالة
                                    </th>
                                    {{-- @if($course->students->first()->pivot->price_paid ?? false) --}}
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        المبلغ المدفوع
                                    </th>
                                    @if($course->has_exam)
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        حالة الاختبار
                                    </th>
                                    @endif
                                    <th
                                        class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        إجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @if($course->payments->isNotEmpty())
                                @foreach($course->payments as $index => $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="payment_ids[]" value="{{ $payment->id }}"
                                            form="bulkAttendanceForm"
                                            data-attended="{{ $payment->is_attended ? '1' : '0' }}"
                                            class="attendance-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $payment->user->name ?? 'غير متوفر' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $payment->user->email ?? 'غير متوفر' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusLabels = [
                                                'completed' => ['مكتمل', 'bg-green-100 text-green-800'],
                                                'success' => ['ناجح', 'bg-green-100 text-green-800'],
                                                'paid' => ['مدفوع', 'bg-green-100 text-green-800'],
                                                'active' => ['نشط', 'bg-blue-100 text-blue-800'],
                                                'pending' => ['قيد الانتظار', 'bg-amber-100 text-amber-800'],
                                            ];
                                            [$label, $classes] = $statusLabels[$payment->status] ?? [$payment->status, 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs {{ $classes }}">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold flex items-center gap-1">
                                        {{ number_format($payment->amount, 2) }}
                                        <x-drhm-icon width="12" height="14" />
                                    </td>
                                    @if($course->has_exam)
                                    @php
                                        $examAttempt = $payment->is_attended
                                            ? $course->examAttempts->firstWhere('user_id', $payment->user_id)
                                            : null;
                                        $examStatus = $payment->is_attended
                                            ? $course->userExamStatus($payment->user_id, $examAttempt)
                                            : 'none';
                                        $examStatusUi = match ($examStatus) {
                                            'not_entered' => ['لم يدخل بعد', 'bg-gray-100 text-gray-700'],
                                            'in_progress' => ['قيد الاختبار', 'bg-amber-100 text-amber-800'],
                                            'passed' => ['ناجح' . ($examAttempt ? ' (' . $examAttempt->score . '/' . $course->examQuestions->count() . ')' : ''), 'bg-green-100 text-green-800'],
                                            'failed' => ['راسب' . ($examAttempt ? ' (' . $examAttempt->score . '/' . $course->examQuestions->count() . ')' : ''), 'bg-red-100 text-red-800'],
                                            default => ['—', 'bg-gray-50 text-gray-400'],
                                        };
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="exam-status-badge px-3 py-1 rounded-full text-xs font-medium {{ $examStatusUi[1] }}"
                                            data-user-id="{{ $payment->user_id }}"
                                            data-status="{{ $examStatus }}">
                                            {{ $examStatusUi[0] }}
                                        </span>
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        @if((float) $course->price > 0)
                                        <a href="{{ route('dashboard.payment.invoice', $payment->id) }}" class="btn-style" title="الفاتورة">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        @endif
                                    
                                        <form action="{{ route('dashboard.courses.toggle-attendance', $payment->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="mx-1 px-3 py-1 rounded-lg {{ $payment->is_attended ? 'bg-green-600' : 'bg-gray-400' }} text-white transition">
                                                <i class="fas {{ $payment->is_attended ? 'fa-user-check' : 'fa-user-clock' }}"></i>
                                                {{ $payment->is_attended ? 'تم الحضور' : 'تحضير' }}
                                            </button>
                                        </form>
                                    
                                        @if($payment->is_attended)
                                            <span class="exam-cert-slot inline" data-user-id="{{ $payment->user_id }}" data-payment-id="{{ $payment->id }}">
                                            @php
                                                $canCertificate = !$course->has_exam || $course->userPassedExam($payment->user_id);
                                            @endphp
                                            @if($canCertificate)
                                            <a href="{{ route('dashboard.courses.certificate', $payment->id) }}"
                                                class="px-3 py-.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                                <i class="fas fa-certificate"></i> الشهادة
                                            </a>
                                            @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div
                        class="text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                        <i class="fas fa-users-slash text-6xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-700 dark:text-gray-300 mb-2">
                            لا يوجد مشتركين بعد
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            لم يشترك أي مستخدم في هذه الدورة حتى الآن
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if($course->payments?->count() > 0)
{{-- Bulk Attendance Modal --}}
<div id="bulkAttendanceModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 text-center">
            <div id="bulkAttendanceModalIcon" class="mx-auto mb-4 w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                <i class="fas fa-user-check text-2xl"></i>
            </div>
            <h3 id="bulkAttendanceModalTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-2">تحضير المحددين</h3>
            <p id="bulkAttendanceModalText" class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                هل تريد تسجيل حضور المشتركين المحددين؟
            </p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="button" onclick="closeBulkAttendanceModal()"
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-lg font-medium transition">
                إلغاء
            </button>
            <button type="button" id="bulkAttendanceConfirmBtn" onclick="confirmBulkAttendance()"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-bold transition">
                نعم، سجّل الحضور
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const selectAll = document.getElementById('selectAllAttendance');
        const checkboxes = () => Array.from(document.querySelectorAll('.attendance-checkbox'));
        const bulkAttendBtn = document.getElementById('bulkAttendBtn');
        const bulkUnattendBtn = document.getElementById('bulkUnattendBtn');
        const bulkAttendCount = document.getElementById('bulkAttendCount');
        const bulkUnattendCount = document.getElementById('bulkUnattendCount');
        const bulkForm = document.getElementById('bulkAttendanceForm');
        const bulkActionInput = document.getElementById('bulkAttendanceAction');
        const modal = document.getElementById('bulkAttendanceModal');
        const modalText = document.getElementById('bulkAttendanceModalText');
        const modalTitle = document.getElementById('bulkAttendanceModalTitle');
        const modalIcon = document.getElementById('bulkAttendanceModalIcon');
        const confirmBtn = document.getElementById('bulkAttendanceConfirmBtn');

        let pendingAction = 'attend';

        function selectedCheckboxes() {
            return checkboxes().filter((cb) => cb.checked);
        }

        function selectedAttendableCount() {
            return selectedCheckboxes().filter((cb) => cb.dataset.attended !== '1').length;
        }

        function selectedUnattendableCount() {
            return selectedCheckboxes().filter((cb) => cb.dataset.attended === '1').length;
        }

        function updateCountBadge(el, count) {
            if (!el) return;
            el.textContent = `(${count})`;
            el.classList.toggle('hidden', count === 0);
        }

        function updateBulkState() {
            const selected = selectedCheckboxes();
            const total = checkboxes().length;
            const count = selected.length;
            const attendable = selectedAttendableCount();
            const unattendable = selectedUnattendableCount();

            if (bulkAttendBtn) bulkAttendBtn.disabled = attendable === 0;
            if (bulkUnattendBtn) bulkUnattendBtn.disabled = unattendable === 0;
            updateCountBadge(bulkAttendCount, attendable);
            updateCountBadge(bulkUnattendCount, unattendable);

            if (selectAll) {
                selectAll.checked = total > 0 && count === total;
                selectAll.indeterminate = count > 0 && count < total;
                selectAll.disabled = total === 0;
            }
        }

        window.openBulkAttendanceModal = function (action) {
            pendingAction = action === 'unattend' ? 'unattend' : 'attend';
            const count = pendingAction === 'attend'
                ? selectedAttendableCount()
                : selectedUnattendableCount();

            if (count === 0) return;

            if (bulkActionInput) bulkActionInput.value = pendingAction;

            if (pendingAction === 'attend') {
                modalTitle.textContent = 'تحضير المحددين';
                modalText.textContent = `هل تريد تسجيل حضور ${count} مشترك؟`;
                confirmBtn.textContent = 'نعم، سجّل الحضور';
                confirmBtn.className = 'flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-bold transition';
                modalIcon.className = 'mx-auto mb-4 w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center';
                modalIcon.innerHTML = '<i class="fas fa-user-check text-2xl"></i>';
            } else {
                modalTitle.textContent = 'إلغاء تحضير المحددين';
                modalText.textContent = `هل تريد إلغاء حضور ${count} مشترك؟`;
                confirmBtn.textContent = 'نعم، إلغِ الحضور';
                confirmBtn.className = 'flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2.5 rounded-lg font-bold transition';
                modalIcon.className = 'mx-auto mb-4 w-14 h-14 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center';
                modalIcon.innerHTML = '<i class="fas fa-user-times text-2xl"></i>';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        };

        window.closeBulkAttendanceModal = function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        };

        window.confirmBulkAttendance = function () {
            const count = pendingAction === 'attend'
                ? selectedAttendableCount()
                : selectedUnattendableCount();

            if (count === 0) {
                closeBulkAttendanceModal();
                return;
            }

            // Only submit IDs matching the chosen action
            checkboxes().forEach((cb) => {
                const isAttended = cb.dataset.attended === '1';
                if (!cb.checked) return;
                if (pendingAction === 'attend' && isAttended) {
                    cb.checked = false;
                    cb.disabled = true;
                } else if (pendingAction === 'unattend' && !isAttended) {
                    cb.checked = false;
                    cb.disabled = true;
                }
            });

            if (bulkActionInput) bulkActionInput.value = pendingAction;
            bulkForm.submit();
        };

        selectAll?.addEventListener('change', function () {
            checkboxes().forEach((cb) => { cb.checked = selectAll.checked; });
            updateBulkState();
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('attendance-checkbox')) {
                updateBulkState();
            }
        });

        modal?.addEventListener('click', function (e) {
            if (e.target === this) closeBulkAttendanceModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeBulkAttendanceModal();
            }
        });

        updateBulkState();
    })();
</script>
@endif

@if($course->has_exam)
<script>
    (function () {
        const url = @json(route('dashboard.courses.exam-statuses', $course));
        const certBase = @json(url('/dashboard/payments'));

        const uiMap = {
            not_entered: { label: 'لم يدخل بعد', classes: 'bg-gray-100 text-gray-700' },
            in_progress: { label: 'قيد الاختبار', classes: 'bg-amber-100 text-amber-800' },
            passed: { label: 'ناجح', classes: 'bg-green-100 text-green-800' },
            failed: { label: 'راسب', classes: 'bg-red-100 text-red-800' },
            none: { label: '—', classes: 'bg-gray-50 text-gray-400' },
        };

        function renderBadge(el, status, score, total) {
            const ui = uiMap[status] || uiMap.none;
            let label = ui.label;
            if ((status === 'passed' || status === 'failed') && score !== null && score !== undefined) {
                label += ' (' + score + (total ? '/' + total : '') + ')';
            }
            el.dataset.status = status;
            el.className = 'exam-status-badge px-3 py-1 rounded-full text-xs font-medium ' + ui.classes;
            el.textContent = label;
        }

        function renderCertificate(slot, canCertificate, paymentId) {
            if (!slot) return;
            const existing = slot.querySelector('a');
            if (canCertificate) {
                if (!existing) {
                    slot.innerHTML = `<a href="${certBase}/${paymentId}/certificate"
                        class="px-3 py-.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-certificate"></i> الشهادة
                    </a>`;
                }
            } else if (existing) {
                slot.innerHTML = '';
            }
        }

        const poll = () => {
            if (document.hidden) return;
            fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            })
                .then((r) => r.json())
                .then((data) => {
                    const statuses = data.statuses || {};
                    document.querySelectorAll('.exam-status-badge[data-user-id]').forEach((badge) => {
                        const userId = String(badge.dataset.userId);
                        const info = statuses[userId];
                        if (!info) return;
                        if (badge.dataset.status === info.status
                            && badge.textContent.includes(String(info.score ?? ''))) {
                            // still update label if score changed presentation
                        }
                        renderBadge(badge, info.status, info.score, info.total);
                    });
                    document.querySelectorAll('.exam-cert-slot[data-user-id]').forEach((slot) => {
                        const userId = String(slot.dataset.userId);
                        const paymentId = slot.dataset.paymentId;
                        const info = statuses[userId];
                        if (!info || !paymentId) return;
                        renderCertificate(slot, !!info.can_certificate, paymentId);
                    });
                })
                .catch(() => {});
        };

        poll();
        setInterval(poll, 3000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) poll();
        });
    })();
</script>
@endif

@if($course->has_exam && $course->examStatus() === 'not_started')
{{-- Start Exam Modal --}}
<div id="startExamModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 text-center">
            <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                <i class="fas fa-clipboard-list text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">بدء الاختبار</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                سيتم فتح الاختبار لجميع الحضور فوراً. هل أنت متأكد؟
            </p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="button" onclick="closeStartExamModal()"
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-lg font-medium transition">
                إلغاء
            </button>
            <button type="button" onclick="confirmStartExam()"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-bold transition">
                نعم، ابدأ الآن
            </button>
        </div>
    </div>
</div>

<script>
    function openStartExamModal() {
        const modal = document.getElementById('startExamModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeStartExamModal() {
        const modal = document.getElementById('startExamModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
    function confirmStartExam() {
        document.getElementById('startExamForm').submit();
    }
    document.getElementById('startExamModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeStartExamModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeStartExamModal();
    });
</script>
@endif

@if($course->has_exam && $course->examStatus() === 'running')
{{-- End Exam Modal --}}
<div id="endExamModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 text-center">
            <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                <i class="fas fa-stop text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">إنهاء الاختبار</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                سيتم إغلاق الاختبار ولن يتمكن الطلاب من دخوله بعد الآن. هل أنت متأكد؟
            </p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="button" onclick="closeEndExamModal()"
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-lg font-medium transition">
                إلغاء
            </button>
            <button type="button" onclick="confirmEndExam()"
                class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg font-bold transition">
                نعم، أنهِ الاختبار
            </button>
        </div>
    </div>
</div>

<script>
    function openEndExamModal() {
        const modal = document.getElementById('endExamModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeEndExamModal() {
        const modal = document.getElementById('endExamModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
    function confirmEndExam() {
        document.getElementById('endExamForm').submit();
    }
    document.getElementById('endExamModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeEndExamModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeEndExamModal();
    });
</script>
@endif
@endsection