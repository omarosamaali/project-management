@props(['SpecialRequest'])
<div class="p-6 space-y-8">
    @php
        $allTasks = $SpecialRequest->tasks;
        $expectedTimeLabel = $SpecialRequest->expected_time_label;
        $spentTimeLabel = $SpecialRequest->spent_time_label;
        $remainingTimeLabel = $SpecialRequest->remaining_time_label;
        $progressPct = $SpecialRequest->progress_percentage;
        $expectedWorkSeconds = $SpecialRequest->expected_work_seconds;
        $hoursRangeStart = $allTasks->whereNotNull('start_date')->min('start_date')
            ?? $SpecialRequest->created_at->format('Y-m-d');
        $hoursRangeEnd = $allTasks->whereNotNull('end_date')->max('end_date');
        $rTotalTasks = $allTasks->count();
        $rDoneTasks = $allTasks->where('status', 'منتهية')->count();
        $rInProgTasks = $allTasks->where('status', 'قيد الإنجاز')->count();
        $rLateTasks = $allTasks->where('status', 'متأخرة')->count();
        $rTotalStages = $SpecialRequest->stages->count();
        $rTotalNotes = $SpecialRequest->notes->count();
        $rTotalFiles = $SpecialRequest->requestFiles->count();
        $rTotalActivities = $SpecialRequest->activities->count();
    @endphp

    {{-- ساعات العمل والإحصائيات (أولاً) --}}
    <div class="border-b pb-6 space-y-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-2 flex items-center gap-2">
            <i class="fas fa-clock text-indigo-600"></i>
            ساعات العمل والتقدم
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div
                class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-5 rounded-lg border-2 border-blue-200 dark:border-blue-700">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                    عدد ساعات العمل المتوقعة
                </label>
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400 leading-relaxed">
                    {{ $expectedTimeLabel }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i>
                    (من {{ \Carbon\Carbon::parse($hoursRangeStart)->format('Y-m-d') }} إلى
                    {{ $hoursRangeEnd ? \Carbon\Carbon::parse($hoursRangeEnd)->format('Y-m-d') : '---' }})
                    @if($allTasks->whereNotNull('start_date')->count() > 0)
                    <span class="text-gray-400"> — من تواريخ المهام</span>
                    @endif
                </p>
            </div>

            <div
                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-5 rounded-lg border-2 border-green-200 dark:border-green-700">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                    <i class="fas fa-stopwatch text-green-600"></i>
                    عدد الساعات المستغرقة حتى الآن
                </label>
                <span class="text-2xl font-bold text-green-600 dark:text-green-400 leading-relaxed">
                    {{ $spentTimeLabel }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i>
                    مجموع الوقت المخزّن من عداد المهام فقط (لا يشمل أيام التقويم أو الوقت غير المحفوظ)
                </p>
                @if(count($SpecialRequest->spent_by_workers) > 0)
                <ul class="mt-3 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                    @foreach($SpecialRequest->spent_by_workers as $worker)
                    <li class="flex justify-between gap-2">
                        <span><i class="fas fa-user text-green-500 ml-1"></i>{{ $worker['user_name'] }}</span>
                        <span class="font-semibold text-green-700 dark:text-green-400">{{ $worker['label'] }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif

                @if($expectedWorkSeconds > 0 || $progressPct > 0)
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-600 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $progressPct >= 100 ? 'bg-red-600' : 'bg-green-600' }}"
                            style="width: {{ min($progressPct, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span
                            class="text-sm font-semibold {{ $progressPct >= 100 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $progressPct }}% مكتمل
                        </span>
                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            متبقي: {{ $remainingTimeLabel }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-blue-600"></i>
                إحصائيات المشروع
            </h3>
            <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد الملفات</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rTotalFiles }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد المهام</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rTotalTasks }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد مراحل المشروع</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rTotalStages }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد الملاحظات</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rTotalNotes }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد الأنشطة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $rTotalActivities }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">المهام المنجزة</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $rDoneTasks }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">قيد الإنجاز</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $rInProgTasks }}</p>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">المهام المتأخرة</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $rLateTasks }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- المدة والتواريخ --}}
    <div class="border-b pb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-alt text-blue-600"></i>
            تفاصيل المشروع
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                    بدء
                    المشروع</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $SpecialRequest->created_at->format('Y-m-d') }}
                </span>
            </div>

            @if($SpecialRequest->maintenance_period)
            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-700">
                <label class="block text-sm font-medium text-orange-600 dark:text-orange-400 mb-1 flex items-center gap-1">
                    <i class="fas fa-tools text-xs"></i>
                    فترة الصيانة بعد التسليم
                </label>
                <span class="text-lg font-bold text-orange-700 dark:text-orange-300">
                    {{ $SpecialRequest->maintenance_period }}
                    {{ $SpecialRequest->maintenance_unit === 'months' ? 'شهر' : 'يوم' }}
                </span>
            </div>
            @endif

            {{-- <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الموعد
                    النهائي
                    المتوقع</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $SpecialRequest->deadline ?? 'غير محدد' }}
                </span>
            </div> --}}

            {{-- <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ
                    الطلب</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $SpecialRequest->created_at->format('Y-m-d H:i') }}
                </span>
            </div> --}}
        </div>
    </div>

    {{-- معلومات العملاء (مخفي عند الشريك) --}}
    @if (auth()->user()->role !== 'partner')
    @php
        $projectClients = $SpecialRequest->clients;
        $allAvailableClients = auth()->user()->role === 'admin'
            ? \App\Models\User::where('role', 'client')
                ->whereNotIn('id', $projectClients->pluck('id'))
                ->get()
            : collect();
    @endphp
    <div class="border-b pb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-user-tie text-blue-600"></i>
                العملاء
                <span class="text-sm font-normal text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400 px-2 py-0.5 rounded-full">
                    {{ $projectClients->count() }}
                </span>
            </h2>
            @if(auth()->user()->role === 'admin' && $allAvailableClients->count() > 0)
            <button onclick="document.getElementById('addClientFormReq').classList.toggle('hidden')"
                class="flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                <i class="fas fa-plus"></i> إضافة عميل
            </button>
            @endif
        </div>

        @if($projectClients->count() > 0)
        <div class="grid md:grid-cols-2 gap-3 mb-4">
            @foreach($projectClients as $c)
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ mb_substr($c->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $c->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $c->email }}</p>
                    </div>
                </div>
                @if(auth()->user()->role === 'admin')
                <form action="{{ route('dashboard.request.remove-client', [$SpecialRequest, $c]) }}"
                    method="POST" onsubmit="return confirm('هل تريد إزالة هذا العميل من المشروع؟')">
                    @csrf @method('DELETE')
                    <button type="submit" title="إزالة العميل"
                        class="text-red-400 hover:text-red-600 transition p-1 rounded">
                        <i class="fas fa-user-times text-sm"></i>
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4 text-center text-gray-500">
            <i class="fas fa-user-slash text-2xl mb-2"></i>
            <p class="text-sm">لا يوجد عملاء مرتبطون بهذا المشروع</p>
        </div>
        @endif

        @if(auth()->user()->role === 'admin')
        <div id="addClientFormReq" class="hidden mt-3">
            <form action="{{ route('dashboard.request.add-client', $SpecialRequest) }}" method="POST"
                class="flex gap-2">
                @csrf
                <select name="user_id"
                    class="flex-1 p-2 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                    required>
                    <option value="">اختر عميلاً لإضافته...</option>
                    @foreach($allAvailableClients as $ac)
                    <option value="{{ $ac->id }}">{{ $ac->name }} — {{ $ac->email }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition whitespace-nowrap">
                    <i class="fas fa-check ml-1"></i> تأكيد الإضافة
                </button>
                <button type="button"
                    onclick="document.getElementById('addClientFormReq').classList.add('hidden')"
                    class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300 transition">
                    إلغاء
                </button>
            </form>
        </div>
        @endif
    </div>
    @endif
</div>