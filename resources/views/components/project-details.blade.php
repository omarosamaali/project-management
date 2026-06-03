@props(['SpecialRequest'])
<div class="p-6 space-y-8">
    @php
        $allTasks = $SpecialRequest->tasks;
        $totalTasks    = $allTasks->count();
        $doneTasks     = $allTasks->where('status', 'منتهية')->count();
        $inProgTasks   = $allTasks->where('status', 'قيد الإنجاز')->count();
        $lateTasks     = $allTasks->where('status', 'متأخرة')->count();
        $totalStages   = $SpecialRequest->stages->count();
        $totalNotes    = $SpecialRequest->notes->count();
        $totalFiles    = $SpecialRequest->requestFiles->count();
        $totalActivities = $SpecialRequest->activities->count();
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
                    {{ $SpecialRequest->expected_time_label }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i>
                    (من {{ $SpecialRequest->created_at->format('Y-m-d') }} إلى {{ $SpecialRequest->deadline ?
                    $SpecialRequest->deadline->format('Y-m-d') : '---' }})
                </p>
                @if($SpecialRequest->deadline)
                @php
                    $totalCalDays = (int) \Carbon\Carbon::parse($SpecialRequest->created_at)->startOfDay()
                                        ->diffInDays(\Carbon\Carbon::parse($SpecialRequest->deadline)->startOfDay());
                    $fullWeeks    = intdiv($totalCalDays, 7);
                    $remainDays   = $totalCalDays % 7;
                    $workDays     = ($fullWeeks * \App\Models\SpecialRequest::WORK_DAYS_PER_WEEK) + min($remainDays, \App\Models\SpecialRequest::WORK_DAYS_PER_WEEK);
                @endphp
                <p class="text-xs text-gray-500 mt-1">
                    = {{ $workDays }} يوم عمل × {{ \App\Models\SpecialRequest::WORK_HOURS_PER_DAY }} ساعة/يوم
                    <span class="text-gray-400">({{ $totalCalDays }} يوم تقويمي، {{ \App\Models\SpecialRequest::WORK_DAYS_PER_WEEK }} أيام/أسبوع)</span>
                </p>
                @endif
            </div>

            <div
                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-5 rounded-lg border-2 border-green-200 dark:border-green-700">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                    <i class="fas fa-stopwatch text-green-600"></i>
                    عدد الساعات المستغرقة حتى الآن
                </label>
                <span class="text-2xl font-bold text-green-600 dark:text-green-400 leading-relaxed">
                    {{ $SpecialRequest->spent_time_label }}
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

                @if($SpecialRequest->expected_work_seconds > 0 || $SpecialRequest->progress_percentage > 0)
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-600 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $SpecialRequest->progress_percentage >= 100 ? 'bg-red-600' : 'bg-green-600' }}"
                            style="width: {{ min($SpecialRequest->progress_percentage, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span
                            class="text-sm font-semibold {{ $SpecialRequest->progress_percentage >= 100 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $SpecialRequest->progress_percentage }}% مكتمل
                        </span>
                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            متبقي: {{ $SpecialRequest->remaining_time_label }}
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
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalFiles }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد المهام</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalTasks }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد مراحل المشروع</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalStages }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد الملاحظات</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalNotes }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">عدد الأنشطة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalActivities }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">المهام المنجزة</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $doneTasks }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">قيد الإنجاز</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $inProgTasks }}</p>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">المهام المتأخرة</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $lateTasks }}</p>
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

        @php
            $projectStartDate = $allTasks->whereNotNull('start_date')->min('start_date')
                ?? $SpecialRequest->created_at->format('Y-m-d');
            $projectEndDate = $allTasks->whereNotNull('end_date')->max('end_date')
                ?? ($SpecialRequest->deadline
                    ? \Carbon\Carbon::parse($SpecialRequest->deadline)->format('Y-m-d')
                    : null);
        @endphp
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">تاريخ بدء المشروع</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($projectStartDate)->format('Y-m-d') }}
                </span>
                @if($allTasks->whereNotNull('start_date')->count() > 0)
                <span class="text-xs text-gray-400 mt-1 block">من أول مهمة</span>
                @endif
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">الموعد النهائي المتوقع</label>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $projectEndDate ? \Carbon\Carbon::parse($projectEndDate)->format('Y-m-d') : 'غير محدد' }}
                </span>
                @if($allTasks->whereNotNull('end_date')->count() > 0)
                <span class="text-xs text-gray-400 mt-1 block">من آخر مهمة</span>
                @endif
            </div>

            {{-- مدة الدعم الفني بعد التسليم --}}
            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-700">
                <label class="block text-sm font-medium text-orange-600 dark:text-orange-400 mb-1 flex items-center gap-1">
                    <i class="fas fa-tools text-xs"></i>
                    مدة الدعم الفني بعد التسليم
                </label>
                @if($SpecialRequest->maintenance_period)
                <span class="text-xl font-bold text-orange-700 dark:text-orange-300">
                    {{ $SpecialRequest->maintenance_period }}
                    {{ $SpecialRequest->maintenance_unit === 'months' ? 'شهر' : 'يوم' }}
                </span>
                @else
                <span class="text-sm text-gray-400 italic">غير محدد</span>
                @endif

                @if(auth()->user()->role === 'admin')
                <button onclick="document.getElementById('maintenanceEditBox').classList.toggle('hidden')"
                    class="mt-2 text-xs text-orange-600 hover:text-orange-800 flex items-center gap-1">
                    <i class="fas fa-edit"></i> تعديل
                </button>
                <div id="maintenanceEditBox" class="hidden mt-3">
                    <form action="{{ route('dashboard.special-request.update-maintenance', $SpecialRequest) }}"
                        method="POST" class="flex flex-wrap gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="maintenance_period" min="1"
                            value="{{ $SpecialRequest->maintenance_period }}"
                            placeholder="عدد"
                            class="w-20 p-1.5 text-sm rounded border border-orange-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <select name="maintenance_unit"
                            class="p-1.5 text-sm rounded border border-orange-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="days" {{ $SpecialRequest->maintenance_unit === 'days' ? 'selected' : '' }}>يوم</option>
                            <option value="months" {{ $SpecialRequest->maintenance_unit === 'months' ? 'selected' : '' }}>شهر</option>
                        </select>
                        <button type="submit"
                            class="px-3 py-1.5 bg-orange-600 text-white rounded text-xs font-bold hover:bg-orange-700">حفظ</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ====== تفاصيل الطلب المُقدَّم من العميل ====== --}}
    <div class="border-b pb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-file-alt text-purple-600"></i>
            تفاصيل الطلب المُقدَّم
        </h2>

        <div class="space-y-5">

            {{-- نوع النظام / المشروع --}}
            @if($SpecialRequest->project_type_label)
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    <i class="fas fa-tag ml-1 text-purple-500"></i> نوع المشروع / النظام
                </label>
                <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full font-bold text-sm">
                    {{ $SpecialRequest->project_type_label }}
                </span>
            </div>
            @endif

            {{-- الميزانية والجدول الزمني --}}
            <div class="grid md:grid-cols-2 gap-4">
                @if($SpecialRequest->budget)
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-700">
                    <label class="block text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider mb-2">
                        <i class="fas fa-dollar-sign ml-1"></i> الميزانية المتوقعة
                    </label>
                    <span class="text-lg font-bold text-green-700 dark:text-green-300">{{ $SpecialRequest->budget }}</span>
                </div>
                @endif

                @if($SpecialRequest->deadline)
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700">
                    <label class="block text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2">
                        <i class="fas fa-calendar-check ml-1"></i> الجدول الزمني (الموعد المطلوب)
                    </label>
                    <span class="text-lg font-bold text-blue-700 dark:text-blue-300">
                        {{ $SpecialRequest->deadline instanceof \Carbon\Carbon
                            ? $SpecialRequest->deadline->format('Y-m-d')
                            : $SpecialRequest->deadline }}
                    </span>
                </div>
                @endif
            </div>

            {{-- الوصف --}}
            @if($SpecialRequest->description)
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    <i class="fas fa-align-left ml-1 text-blue-500"></i> وصف المشروع
                </label>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $SpecialRequest->description }}</p>
            </div>
            @endif

            {{-- الميزات الأساسية --}}
            @if($SpecialRequest->core_features)
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    <i class="fas fa-list-check ml-1 text-green-500"></i> الميزات الأساسية المطلوبة
                </label>
                <div class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $SpecialRequest->core_features }}</div>
            </div>
            @endif

            {{-- أمثلة ومراجع --}}
            @if($SpecialRequest->examples)
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    <i class="fas fa-external-link-alt ml-1 text-indigo-500"></i> أمثلة أو مراجع
                </label>
                <div class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $SpecialRequest->examples }}</div>
            </div>
            @endif

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
            <button onclick="document.getElementById('addClientForm').classList.toggle('hidden')"
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
                        @if($c->phone)
                        <p class="text-xs text-gray-400">{{ $c->phone }}</p>
                        @endif
                    </div>
                </div>
                @if(auth()->user()->role === 'admin')
                <form action="{{ route('dashboard.special-request.remove-client', [$SpecialRequest, $c]) }}"
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

        {{-- نموذج إضافة عميل (للأدمن فقط) --}}
        @if(auth()->user()->role === 'admin')
        <div id="addClientForm" class="{{ $allAvailableClients->count() > 0 ? 'hidden' : 'hidden' }} mt-3">
            <form action="{{ route('dashboard.special-request.add-client', $SpecialRequest) }}" method="POST"
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
                    onclick="document.getElementById('addClientForm').classList.add('hidden')"
                    class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300 transition">
                    إلغاء
                </button>
            </form>
        </div>
        @endif
    </div>
    @endif
</div>