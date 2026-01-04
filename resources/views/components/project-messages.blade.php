@props(['SpecialRequest', 'supports'])

<div class="mx-auto w-full">
    <a href="{{ route('dashboard.technical_support.create') }}"
        class="w-fit mx-5 flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
        <svg class="h-3.5 w-3.5 ml-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path clip-rule="evenodd" fill-rule="evenodd"
                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
        </svg>
        إضافة شكوي
    </a>
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            @if(session('success'))
            <div class="mx-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
            @endif
            <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">#</th>
                        <th scope="col" class="px-4 py-3">رقم المشروع</th>
                        <th scope="col" class="px-4 py-3">العميل</th>
                        <th scope="col" class="px-4 py-3">اسم النظام</th>
                        <th scope="col" class="px-4 py-3">الحالة</th>
                        <th scope="col" class="px-4 py-3">رسائل غير مقروءة</th>
                        <th scope="col" class="px-4 py-3">آخر رسالة</th>
                        <th scope="col" class="px-4 py-3">الإجراءات</th>
                    </tr>
                </thead>
<tbody>
    @forelse($supports as $support)
    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
            {{ $support->id }}
        </td>
        <td class="px-4 py-3">
            <div class="font-medium text-gray-900 dark:text-white">
                {{ $support->subject }}
            </div>
        </td>

        {{-- اسم العميل --}}
        <td class="px-4 py-3">
            @if(isset($support->user))
            {{ $support->user->name }}
            @else
            {{ \App\Models\User::find($support->client_id ?? $support->user_id)->name ?? 'N/A' }}
            @endif
        </td>

        {{-- اسم النظام --}}
        <td class="px-4 py-3">
            @if(isset($support->request) && isset($support->request->system))
            <a href="{{ route('dashboard.requests.show', $support->request_id) }}"
                class="text-blue-600 hover:underline font-bold">
                {{ $support->request->system->name_ar ?? $support->request->system->name }}
            </a>
            @else
            @php
            $systemId = $support->system_id ?? null;
            $systemName = $systemId ? \DB::table('systems')->where('id', $systemId)->value('name_ar') : 'نظام مخصص';
            @endphp
            <a href="{{ route('dashboard.requests.show', $support->request_id) }}"
                class="text-blue-600 hover:underline">
                {{ $systemName }}
            </a>
            @endif
        </td>

        {{-- الحالة --}}
        <td class="px-4 py-3 text-center">
            @php
            $status = $support->status;
            $color = match($status) {
            'open' => 'bg-green-100 text-green-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
            };
            @endphp
            <span class="px-3 py-1 rounded-full text-xs {{ $color }}">
                {{ $status }}
            </span>
        </td>

        {{-- رسائل غير مقروءة --}}
        <td class="px-4 py-3">
            @php
            if (isset($support->unreadMessages)) {
            $unread = $support->unreadMessages->count();
            } else {
            // ملاحظة: تأكد من اسم جدول الرسائل (هل هو support_messages أم messages؟)
            $unread = \DB::table('support_messages')->where('support_id', $support->id)->where('is_read', 0)->count();
            }
            @endphp
            @if($unread > 0)
            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">{{ $unread }} جديدة</span>
            @else
            <span class="text-gray-500 text-xs">لا يوجد</span>
            @endif
        </td>

        {{-- آخر تحديث --}}
        <td class="px-4 py-3 text-xs text-gray-500">
            {{ \Carbon\Carbon::parse($support->updated_at ?? $support->last_message_at)->diffForHumans() }}
        </td>

        {{-- زر فتح المحادثة (حل مشكلة الـ 404) --}}
        <td class="px-4 py-3">
            @php
            // إذا كان السجل من technical_support، وجهه للرابط الخاص به
            // تأكد من وجود اسم الـ route الصحيح لجدول الـ technical
            $route = $support->is_technical
            ? route('dashboard.technical_support.show', $support->id)
            : route('dashboard.support.show', $support->id);
            @endphp
            <a href="{{ $route }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs transition-colors">
                فتح المحادثة
            </a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center py-8 text-gray-500">لا توجد تذاكر دعم</td>
    </tr>
    @endforelse
</tbody>
                <div class="p-4">
                    {{-- {{ $support->links() }} --}}
                </div>
        </div>

    </div>
</div>