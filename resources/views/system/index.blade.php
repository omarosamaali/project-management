@extends('layouts.user')

@section('title', 'الأنظمة')

@section('content')

<x-hero-section />

<x-marquee :logos="$logos" />

<section id="systems" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ __('messages.systems') }}</h1>
        <p class="text-xl text-gray-600">{{ __('messages.system_description') }}</p>
    </div>
<div class="mb-10">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-w-7xl mx-auto">
        <!-- زر الكل -->
        <button onclick="filterSystems('all')"
            class="service-filter-btn active-filter bg-black text-white px-4 py-3 rounded-lg shadow-md transition-all hover:scale-105 font-semibold text-sm"
            data-id="all">
            {{ __('الكل') }}
        </button>

        <!-- أزرار الخدمات -->
        @foreach($services as $service)
        <button onclick="filterSystems({{ $service->id }})"
            class="service-filter-btn bg-white text-gray-700 border-2 border-gray-200 px-4 py-3 rounded-lg shadow-sm hover:shadow-md hover:border-blue-400 hover:bg-blue-50 transition-all hover:scale-105 font-semibold text-sm text-center"
            data-id="{{ $service->id }}">
            {{ app()->getLocale() == 'en' ? $service->name_en : $service->name_ar }}
        </button>
        @endforeach
    </div>
</div>

<style>
    /* زر نشط */
    .active-filter {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        transform: scale(1.05);
        border: 2px solid #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    /* تأثيرات الأزرار */
    .service-filter-btn {
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .service-filter-btn:hover {
        transform: translateY(-2px);
    }

    .service-filter-btn:active {
        transform: scale(0.98);
    }
</style>

<script>
    function filterSystems(serviceId) {
        // إزالة الـ active من كل الأزرار
        document.querySelectorAll('.service-filter-btn').forEach(btn => {
            btn.classList.remove('active-filter', 'bg-black', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });

        // إضافة active للزر المضغوط
        const clickedBtn = document.querySelector(`[data-id="${serviceId}"]`);
        clickedBtn.classList.add('active-filter', 'bg-black', 'text-white');
        clickedBtn.classList.remove('bg-white', 'text-gray-700');

        // تصفية الأنظمة
        const systems = document.querySelectorAll('[data-service-id]');
        systems.forEach(system => {
            if (serviceId === 'all' || system.dataset.serviceId == serviceId) {
                system.style.display = 'block';
                system.classList.add('animate-fade-in');
            } else {
                system.style.display = 'none';
            }
        });
    }
</script>

    <div id="systems-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $item)
        <div class="system-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col"
            data-service="{{ $item->service_id }}">

            <div class="relative h-48 overflow-hidden">
                <span class="absolute top-2 right-2 px-3 py-1 text-xs font-bold rounded-full shadow text-white flex items-center gap-1
                    {{ $item->type === 'system' ? 'bg-black' : 'bg-blue-600' }}">
                    @if($item->type === 'system' || $item->type === 'store')
                    <i class="fas fa-code"></i>
                    <span>خدمة</span>
                    @else
                    <i class="fas fa-graduation-cap"></i>
                    <span>دورة</span>
                    @endif
                </span>

                <!-- اسم الخدمة -->
                @if($item->service_id)
                <span class="absolute top-2 left-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                    {{ $item->service_name_ar }}
                </span>
                @endif

                <img src="{{ $item->type === 'system' || $item->type === 'store' ? asset($item->main_image) : Storage::url($item->main_image) }}"
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
                @elseif($item->type === 'store')
                <p class="text-center text-sm text-gray-500 mb-4">
                    {{ __('messages.get_it_in') }} {{ $item->execution_days }} {{ __('messages.day') }}
                </p>
                <div
                    class="flex items-center justify-center gap-2 text-gray-600 bg-gray-50 py-2.5 px-4 rounded-lg border border-gray-200">
                    <i class="fa-solid fa-shopping-bag text-red-600 text-lg"></i>
                    {{-- @if($item->counter > 0)
                    <span class="text-sm font-medium">
                        {{ __('messages.purchase') }}
                        <span class="font-bold text-red-600">{{ $item->counter ?? 0 }}</span>
                        {{ __('messages.times') }}
                    </span>
                    @else --}}
                    <span class="text-sm font-medium">{{ __('messages.no_purchases') }}</span>
                    {{-- @endif --}}
                </div>
                @else
                <p class="text-center text-sm text-gray-500 mb-4">
                    {{ __('messages.course_duration') }} {{ $item->count_days ?? 0 }} {{ __('messages.day') }}
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

            <div class="mt-3">
                <a href="{{ $item->route }}"
                    class="block text-center w-full bg-black text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                    {{ __('messages.show_details') }}
                </a>
            </div>
        </div>
    </div>
        @endforeach
    </div>

    <div id="no-systems-msg" class="hidden text-center py-20">
        <p class="text-gray-500 text-lg">لا توجد أنظمة أو دورات متاحة لهذا القسم حالياً.</p>
    </div>
    <div id="no-systems-msg" class="hidden text-center py-20">
        <p class="text-gray-500 text-lg">لا توجد أنظمة متاحة لهذا القسم حالياً.</p>
    </div>
</section>

<style>
    .active-filter {
        background-color: #000 !important;
        color: white !important;
        border-color: #000 !important;
    }
</style>

<script>
    function filterSystems(serviceId) {
    const cards = document.querySelectorAll('.system-card');
    const buttons = document.querySelectorAll('.service-filter-btn');
    let visibleCount = 0;

    // تحديث شكل الأزرار
    buttons.forEach(btn => {
        if(btn.getAttribute('data-id') == serviceId) {
            btn.classList.add('active-filter', 'bg-black', 'text-white');
            btn.classList.remove('bg-white', 'text-gray-700');
        } else {
            btn.classList.remove('active-filter', 'bg-black', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700');
        }
    });

    // تصفية الكروت
    cards.forEach(card => {
        if (serviceId === 'all' || card.getAttribute('data-service') == serviceId) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // إظهار رسالة "لا يوجد" إذا كانت النتائج صفر
    const msg = document.getElementById('no-systems-msg');
    if(visibleCount === 0) {
        msg.classList.remove('hidden');
    } else {
        msg.classList.add('hidden');
    }
}
</script>

@endsection