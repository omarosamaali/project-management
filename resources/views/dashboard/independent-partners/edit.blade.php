@extends('layouts.app')
@section('title', 'مراجعة وتعديل حالة الشريك')
@section('content')

<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.independent-partners.index') }}" second="الشركاء"
        third="تعديل حالة الشريك" />

    <div class="mx-auto max-w-5xl w-full">
        <div class="bg-white dark:bg-gray-800 shadow-xl border rounded-2xl overflow-hidden">
            {{-- رأس الصفحة --}}
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-user-shield text-blue-600"></i>
                    مراجعة بيانات الشريك: <span class="text-blue-600">{{ $partner->name }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">يمكنك مراجعة البيانات أدناه وتحديث حالة الحساب فقط.</p>
            </div>

            <form method="POST" action="{{ route('dashboard.independent-partners.update', $partner->id) }}"
                class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-6">
                    {{-- البيانات الأساسية (للقراءة فقط) --}}
                    <div class="space-y-4">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 border-r-4 border-blue-500 pr-2">البيانات
                            الأساسية</h3>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">الاسم الكامل</label>
                            <input type="text" value="{{ $partner->name }}" readonly
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-600 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">البريد الإلكتروني</label>
                            <input type="email" value="{{ $partner->email }}" readonly
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-600 cursor-not-allowed">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">رقم الهاتف</label>
                                <input type="text" value="{{ $partner->phone }}" readonly dir="ltr"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-600 text-right">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">الدولة</label>
                                <input type="text" value="{{ $partner->country ?? 'غير محدد' }}" readonly
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">المهارات المسجلة</label>
                            <div class="flex flex-wrap gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                @forelse($partner->skills ?? [] as $skill)
                                <span
                                    class="bg-white px-3 py-1 rounded-lg text-xs shadow-sm border text-blue-700 font-medium">{{
                                    $skill }}</span>
                                @empty
                                <span class="text-gray-400 text-xs italic">لا توجد مهارات</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- مراجعة الهوية (للمساعدة في اتخاذ القرار) --}}
                    <div class="space-y-4">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 border-r-4 border-blue-500 pr-2">وثائق
                            التحقق</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-500">الصورة الشخصية</label>
                                <img src="{{ asset('storage/' . $partner->avatar) }}"
                                    class="w-full h-32 object-cover rounded-xl border shadow-sm"
                                    onerror="this.src='/assets/img/default-avatar.png'">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-500">صورة الهوية</label>
                                <img src="{{ asset('storage/' . $partner->id_card_path) }}"
                                    class="w-full h-32 object-cover rounded-xl border shadow-sm cursor-pointer"
                                    onclick="window.open(this.src)">
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100">
                            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                <i class="fas fa-info-circle ml-1"></i>
                                تأكد من تطابق الاسم الثلاثي مع البيانات الواردة في صورة الهوية قبل تغيير الحالة إلى
                                "نشط".
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="dark:border-gray-700">

                {{-- التحكم في الحالة (الحقل الوحيد القابل للتعديل) --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-user-tag text-blue-600"></i>
                        تحديث حالة الحساب
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach([
                        'pending' => ['label' => 'بانتظار الموافقة', 'color' => 'amber'],
                        'active' => ['label' => 'توثيق الحساب', 'color' => 'green'],
                        'inactive' => ['label' => 'تعطيل الحساب', 'color' => 'gray'],
                        'blocked' => ['label' => 'حظر الشريك', 'color' => 'red']
                        ] as $key => $data)
                        <label class="relative flex flex-col items-center p-4 border-2 rounded-2xl cursor-pointer transition-all 
                                {{ $partner->status == $key 
                                    ? " border-{$data['color']}-500 bg-{$data['color']}-50 shadow-md ring-2
                            ring-{$data['color']}-200"
                            : "border-gray-100 bg-white hover:border-blue-300 hover:bg-blue-50" }}">

                            <input type="radio" name="status" value="{{ $key }}" {{ $partner->status == $key ? 'checked'
                            : '' }} class="absolute top-3 right-3 w-4 h-4 text-blue-600">

                            <i class="fas {{ $key == 'active' ? 'fa-check-circle' : ($key == 'blocked' ? 'fa-ban' : ($key == 'pending' ? 'fa-clock' : 'fa-times-circle')) }} 
                                    text-2xl mb-2 {{ $partner->status == $key ? " text-{$data['color']}-600"
                                : "text-gray-400" }}"></i>

                            <span class="text-sm font-bold {{ $partner->status == $key ? " text-{$data['color']}-800"
                                : "text-gray-600" }}">
                                {{ $data['label'] }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- أزرار الإجراءات --}}
                <div class="pt-6 flex flex-col md:flex-row gap-3">
                    <button type="submit"
                        class="flex-1 py-4 px-6 rounded-xl text-white bg-blue-600 hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 transition-all flex justify-center items-center gap-2">
                        <i class="fas fa-save"></i>
                        اعتماد الحالة الجديدة
                    </button>
                    <a href="{{ route('dashboard.independent-partners.index') }}"
                        class="py-4 px-6 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 font-bold text-center transition-all">
                        إلغاء والعودة
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection