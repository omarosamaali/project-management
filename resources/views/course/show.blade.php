@extends('layouts.user')

@section('title', 'دورة - ' . ($course->name_ar ?? $course->name_en))

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div
                class="mb-6 bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-6 h-6 ml-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="container mx-auto px-4">
            <!-- Back Button -->
            <button onclick="history.back()" class="mb-6 text-black hover:text-red-800 flex items-center gap-2">
                <i class="fa fa-{{ app()->getLocale() == 'ar' ? 'arrow-right' : 'arrow-left' }}"></i>
                العودة للصفحة الرئيسية
            </button>

            <div class="bg-white rounded-xl shadow-2xl border border-gray-300 overflow-hidden">
                <div class="grid md:grid-cols-2 gap-0">

                    <!-- Image Section - ارتفاع ثابت -->
                    <div class="relative h-[500px] md:h-auto md:sticky md:top-0 md:self-start order-1 md:order-none">
                        <div class="h-full md:h-[600px] lg:h-[700px] max-h-[700px]">
                            <img src="{{ Storage::url($course->main_image) }}" alt="{{ $course->name_ar }}"
                                class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Details Section - قابل للسكرول -->
                    <div class="p-8 order-2 md:order-none overflow-y-auto">
                        <!-- Course Name -->
                        <h1 class="text-4xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                            {{ app()->getLocale() == 'en' ? $course->name_en : $course->name_ar }}
                        </h1>

                        <!-- Description -->
                        <div class="description-wrapper">
                            @php
                                $description =
                                    app()->getLocale() == 'en' ? $course->description_en : $course->description_ar;
                                $shortDescription =
                                    mb_strlen($description) > 250
                                        ? mb_substr($description, 0, 250) . '...'
                                        : $description;
                                $showReadMore = mb_strlen($description) > 250;
                            @endphp

                            <p
                                class="text-xl text-gray-600 mb-4 ltr:text-left rtl:text-right whitespace-pre-line leading-relaxed">
                                {{ $shortDescription }}
                            </p>

                            @if ($showReadMore)
                                <button type="button" onclick="openDescriptionModal()"
                                    class="mb-4 inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition shadow-md hover:shadow-lg">
                                    <i class="fas fa-book-open"></i>
                                    <span>{{ __('اضغط هنا لـ قراءة الوصف بالكامل') }}</span>
                                </button>

                                <!-- Modal -->
                                <div id="description-modal"
                                    class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 animate-fadeIn"
                                    onclick="if(event.target === this) closeDescriptionModal()">
                                    <div
                                        class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] overflow-hidden animate-slideUp">
                                        <!-- Header -->
                                        <div
                                            class="flex justify-between items-center p-6 border-b bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                                            <h3 class="text-2xl font-bold flex items-center gap-3">
                                                <i class="fas fa-graduation-cap"></i>
                                                {{ app()->getLocale() == 'en' ? $course->name_en : $course->name_ar }}
                                            </h3>
                                            <button onclick="closeDescriptionModal()"
                                                class="text-white hover:text-gray-200 transition transform hover:scale-110">
                                                <i class="fas fa-times text-2xl"></i>
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="p-8 overflow-y-auto max-h-[calc(85vh-160px)]">
                                            <div
                                                class="text-lg text-gray-700 leading-relaxed whitespace-pre-line ltr:text-left rtl:text-right">
                                                {{ $description }}
                                            </div>
                                        </div>

                                        <!-- Footer -->
                                        <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                                            <button onclick="closeDescriptionModal()"
                                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition shadow-md hover:shadow-lg font-semibold">
                                                <i class="fas fa-check ml-2"></i>
                                                {{ __('فهمت') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <style>
                            @keyframes fadeIn {
                                from {
                                    opacity: 0;
                                }

                                to {
                                    opacity: 1;
                                }
                            }

                            @keyframes slideUp {
                                from {
                                    opacity: 0;
                                    transform: translateY(20px);
                                }

                                to {
                                    opacity: 1;
                                    transform: translateY(0);
                                }
                            }

                            .animate-fadeIn {
                                animation: fadeIn 0.2s ease-out;
                            }

                            .animate-slideUp {
                                animation: slideUp 0.3s ease-out;
                            }

                            /* تثبيت الصورة عند السكرول على الشاشات الكبيرة */
                            @media (min-width: 768px) {
                                .md\:sticky {
                                    position: sticky;
                                    top: 0;
                                }
                            }
                        </style>

                        <script>
                            function openDescriptionModal() {
                                document.getElementById('description-modal').classList.remove('hidden');
                                document.body.style.overflow = 'hidden';
                            }

                            function closeDescriptionModal() {
                                document.getElementById('description-modal').classList.add('hidden');
                                document.body.style.overflow = 'auto';
                            }

                            // إغلاق بـ ESC
                            document.addEventListener('keydown', (e) => {
                                if (e.key === 'Escape') closeDescriptionModal();
                            });
                        </script>

                        <!-- Price Box -->
                        <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg mb-6">
                            <div class="flex items-center justify-between">
                                @if($course->price > 0)
                                <span class="text-2xl font-bold text-green-700">
                                    السعر الكلي
                                </span>
                                <span class="text-4xl font-bold text-green-600 flex items-center gap-2">
                                    {{ number_format($course->price) }}
                                    <img src="{{ asset('assets/images/drhm-icon.svg') }}" class="w-10" alt="">
                                </span>
                                @else
                                <span class="text-2xl font-bold text-green-700">
                                    <i class="fas fa-check text-green-600"></i>
                                    {{ __('messages.free') }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Course Schedule - التواريخ والأوقات -->
                        <div class="space-y-4 mb-6">
                            <!-- Course Duration -->
                            <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <i class="fas fa-calendar-alt text-blue-600 mt-1 text-xl"></i>
                                <div class="ltr:text-left rtl:text-right flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        مدة الدورة
                                    </h3>
                                    <p class="text-gray-600 text-lg font-bold">
                                        {{ $course->actual_course_days }} {{ $course->actual_course_days == 1 ? 'يوم' : 'أيام' }}
                                    </p>
                                </div>
                            </div>

<!-- Start Date & Time -->
@if ($course->start_date)
<div class="flex items-start gap-3 p-4 bg-green-50 rounded-lg border border-green-200">
    <i class="fas fa-play-circle text-green-600 mt-1 text-xl"></i>
    <div class="ltr:text-left rtl:text-right flex-1">
        <h3 class="font-semibold text-gray-800 mb-2">
            تاريخ البداية والنهاية
        </h3>
        <div class="space-y-1">
            <p class="text-gray-700 flex items-center gap-2">
                <i class="fas fa-calendar text-green-500"></i>
                <span class="font-bold">{{ \Carbon\Carbon::parse($course->start_date)->locale('ar')->isoFormat('dddd، D
                    MMMM YYYY') }}</span>
            </p>
            <p class="text-gray-700 flex items-center gap-2">
                <i class="fas fa-calendar text-red-500"></i>
                <span class="font-bold">{{ \Carbon\Carbon::parse($course->end_date)->locale('ar')->isoFormat('dddd، D
                    MMMM YYYY') }}</span>
            </p>
        </div>
    </div>
</div>
@endif

<!-- End Date & Time -->
@if ($course->end_date)
<div class="flex items-start gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
    <i class="fas fa-stop-circle text-red-600 mt-1 text-xl"></i>
    <div class="ltr:text-left rtl:text-right flex-1">
        <h3 class="font-semibold text-gray-800 mb-2">
            وقت البداية والنهاية
        </h3>
        <div class="space-y-1">
            <p class="text-gray-700 flex items-center gap-2">
                <i class="fas fa-clock text-green-500"></i>
                <span class="font-bold">{{ \Carbon\Carbon::parse($course->start_date)->format('h:i A') }}</span>
            </p>
            <p class="text-gray-700 flex items-center gap-2">
                <i class="fas fa-clock text-red-500"></i>
                <span class="font-bold">{{ \Carbon\Carbon::parse($course->end_date)->format('h:i A') }}</span>
            </p>
        </div>
    </div>
</div>
@endif

<!-- Rest Days (أيام الإجازة) -->
@if ($course->rest_days && count($course->rest_days) > 0)
<div class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
    <i class="fas fa-calendar-times text-yellow-600 mt-1 text-xl"></i>
    <div class="ltr:text-left rtl:text-right flex-1">
        <h3 class="font-semibold text-gray-800 mb-2">
            أيام الإجازة
        </h3>
        <div class="flex flex-wrap gap-2">
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
                class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium border border-yellow-300">
                <i class="fas fa-ban text-xs"></i>
                {{ $daysArabic[$day] ?? $day }}
            </span>
            @endforeach
        </div>
    </div>
</div>
@endif                            <!-- Last Registration Date -->
                            @if ($course->last_date)
                                <div class="flex items-start gap-3 p-4 bg-orange-50 rounded-lg border border-orange-200">
                                    <i class="fas fa-hourglass-end text-orange-600 mt-1 text-xl"></i>
                                    <div class="ltr:text-left rtl:text-right flex-1">
                                        <h3 class="font-semibold text-gray-800 mb-2">
                                            آخر موعد للتسجيل
                                        </h3>
                                        <p class="text-gray-700 flex items-center gap-2">
                                            <i class="fas fa-calendar-times text-orange-500"></i>
                                            <span
                                                class="font-bold">{{ \Carbon\Carbon::parse($course->last_date)->locale('ar')->isoFormat('dddd، D MMMM
                                                                                        YYYY') }}</span>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Requirements -->
                        @if (!empty($course->requirements))
                            <div class="flex items-start gap-3 p-4 bg-gray-100 rounded-lg mb-6">
                                <i class="fa fa-box-open h-6 w-6 text-black mt-1"></i>
                                <div class="ltr:text-left rtl:text-right flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-2">
                                        المتطلبات
                                    </h3>
                                    <ul class="space-y-1">
                                        @foreach ($course->requirements as $req)
                                            <li class="text-gray-600 flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                                {{ app()->getLocale() == 'en' ? $req['en'] ?? '' : $req['ar'] ?? '' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Service Badge -->
                        @if ($course->service_id && $course->service)
                            <div
                                class="mb-6 inline-flex items-center gap-2 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-700 px-4 py-2 rounded-lg shadow-sm">
                                @if ($course->service->image)
                                    <img src="{{ asset('storage/' . $course->service->image) }}"
                                        alt="{{ app()->getLocale() == 'ar' ? $course->service->name_ar : $course->service->name_en }}"
                                        class="w-6 h-6 object-contain"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                    <i class="fas fa-tag" style="display: none;"></i>
                                @else
                                    <i class="fas fa-tag"></i>
                                @endif
                                <span class="font-semibold text-sm">
                                    {{ app()->getLocale() == 'ar' ? $course->service->name_ar : $course->service->name_en }}
                                </span>
                            </div>
                        @endif

                        <!-- Features -->
                        @if (!empty($course->features))
                            <div class="mb-8">
                                <h3 class="text-2xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                                    المميزات
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($course->features as $feature)
                                        <div
                                            class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                            <div class="h-2 w-2 bg-black rounded-full flex-shrink-0"></div>
                                            <span class="text-gray-700">
                                                {{ app()->getLocale() == 'en' ? $feature['en'] ?? '' : $feature['ar'] ?? '' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col gap-4">
                            @auth
                                @php
                                    $current_enrolled = \App\Models\Payment::where('course_id', $course->id)
                                        ->where('status', '!=', 'failed')
                                        ->count();

                                    $actual_remaining = ($course->counter ?? 0) - $current_enrolled;

                                    $is_already_in = \App\Models\Payment::where('user_id', auth()->id())
                                        ->where('course_id', $course->id)
                                        ->exists();
                                @endphp

                                <span class="text-xs text-gray-400 italic">
                                    (المقاعد المتاحة حالياً: {{ $actual_remaining }} من أصل {{ $course->counter }})
                                </span>

                                @if ($is_already_in)
                                    <div
                                        class="w-full bg-green-100 border border-green-500 text-green-700 py-4 rounded-lg font-bold text-center">
                                        <i class="fas fa-check-double ml-2"></i>
                                        أنت مشترك بالفعل في هذه الدورة
                                    </div>
                                @elseif($actual_remaining <= 0)
                                    <div
                                        class="w-full bg-red-100 border border-red-500 text-red-700 py-4 rounded-lg font-bold text-center">
                                        <i class="fas fa-exclamation-triangle ml-2"></i>
                                        عذراً، اكتمل العدد ولا توجد مقاعد شاغرة
                                    </div>
                                @else
                                    <button
                                        onclick="handlePayment({{ $course->id }}, {{ $course->price }}, 'course', 'تأكيد الاشتراك')"
                                        class="w-full bg-green-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-green-700 transition-all shadow-lg">
                                        سجل في الدورة الآن
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-full bg-blue-600 text-white py-4 rounded-lg font-bold text-center shadow-md hover:bg-blue-700">
                                    سجل دخول للاشتراك
                                </a>
                            @endauth
                        </div>

                        <!-- Custom Buttons from JSON (public only — needs_login buttons show on my_courses after purchase) -->
                        @php
                            $visibleButtons = collect($course->buttons ?? [])->filter(function ($button) {
                                return empty($button['needs_login']);
                            });
                        @endphp
                        @if ($visibleButtons->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-8">
                                @foreach ($visibleButtons as $button)
                                    <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                                        class="px-6 py-4 rounded-lg text-center text-white font-semibold hover:opacity-90 transition"
                                        style="background-color: {{ $button['color'] ?? '#3B82F6' }}">
                                        {{ app()->getLocale() == 'ar' ? $button['text_ar'] ?? '' : $button['text_en'] ?? '' }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Additional Images -->
                        @if (!empty($course->images))
                            <div class="mt-8 mb-6">
                                <h3 class="text-2xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                                    صور إضافية للدورة
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach ($course->images as $image)
                                        <img onclick="openModal('{{ Storage::url($image) }}')"
                                            src="{{ Storage::url($image) }}" alt="صورة إضافية"
                                            class="w-full h-40 object-cover rounded-lg shadow border cursor-pointer hover:scale-105 transition">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal مشترك للدفع -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4" id="modalTitle">تأكيد الدفع</h3>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span id="priceLabel">السعر:</span>
                    <span id="originalPrice" class="font-bold"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>رسوم الدفع (7.9% + 2 <x-drhm-icon width="12" height="12" />):</span>
                    <span id="fees"></span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-3">
                    <span>الإجمالي:</span>
                    <span id="totalPrice"></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="document.getElementById('paymentModal').classList.add('hidden')"
                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                    إلغاء
                </button>
                <button onclick="proceedPayment()" id="payButton"
                    class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                    متابعة الدفع
                </button>
            </div>
        </div>
    </div>
    @if($related_courses && $related_courses->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div>
            <h5 class="text-center font-bold text-lg lg:text-4xl pb-5"><i class="fas fa-graduation-cap"></i> {{ __('messages.related_courses') }}</h5>
        </div>
    <div id="systems-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($related_courses as $item)
            <div class="system-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col"
                data-service="{{ $item->service_id }}">
            
                <div class="relative h-48 overflow-hidden">
                    <!-- Badge للتمييز بين نظام ودورة -->
                    <span class="absolute top-2 right-2 px-3 py-1 text-xs font-bold rounded-full shadow text-white flex items-center gap-1
                {{ $item->type === 'system' ? 'bg-black' : 'bg-blue-600' }}">
            
                        @if($item->type === 'system')
                        {{-- أيقونة البرمجة / الكود --}}
                        <i class="fas fa-code"></i>
                        <span>خدمة</span>
                        @else
                        {{-- أيقونة الدورة التعليمية --}}
                        <i class="fas fa-graduation-cap"></i>
                        <span>دورة</span>
                        @endif
                    </span>
            
                    <!-- اسم الخدمة -->
                    @if($item->service_id)
                    <span class="absolute top-2 left-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                        {{ $item->service->name_ar }}
                    </span>
                    @endif
            
                    <img src="{{ $item->type === 'system' ? asset($item->main_image) : Storage::url($item->main_image) }}"
                        alt="{{ $item->name_ar }}" class="w-full h-full object-cover">
                </div>
            
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-2xl font-bold text-gray-800 mb-3 ltr:text-left rtl:text-right">
                        {{ app()->getLocale() == 'en' ? $item->name_en : $item->name_ar }}
                    </h3>
            
                    <p class="text-gray-600 mb-4 line-clamp-2 ltr:text-left rtl:text-right">
                        {{ app()->getLocale() == 'en' ? $item->description_en : $item->description_ar }}
                    </p>
            
                    <!-- السعر -->
                    <div class="mb-4">
                        <span class="text-xl font-bold text-black flex gap-2 items-center justify-center">
                            @if($item->price > 0)
                            {{ __('messages.price') }} {{ number_format($item->price) }}
                            <x-drhm-icon width="12" height="14" />
                            @else
                            {{ __('messages.free') }}
                            @endif
                        </span>
                    </div>
            
                    <!-- معلومات إضافية حسب النوع -->
                    @if($item->type === 'system')
                    <p class="text-center text-sm text-gray-500 mb-4">
                        {{ __('messages.get_it_in') }} {{ $item->execution_days_to }} {{ __('messages.day') }}
                    </p>
                    <div
                        class="flex items-center justify-center gap-2 text-gray-600 bg-gray-50 py-2.5 px-4 rounded-lg border border-gray-200">
                        <i class="fa-solid fa-shopping-bag text-red-600 text-lg"></i>
                        @if($item->counter > 0)
                        <span class="text-sm font-medium">
                            {{ __('messages.purchase') }}
                            <span class="font-bold text-red-600">{{ $item->counter }}</span>
                            {{ __('messages.times') }}
                        </span>
                        @else
                        <span class="text-sm font-medium">{{ __('messages.no_purchases') }}</span>
                        @endif
                    </div>
                    @else
                    <p class="text-center text-sm text-gray-500 mb-4">
                        {{ __('messages.course_duration') }} {{ $item->count_days }} {{ __('messages.day') }}
                    </p>
                    <div class="flex flex-col gap-2 mb-4">
                        <div
                            class="flex items-center justify-center gap-2 {{ $item->total_participants <= 3 ? 'text-red-600 bg-red-50 border-red-200' : 'text-orange-600 bg-orange-50 border-orange-200' }} py-2.5 px-4 rounded-lg border shadow-sm">
                            {{-- أيقونة المقاعد تعطي إيحاء بمكان حقيقي --}}
                            <i class="fas fa-chair text-lg"></i>
            
                            <span class="text-sm font-bold">
                                @if($item->total_participants > 0)
                                {{ __('متبقي') }} {{ $item->total_participants }} {{ __('مقعد فقط! سارع بالحجز') }}
                                @else
                                {{ __('نعتذر، اكتملت المقاعد بالكامل') }}
                                @endif
                            </span>
                        </div>
            
                        @if($item->total_participants > 0 && $item->total_participants <= 5) <div class="flex justify-center">
                            <span class="flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                            <span class="text-[10px] text-red-500 mr-2 font-bold uppercase">الإقبال شديد حالياً</span>
                    </div>
                    @endif
                </div>
                 @endif
            
                <div class="mt-auto">
                    <a href="{{ $item->route }}"
                        class="block text-center w-full bg-black text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                        {{ __('messages.show_details') }}
                    </a>
                </div>
            </div>
            </div>
    @endforeach
    </div>
        </div>
    @endif
    <!-- JavaScript -->
    <script>
        let currentItemId = null;
        let currentItemType = null;

        async function handlePayment(itemId, price, type, title = 'تأكيد الدفع') {
            currentItemId = itemId;
            currentItemType = type;

            // ✅ إذا كان السعر 0، اشترك مباشرة مع رسالة جميلة
            if (price == 0) {
                const result = await Swal.fire({
                    title: '🎉 دورة مجانية!',
                    text: 'هذه الدورة مجانية تماماً. هل تريد الاشتراك الآن؟',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، اشترك الآن',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280'
                });

                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري الاشتراك...',
                        html: '<i class="fas fa-spinner fa-spin fa-3x"></i>',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    await proceedFreeEnrollment();
                }
                return;
            }

            // باقي الكود للدورات المدفوعة...
            const fees = (price * 0.079) + 2;
            const total = price + fees;

            document.getElementById('modalTitle').textContent = title;
            document.getElementById('priceLabel').textContent = type === 'course' ? 'سعر الدورة:' : 'سعر النظام:';
            document.getElementById('originalPrice').textContent = price.toFixed(2) + ' AED';
            document.getElementById('fees').textContent = fees.toFixed(2) + ' AED';
            document.getElementById('totalPrice').textContent = total.toFixed(2) + ' AED';

            document.getElementById('paymentModal').classList.remove('hidden');
        }

        async function proceedFreeEnrollment() {
            try {
                const response = await fetch('{{ route('course.payment.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        course_id: currentItemId
                    })
                });

                const data = await response.json();

                if (data.success && data.is_free) {
                    await Swal.fire({
                        title: 'تم الاشتراك بنجاح! 🎉',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'رائع!',
                        confirmButtonColor: '#10B981'
                    });
                    window.location.reload();
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'حدث خطأ أثناء الاشتراك',
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ في الاتصال',
                    icon: 'error',
                    confirmButtonText: 'حسناً'
                });
            }
        }
        async function proceedPayment() {
            const payButton = document.getElementById('payButton');
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';

            let endpoint = '';
            let payload = {};

            if (currentItemType === 'course') {
                endpoint = '{{ route('course.payment.create') }}';
                payload = {
                    course_id: currentItemId
                };
            } else {
                endpoint = '{{ route('payment.create') }}';
                payload = {
                    system_id: currentItemId
                };
            }

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    if (data.is_free) {
                        // دورة مجانية
                        alert(data.message);
                        window.location.reload();
                    } else {
                        // دورة مدفوعة
                        window.location.href = data.payment_url;
                    }
                } else {
                    alert(data.message || 'حدث خطأ أثناء إنشاء الدفع');
                    payButton.disabled = false;
                    payButton.innerHTML = 'متابعة الدفع';
                }
            } catch (error) {
                alert('حدث خطأ في الاتصال');
                payButton.disabled = false;
                payButton.innerHTML = 'متابعة الدفع';
            }
        }

        function openModal(imageUrl) {
            Swal.fire({
                imageUrl: imageUrl,
                imageAlt: "صورة تفصيلية",
                showCloseButton: true,
                showConfirmButton: false,
                background: '#fff',
                padding: '1rem',
            });
        }
    </script>
@endsection
