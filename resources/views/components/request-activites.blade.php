@props(['SpecialRequest'])

<div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
        <i class="fas fa-history text-emerald-500"></i> سجل نشاط المشروع
    </h2>

    <div class="relative mr-4 border-r-2 border-gray-100 dark:border-gray-700 pr-8 space-y-8">
        @forelse($SpecialRequest->activities as $activity)
        <div class="relative">
            {{-- نقطة التايم لاين --}}
            <span
                class="absolute -right-[41px] top-0 w-5 h-5 rounded-full border-4 border-white dark:border-gray-800 
                    {{ $activity->type == 'status' ? 'bg-emerald-500' : ($activity->type == 'invoice' ? 'bg-amber-500' : 'bg-blue-500') }}">
            </span>

            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ $activity->description }}
                    </span>
                    <span class="text-[10px] text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded-lg">
                        {{ $activity->created_at->diffForHumans() }}
                    </span>
                </div>

                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-user-circle text-[10px]"></i>
                    <span>بواسطة: {{ $activity->user->name }}</span>
                    <span class="mx-1">•</span>
                    <span>{{ $activity->created_at->format('Y/m/d - h:i A') }}</span>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 italic py-4">لا توجد أنشطة مسجلة بعد.</p>
        @endforelse
    </div>
</div>