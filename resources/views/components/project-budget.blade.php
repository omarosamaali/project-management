@props(['SpecialRequest'])
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-green-600"></i> ميزانية المشروع
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة ميزانية المشروع والدفعات</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <button onclick="openEditBudgetModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all">
            <i class="fas fa-edit"></i> تعديل الميزانية
        </button>
        @endif
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
            class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-6 rounded-xl border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">إجمالي الميزانية</p>
                    <p class="text-3xl font-bold text-blue-700 dark:text-blue-300 mt-2 flex items-center gap-1">
                        {{ number_format($SpecialRequest->price, 2) }}
                        <x-drhm-icon color="#1d4ed8" width="18" heigt="18" />
                    </p>
                </div>
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-6 rounded-xl border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">المبلغ المدفوع</p>
                    <p class="text-3xl font-bold text-green-700 dark:text-blue-300 mt-2 flex items-center gap-1">
                        {{ number_format($SpecialRequest->total_paid, 2) }}
                        <x-drhm-icon color="#15803d" width="18" heigt="18" />
                    </p>
                </div>
                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-6 rounded-xl border border-orange-200 dark:border-orange-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">المبلغ المتبقي</p>
                    <p class="text-3xl font-bold text-orange-700 dark:text-blue-300 mt-2 flex items-center gap-1">
                        {{ number_format($SpecialRequest->remaining_amount, 2) }}
                        <x-drhm-icon color="#c2410c" width="18" heigt="18" />
                    </p>
                </div>
                <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">نسبة الدفع</span>
            <span class="text-lg font-bold text-blue-600">{{ $SpecialRequest->payment_progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-4 rounded-full transition-all duration-500"
                style="width: {{ $SpecialRequest->payment_progress }}%"></div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list"></i> الدفعات ({{ $SpecialRequest->requestPayments->count() }})
            </h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($SpecialRequest->requestPayments as $payment)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $payment->payment_name }}</h4>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold @if($payment->status == 'paid') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 @endif">
                                @if($payment->status == 'paid') مدفوعة @elseif($payment->status == 'pending') قيد
                                المراجعة @else غير مدفوعة @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-money-bill-wave"></i>
                                <strong>{{ number_format($payment->amount, 2) }}</strong>
                                <x-drhm-icon width="12" height="14" />
                            </span>
                            @if($payment->due_date)
                            <span class="flex items-center gap-1">
                                <i class="far fa-calendar-alt"></i> الاستحقاق: {{
                                \Carbon\Carbon::parse($payment->due_date)->format('Y/m/d') }}
                            </span>
                            @endif
                            @if($payment->paid_at)
                            <span class="flex items-center gap-1 text-green-600">
                                <i class="fas fa-check-circle"></i> دُفعت في: {{
                                \Carbon\Carbon::parse($payment->paid_at)->format('Y/m/d') }}
                            </span>
                            @endif
                        </div>
                        @if($payment->notes)
                        <div
                            class="mt-2 p-2 bg-gray-50 dark:bg-gray-900 rounded text-xs text-gray-600 dark:text-gray-400">
                            <i class="fas fa-sticky-note ml-1"></i> {{ $payment->notes }}
                        </div>
                        @endif
                    </div>
                    @if(auth()->user()->role === 'client' && $payment->status !== 'paid')
                    <div class="flex flex-col items-end">
                        @php
                        $baseAmount = $payment->amount;
                        $fees = round(($baseAmount * 0.079) + 2, 2);
                        $totalWithFees = $baseAmount + $fees;
                        @endphp
                        <div class="text-right mb-3">
                            <p class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-1">
                                المبلغ الأساسي: {{ number_format($baseAmount, 2) }}
                                <x-drhm-icon width="12" height="14" />
                            </p>
                            <p class="text-sm text-orange-600 font-medium flex items-center gap-1">
                                + رسوم بوابة الدفع: {{ number_format($fees, 2) }}
                                <x-drhm-icon width="12" height="14" />
                            </p>
                            <p class="text-xl font-bold text-emerald-600 mt-1 flex items-center gap-1">
                                الإجمالي: {{ number_format($totalWithFees, 2) }}
                                <x-drhm-icon width="12" height="14" />
                            </p>
                        </div>
                        <form id="ziina-form-{{ $payment->id }}"
                            action="{{ route('ziina.installment.pay', $payment->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="button" onclick="payWithZiina({{ $payment->id }}, {{ $totalWithFees }})"
                                class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-3 rounded-lg font-bold flex items-center gap-2 shadow-lg transition-all transform hover:scale-105">
                                <i class="fas fa-credit-card"></i> دفع {{ number_format($totalWithFees, 2) }}
                                <x-drhm-icon color="#fffff" width="12" height="14" />
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="flex flex-col items-end">
                        @php
                        $paidPayment = \App\Models\Payment::where('status', 'completed')->latest()->first();
                        @endphp
                        <div class="flex flex-col items-end gap-2">
                    
                            {{-- ✅ زر تحويل الحالة إلى paid --}}
                            @if($payment->status !== 'paid')
                            <form action="{{ route('special-request.payment.mark-paid', ['payment' => $payment->id]) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من تحويل هذه الدفعة إلى مدفوعة؟')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-bold transition-colors flex items-center gap-2 shadow">
                                    <i class="fas fa-check-circle"></i>
                                    تحويل إلى مدفوعة
                                </button>
                            </form>
                            @endif
                    
                            {{-- فاتورة --}}
                            @if($paidPayment)
                            <a href="{{ route('special-request.payment.invoice', [
                                'specialRequest' => $SpecialRequest->id, 
                                'payment' => $paidPayment->id,
                                'installment_id' => $payment->id
                            ]) }}" target="_blank"
                                class="px-4 py-2 bg-emerald-100 dark:bg-emerald-700 text-emerald-700 dark:text-white rounded-lg text-sm font-medium hover:bg-emerald-200 dark:hover:bg-emerald-600 transition-colors flex items-center gap-2">
                                <i class="fas fa-file-invoice"></i>
                                معاينة الفاتورة
                            </a>
                            @else
                            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-lg">لا توجد فاتورة متاحة</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-10 text-center">
                <i class="fas fa-receipt text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-400 dark:text-gray-500">لا توجد دفعات محددة</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<div id="editBudgetModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div
        class="bg-white dark:bg-gray-800 w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
        <div
            class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700 sticky top-0">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> تعديل ميزانية المشروع
            </h3>
            <button onclick="closeEditBudgetModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form action="{{ route('dashboard.special-request.update-budget', $SpecialRequest) }}" method="POST"
            class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-wallet ml-1"></i>
                    ميزانية المشروع</label>
                <input type="number" name="price" id="budget_price" value="{{ $SpecialRequest->price }}" step="0.01"
                    min="0" required
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    oninput="calculateTotal()">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-credit-card ml-1"></i>
                    نوع الدفع</label>
                <select name="payment_type" id="payment_type"
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    onchange="toggleInstallments()">
                    <option value="single" {{ $SpecialRequest->payment_type == 'single' ? 'selected' : '' }}>دفعة واحدة
                    </option>
                    <option value="installments" {{ $SpecialRequest->payment_type == 'installments' ? 'selected' : ''
                        }}>دفعات (تقسيط)</option>
                </select>
            </div>
            <div id="installments_section"
                class="{{ $SpecialRequest->payment_type == 'installments' ? '' : 'hidden' }} space-y-3">
                <div class="flex justify-between items-center">
                    <label class="block text-sm font-medium dark:text-gray-300"><i class="fas fa-list ml-1"></i> تقسيم
                        الدفعات</label>
                    <button type="button" onclick="addInstallment()"
                        class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-plus ml-1"></i> إضافة دفعة
                    </button>
                </div>
                <div id="installments_wrapper" class="space-y-2">
                    @foreach($SpecialRequest->requestPayments as $index => $payment)
                    <div class="installment-row flex gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <input type="text" name="installments[{{ $index }}][name]" value="{{ $payment->payment_name }}"
                            placeholder="اسم الدفعة" required
                            class="flex-1 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
                        <input type="number" name="installments[{{ $index }}][amount]" value="{{ $payment->amount }}"
                            placeholder="المبلغ" step="0.01" min="0" required
                            class="w-32 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm installment-amount"
                            oninput="calculateTotal()">
                        <input type="date" name="installments[{{ $index }}][due_date]"
                            value="{{ $payment->due_date?->format('Y-m-d') }}"
                            class="w-40 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
                        <button type="button" onclick="removeInstallment(this)"
                            class="px-3 py-2 bg-red-500 text-white rounded hover:bg-black"><i
                                class="fas fa-trash"></i></button>
                    </div>
                    @endforeach
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex justify-between text-sm"><span>مجموع الدفعات:</span><span id="total_installments"
                            class="font-bold">0.00</span></div>
                    <div class="flex justify-between text-sm mt-1"><span>الميزانية:</span><span id="budget_display"
                            class="font-bold">0.00</span></div>
                    <div id="difference_msg" class="mt-2 text-xs hidden"></div>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700"><i
                        class="fas fa-save ml-1"></i> حفظ التعديلات</button>
                <button type="button" onclick="closeEditBudgetModal()"
                    class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
