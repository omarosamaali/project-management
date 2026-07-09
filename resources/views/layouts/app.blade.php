<!DOCTYPE html>
@php $locale = app()->getLocale(); $isRtl = $locale === 'ar'; $dir = $isRtl ? 'rtl' : 'ltr'; @endphp
<html class="light" lang="{{ $locale }}" dir="{{ $dir }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @php $faviconPath = \App\Models\Setting::get('favicon_path'); @endphp
    <link rel="icon" type="image/x-icon" href="{{ $faviconPath ? Storage::url($faviconPath) : asset('assets/images/fav-icon.webp') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/css/intlTelInput.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        {{-- Navbar --}}
        <nav class="bg-white border-b border-gray-200 px-4 py-2 fixed left-0 right-0 top-0 z-50 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                {{-- Right: toggle + logo --}}
                <div class="flex items-center gap-3">
                    <button data-drawer-target="drawer-navigation" data-drawer-toggle="drawer-navigation"
                        aria-controls="drawer-navigation"
                        class="p-2 text-gray-500 rounded-lg cursor-pointer md:hidden hover:text-gray-700 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <a href="{{ session('company_code') ? route('co.dashboard', ['company' => session('company_code')]) : route('dashboard') }}" class="flex items-center gap-2 {{ $isRtl ? 'md:mr-52' : 'md:ml-52' }}">
                        @php
                            $navCompany = $currentCompany ?? (session('company_code') ? \App\Models\Company::where('code', session('company_code'))->first() : null);
                            $navLogo = \App\Support\CompanyBranding::logoUrl($navCompany)
                        ?? \App\Support\CompanyBranding::settingLogoUrl()
                        ?? \App\Support\CompanyBranding::fallbackLogoUrl();
                        @endphp
                        <img src="{{ $navLogo }}" class="h-14 object-contain" alt="Pet Clinic">
                    </a>
                </div>

                {{-- Center: attendance widget --}}
                @if(!empty($attendanceWidget['show']))
                <div class="hidden md:flex flex-1 min-w-0 justify-center px-2 max-w-3xl">
                    <x-attendance-widget :widget="$attendanceWidget" />
                </div>
                @endif

                {{-- Left: actions --}}
                <div class="flex items-center gap-2">
                    {{-- Language switcher --}}
                    @php $currentLang = $locale; @endphp
                    <div class="flex items-center rounded-lg border border-gray-200 overflow-hidden text-xs font-bold">
                        <a href="{{ route('lang.switch', 'ar') }}"
                            class="px-2.5 py-1.5 transition {{ $currentLang === 'ar' ? 'bg-gray-800 text-white' : 'text-gray-500 hover:bg-gray-100' }}">ع</a>
                        <a href="{{ route('lang.switch', 'en') }}"
                            class="px-2.5 py-1.5 transition {{ $currentLang === 'en' ? 'bg-gray-800 text-white' : 'text-gray-500 hover:bg-gray-100' }}">EN</a>
                    </div>

                    {{-- External site icon --}}
                    <a href="{{ route('landing.index') }}" title="{{ __('messages.external_site') }}"
                        class="p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>

                    <x-dropdown align="left" width="52">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl transition text-sm">
                                <div class="w-7 h-7 rounded-full overflow-hidden flex items-center justify-center font-black text-xs text-white shrink-0" style="background:#104776;">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Storage::url(Auth::user()->avatar) }}" class="w-full h-full object-fill" alt="">
                                    @else
                                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                <svg class="h-3.5 w-3.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="py-3 px-4 border-b border-gray-100 dark:border-gray-700">
                                <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-gray-500 truncate">{{ Auth::user()->email }}</span>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')">
                                <svg class="inline h-4 w-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('messages.my_profile') }}
                            </x-dropdown-link>
                            @php
                                $logoutAction = session('company_code')
                                    ? route('company.logout', session('company_code'))
                                    : route('logout');
                            @endphp
                            <form method="POST" class="border-t border-gray-100 dark:border-gray-700" action="{{ $logoutAction }}">
                                @csrf
                                <x-dropdown-link :href="$logoutAction" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <svg class="inline h-4 w-4 ml-1 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    {{ __('messages.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside style="padding-bottom: {{ in_array(auth()->user()?->role, ['admin', 'super_admin'], true) ? '115px' : '15px' }}; background:#ffffff; border-{{ $isRtl ? 'left' : 'right' }}:1px solid #e2e8f0;"
            class="fixed top-0 {{ $isRtl ? 'right-0' : 'left-0' }} z-40 w-64 h-screen pt-16 transition-transform {{ $isRtl ? 'translate-x-full md:translate-x-0' : '-translate-x-full md:translate-x-0' }}"
            aria-label="Sidenav" id="drawer-navigation">
            <div class="overflow-y-auto py-4 px-3 h-full">

                @php
                    $dashHome = session('company_code')
                        ? route('co.dashboard', ['company' => session('company_code')])
                        : route('dashboard');
                    $sidebarCompany = $currentCompany ?? \App\Support\ClinicOnboarding::companyForUser();
                    $sidebarLogoUrl = \App\Support\CompanyBranding::logoUrl($sidebarCompany)
                        ?? \App\Support\CompanyBranding::settingLogoUrl()
                        ?? \App\Support\CompanyBranding::fallbackLogoUrl();
                    $stgUrl = function (string $tab, string $hash = '') {
                        return coroute('dashboard.site-settings.index') . '?tab=' . urlencode($tab) . ($hash ? '#'.$hash : '');
                    };
                @endphp

                <a href="{{ $dashHome }}" class="flex items-center justify-center rounded-xl border border-gray-100 bg-white px-3 py-3 mb-3">
                    <img src="{{ $sidebarLogoUrl }}" class="h-12 w-auto object-contain" alt="Pet Clinic">
                </a>

                {{-- Search --}}
                @php
                    $onboardingCompany = \App\Support\ClinicOnboarding::companyForUser();
                    $clinicOnboardingInProgress = \App\Support\ClinicOnboarding::isInProgress($onboardingCompany);
                    $clinicAwaitingActivation = \App\Support\ClinicOnboarding::awaitingActivation($onboardingCompany);
                    $clinicMinimalSidebar = \App\Support\ClinicOnboarding::useMinimalSidebar($onboardingCompany);
                @endphp
                @if(! $clinicMinimalSidebar)
                <div class="relative mb-3">
                    <input type="text" id="searchInput" name="search"
                        class="w-full text-sm rounded-xl px-3 py-2 pr-9 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="background:#f8fafc; border:1px solid #e2e8f0; color:#334155;"
                        placeholder="{{ __('messages.search_placeholder') }}">
                    <svg class="absolute right-3 top-2.5 h-4 w-4" style="color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                @endif

                @php
                if (!function_exists('sidebarLink')) {
                    function sidebarLink($route, $label, $icon, $active = false) {
                        $pawIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="4" r="2"/><circle cx="18" cy="8" r="2"/><circle cx="20" cy="16" r="2"/><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/></svg>';
                        if ($active) {
                            $style = 'background:#336cfa; color:#fff;';
                            $class = 'flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition';
                        } else {
                            $style = 'color:#475569;';
                            $class = 'flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition sidebar-link-hover';
                        }
                        return '<a href="'.$route.'" class="'.$class.'" style="'.$style.'">'.$pawIcon.'<span>'.$label.'</span></a>';
                    }
                }
                if (!function_exists('sidebarGroup')) {
                    function sidebarGroup($label) {
                        return '<p class="text-[10px] font-bold uppercase tracking-widest px-3 pt-4 pb-1" style="color:#f45d5d;">'.$label.'</p>';
                    }
                }
                @endphp

                <ul class="space-y-0.5">
                    @php
                        $r = Route::currentRouteName() ?? '';
                        $isClient       = auth()->user()?->role === 'client';
                        $isReceptionist = auth()->user()?->role === 'receptionist';
                        $isDoctor       = in_array(auth()->user()?->role, ['doctor', 'assistant']);
                    @endphp

                    @if($clinicOnboardingInProgress && $onboardingCompany)
                    {{-- ══ Onboarding — تأكيد الحساب / الدفع / بانتظار التفعيل ══ --}}
                    {!! sidebarGroup(__('messages.group_field_archive')) !!}
                    @if(\App\Support\ClinicOnboarding::needsVerification($onboardingCompany))
                    <li>{!! sidebarLink(route('co.verify-account', ['company' => $onboardingCompany->code]), $locale === 'ar' ? 'تأكيد الحساب' : 'Verify account', '', str_contains($r, 'verify-account')) !!}</li>
                    @elseif($clinicAwaitingActivation)
                    <li>{!! sidebarLink(route('co.subscription.index', ['company' => $onboardingCompany->code]), __('messages.nav_subscription'), '', str_contains($r, 'subscription')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.platform-support.index'), __('messages.nav_technical_support'), '', str_contains($r, 'platform-support')) !!}</li>
                    <li>{!! sidebarLink(route('profile.edit'), __('messages.my_profile'), '', $r === 'profile.edit') !!}</li>
                    @elseif(\App\Support\ClinicOnboarding::needsPayment($onboardingCompany))
                    <li>{!! sidebarLink(route('co.subscription.index', ['company' => $onboardingCompany->code]), __('messages.nav_subscription'), '', str_contains($r, 'subscription')) !!}</li>
                    @endif

                    @else

                    @if($isReceptionist)
                    {{-- ══ Receptionist menu ══ --}}
                    <li>{!! sidebarLink(route('dashboard.clients.index'), __('messages.nav_clients'), 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', str_contains($r, 'clients')) !!}</li>

                    {!! sidebarGroup(__('messages.group_customer_service')) !!}
                    <li>{!! sidebarLink(route('dashboard.clinic_requests.index'), __('messages.nav_requests'), 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', str_contains($r, 'clinic_requests') && !str_contains($r, 'appointments')) !!}</li>
                    <li>{!! sidebarLink(route('dashboard.clinic_requests.appointments'), __('messages.nav_daily_appointments'), 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', str_contains($r, 'appointments')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.send-notification.index'), __('messages.nav_send_notification'), 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', str_contains($r, 'send-notification')) !!}</li>

                    {!! sidebarGroup(__('messages.group_invoices')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.clinic_invoices.index'), __('messages.nav_all_invoices'), 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z', str_contains($r, 'clinic_invoices')) !!}</li>

                    {!! sidebarGroup(__('messages.group_consultants')) !!}
                    <li>{!! sidebarLink(route('dashboard.doctor-schedules.index'), __('messages.nav_doctor_schedules'), 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', str_contains($r, 'doctor-schedules')) !!}</li>

                    @elseif($isDoctor)
                    {{-- ══ Doctor / Assistant menu ══ --}}
                    {!! sidebarGroup(__('messages.group_my_appointments')) !!}
                    <li>{!! sidebarLink(route('dashboard.clinic_requests.appointments'), __('messages.nav_daily_appointments'), 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', str_contains($r, 'appointments')) !!}</li>
                    <li>{!! sidebarLink(route('dashboard.clinic_requests.index'), __('messages.nav_all_my_requests'), 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', str_contains($r, 'clinic_requests') && !str_contains($r, 'appointments')) !!}</li>
                    {!! sidebarGroup(__('messages.group_my_account')) !!}
                    <li>{!! sidebarLink(route('dashboard.my-notifications.index'), __('messages.nav_my_notifications'), 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', str_contains($r, 'my-notifications')) !!}</li>
                    <li>{!! sidebarLink(route('profile.edit'), __('messages.my_profile'), 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', $r === 'profile.edit') !!}</li>

                    @elseif($isClient)
                    {{-- ══ Client simplified menu ══ --}}
                    {!! sidebarGroup(__('messages.group_my_account')) !!}
                    <li>{!! sidebarLink(route('dashboard.clients.pets.index', ['client' => auth()->id()]), __('messages.nav_my_pets'), 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', str_contains($r, 'pets.index')) !!}</li>
                    <li>{!! sidebarLink(route('dashboard.clients.pets.create', ['client' => auth()->id()]), __('messages.nav_add_pet'), 'M12 4v16m8-8H4', str_contains($r, 'pets.create')) !!}</li>
                    <li>{!! sidebarLink(route('dashboard.clinic_requests.index'), __('messages.nav_my_requests'), 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', str_contains($r, 'clinic_requests')) !!}</li>
                    <li>{!! sidebarLink(route('dashboard.my-notifications.index'), __('messages.nav_my_notifications'), 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', str_contains($r, 'my-notifications')) !!}</li>
                    {!! sidebarGroup(__('messages.group_communication')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.support.index'), __('messages.nav_my_messages'), 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', str_contains($r, 'support')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.tickets.index'), __('messages.nav_support_tickets'), 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', str_contains($r, 'tickets')) !!}</li>
                    <li>{!! sidebarLink(route('profile.edit'), __('messages.nav_my_profile_short'), 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', $r === 'profile.edit') !!}</li>

                    @else
                    {{-- ══ Full Admin menu ══ --}}
                    @if(in_array(auth()->user()?->role, ['admin', 'super_admin']))
                    <li>{!! sidebarLink($dashHome, __('messages.nav_dashboard'), 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', $r === 'dashboard' || $r === 'co.dashboard') !!}</li>
                    @endif
                    <li>{!! sidebarLink(coroute('dashboard.clients.index'), __('messages.nav_clients'), 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', str_contains($r, 'clients')) !!}</li>
                    {!! sidebarGroup(__('messages.group_customer_service')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.clinic_requests.index'), __('messages.nav_requests'), 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', str_contains($r, 'clinic_requests') && !str_contains($r, 'appointments')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.medical_records.all'), __('messages.nav_medical_records'), 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', str_contains($r, 'medical_records')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.clinic_requests.appointments'), __('messages.nav_daily_appointments'), 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', str_contains($r, 'appointments')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.send-notification.index'), __('messages.nav_send_notification'), 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', str_contains($r, 'send-notification')) !!}</li>
                    @if(in_array(auth()->user()?->role, ['admin', 'super_admin']))
                    {!! sidebarGroup(__('messages.group_invoices')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.clinic_invoices.index'), __('messages.nav_all_invoices'), 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z', str_contains($r, 'clinic_invoices')) !!}</li>
                    @endif

                    @if(!$isClient && !$isReceptionist && !$isDoctor)
                    {!! sidebarGroup(__('messages.group_users')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.doctors.index'), __('messages.nav_doctors'), 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', str_contains($r, 'doctors')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.assistants.index'), __('messages.nav_assistants'), 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', str_contains($r, 'assistants')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.employees.index'), __('messages.nav_employees'), 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6a4 4 0 11-8 0 4 4 0 018 0zm-7 8a7 7 0 0114 0v1H3v-1a7 7 0 017-8z', str_contains($r, 'employees')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.doctor-schedules.index'), __('messages.nav_doctor_schedules'), 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', str_contains($r, 'doctor-schedules')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.staff.index'), __('messages.nav_staff'), 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', str_contains($r, 'staff')) !!}</li>
                    @if(auth()->user()?->role === 'super_admin')
                    {!! sidebarGroup(__('messages.group_settings')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.site-settings.index'), __('messages.nav_site_settings'), 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', str_contains($r, 'site-settings')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.our_services.index'), __('messages.nav_our_services'), 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', str_contains($r, 'our_services')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.blog.index'), __('messages.nav_blog'), 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', str_contains($r, 'dashboard.blog')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.kb_categories.index'), __('messages.nav_service_categories'), 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z', str_contains($r, 'kb_categories')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.about-page.index'), __('messages.nav_about_us'), 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', str_contains($r, 'about-page')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.logos.index'), __('messages.nav_pride_clients'), 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', str_contains($r, 'logos')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.animal_types.index'), __('messages.nav_animal_types'), '', str_contains($r, 'animal_types')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.holidays.index'), __('messages.nav_work_hours'), 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', str_contains($r, 'holidays')) !!}</li>
                    @endif

                    @if(auth()->user()?->role === 'admin')
                    <li>{!! sidebarLink(coroute('dashboard.blog.index'), __('messages.nav_blog'), 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', str_contains($r, 'dashboard.blog')) !!}</li>
                    @endif

                    {!! sidebarGroup(__('messages.group_financial')) !!}
                    <li>{!! sidebarLink(route('dashboard.profits'), __('messages.nav_profits'), 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', $r === 'dashboard.profits') !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.admin_expenses.index'), __('messages.nav_expenses'), 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', str_contains($r, 'admin_expenses')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.expense_categories.index'), __('messages.nav_expense_categories'), 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z', str_contains($r, 'expense_categories')) !!}</li>
                    <li>{!! sidebarLink(route('dashboard.service_offers.index'), __('messages.nav_offers'), 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z', str_contains($r, 'service_offers') || str_contains($r, 'service-offers')) !!}</li>
                    <li>{!! sidebarLink(route('dashboard.reports'), __('messages.nav_reports'), 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', $r === 'dashboard.reports') !!}</li>

                    {!! sidebarGroup(__('messages.group_inventory')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.stock_receipts.index'), __('messages.nav_warehouse'), 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', str_contains($r, 'stock_receipts')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.products.index'), __('messages.nav_products'), 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', str_contains($r, 'products')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.stock_dispatches.index'), __('messages.nav_stock_dispatch'), 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1', str_contains($r, 'stock_dispatches')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.suppliers.index'), __('messages.nav_suppliers'), 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z', str_contains($r, 'suppliers')) !!}</li>

                    {!! sidebarGroup(__('messages.group_communication')) !!}
                    <li>{!! sidebarLink(coroute('dashboard.support.index'), __('messages.nav_correspondence'), 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', str_contains($r, 'support')) !!}</li>
                    <li>{!! sidebarLink(coroute('dashboard.tickets.index'), __('messages.nav_support_tickets'), 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', str_contains($r, 'tickets')) !!}</li>

                    @endif

                    @endif {{-- end admin @else --}}

                    {{-- أرشيف الميدان — للأدمن والسوبر أدمن بعد إتمام التفعيل --}}
                    @if(in_array(auth()->user()?->role, ['admin', 'super_admin']) && ! $clinicMinimalSidebar)
                    <div class="fixed bottom-0 {{ $isRtl ? 'right-0' : 'left-0' }} w-64 px-3 py-2 space-y-0.5" style="background:#f6f4f1;
                    border-top:1px solid #e2e8f0; color:#918a6c;">
                        <p class="text-[10px] font-bold uppercase tracking-widest px-2 pb-1" style="color:#918a6c;">{{ __('messages.group_field_archive') }}</p>
                        @if($onboardingCompany)
                        {!! sidebarLink(route('co.subscription.index', ['company' => $onboardingCompany->code]), __('messages.nav_subscription'), '', str_contains($r, 'subscription')) !!}
                        @else
                        {!! sidebarLink(route('dashboard.subscription.index'), __('messages.nav_subscription'), '', str_contains($r, 'subscription')) !!}
                        @endif
                        {!! sidebarLink(coroute('dashboard.platform-support.index'), __('messages.nav_technical_support'), '', str_contains($r, 'platform-support')) !!}
                    </div>
                    @endif

                    @endif {{-- end onboarding vs full menu --}}

                </ul>

                @if(!empty($attendanceWidget['show']))
                <div
                    class="md:hidden mt-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">{{ __('messages.attendance_daily') }}</p>
                    <x-attendance-widget :widget="$attendanceWidget" :compact="true" />
                </div>
                @endif

                {{-- أزرار الموبايل فقط: الموقع الخارجي + تسجيل الخروج --}}
                <div class="md:hidden mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <a href="{{ route('landing.index') }}"
                        class="flex items-center gap-2 p-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-paw-print h-5 w-5 ml-2" aria-hidden="true"><circle cx="11" cy="4" r="2"></circle><circle cx="18" cy="8" r="2"></circle><circle cx="20" cy="16" r="2"></circle><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"></path></svg>
                        <span>{{ __('messages.external_site') }}</span>
                    </a>

                    <form method="POST" action="{{ session('company_code') ? route('company.logout', session('company_code')) : route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 p-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-paw-print h-5 w-5 ml-2" aria-hidden="true"><circle cx="11" cy="4" r="2"></circle><circle cx="18" cy="8" r="2"></circle><circle cx="20" cy="16" r="2"></circle><path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"></path></svg>
                            <span>{{ __('messages.logout') }}</span>
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        {{-- Main Content --}}
        <main class="p-4 {{ $isRtl ? 'md:mr-60' : 'md:ml-60' }} h-auto pt-16">
            {{-- Active Announcements --}}
            @php
                $authUser = auth()->user();
                $activeAnnouncements = \App\Models\Announcement::active()
                    ->where(function($q) use ($authUser) {
                        $q->where('target', 'all')
                          ->orWhere('target', $authUser->role === 'client' ? 'clients' : 'staff');
                    })
                    ->latest()
                    ->get();
                $annBannerColors = [
                    'info'    => 'bg-blue-50 border-blue-200 text-blue-800 [&_i]:text-blue-500',
                    'success' => 'bg-green-50 border-green-200 text-green-800 [&_i]:text-green-500',
                    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800 [&_i]:text-yellow-500',
                    'danger'  => 'bg-red-50 border-red-200 text-red-800 [&_i]:text-red-500',
                ];
                $annBannerIcons = [
                    'info'    => 'fa-info-circle',
                    'success' => 'fa-check-circle',
                    'warning' => 'fa-exclamation-triangle',
                    'danger'  => 'fa-exclamation-circle',
                ];
            @endphp
            @foreach($activeAnnouncements as $ann)
            <div class="mb-3 border rounded-xl px-4 py-3 flex items-start gap-3 {{ $annBannerColors[$ann->type] ?? $annBannerColors['info'] }}">
                <i class="fas {{ $annBannerIcons[$ann->type] ?? 'fa-info-circle' }} mt-0.5 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold">{{ $ann->title }}</p>
                    @if($ann->content)
                    <p class="text-xs mt-0.5 opacity-80">{{ $ann->content }}</p>
                    @endif
                </div>
            </div>
            @endforeach

            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('partials.intl-tel-input')
    @include('partials.email-domain-input')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Employee attendance widgets (navbar + sidebar + work-times page)
        const attendanceWidgets = document.querySelectorAll('.attendance-widget-root');
        const attendanceStates = [];

        function fmtAttendanceTime(totalSec) {
            const s = Math.max(0, parseInt(totalSec, 10) || 0);
            const h = String(Math.floor(s / 3600)).padStart(2, '0');
            const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
            const sec = String(s % 60).padStart(2, '0');
            return `${h}:${m}:${sec}`;
        }

        function setAttendanceBtnStyle(btn, active, disabled) {
            btn.disabled = disabled;
            btn.classList.remove('ring-2', 'ring-offset-1', 'ring-green-400', 'ring-blue-400', 'ring-yellow-400', 'scale-105', 'bg-green-600', 'text-white', 'border-green-700', 'bg-blue-600', 'border-blue-700', 'bg-yellow-500', 'border-yellow-600', 'bg-gray-100', 'text-gray-700', 'border-gray-300', 'opacity-50', 'cursor-not-allowed');
            if (active) {
                if (btn.dataset.action === 'check_in' || btn.dataset.action === 'break_end') {
                    btn.classList.add('bg-green-600', 'text-white', 'border-green-700', 'ring-2', 'ring-green-400', 'ring-offset-1', 'scale-105');
                } else if (btn.dataset.action === 'check_out') {
                    btn.classList.add('bg-blue-600', 'text-white', 'border-blue-700', 'ring-2', 'ring-blue-400', 'ring-offset-1', 'scale-105');
                } else {
                    btn.classList.add('bg-yellow-500', 'text-white', 'border-yellow-600', 'ring-2', 'ring-yellow-400', 'ring-offset-1', 'scale-105');
                }
            } else {
                btn.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-300');
            }
            if (disabled) {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        function refreshAttendanceUI(stateIndex) {
            const state = attendanceStates[stateIndex];
            const root = state.root;
            const timerEls = root.querySelectorAll('.attendance-timer');
            const statusEls = root.querySelectorAll('.attendance-status-text');
            const label = state.statusLabel || (state.status === 'working' ? '{{ __('messages.status_working') }}' : (state.status === 'break' ? '{{ __('messages.status_break') }}' : '{{ __('messages.status_off') }}'));
            const timeStr = fmtAttendanceTime(state.seconds);

            timerEls.forEach((el) => { el.textContent = timeStr; });
            statusEls.forEach((el) => { el.textContent = label; });

            state.buttons.forEach((btn) => {
                const action = btn.dataset.action;
                const allowed =
                    (state.status === 'off' && action === 'check_in') ||
                    (state.status === 'working' && (action === 'break_start' || action === 'check_out')) ||
                    (state.status === 'break' && (action === 'break_end' || action === 'check_out'));
                setAttendanceBtnStyle(btn, allowed, !allowed);
            });
        }

        function refreshAllAttendanceUI() {
            attendanceStates.forEach((_, i) => refreshAttendanceUI(i));
        }

        async function sendAttendanceAction(action) {
            const res = await fetch('{{ route('dashboard.work-times.quick-action') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ action }),
            });
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || 'حدث خطأ');
            }
            attendanceStates.forEach((state) => {
                state.status = data.status;
                state.statusLabel = data.status_label || null;
                state.seconds = parseInt(data.worked_seconds || 0, 10);
            });
            refreshAllAttendanceUI();
            if (data.late_message) {
                Swal.fire({ icon: 'warning', title: '{{ __('messages.alert_late_checkin') }}', text: data.late_message, confirmButtonText: '{{ __('messages.alert_ok') }}' });
            }
        }

        attendanceWidgets.forEach((root, index) => {
            const buttons = root.querySelectorAll('.attendance-btn');
            attendanceStates.push({
                root,
                buttons: Array.from(buttons),
                status: root.dataset.status || 'off',
                statusLabel: null,
                seconds: parseInt(root.dataset.seconds || '0', 10),
            });

            buttons.forEach((btn) => {
                btn.addEventListener('click', async function() {
                    if (this.disabled) return;
                    try {
                        await sendAttendanceAction(this.dataset.action);
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: '{{ __('messages.alert_warning_title') }}', text: e.message || '{{ __('messages.alert_error_generic') }}' });
                    }
                });
            });

            refreshAttendanceUI(index);
        });

        if (attendanceStates.length) {
            setInterval(() => {
                let changed = false;
                attendanceStates.forEach((state, index) => {
                    if (state.status === 'working') {
                        state.seconds += 1;
                        changed = true;
                        const root = state.root;
                        root.querySelectorAll('.attendance-timer').forEach((el) => {
                            el.textContent = fmtAttendanceTime(state.seconds);
                        });
                    }
                });
            }, 1000);
        }

        // Sidebar search functionality
        const searchInput = document.querySelector('#searchInput');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.trim().toLowerCase();
                const ul = document.querySelector('#drawer-navigation ul');
                if (!ul) return;

                const children = Array.from(ul.children);

                if (searchTerm === '') {
                    children.forEach(el => el.style.display = '');
                    return;
                }

                // Show/hide individual li items based on text match
                children.forEach(el => {
                    if (el.tagName === 'LI') {
                        el.style.display = el.textContent.trim().toLowerCase().includes(searchTerm) ? '' : 'none';
                    }
                });

                // Hide group header <p> if all its following li siblings are hidden
                children.forEach((el, i) => {
                    if (el.tagName === 'P') {
                        let hasVisible = false;
                        for (let j = i + 1; j < children.length; j++) {
                            if (children[j].tagName === 'P') break;
                            if (children[j].tagName === 'LI' && children[j].style.display !== 'none') {
                                hasVisible = true;
                                break;
                            }
                        }
                        el.style.display = hasVisible ? '' : 'none';
                    }
                });
            });
        }

        // Initialize intl-tel-input on all phone fields
        if (typeof window.initIntlPhoneInputs === 'function') {
            window.initIntlPhoneInputs();
        }
    });

    function openSupport() {
        Swal.fire({
            title: "{{ __('messages.alert_subscribe_title') }}",
            text: "{{ __('messages.alert_subscribe_text') }}",
            icon: "warning"
        });
    }

    // ─── Global Flash Alerts ───────────────────────────────────────────
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '{{ __('messages.alert_success_title') }}',
        text: @js(session('success')),
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    @endif
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: '{{ __('messages.alert_error_title') }}',
        text: @js(session('error')),
        timer: 5000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    @endif
    @if(session('warning'))
    Swal.fire({
        icon: 'warning',
        title: '{{ __('messages.alert_warning_title') }}',
        text: @js(session('warning')),
        timer: 4000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    @endif
    @if($errors->any())
    Swal.fire({
        icon: 'error',
        title: '{{ __('messages.alert_form_errors') }}',
        html: '<ul style="text-align:{{ $isRtl ? 'right' : 'left' }};padding-{{ $isRtl ? 'right' : 'left' }}:1rem">' +
            @foreach($errors->all() as $e) '<li>{{ $e }}</li>' + @endforeach
        '</ul>',
        confirmButtonText: '{{ __('messages.alert_ok') }}',
        confirmButtonColor: '#336cfa',
    });
    @endif

    // ─── Global Confirm Delete ─────────────────────────────────────────
    function swalConfirm(form, msg) {
        Swal.fire({
            title: '{{ __('messages.alert_confirm_title') }}',
            text: msg || '{{ __('messages.alert_confirm_text') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f45d5d',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __('messages.alert_confirm_yes') }}',
            cancelButtonText: '{{ __('messages.alert_cancel') }}',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }
    </script>

    {{-- Real-time notification polling (clients only) --}}
    @auth
    @if(auth()->user()->role === 'client')
    <div id="notif-toasts" class="fixed bottom-5 left-5 z-[9999] flex flex-col gap-3 max-w-sm"></div>
    <style>@keyframes slideIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}</style>
    <script>
    (function(){
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        function showToast(n){
            const wrap = document.getElementById('notif-toasts');
            const el = document.createElement('div');
            el.style.cssText = 'background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.12);padding:16px;display:flex;align-items:flex-start;gap:12px;animation:slideIn .3s ease;min-width:280px;max-width:340px';
            el.innerHTML = `<div style="width:36px;height:36px;border-radius:10px;background:#336cfa;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-bell" style="color:#fff;font-size:14px"></i></div><div style="flex:1;min-width:0"><p style="font-size:14px;font-weight:600;color:#1f2937;margin:0">${n.title}</p><p style="font-size:12px;color:#6b7280;margin:4px 0 0">${n.message}</p></div><button onclick="this.parentElement.remove()" style="color:#d1d5db;background:none;border:none;cursor:pointer;flex-shrink:0;padding:0"><i class="fas fa-times" style="font-size:12px"></i></button>`;
            wrap.prepend(el);
            setTimeout(()=>{ if(el.parentElement) el.remove(); }, 8000);
        }
        async function poll(){
            try{
                const r = await fetch('{{ coroute('dashboard.send-notification.poll') }}',
                    {headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},credentials:'same-origin'});
                if(!r.ok) return;
                const d = await r.json();
                if(d.notifications && d.notifications.length){
                    d.notifications.forEach(n => showToast(n));
                }
            }catch(e){}
        }
        setInterval(poll, 5000);
        setTimeout(poll, 1000);
    })();
    </script>
    @endif
    @endauth
    @include('partials.currency-icon-script')
</body>

</html>

