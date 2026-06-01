@extends('layouts.app')

@section('title', 'قُمرة القيادة')

@section('content')

<section class="!pl-0 p-3 sm:p-5 space-y-6">

    {{-- ====== كروت إحصائيات المشاريع ====== --}}
    <div>
        <h2 class="text-lg font-bold text-gray-700 dark:text-white mb-3 flex items-center gap-2">
            <i class="fas fa-project-diagram text-blue-600"></i> إحصائيات المشاريع
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4">
            <a href="{{ route('dashboard.requests.index') }}" class="flex bg-black justify-between rounded-lg">
                <div class="p-4 pr-6 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white whitespace-nowrap">جميع المشاريع</h1>
                    <p class="text-2xl flex items-center text-white">{{ $allRequestsCount }}</p>
                </div>
                <div class="p-5 bg-[#181818] rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
            <a href="{{ route('dashboard.requests.index') }}?status=جديد" class="flex bg-[#333333] justify-between rounded-lg">
                <div class="p-4 pr-4 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white whitespace-nowrap">طلبات جديدة</h1>
                    <p class="text-xl text-white">{{ $newRequestsCount }}</p>
                </div>
                <div class="p-5 bg-[#202020] rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
            <a href="{{ route('dashboard.requests.index') }}?status=تحت الاجراء" class="flex bg-[#595959] justify-between rounded-lg">
                <div class="p-4 pr-4 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white">تحت الإجراء</h1>
                    <p class="text-xl text-white">{{ $underProcessRequestsCount }}</p>
                </div>
                <div class="p-5 bg-[#4b4b4b] rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
            <a href="{{ route('dashboard.requests.index') }}?status=معلقة" class="flex bg-[#808080] justify-between rounded-lg">
                <div class="p-4 pr-4 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white">طلبات معلقة</h1>
                    <p class="text-xl text-white">{{ $pendingRequestsCount }}</p>
                </div>
                <div class="p-5 bg-[#6b6b6b] rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
            <a href="{{ route('dashboard.requests.index') }}?status=منتهية" class="flex bg-[#999999] justify-between rounded-lg">
                <div class="p-4 pr-4 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white">طلبات منتهية</h1>
                    <p class="text-xl text-white">{{ $closedRequestsCount }}</p>
                </div>
                <div class="p-5 bg-[#858585] rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
        </div>
    </div>

    {{-- ====== كروت إحصائيات الدورات ====== --}}
    <div>
        <h2 class="text-lg font-bold text-gray-700 dark:text-white mb-3 flex items-center gap-2">
            <i class="fas fa-graduation-cap text-green-600"></i> إحصائيات الدورات
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('dashboard.my_courses.index') }}" class="flex bg-black justify-between rounded-lg hover:-translate-y-1 transition-transform">
                <div class="p-4 pr-6 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white whitespace-nowrap">إجمالي الدورات</h1>
                    <p class="text-2xl flex items-center text-white">{{ $allCoursesCount }}</p>
                </div>
                <div class="p-5 bg-[#181818] rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
            <a href="{{ route('dashboard.my_courses.index') }}?filter=active" class="flex bg-green-700 justify-between rounded-lg hover:-translate-y-1 transition-transform">
                <div class="p-4 pr-6 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white whitespace-nowrap">دورات نشطة</h1>
                    <p class="text-2xl text-white">{{ $activeCoursesCount }}</p>
                </div>
                <div class="p-5 bg-green-800 rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
            <a href="{{ route('dashboard.my_courses.index') }}?filter=upcoming" class="flex bg-blue-600 justify-between rounded-lg hover:-translate-y-1 transition-transform">
                <div class="p-4 pr-6 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white whitespace-nowrap">دورات قادمة</h1>
                    <p class="text-2xl text-white">{{ $upcomingCoursesCount }}</p>
                </div>
                <div class="p-5 bg-blue-700 rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
            <a href="{{ route('dashboard.my_courses.index') }}?filter=ended" class="flex bg-[#808080] justify-between rounded-lg hover:-translate-y-1 transition-transform">
                <div class="p-4 pr-6 flex flex-col justify-between">
                    <h1 class="text-md font-bold text-white whitespace-nowrap">دورات منتهية</h1>
                    <p class="text-2xl text-white">{{ $endedCoursesCount }}</p>
                </div>
                <div class="p-5 bg-[#6b6b6b] rounded-lg">
                    <img src="{{ asset('assets/images/white-logo.png') }}" class="w-20 h-20 opacity-50" alt="">
                </div>
            </a>
        </div>
    </div>

    {{-- ====== قسم الإشعارات ====== --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-700 dark:text-white flex items-center gap-2">
                <i class="fas fa-bell text-yellow-500"></i> الإشعارات
                @if($notifications->count() > 0)
                <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $notifications->count() }}</span>
                @endif
            </h2>
            @if($notifications->count() > 0)
            <button onclick="markAllRead()"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                <i class="fas fa-check-double"></i> تعليم الكل مقروءة
            </button>
            @endif
        </div>

        @if($notifications->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
            <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-400 dark:text-gray-500">لا توجد إشعارات جديدة</p>
        </div>
        @else
        <div class="space-y-2" id="notifications-container">
            @foreach($notifications as $notification)
            <div id="notif-{{ $notification->id }}"
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-start justify-between hover:shadow-md transition-shadow cursor-pointer group"
                onclick="handleNotifClick({{ $notification->id }}, '{{ $notification->url ?? '' }}')">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        {{ $notification->type === 'success' ? 'bg-green-100' : ($notification->type === 'warning' ? 'bg-yellow-100' : 'bg-blue-100') }}">
                        <i class="fas {{ $notification->icon }}
                            {{ $notification->type === 'success' ? 'text-green-600' : ($notification->type === 'warning' ? 'text-yellow-600' : 'text-blue-600') }}"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 dark:text-white text-sm">{{ $notification->title }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">{{ $notification->message }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <button onclick="event.stopPropagation(); markRead({{ $notification->id }})"
                    class="text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity text-xs px-2 py-1 rounded hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</section>

<script>
    function markRead(id) {
        fetch(`/dashboard/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        }).then(() => {
            const el = document.getElementById(`notif-${id}`);
            if (el) {
                el.style.transition = 'opacity 0.3s ease, max-height 0.3s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            }
        }).catch(console.error);
    }

    function handleNotifClick(id, url) {
        markRead(id);
        if (url && url.trim() !== '') {
            setTimeout(() => { window.location.href = url; }, 200);
        }
    }

    function markAllRead() {
        fetch('/dashboard/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        }).then(() => {
            const container = document.getElementById('notifications-container');
            if (container) {
                container.innerHTML = '<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center"><i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i><p class="text-gray-400">لا توجد إشعارات جديدة</p></div>';
            }
        }).catch(console.error);
    }
</script>

@endsection
