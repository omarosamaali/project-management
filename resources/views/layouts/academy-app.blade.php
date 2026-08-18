<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.academy'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/fav-icon.webp') }}">
    @include('partials.reverb-config')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ac-ink: #061525;
            --ac-ink-soft: #0e3a5c;
            --ac-gold: #d4a017;
            --ac-teal: #0b8f7f;
            --ac-coral: #e85d4c;
            --ac-line: #d4e0ec;
            --ac-muted: #5a6d82;
            --ac-sand: #f0f4f8;
            --ac-font: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
            --ac-display: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif;
            --ac-max: min(96rem, 100%);
        }
        body.academy-shell {
            font-family: var(--ac-font);
            background:
                radial-gradient(900px 380px at 100% 0%, rgba(212,160,23,.09), transparent 55%),
                radial-gradient(700px 320px at 0% 40%, rgba(11,143,127,.07), transparent 50%),
                var(--ac-sand);
            color: var(--ac-ink);
            min-height: 100dvh;
            margin: 0;
        }

        /* Creative top navbar */
        .ac-navbar {
            position: sticky; top: 0; z-index: 50;
            padding: .65rem clamp(.75rem, 2vw, 1.25rem) .35rem;
        }
        .ac-navbar-shell {
            max-width: var(--ac-max);
            margin: 0 auto;
            display: flex; align-items: center; gap: .75rem;
            padding: .55rem .65rem .55rem .85rem;
            border-radius: 1.35rem;
            background:
                radial-gradient(420px 120px at 0% 0%, rgba(11,143,127,.28), transparent 60%),
                linear-gradient(120deg, #061525 0%, #0a2f45 55%, #0c3d48 100%);
            border: 1px solid rgba(255,255,255,.08);
            box-shadow: 0 16px 40px rgba(6,21,37,.22);
            backdrop-filter: blur(14px);
        }
        .ac-brand {
            display: inline-flex; align-items: center; gap: .55rem;
            text-decoration: none; color: #fff; flex-shrink: 0;
        }
        .ac-brand img {
            height: 2.35rem; width: auto; max-width: 9.5rem; object-fit: contain;
        }
        .ac-brand-mark {
            display: none;
            flex-direction: column; line-height: 1.15;
        }
        @media (min-width: 1100px) { .ac-brand-mark { display: flex; } }
        .ac-brand-mark strong {
            font-family: var(--ac-display); font-size: .82rem; font-weight: 800;
        }
        .ac-brand-mark small {
            font-size: .65rem; color: rgba(255,255,255,.55); font-weight: 600;
        }

        .ac-nav-pills {
            display: none;
            align-items: center; gap: .25rem;
            margin-inline: auto;
            padding: .28rem;
            border-radius: 999px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.08);
            max-width: 100%;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .ac-nav-pills::-webkit-scrollbar { display: none; }
        @media (min-width: 900px) { .ac-nav-pills { display: flex; } }
        .ac-nav-pill {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .55rem .9rem; border-radius: 999px;
            color: rgba(255,255,255,.72); text-decoration: none;
            font-size: .8rem; font-weight: 800; white-space: nowrap;
            transition: background .18s, color .18s, transform .18s;
        }
        .ac-nav-pill i { font-size: .78rem; opacity: .85; }
        .ac-nav-pill:hover {
            background: rgba(255,255,255,.1); color: #fff;
            transform: translateY(-1px);
        }
        .ac-nav-pill.is-active {
            background: linear-gradient(135deg, #0b8f7f, #0e6e63);
            color: #fff;
            box-shadow: 0 8px 18px rgba(11,143,127,.3);
        }

        .ac-nav-actions {
            display: flex; align-items: center; gap: .45rem;
            margin-inline-start: auto; flex-shrink: 0;
        }
        @media (min-width: 900px) {
            .ac-nav-actions { margin-inline-start: 0; }
        }

        .ac-user-chip {
            display: inline-flex; align-items: center; gap: .55rem;
            padding: .45rem .85rem .45rem .45rem;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.1);
            text-decoration: none; color: #fff;
            max-width: 13rem;
        }
        .ac-user-chip img {
            width: 2.15rem; height: 2.15rem; border-radius: 999px; object-fit: cover;
            border: 2px solid rgba(255,255,255,.25);
            background: #1a3a52;
        }
        .ac-user-chip .meta { min-width: 0; display: none; padding-inline-end: .15rem; }
        @media (min-width: 640px) { .ac-user-chip .meta { display: block; } }
        .ac-user-chip .name {
            display: block; font-size: .75rem; font-weight: 800;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 7rem;
            line-height: 1.25;
        }
        .ac-user-chip .role {
            display: block; font-size: .65rem; color: rgba(255,255,255,.55); font-weight: 600;
            line-height: 1.2; margin-top: .1rem;
        }
        .ac-icon-btn {
            width: 2.15rem; height: 2.15rem; border-radius: 999px; border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.06); color: #fff; display: inline-flex;
            align-items: center; justify-content: center; text-decoration: none; cursor: pointer;
            transition: background .15s, color .15s, border-color .15s;
        }
        .ac-icon-btn:hover {
            background: rgba(232,93,76,.18); border-color: rgba(232,93,76,.35); color: #ffb4a8;
        }

        .ac-page-title {
            max-width: var(--ac-max);
            margin: 0 auto;
            padding: .35rem clamp(1rem, 2.5vw, 1.75rem) 0;
            display: none;
            align-items: baseline; justify-content: space-between; gap: .75rem;
        }
        @media (min-width: 900px) { .ac-page-title { display: flex; } }
        .ac-page-title h1 {
            margin: 0; font-size: 1.05rem; font-weight: 800;
            font-family: var(--ac-display);
        }
        .ac-page-title .hint {
            margin: 0; font-size: .78rem; color: var(--ac-muted); font-weight: 600;
        }

        .ac-main {
            max-width: var(--ac-max); width: 100%; margin: 0 auto;
            padding: 1rem 1rem 6.5rem;
        }
        @media (min-width: 768px) {
            .ac-main { padding: 1.15rem clamp(1rem, 2.5vw, 1.75rem) 2.5rem; }
        }
        @media (min-width: 900px) {
            .ac-main { padding-bottom: 2rem; }
        }
        @media (min-width: 1600px) {
            .ac-main { padding-inline: 2rem; }
        }

        /* Floating bottom nav — mobile/tablet */
        .ac-bottom {
            position: fixed; inset-inline: .75rem; bottom: .75rem; z-index: 40;
            background: rgba(6,21,37,.94); border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(14px);
            padding: .4rem;
            display: grid; gap: .2rem;
            border-radius: 1.25rem;
            box-shadow: 0 16px 40px rgba(6,21,37,.28);
        }
        @media (min-width: 900px) { .ac-bottom { display: none; } }
        .ac-bottom-link {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: .2rem; padding: .5rem .2rem; border-radius: .95rem;
            text-decoration: none; color: rgba(255,255,255,.62); font-size: .65rem; font-weight: 700;
            min-width: 0; transition: background .15s, color .15s;
        }
        .ac-bottom-link i { font-size: 1.05rem; }
        .ac-bottom-link span {
            max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .ac-bottom-link.is-active {
            color: #fff; background: linear-gradient(135deg, #0b8f7f, #0e6e63);
        }

        .ac-flash {
            margin-bottom: 1rem; padding: .85rem 1rem; border-radius: 1rem;
            font-size: .875rem; font-weight: 600;
        }
        .ac-flash-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .ac-flash-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .academy-shell .ac-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
            padding: .55rem 1rem; border-radius: 999px; border: 0;
            font-size: .875rem; font-weight: 800; line-height: 1.2;
            text-decoration: none !important; cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease, filter .15s ease;
            -webkit-appearance: none; appearance: none;
            font-family: inherit;
        }
        .academy-shell .ac-btn:hover { transform: translateY(-1px); }
        .academy-shell .ac-btn-sm { padding: .5rem .85rem; font-size: .8rem; min-height: 2.25rem; }
        .academy-shell .ac-btn-primary {
            background: linear-gradient(135deg, var(--ac-ink), var(--ac-ink-soft)) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 18px rgba(6,21,37,.16);
        }
        .academy-shell .ac-btn-primary:hover,
        .academy-shell .ac-btn-primary:focus {
            background: var(--ac-teal) !important;
            color: #ffffff !important;
        }
        .academy-shell .ac-btn-amber {
            background-color: var(--ac-gold) !important;
            color: #ffffff !important;
        }
        .academy-shell .ac-btn-amber:hover { filter: brightness(1.05); color: #ffffff !important; }
        .academy-shell .ac-btn-ghost {
            background-color: #e8eef5 !important;
            color: var(--ac-ink) !important;
            border: 1px solid #d0dae6 !important;
        }
        .academy-shell .ac-btn-ghost:hover {
            background-color: #dbe4ef !important;
            color: var(--ac-ink) !important;
        }
        .academy-shell .ac-btn-danger {
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
            border: 1px solid #fecaca !important;
        }
        .academy-shell .ac-btn-danger:hover {
            background-color: #fee2e2 !important;
            color: #b91c1c !important;
        }
        .academy-shell .ac-card-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .5rem;
            margin-top: auto;
            padding-top: .75rem;
            width: 100%;
        }
        .academy-shell .ac-card-actions .ac-btn,
        .academy-shell .ac-card-actions form,
        .academy-shell .ac-card-actions form .ac-btn {
            width: 100%;
        }
        .academy-shell .ac-card-actions form { margin: 0; display: block; }
        .academy-shell .ac-card-actions-auto {
            display: flex; flex-wrap: wrap; gap: .5rem;
            margin-top: auto; padding-top: .75rem; width: 100%;
        }
        .academy-shell .ac-card-actions-auto .ac-btn {
            flex: 1 1 auto; min-width: 5.75rem;
        }
        .academy-shell .ac-pagination {
            display: flex; justify-content: center; width: 100%;
        }
        .academy-shell .ac-pagination nav {
            display: inline-flex; flex-wrap: wrap; gap: .35rem;
            align-items: center; justify-content: center;
        }
        .academy-shell .ac-pagination span,
        .academy-shell .ac-pagination a {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 2.25rem; height: 2.25rem; padding: 0 .65rem;
            border-radius: .7rem; font-size: .8rem; font-weight: 700;
            text-decoration: none; border: 1px solid var(--ac-line);
            background: #fff; color: var(--ac-ink);
        }
        .academy-shell .ac-pagination span[aria-current="page"] span,
        .academy-shell .ac-pagination [aria-current="page"] {
            background: var(--ac-ink) !important;
            color: #fff !important;
            border-color: var(--ac-ink) !important;
        }
        .academy-shell .ac-pagination span[aria-disabled="true"] span {
            opacity: .45; cursor: default;
        }
        .academy-shell h1, .academy-shell .ac-display {
            font-family: var(--ac-display);
        }

        /* Course forms — remap legacy blue/indigo to academy palette */
        .academy-shell .tab-button:hover {
            color: var(--ac-teal) !important;
            background-color: rgba(11,143,127,.1) !important;
        }
        .academy-shell .tab-button.active {
            color: var(--ac-ink) !important;
            border-bottom-color: var(--ac-teal) !important;
            background-color: rgba(11,143,127,.12) !important;
        }
        .academy-shell .bg-blue-600,
        .academy-shell .bg-indigo-600 {
            background-color: var(--ac-teal) !important;
        }
        .academy-shell .hover\:bg-blue-700:hover,
        .academy-shell .hover\:bg-indigo-700:hover {
            background-color: #0e6e63 !important;
        }
        .academy-shell .text-blue-600,
        .academy-shell .text-indigo-600,
        .academy-shell .hover\:text-indigo-800:hover {
            color: var(--ac-teal) !important;
        }
        .academy-shell .text-blue-700,
        .academy-shell .text-indigo-800,
        .academy-shell .text-indigo-700 {
            color: #0c4a55 !important;
        }
        .academy-shell .bg-blue-50,
        .academy-shell .bg-indigo-50,
        .academy-shell .bg-indigo-50\/40 {
            background-color: #e4f6f3 !important;
        }
        .academy-shell .border-blue-200,
        .academy-shell .border-indigo-200,
        .academy-shell .border-indigo-100 {
            border-color: #b7e5de !important;
        }
        .academy-shell .border-blue-300 {
            border-color: #7dcec4 !important;
        }
        .academy-shell .border-blue-500 {
            border-color: var(--ac-teal) !important;
        }
        .academy-shell .hover\:bg-blue-50:hover,
        .academy-shell .hover\:border-blue-500:hover {
            background-color: #e4f6f3 !important;
            border-color: var(--ac-teal) !important;
        }
        .academy-shell .focus\:ring-blue-500:focus,
        .academy-shell .focus\:ring-indigo-500:focus {
            --tw-ring-color: rgba(11,143,127,.35) !important;
            border-color: var(--ac-teal) !important;
        }
        .academy-shell .focus\:border-blue-500:focus {
            border-color: var(--ac-teal) !important;
        }
        .academy-shell .peer-focus\:ring-blue-300:focus,
        .academy-shell .focus\:ring-blue-300:focus {
            --tw-ring-color: rgba(11,143,127,.3) !important;
        }
        .academy-shell .peer-checked\:bg-blue-600:checked {
            background-color: var(--ac-teal) !important;
        }
        .academy-shell input[type="radio"],
        .academy-shell input[type="checkbox"] {
            accent-color: var(--ac-teal);
        }
        .academy-shell .path-item-exam {
            border-right-color: var(--ac-teal) !important;
        }
        .academy-shell label:has(input[type="radio"]:checked),
        .academy-shell label:has(input[type="checkbox"]:checked) {
            border-color: var(--ac-teal);
        }
    </style>
    @stack('styles')
</head>
<body class="academy-shell">
@php
    /** @var \App\Models\User $acUser */
    $acUser = Auth::user();
    $isTrainer = $acUser->isTrainer();
    $isTrainee = $acUser->isTrainee();

    $navItems = [];
    $navItems[] = [
        'label' => 'الرئيسية',
        'icon' => 'fas fa-home',
        'route' => route('dashboard'),
        'active' => request()->routeIs('dashboard'),
    ];

    if ($isTrainee || $isTrainer) {
        $navItems[] = [
            'label' => 'دوراتي',
            'icon' => 'fas fa-graduation-cap',
            'route' => route('dashboard.my_courses.index'),
            'active' => request()->routeIs('dashboard.my_courses.*'),
        ];
        $navItems[] = [
            'label' => 'طلباتي الخاصة',
            'icon' => 'fas fa-user-lock',
            'route' => route('dashboard.academy.private-requests.trainee-index'),
            'active' => request()->routeIs('dashboard.academy.private-requests.trainee-index'),
        ];
    }

    if ($isTrainer) {
        $navItems[] = [
            'label' => 'طلبات الدورات الخاصة',
            'icon' => 'fas fa-inbox',
            'route' => route('dashboard.academy.private-requests.trainer-inbox'),
            'active' => request()->routeIs('dashboard.academy.private-requests.trainer-inbox'),
        ];
        $navItems[] = [
            'label' => 'إدارة الدورات',
            'icon' => 'fas fa-chalkboard-teacher',
            'route' => route('dashboard.courses.index'),
            'active' => request()->routeIs('dashboard.courses.*') && !request()->routeIs('dashboard.course-categories.*'),
        ];
        $navItems[] = [
            'label' => 'أرباحي',
            'icon' => 'fas fa-wallet',
            'route' => route('dashboard.academy.my-profits'),
            'active' => request()->routeIs('dashboard.academy.my-profits'),
        ];
        $navItems[] = [
            'label' => 'أيام الإجازة',
            'icon' => 'fas fa-calendar-times',
            'route' => route('dashboard.academy.off-days.index'),
            'active' => request()->routeIs('dashboard.academy.off-days.*'),
        ];
        $navItems[] = [
            'label' => 'إعدادات الدفع',
            'icon' => 'fas fa-university',
            'route' => route('dashboard.academy.payment-profile.edit'),
            'active' => request()->routeIs('dashboard.academy.payment-profile.*'),
        ];
    }

    $navItems[] = [
        'label' => 'الأكاديمية',
        'icon' => 'fas fa-globe',
        'route' => route('academy.index'),
        'active' => request()->routeIs('academy.*') || request()->routeIs('courses.show'),
    ];

    $navItems[] = [
        'label' => 'حسابي',
        'icon' => 'fas fa-user',
        'route' => route('profile.edit'),
        'active' => request()->routeIs('profile.*'),
    ];

    $bottomItems = array_slice($navItems, 0, 5);
    $pageTitle = trim($__env->yieldContent('title')) ?: __('messages.academy');
    $roleLabel = $isTrainer ? 'محاضر' : ($isTrainee ? 'متدرب' : 'مستخدم');
@endphp

<header class="ac-navbar">
    <div class="ac-navbar-shell">
        <a href="{{ route('dashboard') }}" class="ac-brand">
            <img src="{{ \App\Models\Setting::academyChromeLogoUrl() }}" alt="{{ __('messages.academy') }}">
            <span class="ac-brand-mark">
                <strong>{{ __('messages.academy') }}</strong>
                <small>{{ $roleLabel }}</small>
            </span>
        </a>

        <nav class="ac-nav-pills" aria-label="القائمة الرئيسية">
            @foreach($navItems as $item)
            <a href="{{ $item['route'] }}" class="ac-nav-pill {{ $item['active'] ? 'is-active' : '' }}">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        <div class="ac-nav-actions">
            <a href="{{ route('profile.edit') }}" class="ac-user-chip" title="{{ $acUser->name }}">
                <img src="{{ $acUser->avatarUrl() }}" alt=""
                    onerror="this.onerror=null;this.src='{{ $acUser->letterAvatarDataUri() }}';">
                <span class="meta">
                    <span class="name">{{ $acUser->name }}</span>
                    <span class="role">{{ $roleLabel }}</span>
                </span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="ac-icon-btn" title="تسجيل الخروج" aria-label="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<div class="ac-page-title">
    <h1>{{ $pageTitle }}</h1>
    <p class="hint">{{ $roleLabel }} · {{ $acUser->name }}</p>
</div>

<main class="ac-main">
    @yield('content')
</main>

<nav class="ac-bottom" style="grid-template-columns: repeat({{ max(1, count($bottomItems)) }}, minmax(0, 1fr));" aria-label="التنقل السفلي">
    @foreach($bottomItems as $item)
    <a href="{{ $item['route'] }}" class="ac-bottom-link {{ $item['active'] ? 'is-active' : '' }}">
        <i class="{{ $item['icon'] }}"></i>
        <span>{{ $item['label'] }}</span>
    </a>
    @endforeach
</nav>

@include('partials.custom-dialogs')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .academy-shell .swal2-popup {
        border-radius: 1.25rem !important;
        font-family: inherit !important;
        padding: 1.5rem 1.25rem 1.35rem !important;
    }
    .academy-shell .swal2-title {
        font-size: 1.15rem !important;
        font-weight: 800 !important;
        color: #061525 !important;
    }
    .academy-shell .swal2-html-container {
        font-size: .92rem !important;
        color: #5a6d82 !important;
        font-weight: 600 !important;
    }
    .academy-shell .swal2-confirm {
        border-radius: 999px !important;
        font-weight: 800 !important;
        padding: .55rem 1.35rem !important;
        box-shadow: 0 8px 18px rgba(6,21,37,.16) !important;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: @json(session('success')),
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#0D2444',
        });
    }
    @elseif(session('error'))
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'تنبيه',
            text: @json(session('error')),
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#0D2444',
        });
    }
    @endif
});
</script>
<script>
@auth
@if(auth()->user()->role !== 'admin')
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.setupDayExamRealtime === 'function') {
        window.setupDayExamRealtime({
            checkUrl: @json(route('dashboard.courses.exam.pending-check')),
            userId: {{ (int) auth()->id() }},
        });
    }
});
@endif
@endauth
</script>
@stack('scripts')
</body>
</html>
