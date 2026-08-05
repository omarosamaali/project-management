<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/css/intlTelInput.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/fav-icon.webp') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $authUi = \App\Support\AuthUi::resolve();
        $isAcademyPage = request()->routeIs('academy.*')
            || request()->routeIs('courses.show')
            || (
                request()->routeIs('login', 'register', 'password.*', 'verification.*')
                && $authUi === \App\Support\AuthUi::ACADEMY
            );
        $navLogo = $isAcademyPage
            ? \App\Models\Setting::academyChromeLogoUrl()
            : asset('assets/images/logo.webp');
        $navHome = $isAcademyPage
            ? route('academy.index')
            : (auth()->check() && auth()->user()->isTrainee() ? route('academy.index') : route('system.index'));
        $hideSystemsNav = (auth()->check() && (auth()->user()->isTrainee() || auth()->user()->isTrainer()))
            || (! auth()->check() && $isAcademyPage);
        $loginUrl = $isAcademyPage
            ? \App\Support\AuthUi::loginUrl(['ui' => 'academy'])
            : \App\Support\AuthUi::loginUrl(['ui' => 'classic']);
        $registerUrl = $isAcademyPage
            ? \App\Support\AuthUi::registerUrl(['ui' => 'academy'])
            : \App\Support\AuthUi::registerUrl(['ui' => 'classic']);
    @endphp
    <style>
        .mobile-drawer {
            inset-inline-end: 0;
            transform: translateX(100%);
            transition: transform .3s ease-out;
            visibility: hidden;
        }
        [dir="rtl"] .mobile-drawer {
            transform: translateX(-100%);
        }
        .mobile-drawer.is-open {
            transform: translateX(0);
            visibility: visible;
        }
        #mobileMenuBackdrop.is-open {
            opacity: 1;
            pointer-events: auto;
        }
        body.mobile-drawer-open {
            overflow: hidden;
        }
        body.is-academy-site {
            font-family: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
            background: #f0f4f8;
        }
        /* Desktop full nav from 1024px; below that: logo + lang + hamburger. */
        html:has(body.is-academy-site),
        body.is-academy-site {
            overflow-x: clip;
            max-width: 100%;
        }
        .academy-public-nav {
            background: rgba(6, 21, 37, .94) !important;
            border-bottom: 1px solid rgba(255,255,255,.08);
            box-shadow: 0 12px 32px rgba(6,21,37,.28) !important;
            backdrop-filter: blur(14px);
            width: 100%;
            max-width: 100%;
            overflow-x: clip;
        }
        .academy-public-nav .ac-pub-inner {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 clamp(0.85rem, 2.2vw, 2.25rem);
            box-sizing: border-box;
        }
        .academy-public-nav .ac-pub-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            min-height: 5.25rem;
            column-gap: 0.5rem;
        }
        .academy-public-nav .ac-pub-cluster {
            display: flex;
            align-items: center;
            gap: .45rem;
            min-width: 0;
            width: 100%;
            max-width: 100%;
        }
        .academy-public-nav .ac-pub-cluster--start {
            justify-content: flex-end;
        }
        .academy-public-nav .ac-pub-cluster--end {
            justify-content: flex-start;
        }
        .academy-public-nav .ac-pub-cluster--end .ac-pub-auth {
            margin-inline-start: auto;
            flex-shrink: 0;
        }
        .academy-public-nav .ac-pub-side {
            display: flex;
            align-items: center;
            gap: .45rem;
            flex: 0 1 auto;
            min-width: 0;
            max-width: 100%;
        }
        .academy-public-nav .ac-pub-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            justify-self: center;
            padding-block: .35rem;
            padding-inline: 0;
            z-index: 1;
        }
        .academy-public-nav .ac-pub-auth {
            display: none;
            align-items: center;
            gap: .45rem;
            flex: 0 0 auto;
        }
        .academy-public-nav .ac-pub-logo img {
            height: clamp(3.4rem, 5.2vw, 4.6rem);
            width: auto;
            max-width: min(16rem, 42vw);
            object-fit: contain;
            display: block;
        }
        .academy-public-nav .ac-pub-links {
            display: none;
            align-items: center;
            gap: .3rem;
            padding: .28rem;
            border-radius: 999px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            flex: 0 1 auto;
            width: auto;
            max-width: 100%;
            flex-wrap: nowrap;
            min-width: 0;
        }
        /* < 1024: compact bar — logo | lang + hamburger */
        @media (max-width: 1023px) {
            .academy-public-nav .ac-pub-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: .5rem;
                min-height: 4.25rem;
            }
            .academy-public-nav .ac-pub-cluster--start { display: none; }
            .academy-public-nav .ac-pub-cluster--end {
                width: auto;
                flex: 0 0 auto;
                justify-content: flex-end;
            }
            .academy-public-nav .ac-pub-cluster--end .ac-pub-auth {
                margin-inline-start: 0;
            }
            .academy-public-nav .ac-pub-side-end {
                gap: .15rem;
            }
            .academy-public-nav .ac-pub-links,
            .academy-public-nav .ac-pub-auth {
                display: none !important;
            }
            .academy-public-nav .ac-pub-mobile {
                display: flex !important;
                align-items: center;
                gap: .1rem;
                direction: ltr; /* keep hamburger then lang as in compact design */
            }
            .academy-public-nav .ac-pub-logo img {
                height: clamp(2.85rem, 9vw, 3.6rem);
                max-width: min(12rem, 52vw);
            }
        }
        @media (min-width: 1024px) {
            .academy-public-nav .ac-pub-links { display: inline-flex; }
            .academy-public-nav .ac-pub-mobile { display: none !important; }
            .academy-public-nav .ac-pub-row.has-auth .ac-pub-auth {
                display: inline-flex;
            }
            .ac-pub-drawer-host { display: none !important; }

            /* Space between logo and side nav: large on wide screens, shrinks toward 1024. */
            .academy-public-nav .ac-pub-row {
                column-gap: clamp(0.5rem, -2.15rem + 5.2vw, 5.5rem);
            }
            .academy-public-nav .ac-pub-logo {
                padding-inline: clamp(0rem, -1.1rem + 2.4vw, 2.75rem);
            }
        }
        /* 1024–1279: tight chrome so Arabic labels fit without overflow */
        @media (min-width: 1024px) and (max-width: 1279px) {
            .academy-public-nav .ac-pub-inner { padding: 0 .75rem; }
            .academy-public-nav .ac-pub-row {
                min-height: 4.35rem;
            }
            .academy-public-nav .ac-pub-cluster { gap: .3rem; }
            .academy-public-nav .ac-pub-side { gap: .3rem; }
            .academy-public-nav .ac-pub-link {
                padding: .4rem .55rem;
                font-size: .72rem;
                gap: .25rem;
            }
            .academy-public-nav .ac-pub-cta {
                padding: .42rem .7rem;
                font-size: .72rem;
                gap: .3rem;
                box-shadow: 0 6px 14px rgba(255,61,122,.28);
            }
            .academy-public-nav .ac-pub-links { gap: .12rem; padding: .16rem; }
            .academy-public-nav .ac-pub-profile-name,
            .academy-public-nav .ac-pub-logout span { display: none; }
            .academy-public-nav .ac-pub-profile {
                padding: .35rem;
                max-width: none;
            }
            .academy-public-nav .ac-pub-logout { padding: .45rem .55rem; }
            .academy-public-nav .ac-pub-logo img {
                height: clamp(2.7rem, 3.4vw, 3.35rem);
                max-width: 9.5rem;
            }
        }
        /* 1280–1599: slightly roomier, still icon-only auth */
        @media (min-width: 1280px) and (max-width: 1599px) {
            .academy-public-nav .ac-pub-link {
                padding: .48rem .72rem;
                font-size: .8rem;
            }
            .academy-public-nav .ac-pub-cta {
                padding: .52rem .9rem;
                font-size: .8rem;
            }
            .academy-public-nav .ac-pub-profile-name,
            .academy-public-nav .ac-pub-logout span { display: none; }
            .academy-public-nav .ac-pub-profile { padding: .4rem; max-width: none; }
            .academy-public-nav .ac-pub-logout { padding: .5rem .65rem; }
            .academy-public-nav .ac-pub-logo img {
                height: clamp(3rem, 3.8vw, 3.85rem);
            }
            .academy-public-nav .ac-pub-row { min-height: 4.6rem; }
        }
        .academy-public-nav .ac-pub-link {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .55rem .95rem; border-radius: 999px;
            font-size: .875rem; font-weight: 700; color: rgba(255,255,255,.78);
            text-decoration: none; transition: background .18s, color .18s;
            white-space: nowrap;
        }
        .academy-public-nav .ac-pub-link:hover { background: rgba(255,255,255,.1); color: #fff; }
        .academy-public-nav .ac-pub-link.is-active { background: #ff3d7a; color: #fff; }
        .academy-public-nav .ac-pub-cta {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .6rem 1.15rem; border-radius: 999px;
            background: linear-gradient(135deg, #ff3d7a, #e11d62);
            color: #fff !important; font-weight: 800; font-size: .875rem;
            text-decoration: none; box-shadow: 0 8px 20px rgba(255,61,122,.35);
            white-space: nowrap;
        }
        .academy-public-nav .ac-pub-cta:hover { filter: brightness(1.06); }
        .academy-public-nav .ac-pub-profile {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .45rem .85rem .45rem .45rem; border-radius: 999px;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.14);
            color: #fff; text-decoration: none; font-weight: 700; font-size: .82rem;
            max-width: 10.5rem; transition: background .18s, border-color .18s;
        }
        .academy-public-nav .ac-pub-profile:hover {
            background: rgba(255,255,255,.16); border-color: rgba(255,255,255,.28);
        }
        .academy-public-nav .ac-pub-profile.is-active {
            background: rgba(255,61,122,.22); border-color: rgba(255,61,122,.55);
        }
        .academy-public-nav .ac-pub-profile-avatar {
            width: 1.7rem; height: 1.7rem; border-radius: 999px;
            display: inline-flex; align-items: center; justify-content: center;
            background: #ff3d7a; color: #fff; font-size: .72rem; font-weight: 800;
            flex-shrink: 0; object-fit: cover;
        }
        .academy-public-nav .ac-pub-profile-name {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .academy-public-nav .ac-pub-logout {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .55rem .9rem; border-radius: 999px; border: 1px solid rgba(248,113,113,.35);
            background: rgba(248,113,113,.12); color: #fecaca;
            font-weight: 700; font-size: .82rem; cursor: pointer;
            transition: background .18s, border-color .18s, color .18s;
        }
        .academy-public-nav .ac-pub-logout:hover {
            background: rgba(248,113,113,.22); border-color: rgba(248,113,113,.55); color: #fff;
        }
        .academy-public-nav .ac-pub-mobile a,
        .academy-public-nav #mobileMenuToggle { color: #fff !important; }
        .academy-public-nav #mobileMenuToggle:hover { background: rgba(255,255,255,.1) !important; }
        aside.academy-drawer { background: #061525 !important; color: #fff; }
        aside.academy-drawer .mobile-drawer-link { color: rgba(255,255,255,.88) !important; }
        aside.academy-drawer .mobile-drawer-link:hover { background: rgba(255,255,255,.08) !important; }
        aside.academy-drawer .is-drawer-active {
            background: #ff3d7a !important; color: #fff !important;
        }
        aside.academy-drawer .border-b,
        aside.academy-drawer .border-t { border-color: rgba(255,255,255,.1) !important; }
        aside.academy-drawer button[aria-label="إغلاق"] { color: #fff !important; }
        aside.academy-drawer button[aria-label="إغلاق"]:hover { background: rgba(255,255,255,.1) !important; }
    </style>
</head>

<body class="min-h-screen overflow-x-clip {{ $isAcademyPage ? 'is-academy-site' : 'bg-gradient-to-br from-white to-gray-50 font-cairo' }}">
    <nav class="sticky top-0 z-50 {{ $isAcademyPage ? 'academy-public-nav' : 'bg-white shadow-lg' }}">
        <div class="{{ $isAcademyPage ? 'ac-pub-inner' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' }}">
            <div class="{{ $isAcademyPage ? 'ac-pub-row'.(auth()->check() ? ' has-auth' : '') : 'flex justify-between h-16' }}">
                @if($isAcademyPage)
                {{-- Desktop (≥1024): 1fr | logo | 1fr. Below: logo + lang/hamburger. --}}
                <div class="ac-pub-cluster ac-pub-cluster--start">
                    <div class="ac-pub-side ac-pub-side-start">
                        <div class="ac-pub-links">
                            <a href="{{ route('academy.index') }}" class="ac-pub-cta {{ request()->routeIs('academy.index') ? 'is-active' : '' }}">
                                {{ __('messages.academy') }}
                            </a>
                            <a href="{{ route('academy.courses') }}" class="ac-pub-link {{ request()->routeIs('academy.courses') || request()->routeIs('academy.category') || request()->routeIs('courses.show') ? 'is-active' : '' }}">
                                {{ __('messages.academy_view_all_courses') }}
                            </a>
                            <a href="{{ route('academy.trainers.index') }}" class="ac-pub-link {{ request()->routeIs('academy.trainers.*') ? 'is-active' : '' }}">
                                {{ __('messages.academy_trainers_kicker') }}
                            </a>
                            @auth
                            <a href="{{ route('academy.wishlist.index') }}" class="ac-pub-link {{ request()->routeIs('academy.wishlist.*') ? 'is-active' : '' }}">
                                <i class="fas fa-heart text-xs"></i>
                                {{ __('messages.academy_wishlist') }}
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <a href="{{ $navHome }}" class="ac-pub-logo">
                    <img src="{{ $navLogo }}" alt="{{ __('messages.academy') }}">
                </a>

                <div class="ac-pub-cluster ac-pub-cluster--end">
                    <div class="ac-pub-side ac-pub-side-end">
                        <div class="ac-pub-links">
                            @auth
                            <a href="{{ route('dashboard') }}" class="ac-pub-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'is-active' : '' }}">
                                <i class="fas fa-gauge-high text-xs"></i>
                                {{ __('messages.dashboard') }}
                            </a>
                            @else
                            <a href="{{ $loginUrl }}" class="ac-pub-link {{ request()->routeIs('login') ? 'is-active' : '' }}">
                                {{ __('messages.login') }}
                            </a>
                            @endauth
                            @if (app()->getLocale() == 'ar')
                            <a href="{{ route('lang.switch', 'en') }}" class="ac-pub-link">English</a>
                            @else
                            <a href="{{ route('lang.switch', 'ar') }}" class="ac-pub-link">العربية</a>
                            @endif
                            @auth
                            <a href="{{ route('academy.courses') }}" class="ac-pub-cta {{ request()->routeIs('academy.courses') || request()->routeIs('academy.category') ? 'is-active' : '' }}">
                                <i class="fas fa-layer-group text-xs"></i>
                                {{ __('messages.academy_view_all_courses') }}
                            </a>
                            @else
                            <a href="{{ route('academy.become-trainer') }}" class="ac-pub-cta {{ request()->routeIs('academy.become-trainer') ? 'is-active' : '' }}">
                                <i class="fas fa-chalkboard-teacher text-xs"></i>
                                {{ __('messages.become_trainer_nav') }}
                            </a>
                            @endauth
                        </div>

                        <div class="ac-pub-mobile flex items-center gap-1">
                            <button type="button" onclick="toggleMobileMenu()" id="mobileMenuToggle"
                                class="p-2 rounded-lg text-white hover:bg-white/10"
                                aria-controls="mobileMenu" aria-expanded="false" aria-label="القائمة">
                                <svg id="menuIcon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <svg id="closeIcon" class="h-6 w-6 hidden" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            @if (app()->getLocale() == 'ar')
                            <a href="{{ route('lang.switch', 'en') }}"
                                class="px-3 py-2 rounded-lg transition text-white hover:bg-white/10 flex items-center"
                                aria-label="English">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </a>
                            @else
                            <a href="{{ route('lang.switch', 'ar') }}"
                                class="px-3 py-2 rounded-lg transition text-white hover:bg-white/10 flex items-center"
                                aria-label="العربية">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>

                    @auth
                    <div class="ac-pub-auth">
                        <a href="{{ route('profile.edit') }}"
                            class="ac-pub-profile {{ request()->routeIs('profile.*') ? 'is-active' : '' }}"
                            title="{{ Auth::user()->name }}">
                            <img src="{{ Auth::user()->avatarUrl() }}" alt=""
                                class="ac-pub-profile-avatar"
                                onerror="this.onerror=null;this.src='{{ Auth::user()->letterAvatarDataUri() }}';">
                            <span class="ac-pub-profile-name">{{ Auth::user()->name }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="ac-pub-logout" title="{{ __('messages.logout') }}">
                                <i class="fas fa-sign-out-alt text-xs"></i>
                                <span>{{ __('messages.logout') }}</span>
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
                @else
                <div class="flex items-center rtl:ml-auto">
                    <a href="{{ $navHome }}">
                        <img src="{{ $navLogo }}" class="h-18 w-12" alt="Evorq">
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <a href="https://evorq.com/" class="px-4 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100">
                        {{ __('messages.web') }}
                    </a>
                    @unless($hideSystemsNav)
                    <a href="{{ route('system.index') }}" class="px-4 py-2 rounded-lg transition
                            {{ request()->routeIs('system.index')
                                ? 'bg-black text-white hover:bg-gray-700'
                                : 'text-gray-700 hover:bg-gray-100' }}">
                        {{ __('messages.systems') }}
                    </a>
                    @endunless
                    <a href="{{ route('academy.index') }}" class="px-4 py-2 rounded-lg transition
                            {{ request()->routeIs('academy.*')
                                ? 'bg-black text-white hover:bg-gray-700'
                                : 'text-gray-700 hover:bg-gray-100' }}">
                        {{ __('messages.academy') }}
                    </a>
                    @auth
                    @unless(auth()->user()->isTrainee() || auth()->user()->isTrainer())
                    <a href="{{ route('special-request.index') }}"
                        class="{{ request()->routeIs('special-request.index') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }} px-4 py-2 rounded-lg transition flex items-center gap-2">
                        {{ __('messages.special_request') }}
                    </a>
                    @endunless
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }} px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-gauge-high text-sm"></i>
                        {{ __('messages.dashboard') }}
                    </a>
                    @else
                    <a href="{{ $loginUrl }}" class="{{ request()->routeIs('login') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }}
                        px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        {{ __('messages.login') }}
                    </a>
                    @endauth
                    @if (app()->getLocale() == 'ar')
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="px-4 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        English
                    </a>
                    @else
                    <a href="{{ route('lang.switch', 'ar') }}"
                        class="px-4 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        العربية
                    </a>
                    @endif
                    @auth
                    <div class="flex items-center gap-2 ms-6 ps-6 border-s border-gray-200">
                        <a href="{{ route('profile.edit') }}"
                            class="{{ request()->routeIs('profile.edit') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }} px-3 py-2 rounded-lg transition flex items-center gap-2 max-w-[12rem]"
                            title="{{ Auth::user()->name }}">
                            <img src="{{ Auth::user()->avatarUrl() }}" alt=""
                                class="w-7 h-7 rounded-full object-cover shrink-0"
                                onerror="this.onerror=null;this.src='{{ Auth::user()->letterAvatarDataUri() }}';">
                            <span class="truncate text-sm font-medium">{{ Auth::user()->name }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-3 py-2 rounded-lg transition text-red-600 hover:bg-red-50 flex items-center gap-2">
                                <i class="fas fa-sign-out-alt text-sm"></i>
                                {{ __('messages.logout') }}
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
                @endif

                @unless($isAcademyPage)
                <div class="md:hidden flex items-center gap-1">
                    @if (app()->getLocale() == 'ar')
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="px-3 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center"
                        aria-label="English">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </a>
                    @else
                    <a href="{{ route('lang.switch', 'ar') }}"
                        class="px-3 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center"
                        aria-label="العربية">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </a>
                    @endif
                    <button type="button" onclick="toggleMobileMenu()" id="mobileMenuToggle"
                        class="p-2 rounded-lg text-gray-700 hover:bg-gray-100"
                        aria-controls="mobileMenu" aria-expanded="false" aria-label="القائمة">
                        <svg id="menuIcon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="closeIcon" class="h-6 w-6 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @endunless
            </div>
        </div>
    </nav>

    <div id="mobileMenuBackdrop"
        class="fixed inset-0 z-[55] bg-black/45 opacity-0 pointer-events-none transition-opacity duration-300 {{ $isAcademyPage ? 'ac-pub-drawer-host' : 'md:hidden' }}"
        onclick="closeMobileMenu()" aria-hidden="true"></div>

    <aside id="mobileMenu"
        class="mobile-drawer fixed top-0 bottom-0 z-[60] w-[min(20rem,86vw)] max-w-full shadow-2xl
            flex flex-col {{ $isAcademyPage ? 'ac-pub-drawer-host academy-drawer' : 'md:hidden bg-white' }}"
        role="dialog" aria-modal="true" aria-label="القائمة" hidden>

        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b {{ $isAcademyPage ? '' : 'border-gray-100' }}">
            <div class="flex items-center gap-2 min-w-0">
                <img src="{{ $navLogo }}" alt="" class="h-10 w-auto max-w-[9rem] object-contain">
            </div>
            <button type="button" onclick="closeMobileMenu()"
                class="p-2 rounded-lg {{ $isAcademyPage ? 'text-white hover:bg-white/10' : 'text-gray-600 hover:bg-gray-100' }}" aria-label="إغلاق">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1.5">
            @if($isAcademyPage)
            <a href="{{ route('academy.index') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('academy.index') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-home w-5 text-center opacity-70"></i>
                {{ __('messages.academy') }}
            </a>
            <a href="{{ route('academy.courses') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('academy.courses') || request()->routeIs('academy.category') || request()->routeIs('courses.show') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-layer-group w-5 text-center opacity-70"></i>
                {{ __('messages.academy_view_all_courses') }}
            </a>
            <a href="{{ route('academy.trainers.index') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('academy.trainers.*') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-chalkboard-teacher w-5 text-center opacity-70"></i>
                {{ __('messages.academy_trainers_kicker') }}
            </a>
            @guest
            <a href="{{ route('academy.become-trainer') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('academy.become-trainer') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-user-plus w-5 text-center opacity-70"></i>
                {{ __('messages.become_trainer_nav') }}
            </a>
            @endguest
            @auth
            <a href="{{ route('academy.wishlist.index') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('academy.wishlist.*') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-heart w-5 text-center opacity-70"></i>
                {{ __('messages.academy_wishlist') }}
            </a>
            <a href="{{ route('dashboard') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-gauge-high w-5 text-center opacity-70"></i>
                {{ __('messages.dashboard') }}
            </a>
            <a href="{{ route('profile.edit') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('profile.edit') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-user w-5 text-center opacity-70"></i>
                {{ Auth::user()->name }}
            </a>
            @else
            <a href="{{ $loginUrl }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium {{ request()->routeIs('login') ? 'is-drawer-active' : '' }}">
                <i class="fas fa-right-to-bracket w-5 text-center opacity-70"></i>
                {{ __('messages.login') }}
            </a>
            @endauth
            @else
            <a href="https://evorq.com/"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-gray-100 transition text-sm font-medium">
                <i class="fas fa-globe w-5 text-center text-gray-400"></i>
                {{ __('messages.web') }}
            </a>
            @unless($hideSystemsNav)
            <a href="{{ route('system.index') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                    {{ request()->routeIs('system.index') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-th-large w-5 text-center {{ request()->routeIs('system.index') ? 'text-white/80' : 'text-gray-400' }}"></i>
                {{ __('messages.systems') }}
            </a>
            @endunless
            <a href="{{ route('academy.index') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                    {{ request()->routeIs('academy.*') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-graduation-cap w-5 text-center {{ request()->routeIs('academy.*') ? 'text-white/80' : 'text-gray-400' }}"></i>
                {{ __('messages.academy') }}
            </a>
            @auth
            @unless(auth()->user()->isTrainee() || auth()->user()->isTrainer())
            <a href="{{ route('special-request.index') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                    {{ request()->routeIs('special-request.index') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-clipboard-list w-5 text-center {{ request()->routeIs('special-request.index') ? 'text-white/80' : 'text-gray-400' }}"></i>
                {{ __('messages.special_request') }}
            </a>
            @endunless
            <a href="{{ route('dashboard') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                    {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-gauge-high w-5 text-center {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.*') ? 'text-white/80' : 'text-gray-400' }}"></i>
                {{ __('messages.dashboard') }}
            </a>
            <a href="{{ route('profile.edit') }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                    {{ request()->routeIs('profile.edit') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <img src="{{ Auth::user()->avatarUrl() }}" alt=""
                    class="w-7 h-7 rounded-full object-cover shrink-0"
                    onerror="this.onerror=null;this.src='{{ Auth::user()->letterAvatarDataUri() }}';">
                <span class="truncate">{{ Auth::user()->name }}</span>
            </a>
            @else
            <a href="{{ $loginUrl }}"
                class="mobile-drawer-link flex items-center gap-3 px-4 py-3 rounded-xl transition text-sm font-medium
                    {{ request()->routeIs('login') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-right-to-bracket w-5 text-center text-gray-400"></i>
                {{ __('messages.login') }}
            </a>
            @endauth
            @endif
        </nav>

        @auth
        <div class="border-t p-3 {{ $isAcademyPage ? '' : 'border-gray-100' }}">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl transition text-sm font-medium text-red-500 {{ $isAcademyPage ? 'hover:bg-white/10' : 'hover:bg-red-50' }}">
                    <i class="fas fa-sign-out-alt"></i>
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <main>
        @yield('content')
    </main>

    <x-footer />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/gh/cferdinandi/smooth-scroll@16/dist/smooth-scroll.polyfills.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/js/intlTelInput.min.js"></script>
    <script>
        window.EVORQ_LOGIN_URL = @json($loginUrl);

        @if(session('session_expired'))
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: 'انتهت صلاحية الجلسة',
                text: 'يجب تسجيل الدخول مرة أخرى للمتابعة.',
                confirmButtonText: 'تسجيل الدخول',
                confirmButtonColor: '#061525',
                allowOutsideClick: false,
                allowEscapeKey: false,
            });
        });
        @elseif(session('success'))
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: @json(session('success')),
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#061525',
            });
        });
        @elseif(session('error') || $errors->has('csrf'))
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'تنبيه',
                text: @json(session('error') ?: ($errors->first('csrf') ?: 'حدث خطأ، يرجى المحاولة مرة أخرى.')),
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#061525',
            });
        });
        @elseif($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'تعذر إكمال العملية',
                text: @json($errors->first() ?: 'يرجى مراجعة الحقول والمحاولة مرة أخرى.'),
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#061525',
            });
        });
        @endif

        const input = document.querySelector("#phone");
        if(input) {
            window.intlTelInput(input, {
                loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/js/utils.js"),
                initialCountry: "ae",
                separateDialCode: true
            });
        }

        var scroll = new SmoothScroll('a[href*="#"]', {
            speed: 900,
            speedAsDuration: true,
            easing: 'ease',
            offset: 100
        });

        function openMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const backdrop = document.getElementById('mobileMenuBackdrop');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');
            const toggle = document.getElementById('mobileMenuToggle');
            if (!menu) return;

            menu.hidden = false;
            requestAnimationFrame(() => {
                menu.classList.add('is-open');
                backdrop?.classList.add('is-open');
            });
            menuIcon?.classList.add('hidden');
            closeIcon?.classList.remove('hidden');
            toggle?.setAttribute('aria-expanded', 'true');
            document.body.classList.add('mobile-drawer-open');
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const backdrop = document.getElementById('mobileMenuBackdrop');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');
            const toggle = document.getElementById('mobileMenuToggle');
            if (!menu) return;

            menu.classList.remove('is-open');
            backdrop?.classList.remove('is-open');
            menuIcon?.classList.remove('hidden');
            closeIcon?.classList.add('hidden');
            toggle?.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('mobile-drawer-open');

            const hide = () => {
                if (!menu.classList.contains('is-open')) menu.hidden = true;
            };
            menu.addEventListener('transitionend', hide, { once: true });
            setTimeout(hide, 350);
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (!menu) return;
            if (menu.classList.contains('is-open')) closeMobileMenu();
            else openMobileMenu();
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMobileMenu();
        });
    </script>
</body>

</html>
