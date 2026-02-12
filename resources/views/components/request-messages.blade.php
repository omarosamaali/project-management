@props(['requestData', 'supports'])

<div class="flex flex-col h-[600px] bg-gray-50 dark:bg-gray-900 rounded-xl shadow-inner border dark:border-gray-700">

    <div class="p-4 border-b bg-white dark:bg-gray-800 rounded-t-xl flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 text-blue-600 rounded-full">
                <i class="fas fa-comments"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white">نقاشات المشروع</h3>
                <p class="text-xs text-gray-500">مشروع رقم: #{{ $requestData->id }}</p>
            </div>
        </div>
    </div>

    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 flex flex-col">
        @forelse($supports as $message)
        @php
        $isMine = $message->user_id === auth()->id();
        $senderRole = $message->user->role ?? 'user';
        @endphp

        <div class="flex {{ $isMine ? 'justify-start flex-row-reverse' : 'justify-start' }} items-end gap-2">
            <div
                class="w-8 h-8 rounded-full bg-gray-300 flex-shrink-0 flex items-center justify-center text-[10px] font-bold text-white shadow-sm overflow-hidden">
                @if($message->user->image)
                <img src="{{ asset('storage/' . $message->user->image) }}" alt="">
                @else
                {{ substr($message->user->name ?? 'U', 0, 1) }}
                @endif
            </div>

            <div class="max-w-[70%] space-y-1">
                <div class="flex items-center gap-2 {{ $isMine ? 'flex-row-reverse' : '' }}">
                    <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">
                        {{ $message->user->name ?? 'مستخدم محذوف' }}
                    </span>
                    <span class="text-[9px] text-gray-400">
                        {{ $message->created_at->diffForHumans() }}
                    </span>
                </div>

                <div class="p-3 rounded-2xl text-sm shadow-sm 
                        {{ $isMine 
                            ? 'bg-blue-600 text-white rounded-br-none' 
                            : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border dark:border-gray-700 rounded-bl-none' 
                        }}">
                    {{ $message->message }}
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-10">
            <i class="fas fa-comment-slash text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 text-sm">لا توجد نقاشات بعد.. ابدأ المحادثة الآن!</p>
        </div>
        @endforelse
    </div>

    <div class="p-4 bg-white dark:bg-gray-800 border-t dark:border-gray-700 rounded-b-xl">
        <form action="{{ route('dashboard.request-messages.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="hidden" name="request_id" value="{{ $requestData->id }}">
            <input type="text" name="message" required placeholder="اكتب رسالتك هنا..."
                class="flex-1 p-3 bg-gray-100 dark:bg-gray-700 border-none rounded-lg focus:ring-2 focus:ring-blue-500 text-sm dark:text-white">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition-all flex items-center justify-center">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>