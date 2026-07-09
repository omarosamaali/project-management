@props(['SpecialRequest'])

@php
    $approvalsTableReady = \Illuminate\Support\Facades\Schema::hasTable('project_approvals');
    $approvals = $approvalsTableReady ? ($SpecialRequest->projectApprovals ?? collect()) : collect();
    $candidates = collect()
        ->merge($SpecialRequest->partners ?? collect())
        ->merge($SpecialRequest->clients ?? collect())
        ->merge(\App\Models\User::where('role', 'admin')->get())
        ->unique('id')
        ->sortBy('name');
@endphp

<div class="p-6 space-y-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center border-b pb-4">
        <h2 class="text-xl font-bold flex items-center gap-2 text-gray-800 dark:text-white">
            <i class="fas fa-stamp text-emerald-500"></i> الاعتمادات
        </h2>
        <button onclick="document.getElementById('addApprovalModal').classList.remove('hidden')"
            class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
            <i class="fas fa-plus ml-1"></i> إضافة مستند للاعتماد
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($approvals as $item)
        <div
            class="group border dark:border-gray-700 p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900 transition-all relative">
            <div class="flex items-start gap-3">
                <div class="p-3 rounded-lg {{ $item->isApproved() ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30' : 'bg-amber-50 text-amber-600 dark:bg-amber-900/30' }}">
                    <i class="fas fa-file-signature text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0 text-right" dir="rtl">
                    <div class="flex items-center gap-2 flex-wrap justify-end mb-1">
                        <h4 class="font-bold text-gray-900 dark:text-white truncate">{{ $item->title }}</h4>
                        @if($item->isApproved())
                        <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">معتمد</span>
                        @else
                        <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold">غير معتمد</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $item->description ?? 'لا يوجد وصف' }}</p>

                    <div class="mt-2 flex flex-col gap-1">
                        <div class="flex items-center gap-1 text-[10px] text-emerald-600 font-bold">
                            <i class="fas fa-user-edit"></i>
                            <span>بواسطة: {{ $item->user->name ?? 'غير معروف' }}</span>
                        </div>

                        <div class="text-[10px] text-gray-600 dark:text-gray-400 mt-1">
                            <span class="font-bold">المطلوب اعتماده من:</span>
                            <ul class="mt-1 space-y-0.5">
                                @foreach($item->approvers as $approver)
                                <li class="flex items-center justify-end gap-1">
                                    @if($approver->pivot->approved_at)
                                    <i class="fas fa-check-circle text-emerald-500"></i>
                                    @else
                                    <i class="far fa-clock text-amber-500"></i>
                                    @endif
                                    <span>{{ $approver->name }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap justify-end mt-2">
                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank"
                                class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-1 rounded font-bold">عرض الملف</a>
                            <span class="text-[10px] text-gray-400">{{ $item->created_at->format('Y/m/d') }}</span>
                        </div>

                        @php
                            $myPivot = $item->approvers->firstWhere('id', auth()->id());
                            $canApprove = $myPivot && !$myPivot->pivot->approved_at && !$item->isApproved();
                            $canManage = (int) $item->user_id === (int) auth()->id() && !$item->isApproved();
                        @endphp
                        @if($canApprove)
                        <form action="{{ route('approvals.approve', $item->id) }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit"
                                class="w-full text-[11px] bg-emerald-600 hover:bg-emerald-700 text-white py-1.5 rounded-lg font-bold">
                                <i class="fas fa-check ml-1"></i> اعتماد
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                @if($canManage)
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                        onclick="openEditApprovalModal({{ $item->id }}, @json($item->title), @json($item->description ?? ''), @json($item->approvers->pluck('id')->values()))"
                        class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('approvals.destroy', $item->id) }}" method="POST"
                        onsubmit="return confirm('حذف مستند الاعتماد؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-black hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @empty
        <p class="text-center col-span-full text-gray-400 py-10">لا توجد مستندات للاعتماد.</p>
        @endforelse
    </div>
</div>

<div id="addApprovalModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-xl text-right max-h-[90vh] overflow-y-auto" dir="rtl">
        <h3 class="text-lg font-bold mb-4">إضافة مستند للاعتماد</h3>
        <form action="{{ route('approvals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="special_request_id" value="{{ $SpecialRequest->id }}">
            <input type="text" name="title" placeholder="عنوان المستند" required
                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:text-white outline-none">
            <textarea name="description" placeholder="تفاصيل إضافية"
                class="w-full p-3 border rounded-xl dark:bg-gray-700 outline-none h-24"></textarea>
            <input type="file" name="file" required class="text-xs w-full">
            <div>
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300 block mb-2">المعتمدون المطلوبون</label>
                @if($candidates->isEmpty())
                <p class="text-xs text-amber-600">عيّن فريق العمل أولاً من تبويب فريق العمل.</p>
                @else
                <div class="max-h-40 overflow-y-auto border rounded-xl p-2 space-y-2 dark:border-gray-600">
                    @foreach($candidates as $person)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="approver_ids[]" value="{{ $person->id }}"
                            class="rounded text-emerald-600">
                        <span>{{ $person->name }}</span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 text-white py-2 rounded-xl font-bold" {{ $candidates->isEmpty() ? 'disabled' : '' }}>إضافة</button>
                <button type="button" onclick="document.getElementById('addApprovalModal').classList.add('hidden')"
                    class="flex-1 bg-gray-100 py-2 rounded-xl font-bold dark:bg-gray-700 dark:text-white">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<div id="editApprovalModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl p-6 shadow-xl text-right max-h-[90vh] overflow-y-auto" dir="rtl">
        <h3 class="text-lg font-bold mb-4 text-emerald-600">تعديل الاعتماد</h3>
        <form id="editApprovalForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <input type="text" name="title" id="edit_approval_title" required
                class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:text-white outline-none">
            <textarea name="description" id="edit_approval_description"
                class="w-full p-3 border rounded-xl dark:bg-gray-700 outline-none h-24"></textarea>
            <div>
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300 block mb-2">المعتمدون المطلوبون</label>
                <div class="max-h-40 overflow-y-auto border rounded-xl p-2 space-y-2 dark:border-gray-600" id="edit_approval_approvers">
                    @foreach($candidates as $person)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="approver_ids[]" value="{{ $person->id }}"
                            class="edit-approver-cb rounded text-emerald-600">
                        <span>{{ $person->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 text-white py-2 rounded-xl font-bold">حفظ</button>
                <button type="button" onclick="document.getElementById('editApprovalModal').classList.add('hidden')"
                    class="flex-1 bg-gray-100 py-2 rounded-xl font-bold dark:bg-gray-700">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditApprovalModal(id, title, description, approverIds) {
        const form = document.getElementById('editApprovalForm');
        form.action = `/project-approvals/${id}`;
        document.getElementById('edit_approval_title').value = title;
        document.getElementById('edit_approval_description').value = description || '';
        document.querySelectorAll('.edit-approver-cb').forEach(cb => {
            cb.checked = approverIds.includes(parseInt(cb.value, 10));
        });
        document.getElementById('editApprovalModal').classList.remove('hidden');
    }
</script>
