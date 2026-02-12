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
                <div class="relative h-96 md:h-full order-1 md:order-none">
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
                $system->features[0][app()->getLocale()] !== null && $system->features[0][app()->getLocale()] !== '')
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
                    {{-- حالة تم الشراء مسبقاً --}}
                    <button disabled
                        class="flex-1 bg-gray-400 text-white py-4 rounded-lg font-bold text-lg cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        تم الشراء بالفعل
                    </button>
                    @elseif($remaining_seats <= 0) {{-- حالة اكتمال المقاعد --}} <button disabled
                        class="flex-1 bg-gray-200 text-gray-500 py-4 rounded-lg font-bold text-lg cursor-not-allowed border border-gray-300 flex items-center justify-center gap-2">
                        <i class="fas fa-user-slash"></i>
                        نعتذر، اكتمل العدد
                        </button>
                        @else
                        {{-- حالة المقاعد متاحة --}}
                        @if($system->system_external == 0)
                        <button onclick="handlePurchase({{ $system->id }}, {{ $system->price }})"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            {{ __('messages.buy_now') }}
                        </button>
                        @else
                        <a href="{{ $system->external_url }}" target="_blank"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-green-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            {{ __('messages.buy_now') }}
                        </a>
                        @endif
                        @endif
                        @else
                        {{-- في حالة عدم تسجيل الدخول --}}
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
</section>

@endsection