<!doctype html>
<html class="no-js" lang="{{ $lsData['lang'] }}" dir="{{ $lsData['dir'] }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $lsData['site_name'] }}</title>
    <meta name="description" content="{{ $lsData['site_name'] }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="{{ $lsData['favicon_url'] }}">

    <link rel="stylesheet" href="{{ asset('landing/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/flaticon_pet_care.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/odometer.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/default.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/main.css') }}">
    <style>
        :root {
            --tg-theme-primary: {{ $lsData['primary_color'] }};
            --tg-theme-secondary: {{ $lsData['secondary_color'] }};
        }
        .video__box-shape svg { color: var(--tg-theme-primary); }
        .experience__box-shape svg { color: var(--tg-theme-secondary); }

        /* ---- registration selects ---- */
        .reg-sel-wrap { position: relative; }
        .reg-sel-wrap select {
            width: 100%;
            height: 50px;
            background: #113179;
            border: none;
            border-radius: 6px;
            padding: 0 40px 0 16px;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            font-family: inherit;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            text-align: center;
            text-align-last: center;
            outline: none;
        }
        .reg-sel-wrap select option {
            background: #0d2060;
            color: #fff;
            text-align: center;
            font-weight: 500;
        }
        .reg-sel-wrap select option:first-child { color: #8fa3d0; }
        .reg-sel-wrap::after {
            content: '';
            pointer-events: none;
            position: absolute;
            right: 14px;
            top: calc(50% + 11px);
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid rgba(255,255,255,0.6);
        }

        /* Arabic circular badge — word-based layout avoids letter overlap */
        [dir="rtl"] .healthy-pets .content .circle {
            font-size: 13px;
            text-transform: none;
            letter-spacing: 0;
        }
        [dir="rtl"] .healthy-pets .content .circle span {
            white-space: nowrap;
            font-size: 13px;
        }
        [dir="rtl"] .healthy-pets .content .circle.is-words span {
            top: -72px;
            transform-origin: 0 72px;
        }
        .brand-phrase-primary { color: var(--tg-theme-primary); }
        .brand-phrase-secondary { color: var(--tg-theme-secondary); }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400..700&display=swap" rel="stylesheet">
    <style>
        body, h1, h2, h3, h4, h5, h6, p, a, span, li, ul, ol, nav,
        button, input, select, textarea, div, section, label, td, th {
            font-family: "El Messiri", sans-serif !important;
        }
    </style>
    @if($lsData['dir'] === 'rtl')
    <style>
        .tgmenu__nav,
        .tg-header__top-info,
        .footer__content {
            direction: rtl;
            text-align: right;
        }
    </style>
    @endif
</head>

<body>

    @if(session('appointment_success'))
    <div id="appt-toast" style="position:fixed;top:24px;left:50%;transform:translateX(-50%);z-index:99999;background:#1a7f4b;color:#fff;padding:16px 32px;border-radius:12px;font-size:16px;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,.35);display:flex;align-items:center;gap:10px;max-width:90vw;text-align:center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink:0;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
        {{ $lsData['lang']==='ar' ? 'تم إرسال طلب حجزك بنجاح! سنتواصل معك قريباً.' : 'Your appointment request was sent successfully! We will contact you soon.' }}
        <button onclick="document.getElementById('appt-toast').style.display='none'" style="margin-right:8px;background:rgba(255,255,255,.2);border:none;color:#fff;border-radius:50%;width:26px;height:26px;cursor:pointer;font-size:16px;line-height:1;flex-shrink:0;">×</button>
    </div>
    <script>setTimeout(function(){ var t=document.getElementById('appt-toast'); if(t) t.style.display='none'; }, 6000);</script>
    @endif

    @include('landing.partials._header')


    <!-- main-area -->
    <main class="fix">

        <!-- banner-area -->
        <section class="banner__area banner__bg" data-background="{{ $lsData['banner_bg'] }}">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-5 col-lg-6">
                        <div class="banner__content">
                            <h2 class="title" data-aos="fade-up" data-aos-delay="200">
                                <img src="{{ asset('landing/assets/img/banner/banner_title_img01.png') }}" alt="">
                                {{ $lsData['banner_title'] }}
                                <span class="icon"><img
                                        src="{{ asset('landing/assets/img/banner/banner_title_img02.png') }}"
                                        alt=""></span>
                            </h2>
                            <p data-aos="fade-up" data-aos-delay="400">
                                {{ $lsData['banner_desc'] ?: 'Template Kit uses demo images from Envato Elements
                                Follower will need to license these images from Envato.' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-6 col-md-9">
                        <div class="banner__img text-end">
                            <img src="{{ $lsData['banner_img'] }}" alt="img" data-aos="fade-left" data-aos-delay="800">
                            <div class="healthy-pets" data-aos="zoom-in" data-aos-delay="1000">
                                <div class="icon">
                                    <img src="{{ asset('landing/assets/img/icon/pet_icon01.svg') }}" alt=""
                                        class="injectable">
                                </div>
                                <div class="content">
                                    <h6 class="circle rotateme" data-circle-lang="{{ $lsData['lang'] }}">{{ $lsData['lang']==='ar' ? 'رعاية - صحة - حيوانات - محبة' : 'BETTER - HEALTHY - PETS - LOVE -' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="banner__shape-wrap">
                <img src="{{ asset('landing/assets/img/banner/banner_shape01.png') }}" alt="img" data-aos="fade-down"
                    data-aos-delay="1200">
                <img src="{{ asset('landing/assets/img/banner/banner_shape02.png') }}" alt="img"
                    data-aos="fade-up-right" data-aos-delay="1200">
                <img src="{{ asset('landing/assets/img/banner/banner_shape03.png') }}" alt="img" class="ribbonRotate">
                <img src="{{ asset('landing/assets/img/banner/banner_shape04.png') }}" alt="img">
            </div>
        </section>
        <!-- banner-area-end -->

        <!-- about-area -->
        <section class="about__area">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-5 col-lg-6 col-md-8">
                        <div class="about__img">
                            <img src="{{ $lsData['about_img'] }}" alt="">
                            <div class="video__box" style="width: 170px;">
                                <div class="video__box-shape">
                                    <img src="{{ asset('landing/assets/img/images/about_video_shape.svg') }}" alt=""
                                        class="injectable">
                                </div>
                                <h5 class="title">{{ $lsData['lang']==='ar' ? 'شاهد الفيديو' : 'Watch Our Working Video'
                                    }}</h5>
                                <a href="{{ $lsData['about_video_url'] }}" class="popup-video play-btn"><i
                                        class="fas fa-play"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-6">
                        <div class="about__content">
                            <div class="section__title mb-20">
                                <span class="sub-title">{{ $lsData['about_subtitle'] }}
                                    <strong class="shake">
                                        <img src="{{ asset('landing/assets/img/icon/pet_icon02.svg') }}" alt=""
                                            class="injectable">
                                    </strong>
                                </span>
                                <h2 class="title">
                                    @if(trim((string) $lsData['about_title']) === 'منصة واحدة لإدارة عيادتك البيطرية')
                                        <span class="brand-phrase-primary">منصة واحدة</span>
                                        <span class="brand-phrase-secondary">لإدارة عيادتك البيطرية</span>
                                    @else
                                        {{ $lsData['about_title'] }}
                                    @endif
                                </h2>
                            </div>
                            <div class="about__content-inner">
                                <div class="experience__box">
                                    <div class="experience__box-shape">
                                        <img src="{{ asset('landing/assets/img/images/experience_shape.svg') }}" alt=""
                                            class="injectable">
                                    </div>
                                    <div class="experience__box-content">
                                        <h4 class="title">{{ $lsData['about_years'] }} <span>{{ $lsData['lang']==='ar' ?
                                                'سنة' : 'Yr' }}</span></h4>
                                        <p>{{ $lsData['lang']==='ar' ? 'خبرة' : 'Experience' }}</p>
                                    </div>
                                </div>
                                @if($lsData['about_desc1'])
                                <p>{{ $lsData['about_desc1'] }}</p>
                                @endif
                            </div>
                            @if($lsData['about_desc2'])
                            <p>{{ $lsData['about_desc2'] }}</p>
                            @endif
                            <div class="about__content-bottom">
                                <div class="about__content-sign">
                                    <img src="{{ $lsData['about_sign_img'] }}" alt="">
                                </div>
                                <div class="customer__review">
                                    <div class="customer__review-img">
                                        <ul class="list-wrap">
                                            <li><img src="{{ asset('landing/assets/img/images/author_01.png') }}"
                                                    alt=""></li>
                                            <li><img src="{{ asset('landing/assets/img/images/author_02.png') }}"
                                                    alt=""></li>
                                            <li><img src="{{ asset('landing/assets/img/images/author_03.png') }}"
                                                    alt=""></li>
                                            <li><img src="{{ asset('landing/assets/img/images/author_04.png') }}"
                                                    alt=""></li>
                                        </ul>
                                    </div>
                                    <div class="customer__review-content">
                                        <div class="rating">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <span>{{ $lsData['about_rating'] }} ({{ $lsData['about_reviews'] }} {{
                                            $lsData['lang']==='ar' ? 'تقييم' : 'Reviews' }})</span>
                                    </div>
                                </div>
                            </div>
                            <div class="shape">
                                <img src="{{ asset('landing/assets/img/images/about_shape02.png') }}" alt="img"
                                    data-aos="fade-down-left" data-aos-delay="400">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="about__shape-wrap">
                <img src="{{ asset('landing/assets/img/images/about_shape01.png') }}" alt="img" data-aos="fade-up-right"
                    data-aos-delay="800">
                <img src="{{ asset('landing/assets/img/images/about_shape03.png') }}" alt="img" class="ribbonRotate">
            </div>
        </section>
        <!-- about-area-end -->

        <!-- marquee-area -->
        <div class="marquee__area">
            <div class="marquee__wrap">
                @foreach([1,2] as $m)
                <div class="marquee__box">
                    @foreach([1,2,3,4] as $r)
                    <a href="{{ route('landing.contact') }}">
                        {{ $lsData['marquee'] }}
                        <img src="{{ asset('landing/assets/img/images/marquee_icon.svg') }}" alt="">
                    </a>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        <!-- marquee-area-end -->

        <!-- services-area -->
        <section class="services__area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-6 col-lg-7">
                        <div class="section__title mb-40">
                            <span class="sub-title">{{ $lsData['services_subtitle'] }}
                                <strong class="shake">
                                    <img src="{{ asset('landing/assets/img/icon/pet_icon02.svg') }}" alt=""
                                        class="injectable">
                                </strong>
                            </span>
                            <h2 class="title">{{ $lsData['services_title'] }}</h2>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-5">
                        <div class="view__all-btn text-end mb-40">
                            <a href="{{ route('landing.services') }}" class="btn border-btn">
                                {{ $lsData['lang']==='ar' ? 'جميع الخدمات' : 'See All Services' }}
                                <img src="{{ asset('landing/assets/img/icon/right_arrow.svg') }}" alt=""
                                    class="injectable">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @forelse($lsData['services'] as $service)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                        <div class="services__item">
                            <div class="services__shape">
                                <div class="shape-one"><img
                                        src="{{ asset('landing/assets/img/images/services_shape01.svg') }}" alt=""
                                        class="injectable"></div>
                                <div class="shape-two"><img
                                        src="{{ asset('landing/assets/img/images/services_shape02.svg') }}" alt=""
                                        class="injectable"></div>
                            </div>
                            <div class="services__icon">
                                @if($service->image)
                                <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}"
                                    style="width:70px;height:70px;object-fit:cover;border-radius:12px;">
                                @elseif($service->icon)
                                <i class="{{ $service->icon }}"></i>
                                @else
                                <i class="flaticon-vaccine"></i>
                                @endif
                                <div class="services__icon-shape">
                                    <img src="{{ asset('landing/assets/img/images/services_icon_shape.svg') }}" alt=""
                                        class="injectable">
                                </div>
                            </div>
                            <div class="services__content">
                                @php
                                $svcName = ($lsData['lang']==='en' && $service->name_en) ? $service->name_en :
                                $service->name;
                                $svcDesc = ($lsData['lang']==='en' && $service->description_en) ?
                                $service->description_en : $service->description;
                                @endphp
                                <h4 class="title">
                                    <a href="{{ route('landing.contact') }}">{{ $svcName }}</a>
                                </h4>
                                <p>{{ Str::limit($svcDesc, 80) }}</p>
                                <a href="{{ route('landing.contact') }}" class="btn border-btn">
                                    {{ $lsData['lang']==='ar' ? 'التفاصيل' : 'See Details' }}
                                    <img src="{{ asset('landing/assets/img/icon/right_arrow02.svg') }}" alt=""
                                        class="injectable">
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-10 text-gray-400">
                        {{ $lsData['lang']==='ar' ? 'لا توجد خدمات مضافة بعد' : 'No services added yet' }}
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="services__shape-wrap">
                <img src="{{ asset('landing/assets/img/images/services_shape01.png') }}" alt="img" class="ribbonRotate">
                <img src="{{ asset('landing/assets/img/images/services_shape02.png') }}" alt="img"
                    data-aos="fade-up-right" data-aos-delay="800">
                <img src="{{ asset('landing/assets/img/images/services_shape03.png') }}" alt="img"
                    data-aos="fade-down-left" data-aos-delay="400">
            </div>
        </section>
        <!-- services-area-end -->

        <!-- why-we-are-area -->
        <section class="why__we-are-area">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-10">
                        <div class="why__we-are-img">
                            <img src="{{ $lsData['why_img'] }}" alt="">
                            <div class="shape shape-one" data-aos="fade-down-right" data-aos-delay="500">
                                <img src="{{ asset('landing/assets/img/images/why_shape01.svg') }}" alt=""
                                    class="injectable">
                            </div>
                            <div class="shape shape-two" data-aos="fade-up-right" data-aos-delay="500">
                                <img src="{{ asset('landing/assets/img/images/why_shape02.svg') }}" alt=""
                                    class="injectable">
                            </div>
                            <div class="shape shape-three" data-aos="fade-up-left" data-aos-delay="500">
                                <img src="{{ asset('landing/assets/img/images/why_shape03.svg') }}" alt=""
                                    class="injectable">
                            </div>
                            <div class="shape shape-four ribbonRotate">
                                <img src="{{ asset('landing/assets/img/images/why_shape04.svg') }}" alt=""
                                    class="injectable">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="why__we-are-content">
                            <div class="section__title mb-10">
                                <span class="sub-title">{{ $lsData['why_subtitle'] }}
                                    <strong class="shake">
                                        <img src="{{ asset('landing/assets/img/icon/pet_icon02.svg') }}" alt=""
                                            class="injectable">
                                    </strong>
                                </span>
                                <h2 class="title">{{ $lsData['why_title'] }}</h2>
                            </div>
                            @if($lsData['why_desc'])
                            <p>{{ $lsData['why_desc'] }}</p>
                            @endif
                            <div class="why__list-box">
                                <ul class="list-wrap">
                                    @foreach($lsData['why_items'] as $item)
                                    <li>
                                        <div class="why__list-box-item">
                                            <div class="why__list-box-item-top">
                                                <div class="icon">
                                                    <img src="{{ asset('landing/assets/img/icon/check_icon.svg') }}"
                                                        alt="" class="injectable">
                                                </div>
                                                <h4 class="title">{{ $lsData['lang']==='ar' ? ($item['title_ar']??'') :
                                                    ($item['title_en']??'') }}</h4>
                                            </div>
                                            <p>{{ $lsData['lang']==='ar' ? ($item['desc_ar']??'') :
                                                ($item['desc_en']??'') }}</p>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- why-we-are-area-end -->

        <!-- counter-area -->
        <section class="counter__area">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-5 col-md-8 order-0 order-lg-2">
                        <div class="counter__img">
                            <div class="mask-img-wrap">
                                <img src="{{ $lsData['counter_img'] }}" alt="img">
                            </div>
                            <div class="counter__img-shape">
                                <img src="{{ asset('landing/assets/img/images/counter_img_shape.svg') }}" alt=""
                                    class="injectable">
                            </div>
                            <div class="shape">
                                <img src="{{ asset('landing/assets/img/images/counter_shape01.png') }}" alt="img"
                                    class="ribbonRotate">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-7">
                        <div class="counter__content">
                            <div class="section__title white-title mb-10">
                                <span class="sub-title">{{ $lsData['counter_subtitle'] }}
                                    <strong class="shake">
                                        <img src="{{ asset('landing/assets/img/icon/pet_icon02.svg') }}" alt=""
                                            class="injectable">
                                    </strong>
                                </span>
                                <h2 class="title">{{ $lsData['counter_title'] }}</h2>
                            </div>
                            @if($lsData['counter_desc'])
                            <p>{{ $lsData['counter_desc'] }}</p>
                            @endif
                            <a href="{{ route('landing.about') }}" class="btn border-btn white-btn">
                                {{ $lsData['lang']==='ar' ? 'اقرأ المزيد' : 'Read More' }}
                                <img src="{{ asset('landing/assets/img/icon/right_arrow.svg') }}" alt=""
                                    class="injectable">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5 order-3">
                        <div class="counter__item-wrap">
                            @foreach($lsData['counter_items'] as $ci)
                            <div class="counter__item">
                                <h2 class="count">
                                    <span class="odometer" data-count="{{ $ci['num'] ?? 0 }}"></span>{{ $ci['suffix'] ??
                                    '+' }}
                                </h2>
                                <p>{{ $lsData['lang']==='ar' ? ($ci['label_ar']??'') : ($ci['label_en']??'') }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="counter__shape">
                <img src="{{ asset('landing/assets/img/images/counter_shape02.png') }}" alt="img"
                    data-aos="fade-up-left" data-aos-delay="400">
            </div>
        </section>
        <!-- counter-area-end -->

        <!-- brand-area -->
        <div class="brand__area">
            <div class="container">
                <div class="brand__item-wrap">
                    <div class="swiper brand-active">
                        <div class="swiper-wrapper">
                            @if(count($lsData['brand_logos']) > 0)
                            @foreach($lsData['brand_logos'] as $logo)
                            <div class="swiper-slide">
                                <div class="brand__item">
                                    <img src="{{ $logo }}" alt="brand">
                                </div>
                            </div>
                            @endforeach
                            @else
                            @foreach(range(1,7) as $b)
                            <div class="swiper-slide">
                                <div class="brand__item">
                                    <img src="{{ asset('landing/assets/img/brand/brand_img0'.$b.'.png') }}" alt="brand">
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- brand-area-end -->

        <!-- team-area -->
        <section class="team__area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="section__title text-center mb-40">
                            <span class="sub-title">{{ $lsData['lang']==='ar' ? 'نغير حياتك وعالمك' : 'WE CHANGE YOUR
                                LIFE & WORLD' }}
                                <strong class="shake">
                                    <img src="{{ asset('landing/assets/img/icon/pet_icon02.svg') }}" alt=""
                                        class="injectable">
                                </strong>
                            </span>
                            <h2 class="title">{{ $lsData['lang']==='ar' ? 'تعرف على أطبائنا المتخصصين' : 'Meet Our
                                Expertise Pet Doctors' }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @forelse($lsData['doctors'] as $doctor)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                        <div class="team__item">
                            <div class="team__item-img">
                                <div class="mask-img-wrap">
                                    @if($doctor->profile_photo_path)
                                    <img src="{{ Storage::url($doctor->profile_photo_path) }}"
                                        alt="{{ $doctor->name }}">
                                    @else
                                    <img src="{{ asset('landing/assets/img/team/team_img01.jpg') }}"
                                        alt="{{ $doctor->name }}">
                                    @endif
                                </div>
                                <div class="team__item-img-shape">
                                    <div class="shape-one"><img
                                            src="{{ asset('landing/assets/img/team/team_img_shape01.svg') }}" alt=""
                                            class="injectable"></div>
                                    <div class="shape-two"><img
                                            src="{{ asset('landing/assets/img/team/team_img_shape02.svg') }}" alt=""
                                            class="injectable"></div>
                                </div>
                                <div class="team__social">
                                    <ul class="list-wrap">
                                        @if($lsData['whatsapp'])<li><a href="{{ $lsData['whatsapp'] }}"
                                                target="_blank"><i class="fab fa-whatsapp"></i></a></li>@endif
                                        @if($lsData['facebook'])<li><a href="{{ $lsData['facebook'] }}"
                                                target="_blank"><i class="fab fa-facebook-f"></i></a></li>@endif
                                        @if($lsData['instagram'])<li><a href="{{ $lsData['instagram'] }}"
                                                target="_blank"><i class="fab fa-instagram"></i></a></li>@endif
                                    </ul>
                                </div>
                            </div>
                            <div class="team__item-content">
                                @php
                                $docName = $lsData['lang']==='en'
                                ? ($doctor->name_en ?: $doctor->name)
                                : $doctor->name;
                                $spec = $lsData['lang']==='en'
                                ? ($doctor->specialty_en ?: ($doctor->specialty_ar ?: 'Veterinary Doctor'))
                                : ($doctor->specialty_ar ?: ($doctor->specialty_en ?: 'مستشار بيطري'));
                                @endphp
                                <h4 class="title"><a href="{{ route('landing.contact') }}">{{ $docName }}</a></h4>
                                <span>{{ $spec }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-gray-400 py-8">
                        {{ $lsData['lang']==='ar' ? 'لم يتم إضافة مستشارين بعد' : 'No doctors added yet' }}
                    </div>
                    @endforelse
                </div>
                <div class="team__bottom-content">
                    <a href="{{ route('landing.team') }}" class="btn">
                        {{ $lsData['lang']==='ar' ? 'جميع المستشارين' : 'See All Doctors' }}
                        <img src="{{ asset('landing/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable">
                    </a>
                </div>
            </div>
            <div class="team__shape">
                <img src="{{ asset('landing/assets/img/team/team_shape.png') }}" alt="img" class="ribbonRotate">
            </div>
        </section>
        <!-- team-area-end -->
        <!-- testimonial-area -->
        <section class="testimonial__area">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-6 col-md-8 order-0 order-lg-2">
                        <div class="testimonial__img">
                            <div class="mask-img testimonial__img-mask">
                                <img src="{{ $lsData['testimonial_img'] }}" alt="img">
                            </div>
                            <div class="testimonial__img-shape">
                                <div class="shape-one">
                                    <img src="/landing/assets/img/images/testimonial_img_shape.svg" alt="" class="injectable">
                                </div>
                                <div class="shape-two">
                                    <img src="/landing/assets/img/images/testimonial_shape03.png" alt="img" class="alltuchtopdown">
                                </div>
                            </div>
                            <div class="review__box">
                                <div class="review__box-shape">
                                    <img src="/landing/assets/img/images/review_shape.svg" alt="" class="injectable">
                                </div>
                                <div class="review__box-content">
                                    <img src="/landing/assets/img/icon/star.svg" alt="" class="injectable">
                                    <h2 class="title">{{ $lsData['testimonial_reviews'] }}</h2>
                                    <span>{{ $lsData['lang']==='ar' ? 'تقييم' : 'Reviews' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="testimonial__item-wrap">
                            <div class="swiper testimonial-active">
                                <div class="swiper-wrapper">
                                    @foreach($lsData['testimonials'] as $testi)
                                    @php
                                        $testiTitle = $lsData['lang']==='en' ? ($testi['title_en'] ?? $testi['title_ar']) : $testi['title_ar'];
                                        $testiText  = $lsData['lang']==='en' ? ($testi['text_en']  ?? $testi['text_ar'])  : $testi['text_ar'];
                                        $testiRole  = $lsData['lang']==='en' ? ($testi['role_en']  ?? $testi['role_ar'])  : $testi['role_ar'];
                                    @endphp
                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="/landing/assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <h2 class="title">{{ $testiTitle }}</h2>
                                                <p>" {{ $testiText }} “</p>
                                                <div class="testimonial__author">
                                                    <div class="testimonial__author-thumb">
                                                        @if(!empty($testi['author_photo']))
                                                            <img src="{{ url('storage/' . $testi['author_photo']) }}" alt="{{ $testi['author_name'] }}">
                                                        @else
                                                            <img src="/landing/assets/img/images/testi_author01.png" alt="{{ $testi['author_name'] }}">
                                                        @endif
                                                    </div>
                                                    <div class="testimonial__author-content">
                                                        <h4 class="title">{{ $testi['author_name'] }}</h4>
                                                        <span>{{ $testiRole }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial__shape-wrap">
                <img src="/landing/assets/img/images/testimonial_shape01.png" alt="img" data-aos="fade-down-right" data-aos-delay="400">
                <img src="/landing/assets/img/images/testimonial_shape02.png" alt="img" data-aos="fade-right" data-aos-delay="400">
            </div>
        </section>
        <!-- testimonial-area-end -->
        <!-- registration-area-end -->

        <!-- blog-post-area -->
        <section class="blog__post-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="section__title mb-40">
                            <span class="sub-title">{{ $lsData['blog_subtitle'] }}
                                <strong class="shake">
                                    <img src="{{ asset('landing/assets/img/icon/pet_icon02.svg') }}" alt="" class="injectable">
                                </strong>
                            </span>
                            <h2 class="title">{{ $lsData['blog_title'] }}</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="view__all-btn text-end mb-40">
                            <a href="{{ route('landing.blog') }}" class="btn btn-two">
                                {{ $lsData['blog_btn'] }}
                                <img src="{{ asset('landing/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @forelse($homeBlogPosts ?? [] as $post)
                    @php
                        $cardTitle = ($lsData['lang']==='en' && $post->title_en) ? $post->title_en : $post->title;
                        $cardTags  = ($lsData['lang']==='en') ? ($post->tags_en ?? $post->tags ?? []) : ($post->tags ?? []);
                        $cardLink  = route('landing.blog-details', $post->id);
                        $cardDate  = optional($post->published_at)->format('d M Y') ?? $post->created_at->format('d M Y');
                        $cardImage = $post->image ? asset('storage/' . $post->image) : asset('landing/assets/img/blog/blog_post0' . ($loop->index + 1) . '.jpg');
                    @endphp
                    <div class="col-lg-4 col-md-6 col-sm-8">
                        <div class="blog__post-item shine-animate-item">
                            <div class="blog__post-thumb">
                                <div class="blog__post-mask shine-animate">
                                    <a href="{{ $cardLink }}"><img src="{{ $cardImage }}" alt="{{ $cardTitle }}"></a>
                                    @if(!empty($cardTags))
                                    <ul class="list-wrap blog__post-tag">
                                        @foreach(array_slice((array)$cardTags, 0, 2) as $tag)
                                        <li><a href="{{ route('landing.blog') }}">{{ $tag }}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                                <div class="shape">
                                    <img src="{{ asset('landing/assets/img/blog/blog_img_shape.svg') }}" alt="" class="injectable">
                                </div>
                            </div>
                            <div class="blog__post-content">
                                <div class="blog__post-meta">
                                    <ul class="list-wrap">
                                        <li><i class="flaticon-calendar"></i>{{ $cardDate }}</li>
                                    </ul>
                                </div>
                                <h2 class="title"><a href="{{ $cardLink }}">{{ $cardTitle }}</a></h2>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-4">
                        <p style="color:#888;">{{ $lsData['lang']==='ar' ? 'لا توجد مقالات منشورة بعد' : 'No published posts yet' }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="blog__shape-wrap">
                <img src="{{ asset('landing/assets/img/blog/blog_shape01.png') }}" alt="img" data-aos="fade-up-right" data-aos-delay="400">
                <img src="{{ asset('landing/assets/img/blog/blog_shape02.png') }}" alt="img" class="ribbonRotate">
            </div>
        </section>
        <!-- blog-post-area-end -->
        <!-- instagram-area -->
        <div class="instagram__area">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="instagram__follow-btn">
                            <a href="{{ $lsData['instagram'] ?: 'https://www.instagram.com/' }}" target="_blank">
                                {{ $lsData['instagram_btn'] }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="swiper instagram-active">
                    <div class="swiper-wrapper">
                        @foreach($lsData['instagram_images'] as $i => $imgSrc)
                        @php $imgLink = $lsData['instagram_links'][$i] ?: ($lsData['instagram'] ?: 'https://www.instagram.com/'); @endphp
                        <div class="swiper-slide">
                            <div class="instagram__item">
                                <a href="{{ $imgLink }}" target="_blank">
                                    <img src="{{ $imgSrc }}" alt="">
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- instagram-area-end -->

    </main>
    <!-- main-area-end -->


    @include('landing.partials._footer')


    <!-- JS here -->
    <script src="{{ asset('landing/assets/js/vendor/jquery-3.6.0.min.js') }}"
        onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'"></script>
    <script src="{{ asset('landing/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.odometer.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.appear.js') }}"></script>
    <script src="{{ asset('landing/assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/svg-inject.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/ajax-form.js') }}"></script>
    <script src="{{ asset('landing/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/aos.js') }}"></script>
    <script src="{{ asset('landing/assets/js/main.js') }}"></script>
    <script>
        SVGInject(document.querySelectorAll("img.injectable"));
    $(function () {
        $('.circle').each(function () {
            var text = $(this).text().replace(/\s+/g, ' ').trim();
            var isArabic = $(this).data('circle-lang') === 'ar' || /[\u0600-\u06FF]/.test(text);
            var items;

            if (isArabic) {
                items = text.split(/\s*-\s*/).map(function (part) {
                    return part.trim();
                }).filter(Boolean);
                $(this).addClass('is-words');
            } else {
                items = text.split('').filter(function (ch) {
                    return ch.trim() !== '';
                });
            }

            var total = items.length;
            if (!total) {
                return;
            }

            var deg = 360 / total;
            var html = '';
            $.each(items, function (i, item) {
                html += '<span style="transform:rotate(' + (deg * i) + 'deg)">' + item + '</span>';
            });
            $(this).html(html);
        });

        // إعادة تهيئة Swiper للتقييمات
        var testiEl = document.querySelector('.testimonial-active');
        if (testiEl) {
            if (testiEl.swiper) { testiEl.swiper.destroy(true, true); }
            var slideCount = testiEl.querySelectorAll('.swiper-slide').length;
            new Swiper('.testimonial-active', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: slideCount > 1,
                autoplay: { delay: 5000, disableOnInteraction: false },
                pagination: { el: '.testimonial-active .swiper-pagination', clickable: true },
            });
        }
    });
    </script>


    @if(!empty($lsData['whatsapp_number']))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lsData['whatsapp_number']) }}" target="_blank" rel="noopener noreferrer"
       style="position:fixed;bottom:24px;left:24px;z-index:9999;background:#25D366;color:#fff;width:58px;height:58px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,0.45);text-decoration:none;transition:transform 0.2s,box-shadow 0.2s;"
       onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 28px rgba(37,211,102,0.65)'"
       onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 20px rgba(37,211,102,0.45)'">
        <i class="fab fa-whatsapp" style="font-size:30px;line-height:1;"></i>
    </a>
    @endif

</body>

</html>