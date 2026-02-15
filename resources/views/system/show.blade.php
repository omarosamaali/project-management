@extends('layouts.user')

@section('title', 'نظام - ' . app()->getLocale() == 'en' ? $system->name_en : $system->name_ar)

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
            {{ __('messages.back_to_systems') }}
        </button>

        <div class="bg-white rounded-xl shadow-2xl border border-gray-300 overflow-hidden">
            <div class="grid md:grid-cols-2 gap-8">

                <!-- Image Section -->
                <div class="h-full md:h-[600px] lg:h-[700px] max-h-[700px]">
                    <img src="{{ asset($system->main_image) }}"
                        alt="{{ app()->getLocale() == 'en' ? $system->name_en : $system->name_ar }}"
                        class="w-full h-full object-cover" />
                </div>

                <!-- Details Section -->
                <div class="p-8 order-2 md:order-none">
                    <!-- System Name -->
                    <h1 class="text-4xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                        {{ app()->getLocale() == 'en' ? $system->name_en : $system->name_ar }}
                    </h1>

                    <!-- System Description -->
                    <p class="text-xl text-gray-600 mb-6 ltr:text-left rtl:text-right">
                        {{ app()->getLocale() == 'en' ? $system->description_en : $system->description_ar }}
                    </p>

                    <!-- Price Box -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-green-700">
                                {{ __('messages.total_price') }}
                            </span>
                            <span class="text-4xl font-bold text-green-600 flex items-center gap-2">
                                {{ $system->price }}
                                <img src="{{ asset('assets/images/drhm-icon.svg') }}" class="w-10" alt="">
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <!-- Duration Card -->
                        <div class="flex items-start gap-3 p-4 bg-red-50 rounded-lg">
                            <i class="fa fa-clock h-6 w-6 text-black mt-1"></i>
                            <div class="ltr:text-left rtl:text-right">
                                <h3 class="font-semibold text-gray-800">
                                    {{ __('messages.execution_duration') }}
                                </h3>
                                <p class="text-gray-600">
                                    {{ __('messages.from_to_days', [
                                    'from' => $system->execution_days_from,
                                    'to' => $system->execution_days_to
                                    ]) }}
                                </p>
                            </div>
                        </div>

                        <!-- Requirements Card -->
                        @if(!empty($system->requirements) && isset($system->requirements[0][app()->getLocale()]) &&
                        $system->requirements[0][app()->getLocale()] !== null &&
                        $system->requirements[0][app()->getLocale()] !== '')
                        <div class="flex items-start gap-3 p-4 bg-gray-100 rounded-lg">
                            <i class="fa fa-box-open h-6 w-6 text-black mt-1"></i>
                            <div class="ltr:text-left rtl:text-right flex-1">
                                <h3 class="font-semibold text-gray-800 mb-2">
                                    {{ __('messages.requirements') }}
                                </h3>
                                <ul class="space-y-1">
                                    @foreach($system->requirements as $requirement)
                                    <li class="text-gray-600 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                        {{ app()->getLocale() == 'en' ? $requirement['en'] : $requirement['ar'] }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($system->service_id && $system->service)
                    <div
                        class="mb-6 inline-flex items-center gap-2 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-700 px-4 py-2 rounded-lg shadow-sm">
                        @if($system->service->image)
                        <img src="{{ asset('storage/' . $system->service->image) }}"
                            alt="{{ app()->getLocale() == 'ar' ? $system->service->name_ar : $system->service->name_en }}"
                            class="w-6 h-6 object-contain"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                        <i class="fas fa-tag" style="display: none;"></i>
                        @else
                        <i class="fas fa-tag"></i>
                        @endif
                        <span class="font-semibold text-sm">
                            {{ app()->getLocale() == 'ar' ? $system->service->name_ar : $system->service->name_en }}
                        </span>
                    </div>
                    @endif

                    <!-- Features List -->
                    @if(!empty($system->features) && isset($system->features[0][app()->getLocale()]) &&
                    $system->features[0][app()->getLocale()] !== null && $system->features[0][app()->getLocale()] !==
                    '')
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                            {{ __('messages.all_features') }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($system->features as $feature)
                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="h-2 w-2 bg-black rounded-full flex-shrink-0"></div>
                                <span class="text-gray-700">
                                    {{ app()->getLocale() == 'en' ? $feature['en'] : $feature['ar'] }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex gap-4">
                        @auth
                        @if($is_purchased)
                        <button
                            class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white py-4 rounded-lg font-bold text-lg hover:from-red-700 hover:to-red-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            تم الشراء
                        </button>
                        @else
                        @if($system->system_external == 0)
                        <button onclick="handlePurchase({{ $system->id }}, {{ $system->price }})"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            {{ __('messages.buy_now') }}
                        </button>
                        @else
                        <a href="{{ $system->external_url }}" target="_blank" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-lg font-bold text-lg
                            hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center
                            gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            {{ __('messages.buy_now') }}
                        </a>
                        @endif
                        <!-- Modal للتأكيد -->
                        <div id="purchaseModal"
                            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                                <h3 class="text-xl font-bold mb-4">{{ __('messages.confirm_purchase') }}</h3>

                                <div class="space-y-3 mb-6">
                                    <div class="flex justify-between">
                                        <span>{{ __('messages.original_price') }}:</span>
                                        <span id="originalPrice" class="font-bold"></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span class="items-center flex gap-1">{{ __('messages.payment_fees') }} ( 7.9% +
                                            2
                                            <x-drhm-icon width="12" height="12" />)
                                        </span>
                                        <span id="fees"></span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold border-t pt-3">
                                        <span>{{ __('messages.total') }}:</span>
                                        <span id="totalPrice"></span>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <button onclick="document.getElementById('purchaseModal').classList.add('hidden')"
                                        class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                                        {{ __('messages.cancel') }}
                                    </button>
                                    <button onclick="proceedPayment()" id="payButton"
                                        class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                                        {{ __('messages.proceed_payment') }}
                                    </button>
                                </div>
                            </div>
                        </div>
<script>
    // اجعل المتغيرات والدوال معرفة عالمياً (Global) لضمان وصول الأزرار إليها
    let currentSystemId = null;

    function handlePurchase(systemId, price) {
        console.log("Purchase handled for ID:", systemId); // للتأكد في الكونسول
        currentSystemId = systemId;
        
        // حساب الرسوم (7.9% + 2 درهم)
        const fees = (parseFloat(price) * 0.079) + 2;
        const total = parseFloat(price) + fees;
        
        // تحديث النصوص في الـ Modal
        document.getElementById('originalPrice').textContent = parseFloat(price).toFixed(2) + ' AED';
        document.getElementById('fees').textContent = fees.toFixed(2) + ' AED';
        document.getElementById('totalPrice').textContent = total.toFixed(2) + ' AED';
        
        // إظهار المودال
        const modal = document.getElementById('purchaseModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    async function proceedPayment() {
        const payButton = document.getElementById('payButton');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            alert('CSRF Token not found! Please check your head tag.');
            return;
        }

        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
        
        try {
            const response = await fetch('/payment/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    system_id: currentSystemId,
                    type: 'system' // ضروري جداً لتخطي الـ validation
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                window.location.href = data.payment_url;
            } else {
                alert(data.message || 'حدث خطأ في عملية الدفع');
                payButton.disabled = false;
                payButton.innerHTML = 'متابعة الدفع';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('فشل الاتصال بالخادم');
            payButton.disabled = false;
            payButton.innerHTML = 'متابعة الدفع';
        }
    }
</script>                        @endif
                        @else
                        <a href="{{ route('login') }}"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            {{ __('messages.buy_now') }}
                        </a>
                        @endauth
                    </div>

                    @if(!empty($system->buttons))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-4">
                        @foreach($system->buttons as $button)
                        <a href="{{ $button['link'] ?? '#' }}" target="_blank"
                            class="px-6 py-4 rounded-lg text-center text-white font-semibold hover:opacity-90 transition"
                            style="background-color: {{ $button['color'] ?? '#3B82F6' }}">
                            {{ app()->getLocale() == 'ar' ? $button['text_ar'] : $button['text_en'] }}
                        </a>
                        @endforeach
                    </div>
                    @endif

                    @if($system->images && count($system->images) > 0)
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 ltr:text-left rtl:text-right">
                            {{ __('messages.additional_images') }}
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($system->images as $image)
                            <img onclick="openModal('{{ asset($image) }}')" id="image-{{ $image }}"
                                src="{{ asset($image) }}" alt="صورة إضافية"
                                class="w-full h-40 object-cover rounded-lg shadow border cursor-pointer hover:scale-105 transition">
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <script>
                        function openModal($image) {
                            
                            Swal.fire({
                                imageUrl: $image,
                                imageWidth: 400,
                                imageHeight: 400,
                                imageAlt: "Custom image"
                            });
                        }
                    </script>
                </div>
            </div>
        </div>

    </div>
    @if($related_systems && count($related_systems) > 0)
    <div class="mt-5 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div>
            <h5 class="text-center font-bold text-lg lg:text-4xl pb-5"><i class="fas fa-code"></i> {{
                __('messages.related_systems') }}</h5>
        </div>
        <div id="systems-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($related_systems as $item)
            <div class="system-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col"
                data-service="{{ $item->service_id }}">

                <div class="relative h-48 overflow-hidden">
                    <!-- Badge للتمييز بين نظام ودورة -->
                    <span class="absolute top-2 right-2 px-3 py-1 text-xs font-bold rounded-full shadow text-white flex items-center gap-1
                        bg-blue-600">

                        <i class="fas fa-code"></i>
                        <span>خدمة</span>
                    </span>

                    <!-- اسم الخدمة -->
                    @if($item->service_id)
                    <span class="absolute top-2 left-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                        {{ $item->service->name_ar }}
                    </span>
                    @endif

                    <img src="{{ asset($system->main_image) }}" alt="{{ $item->name_ar }}"
                        class="w-full h-full object-cover">
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

                        @if($item->total_participants > 0 && $item->total_participants <= 5) <div
                            class="flex justify-center">
                            <span class="flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                            <span class="text-[10px] text-red-500 mr-2 font-bold uppercase">الإقبال شديد حالياً</span>
                    </div>
                    @endif
                </div> @endif

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

</section>

@endsection