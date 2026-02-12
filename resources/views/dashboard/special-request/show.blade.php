@extends('layouts.app')

@section('title', 'عرض تفاصيل الطلب الخاص')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.special-request.index') }}" second="طلباتى خاصة"
        third="عرض الطلب الخاص" />
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500 text-white p-4 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif
    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-2xl border rounded-xl overflow-hidden">

            <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-blue-50 dark:from-gray-700 dark:to-gray-800">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                            <i class="fas fa-file-alt text-blue-600"></i>
                            {{ $SpecialRequest->title }} - @if($SpecialRequest->status == 'active')
                            جديد
                            @elseif($SpecialRequest->status == 'in_progress')
                            جاري العمل به
                            @elseif($SpecialRequest->status == 'pending')
                            قيد المراجعة
                            @elseif($SpecialRequest->status == 'completed')
                            مكتمل
                            @else
                            {{ $SpecialRequest->status }}
                            @endif
                        </h1>
                        <span class="text-[13px] text-[#646464] font-bold">{{ $SpecialRequest->project_type }}</span>
                    </div>
                    <div class="flex gap-2">

                        {{-- الزر --}}
                        <div class="flex gap-2">
                            {{-- زر الأدمن: تحويل أو تسليم --}}
                            @if(auth()->user()->role === 'admin')
                            <button onclick="openProjectStatusModal()"
                                class="px-4 py-2 {{ $SpecialRequest->is_project ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg transition flex items-center gap-2">
                                <i
                                    class="fas {{ $SpecialRequest->is_project ? 'fa-check-circle' : 'fa-exchange-alt' }}"></i>
                                {{ $SpecialRequest->is_project ? 'مشروع نشط' : 'تحويل إلى مشروع' }}
                            </button>

                            {{-- زر تسليم المشروع للأدمن (يظهر فقط إذا كان مشروع ونشط ولم يكتمل بعد) --}}
                            @if($SpecialRequest->status !== 'completed')
                            <button
                                onclick="confirmDelivery('{{ route('dashboard.special-requests.deliver', $SpecialRequest->id) }}')"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition flex items-center gap-2">
                                <i class="fas fa-truck-loading"></i>
                                تسليم المشروع للعميل
                            </button>
                            @endif
                            @endif

                            @if(auth()->user()->role === 'client' && $SpecialRequest->status === 'in_review')
                            <button
                                onclick="confirmReceipt('{{ route('dashboard.special-requests.receive', $SpecialRequest->id) }}')"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all flex items-center gap-2 shadow-md">
                                <i class="fas fa-check-double"></i>
                                تأكيد جودة العمل والاستلام النهائي
                            </button>
                            {{--
                        </div> --}}
                        @endif
                    </div>

                    {{-- SweetAlert Scripts --}}
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        // وظيفة تسليم الأدمن
    function confirmDelivery(url) {
        Swal.fire({
            title: 'هل أنت متأكد من تسليم المشروع؟',
            text: "برجاء التأكد من ان كل شئ تمام ويعمل بدون مشاكل قبل التسليم للعميل.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed', // Purple
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، قم بالتسليم',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }

    // وظيفة استلام العميل
    function confirmReceipt(url) {
        Swal.fire({
            title: 'تأكيد استلام المشروع',
            text: "هل قمت بفحص المشروع وتأكدت من جودة العمل؟",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316', // Orange
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، استلمت',
            cancelButtonText: 'ليس بعد'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
                    </script>


                    {{-- Modal --}}
                    <div id="projectStatusModal"
                        class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
                            <div
                                class="p-6 border-b dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-exchange-alt text-blue-600"></i>
                                    حالة المشروع
                                </h3>
                            </div>

                            <form
                                action="{{ route('dashboard.special-request.update-project-status', $SpecialRequest) }}"
                                method="POST" class="p-6 space-y-4">
                                @csrf

                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fas fa-cog ml-1"></i>
                                        تحديد حالة الطلب
                                    </label>

                                    {{-- خيار: تحويل لمشروع --}}
                                    <label
                                        class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition {{ $SpecialRequest->is_project ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-green-300' }}">
                                        <input type="radio" name="is_project" value="1" {{ $SpecialRequest->is_project ?
                                        'checked' : '' }}
                                        onchange="toggleBiddingField(true)" class="w-5 h-5 text-green-600">
                                        <div class="mr-3">
                                            <div
                                                class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                                مشروع نشط
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">سيتم تفعيل عروض
                                                الأسعار والمميزات</p>
                                        </div>
                                    </label>

                                    {{-- خيار: طلب عادي --}}
                                    <label
                                        class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition {{ !$SpecialRequest->is_project ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}">
                                        <input type="radio" name="is_project" value="0" {{ !$SpecialRequest->is_project
                                        ? 'checked' : '' }}
                                        onchange="toggleBiddingField(false)" class="w-5 h-5 text-blue-600">
                                        <div class="mr-3">
                                            <div
                                                class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                <i class="fas fa-file-alt text-blue-600"></i>
                                                طلب عادي
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div id="biddingDeadlineField"
                                    class="{{ $SpecialRequest->is_project ? '' : 'hidden' }} space-y-2 mt-4 border-t pt-4 dark:border-gray-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-calendar-alt ml-1 text-indigo-500"></i>
                                        آخر موعد لتقديم عروض الأسعار
                                    </label>
                                    <input type="datetime-local" name="bidding_deadline" id="bidding_input"
                                        value="{{ $SpecialRequest->bidding_deadline ? \Carbon\Carbon::parse($SpecialRequest->bidding_deadline)->format('Y-m-d\TH:i') : '' }}"
                                        class="w-full p-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 outline-none transition">
                                    <p class="text-[10px] text-gray-400">حدد التاريخ والوقت الذي سيتوقف فيه استقبال
                                        العروض لهذا المشروع.</p>
                                </div>

                                <div class="flex gap-3 pt-4">
                                    <button type="submit"
                                        class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">
                                        حفظ التغييرات
                                    </button>
                                    <button type="button" onclick="closeProjectStatusModal()"
                                        class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 rounded-lg font-bold hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                        إلغاء
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function openProjectStatusModal() {
                document.getElementById('projectStatusModal').classList.remove('hidden');
            }
        
            function closeProjectStatusModal() {
                document.getElementById('projectStatusModal').classList.add('hidden');
            }
        
            // وظيفة إظهار/إخفاء حقل التاريخ
            function toggleBiddingField(show) {
                const field = document.getElementById('biddingDeadlineField');
                if (show) {
                    field.classList.remove('hidden');
                    // إضافة تأثير انيميشن بسيط (اختياري)
                    field.classList.add('block');
                } else {
                    field.classList.add('hidden');
                }
            }
        
            // إغلاق بالضغط على الخلفية أو ESC
            document.getElementById('projectStatusModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeProjectStatusModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeProjectStatusModal();
            });
                    </script>
                    <a href="{{ route('dashboard.special-request.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center gap-1">
                        <i class="fas fa-arrow-right"></i>
                        رجوع للقائمة
                    </a>
                </div>
            </div>
        </div>
        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-700 mt-10">
            <nav class="justify-center -mb-px flex flex-wrap gap-1 overflow-x-auto" aria-label="Tabs">
                <button type="button" onclick="openTab(event, 'details')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    التفاصيل
                </button>

                <button type="button" onclick="openTab(event, 'team')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    فريق العمل
                </button>

                <button type="button" onclick="openTab(event, 'stages')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-project-diagram"></i>
                    مراحل المشروع
                </button>

                <button type="button" onclick="openTab(event, 'tasks')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-tasks"></i>
                    المهام
                </button>

                <button type="button" onclick="openTab(event, 'notees')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-sticky-note"></i>
                    الملاحظات
                </button>

                @if(Auth::user()->role != 'partner')
                <button type="button" onclick="openTab(event, 'budget')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave"></i>
                    ميزانية المشروع
                </button>
                @endif

                @if(Auth::user()->role == 'admin')
                <button type="button" onclick="openTab(event, 'expenses')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-receipt"></i>
                    المصاريف
                </button>
                @endif

                <button type="button" onclick="openTab(event, 'issues')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    الأخطاء والمعوقات
                </button>

                <button type="button" onclick="openTab(event, 'files')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-folder-open"></i>
                    ملفات المشروع
                </button>

                <button type="button" onclick="openTab(event, 'meetings')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-video"></i>
                    الاجتماعات
                </button>

                <button type="button" onclick="openTab(event, 'chat')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-comments"></i>
                    النقاشات
                </button>

                
                @if(Auth::user()->role == 'admin' || 'partner' && $SpecialRequest->is_project == 1)
                <button type="button" onclick="openTab(event, 'offerss')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-handshake"></i>
                    عروض الاسعار
                </button>
                @endif

                @if(Auth::user()->role != 'client')
                <button type="button" onclick="openTab(event, 'activities')"
                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-history"></i>
                    سجل الاحداث
                </button>
                @endif
            </nav>
        </div>

        <div id="offerss" class="tab-content hidden">
            <x-project-offers :managers="$managers" :SpecialRequest="$SpecialRequest" :partners="$partners" />
        </div>

        <div class="mt-8">
            {{-- التابة الأولي التفاصيل --}}
            <div id="details" class="tab-content">
                <x-project-details :SpecialRequest="$SpecialRequest" />
            </div>

            {{-- التابة الثانية فريق العمل --}}
            <div id="team" class="tab-content hidden">
                <x-project-team :managers="$managers" :SpecialRequest="$SpecialRequest" :partners="$partners" />
            </div>

            {{-- التابة الثالثة مراحل المشروع --}}
            <div id="stages" class="tab-content hidden">
                <x-project-stages :SpecialRequest="$SpecialRequest" />
            </div>

            {{-- التابة الرابعة المهام --}}
            <div id="tasks" class="tab-content hidden">
                <x-project-tasks :SpecialRequest="$SpecialRequest" />
            </div>

            {{-- التابة الخامسة ملاحظات --}}
            <div id="notees" class="tab-content hidden">
                <x-project-notes :SpecialRequest="$SpecialRequest" />
            </div>

            <!-- ميزانية المشروع -->
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'client')
            <div id="budget" class="tab-content hidden">
                <x-project-budget :SpecialRequest="$SpecialRequest" />
            </div>
            @endif

            <!-- المصاريف -->
            @if(Auth::user()->role === 'admin')
            <div id="expenses" class="tab-content hidden">
                <x-project-expenses :SpecialRequest="$SpecialRequest" />
            </div>
            @endif
            <div id="chat" class="tab-content hidden">
                <x-project-messages :supports="$supports" :SpecialRequest="$SpecialRequest" />
            </div>
            <!-- الأخطاء والمعوقات -->
            <div id="issues" class="tab-content hidden">
                <x-project-issue :SpecialRequest="$SpecialRequest" />
            </div>

            <!-- ملفات المشروع -->
            <div id="files" class="tab-content hidden">
                <x-project-files :SpecialRequest="$SpecialRequest" />
            </div>

            <!-- الأنشطة -->
            <div id="activities" class="tab-content hidden">
                <x-project-activites :SpecialRequest="$SpecialRequest" />
            </div>

            <!-- الاجتماعات -->
            <div id="meetings" class="tab-content hidden">
                <x-project-meetings :SpecialRequest="$SpecialRequest" />
            </div>

            {{-- عروض الأسعار --}}
            {{-- --}}

        </div>

    </div>
    </div>
</section>
<script>
    // 1. تعريف المتغيرات العامة
    let paymentCounter = {{ $SpecialRequest->payments->count() }};
    const requestId = {{ $SpecialRequest->id }};

    // 2. دالة فتح التابات مع حفظ الحالة
    function openTab(event, tabName) {
        // إخفاء كل المحتويات
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));

        // إعادة ضبط شكل كل الأزرار
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-600', 'text-blue-600', 'dark:border-blue-500', 'dark:text-blue-400');
            button.classList.add('border-transparent', 'text-gray-500');
        });

        // إظهار المحتوى المختار
        const target = document.getElementById(tabName);
        if (target) target.classList.remove('hidden');

        // تمييز الزر النشط
        let currentBtn = event ? event.currentTarget : document.querySelector(`button[onclick*="'${tabName}'"]`);
        if (currentBtn) {
            currentBtn.classList.remove('border-transparent', 'text-gray-500');
            currentBtn.classList.add('border-blue-600', 'text-blue-600', 'dark:border-blue-500', 'dark:text-blue-400');
        }

        // حفظ اسم التابة في الذاكرة
        localStorage.setItem('activeTab_' + requestId, tabName);
    }

    // 3. كود التشغيل عند تحميل الصفحة (المنطق الوحيد المطلوب)
    document.addEventListener('DOMContentLoaded', function() {
        // استرجاع التابة المحفوظة
        const savedTab = localStorage.getItem('activeTab_' + requestId);
        
        if (savedTab && document.getElementById(savedTab)) {
            openTab(null, savedTab);
        } else {
            // افتح أول تابة لو مفيش حاجة محفوظة
            const firstTab = document.querySelector('.tab-button');
            if (firstTab) firstTab.click();
        }

        // تشغيل دوال الحسابات والدفع
        if(document.getElementById('payment_type')) {
            togglePayments();
            calculatePaymentsTotal();
        }
    });

    // 4. وظائف الدفع والحسابات (بدون تكرار)
    function togglePayments() {
        const type = document.getElementById('payment_type')?.value;
        const section = document.getElementById('installments_section');
        if (section) {
            type === 'installments' ? section.classList.remove('hidden') : section.classList.add('hidden');
        }
        calculatePaymentsTotal();
    }

    function addPaymentRow() {
        const wrapper = document.getElementById('payments_wrapper');
        const index = paymentCounter++;
        const html = `
            <div class="payment-row flex gap-2 items-start bg-white dark:bg-gray-700 p-3 rounded-lg border">
                <div class="flex-1">
                    <input type="text" name="installments[${index}][name]" placeholder="اسم الدفعة" class="w-full p-2 border rounded text-sm dark:bg-gray-800 dark:text-white" required>
                </div>
                <div class="w-32">
                    <input type="number" name="installments[${index}][amount]" class="payment-amount w-full p-2 border rounded text-sm dark:bg-gray-800 dark:text-white" step="0.01" required oninput="calculatePaymentsTotal()">
                </div>
                <button type="button" onclick="this.closest('.payment-row').remove(); calculatePaymentsTotal();" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-black transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`;
        wrapper.insertAdjacentHTML('beforeend', html);
        calculatePaymentsTotal();
    }

    function calculatePaymentsTotal() {
        const priceInput = document.getElementById('price');
        if(!priceInput) return;
        
        const budget = parseFloat(priceInput.value) || 0;
        let total = 0;
        document.querySelectorAll('.payment-amount').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        // تحديث الواجهة (Progress bar & totals)
        if(document.getElementById('installments_total')) {
            document.getElementById('installments_total').textContent = total.toFixed(2);
            document.getElementById('project_budget_display').textContent = budget.toFixed(2);
            const progress = budget > 0 ? (total / budget) * 100 : 0;
            const progressBar = document.getElementById('payment_progress');
            if(progressBar) progressBar.style.width = Math.min(progress, 100) + '%';
        }
    }

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
            document.body.style.overflow = modal.classList.contains('hidden') ? 'auto' : 'hidden';
        }
    }
</script>
@endsection