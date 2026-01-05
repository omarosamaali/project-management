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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($systems as $system)
        <div
            class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col">
            <div class="relative h-48 overflow-hidden">
                @if($system->service_id)
                <span class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded">
                    {{ $system->service->name_ar }}
                </span>
                @endif
                <img src="{{ asset($system->main_image) }}"
                    alt="{{ app()->getLocale() == 'en' ? $system->name_en : $system->name_ar }}"
                    class="w-full h-full object-cover">
            </div>

            <div class="p-6 flex flex-col flex-grow">
                <h3 class="text-2xl font-bold text-gray-800 mb-3 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $system->name_en : $system->name_ar }}
                </h3>
                <p class="text-gray-600 mb-4 line-clamp-2 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $system->description_en : $system->description_ar }}
                </p>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-gray-700 ">
                        <span class="font-bold text-lg flex">{{ $system->price }} <img
                                src="{{ asset('assets/images/drhm-icon.svg') }}" alt="">
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600 ">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $system->execution_days }} {{ $system->execution_days_from }} {{
                            __('messages.from') }}
                            {{ $system->execution_days_to }} {{ __('messages.day') }}</span>
                    </div>
                </div>

                <div class="mb-4 flex-grow">
                    <h4 class="font-semibold text-gray-700 mb-2 ltr:text-left rtl:text-right">{{
                        __('messages.features') }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($system->features as $feature)
                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm">
                            {{ app()->getLocale() == 'en' ? $feature['en'] : $feature['ar'] }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="mt-auto pt-4 space-y-3">
                    <a href="{{ route('system.show', $system) }}"
                        class="block text-center w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-3 rounded-lg font-semibold hover:from-red-700 hover:to-red-800 transition-all shadow-md hover:shadow-lg">
                        {{ __('messages.show_details') }}
                    </a>

                    <div
                        class="flex items-center justify-center gap-2 text-gray-600 bg-gray-50 py-2.5 px-4 rounded-lg border border-gray-200">
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
        </div>
        @endforeach
    </div>
</section>
@endsection