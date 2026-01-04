@props(['SpecialRequest', 'partners', 'managers'])

<div class="pt-6 p-6 space-y-8">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <i class="fas fa-tools text-blue-600"></i>
        تعيين فريق العمل
    </h2>

    {{-- قسم الشركاء المسندين --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 border">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-users"></i>
            الشركاء المُسندين للمشروع
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
            // تحديد الصلاحية: الأدمن يرى الكل، والشريك يرى نفسه فقط
            $canSeeDetails = auth()->user()->role === 'admin' || auth()->id() === $partner->id;

            // حساب إحصائيات الاجتماعات الخاصة بهذا الشريك لهذا الطلب تحديداً
            $partnerMeetings = $partner->projectMeetings()
            ->where('special_request_id', $SpecialRequest->id)
            ->get();

            $attendedCount = $partnerMeetings->where('pivot.status', 'attended')->count();
            $declinedCount = $partnerMeetings->where('pivot.status', 'declined')->count();
            $absentCount = $partnerMeetings->where('pivot.status', 'absent')->count();

            // فرضاً أن المهام مرتبطة بعلاقة tasks في موديل المستخدم
            $tasksCount = $SpecialRequest->tasks->where('assigned_to', $partner->id)->count();
            @endphp

            <div
                class="flex flex-col bg-gray-50 dark:bg-gray-700 p-4 rounded-xl border border-gray-100 dark:border-gray-600 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">
                                {{ $partner->name }}
                                @if($canSeeDetails)
                                <span
                                    class="text-sm font-black text-green-600 dark:text-green-400 ltr:ml-3 rtl:mr-3 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded">
                                    {{ $partner->pivot->profit_share_percentage }}%
                                </span>
                                @endif
                            </p>
                            <p class="text-[10px] text-gray-400 mt-1">
                                تم الإسناد: {{ $partner->pivot->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    {{-- قسم الإحصائيات --}}
                    <div
                        class="grid grid-cols-2 md:grid-cols-5 gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        {{-- المهام --}}
                        <h6>إحصائيات الشريك</h6>
                        <div class="bg-white dark:bg-gray-800 p-2 rounded-lg text-center shadow-sm">
                            <p class="text-[20px] text-gray-400 uppercase">المهام</p>
                            <p class="font-bold text-gray-700 dark:text-gray-200">{{ $tasksCount }}</p>
                        </div>
                        {{-- حضور --}}
                        <div
                            class="bg-white dark:bg-gray-800 p-2 rounded-lg text-center shadow-sm border-b-2 border-green-500">
                            <p class="text-[20px] text-green-500 uppercase">حضر</p>
                            <p class="font-bold text-gray-700 dark:text-gray-200">{{ $attendedCount }}</p>
                        </div>
                        {{-- اعتذر --}}
                        <div
                            class="bg-white dark:bg-gray-800 p-2 rounded-lg text-center shadow-sm border-b-2 border-orange-500">
                            <p class="text-[20px] text-orange-500 uppercase">اعتذر</p>
                            <p class="font-bold text-gray-700 dark:text-gray-200">{{ $declinedCount }}</p>
                        </div>
                        {{-- غاب --}}
                        <div
                            class="bg-white dark:bg-gray-800 p-2 rounded-lg text-center shadow-sm border-b-2 border-red-500">
                            <p class="text-[20px] text-red-500 uppercase">غاب</p>
                            <p class="font-bold text-gray-700 dark:text-gray-200">{{ $absentCount }}</p>
                        </div>
                    </div>
                    @if(auth()->user()->role === 'admin')
                    <form action="{{ route('dashboard.special-request.remove-partner', [$SpecialRequest, $partner]) }}"
                        method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                            <i class="fas fa-user-times"></i> الغاء الاسناد
                        </button>
                    </form>
                    @endif
                </div>



                @if ($partner->pivot->notes && $canSeeDetails)
                <div
                    class="mt-3 text-xs bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded border-r-2 border-yellow-400 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-sticky-note ml-1 text-yellow-600"></i> {{ $partner->pivot->notes }}
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
    {{-- أضفنا class "percentage-input" للتلاعب بها عبر JS --}}
    <input type="number" id="percentage_{{ $partner->id }}" name="profit_share_percentage[{{ $partner->id }}]" min="1"
        max="100" placeholder="%" value="{{ old('profit_share_percentage.' . $partner->id) }}" {{-- هذه الإضافة حاسمة:
        إذا لم يكن الـ checkbox مختاراً، نعطل الحقل --}} {{ !old('partner_id') || !in_array($partner->id,
    old('partner_id')) ? 'disabled' : '' }}
    class="percentage-input w-20 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900
    dark:text-white text-sm rounded-lg p-1.5"
    oninput="validateTotal()">
    <span class="text-gray-600 dark:text-gray-300">%</span>
</div>
<script>
    // أضف هذا الكود في نهاية ملف show.blade.php أو في قسم الـ scripts
    document.querySelectorAll('.partner-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
    const partnerId = this.value;
    const percentageInput = document.getElementById('percentage_' + partnerId);
    
    if (this.checked) {
    percentageInput.disabled = false;
    percentageInput.required = true;
    } else {
    percentageInput.disabled = true;
    percentageInput.value = '';
    percentageInput.required = false;
    }
    validateTotal(); // لتحديث المجموع فوراً
    });
    });
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

    @if($SpecialRequest->is_project == 0)
    <form action="{{ route('dashboard.project_manager.store', $SpecialRequest) }}" method="POST"
        class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        @csrf
        <h3 class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            تحديد مدير المشروع
        </h3>
        @forelse($managers as $partner)
        <div class="flex items-center justify-between p-2 border-b last:border-b-0">
            <div class="flex items-center gap-3">
                <input type="radio" id="manager_{{ $partner->id }}" name="user_id" value="{{ $partner->id }}" {{--
                    التحقق من القيمة القديمة أو القيمة المسجلة حالياً في قاعدة البيانات --}} {{
                    (old('user_id')==$partner->id ||
                (isset($SpecialRequest->projectManager) && $SpecialRequest->projectManager->user_id == $partner->id)) ?
                'checked' : '' }}
                class="manager-checkbox rounded-full text-blue-600 focus:ring-blue-500"
                onchange="validateTotal()">

                <label for="manager_{{ $partner->id }}" class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $partner->name }}
                </label>
            </div>
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
        </div>
        @empty
        <p class="text-center text-gray-500 dark:text-gray-400">لا يوجد شركاء متاحون للإسناد.</p>
        @endforelse
        <button type="submit" id="submitBtn"
            class="px-6 mt-3 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-user-check"></i>
            تحديد مدير المشروع
        </button>
    </form>
    @endif
</div>
