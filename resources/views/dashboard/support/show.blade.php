@extends('layouts.app')

@section('title', 'عرض تفاصيل الشريك')

@section('content')
<section class="p-3 sm:p-5">
    @if(Auth::user()->role == 'admin')
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.support.index') }}" second="الدعم" third="عرض المحادثة" />
    @else
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="الدعم" />
    @endif

    <div class="p-4 max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $support->subject }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    العميل: <span class="font-medium">{{ $support->user->name }}</span> |
                    رقم الطلب: <a href="{{ route('dashboard.requests.show', $support->request_id) }}"
                        class="text-blue-600 hover:underline">{{ $support->request->order_number }}</a>
                </p>
            </div>
            <div class="flex gap-2">
                {{-- <form action="{{ route('dashboard.support.status', $support->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500">
                        <option value="open" {{ $support->status == 'open' ? 'selected' : '' }}>مفتوحة</option>
                        <option value="in_progress" {{ $support->status == 'in_progress' ? 'selected' : '' }}>قيد
                            المعالجة
                        </option>
                        <option value="closed" {{ $support->status == 'closed' ? 'selected' : '' }}>ملغية</option>
                    </select>
                </form> --}}
                @if (Auth::user()->role == 'admin')
                    
                <a href="{{ route('dashboard.support.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                رجوع
            </a>
            @endif
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Chat Messages -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">المحادثة</h2>
            </div>

            <div class="p-4 h-96 overflow-y-auto" id="chatMessages">
                @forelse($support->messages as $message)
                <div class="mb-4 {{ $message->user_id == auth()->id() ? 'text-left' : 'text-right' }}">
                    <div class="inline-block max-w-xl">
                        <div class="mb-1">
                            <span class="text-xs text-gray-500 font-medium">
                                {{ $message->user->name }}
                            </span>
                            <span class="text-xs text-gray-400 mr-2">
                                {{ $message->created_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                        <div
                            class="max-w-[400px] break-words !w-fit p-3 rounded-lg {{ $message->user_id == auth()->id() ? 'bg-blue-600 text-white mr-auto' : 'bg-gray-100 text-gray-900' }}">
                            {{ $message->message }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-comments text-4xl mb-2"></i>
                    <p>لا توجد رسائل بعد، ابدأ المحادثة!</p>
                </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t">
                <form action="{{ route('dashboard.support.message', $support->id) }}" method="POST" class="flex gap-2">
                    @csrf
                    <textarea name="message" rows="2" required
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="اكتب رسالتك هنا..."></textarea>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 h-fit">
                        <i class="fas fa-paper-plane"></i> إرسال
                    </button>
                </form>
                @error('message')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <script>
        // Scroll to bottom on page load
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    });
    </script>
</section>
@endsection