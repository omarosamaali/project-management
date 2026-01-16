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

    <div class="flex flex-wrap justify-center gap-4 mb-10">
        <button onclick="filterSystems('all')"
            class="service-filter-btn active-filter bg-black text-white px-6 py-2 rounded-full shadow-md transition-all"
            data-id="all">
            {{ __('الكل') }}
        </button>
        @foreach($services as $service)
        <button onclick="filterSystems({{ $service->id }})"
            class="service-filter-btn bg-white text-gray-700 border border-gray-200 px-6 py-2 rounded-full shadow-sm hover:bg-gray-50 transition-all"
            data-id="{{ $service->id }}">
            {{ app()->getLocale() == 'en' ? $service->name_en : $service->name_ar }}
        </button>
        @endforeach
    </div>

    <div id="systems-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($systems as $system)
        <div class="system-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col"
            data-service="{{ $system->service_id }}">

            <div class="relative h-48 overflow-hidden">
                @if($system->service_id)
                <span class="absolute top-2 right-2 bg-black text-white text-xs px-2 py-1 rounded">
                    {{ $system->service->name_ar }}
                </span>
                @endif
                <img src="{{ asset($system->main_image) }}" alt="..." class="w-full h-full object-cover">
            </div>

            <div class="p-6 flex flex-col flex-grow">
                <h3 class="text-2xl font-bold text-gray-800 mb-3 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $system->name_en : $system->name_ar }}
                </h3>
                <p class="text-gray-600 mb-4 line-clamp-2 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $system->description_en : $system->description_ar }}
                </p>

                <div class="mt-auto pt-4">
                    <a href="{{ route('system.show', $system) }}"
                        class="block text-center w-full bg-black text-white py-3 rounded-lg font-semibold">
                        {{ __('messages.show_details') }}
                    </a>
                </div>

                <div
                    class="mt-4 flex items-center justify-center gap-2 text-gray-600 bg-gray-50 py-2.5 px-4 rounded-lg border border-gray-200">
                    <i class="fa-solid fa-shopping-bag text-red-600 text-lg"></i>
                    @if($system->counter > 0)
                    <span class="text-sm font-medium">
                        {{ __('messages.purchase') }}
                        <span class="font-bold text-red-600">
                            {{ $system->counter }}
                        </span>
                        {{ __('messages.times') }}
                    </span>
                    @else
                    <span class="text-sm font-medium">
                        {{ __('messages.no_purchases') }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="no-systems-msg" class="hidden text-center py-20">
        <p class="text-gray-500 text-lg">لا توجد أنظمة متاحة لهذا القسم حالياً.</p>
    </div>
</section>

<style>
    .active-filter {
        background-color: #000 !important;
        /* black */
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