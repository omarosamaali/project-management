@extends('layouts.app')

@section('title', 'تفاصيل المشروع: ' . $newProject->title)

@section('content')
<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.new_project.index') }}" second="تفاصيل المشروع" />

    <div class="max-w-5xl mx-auto space-y-6">

        <div
            class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-500 p-6 text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <span
                            class="bg-gray-400 bg-opacity-30 text-xs uppercase px-3 py-1 rounded-full mb-2 inline-block">
                            {{ $newProject->project_type }}
                        </span>
                        <h1 class="text-3xl font-bold">{{ $newProject->title }}</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-white text-gray-700 px-4 py-1.5 rounded-lg font-bold shadow-sm">
                            الحالة: {{ $newProject->status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <div class="lg:col-span-2 space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3 flex items-center">
                                <i class="fas fa-file-alt ml-2 text-gray-500"></i> وصف المشروع
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
                                {{ $newProject->description }}
                            </p>
                        </div>

                        @if($newProject->core_features)
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl border-r-4 border-gray-500">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-300 mb-3">المميزات المطلوبة:</h3>
                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-loose">
                                {{ $newProject->core_features }}
                            </div>
                        </div>
                        @endif

                        @if($newProject->examples)
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">أمثلة مشابهة:</h3>
                            <p class="text-gray-600 dark:text-gray-400 italic underline">
                                {{ $newProject->examples }}
                            </p>
                        </div>
                        @endif
                        <div class="space-y-4">
                            <div
                                class="bg-gray-50 dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-bold text-gray-400 uppercase mb-4 tracking-wider">تفاصيل
                                    والوقت</h3>

                                <div class="space-y-5">
                                    {{-- <div class="flex justify-between items-center">
                                        <span class="text-gray-500 font-medium">الميزانية (Budget)</span>
                                        <span class="text-green-600 font-bold text-lg">{{ $newProject->budget }}</span>
                                    </div> --}}

                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 font-medium">اخر موعد لتقديم عروض الاسعار</span>
                                        <span class="text-black font-bold">{{ $newProject->deadline ?? 'غير محدد'
                                            }}</span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 font-medium">موعد تسليم المشروع</span>
                                        <span class="text-black font-bold">{{ $newProject->deadline ?? 'غير محدد'
                                            }}</span>
                                    </div>

                                    <div class="text-xs text-gray-400 text-left pt-2">
                                        <i class="fas fa-clock ml-1"></i> تاريخ النشر: {{
                                        $newProject->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            @if($newProject->installments_data)
                            <div
                                class="bg-amber-50 dark:bg-amber-900 dark:bg-opacity-20 p-4 rounded-xl border border-amber-100 dark:border-amber-800 text-sm">
                                <h4 class="font-bold text-amber-800 dark:text-amber-400 mb-1"><i
                                        class="fas fa-receipt ml-1"></i> معلومات الأقساط:</h4>
                                <p class="text-amber-700 dark:text-amber-300 italic">{{ $newProject->installments_data
                                    }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @if(Auth::user()->role == 'partner' && Auth::user()->can_propose_quotes == 1 && $newProject->budget_to == null)
        <div
            class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-8">
            @if($errors->any())
            <div class="mx-4 mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                role="alert">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    <span class="font-bold">حدث خطأ أثناء إرسال العرض:</span>
                </div>
                <ul class="list-disc list-inside mr-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @php
            // تحقق إذا فيه عرض مقبول بالفعل
            $proposalAccepted = $newProject->proposals()->where('status', 'accepted')->exists();
            @endphp

            @if(Auth::user()->role == 'partner'
            && Auth::user()->can_propose_quotes == 1 && !$proposalAccepted)
            <div
                class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="h-12 w-12 bg-gray-600 rounded-full flex items-center justify-center text-white text-xl shadow-lg shadow-gray-200">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">هل أنت جاهز للعمل؟</h2>
                        <p class="text-gray-500 italic">قدم عرضاً احترافياً لزيادة فرص قبولك</p>
                    </div>
                </div>

                @if($errors->any())
                <div class="mx-4 mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                    role="alert">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle ml-2"></i>
                        <span class="font-bold">حدث خطأ أثناء إرسال العرض:</span>
                    </div>
                    <ul class="list-disc list-inside mr-5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('dashboard.new_project.store_proposal', $newProject->id) }}" method="POST"
                    class="space-y-6">
                    @csrf <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2"> <label
                                class="text-sm font-bold flex items-center text-gray-700 dark:text-gray-300">
                                قيمة العرض </label>
                            <div class="flex items-center gap-3">
                                <div class="relative flex-1 flex"> <input type="number" name="budget_to"
                                        value="{{ old('budget_to', $myProposal->budget_to ?? '') }}"
                                        class=" {{ $newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to == null ? '!bg-gray-400' : '' }} w-full pr-2 rounded-r-xl border-gray-300 focus:ring-gray-500"
                                        required {{ $newProject->bidding_deadline > \Carbon\Carbon::now() &&
                                    $newProject->budget_to ==
                                    null ? '' : 'disabled' }} > <div
                                        class="bg-gray-300 flex items-center justify-center px-1 rounded-l-xl">
                                        <x-drhm-icon color="black" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2"> <label
                                class="block text-sm font-bold text-gray-700 dark:text-gray-300">مدة
                                التسليم</label>
                            <div class="relative flex-1 flex"> <input type="number" name="execution_time"
                                    value="{{ old('execution_time', $myProposal->execution_time ?? '') }}"
                                    class="w-full pl-10 rounded-r-xl border-gray-300 focus:ring-gray-500 dark:bg-gray-900"
                                    {{ $newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to
                                == null ? '' :
                                'disabled' }} required> <div
                                    class="bg-gray-300 flex items-center justify-center px-1 rounded-l-xl ">
                                    أيام </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2"> <label
                            class="block text-sm font-bold text-gray-700 dark:text-gray-300">تفاصيل العرض
                        </label> <textarea {{
                            $newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to == null ? '' : 'disabled' }} name="proposal_details" rows="5" class="w-full rounded-xl border-gray-300 focus:ring-gray-500 dark:bg-gray-900" required>{!! old('proposal_details', $myProposal->proposal_details ?? '') !!}</textarea>
                    </div> @if($newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to == null)
                    <div class="flex items-center justify-between gap-4 pt-6 border-t dark:border-gray-700"> <button
                            type="submit"
                            class="w-full bg-gray-900 hover:bg-gray-700 text-white px-10 py-3 rounded-xl font-bold shadow-xl shadow-gray-100 transition-all hover:-translate-y-1">
                            <i class="fas fa-paper-plane ml-2"></i> {{ $myProposal ? 'تحديث العرض الحالي' : 'إرسال العرض
                            المالي والتقني'
                            }} </button> @if($myProposal) <span class="text-xs text-green-500 font-medium italic"> <i
                                class="fas fa-info-circle"></i> لقد قمت بتقديم عرض مسبقاً، يمكنك تعديله الآن. </span>
                        @endif </div>
                    @endif
                </form>
            </div>
            @endif
        </div>
        @endif
        @if(Auth::user()->role === 'independent_partner' && Auth::user()->status === 'active')
        <div
            class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-8">
            <div class="flex items-center gap-4 mb-8">
                <div
                    class="h-12 w-12 bg-gray-600 rounded-full flex items-center justify-center text-white text-xl shadow-lg shadow-gray-200">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">هل أنت جاهز للعمل؟</h2>
                    <p class="text-gray-500 italic">قدم عرضاً احترافياً لزيادة فرص قبولك</p>
                </div>
            </div>

            @if($errors->any())
            <div class="mx-4 mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200"
                role="alert">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    <span class="font-bold">حدث خطأ أثناء إرسال العرض:</span>
                </div>
                <ul class="list-disc list-inside mr-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('dashboard.new_project.store_proposal', $newProject->id) }}" method="POST"
                class="space-y-6">
                @csrf <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2"> <label
                            class="text-sm font-bold flex items-center text-gray-700 dark:text-gray-300">
                            قيمة العرض </label>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1 flex"> <input type="number" name="budget_to"
                                    value="{{ old('budget_to', $myProposal->budget_to ?? '') }}"
                                    class=" {{ $newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to == null ? '!bg-gray-400' : '' }} w-full pr-2 rounded-r-xl border-gray-300 focus:ring-gray-500"
                                    required {{ $newProject->bidding_deadline > \Carbon\Carbon::now() &&
                                $newProject->budget_to
                                ==
                                null ? '' : 'disabled' }} > <div
                                    class="bg-gray-300 flex items-center justify-center px-1 rounded-l-xl">
                                    <x-drhm-icon color="black" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2"> <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">مدة
                            التسليم</label>
                        <div class="relative flex-1 flex"> <input type="number" name="execution_time"
                                value="{{ old('execution_time', $myProposal->execution_time ?? '') }}"
                                class="w-full pl-10 rounded-r-xl border-gray-300 focus:ring-gray-500 dark:bg-gray-900"
                                {{ $newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to ==
                            null ? '' :
                            'disabled' }} required> <div
                                class="bg-gray-300 flex items-center justify-center px-1 rounded-l-xl ">
                                أيام </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-2"> <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">تفاصيل
                        العرض
                    </label> <textarea {{
                        $newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to == null ? '' : 'disabled' }} name="proposal_details" rows="5" class="w-full rounded-xl border-gray-300 focus:ring-gray-500 dark:bg-gray-900" required>{!! old('proposal_details', $myProposal->proposal_details ?? '') !!}</textarea>
                </div> @if($newProject->bidding_deadline > \Carbon\Carbon::now() && $newProject->budget_to == null) <div
                    class="flex items-center justify-between gap-4 pt-6 border-t dark:border-gray-700"> <button
                        type="submit"
                        class="w-full bg-gray-900 hover:bg-gray-700 text-white px-10 py-3 rounded-xl font-bold shadow-xl shadow-gray-100 transition-all hover:-translate-y-1">
                        <i class="fas fa-paper-plane ml-2"></i> {{ $myProposal ? 'تحديث العرض الحالي' : 'إرسال العرض
                        المالي
                        والتقني'
                        }} </button> @if($myProposal) <span class="text-xs text-green-500 font-medium italic"> <i
                            class="fas fa-info-circle"></i> لقد قمت بتقديم عرض مسبقاً، يمكنك تعديله الآن. </span> @endif
                </div>
                @endif
            </form>
        </div>
        @else
        {{-- رسالة تظهر إذا كان الدور غير مطابق أو الحساب غير نشط --}}
        <div
            class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl border-2 border-dashed border-amber-200 dark:border-amber-900/50 p-10 text-center">
            <div class="flex flex-col items-center">
                {{-- أيقونة الحالة --}}
                <div
                    class="h-20 w-20 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-600 mb-6 animate-pulse">
                    <i class="fas fa-user-clock text-3xl"></i>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">
                    @if(Auth::user()->status === 'pending')
                    حسابك قيد المراجعة حالياً
                    @elseif(Auth::user()->status === 'blocked')
                    تم تعليق حسابك مؤقتاً
                    @else
                    يتطلب تفعيل الحساب للبدء
                    @endif
                </h2>

                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                    @if(Auth::user()->status === 'pending')
                    شكراً لانضمامك إلينا! فريق الإدارة يقوم حالياً بمراجعة بياناتك وفيديو التحقق.
                    <span class="block mt-2 font-bold text-amber-600">ستتمكن من تقديم العروض فور توثيق الحساب.</span>
                    @elseif(Auth::user()->status === 'blocked')
                    نأسف، لا يمكنك تقديم عروض في الوقت الحالي بسبب قيود على حسابك. يرجى التواصل مع الدعم الفني
                    للاستفسار.
                    @else
                    عذراً، هذه الميزة مخصصة للشركاء المستقلين الموثقين فقط.
                    @endif
                </p>

                {{-- زر العودة أو التواصل --}}
                <div class="mt-8 flex gap-4">

                    @if(Auth::user()->status === 'pending')
                    <div class="px-6 py-2 bg-amber-50 text-amber-700 rounded-xl font-medium border border-amber-100">
                        <i class="fas fa-history ml-1"></i> بانتظار التوثيق
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection