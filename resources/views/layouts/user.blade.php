<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/css/intlTelInput.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/fav-icon.webp') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-white to-gray-50 font-cairo">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center rtl:ml-auto">
                    <a href="{{ route('system.index') }}">
                        <img src="{{ asset('assets/images/logo.webp') }}" class="h-18 w-12" alt="">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-4 flex-row-reverse space-x-reverse">
                    <!-- Language Button -->
                    @if (app()->getLocale() == 'ar')
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="px-4 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center gap-2 flex-row-reverse">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        English
                    </a>
                    @else
                    <a href="{{ route('lang.switch', 'ar') }}"
                        class="px-4 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center gap-2 flex-row-reverse">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        العربية
                    </a>
                    @endif

                    <!-- Systems Button -->
                    <a href="{{ route('system.index') }}" class="px-4 py-2 rounded-lg transition
                            {{ request()->routeIs('system.index')
                                ? 'bg-black text-white hover:bg-gray-700'
                                : 'text-gray-700 hover:bg-gray-100' }}
                        ">
                        {{ __('messages.systems') }}
                    </a>
                    <!-- Login Button -->
                    @auth
                    <a href="{{ route('special-request.index') }}"
                        class="{{ request()->routeIs('special-request.index') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }} px-4 py-2 rounded-lg transition  flex items-center gap-2 flex-row-reverse">
                        {{ __('messages.special_request') }}
                    </a>
                    @endguest
                    <!-- Login Button -->
                    @guest
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }}
                        px-4 py-2 rounded-lg transition  flex items-center gap-2 flex-row-reverse">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        {{ __('messages.login') }}
                    </a>
                    @else
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('login') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }}
                                                                px-4 py-2 rounded-lg transition  flex items-center gap-2 flex-row-reverse">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        {{ __('messages.dashboard') }}
                    </a>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <!-- Language Switcher for Mobile -->
                    @if (app()->getLocale() == 'ar')
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="px-4 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center gap-2 flex-row-reverse">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </a>
                    @else
                    <a href="{{ route('lang.switch', 'ar') }}"
                        class="px-4 py-2 rounded-lg transition text-gray-700 hover:bg-gray-100 flex items-center gap-2 flex-row-reverse">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </a>
                    @endif
                    <button onclick="toggleMobileMenu()" class="text-gray-700 p-2">
                        <svg id="menuIcon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="closeIcon" class="h-6 w-6 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <!-- Systems Button -->
                <a href="{{ route('system.index') }}" class="block text-center px-4 py-2 rounded-lg transition {{ request()->routeIs('system.index')
                                                                    ? 'bg-black text-white hover:bg-gray-700'
                                                                    : 'text-gray-700 hover:bg-gray-100' }}
                                                            ">
                    {{ __('messages.systems') }}
                </a>

                <!-- Login Button -->
                <a href="{{ route('login') }}"
                    class="block justify-center text-center {{ request()->routeIs('login') ? 'bg-black text-white hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100' }}
                                                             px-4 py-2 rounded-lg transition  flex items-center gap-2 flex-row-reverse">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                        </path>
                    </svg>
                    {{ __('messages.login') }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/gh/cferdinandi/smooth-scroll@16/dist/smooth-scroll.polyfills.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/js/intlTelInput.min.js"></script>
    <script>
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

        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');

            menu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }
    </script>
</body>

</html>