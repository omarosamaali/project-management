@props(['SpecialRequest', 'partners', 'managers'])
@if($SpecialRequest->is_project != 1)
<div class="pt-6 p-6 space-y-8">
    @if (Auth::user()->role != 'client')
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <i class="fas fa-tools text-blue-600"></i>
        تعيين فريق العمل
    </h2>
    @endif

    {{-- قسم الشركاء المسندين --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 border">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-users"></i>
            @if(Auth::user()->role != 'client')
            الشركاء المُسندين للمشروع
            @else
            فريق العمل
            @endif
        </h3>

        @php
        $currentTotalShare = $SpecialRequest->partners->sum('pivot.profit_share_percentage');
        @endphp

        @if ($SpecialRequest->partners->count() > 0)
        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-800 dark:text-blue-300">
                    <i class="fas fa-chart-pie ml-2"></i>
                    مجموع النسب المسندة حالياً:
                </span>
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $currentTotalShare }}%
                </span>
            </div>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle ml-1"></i>
                المتاح للإسناد: <strong>{{ 100 - $currentTotalShare }}%</strong>
            </div>
        </div>

        <div class="space-y-3">
            @foreach ($SpecialRequest->partners as $partner)
            @php
            $canSeeDetails = auth()->user()->role === 'admin' || auth()->id() === $partner->id;

            $partnerMeetings = $partner->projectMeetings()
            ->where('special_request_id', $SpecialRequest->id)
            ->get();

            $attendedCount = $partnerMeetings->where('pivot.status', 'attended')->count();
            $declinedCount = $partnerMeetings->where('pivot.status', 'declined')->count();
            $absentCount = $partnerMeetings->where('pivot.status', 'absent')->count();

            $memberTasks = $SpecialRequest->tasks->where('user_id', $partner->id);
            $tasksCount = $memberTasks->count();
            $inProgress = $memberTasks->where('status', 'قيد الإنجاز')->count();
            $overdue = $memberTasks->where('status', 'متأخرة')->count();
            $completed = $memberTasks->where('status', 'منتهية')->count();
            $waiting = $memberTasks->where('status', 'بالانتظار')->count();
            $memberPercent = $tasksCount > 0 ? round(($completed / $tasksCount) * 100) : 0;
            @endphp

            {{-- كارت الشريك --}}
            <div
                class="bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600 overflow-hidden">

                {{-- الرأس: الاسم + النسبة --}}
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $partner->name }}</span>

                                @if($SpecialRequest->projectManager && $SpecialRequest->projectManager->user_id ==
                                $partner->id)
                                <span
                                    class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full">
                                    <i class="fas fa-crown"></i> مدير المشروع
                                </span>
                                @endif

                                @if($canSeeDetails)
                                <span class="text-xs font-black px-2 py-0.5 rounded-full
                                    {{ $partner->pivot->share_type === 'percentage'
                                        ? 'text-green-600 bg-green-50 dark:bg-green-900/30'
                                        : 'text-blue-600 bg-blue-50 dark:bg-blue-900/30' }}">
                                    @if($partner->pivot->share_type === 'percentage')
                                    {{ $partner->pivot->profit_share_percentage }}%
                                    @else
                                    {{ number_format($partner->pivot->fixed_amount, 2) }}
                                    @endif
                                </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5">تم الإسناد: {{
                                $partner->pivot->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- نسبة الإنجاز --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-green-600">{{ $memberPercent }}%</span>
                        <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $memberPercent }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- الإحصائيات: المهام + الاجتماعات في صف واحد --}}
                <div
                    class="grid grid-cols-7 divide-x divide-gray-200 dark:divide-gray-600 border-t border-gray-200 dark:border-gray-600">

                    {{-- المهام --}}
                    <div class="p-2 text-center bg-white dark:bg-gray-800">
                        <p class="text-[13px] text-gray-500 font-medium mb-0.5">المهام</p>
                        <p class="text-sm font-black text-gray-700 dark:text-gray-200">{{ $tasksCount }}</p>
                    </div>
                    <div class="p-2 text-center bg-blue-50 dark:bg-blue-900/10">
                        <p class="text-[13px] text-blue-600 font-medium mb-0.5">جاري</p>
                        <p class="text-sm font-black text-blue-700 dark:text-blue-300">{{ $inProgress }}</p>
                    </div>
                    <div class="p-2 text-center bg-red-50 dark:bg-red-900/10">
                        <p class="text-[13px] text-red-600 font-medium mb-0.5">متأخرة</p>
                        <p class="text-sm font-black text-red-700 dark:text-red-300">{{ $overdue }}</p>
                    </div>
                    <div class="p-2 text-center bg-green-50 dark:bg-green-900/10">
                        <p class="text-[13px] text-green-600 font-medium mb-0.5">منتهية</p>
                        <p class="text-sm font-black text-green-700 dark:text-green-300">{{ $completed }}</p>
                    </div>

                    {{-- فاصل بين المهام والاجتماعات --}}
                    <div
                        class="p-2 text-center bg-gray-100 dark:bg-gray-700/50 flex flex-col items-center justify-center">
                        <i class="fas fa-users text-gray-400 text-[13px]"></i>
                        <p class="text-[8px] text-gray-400 mt-0.5">اجتماعات</p>
                    </div>

                    {{-- الاجتماعات --}}
                    <div class="p-2 text-center bg-emerald-50 dark:bg-emerald-900/10">
                        <p class="text-[13px] text-emerald-600 font-medium mb-0.5">حضر</p>
                        <p class="text-sm font-black text-emerald-700 dark:text-emerald-300">{{ $attendedCount }}</p>
                    </div>
                    <div class="p-2 text-center bg-orange-50 dark:bg-orange-900/10">
                        <p class="text-[13px] text-orange-600 font-medium mb-0.5">اعتذر</p>
                        <p class="text-sm font-black text-orange-700 dark:text-orange-300">{{ $declinedCount }}</p>
                    </div>
                </div>

                {{-- قائمة المهام الفعلية --}}
                @if($tasksCount > 0)
                <div
                    class="divide-y divide-gray-100 dark:divide-gray-700 border-t border-gray-200 dark:border-gray-600">
                    @foreach($memberTasks as $task)
                    @php
                    $statusConfig = [
                    'قيد الإنجاز' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-spinner'],
                    'متأخرة' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-exclamation-circle'],
                    'منتهية' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                    'بالانتظار' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-pause-circle'],
                    ];
                    $cfg = $statusConfig[$task->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' =>
                    'fa-circle'];
                    @endphp
                    <div
                        class="flex items-center justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700/30 transition">
                        <div class="flex items-center gap-2">
                            <i class="fas {{ $cfg['icon'] }} {{ $cfg['text'] }} text-xs"></i>
                            <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $task->title }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[13px] text-gray-400">{{ $task->start_date }} → {{ $task->end_date
                                }}</span>
                            <span
                                class="text-[13px] font-bold px-1.5 py-0.5 rounded-full {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                {{ $task->status }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- ملاحظات --}}
                @if ($partner->pivot->notes && $canSeeDetails)
                <div
                    class="mx-3 mb-2 mt-1 text-[10px] bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded border-r-2 border-yellow-400 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-sticky-note ml-1 text-yellow-600"></i> {{ $partner->pivot->notes }}
                </div>
                @endif

                {{-- زر الإلغاء --}}
                @if(auth()->user()->role === 'admin')
                <div class="px-3 pb-2">
                    <form action="{{ route('dashboard.special-request.remove-partner', [$SpecialRequest, $partner]) }}"
                        method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="text-[10px] text-red-500 hover:text-red-700 transition flex items-center gap-1">
                            <i class="fas fa-user-times"></i> إلغاء الإسناد
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="fas fa-users-slash text-4xl mb-3"></i>
            <p>لم يتم إسناد أي شريك لهذا المشروع بعد</p>
        </div>
        @endif
    </div>

    {{-- نموذج إسناد شريك جديد --}}
    @if ($errors->any())
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <div class="flex items-center gap-2">
            <i class="fas fa-times-circle"></i>
            <span class="font-medium">{{ $errors->first() }}</span>
        </div>
    </div>
    @endif
    @if(Auth::user()->role === 'admin')
    <form id="assignPartnersForm" action="{{ route('dashboard.special-request.assign-partners', $SpecialRequest) }}"
        method="POST" class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        @csrf

        {{-- رسالة التحذير الفورية --}}
        <div id="validationAlert" class="hidden mb-4 p-4 rounded-lg border">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-2xl mt-1"></i>
                <div class="flex-1">
                    <h4 class="font-bold mb-1">تحذير: تجاوز النسبة المسموحة!</h4>
                    <p id="validationMessage" class="text-sm"></p>
                </div>
            </div>
        </div>
        {{-- مؤشر المجموع الفوري --}}
        <div id="totalIndicator" class="mb-4 p-4 rounded-lg border transition-all duration-300">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium">
                    <i class="fas fa-calculator ml-2"></i>
                    مجموع النسب الجديدة:
                </span>
                <span id="totalPercentage" class="text-2xl font-bold">
                    0%
                </span>
            </div>
            <div class="mt-2 text-sm">
                <span>المسند حالياً: <strong>{{ $currentTotalShare }}%</strong></span>
                <span class="mx-2">|</span>
                <span>المجموع الكلي: <strong id="grandTotal">{{ $currentTotalShare
                        }}%</strong></span>
                <span class="mx-2">|</span>
                <span>المتبقي: <strong id="remaining">{{ 100 - $currentTotalShare
                        }}%</strong></span>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                اختر الشركاء وحدد نسبة الأرباح لكل منهم:
            </h3>
            <div class="space-y-3 bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-300 dark:border-gray-600">
                @forelse($partners as $partner)
                <div class="flex items-center justify-between p-2 border-b last:border-b-0">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="partner_{{ $partner->id }}" name="partner_id[]"
                            value="{{ $partner->id }}"
                            class="partner-checkbox rounded text-blue-600 focus:ring-blue-500"
                            onchange="validateTotal()">
                        <label for="partner_{{ $partner->id }}"
                            class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $partner->name }}
                        </label>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- اختيار النوع --}}
                        <select id="share_type_{{ $partner->id }}" name="share_type[{{ $partner->id }}]" disabled class="share-type-select w-28 bg-gray-50 dark:bg-gray-700 border border-gray-300
                            dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg p-1.5"
                            onchange="toggleShareType({{ $partner->id }})">
                            <option value="percentage">نسبة %</option>
                            <option value="fixed">مبلغ ثابت</option>
                        </select>

                        {{-- حقل النسبة المؤوية --}}
                        <input type="number" id="percentage_{{ $partner->id }}"
                            name="profit_share_percentage[{{ $partner->id }}]" min="0" max="100" placeholder="%"
                            disabled class="percentage-input w-20 bg-gray-50 dark:bg-gray-700 border border-gray-300
                            dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg p-1.5"
                            oninput="validateTotal()">
                        <span class="percentage-label text-gray-600 dark:text-gray-300"
                            data-partner="{{ $partner->id }}">%</span>

                        {{-- حقل المبلغ الثابت (مخفي بالبداية) --}}
                        <input type="number" id="fixed_amount_{{ $partner->id }}"
                            name="fixed_amount[{{ $partner->id }}]" min="0" placeholder="المبلغ" disabled
                            style="display: none;" class="fixed-amount-input w-28 bg-gray-50 dark:bg-gray-700 border border-gray-300
                            dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg p-1.5">
                    </div>
                    <script>
                        document.querySelectorAll('.partner-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const partnerId = this.value;
                        const shareTypeSelect = document.getElementById('share_type_' + partnerId);
                        const percentageInput = document.getElementById('percentage_' + partnerId);
                        const fixedAmountInput = document.getElementById('fixed_amount_' + partnerId);
                
                        if (this.checked) {
                            shareTypeSelect.disabled = false;
                            // فعّل الحقل حسب النوع الحالي
                            applyShareType(partnerId);
                        } else {
                            // أعد كل شيء للصفر
                            shareTypeSelect.disabled = true;
                            shareTypeSelect.value = 'percentage';
                
                            percentageInput.disabled = true;
                            percentageInput.value = '';
                            percentageInput.style.display = '';
                
                            fixedAmountInput.disabled = true;
                            fixedAmountInput.value = '';
                            fixedAmountInput.style.display = 'none';
                
                            // الـ labels
                            document.querySelector('.percentage-label[data-partner="' + partnerId + '"]').style.display = '';
                            document.querySelector('.fixed-label[data-partner="' + partnerId + '"]').style.display = 'none';
                        }
                        validateTotal();
                    });
                });
                
                function toggleShareType(partnerId) {
                    applyShareType(partnerId);
                    validateTotal();
                }
                
                function applyShareType(partnerId) {
                    const type = document.getElementById('share_type_' + partnerId).value;
                    const percentageInput = document.getElementById('percentage_' + partnerId);
                    const fixedAmountInput = document.getElementById('fixed_amount_' + partnerId);
                    const percentageLabel = document.querySelector('.percentage-label[data-partner="' + partnerId + '"]');
                    const fixedLabel = document.querySelector('.fixed-label[data-partner="' + partnerId + '"]');
                
                    if (type === 'percentage') {
                        percentageInput.disabled = false;
                        percentageInput.style.display = '';
                        percentageLabel.style.display = '';
                
                        fixedAmountInput.disabled = true;
                        fixedAmountInput.value = '';
                        fixedAmountInput.style.display = 'none';
                        fixedLabel.style.display = 'none';
                    } else {
                        fixedAmountInput.disabled = false;
                        fixedAmountInput.style.display = '';
                        fixedLabel.style.display = '';
                
                        percentageInput.disabled = true;
                        percentageInput.value = '';
                        percentageInput.style.display = 'none';
                        percentageLabel.style.display = 'none';
                    }
                }
                
                function validateTotal() {
                    // حساب المجموع بس للنسب المؤوية
                    let newPercentageTotal = 0;
                    document.querySelectorAll('.partner-checkbox:checked').forEach(cb => {
                        const partnerId = cb.value;
                        const type = document.getElementById('share_type_' + partnerId).value;
                        if (type === 'percentage') {
                            const val = parseInt(document.getElementById('percentage_' + partnerId).value) || 0;
                            newPercentageTotal += val;
                        }
                    });
                
                    const currentTotal = {{ $currentTotalShare }};
                    const grandTotal = currentTotal + newPercentageTotal;
                    const remaining = 100 - grandTotal;
                
                    // تحديث الـ UI
                    document.getElementById('totalPercentage').textContent = newPercentageTotal + '%';
                    document.getElementById('grandTotal').textContent = grandTotal + '%';
                    document.getElementById('remaining').textContent = remaining + '%';
                
                    // تلوين حسب الحالة
                    const indicator = document.getElementById('totalIndicator');
                    const alert = document.getElementById('validationAlert');
                
                    if (grandTotal > 100) {
                        indicator.className = 'mb-4 p-4 rounded-lg border border-red-300 bg-red-50 dark:bg-red-900/20';
                        document.getElementById('totalPercentage').className = 'text-2xl font-bold text-red-600';
                        alert.classList.remove('hidden');
                        alert.className = 'mb-4 p-4 rounded-lg border border-red-300 bg-red-50 dark:bg-red-900/20 text-red-700';
                        document.getElementById('validationMessage').textContent =
                            'مجموع النسب المؤوية تجاوز 100%. الحد المتبقي: ' + (100 - currentTotal) + '%';
                        document.getElementById('submitBtn').disabled = true;
                        document.getElementById('submitBtn').classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        indicator.className = 'mb-4 p-4 rounded-lg border border-green-300 bg-green-50 dark:bg-green-900/20';
                        document.getElementById('totalPercentage').className = 'text-2xl font-bold text-green-600';
                        alert.classList.add('hidden');
                        document.getElementById('submitBtn').disabled = false;
                        document.getElementById('submitBtn').classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }
                    </script>
                </div>
                @empty
                <p class="text-center text-gray-500 dark:text-gray-400">لا يوجد شركاء متاحون
                    للإسناد.
                </p>
                @endforelse
            </div>
        </div>

        <div class="mb-4">
            <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                ملاحظات للشريك (اختياري)
            </label>
            <textarea id="notes" name="notes" rows="3"
                class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                placeholder="أضف أي ملاحظات أو تعليمات للشريك...">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3 mt-3">
            <button type="submit" id="submitBtn"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-user-check"></i>
                إسناد المشروع
            </button>
            <a href="{{ route('dashboard.special-request.index') }}"
                class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
    @endif
    @if(Auth::user()->role === 'admin')
    @if($SpecialRequest->is_project == 0)
    <form action="{{ route('dashboard.project_manager.store') }}" method="POST"
        class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        @csrf
        <h3 class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">تحديد مدير المشروع</h3>
        @forelse($managers as $partner)
        <div class="flex items-center justify-between p-2 border-b last:border-b-0">
            <div class="flex items-center gap-3">
                <input type="radio" id="manager_{{ $partner->id }}" name="user_id" value="{{ $partner->id }}" {{
                    (isset($SpecialRequest->projectManager) && $SpecialRequest->projectManager->user_id == $partner->id)
                ?
                'checked' : '' }}
                class="manager-checkbox rounded-full text-blue-600 focus:ring-blue-500">

                <label for="manager_{{ $partner->id }}" class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $partner->name }}
                </label>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500">لا يوجد شركاء متاحون للإسناد.</p>
        @endforelse
        @if(get_class($SpecialRequest) === 'App\Models\Requests')
        <input type="hidden" name="request_id" value="{{ $SpecialRequest->id }}">
        @else
        <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
        @endif

        <button type="submit" class="px-6 mt-3 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            تحديد مدير المشروع
        </button>
    </form>
    @endif
    @endif
</div>
@endif

@if($SpecialRequest->is_project == 1)

@if(Auth::user()->role === 'admin')
<div class="pt-6 p-6 space-y-6">

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-crown text-amber-500"></i>
            تعيين مدير المشروع
        </h3>

        <form action="{{ route('dashboard.project_manager.store') }}" method="POST">
            @csrf

            <div class="space-y-3">
                @forelse(\App\Models\User::where('role', 'partner')->get() as $partner)
                <div class="flex items-center justify-between p-3 rounded-lg border
                        {{ (isset($SpecialRequest->projectManager) && $SpecialRequest->projectManager->user_id == $partner->id)
                            ? 'bg-amber-50 border-amber-300 dark:bg-amber-900/20'
                            : 'bg-gray-50 dark:bg-gray-700' }}">

                    <div class="flex items-center gap-3">
                        <input type="radio" id="manager_project_{{ $partner->id }}" name="user_id"
                            value="{{ $partner->id }}" {{ (isset($SpecialRequest->projectManager) &&
                        $SpecialRequest->projectManager->user_id == $partner->id) ? 'checked' : '' }}
                        class="rounded-full text-blue-600 focus:ring-blue-500">

                        <label for="manager_project_{{ $partner->id }}"
                            class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $partner->name }}
                        </label>
                    </div>

                    @if(isset($SpecialRequest->projectManager) && $SpecialRequest->projectManager->user_id ==
                    $partner->id)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full
                                text-amber-700 bg-amber-100 dark:bg-amber-900/40 dark:text-amber-300">
                        مدير المشروع الحالي
                    </span>
                    @endif
                </div>
                @empty
                <p class="text-center text-gray-500">لا يوجد شركاء متاحون</p>
                @endforelse
            </div>

            {{-- hidden id --}}
            @if(get_class($SpecialRequest) === 'App\Models\Requests')
            <input type="hidden" name="request_id" value="{{ $SpecialRequest->id }}">
            @else
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
            @endif

            <button type="submit"
                class="mt-4 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-check"></i>
                حفظ مدير المشروع
            </button>
        </form>
    </div>

