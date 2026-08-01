@props([
    'brand' => 'academy',
    'size' => 'md', // sm | md | lg
])
@php
    $logoUrl = $brand === 'academy'
        ? asset('assets/images/academy_watermark.png')
        : asset('assets/images/logo.webp');
    $opacity = (float) config('watermark.opacity', 0.38);
    [$width, $top, $end] = match ($size) {
        'sm' => ['min(36%, 4.75rem)', '0.5rem', '0.5rem'],
        'lg' => ['min(16%, 9rem)', '1rem', '1rem'],
        default => ['min(28%, 6.5rem)', '0.65rem', '0.65rem'],
    };
@endphp
<img
    src="{{ $logoUrl }}"
    alt=""
    aria-hidden="true"
    {{ $attributes->class('ac-media-wm ac-media-wm--'.$size) }}
    style="position:absolute;top:{{ $top }};inset-inline-end:{{ $end }};inset-inline-start:auto;width:{{ $width }};height:auto;max-height:28%;opacity:{{ $opacity }};pointer-events:none;z-index:3;user-select:none;-webkit-user-drag:none;"
>
