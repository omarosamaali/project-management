@extends('layouts.app')
@section('content')
<div class="p-5 pb-0">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.educational_resources.index') }}"
        second="المصادر التعليمية" />
</div>

<section class="p-5">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6" dir="rtl">
        <div>
            <h2 class="text-2xl font-bold dark:text-white">المصادر التعليمية</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">إدارة مكتبة الفيديوهات التعليمية</p>
        </div>
        @if(Auth::user()->role == 'admin')
        <a href="{{ route('dashboard.educational_resources.create') }}"
            class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>إضافة فيديو جديد</span>
        </a>
        @endif
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border-r-4 border-green-500 text-green-800 dark:text-green-200 px-6 py-4 rounded-lg mb-6 flex items-center gap-3"
        dir="rtl">
        <i class="fas fa-check-circle text-xl"></i>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Resources Grid --}}
    @if($resources->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($resources as $resource)
        @php
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
        $resource->youtube_url, $matches);
        $videoId = $matches[1] ?? 'default';
        @endphp

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 group">
            {{-- Video Thumbnail with Overlay --}}
            <div class="relative h-48 overflow-hidden">
                <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    alt="{{ $resource->title }}" onerror="this.src='https://img.youtube.com/vi/{{ $videoId }}/0.jpg'">

                {{-- Dark Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                {{-- Play Button --}}
                <button onclick="openVideoModal('{{ $videoId }}', '{{ addslashes($resource->title) }}')"
                    class="absolute inset-0 flex items-center justify-center group/play cursor-pointer">
                    <div
                        class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-2xl group-hover/play:scale-110 transition-transform">
                        <i class="fas fa-play text-white text-xl ml-1"></i>
                    </div>
                </button>

                {{-- Language Badge --}}
                <span
                    class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-full">
                    {{ $resource->language == 'ar' ? 'عربي' : 'English' }}
                </span>

                {{-- Status Badge --}}
                @if(Auth::user()->role == 'admin')
                <span
                    class="absolute top-3 left-3 {{ $resource->status ? 'bg-green-500' : 'bg-gray-500' }} text-white text-xs font-semibold px-3 py-1.5 rounded-full">
                    {{ $resource->status ? '● نشط' : '● غير نشط' }}
                </span>
                @endif
            </div>

            {{-- Content --}}
            <div class="p-5" dir="rtl">
                {{-- Title --}}
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-3 line-clamp-2 min-h-[3.5rem]">
                    {{ $resource->title }}
                </h3>

                {{-- Users Tags --}}
                @if(Auth::user()->role == 'admin')
                <div class="flex flex-wrap gap-2 mb-4">
                    @if(is_array($resource->users))
                    @foreach($resource->users as $user)
                    <span
                        class="inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-semibold px-3 py-1.5 rounded-lg border border-blue-200 dark:border-blue-700">
                        @if($user == 'partner')
                        <i class="fas fa-user-tie"></i> الشركاء الموظفين
                        @elseif($user == 'independent_partner')
                        <i class="fas fa-user-check"></i> الشركاء المستقلين
                        @elseif($user == 'client')
                        <i class="fas fa-users"></i> العملاء
                        @endif
                    </span>
                    @endforeach
                    @else
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $resource->users }}</span>
                    @endif
                </div>
                @endif
                
                {{-- Actions --}}
                <div class="flex gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button onclick="openVideoModal('{{ $videoId }}', '{{ addslashes($resource->title) }}')"
                        class="text-[13px] px-1 flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-center py-2.5 rounded-lg font-semibold
                        hover:bg-red-100 dark:hover:bg-red-900/50 transition-all">
                        <i class="fab fa-youtube ml-1"></i>
                        مشاهدة
                    </button>
                    @if(Auth::user()->role == 'admin')
                    <a href="{{ route('dashboard.educational_resources.edit', $resource) }}"
                        class="text-[13px] flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-center py-2.5 rounded-lg font-semibold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-all">
                        <i class="fas fa-edit ml-1"></i>
                        تعديل
                    </a>

                    <form action="{{ route('dashboard.educational_resources.destroy', $resource) }}" method="POST"
                        onsubmit="return confirm('⚠️ هل أنت متأكد من حذف هذا الفيديو؟')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-[13px] w-full bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 py-2.5 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-600 transition-all">
                            <i class="fas fa-trash-alt ml-1"></i>
                            حذف
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- Empty State --}}
    @if(Auth::user()->role == 'admin')
    <div class="flex flex-col items-center justify-center py-20">
        <div class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-video text-6xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">لا توجد مصادر تعليمية</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">ابدأ بإضافة أول فيديو تعليمي الآن</p>
        <a href="{{ route('dashboard.educational_resources.create') }}"
            class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>إضافة فيديو جديد</span>
        </a>
    </div>
    @endif
    @endif
</section>

{{-- Video Modal --}}
<div id="videoModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 hidden items-center justify-center p-4"
    onclick="closeVideoModal(event)">
    <div class="relative w-full max-w-6xl" onclick="event.stopPropagation()">
        {{-- Close Button --}}
        <button onclick="closeVideoModal()"
            class="absolute -top-12 right-0 text-white hover:text-red-500 transition-colors">
            <i class="fas fa-times text-3xl"></i>
        </button>

        {{-- Video Title --}}
        <h3 id="videoTitle" class="text-white text-xl font-bold mb-4 text-right" dir="rtl"></h3>

        {{-- Video Container --}}
        <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl" style="padding-bottom: 56.25%;">
            <iframe id="videoFrame" class="absolute top-0 left-0 w-full h-full" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>

        {{-- External Link --}}
        <div class="mt-4 text-center">
            <a id="externalLink" target="_blank"
                class="inline-flex items-center gap-2 text-white hover:text-blue-400 transition-colors">
                <i class="fab fa-youtube"></i>
                <span>فتح في يوتيوب</span>
                <i class="fas fa-external-link-alt text-sm"></i>
            </a>
        </div>
    </div>
</div>

{{-- @push('scripts') --}}
<script>
    function openVideoModal(videoId, title) {
        const modal = document.getElementById('videoModal');
        const videoFrame = document.getElementById('videoFrame');
        const videoTitle = document.getElementById('videoTitle');
        const externalLink = document.getElementById('externalLink');
        
        // Set video source with autoplay
        videoFrame.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
        
        // Set title
        videoTitle.textContent = title;
        
        // Set external link
        externalLink.href = `https://www.youtube.com/watch?v=${videoId}`;
        
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }
    
    function closeVideoModal(event) {
        // Close only if clicking on backdrop or close button
        if (!event || event.target.id === 'videoModal' || event.target.closest('button')) {
            const modal = document.getElementById('videoModal');
            const videoFrame = document.getElementById('videoFrame');
            
            // Stop video
            videoFrame.src = '';
            
            // Hide modal
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            // Restore body scroll
            document.body.style.overflow = 'auto';
        }
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeVideoModal();
        }
    });
</script>
{{-- @endpush --}}
@endsection