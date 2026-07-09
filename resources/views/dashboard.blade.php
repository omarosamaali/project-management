@extends('layouts.app')
@php
    $isAr = app()->getLocale() === 'ar';
    $subscriptionData = \App\Support\ClinicSubscription::data($currentCompany ?? null);
    $subscriptionExpiresAt = $subscriptionData['expires_at'] ?? null;
@endphp
@section('title', $isAr ? 'لوحة التحكم' : 'Dashboard')

@section('content')
<section class="!pl-0 p-3 sm:p-5 space-y-5">

    @if($subscriptionExpiresAt)
    <div class="w-full rounded-2xl px-4 py-3 shadow-sm border border-green-200 bg-green-50 text-green-800 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <i class="fab fa-whatsapp text-xl"></i>
            <span class="text-sm font-bold">
                {{ $isAr ? 'تاريخ انتهاء الاشتراك:' : 'Subscription expiry date:' }}
            </span>
        </div>
        <span class="text-sm font-black">
            {{ $subscriptionExpiresAt->translatedFormat('d F Y') }}
        </span>
    </div>
    @endif

    {{-- Banner --}}
    @if(!empty($dashboardBanner['image_url']))
    <div class="w-full rounded-2xl overflow-hidden shadow-md">
        @if(!empty($dashboardBanner['link_url']))
            <a href="{{ $dashboardBanner['link_url'] }}" target="_blank" rel="noopener noreferrer" class="block">
                <img src="{{ $dashboardBanner['image_url'] }}" alt="{{ $dashboardBanner['title'] ?? 'Banner' }}"
                     style="display:block; width:100%; height:200px; object-fit:cover;">
            </a>
        @else
            <img src="{{ $dashboardBanner['image_url'] }}" alt="{{ $dashboardBanner['title'] ?? 'Banner' }}"
                 style="display:block; width:100%; height:200px; object-fit:cover;">
        @endif
    </div>
    @endif

    {{-- Welcome --}}
    <div class="rounded-2xl bg-gradient-to-l from-[#104776] to-[#336cfa] px-6 py-7 sm:py-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 shadow-lg">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white leading-snug">
                {{ $isAr ? 'أهلاً،' : 'Hello,' }} {{ isset($currentCompany) ? $currentCompany->display_name : auth()->user()->name }}
            </h1>
            <p class="text-blue-200 text-sm mt-1.5">
                {{ now()->translatedFormat('l، d F Y') }}
                @if(isset($currentCompany))
                    — {{ auth()->user()->name }}
                @else
                    — {{ $isAr ? 'لوحة تحكم العيادة' : 'Clinic Dashboard' }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white/10 rounded-xl px-5 py-3 text-center">
                <p class="text-blue-200 text-xs mb-1">{{ $isAr ? 'مواعيد اليوم' : "Today's Appointments" }}</p>
                <p class="text-white text-3xl font-black">{{ $stats['appointments_today'] }}</p>
            </div>
            <div class="bg-white/10 rounded-xl px-5 py-3 text-center">
                <p class="text-blue-200 text-xs mb-1">{{ $isAr ? 'بانتظار التأكيد' : 'Awaiting Confirmation' }}</p>
                <p class="text-white text-3xl font-black">{{ $stats['appointments_pending'] }}</p>
            </div>
        </div>
    </div>

    {{-- Clients & Pets --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <div class="w-1 h-5 bg-[#336cfa] rounded-full"></div>
            <h2 class="text-base font-bold text-gray-700">{{ $isAr ? 'العملاء والحيوانات' : 'Clients & Pets' }}</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
            $clientCards = [
                ['label'=> $isAr ? 'العملاء'    : 'Clients',     'value'=>$stats['clients'],    'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color'=>'blue',   'link'=>route('dashboard.clients.index')],
                ['label'=> $isAr ? 'الحيوانات'  : 'Pets',        'value'=>$stats['pets'],       'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color'=>'pink',   'link'=>route('dashboard.clients.index')],
                ['label'=> $isAr ? 'المستشارين' : 'Consultants', 'value'=>$stats['doctors'],    'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color'=>'teal',   'link'=>route('dashboard.doctors.index')],
                ['label'=> $isAr ? 'المساعدون'  : 'Assistants',  'value'=>$stats['assistants'], 'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color'=>'indigo', 'link'=>route('dashboard.assistants.index')],
            ];
            $colorMap = [
                'blue'   => ['bg'=>'bg-blue-500',   'light'=>'bg-blue-50',   'text'=>'text-blue-600'],
                'pink'   => ['bg'=>'bg-pink-500',   'light'=>'bg-pink-50',   'text'=>'text-pink-600'],
                'teal'   => ['bg'=>'bg-teal-500',   'light'=>'bg-teal-50',   'text'=>'text-teal-600'],
                'indigo' => ['bg'=>'bg-indigo-500', 'light'=>'bg-indigo-50', 'text'=>'text-indigo-600'],
            ];
            @endphp
            @foreach($clientCards as $card)
            @php $c = $colorMap[$card['color']]; @endphp
            <a href="{{ $card['link'] }}" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-3">
                <div class="{{ $c['light'] }} {{ $c['text'] }} p-3 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-800">{{ number_format($card['value']) }}</p>
                    <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Appointments & Requests --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <div class="w-1 h-5 bg-[#336cfa] rounded-full"></div>
            <h2 class="text-base font-bold text-gray-700">{{ $isAr ? 'المواعيد والطلبات' : 'Appointments & Requests' }}</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @php
            $apptCards = [
                ['label'=> $isAr ? 'إجمالي الطلبات' : 'Total Requests',      'value'=>$stats['appointments_total'],   'from'=>'from-gray-700',  'to'=>'to-gray-900',  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'link'=>route('dashboard.clinic_requests.index')],
                ['label'=> $isAr ? 'قيد الانتظار'   : 'Pending',             'value'=>$stats['appointments_pending'], 'from'=>'from-amber-500', 'to'=>'to-amber-700', 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',            'link'=>route('dashboard.clinic_requests.index').'?status=pending'],
                ['label'=> $isAr ? 'مواعيد اليوم'   : "Today's Appointments", 'value'=>$stats['appointments_today'],   'from'=>'from-[#336cfa]', 'to'=>'to-[#104776]', 'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'link'=>route('dashboard.clinic_requests.index')],
            ];
            @endphp
            @foreach($apptCards as $card)
            <a href="{{ $card['link'] }}" class="group relative rounded-2xl bg-gradient-to-br {{ $card['from'] }} {{ $card['to'] }} p-4 shadow hover:shadow-lg hover:-translate-y-0.5 transition-all overflow-hidden">
                <div class="absolute -left-3 -bottom-3 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="h-20 w-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
                <p class="text-3xl font-black text-white">{{ number_format($card['value']) }}</p>
                <p class="text-xs text-white/70 mt-1">{{ $card['label'] }}</p>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Inventory & Services --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <div class="w-1 h-5 bg-[#336cfa] rounded-full"></div>
            <h2 class="text-base font-bold text-gray-700">{{ $isAr ? 'المخزن والخدمات' : 'Inventory & Services' }}</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
            $storeCards = [
                ['label'=> $isAr ? 'الخدمات'             : 'Services',          'value'=>$stats['services'],  'color'=>'violet', 'icon'=>'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'link'=>route('dashboard.our_services.index')],
                ['label'=> $isAr ? 'المنتجات'            : 'Products',          'value'=>$stats['products'],  'color'=>'emerald','icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'link'=>coroute('dashboard.products.index')],
                ['label'=> $isAr ? 'الموردون'            : 'Suppliers',         'value'=>$stats['suppliers'], 'color'=>'cyan',   'icon'=>'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z', 'link'=>route('dashboard.suppliers.index')],
                ['label'=> $isAr ? 'المصروفات (الشهر)' : 'Expenses (Month)', 'value'=>number_format($stats['expenses_month'], 2), 'color'=>'rose', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 8v1m0-9a9 9 0 110 18A9 9 0 0112 3z', 'link'=>route('dashboard.admin_expenses.index')],
            ];
            $colorMap2 = [
                'violet'  => ['bg'=>'bg-violet-500',  'light'=>'bg-violet-50',  'text'=>'text-violet-600'],
                'emerald' => ['bg'=>'bg-emerald-500', 'light'=>'bg-emerald-50', 'text'=>'text-emerald-600'],
                'cyan'    => ['bg'=>'bg-cyan-500',    'light'=>'bg-cyan-50',    'text'=>'text-cyan-600'],
                'rose'    => ['bg'=>'bg-rose-500',    'light'=>'bg-rose-50',    'text'=>'text-rose-600'],
            ];
            @endphp
            @foreach($storeCards as $card)
            @php $c = $colorMap2[$card['color']]; @endphp
            <a href="{{ $card['link'] }}" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-3">
                <div class="{{ $c['light'] }} {{ $c['text'] }} p-3 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-800">{{ $card['value'] }}</p>
                    <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Recent Appointments + Recent Pets --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-700 text-sm">{{ $isAr ? 'آخر الطلبات' : 'Recent Requests' }}</h3>
                <a href="{{ route('dashboard.clinic_requests.index') }}" class="text-xs text-[#336cfa] hover:underline">{{ $isAr ? 'عرض الكل' : 'View All' }}</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recent_appointments as $appt)
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-8 h-8 rounded-full bg-[#336cfa]/10 text-[#336cfa] flex items-center justify-center text-xs font-bold flex-shrink-0">
                        {{ mb_substr($appt->pet?->name ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $appt->pet?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $appt->doctor?->name ?? ($isAr ? 'بدون مستشار' : 'No consultant') }} · {{ $appt->appointment_date?->format('d/m/Y') }}</p>
                    </div>
                    @php
                    $statusColor = match($appt->status) {
                        'pending'   => 'bg-yellow-100 text-yellow-700',
                        'approved'  => 'bg-green-100 text-green-700',
                        'rejected'  => 'bg-red-100 text-red-700',
                        'completed' => 'bg-teal-100 text-teal-700',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                    @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColor }} flex-shrink-0">{{ $appt->status_label }}</span>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-sm">{{ $isAr ? 'لا توجد طلبات بعد' : 'No requests yet' }}</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <h3 class="font-bold text-gray-700 text-sm">{{ $isAr ? 'آخر الحيوانات المضافة' : 'Recently Added Pets' }}</h3>
                <a href="{{ route('dashboard.clients.index') }}" class="text-xs text-[#336cfa] hover:underline">{{ $isAr ? 'عرض العملاء' : 'View Clients' }}</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recent_pets as $pet)
                <div class="flex items-center gap-3 px-4 py-3">
                    @if($pet->image)
                    <img src="{{ Storage::url($pet->image) }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
                    @else
                    <div class="w-8 h-8 rounded-full bg-pink-100 text-pink-500 flex items-center justify-center text-xs font-bold flex-shrink-0">
                        {{ mb_substr($pet->name, 0, 1) }}
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $pet->name }}</p>
                        <p class="text-xs text-gray-400">{{ $pet->user?->name ?? '—' }} · {{ $pet->animalType?->name ?? '' }}</p>
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $pet->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-sm">{{ $isAr ? 'لا توجد حيوانات بعد' : 'No pets yet' }}</div>
                @endforelse
            </div>
        </div>

    </div>

</section>
@endsection
