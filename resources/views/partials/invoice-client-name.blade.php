@props([
    'user' => null,
    'fallback' => 'عميل',
    'nameClass' => 'font-bold text-gray-900',
    'companyClass' => 'font-bold text-gray-900 text-lg',
])

@php
    $user = $user ?? auth()->user();
    $companyName = \App\Support\ClientCompanyFields::invoiceCompanyName($user);
@endphp

@if($companyName)
    <p class="{{ $companyClass }}">{{ $companyName }}</p>
@endif
<p class="{{ $nameClass }}">{{ $user?->name ?? $fallback }}</p>
