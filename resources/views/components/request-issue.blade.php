@props(['SpecialRequest'])

<div class="p-6 space-y-8 bg-gray-50/50 dark:bg-gray-900/20 min-h-screen">

    {{-- الرأس --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-black"></i>
                </span>
                الأخطاء والمعوقات
            </h2>
        </div>
        <button onclick="openAddIssueModal()"
            class="bg-black hover:bg-red-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 shadow-lg transition-all font-bold">
            <i class="fas fa-plus-circle"></i> تسجيل خطأ جديد
        </button>
    </div>

    {{-- فريق العمل المتاح --}}
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-xs font-bold text-gray-400 uppercase mb-4 flex items-center gap-2">
            <i class="fas fa-users-cog"></i> الأشخاص المتاحون للمعالجة
        </h3>
        <div class="flex flex-wrap gap-3 text-sm">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-bold">
                العميل: {{ $SpecialRequest->user?->name ?? 'غير معروف' }}
            </span>
            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-bold">مدير النظام</span>
            @foreach ($SpecialRequest->partners as $partner)
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full font-bold">{{ $partner->name }}</span>
            @endforeach
        </div>
    </div>
@php
$requestId = $SpecialRequest->id ?? null;
$allIssues = \App\Models\Issue::where('request_id', $requestId)
->orderBy('created_at', 'desc')
->get();

// تصنيف المشاكل
$unresolvedIssues = $allIssues->where('status', '!=', 'resolved');
$resolvedIssues = $allIssues->where('status', 'resolved');
@endphp

{{-- 1. المشاكل غير المحلولة (قيد المعالجة) --}}
@if($unresolvedIssues->count() > 0)
<div class="space-y-4">
    <h3 class="text-lg font-bold text-black flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i> المشاكل قيد المعالجة ({{ $unresolvedIssues->count() }})
    </h3>

    @foreach($unresolvedIssues as $issue)
    @php
    // أ. تجهيز بيانات المعنيين
    $assignedData = $issue->assigned_users;
    $assignedIds = is_array($assignedData) ? $assignedData : (is_string($assignedData) ? json_decode($assignedData,
    true) ?: [] : []);

    // ب. جلب موديل المستخدمين المعنيين (للعرض)
    $assignedUsers = !empty($assignedIds) ? \App\Models\User::whereIn('id', $assignedIds)->get() : collect();

    // ج. فحص الصلاحية (عمر أسامة لن يراها إلا إذا كان معنياً)
    $isAuthorized = (Auth::user()->role === 'admin') ||
    ($issue->user_id == Auth::id()) ||
    (in_array(Auth::id(), $assignedIds));
    @endphp

    @if($isAuthorized)
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 border-red-200 dark:border-red-800 overflow-hidden">
        {{-- رأس المشكلة --}}
        <div class="p-6 border-b dark:border-gray-700">
            <div class="flex flex-wrap justify-between gap-4 mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $issue->title }}</h3>
                        <span
                            class="px-2 py-1 rounded-lg text-[10px] font-black uppercase {{ $issue->status == 'discussing' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700' }}">
                            {{ $issue->status == 'discussing' ? 'قيد المناقشة' : 'جديد' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                        <span><i class="fas fa-user-circle"></i> {{ $issue->user->name }}</span>
                        <span><i class="fas fa-clock"></i> {{ $issue->created_at->diffForHumans() }}</span>
                        <span class="flex items-center gap-1 flex-wrap">
                            <i class="fas fa-hand-point-left text-blue-500"></i> المعنيين:
                            @forelse($assignedUsers as $user)
                            <b
                                class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded text-[11px] border border-blue-100 dark:border-blue-800">
                                {{ $user->name }}
                            </b>
                            @empty
                            <span class="text-gray-400">لم يتم تعيين أحد</span>
                            @endforelse
                        </span>
                    </div>
                </div>

                {{-- أزرار التحكم --}}
                <div class="flex items-center gap-2">
                    <button onclick="toggleComments({{ $issue->id }})"
                        class="bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-lg text-xs font-bold transition-colors">
                        <i class="fas fa-comment-dots"></i>
                    </button>
                    @if(Auth::user()->role === 'admin')
                    <button onclick="openEditIssueModal({{ $issue->id }})"
                        class="bg-blue-100 text-blue-600 p-2 rounded-lg text-xs hover:bg-blue-200 transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('issues.destroy', $issue->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('هل أنت متأكد؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-100 text-black p-2 rounded-lg text-xs hover:bg-red-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <p
                class="text-gray-600 dark:text-gray-300 text-sm bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border dark:border-gray-700">
                {{ $issue->description }}
            </p>

            @if($issue->image)
            <img src="{{ asset('storage/'.$issue->image) }}" class="mt-4 rounded-lg max-h-48 border cursor-pointer"
                onclick="window.open(this.src)">
            @endif
        </div>
        {{-- ... (قسم التعليقات يتبع نفس المنطق) ... --}}
    </div>
    @endif
    @endforeach
</div>
@endif

{{-- 2. المشاكل المحلولة (تحت) --}}
@if($resolvedIssues->count() > 0)
<div class="space-y-6 mt-12">
    <h3 class="text-lg font-bold text-green-600 flex items-center gap-2 border-b-2 border-green-100 pb-2">
        <i class="fas fa-check-double"></i> المشاكل التي تم حلها بنجاح ({{ $resolvedIssues->count() }})
    </h3>

    @foreach($resolvedIssues as $issue)
    @php
    // فحص الصلاحية هنا أيضاً لضمان الخصوصية حتى بعد الحل
    $assignedData = $issue->assigned_users;
    $assignedIds = is_array($assignedData) ? $assignedData : (is_string($assignedData) ? json_decode($assignedData,
    true) ?: [] : []);
    $isAuthorized = (Auth::user()->role === 'admin') || ($issue->user_id == Auth::id()) || (in_array(Auth::id(),
    $assignedIds));
    @endphp

    @if($isAuthorized)
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5 bg-gray-50/50 dark:bg-gray-900/20 border-b dark:border-gray-700">
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-bold text-gray-800 dark:text-white">{{ $issue->title }}</h4>
                <span class="text-[10px] text-gray-400 italic">حُلت {{ $issue->updated_at->diffForHumans() }}</span>
            </div>
            <p class="text-xs text-gray-500 line-clamp-2">{{ $issue->description }}</p>
        </div>

        <div class="p-5">
            @php $solution = $issue->comments->where('is_solution', true)->first(); @endphp
            @if($solution)
            <div
                class="bg-green-50 dark:bg-green-900/20 p-5 rounded-2xl border-2 border-green-200 dark:border-green-800 relative">
                <div
                    class="absolute -top-3 right-4 bg-green-600 text-white text-[10px] px-3 py-1 rounded-full font-bold">
                    <i class="fas fa-check-circle"></i> الحل المعتمد
                </div>
                <div class="flex gap-4 items-start mt-2">
                    <div
                        class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                        {{ mb_substr($solution->user->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <span class="text-sm font-bold text-green-900 dark:text-green-400">{{ $solution->user->name
                            }}</span>
                        <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed font-semibold">{{
                            $solution->comment }}</p>
                    </div>
                </div>
            </div>
            @else
            <p class="text-center text-sm text-gray-400 italic">تم إغلاق المشكلة.</p>
            @endif
        </div>
    </div>
    @endif
    @endforeach
</div>
@endif
</div>

{{-- Modal إضافة مشكلة --}}
<div id="addIssueModal"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-xl rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-red-50 dark:bg-red-900/10">
            <h3 class="text-xl font-bold text-black flex items-center gap-2">
                <i class="fas fa-bug"></i> تسجيل مشكلة جديدة
            </h3>
            <button onclick="closeAddIssueModal()" class="text-gray-400 hover:text-black text-2xl">&times;</button>
        </div>
        <form action="{{ route('issues-request.store') }}" method="POST" enctype="multipart/form-data"
            class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="request_id" value="{{ $SpecialRequest->id }}">
            <div>
                <label class="block text-sm font-bold mb-1">عنوان المشكلة</label>
                <input type="text" name="title" required
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">المعنيين بالمعالجة (Ctrl للاختيار المتعدد)</label>
                <select name="assigned_users[]" multiple required
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:text-white h-32 focus:ring-2 focus:ring-blue-500">
                    {{-- الجزء المتسبب في الخطأ داخل الـ Modal --}}
                    <option value="{{ $SpecialRequest->user?->id }}">
                        العميل: {{ $SpecialRequest->user->name ?? $SpecialRequest->client->name }}
                    </option>
                    <option value="1">مدير النظام</option>
                    @foreach ($SpecialRequest->partners as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">التفاصيل وصورة (اختياري)</label>
                <textarea name="description" rows="3" required
                    class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:text-white mb-2"></textarea>
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-2 bg-black text-white py-3 px-8 rounded-xl font-bold shadow-lg shadow-red-200 hover:bg-red-700 transition-all">تسجيل
                    المشكلة</button>
                <button type="button" onclick="closeAddIssueModal()"
                    class="flex-1 bg-gray-100 dark:bg-gray-700 py-3 rounded-xl font-bold dark:text-white">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleComments(issueId) {
        const element = document.getElementById(`comments-${issueId}`);
        element.classList.toggle('hidden');
    }

    function openAddIssueModal() {
        document.getElementById('addIssueModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeAddIssueModal() {
        document.getElementById('addIssueModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function openEditIssueModal(issueId) {
        document.getElementById(`editIssueModal-${issueId}`).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeEditIssueModal(issueId) {
        document.getElementById(`editIssueModal-${issueId}`).style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
</script>