</div>
@endif
@php
$acceptedProposal = $SpecialRequest->proposals
->where('status', 'accepted')
->first();
@endphp

@if($acceptedProposal)
<div class="m-6 !mt-1 bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700 rounded-lg p-5 space-y-4">

    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            العرض المختار
        </h3>

        <span class="text-xs font-bold px-2 py-1 rounded-full bg-amber-600 text-white">
            تم الاختيار
        </span>
    </div>

    <div class="text-sm space-y-2 text-gray-800 dark:text-gray-200">
        <p>
            <strong>مقدم العرض:</strong>
            {{ $acceptedProposal->user->name ?? 'غير معروف' }}
        </p>

        <p>
            <div class="flex items-center gap-1">
            <strong>قيمة العرض:</strong>
                {{ number_format($acceptedProposal->budget_to, 2) }} <x-drhm-icon width="12" height="14" />
            </div>
        </p>

        <p>
            <strong>مدة التنفيذ:</strong>
            {{ $acceptedProposal->execution_time }} يوم
        </p>
    </div>

    @if($acceptedProposal->proposal_details)
    <div class="text-sm text-gray-600 dark:text-gray-300 border-t pt-3">
        <strong>تفاصيل العرض:</strong>
        <p class="mt-1 italic">
            {{ $acceptedProposal->proposal_details }}
        </p>
    </div>
    @endif

</div>
@else
<div class="text-center text-gray-400 text-sm py-6">
    لم يتم اختيار أي عرض حتى الآن
</div>
@endif
@endif