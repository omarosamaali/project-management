@props(['mono' => false])
@php
    $textClass = $mono ? 'text-black' : 'text-gray-700';
    $borderClass = $mono ? 'border-black' : 'border-t';
@endphp
<div class="invoice-header grid grid-cols-1 sm:grid-cols-3 gap-4 items-center p-8 border-b {{ $borderClass }} text-white">
    <div class="invoice-header__ar text-right">
        <h1 class="text-xl {{ $textClass }} font-bold mb-1">ايفورك للتكنولوجيا</h1>
        <p class="text-sm opacity-90 {{ $textClass }}">الامارات العربية المتحدة, إمارة دبي</p>
        <p class="text-sm opacity-90 {{ $textClass }}">منطقة الورقاء2</p>
        <p class="text-sm opacity-90 {{ $textClass }}">شركة مرخصة من دائرة الاقتصاد والسياحة بدبي</p>
    </div>

    <div class="invoice-header__logo flex justify-center">
        <div class="bg-white p-2 rounded-lg w-fit">
            <img src="{{ asset('assets/images/logo.webp') }}" alt="Evorq Logo"
                class="h-24 w-24 object-contain {{ $mono ? 'grayscale' : '' }}">
        </div>
    </div>

    <div class="invoice-header__en text-left">
        <h1 class="text-xl {{ $textClass }} font-bold mb-1">EVORQ TECHNOLOGIES</h1>
        <p class="text-sm opacity-90 {{ $textClass }}">United Arab Emirates, Dubai</p>
        <p class="text-sm opacity-90 {{ $textClass }}">Al Warqa 2</p>
        <p class="text-sm opacity-90 {{ $textClass }}">A company licensed by the Dubai Department of Economy and Tourism</p>
    </div>
</div>
