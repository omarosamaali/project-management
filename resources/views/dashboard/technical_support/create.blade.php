@extends('layouts.app')

@section('title', 'فتح تذكرة دعم جديدة')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.technical_support.index') }}" third="إضافة تذكرة دعم"
        second="التذاكر" />

    <div class="mx-auto w-full max-w-4xl space-y-6">

        {{-- ════ بطاقات الدعم الفني — $supportProjects هي Requests فقط ════ --}}
        @if(isset($supportProjects) && $supportProjects->isNotEmpty())
        <div>
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i class="fas fa-shield-alt text-blue-500"></i>
                مشاريعك النشطة — مدة الدعم الفني المتبقية
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($supportProjects as $req)
                @php
                $remaining = $req->support_remaining_days;
                $total = $req->system->support_days ?? 1;
                $percent = $req->support_percentage;
                $color = $req->support_color;

                $colors = [
                'green' => ['border'=>'border-green-200 dark:border-green-700','bg'=>'bg-green-50
                dark:bg-green-900/10','num'=>'text-green-600
                dark:text-green-400','bar'=>'bg-green-500','badge'=>'bg-green-100 text-green-700','label'=>'نشط ✓'],
                'yellow' => ['border'=>'border-yellow-200 dark:border-yellow-700','bg'=>'bg-yellow-50
                dark:bg-yellow-900/10','num'=>'text-yellow-500
                dark:text-yellow-400','bar'=>'bg-yellow-400','badge'=>'bg-yellow-100 text-yellow-700','label'=>'قارب على
                الانتهاء ⚠'],
                'red' => ['border'=>'border-red-200 dark:border-red-700','bg'=>'bg-red-50
                dark:bg-red-900/10','num'=>'text-red-500 dark:text-red-400','bar'=>'bg-red-500','badge'=>'bg-red-100
                text-red-700','label'=>'ينتهي قريباً !'],
                'gray' => ['border'=>'border-gray-200 dark:border-gray-600','bg'=>'bg-gray-50
                dark:bg-gray-800','num'=>'text-gray-400','bar'=>'bg-gray-400','badge'=>'bg-gray-100
                text-gray-500','label'=>'غير نشط'],
                ];
                $c = $colors[$color] ?? $colors['gray'];
                @endphp
                <div class="rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} p-4 flex flex-col gap-3">

                    <div class="flex items-start justify-between gap-2">
                        <span class="font-bold text-sm text-gray-800 dark:text-white leading-tight">
                            {{ $req->system->name_ar ?? ('مشروع #' . $req->id) }}
                        </span>
                        <span class="text-xs text-gray-400 whitespace-nowrap">#{{ $req->order_number }}</span>
                    </div>

                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black tabular-nums leading-none {{ $c['num'] }}">
                            {{ max(0, $remaining) }}
                        </span>
                        <div class="flex flex-col text-xs text-gray-500 dark:text-gray-400 leading-tight pb-1">
                            <span class="font-semibold">يوم متبقي</span>
                            <span>من أصل {{ $total }} يوم</span>
                        </div>
                        <span class="mr-auto text-xs px-2 py-1 rounded-full font-semibold {{ $c['badge'] }}">
                            {{ $c['label'] }}
                        </span>
                    </div>

                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                        <div class="{{ $c['bar'] }} h-1.5 rounded-full transition-all duration-700"
                            style="width: {{ $percent }}%"></div>
                    </div>

                    @if($req->support_start_date)
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>بدأ: {{ $req->support_start_date->format('Y/m/d') }}</span>
                        <span>ينتهي: {{ $req->support_start_date->copy()->addDays($total)->format('Y/m/d') }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ════ نموذج فتح التذكرة ════ --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">

            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">نموذج فتح تذكرة دعم جديدة</h2>

            @if(session('success'))
            <div
                class="mb-4 p-4 text-sm text-green-800 bg-green-50 dark:bg-green-900/20 dark:text-green-300 border border-green-200 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('dashboard.technical_support.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- اختيار المشروع --}}
                <div>
                    <label for="request_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        ربط التذكرة بمشروع <span class="text-red-500">*</span>
                    </label>
                    <select id="request_id" name="request_id" required onchange="showSupportBadge(this)"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('request_id') border-red-500 @enderror">
                        <option value="">-- اختر المشروع --</option>
                        @foreach($userRequests as $req)
                        @php
                        // SpecialRequest ليس عندها support_remaining_days
                        $isRequest = $req instanceof \App\Models\Requests;
                        $r = $isRequest ? $req->support_remaining_days : null;
                        $total = $isRequest ? ($req->system->support_days ?? 0) : 0;
                        $name = $isRequest
                        ? ($req->system->name_ar ?? ('مشروع #' . $req->id))
                        : ($req->title ?? ('طلب خاص #' . $req->id));
                        $daysLabel = $r !== null ? " ({$r} يوم متبقي)" : '';
                        @endphp
                        <option value="{{ $req->id }}" {{ old('request_id')==$req->id ? 'selected' : '' }}
                            data-remaining="{{ $r ?? '' }}"
                            data-total="{{ $total }}">
                            {{ $name }}{{ $daysLabel }}
                        </option>
                        @endforeach
                    </select>

                    {{-- مؤشر ديناميكي --}}
                    <div id="supportBadge"
                        class="hidden mt-2 text-sm px-3 py-2 rounded-lg border flex items-center gap-2"></div>

                    @error('request_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- الموضوع --}}
                <div>
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        موضوع التذكرة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('subject') border-red-500 @enderror"
                        placeholder="مشكلة في إظهار البيانات، استفسار بخصوص خدمة...">
                    @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- الوصف --}}
                <div>
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        تفاصيل المشكلة <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="5" required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror"
                        placeholder="يرجى وصف المشكلة بالتفصيل وموعد حدوثها إن أمكن.">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-2.5 transition">
                    <i class="fa-solid fa-ticket-simple"></i>
                    فتح تذكرة الدعم الآن
                </button>
            </form>
        </div>
    </div>
</section>

<script>
    function showSupportBadge(select) {
    const opt       = select.options[select.selectedIndex];
    const badge     = document.getElementById('supportBadge');
    const remaining = opt.dataset.remaining;
    const total     = parseInt(opt.dataset.total);

    if (!opt.value || remaining === '') {
        badge.className = 'hidden mt-2 text-sm px-3 py-2 rounded-lg border flex items-center gap-2';
        return;
    }

    const r   = parseInt(remaining);
    const pct = total > 0 ? (r / total) * 100 : 0;

    badge.classList.remove('hidden');

    if (pct > 50) {
        badge.className = 'mt-2 text-sm px-3 py-2 rounded-lg border flex items-center gap-2 bg-green-50 border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-700 dark:text-green-300';
        badge.innerHTML = `<i class="fas fa-check-circle"></i> الدعم الفني نشط — متبقي <strong>${r}</strong> يوم من أصل ${total} يوم`;
    } else if (pct > 20) {
        badge.className = 'mt-2 text-sm px-3 py-2 rounded-lg border flex items-center gap-2 bg-yellow-50 border-yellow-200 text-yellow-700 dark:bg-yellow-900/20 dark:border-yellow-700 dark:text-yellow-300';
        badge.innerHTML = `<i class="fas fa-exclamation-triangle"></i> قارب على الانتهاء — متبقي <strong>${r}</strong> يوم`;
    } else {
        badge.className = 'mt-2 text-sm px-3 py-2 rounded-lg border flex items-center gap-2 bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-700 dark:text-red-300';
        badge.innerHTML = `<i class="fas fa-times-circle"></i> ينتهي قريباً — متبقي <strong>${r}</strong> يوم فقط`;
    }
}
</script>

@endsection