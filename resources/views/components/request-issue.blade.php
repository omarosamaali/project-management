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

    {{-- فصل المشاكل المحلولة وغير المحلولة --}}
    @php
    $unresolvedIssues = $SpecialRequest->issues->where('status', '!=', 'resolved')->sortByDesc('created_at');
    $resolvedIssues = $SpecialRequest->issues->where('status', 'resolved')->sortByDesc('created_at');
    @endphp

    {{-- المشاكل غير المحلولة (فوق) --}}
    @if($unresolvedIssues->count() > 0)
    <div class="space-y-4">
        <h3 class="text-lg font-bold text-black flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> المشاكل قيد المعالجة ({{ $unresolvedIssues->count() }})
        </h3>

        @foreach($unresolvedIssues as $issue)
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
                                @php
                                $assignedData = $issue->assigned_users;
                                $assignedIds = is_array($assignedData) ? $assignedData : (is_string($assignedData) ?
                                json_decode($assignedData, true) ?: [] : []);
                                $assignedUsers = !empty($assignedIds) ? \App\Models\User::whereIn('id',
                                $assignedIds)->get() : collect();
                                @endphp
                                @forelse($assignedUsers as $user)
                                <b
                                    class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded text-[11px] border border-blue-100 dark:border-blue-800">
                                    {{ $user->name }}
                                </b>
                                @empty
                                <span class="text-gray-400">لم يتم تعيين أحد</span>
                                @endforelse
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-comments text-purple-500"></i> {{ $issue->comments->count() }} تعليق
                            </span>
                        </div>
                    </div>

                    {{-- أزرار التحكم --}}
                    <div class="flex items-center gap-2">
                        <button onclick="toggleComments({{ $issue->id }})"
                            class="bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-lg text-xs font-bold transition-colors"
                            title="عرض المحادثة">
                            <i class="fas fa-comment-dots"></i>
                        </button>

                        <button onclick="openEditIssueModal({{ $issue->id }})"
                            class="bg-blue-100 text-blue-600 p-2 rounded-lg text-xs hover:bg-blue-200 transition-colors"
                            title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('issues.destroy', $issue->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('هل أنت متأكد من حذف هذه المشكلة نهائياً؟');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="bg-red-100 text-black p-2 rounded-lg text-xs hover:bg-red-200 transition-colors"
                                title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <p
                    class="text-gray-600 dark:text-gray-300 text-sm bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border dark:border-gray-700">
                    {{ $issue->description }}
                </p>

                @if($issue->image)
                <img src="{{ asset('storage/'.$issue->image) }}"
                    class="mt-4 rounded-lg max-h-48 border cursor-pointer hover:opacity-90 transition-opacity"
                    onclick="window.open(this.src)">
                @endif
            </div>

            {{-- قسم النقاشات --}}
            <div id="comments-{{ $issue->id }}" class="hidden bg-gray-50 dark:bg-gray-900/30">
                <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                    @php
                    $solution = $issue->comments->where('is_solution', true)->first();
                    $otherComments = $issue->comments->where('is_solution', false)->sortByDesc('created_at');
                    $allComments = collect();
                    if($solution) $allComments->push($solution);
                    foreach($otherComments as $c) $allComments->push($c);
                    @endphp

                    @forelse($allComments as $comment)
                    <div class="flex gap-3 {{ $comment->user_id == auth()->id() ? 'flex-row-reverse' : '' }}">
                        <div class="flex-1 {{ $comment->user_id == auth()->id() ? 'text-right' : '' }}">
                            <div
                                class="flex items-center gap-2 mb-1 {{ $comment->user_id == auth()->id() ? 'justify-end' : '' }}">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $comment->user->name
                                    }}</span>
                                @if($comment->is_solution)
                                <span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[10px] font-black flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i> تم الحل
                                </span>
                                @endif
                            </div>

                            <div
                                class="bg-white dark:bg-gray-800 p-3 rounded-xl shadow-sm border {{ $comment->is_solution ? 'border-2 border-green-500 ring-2 ring-green-100' : 'dark:border-gray-700' }} inline-block max-w-full">
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
                            </div>

                            <div
                                class="flex gap-2 mt-2 text-xs {{ $comment->user_id == auth()->id() ? 'justify-end' : '' }}">
                                @if($issue->status != 'resolved')
                                <form action="{{ route('issue-comments.mark-solution', [$issue->id, $comment->id]) }}"
                                    method="POST">
                                    @csrf
                                    <button
                                        class="text-green-600 hover:text-green-700 font-bold flex items-center gap-1">
                                        <i class="fas fa-check"></i> اختيار كحل
                                    </button>
                                </form>
                                @endif

                                @if($comment->user_id == auth()->id() && !$comment->is_solution)
                                <form action="{{ route('issue-comments.destroy', $comment->id) }}" method="POST"
                                    onsubmit="return confirm('حذف التعليق؟')">
                                    @csrf @method('DELETE')
                                    <button class="text-black hover:text-red-700">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-400">لا توجد تعليقات</p>
                    @endforelse
                </div>

                {{-- نموذج إضافة تعليق --}}
                <div class="p-4 border-t dark:border-gray-700 bg-white dark:bg-gray-800">
                    <form action="{{ route('issue-comments.store', $issue->id) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="flex gap-2">
                            <textarea name="comment" rows="2" required placeholder="اكتب تعليقك هنا..."
                                class="flex-1 p-3 rounded-xl border dark:bg-gray-700 dark:text-white text-sm resize-none focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <label
                                class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer hover:text-purple-600">
                                <i class="fas fa-image"></i>
                                <span>إرفاق صورة</span>
                                <input type="file" name="image" accept="image/*" class="hidden">
                            </label>
                            <button type="submit"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-xl font-bold text-sm flex items-center gap-2 transition-colors">
                                <i class="fas fa-paper-plane"></i> إرسال
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- مودال التعديل --}}
            <div id="editIssueModal-{{ $issue->id }}"
                class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div class="bg-white dark:bg-gray-800 w-full max-w-xl rounded-3xl shadow-2xl overflow-hidden">
                    <div
                        class="p-6 border-b dark:border-gray-700 flex justify-between items-center bg-blue-50 dark:bg-blue-900/10">
                        <h3 class="text-xl font-bold text-blue-600 flex items-center gap-2">
                            <i class="fas fa-edit"></i> تعديل المشكلة
                        </h3>
                        <button onclick="closeEditIssueModal({{ $issue->id }})"
                            class="text-gray-400 hover:text-black text-2xl">&times;</button>
                    </div>
                    <form action="{{ route('issues.update', $issue->id) }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-4 text-right" dir="rtl">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-white">عنوان المشكلة</label>
                            <input type="text" name="title" value="{{ $issue->title }}" required
                                class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-white">المعنيين بالمعالجة</label>
                            <select name="assigned_users[]" multiple required
                                class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:text-white h-32 focus:ring-2 focus:ring-blue-500">
                                <option value="{{ $SpecialRequest?->user->id }}" {{ in_array($SpecialRequest?->user->id,
                                    $assignedIds) ? 'selected' : '' }}>
                                    العميل: {{ $SpecialRequest?->user->name }}
                                </option>
                                <option value="1" {{ in_array(1, $assignedIds) ? 'selected' : '' }}>مدير النظام</option>
                                @foreach ($SpecialRequest->partners as $partner)
                                <option value="{{ $partner->id }}" {{ in_array($partner->id, $assignedIds) ? 'selected'
                                    : '' }}>
                                    {{ $partner->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 dark:text-white">الوصف</label>
                            <textarea name="description" rows="3" required
                                class="w-full p-3 rounded-xl border dark:bg-gray-700 dark:text-white">{{ $issue->description }}</textarea>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                class="flex-2 bg-blue-600 text-white py-3 px-8 rounded-xl font-bold shadow-lg shadow-blue-200">
                                حفظ التعديلات
                            </button>
                            <button type="button" onclick="closeEditIssueModal({{ $issue->id }})"
                                class="flex-1 bg-gray-100 dark:bg-gray-700 py-3 rounded-xl font-bold dark:text-white">
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- المشاكل المحلولة (تحت) --}}
    @if($resolvedIssues->count() > 0)
    <div class="space-y-6 mt-12">
        <h3 class="text-lg font-bold text-green-600 flex items-center gap-2 border-b-2 border-green-100 pb-2">
            <i class="fas fa-check-double"></i> المشاكل التي تم حلها بنجاح ({{ $resolvedIssues->count() }})
        </h3>

        @foreach($resolvedIssues as $issue)
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
                        class="absolute -top-3 right-4 bg-green-600 text-white text-[10px] px-3 py-1 rounded-full font-bold shadow-sm">
                        <i class="fas fa-check-circle"></i> الحل المعتمد
                    </div>
                    <div class="flex gap-4 items-start mt-2">
                        <div
                            class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold shrink-0 shadow-md">
                            {{ mb_substr($solution->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-bold text-green-900 dark:text-green-400">{{
                                    $solution->user->name }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed font-semibold">{{
                                $solution->comment }}</p>
                            @if($solution->image)
                            <div class="mt-4">
                                <img src="{{ asset('storage/'.$solution->image) }}"
                                    class="rounded-xl max-h-60 border-2 border-white dark:border-gray-700 shadow-sm hover:scale-[1.02] transition-transform cursor-pointer"
                                    onclick="window.open(this.src)">
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <p class="text-center text-sm text-gray-400 italic">تم إغلاق المشكلة كـ "محلوة" بدون تحديد تعليق معين
                    كحل.</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- رسالة فارغة --}}
    @if($SpecialRequest->issues->count() == 0)
    <div
        class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700 mt-8">
        <div class="bg-gray-100 dark:bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-gray-500 dark:text-gray-400 font-bold">لا توجد أي معوقات أو أخطاء مسجلة حالياً</h3>
        <p class="text-sm text-gray-400 mt-1">النظام يعمل بشكل مستقر.</p>
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
        <form action="{{ route('issues.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
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
    العميل: {{ $SpecialRequest->user?->name ?? 'غير متوفر' }}
</option>                    <option value="1">مدير النظام</option>
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