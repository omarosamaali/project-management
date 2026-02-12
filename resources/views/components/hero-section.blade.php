<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-gray-900 via-black to-gray-900 text-white overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0"
            style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        </div>
    </div>

    <!-- Animated Shapes -->
    <div
        class="absolute top-20 left-10 w-72 h-72 bg-black rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob">
    </div>
    <div
        class="absolute top-40 right-10 w-72 h-72 bg-gray-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000">
    </div>
    <div
        class="absolute -bottom-8 left-40 w-72 h-72 bg-black rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
    </div>

    <div class="container mx-auto px-4 py-20 md:py-32 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">

            <!-- Text Content -->
            <div class="space-y-8">
                <!-- Badge -->
                <div
                    class="inline-flex items-center gap-2 bg-black/20 border border-gray-500/30 rounded-full px-4 py-2 backdrop-blur-sm">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-500"></span>
                    </span>
                    <span class="text-sm font-medium">{{ __('messages.trusted_by_100') }}</span>
                </div>

                <!-- Main Heading -->
                <h1 style="line-height: 1.45 !important;" class="text-4xl md:text-6xl lg:text-7xl font-bold leading-tight">
                    {{ __('messages.hero_title') }} {{ __('messages.hero_highlight') }} {{ __('messages.with_evorq') }}
                </h1>

                <!-- Description -->
                <p class="text-lg md:text-xl text-gray-300 leading-relaxed">
                    {{ __('messages.hero_description') }}
                </p>

                <!-- Features List -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-black/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-gray-200">{{ __('messages.feature_1') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-black/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-gray-200">{{ __('messages.feature_2') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-black/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-gray-200">{{ __('messages.feature_3') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-black/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-gray-200">{{ __('messages.feature_4') }}</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- <a href="#systems"
                        class="group relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-red-700 text-white px-8 py-4 rounded-xl font-bold text-lg overflow-hidden transition-all hover:shadow-2xl hover:shadow-red-600/50 hover:scale-105">
                        <span class="relative z-10">{{ __('messages.explore_systems') }}</span>
                        <svg class="w-5 h-5 relative z-10 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-red-700 to-red-800 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                        </div>
                    </a> --}}

<a href="https://wa.me/971501774477" target="_blank"
    class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border-2 border-white/20 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/20 transition-all hover:scale-105">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
        </path>
    </svg>
    {{ __('messages.talk_to_expert') }}
</a>
                </div>

                <!-- Trust Indicators -->
                <div class="flex items-center gap-6 pt-4">
                    <div class="flex -space-x-2">
                        <img class="w-10 h-10 rounded-full border-2 border-gray-900"
                            src="https://i.pravatar.cc/100?img=1" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-gray-900"
                            src="https://i.pravatar.cc/100?img=2" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-gray-900"
                            src="https://i.pravatar.cc/100?img=3" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-gray-900"
                            src="https://i.pravatar.cc/100?img=4" alt="">
                        <div
                            class="w-10 h-10 rounded-full border-2 border-gray-900 bg-black flex items-center justify-center text-xs font-bold">
                            +50
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                            </path>
                        </svg>
                        <span class="text-sm text-gray-300"><span class="font-bold">4.9</span>/5 ({{
                            __('messages.reviews_count') }})</span>
                    </div>
                </div>
            </div>

            <!-- Image/Visual Side -->
            <div class="relative">
                <!-- Main Image Container -->
                <div class="relative z-10">
                    <div
                        class="bg-gradient-to-br from-gray-600/10 to-transparent backdrop-blur-xs border border-black rounded-2xl p-8 shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80"
                            alt="Dashboard Preview"
                            class="w-full rounded-lg shadow-2xl transform hover:scale-105 transition-transform duration-500">
                    </div>
                </div>

                <!-- Floating Cards -->
                <div class="absolute top-10 -right-10 bg-white rounded-xl shadow-2xl p-4 animate-float">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ __('messages.success_rate') }}</p>
                            <p class="text-2xl font-bold text-green-600">99.9%</p>
                        </div>
                    </div>
                </div>

                <div
                    class="absolute -bottom-5 -left-10 bg-white rounded-xl shadow-2xl p-4 animate-float animation-delay-2000">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ __('messages.avg_delivery') }}</p>
                            <p class="text-2xl font-bold text-blue-600">7 {{ __('messages.days') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                fill="white" />
        </svg>
    </div>
</section>

<!-- Custom Animations -->
<style>
    @keyframes blob {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        25% {
            transform: translate(20px, -50px) scale(1.1);
        }

        50% {
            transform: translate(-20px, 20px) scale(0.9);
        }

        75% {
            transform: translate(50px, 50px) scale(1.05);
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    .animate-blob {
        animation: blob 7s infinite;
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>