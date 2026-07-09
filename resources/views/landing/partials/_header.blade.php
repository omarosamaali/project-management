    <!--Preloader-->
    <div id="preloader">
        <div id="loader" class="loader">
            <div class="loader-container">
                <div class="loader-icon"><img src="{{ asset('landing/assets/img/logo/preloader.svg') }}" alt="Preloader"></div>
            </div>
        </div>
    </div>
    <script>
    window.addEventListener('load', function () {
        var p = document.getElementById('preloader');
        if (p) { p.style.display = 'none'; }
    });
    </script>

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <style>
        .tgmenu__nav .logo {
            height: 56px;
            display: flex;
            align-items: center;
        }
        .tgmenu__nav .logo img,
        .tgmobile__menu .nav-logo img,
        .offCanvas__logo img {
            max-height: 54px;
            width: auto;
            object-fit: contain;
        }
    </style>

    <!-- header-area -->
    <header>
        <div id="header-fixed-height"></div>
        <div class="tg-header__top">
            <div class="container custom-container">
                <div class="row">
                    <div class="col-xl-6 col-lg-8">
                        <ul class="tg-header__top-info left-side list-wrap">
                            @if($lsData['address'])
                            <li><i class="flaticon-placeholder"></i>{{ $lsData['address'] }}</li>
                            @endif
                            @if($lsData['email'])
                            <li><i class="flaticon-mail"></i><a href="mailto:{{ $lsData['email'] }}">{{ $lsData['email'] }}</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-xl-6 col-lg-4">
                        <ul class="tg-header__top-right list-wrap">
                            @if($lsData['opening_hours'])
                            <li><i class="flaticon-three-o-clock-clock"></i>{{ $lsData['opening_hours'] }}</li>
                            @endif
                            {{-- مبدّل اللغة --}}
                            <li>
                                <div style="display:flex;gap:4px;align-items:center;margin-right:8px;">
                                    <a href="{{ landing_lang_url( 'ar') }}"
                                        style="padding:2px 8px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;
                                              {{ $lsData['lang']==='ar' ? 'background:#fff;color:#104776;' : 'color:#fff;' }}">ع</a>
                                    <a href="{{ landing_lang_url( 'en') }}"
                                        style="padding:2px 8px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;
                                              {{ $lsData['lang']==='en' ? 'background:#fff;color:#104776;' : 'color:#fff;' }}">EN</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="sticky-header" class="tg-header__area">
            <div class="container custom-container">
                <div class="row">
                    <div class="col-12">
                        <div class="tgmenu__wrap">
                            <nav class="tgmenu__nav">
                                <div class="logo">
                                    <a href="{{ route('landing.index') }}"><img src="{{ $lsData['logo_colored'] }}" alt="{{ $lsData['site_name'] }}"></a>
                                </div>
                                <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-lg-flex">
                                    <ul class="navigation">
                                        <li class="{{ request()->routeIs('landing.index') ? 'active' : '' }}"><a href="{{ route('landing.index') }}">{{ $lsData['lang']==='ar' ? 'الرئيسية' : 'Home' }}</a></li>
                                        <li class="{{ request()->routeIs('landing.about') ? 'active' : '' }}"><a href="{{ route('landing.about') }}">{{ $lsData['lang']==='ar' ? 'من نحن' : 'About Us' }}</a></li>
                                        <li class="{{ request()->routeIs('landing.blog*') ? 'active' : '' }}"><a href="{{ route('landing.blog') }}">{{ $lsData['lang']==='ar' ? 'المدونة' : 'Blog' }}</a></li>
                                        <li class="{{ request()->routeIs('landing.products*') || request()->routeIs('landing.product*') ? 'active' : '' }}"><a href="{{ route('landing.products') }}">{{ $lsData['lang']==='ar' ? 'المتجر' : 'Shop' }}</a></li>
                                        <li class="{{ request()->routeIs('landing.services*') || request()->routeIs('landing.service*') ? 'active' : '' }}"><a href="{{ route('landing.services') }}">{{ $lsData['lang']==='ar' ? 'الخدمات' : 'Services' }}</a></li>
                                        <li class="{{ request()->routeIs('landing.contact') ? 'active' : '' }}"><a href="{{ route('landing.contact') }}">{{ $lsData['lang']==='ar' ? 'اتصل بنا' : 'Contact' }}</a></li>
                                    </ul>
                                </div>
                                <div class="tgmenu__action d-none d-md-block">
                                    <ul class="list-wrap">
                                        <li class="header-search">
                                            <a href="javascript:void(0)" class="search-open-btn"><i class="flaticon-loupe"></i></a>
                                        </li>
                                        <li class="offCanvas-menu" style="margin-right:10px !important; margin-left: 10px !important;">
                                            <a href="javascript:void(0)" class="menu-tigger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="16" viewBox="0 0 26 16" fill="none">
                                                    <rect width="9" height="2" rx="1" fill="currentcolor" />
                                                    <rect x="11" width="15" height="2" rx="1" fill="currentcolor" />
                                                    <rect y="14" width="26" height="2" rx="1" fill="currentcolor" />
                                                    <rect y="7" width="16" height="2" rx="1" fill="currentcolor" />
                                                    <rect x="17" y="7" width="9" height="2" rx="1" fill="currentcolor" />
                                                </svg>
                                            </a>
                                        </li>
                                        <li class="header-btn">
                                            <a href="{{ route('go.appointment') }}" class="btn">
                                                <i class="flaticon-calendar-1"></i>
                                                {{ $lsData['lang']==='ar' ? 'احجز موعد' : 'Appointment' }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mobile-nav-toggler"><i class="flaticon-layout"></i></div>
                            </nav>
                        </div>

                        <!-- Mobile Menu -->
                        <div class="tgmobile__menu">
                            <nav class="tgmobile__menu-box">
                                <div class="close-btn"><i class="fas fa-times"></i></div>
                                <div class="nav-logo">
                                    <a href="{{ route('landing.index') }}"><img src="{{ $lsData['logo_colored'] }}" alt="{{ $lsData['site_name'] }}"></a>
                                </div>
                                <div class="tgmobile__search">
                                    <form action="#">
                                        <input type="text" placeholder="{{ $lsData['lang']==='ar' ? 'ابحث هنا...' : 'Search here...' }}">
                                        <button><i class="fas fa-search"></i></button>
                                    </form>
                                </div>
                                <div class="tgmobile__menu-outer"></div>
                                <div class="social-links">
                                    <ul class="list-wrap">
                                        @if($lsData['facebook'])<li><a href="{{ $lsData['facebook'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>@endif
                                        @if($lsData['twitter'])<li><a href="{{ $lsData['twitter'] }}" target="_blank"><i class="fab fa-twitter"></i></a></li>@endif
                                        @if($lsData['whatsapp'])<li><a href="{{ $lsData['whatsapp'] }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>@endif
                                        @if($lsData['instagram'])<li><a href="{{ $lsData['instagram'] }}" target="_blank"><i class="fab fa-instagram"></i></a></li>@endif
                                    </ul>
                                </div>
                            </nav>
                        </div>
                        <div class="tgmobile__menu-backdrop"></div>
                        <!-- End Mobile Menu -->
                    </div>
                </div>
            </div>
        </div>

        <!-- header-search -->
        <div class="search__popup">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="search__wrapper">
                            <div class="search__close">
                                <button type="button" class="search-close-btn">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 1L1 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M1 1L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="search__form">
                                <form action="#">
                                    <div class="search__input">
                                        <input class="search-input-field" type="text"
                                            placeholder="{{ $lsData['lang']==='ar' ? 'اكتب كلمات البحث' : 'Type keywords here' }}">
                                        <span class="search-focus-border"></span>
                                        <button>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M9.55 18.1C14.272 18.1 18.1 14.272 18.1 9.55C18.1 4.82797 14.272 1 9.55 1C4.82797 1 1 4.82797 1 9.55C1 14.272 4.82797 18.1 9.55 18.1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M19.0002 19.0002L17.2002 17.2002" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="search-popup-overlay"></div>

        <!-- offCanvas-menu -->
        <div class="offCanvas__info">
            <div class="offCanvas__close-icon menu-close">
                <button><i class="far fa-window-close"></i></button>
            </div>
            <div class="offCanvas__logo mb-30">
                <a href="{{ route('landing.index') }}"><img src="{{ $lsData['logo_colored'] }}" alt="{{ $lsData['site_name'] }}"></a>
            </div>
            <div class="offCanvas__side-info mb-30">
                @if($lsData['address'])
                <div class="contact-list mb-30">
                    <h4>{{ $lsData['lang']==='ar' ? 'عنوان المكتب' : 'Office Address' }}</h4>
                    <p>{{ $lsData['address'] }}</p>
                </div>
                @endif
                @if($lsData['phone'])
                <div class="contact-list mb-30">
                    <h4>{{ $lsData['lang']==='ar' ? 'رقم الهاتف' : 'Phone Number' }}</h4>
                    <p><a href="tel:{{ $lsData['phone'] }}">{{ $lsData['phone'] }}</a></p>
                </div>
                @endif
                @if($lsData['email'])
                <div class="contact-list mb-30">
                    <h4>{{ $lsData['lang']==='ar' ? 'البريد الإلكتروني' : 'Email Address' }}</h4>
                    <p><a href="mailto:{{ $lsData['email'] }}">{{ $lsData['email'] }}</a></p>
                </div>
                @endif
            </div>
            <div class="offCanvas__social-icon mt-30">
                @if($lsData['facebook'])<a href="{{ $lsData['facebook'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a>@endif
                @if($lsData['twitter'])<a href="{{ $lsData['twitter'] }}" target="_blank"><i class="fab fa-twitter"></i></a>@endif
                @if($lsData['instagram'])<a href="{{ $lsData['instagram'] }}" target="_blank"><i class="fab fa-instagram"></i></a>@endif
                @if($lsData['youtube'])<a href="{{ $lsData['youtube'] }}" target="_blank"><i class="fab fa-youtube"></i></a>@endif
            </div>

            {{-- روابط المستخدم --}}
            @auth
            <div style="margin-top:24px;border-top:1px solid #eee;padding-top:20px;display:flex;flex-direction:column;gap:10px;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:#fff1f1;color:#c0392b;font-weight:600;border:none;cursor:pointer;font-size:14px;font-family:inherit;">
                        <i class="fas fa-sign-out-alt"></i>
                        {{ $lsData['lang']==='ar' ? 'تسجيل الخروج' : 'Logout' }}
                    </button>
                </form>
            </div>
            @else
            <div style="margin-top:24px;border-top:1px solid #eee;padding-top:20px;display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('register') }}"
                   style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:#f0fff4;color:#1a7f4b;font-weight:600;text-decoration:none;font-size:14px;">
                    <i class="fas fa-user-plus"></i>
                    {{ $lsData['lang']==='ar' ? 'إنشاء حساب' : 'Register' }}
                </a>
            </div>
            @endauth
        </div>
        <div class="offCanvas__overly"></div>
    </header>
    <!-- header-area-end -->
