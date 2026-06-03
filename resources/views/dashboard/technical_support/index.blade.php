@extends('layouts.app')

@section('title', 'تذاكر الدعم الفني')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="تذاكر الدعم الفني" />

    <div class="mx-auto w-full space-y-5">

        {{-- ════════════════════════════════════════
        بانر الدعم الفني النشط
        ════════════════════════════════════════ --}}
        @if(isset($activeRequests) && $activeRequests->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i class="fas fa-shield-alt text-blue-500"></i>
                مدة الدعم الفني المتبقية لمشاريعك
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($activeRequests as $req)
                @php
                $isSpecial = $req instanceof \App\Models\SpecialRequest;
                $remaining = $req->support_remaining_days;
                $total = $isSpecial ? $req->support_total_days : ($req->support_total_days ?: ($req->system->support_days ?? 1));
                $percent = $req->support_percentage;
                $color = $req->support_color;
                $name = $req->project_display_name;
                $colorMap = [
                'green' => ['border'=>'border-green-200 dark:border-green-700','num'=>'text-green-600
                dark:text-green-400','bar'=>'bg-green-500'],
                'yellow' => ['border'=>'border-yellow-200 dark:border-yellow-700','num'=>'text-yellow-500
                dark:text-yellow-400','bar'=>'bg-yellow-400'],
                'red' => ['border'=>'border-red-200 dark:border-red-700','num'=>'text-red-500
                dark:text-red-400','bar'=>'bg-red-500'],
                'gray' => ['border'=>'border-gray-200
                dark:border-gray-600','num'=>'text-gray-400','bar'=>'bg-gray-400'],
                ];
                $c = $colorMap[$color] ?? $colorMap['gray'];
                @endphp
                <div class="border {{ $c['border'] }} rounded-xl p-3 flex flex-col gap-2 bg-gray-50 dark:bg-gray-750">
                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">
                        {{ $name }}
                    </div>
                    <div class="flex items-end gap-1.5">
                        <span class="text-3xl font-black tabular-nums leading-none {{ $c['num'] }}">
                            {{ max(0, $remaining) }}
                        </span>
                        <span class="text-xs text-gray-400 pb-0.5">يوم / {{ $total }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1 overflow-hidden">
                        <div class="{{ $c['bar'] }} h-1 rounded-full" style="width:{{ $percent }}%"></div>
                    </div>
                    @if($req->support_end_date)
                    <div class="text-xs text-gray-400">
                        ينتهي: {{ $req->support_end_date->format('Y/m/d') }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════════════
        جدول التذاكر
        ════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

            {{-- شريط الأدوات --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 p-4">
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.technical_support.index') }}" method="GET">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request()->search }}"
                                placeholder="ابحث بالموضوع أو اسم العميل..."
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </form>
                </div>
                <a href="{{ route('dashboard.technical_support.create') }}"
                    class="flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 transition">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                    </svg>
                    إضافة تذكرة
                </a>
            </div>

            {{-- Alerts --}}
            <div class="px-4">
                @if(session('success'))
                <div
                    class="mb-4 p-3 text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:text-green-300 dark:border-green-700 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div
                    class="mb-4 p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/20 dark:text-red-300 dark:border-red-700 flex items-center gap-2">
                    <i class="fas fa-times-circle"></i> {{ session('error') }}
                </div>
                @endif
            </div>

            {{-- الجدول --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            @if(Auth::user()->role === 'admin')
                            <th class="px-4 py-3">العميل</th>
                            @endif
                            <th class="px-4 py-3">المشروع</th>
                            <th class="px-4 py-3">الموضوع</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">تاريخ الفتح</th>
                            <th class="px-4 py-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">

                            {{-- الرقم --}}
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                                #{{ $ticket->id }}
                            </td>

                            {{-- العميل (للأدمن فقط) --}}
                            @if(Auth::user()->role === 'admin')
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-300">
                                        {{ mb_substr($ticket->client->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white text-xs">
                                        {{ $ticket->client->name ?? 'غير معروف' }}
                                    </span>
                                </div>
                            </td>
                            @endif

                            {{-- المشروع --}}
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                    {{ $ticket->project_name }}
                                </span>
                            </td>

                            {{-- الموضوع --}}
                            <td class="px-4 py-3">
                                <div class="max-w-xs">
                                    <p class="font-medium text-gray-900 dark:text-white text-xs truncate">
                                        {{ $ticket->subject }}
                                    </p>
                                    <p class="text-gray-400 text-xs truncate mt-0.5">
                                        {{ Str::limit($ticket->description, 50) }}
                                    </p>
                                </div>
                            </td>

                            {{-- الحالة --}}
                            <td class="px-4 py-3">
                                @php
                                $statusConfig = [
                                'open' => ['class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                'icon' => '🔴', 'label' => 'مفتوحة'],
                                'in_review' => ['class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30
                                dark:text-yellow-300', 'icon' => '🟡', 'label' => 'قيد المراجعة'],
                                'resolved' => ['class' => 'bg-green-100 text-green-700 dark:bg-green-900/30
                                dark:text-green-300', 'icon' => '🟢', 'label' => 'محلولة'],
                                'closed' => ['class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                'icon' => '⚫', 'label' => 'منتهية'],
                                ];
                                $sc = $statusConfig[$ticket->status] ?? ['class'=>'bg-gray-100
                                text-gray-600','icon'=>'⚪','label'=>$ticket->status];
                                @endphp
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc['class'] }}">
                                    {{ $sc['icon'] }} {{ $sc['label'] }}
                                </span>
                            </td>

                            {{-- التاريخ --}}
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                <div>{{ $ticket->created_at->format('Y/m/d') }}</div>
                                <div class="text-gray-400">{{ $ticket->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- الإجراءات --}}
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard.technical_support.show', $ticket->id) }}"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium transition">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'admin' ? 7 : 6 }}"
                                class="text-center px-4 py-12 text-gray-400">
                                <i class="fas fa-ticket-alt text-4xl mb-3 block opacity-30"></i>
                                <p class="font-medium">لا توجد تذاكر دعم فني حتى الآن</p>
                                <a href="{{ route('dashboard.technical_support.create') }}"
                                    class="mt-3 inline-block text-blue-600 hover:underline text-sm">
                                    + افتح تذكرتك الأولى
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($tickets->hasPages())
            <div class="p-4 border-t dark:border-gray-700">
                {{ $tickets->links() }}
            </div>
            @endif
        </div>
    </div>
</section>

@endsection