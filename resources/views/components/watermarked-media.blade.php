@php
    $brand = $brand ?? 'app';
    $logoUrl = $brand === 'academy'
        ? asset('assets/images/academy_watermark.png')
        : asset('assets/images/logo.webp');
    $opacity = $opacity ?? config('watermark.opacity', 0.38);
@endphp
<div class="wm-media-wrap ac-media-wm-host {{ $class ?? '' }}" style="position:relative;display:block;overflow:hidden;">
    {{ $slot }}
    <img src="{{ $logoUrl }}" alt="" aria-hidden="true"
        class="wm-media-logo ac-media-wm"
        style="position:absolute;top:12px;inset-inline-end:12px;inset-inline-start:auto;width:min(18%,140px);height:auto;opacity:{{ $opacity }};pointer-events:none;z-index:3;user-select:none;">
</div>