<div id="rejectModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 bg-gradient-to-r from-red-50 to-orange-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"><i
                    class="fas fa-times-circle text-black"></i> رفض الدفعة</h3>
        </div>
        <form id="rejectForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">سبب الرفض</label>
                <textarea name="rejection_notes" rows="3" required
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                    placeholder="اكتب سبب رفض إثبات الدفع..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-black text-white py-2.5 rounded-lg font-bold hover:bg-red-700">تأكيد الرفض</button>
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-2.5 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endif

@if(auth()->user()->role === 'client')
<div id="paymentProofModal"
    class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
        <div
            class="p-6 border-b dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"><i
                    class="fas fa-upload text-green-600"></i> رفع إثبات الدفع</h3>
        </div>
        <form id="paymentProofForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-file-upload ml-1"></i>
                    إثبات الدفع (صورة أو PDF)</label>
                <input type="file" name="payment_proof" required accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="text-xs text-gray-500 mt-1">الحد الأقصى: 5 ميجابايت</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300"><i class="fas fa-sticky-note ml-1"></i>
                    ملاحظات (اختياري)</label>
                <textarea name="payment_notes" rows="3"
                    class="w-full p-3 rounded-lg border dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                    placeholder="أي ملاحظات إضافية..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-green-600 text-white py-2.5 rounded-lg font-bold hover:bg-green-700"><i
                        class="fas fa-paper-plane ml-1"></i> إرسال</button>
                <button type="button" onclick="closePaymentProofModal()"
                    class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-2.5 rounded-lg font-bold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    let installmentCounter = {{ $SpecialRequest->requestPayments->count() }};
