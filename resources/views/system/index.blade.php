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
            <button type="button" onclick="filterSystems('all')"
                class="service-filter-btn active-filter bg-black text-white px-4 py-3 rounded-lg shadow-md transition-all hover:scale-105 font-semibold text-sm"
                data-id="all">
                {{ __('الكل') }}
            </button>

            @foreach($services as $service)
            <button type="button" onclick="filterSystems({{ $service->id }})"
                class="service-filter-btn bg-white text-gray-700 border-2 border-gray-200 px-4 py-3 rounded-lg shadow-sm hover:shadow-md hover:border-blue-400 hover:bg-blue-50 transition-all hover:scale-105 font-semibold text-sm text-center"
                data-id="{{ $service->id }}">
                {{ app()->getLocale() == 'en' ? $service->name_en : $service->name_ar }}
            </button>
            @endforeach
        </div>
    </div>

    <div id="systems-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($items as $item)
        <div class="system-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col"
            data-service="{{ $item->service_id }}">

            <div class="relative h-48 overflow-hidden">
                <span class="absolute top-2 right-2 px-3 py-1 text-xs font-bold rounded-full shadow text-white flex items-center gap-1 bg-black">
                    <i class="fas fa-code"></i>
                    <span>{{ __('messages.service_badge') }}</span>
                </span>

                @if($item->service_id)
                <span class="absolute top-2 left-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                    {{ $item->service_name_ar }}
                </span>
                @endif

                <img src="{{ asset($item->main_image) }}"
                    alt="{{ $item->name_ar }}" class="w-full h-full object-cover">
            </div>

            <div class="p-6 flex flex-col flex-grow">
                <h3 class="text-2xl font-bold text-gray-800 mb-3 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $item->name_en : $item->name_ar }}
                </h3>

                <p class="text-gray-600 mb-4 line-clamp-2 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $item->description_en : $item->description_ar }}
                </p>

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

                @if($item->type === 'system')
                <p class="text-center text-sm text-gray-500 mb-4">
                    {{ __('messages.get_it_in') }} {{ $item->execution_days_to }} {{ __('messages.day') }}
                </p>
                <div class="flex items-center justify-center gap-2 text-gray-600 bg-gray-50 py-2.5 px-4 rounded-lg border border-gray-200">
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
                    {{ __('messages.get_it_in') }} {{ $item->execution_days }} {{ __('messages.day') }}
                </p>
                <div class="flex items-center justify-center gap-2 text-gray-600 bg-gray-50 py-2.5 px-4 rounded-lg border border-gray-200">
                    <i class="fa-solid fa-shopping-bag text-red-600 text-lg"></i>
                    <span class="text-sm font-medium">{{ __('messages.no_purchases') }}</span>
                </div>
                @endif

                <div class="mt-3">
                    <a href="{{ $item->route }}"
                        class="block text-center w-full bg-black text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition">
                        {{ __('messages.show_details') }}
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 text-gray-500">
            {{ __('messages.no_services_available') }}
        </div>
        @endforelse
    </div>

    <div id="no-systems-msg" class="hidden text-center py-20">
        <p class="text-gray-500 text-lg">{{ __('messages.no_services_for_filter') }}</p>
    </div>
</section>

{{-- Evorq Academy promo --}}
<section class="systems-academy-ad" aria-labelledby="systems-academy-ad-title">
    <div class="systems-academy-ad__inner">
        <div class="systems-academy-ad__copy">
            <p class="systems-academy-ad__eyebrow">{{ __('messages.systems_academy_ad_eyebrow') }}</p>
            <h2 id="systems-academy-ad-title" class="systems-academy-ad__title">{{ __('messages.systems_academy_ad_title') }}</h2>
            <p class="systems-academy-ad__sub">{{ __('messages.systems_academy_ad_sub') }}</p>
            <a href="{{ route('academy.index') }}" class="systems-academy-ad__cta">
                <i class="fas fa-graduation-cap"></i>
                {{ __('messages.systems_academy_ad_cta') }}
            </a>
        </div>
        <div class="systems-academy-ad__visual" aria-hidden="true">
            <div class="systems-academy-ad__orb"></div>
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
    </div>
</section>

<style>
    .active-filter {
        background-color: #000 !important;
        color: white !important;
        border-color: #000 !important;
    }

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

    .systems-academy-ad {
        margin: 1rem auto 3.5rem;
        max-width: 80rem;
        padding: 0 1rem;
    }

    .systems-academy-ad__inner {
        position: relative;
        overflow: hidden;
        display: grid;
        gap: 1.5rem;
        align-items: center;
        border-radius: 1.5rem;
        padding: clamp(1.75rem, 4vw, 2.75rem);
        background:
            radial-gradient(circle at 85% 20%, rgba(236, 72, 153, 0.35), transparent 42%),
            radial-gradient(circle at 10% 90%, rgba(244, 114, 182, 0.22), transparent 45%),
            linear-gradient(135deg, #111827 0%, #1f2937 55%, #0f172a 100%);
        color: #fff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.28);
    }

    @media (min-width: 768px) {
        .systems-academy-ad__inner {
            grid-template-columns: 1.4fr 0.8fr;
        }
    }

    .systems-academy-ad__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin: 0 0 0.75rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        background: rgba(236, 72, 153, 0.18);
        border: 1px solid rgba(244, 114, 182, 0.45);
        color: #f9a8d4;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .systems-academy-ad__title {
        margin: 0 0 0.65rem;
        font-size: clamp(1.45rem, 3vw, 2.1rem);
        font-weight: 900;
        line-height: 1.25;
    }

    .systems-academy-ad__sub {
        margin: 0 0 1.35rem;
        max-width: 36rem;
        color: rgba(226, 232, 240, 0.88);
        font-size: 0.98rem;
        line-height: 1.7;
    }

    .systems-academy-ad__cta {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.85rem 1.35rem;
        border-radius: 999px;
        background: #ec4899;
        color: #fff;
        font-weight: 800;
        font-size: 0.95rem;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(236, 72, 153, 0.35);
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .systems-academy-ad__cta:hover {
        background: #db2777;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(219, 39, 119, 0.4);
    }

    .systems-academy-ad__visual {
        position: relative;
        min-height: 8rem;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 4.5rem;
        color: rgba(251, 207, 232, 0.9);
    }

    @media (min-width: 768px) {
        .systems-academy-ad__visual {
            display: flex;
        }
    }

    .systems-academy-ad__orb {
        position: absolute;
        width: 11rem;
        height: 11rem;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(236, 72, 153, 0.45), transparent 70%);
        filter: blur(2px);
    }

    .systems-academy-ad__visual i {
        position: relative;
        z-index: 1;
    }
</style>

<script>
    function filterSystems(serviceId) {
        const cards = document.querySelectorAll('.system-card');
        const buttons = document.querySelectorAll('.service-filter-btn');
        let visibleCount = 0;

        buttons.forEach(btn => {
            if (btn.getAttribute('data-id') == serviceId) {
                btn.classList.add('active-filter', 'bg-black', 'text-white');
                btn.classList.remove('bg-white', 'text-gray-700');
            } else {
                btn.classList.remove('active-filter', 'bg-black', 'text-white');
                btn.classList.add('bg-white', 'text-gray-700');
            }
        });

        cards.forEach(card => {
            if (serviceId === 'all' || card.getAttribute('data-service') == serviceId) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const msg = document.getElementById('no-systems-msg');
        if (msg) {
            msg.classList.toggle('hidden', visibleCount !== 0);
        }
    }
</script>

@endsection
