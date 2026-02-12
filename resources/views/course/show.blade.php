@extends('layouts.user')

@section('title', 'دورة - ' . ($course->name_ar ?? $course->name_en))

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
    <div class="mb-6 bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md animate-fade-in">
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
            <div class="grid md:grid-cols-2 gap-8">

                <!-- Image Section -->
                <div class="relative h-96 md:h-full order-1 md:order-none">
                    <img src="{{ Storage::url($course->main_image) }}" alt="{{ $course->name_ar }}"
                        class="w-full h-full object-cover" />
                </div>

                <!-- Details Section -->
                <div class="p-8 order-2 md:order-none">
                    <!-- Course Name -->
                    <h1 class="text-4xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                        {{ app()->getLocale() == 'en' ? $course->name_en : $course->name_ar }}
                    </h1>

                    <!-- Description -->
                    <p class="text-xl text-gray-600 mb-6 ltr:text-left rtl:text-right">
                        {{ app()->getLocale() == 'en' ? $course->description_en : $course->description_ar }}
                    </p>

                    <!-- Price Box -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-green-700">
                                السعر الكلي
                            </span>
                            <span class="text-4xl font-bold text-green-600 flex items-center gap-2">
                                {{ number_format($course->price) }}
                                <img src="{{ asset('assets/images/drhm-icon.svg') }}" class="w-10" alt="">
                            </span>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg">
                            <i class="fas fa-clock h-6 w-6 text-blue-600 mt-1"></i>
                            <div class="ltr:text-left rtl:text-right">
                                <h3 class="font-semibold text-gray-800">
                                    مدة الدورة
                                </h3>
                                <p class="text-gray-600">
                                    {{ $course->count_days }} يوم
                                </p>
                            </div>
                        </div>

                        <!-- Requirements -->
                        @if(!empty($course->requirements))
                        <div class="flex items-start gap-3 p-4 bg-gray-100 rounded-lg">
                            <i class="fa fa-box-open h-6 w-6 text-black mt-1"></i>
                            <div class="ltr:text-left rtl:text-right flex-1">
                                <h3 class="font-semibold text-gray-800 mb-2">
                                    المتطلبات
                                </h3>
                                <ul class="space-y-1">
                                    @foreach($course->requirements as $req)
                                    <li class="text-gray-600 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                        {{ app()->getLocale() == 'en' ? ($req['en'] ?? '') : ($req['ar'] ?? '') }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Service Badge -->
                    @if($course->service_id && $course->service)
                    <div
                        class="mb-6 inline-flex items-center gap-2 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-700 px-4 py-2 rounded-lg shadow-sm">
                        @if($course->service->image)
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
                    @if(!empty($course->features))
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                            المميزات
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($course->features as $feature)
                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="h-2 w-2 bg-black rounded-full flex-shrink-0"></div>
                                <span class="text-gray-700">
                                    {{ app()->getLocale() == 'en' ? ($feature['en'] ?? '') : ($feature['ar'] ?? '') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
<div class="flex flex-col gap-4">
    @auth
    @php
    // 1. حساب عدد المشتركين الفعليين الآن من جدول المدفوعات
    $current_enrolled = \App\Models\Payment::where('course_id', $course->id)
    ->where('status', '!=', 'failed')
    ->count();

    // 2. حساب المقاعد المتبقية (السعة الكلية - عدد المشتركين)
    $actual_remaining = ($course->counter ?? 0) - $current_enrolled;

    // 3. التأكد هل المستخدم الحالي مشترك فعلاً؟
    $is_already_in = \App\Models\Payment::where('user_id', auth()->id())
    ->where('course_id', $course->id)
    ->exists();
    @endphp

    <span class="text-xs text-gray-400 italic">
        (المقاعد المتاحة حالياً: {{ $actual_remaining }} من أصل {{ $course->counter }})
    </span>

    @if($is_already_in)
    <div class="w-full bg-green-100 border border-green-500 text-green-700 py-4 rounded-lg font-bold text-center">
        <i class="fas fa-check-double ml-2"></i>
        أنت مشترك بالفعل في هذه الدورة
    </div>
    @elseif($actual_remaining <= 0) <div
        class="w-full bg-red-100 border border-red-500 text-red-700 py-4 rounded-lg font-bold text-center">
        <i class="fas fa-exclamation-triangle ml-2"></i>
        عذراً، اكتمل العدد ولا توجد مقاعد شاغرة
</div>
@else
<button onclick="handlePayment({{ $course->id }}, {{ $course->price }}, 'course', 'تأكيد الاشتراك')"
    class="w-full bg-green-600 text-white py-4 rounded-lg font-bold text-lg hover:bg-green-700 transition-all shadow-lg">
    سجل في الدورة الآن
</button>
@endif

@else
{{-- زائر غير مسجل دخول --}}
<a href="{{ route('login') }}"
    class="w-full bg-blue-600 text-white py-4 rounded-lg font-bold text-center shadow-md hover:bg-blue-700">
    سجل دخول للاشتراك
</a>
@endauth
</div>
                    
                    <!-- Custom Buttons from JSON -->
                    @if(!empty($course->buttons))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-8">
                        @foreach($course->buttons as $button)
                        <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                            class="px-6 py-4 rounded-lg text-center text-white font-semibold hover:opacity-90 transition"
                            style="background-color: {{ $button['color'] ?? '#3B82F6' }}">
                            {{ app()->getLocale() == 'ar' ? ($button['text_ar'] ?? '') : ($button['text_en'] ?? '') }}
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <!-- Additional Images -->
                    @if(!empty($course->images))
                    <div class="mt-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                            صور إضافية للدورة
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($course->images as $image)
                            <img onclick="openModal('{{ Storage::url($image) }}')" src="{{ Storage::url($image) }}"
                                alt="صورة إضافية"
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
                <span>رسوم الدفع (7.9% + 2 درهم):</span>
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

<!-- JavaScript -->
<script>
    let currentItemId = null;
    let currentItemType = null;

    function handlePayment(itemId, price, type, title = 'تأكيد الدفع') {
        currentItemId = itemId;
        currentItemType = type;

        const fees = (price * 0.079) + 2;
        const total = price + fees;

        document.getElementById('modalTitle').textContent = title;
        document.getElementById('priceLabel').textContent = type === 'course' ? 'سعر الدورة:' : 'سعر النظام:';
        document.getElementById('originalPrice').textContent = price.toFixed(2) + ' درهم';
        document.getElementById('fees').textContent = fees.toFixed(2) + ' درهم';
        document.getElementById('totalPrice').textContent = total.toFixed(2) + ' درهم';

        document.getElementById('paymentModal').classList.remove('hidden');
    }

    async function proceedPayment() {
        const payButton = document.getElementById('payButton');
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';

        let endpoint = '';
        let payload = {};

        if (currentItemType === 'course') {
            endpoint = '{{ route("course.payment.create") }}';
            payload = { course_id: currentItemId };
        } else {
            endpoint = '{{ route("payment.create") }}';
            payload = { system_id: currentItemId };
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
                window.location.href = data.payment_url;
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