function openEditBudgetModal() { document.getElementById('editBudgetModal').classList.remove('hidden'); calculateTotal(); }
function closeEditBudgetModal() { document.getElementById('editBudgetModal').classList.add('hidden'); }
function toggleInstallments() {
    const type = document.getElementById('payment_type').value;
    const section = document.getElementById('installments_section');
    type === 'installments' ? section.classList.remove('hidden') : section.classList.add('hidden');
}
function addInstallment() {
    const wrapper = document.getElementById('installments_wrapper');
    const index = installmentCounter++;
    const html = `<div class="installment-row flex gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <input type="text" name="installments[${index}][name]" placeholder="اسم الدفعة" required class="flex-1 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
        <input type="number" name="installments[${index}][amount]" placeholder="المبلغ" step="0.01" min="0" required class="w-32 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm installment-amount" oninput="calculateTotal()">
        <input type="date" name="installments[${index}][due_date]" class="w-40 p-2 rounded border dark:bg-gray-800 dark:text-white text-sm">
        <button type="button" onclick="removeInstallment(this)" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-black"><i class="fas fa-trash"></i></button>
    </div>`;
    wrapper.insertAdjacentHTML('beforeend', html);
    calculateTotal();
}
function removeInstallment(btn) { btn.closest('.installment-row').remove(); calculateTotal(); }
function calculateTotal() {
    const budget = parseFloat(document.getElementById('budget_price').value) || 0;
    let total = 0;
    document.querySelectorAll('.installment-amount').forEach(input => { total += parseFloat(input.value) || 0; });
    document.getElementById('total_installments').textContent = total.toFixed(2);
    document.getElementById('budget_display').textContent = budget.toFixed(2);
    const diff = budget - total;
    const msg = document.getElementById('difference_msg');
    if (diff > 0) {
        msg.classList.remove('hidden', 'text-black'); msg.classList.add('text-yellow-600');
        msg.innerHTML = `<i class="fas fa-exclamation-triangle"></i> المتبقي: ${diff.toFixed(2)} <x-drhm-icon width="12" height="14" />`;
    } else if (diff < 0) {
        msg.classList.remove('hidden', 'text-yellow-600'); msg.classList.add('text-black');
        msg.innerHTML = `<i class="fas fa-times-circle"></i> تجاوز الميزانية بـ ${Math.abs(diff).toFixed(2)} <x-drhm-icon width="12" height="14" />`;
    } else { msg.classList.add('hidden'); }
}
function openPaymentProofModal(paymentId) {
    const form = document.getElementById('paymentProofForm');
    form.action = `/payments/${paymentId}/upload-proof`;
    document.getElementById('paymentProofModal').classList.remove('hidden');
}
function closePaymentProofModal() { document.getElementById('paymentProofModal').classList.add('hidden'); }
function openRejectModal(paymentId) {
    const form = document.getElementById('rejectForm');
    form.action = `/payments/${paymentId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }
function payWithZiina(paymentId, totalAmount) {
    const button = document.querySelector(`#ziina-form-${paymentId} button`);
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحويل...';
    fetch(`/payments/${paymentId}/ziina-pay`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.payment_url) { window.location.href = data.payment_url; } 
        else { alert('خطأ: ' + (data.message || 'فشل إنشاء الدفع')); button.disabled = false; button.innerHTML = `<i class="fas fa-credit-card"></i> دفع ${totalAmount.toFixed(2)}`; }
    })
    .catch(err => { alert('فشل في الاتصال'); button.disabled = false; });
}
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { closeEditBudgetModal(); closePaymentProofModal(); closeRejectModal(); }
});
</script>