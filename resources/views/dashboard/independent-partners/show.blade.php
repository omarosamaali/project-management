@extends('layouts.app')
@section('title', 'تفاصيل الشريك: ' . $partner->name)

@section('content')
<section class="p-3 sm:p-5 dark:bg-gray-900">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.independent-partners.index') }}" second="الشركاء"
        third="تفاصيل الشريك" />

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- الرأس: معلومات الحساب الأساسية --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border p-6 flex flex-col md:flex-row items-center gap-6">
            <div class="relative">
                <img src="{{ asset('storage/' . $partner->avatar) }}"
                    class="w-32 h-32 rounded-2xl object-cover border-4 border-blue-50 shadow-sm"
                    onerror="this.src='/assets/img/default-avatar.png'">
                <span
                    class="absolute -bottom-2 -left-2 px-3 py-1 rounded-lg text-xs font-bold 
                    {{ $partner->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $partner->status }}
                </span>
            </div>

            <div class="flex-1 text-center md:text-right">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $partner->name }}</h1>
                <p class="text-gray-500 mt-1"><i class="fas fa-envelope ml-2"></i>{{ $partner->email }}</p>
                <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-4">
                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-phone-alt ml-1"></i> {{ $partner->phone }}
                    </span>
                    <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-sm">
                        <td class="px-4 py-3">
                            {{-- {{ $partner->country ?? 'غير محدد' }} --}}
                            <div class="flex items-center gap-1 w-[200px] ">
                                <i class="fas fa-globe-africa ml-1"></i>
                                <style>
                                    .select2-container--default .select2-selection--single {
                                        background-color: unset !important;
                                        border: unset !important;
                        
                                    }
                        
                                    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow,
                                    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
                                        display: none !important;
                                    }
                                </style>
                                @if($partner->country)
                                <img src="https://flagcdn.com/w40/{{ strtolower($partner->country) }}.png" alt="علم {{ $partner->country }}"
                                    class="w-8 h-6 object-cover rounded">
                                @endif
                                <select disabled id="country_select2" name="country"
                                    class="!py-3 placeholder-gray-500 block mt-1 w-full rtl:text-right " required>
                                    <option :value="old('country', $partner->country)" disabled selected>... جاري تحميل
                                        الدول ...</option>
                                </select>
                            </div>
                            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
                            </script>
                        
                            <script>
                                $(document).ready(function() {
                                const countryDataUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
                                const currentCountry = '{{ old('country', $partner->country) }}'; // هنا الإصلاح
                                
                                fetch(countryDataUrl)
                                    .then(response => response.json())
                                    .then(data => {
                                        const selectElement = $('#country_select2');
                                        selectElement.empty();
                        
                                        selectElement.append(new Option("اختر دولتك", "", false, false));
                                        
                                        data.forEach(country => {
                                            const countryName = country.translations.ara.common || country.name.common;
                                            const countryCode = country.cca2;
                                            
                                            // تحديد الدولة الحالية
                                            const isSelected = currentCountry === countryCode;
                                            const newOption = new Option(countryName, countryCode, isSelected, isSelected);
                                            
                                            selectElement.append(newOption);
                                        });
                        
                                        selectElement.select2({
                                            placeholder: "اختر دولتك",
                                            allowClear: true,
                                            dir: "rtl"
                                        });
                                        
                                        // لو فيه قيمة محفوظة، اختارها
                                        if (currentCountry) {
                                            selectElement.val(currentCountry).trigger('change');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('حدث خطأ أثناء تحميل قائمة الدول:', error);
                                        $('#country_select2').empty().append(new Option("تعذر تحميل الدول", "", true, true));
                                    });
                            });
                            </script>
                    </span>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('dashboard.independent-partners.edit', $partner->id) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all">
                    <i class="fas fa-edit ml-2"></i>تعديل الحالة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- العمود الجانبي: المهارات والخدمات --}}
            <div class="md:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-tools ml-2 text-blue-600"></i> المهارات والخدمات
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @if($partner->skills && is_array($partner->skills))
                        @foreach($partner->skills as $skill)
                        <span
                            class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 dark:border-gray-600">
                            {{ $skill }}
                        </span>
                        @endforeach
                        @else
                        <p class="text-gray-400 text-sm italic">لم يتم تحديد مهارات</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-calendar-alt ml-2 text-blue-600"></i> تاريخ الانضمام
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ $partner->created_at->format('Y-m-d') }}
                        <span class="text-xs text-gray-400">({{ $partner->created_at->diffForHumans() }})</span>
                    </p>
                </div>
            </div>

            {{-- المحتوى الرئيسي: مستندات التحقق --}}
            <div class="md:col-span-2 space-y-6">

                {{-- قسم الفيديو --}}
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border">
                    <h3 class="font-bold mb-4 text-blue-700 dark:text-blue-400 flex items-center justify-between">
                        <span><i class="fas fa-video ml-2"></i> فيديو التحقق السيلفي</span>
                        @if($partner->verification_video)
                        <span class="text-xs font-normal text-gray-400">MP4 Format</span>
                        @endif
                    </h3>
                    @if($partner->verification_video)
                    <div class="relative group rounded-xl overflow-hidden bg-black">
                        <video controls class="w-full max-h-[400px]">
                            <source src="{{ asset('storage/' . $partner->verification_video) }}" type="video/mp4">
                            متصفحك لا يدعم تشغيل الفيديو.
                        </video>
                    </div>
                    @else
                    <div
                        class="bg-gray-50 dark:bg-gray-700 h-48 flex flex-col items-center justify-center rounded-xl border-2 border-dashed">
                        <i class="fas fa-video-slash text-3xl text-gray-300 mb-2"></i>
                        <p class="text-gray-400">لم يتم رفع فيديو تحقق</p>
                    </div>
                    @endif
                </div>

                {{-- قسم الهوية --}}
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border">
                    <h3 class="font-bold mb-4 text-blue-700 dark:text-blue-400">
                        <i class="fas fa-id-card ml-2"></i> صورة الهوية / جواز السفر
                    </h3>
                    @if($partner->id_card_path)
                    <div class="relative group rounded-xl overflow-hidden border">
                        <img src="{{ asset('storage/' . $partner->id_card_path) }}"
                            class="w-full h-[300px] object-cover cursor-pointer transition-transform duration-300 group-hover:scale-105"
                            onclick="window.open(this.src)">
                        <div
                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <span class="text-white font-bold"><i class="fas fa-search-plus ml-2"></i>اضغط
                                للتكبير</span>
                        </div>
                    </div>
                    @else
                    <div
                        class="bg-gray-50 dark:bg-gray-700 h-48 flex flex-col items-center justify-center rounded-xl border-2 border-dashed">
                        <i class="fas fa-id-badge text-3xl text-gray-300 mb-2"></i>
                        <p class="text-gray-400">لم يتم رفع صورة الهوية</p>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- تنبيه بأسفل الصفحة --}}
        @if($partner->status == 'pending')
        <div class="bg-amber-50 border-r-4 border-amber-400 p-4 rounded-xl flex items-center gap-4">
            <div class="bg-amber-100 p-2 rounded-full">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-amber-800">طلب بانتظار المراجعة</h4>
                <p class="text-amber-700 text-sm">يرجى مطابقة ملامح الوجه في الفيديو مع صورة الهوية والاسم الثلاثي قبل
                    تفعيل الحساب.</p>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection