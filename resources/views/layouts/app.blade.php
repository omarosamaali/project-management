<!DOCTYPE html>
<html class="light" lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'قُمرة القيادة')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/fav-icon.webp') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/css/intlTelInput.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        {{-- Navbar --}}
        <nav
            class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
            <div class="flex flex-wrap justify-between items-center">
                <div class="flex justify-start items-center">
                    <button data-drawer-target="drawer-navigation" data-drawer-toggle="drawer-navigation"
                        aria-controls="drawer-navigation"
                        class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg aria-hidden="true" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Toggle sidebar</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between md:mr-24">
                        <img src="{{ asset('assets/images/logo.webp') }}" class="h-20" alt="">
                    </a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('system.index') }}"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        الموقع الخارجي
                    </a>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="py-3 px-4">
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{
                                    Auth::user()->name }}</span>
                                <span class="block text-sm text-gray-900 truncate dark:text-white">{{
                                    Auth::user()->email }}</span>
                            </div>
                            <x-dropdown-link class="border-t" :href="route('profile.edit')">
                                {{ __('ملفي الشخصي') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" class="border-t" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('تسجيل الخروج') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside
            class="fixed top-0 right-0 z-40 w-64 h-screen pt-24 transition-transform translate-x-full bg-white border-l border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
            aria-label="Sidenav" id="drawer-navigation">
            <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
                <input type="text" id="searchInput" name="search"
                    class="mb-3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="بحث">
                <ul class="space-y-2">
                    <span class="block text-sm text-black dark:text-white font-bold px-2 ">اهلا
                        {{ Auth::user()->name }}</span>
                    @if(Auth::user()->role != 'client')
                    <li>
                        <a href="{{ route('dashboard.performance.show') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.performance.show' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.performance.show' ? 'text-white' : '' }} fa fa-marker text-gray-500 pl-2"></i>
                            <span class="ml-3">ادائي</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.tasks.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.tasks.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.tasks.index' ? 'text-white' : '' }} fab fa-chrome text-gray-500 pl-2"></i>
                            <span class="ml-3">الطلبات</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->role == 'partner')
                    <li>
                        <a href="{{ route('dashboard.withdrawal-requests.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.withdrawal-requests.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.withdrawal-requests.index' ? 'text-white' : '' }} fa fa-marker text-gray-500 pl-2"></i>
                            <span class="ml-3">أرباحي</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->role != 'admin')
                    <li>
                        <a href="{{ route('profile.edit') }}"
                            class="{{ Route::currentRouteName() == 'profile.edit' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'profile.edit' ? 'text-white' : '' }} fas fa-cog text-gray-500 pl-2"></i>
                            <span class="ml-3">الإعدادات</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->role == 'partner' && Auth::user()->services_screen_available == 1)
                    <li>
                        <a href="{{ route('dashboard.my_services.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.my_services.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.my_services.index' ? 'text-white' : '' }} fas fa-briefcase text-gray-500 pl-2"></i>
                            <span class="ml-3">خدماتي</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->role == 'admin')
                    <span
                        class="sidebar-item block text-sm text-black dark:text-white font-bold px-2 pt-4 border-t">الإدارة</span>
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="{{ Route::currentRouteName() == 'dashboard' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard' ? 'text-white' : '' }} fa fa-home text-gray-500 pl-2"></i>
                            <span class="ml-3">قُمرة القيادة</span>
                        </a>
                    </li>
                    <span
                        class="sidebar-item block text-sm text-black dark:text-white font-bold px-2 pt-4 border-t">الأنظمة
                        والخدمات
                    </span>
                    <li>
                        <a href="{{ route('dashboard.systems.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.systems.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.systems.index' ? 'text-white' : '' }} fab fa-chrome text-gray-500 pl-2"></i>
                            <span class="ml-3">الأنظمة</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.services.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.services.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.services.index' ? 'text-white' : '' }} fa fa-marker text-gray-500 pl-2"></i>
                            <span class="ml-3">نوع الخدمة</span>
                        </a>
                    </li>
                    <span class="sidebar-item block text-sm text-black dark:text-white font-bold px-2 pt-4 border-t">
                        إدارة الموظفين
                    </span>
                    <li>
                        <a href="{{ route('dashboard.salaries.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.salaries.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.salaries.index' ? 'text-white' : '' }} fab fa-chrome text-gray-500 pl-2"></i>
                            <span class="ml-3">الرواتب الشهرية</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.admin_remarks.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.admin_remarks.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.admin_remarks.index' ? 'text-white' : '' }} fa fa-marker text-gray-500 pl-2"></i>
                            <span class="ml-3">الملاحظات</span>
                        </a>
                    </li>
                    <a href="{{ route('dashboard.work-times.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.work-times.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.work-times.index' ? 'text-white' : '' }} fas fa-clock text-gray-500 pl-2"></i>
                        <span class="ml-3">الحضور والإنصراف</span>
                    </a>
                    <a href="{{ route('dashboard.adjustments.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.adjustments.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.adjustments.index' ? 'text-white' : '' }} fas fa-percent text-gray-500 pl-2"></i>
                        <span class="ml-3">الخصومات والمكافات</span>
                    </a>
                    <span
                        class="sidebar-item block text-sm text-black dark:text-white font-bold px-2 pt-5 border-t">المستخدمين</span>
                    <li>
                        <a href="{{ route('dashboard.partners.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.partners.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.partners.index' ? 'text-white' : '' }} fas fa-handshake text-gray-500 pl-2"></i>
                            <span class="ml-3">الشركاء</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.partner_systems.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.partner_systems.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.partner_systems.index' ? 'text-white' : '' }} fa fa-user-friends text-gray-500 pl-2"></i>
                            <span class="ml-3">خدمات الشركاء</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.clients.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.clients.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.clients.index' ? 'text-white' : '' }} fa fa-user-friends text-gray-500 pl-2"></i>
                            <span class="ml-3">العملاء</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'client')
                    <span class="sidebar-item block text-sm text-black dark:text-white font-bold px-2 pt-5
                        {{ Auth::user()->role == 'admin' ? 'border-t' : '' }}
                        ">الطلبات</span>
                    <li>
                        <a href="{{ route('dashboard.requests.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.requests.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.requests.index' ? 'text-white' : '' }} fa fa-shopping-cart text-gray-500 pl-2"></i>
                            <span class="ml-3">الطلبات</span>
                        </a>
                    </li>
                    @endif
                    {{-- @if (Auth::user()->role == 'admin')
                    <li>
                        <a href="{{ route('dashboard.special-request.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.special-request.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.special-request.index' ? 'text-white' : '' }} fa fa-shopping-cart text-gray-500 pl-2"></i>
                            <span class="ml-3">الطلبات الخاصة</span>
                        </a>
                    </li>
                    @endif --}}
                    {{-- @if (Auth::user()->role == 'client')
                    <li>
                        <a href="{{ route('special-request.show') }}"
                            class="{{ Route::currentRouteName() == 'special-request.show' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'special-request.show' ? 'text-white' : '' }} fa fa-shopping-cart text-gray-500 pl-2"></i>
                            <span class="ml-3">طلباتي الخاصة</span>
                        </a>
                    </li>
                    @endif --}}
                    {{-- القسم المالي --}}
                    @if (Auth::user()->role == 'admin')
                    <span class="sidebar-item block text-sm text-black dark:text-white font-bold px-2 pt-5 border-t">
                        القسم المالي
                    </span>
                    <li>
                        <a href="#"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i class="fa fa-chart-line text-gray-500 pl-2"></i>
                            <span class="ml-3">الأرباح</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i class="fa fa-coins text-gray-500 pl-2"></i>
                            <span class="ml-3">المصروفات</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i class="fa fa-dollar text-gray-500 pl-2"></i>
                            <span class="ml-3">طلبات التحويل</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'client')
                    <span class="sidebar-item block text-sm text-black dark:text-white font-bold px-2 pt-5 border-t">
                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'partner')
                        المهام والدعم
                        @else
                        المشاريع والدعم
                        @endif
                    </span>
                    @if(Auth::user()->role != 'client')
                    <li>
                        <a href="{{ route('dashboard.support.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.support.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.support.index' ? 'text-white' : '' }} fa fa-question-circle text-gray-500 pl-2"></i>
                            <span class="ml-3">
                                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'partner')
                                المهام
                                @else
                                المشاريع
                                @endif
                            </span>
                        </a>
                    </li>
                    @endif
                    @endif
                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'partner')
                    <li>
                        <a href="{{ route('dashboard.technical_support.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.technical_support.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.technical_support.index' ? 'text-white' : '' }} fa fa-question-circle text-gray-500 pl-2"></i>
                            <span class="ml-3">الدعم الفني</span>
                        </a>
                    </li>
                    @else
                    @if ($support)
                    <li>
                        <a href="{{ route('dashboard.technical_support.index') }}"
                            class="{{ Route::currentRouteName() == 'dashboard.technical_support.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i
                                class="{{ Route::currentRouteName() == 'dashboard.technical_support.index' ? 'text-white' : '' }} fa fa-question-circle text-gray-500 pl-2"></i>
                            <span class="ml-3">الدعم الفني</span>
                        </a>
                    </li>
                    @else
                    <li>
                        <button onclick="openSupport()"
                            class="block w-full text-right items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <i class="fa fa-question-circle text-gray-500 pl-2"></i>
                            <span class="ml-3">الدعم الفني</span>
                        </button>
                    </li>
                    @endif
                    @endif

                    {{-- admin screens --}}
                    @if(Auth::user()->role == 'admin')
                    <a href="{{ route('dashboard.available_services.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.available_services.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i class="{{ Route::currentRouteName() == 'dashboard.available_services.index' ? 'text-white' : '' }} 
                            fas fa-tools text-gray-500 pl-2"></i>
                        <span class="ml-3">ادخال الخدمات</span>
                    </a>
                    @endif

                    {{-- independent partner screens --}}
                    @if(Auth::user()->role == 'independent_partner')
                    <a href="{{ route('dashboard.my_service.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.my_service.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.my_service.index' ? 'text-white' : '' }} fas fa-briefcase text-gray-500 pl-2"></i>
                        <span class="ml-3">شاشة الأعمال</span>
                    </a>
                    @endif

                    {{-- independent partner screens --}}
                    @if(Auth::user()->role == 'partner' && Auth::user()->is_employee == 1 &&
                    Auth::user()->can_view_projects == 1)
                    <a href="{{ route('dashboard.new_project.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.new_project.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.new_project.index' ? 'text-white' : '' }} fas fa-project-diagram text-gray-500 pl-2"></i>
                        <span class="ml-3">المشاريع الجديدة</span>
                    </a>
                    @endif

                    @if(Auth::user()->role == 'independent_partner' || Auth::user()->role == 'partner' && Auth::user()->can_propose_quotes == 1)
                    <a href="{{ route('dashboard.new_project.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.new_project.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.new_project.index' ? 'text-white' : '' }} fas fa-project-diagram text-gray-500 pl-2"></i>
                        <span class="ml-3">المشاريع الجديدة</span>
                    </a>
                    @endif

                    @if(Auth::user()->role != 'client' && Auth::user()->role != 'admin')
                    <a href="{{ route('dashboard.kb.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.kb.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.kb.index' ? 'text-white' : '' }} fas fa-book-reader text-gray-500 pl-2"></i>
                        <span class="ml-3">بنك المعلومات</span>
                    </a>
                    @endif
                    @if(Auth::user()->role == 'admin')
                    <a href="{{ route('dashboard.kb_categories.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.kb_categories.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.kb_categories.index' ? 'text-white' : '' }} fas fa-tags text-gray-500 pl-2"></i>
                        <span class="ml-3">تصنيفات المعلومات</span>
                    </a>
                    <a href="{{ route('dashboard.kb.index') }}"
                        class="{{ Route::currentRouteName() == 'dashboard.kb.index' ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ Route::currentRouteName() == 'dashboard.kb.index' ? 'text-white' : '' }} fas fa-book-reader text-gray-500 pl-2"></i>
                        <span class="ml-3">بنك المعلومات</span>
                    </a>
                    @endif

                    @if(Auth::user()->role == 'partner' && Auth::user()->is_employee == 1 &&
                    Auth::user()->can_request_meetings == 1)
                    <a href="{{ route('dashboard.sessions.index') }}"
                        class="{{ str_contains(Route::currentRouteName(), 'dashboard.sessions') ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ str_contains(Route::currentRouteName(), 'dashboard.sessions') ? 'text-white' : '' }} fas fa-handshake text-gray-500 pl-2"></i>
                        <span class="ml-3">إدارة الاجتماعات</span>
                    </a>
                    @endif

                    @if(Auth::user()->role == 'admin')
                    <a href="{{ route('dashboard.logos.index') }}"
                        class="{{ str_contains(Route::currentRouteName(), 'dashboard.logos') ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ str_contains(Route::currentRouteName(), 'dashboard.logos') ? 'text-white' : '' }} fas fa-handshake text-gray-500 pl-2"></i>
                        <span class="ml-3">عملاء نفخر بخدمتهم</span>
                    </a>
                    <a href="{{ route('dashboard.sessions.index') }}"
                        class="{{ str_contains(Route::currentRouteName(), 'dashboard.sessions') ? 'text-white hover:bg-gray-800 bg-gray-700 dark:bg-gray-700' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <i
                            class="{{ str_contains(Route::currentRouteName(), 'dashboard.sessions') ? 'text-white' : '' }} fas fa-handshake text-gray-500 pl-2"></i>
                        <span class="ml-3">إدارة الاجتماعات</span>
                    </a>

                    @endif

                </ul>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="p-4 md:mr-60 h-auto pt-24">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Sidebar search functionality
        const searchInput = document.querySelector('#searchInput');
        const sidebarItems = document.querySelectorAll('.sidebar-item, li a');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.trim().toLowerCase();

                sidebarItems.forEach(item => {
                    const text = item.textContent.trim().toLowerCase();
                    const parentLi = item.closest('li');

                    if (text.includes(searchTerm)) {
                        if (parentLi) {
                            parentLi.style.display = '';
                        } else {
                            item.style.display = '';
                        }
                    } else {
                        if (parentLi) {
                            parentLi.style.display = 'none';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });

                if (searchTerm === '') {
                    sidebarItems.forEach(item => {
                        const parentLi = item.closest('li');
                        if (parentLi) {
                            parentLi.style.display = '';
                        } else {
                            item.style.display = '';
                        }
                    });
                }
            });
        }

        // Initialize intlTelInput ONLY if phone input exists
        const phoneInput = document.querySelector("#phone");
        if (phoneInput) {
            window.intlTelInput(phoneInput, {
                loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.11.2/build/js/utils.js"),
                initialCountry: "ae",
                separateDialCode: true
            });
        }
    });

    function openSupport() {
        Swal.fire({
            title: "إعتذار",
            text: "يجب الإشتراك فى اي نظام للحصول علي تذكرة الدعم",
            icon: "success"
        });
    }
    </script>
</body>

</html>