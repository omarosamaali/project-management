@props(['SpecialRequest', 'partners', 'managers'])

<div class="pt-6 p-6 space-y-8">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <i class="fas fa-tools text-blue-600"></i>
        تعيين فريق العمل
    </h2>

    {{-- قسم الشركاء المسندين --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-users-cog ml-2 text-blue-500"></i>
            الشركاء المُسندين للمشروع
        </h3>

        @php
        // سحب الشركاء مع بيانات الـ Pivot
        $assignedPartners = $SpecialRequest->partners;
        $currentTotalShare = $assignedPartners->sum('pivot.profit_share_percentage');
        @endphp

        @if ($assignedPartners->count() > 0)
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-300">مجموع النسب المسندة:</span>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $currentTotalShare }}%</div>
                </div>
                <div class="text-left">
                    <span class="text-xs text-gray-500 block">المتبقي:</span>
                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ 100 - $currentTotalShare }}%</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @foreach ($assignedPartners as $partner)
            <div
                class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600 relative overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-user text-blue-500"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $partner->name }}</h4>
                            <span class="text-[10px] text-gray-500 italic">
                                <i class="far fa-clock ml-1"></i>تم الإسناد: {{
                                $partner->pivot->created_at->diffForHumans()
                                }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-center bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border">
                            <span class="block text-[10px] text-gray-400 uppercase">النسبة</span>
                            <span class="font-black text-green-600 dark:text-green-400">{{
                                $partner->pivot->profit_share_percentage }}%</span>
                        </div>

                        @if(auth()->user()->role === 'admin')
                        <form
                            action="{{ route('dashboard.special-request.remove-partner', [$SpecialRequest->id, $partner->id]) }}"
                            method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء إسناد هذا الشريك؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                @if ($partner->pivot->notes)
                <div
                    class="mt-3 text-xs bg-yellow-50 dark:bg-yellow-900/10 p-2 rounded text-gray-600 dark:text-gray-400 border-r-2 border-yellow-400">
                    <strong><i class="far fa-comment-alt ml-1"></i> ملاحظة:</strong> {{ $partner->pivot->notes }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-10">
            <div
                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-slash text-gray-400 text-xl"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400 italic">لا يوجد شركاء مضافون حالياً</p>
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

    <form id="assignPartnersForm"
        action="{{ route('dashboard.special-request.request-assign-partners', $SpecialRequest->id) }}" method="POST"
        class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
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
                        <input type="number" id="percentage_{{ $partner->id }}"
                            name="profit_share_percentage[{{ $partner->id }}]" min="1" max="100" placeholder="%"
                            value="{{ old('profit_share_percentage.' . $partner->id) }}" {{-- هذه الإضافة حاسمة: إذا لم
                            يكن الـ checkbox مختاراً، نعطل الحقل --}} {{ !old('partner_id') || !in_array($partner->id,
                        old('partner_id')) ? 'disabled' : '' }}
                        class="percentage-input w-20 bg-gray-50 dark:bg-gray-700 border border-gray-300
                        dark:border-gray-600 text-gray-900
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
    <form action="{{ route('dashboard.project_manager.store', $SpecialRequest->id) }}" method="POST"
        class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        @csrf
        <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
        <input type="hidden" name="request_id" value="{{ $SpecialRequest->id }}">

        @csrf
        <h3 class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            تحديد مدير المشروع
        </h3>
        @forelse($managers as $partner)
        <div class="flex items-center justify-between p-2 border-b last:border-b-0">
            <div class="flex items-center gap-3">
                <input type="radio" id="manager_{{ $partner->id }}" name="user_id" value="{{ $partner->id }}" {{--
                    التحقق من القيمة القديمة أو القيمة المسجلة حالياً في قاعدة البيانات --}} 
                    {{ (old('user_id')==$partner->id ||
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