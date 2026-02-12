@extends('layouts.app')

@section('title', 'تفاصيل الدورة')

@section('content')

@php
$course = $payment->course;
$startDate = \Carbon\Carbon::parse($course->start_date);
$endDate = \Carbon\Carbon::parse($course->end_date);
$now = \Carbon\Carbon::now();

// منطق ظهور الرابط (قبل 30 دقيقة)
$showLink = $now->greaterThanOrEqualTo($startDate->copy()->subMinutes(30)) && $now->lessThanOrEqualTo($endDate);
$isFinished = $now->greaterThan($endDate);
@endphp

<section class="p-3 sm:p-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb first="دوراتي التدريبية" link="{{ route('dashboard.my_courses.index') }}" second="تفاصيل الدورة" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        {{-- الجانب الأيمن: تفاصيل الدورة --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ asset('storage/' . $course->main_image) }}" class="w-full h-full object-cover"
                        alt="{{ $course->name_ar }}">
                    <div class="absolute mt-2 top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                        {{ $course->service?->name_ar }}
                    </div>
                </div>

                <div class="mt-3 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $course->name_ar }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                        {{ $course->description_ar }}
                    </p>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-6">
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-calendar-alt text-blue-500 w-5"></i>
                            <span>تاريخ البدء: <strong>{{ $startDate->format('Y-m-d') }}</strong></span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-clock text-blue-500 w-5"></i>
                            <span>وقت المحاضرة: <strong>{{ $startDate->format('H:i A') }}</strong></span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-map-marker-alt text-blue-500 w-5"></i>
                            <span>نوع الحضور: <strong>{{ $course->location_type == 'online' ? 'أونلاين' : 'في المقر'
                                    }}</strong></span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <i class="fas fa-hourglass-half text-blue-500 w-5"></i>
                            <span>المدة: <strong>{{ $course->count_days }} أيام</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            @if($course->location_type != 'online')
            {{-- قسم تفاصيل المكان (الموقع) --}}
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
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
                </div>
                @endif
            </div>
            @endif
            {{-- قسم المحتوى / اللينك --}}
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg p-6">
                <h3 class="text-lg font-bold text-blue-800 dark:text-blue-300 mb-3">
                    <i class="fas fa-video ml-2"></i> رابط دخول المحاضرة
                </h3>
                @if($course->location_type == 'online')
                @if($showLink)
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-blue-700 dark:text-blue-400">المحاضرة جارية الآن، يمكنك الانضمام مباشرة من
                        خلال الرابط:</p>
                    <a href="{{ $course->online_link }}" target="_blank"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 animate-pulse transition-all">
                        انضم الآن
                    </a>
                </div>
                @elseif($isFinished)
                <p class="text-red-600 font-bold italic text-sm">نعتذر، لقد انتهى موعد هذه الدورة.</p>
                @else
                <div class="flex items-center gap-3 text-amber-700 bg-amber-50 p-3 rounded-lg border border-amber-100">
                    <i class="fas fa-info-circle"></i>
                    <p class="text-sm">سوف يتم تفعيل الرابط تلقائياً يوم <strong>{{ $startDate->format('Y-m-d')
                            }}</strong> الساعة <strong>{{ $startDate->subMinutes(30)->format('H:i A') }}</strong>.</p>
                </div>
                @endif
                @else
                <p class="text-gray-600 dark:text-gray-400">هذه الدورة تتطلب الحضور الشخصي لمقر الأكاديمية.</p>
                @endif
            </div>

            {{-- قسم معرض الصور الفرعية --}}
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
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2">تفاصيل الاشتراك</h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">رقم الفاتورة:</span>
                        <span class="font-mono font-bold">#{{ $payment->payment_id ?? $payment->id }}</span>
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

            {{-- الدعم الفني --}}
            <div class="bg-gray-900 text-white rounded-lg p-6 shadow-md">
                <h4 class="font-bold mb-2">تحتاج مساعدة؟</h4>
                <p class="text-xs text-gray-400 mb-4">إذا واجهت أي مشكلة في الدخول للمحاضرة لا تتردد في التواصل معنا.
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