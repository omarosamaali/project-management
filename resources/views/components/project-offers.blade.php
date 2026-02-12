@props(['SpecialRequest', 'partners', 'managers'])

@php
    $user = auth()->user();
    if($user->role === 'partner' || $user->role === 'independent_partner') {
        $proposals = $SpecialRequest->proposals
            ->where('user_id', $user->id)
            ->where('status', 'accepted');
    } else {
        $proposals = $SpecialRequest->proposals;
    }
@endphp

<div class="space-y-6 px-4">
    <div class="mt-4 flex items-center justify-between border-b pb-4 border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-bold text-black dark:text-white flex items-center gap-2">
            <i class="fas fa-file-invoice-dollar"></i>
            عروض الأسعار المقدمة
            <span class="bg-black dark:bg-white text-white dark:text-black text-[10px] px-2 py-0.5 rounded">
                {{ $proposals->count() }}
            </span>
        </h3>
    </div>

    <div class="grid gap-4">
        @forelse($proposals as $proposal)
            @php
                $isAccepted = $proposal->status === 'accepted';
            @endphp

            <div class="transition-all duration-300 relative border rounded-lg p-5 
                {{ $isAccepted ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700' }}">

                {{-- علامة القبول --}}
                @if($isAccepted)
                <div class="absolute -top-3 -right-3 w-8 h-8 bg-black dark:bg-white text-white dark:text-black rounded-full flex items-center justify-center border-4 border-white dark:border-gray-900 shadow-sm">
                    <i class="fas fa-check text-xs"></i>
                </div>
                @endif

                {{-- معلومات العرض --}}
                <div class="flex flex-col md:flex-row items-start justify-between gap-6">
                    <div class="flex-1">
                        <h4 class="font-bold">{{ $proposal->user->name }}</h4>
                        <p class="text-sm italic">{!! nl2br(e(strip_tags($proposal->proposal_details))) !!}</p>
                        <div class="flex gap-4 mt-2">
                            <span>قيمة العرض: {{ number_format($proposal->budget_to, 2) }}</span>
                            <span>مدة التنفيذ: {{ $proposal->execution_time }} يوم</span>
                            <span>الحالة: {{ $isAccepted ? 'تم القبول' : 'جديد' }}</span>
                        </div>
                    </div>
                    @if($proposal->status != 'accepted')
                    @if(!in_array($user->role, ['partner', 'independent_partner']) && $proposal->status === 'pending')
                        <div class="flex flex-col gap-2 min-w-[120px]">
                            <form action="{{ route('proposals.accept', $proposal->id) }}" method="POST">
                                @csrf
                                <button class="w-full px-4 py-2 bg-black text-white rounded text-xs">قبول العرض</button>
                            </form>
                            <form action="{{ route('proposals.reject', $proposal->id) }}" method="POST">
                                @csrf
                                <button class="w-full px-4 py-2 border border-gray-200 rounded text-xs">استبعاد</button>
                            </form>
                        </div>
                    @endif
                    @endif
                </div>
            </div>
        @empty
        <div class="text-center py-12 border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-xl">
            <p class="text-gray-400 text-sm">لا توجد عروض حالياً.</p>
        </div>
        @endforelse
    </div>
</div>
