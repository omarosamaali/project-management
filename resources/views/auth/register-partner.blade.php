@extends('layouts.user')

@section('title', 'تسجيل شريك مستقل جديد')

@section('content')
<style>
    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
        display: none !important;
    }

    .select2-container,
    .iti {
        width: 100%;
    }

    .select2-container--default .select2-selection--single {
        height: 49px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
        position: relative !important;
        top: 4px !important;
    }

    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
        top: 9px !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db !important;
    }
</style>
<div
    class="my-10 mx-auto max-w-6xl w-full bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden grid md:grid-cols-12 min-h-[700px]">

    <div class="hidden md:flex md:col-span-4 bg-gradient-to-b from-slate-900 to-slate-800 p-10 flex-col justify-between text-white text-right"
        dir="rtl">
        <div>
            <div class="mb-8">
                <i class="fas fa-user-shield text-5xl text-blue-400"></i>
            </div>
            <h2 class="text-3xl font-bold mb-6">انضم لنخبة المستقلين</h2>
            <p class="text-gray-300 leading-relaxed">
                نحن نهتم بجودة الخدمات المقدمة، لذا نطلب بعض الوثائق لضمان هوية شركائنا وحماية حقوق الجميع.
            </p>

            <div class="mt-10 space-y-6">
                <div class="flex items-start gap-3">
                    <span class="bg-blue-500/20 p-2 rounded-lg text-blue-400"><i class="fas fa-id-card"></i></span>
                    <p class="text-sm">تأكد أن صورة الهوية واضحة وبنفس بيانات التسجيل.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="bg-green-500/20 p-2 rounded-lg text-green-400"><i class="fas fa-video"></i></span>
                    <p class="text-sm">الفيديو السيلفي وسيلة سريعة للتحقق الفوري من هويتك.</p>
                </div>
            </div>
        </div>

        <div class="text-xs text-gray-500 italic">
            * جميع بياناتك مشفرة ومحمية وفق سياسة الخصوصية.
        </div>
    </div>

    <div class="col-span-12 md:col-span-8 p-8 md:p-12" dir="rtl">
        <div class="mb-8 text-right">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">إنشاء حساب شريك</h1>
            <p class="text-gray-500 mt-2">يرجى ملء كافة الحقول بدقة لبدء مراجعة طلبك.</p>
        </div>
<form action="{{ route('register.partner.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- تنبيه عام بالأخطاء --}}
    @if ($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-4 mb-6 rounded-xl animate-pulse">
        <div class="flex">
            <div class="mr-3">
                <p class="text-sm text-red-700 font-bold">يرجى تصحيح الأخطاء الموجودة أدناه لإتمام التسجيل.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-5">
        {{-- الاسم الثلاثي --}}
        <div class="col-span-2 md:col-span-1">
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الاسم الثلاثي</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: محمد أحمد علي"
                class="placeholder-gray-400 w-full p-3.5 rounded-xl border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-200' }} dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- الدولة --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الدولة</label>
            <select id="country_select2" name="country"
                class="!py-3 placeholder-gray-400 block mt-1 w-full rtl:text-right" required>
                <option value="" disabled selected>... جاري تحميل الدول ...</option>
            </select>
            @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- البريد الإلكتروني --}}
        <div class="col-span-2 md:col-span-1">
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@mail.com"
                class="placeholder-gray-400 w-full p-3.5 rounded-xl border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- رقم الهاتف مع زر الإرسال --}}
        <div class="col-span-2 md:col-span-1">
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">رقم الهاتف (واتساب)</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="9665XXXXXXXX"
                    class="placeholder-gray-400 w-full p-3.5 rounded-xl border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-200' }} dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- كلمة المرور --}}
    <div class="grid md:grid-cols-2 gap-5">
        <div class="col-span-2 md:col-span-1">
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">كلمة المرور</label>
            <input type="password" name="password" required placeholder="********"
                class="placeholder-gray-400 w-full p-3.5 rounded-xl border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="col-span-2 md:col-span-1">
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation" required placeholder="********"
                class="placeholder-gray-400 w-full p-3.5 rounded-xl border border-gray-200 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
    </div>

    {{-- المهارات --}}
    <div class="pt-4">
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">نوع الخدمات والمهارات</label>
        <div class="flex flex-wrap gap-3">
            @php
            $skillsList = \App\Models\Service::all()->pluck('name_ar')->toArray();
            @endphp

            @foreach($skillsList as $skill)
            <label
                class="inline-flex items-center px-4 py-2 rounded-full border border-gray-200 cursor-pointer hover:bg-blue-50 hover:border-blue-400 transition-all">
                <input type="checkbox" name="skills[]" value="{{ $skill }}" {{ is_array(old('skills')) &&
                    in_array($skill, old('skills')) ? 'checked' : '' }} class="ml-2 rounded text-blue-600">
                <span class="text-sm font-medium text-gray-700">{{ $skill }}</span>
            </label>
            @endforeach
        </div>
        @error('skills') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <hr class="my-8 opacity-50">

    {{-- رفع الملفات --}}
    <div class="grid md:grid-cols-2 gap-6">
        {{-- الصورة الشخصية --}}
        <div class="relative group">
            <label class="block text-sm font-bold mb-2">الصورة الشخصية <span class="text-red-500">*</span></label>
            <div id="avatar-container"
                class="border-2 border-dashed {{ $errors->has('avatar') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-2xl p-4 text-center hover:border-blue-400 transition-all cursor-pointer overflow-hidden h-56 flex items-center justify-center relative">
                <input type="file" id="avatar-input" name="avatar"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*">
                <div id="avatar-placeholder">
                    @if($errors->has('avatar'))
                        <i class="fas fa-exclamation-circle text-2xl text-red-400 mb-2"></i>
                        <p class="text-xs text-red-500 font-bold">يرجى إعادة رفع الصورة</p>
                    @else
                        <i class="fas fa-camera text-2xl text-gray-400 mb-2"></i>
                        <p class="text-xs text-gray-500">اسحب أو اختر صورة مربعة</p>
                    @endif
                </div>
                <img id="avatar-preview" src="#" alt="Preview"
                    class="hidden absolute inset-0 w-full h-full object-cover">
            </div>
            @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- صورة البطاقة --}}
        <div class="relative group">
            <label class="block text-sm font-bold mb-2">صورة البطاقة / الجواز <span class="text-red-500">*</span></label>
            <div id="id-container"
                class="border-2 border-dashed {{ $errors->has('id_card_path') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-2xl p-4 text-center hover:border-blue-400 transition-all cursor-pointer overflow-hidden h-56 flex items-center justify-center relative">
                <input type="file" id="id-input" name="id_card_path"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*">
                <div id="id-placeholder">
                    @if($errors->has('id_card_path'))
                        <i class="fas fa-exclamation-circle text-2xl text-red-400 mb-2"></i>
                        <p class="text-xs text-red-500 font-bold">يرجى إعادة رفع الصورة</p>
                    @else
                        <i class="fas fa-id-card-alt text-2xl text-gray-400 mb-2"></i>
                        <p class="text-xs text-gray-500">صورة واضحة للوجهين</p>
                    @endif
                </div>
                <img id="id-preview" src="#" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover">
            </div>
            @error('id_card_path') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- فيديو التحقق --}}
        <div class="col-span-2 relative">
            <label class="block text-sm font-bold mb-2">فيديو سيلفي مع البطاقة (التحقق) <span class="text-red-500">*</span></label>
            <div id="video-container"
                class="border-2 border-dashed {{ $errors->has('verification_video') ? 'border-red-400 bg-red-50' : 'bg-blue-50 dark:bg-blue-900/10 border-blue-200' }} rounded-2xl p-4 text-center hover:border-blue-500 transition-all cursor-pointer relative min-h-[200px] flex items-center justify-center">
                <input type="file" id="video-input" name="verification_video"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="video/*">
                <div id="video-placeholder">
                    @if($errors->has('verification_video'))
                        <i class="fas fa-exclamation-circle text-3xl text-red-400 mb-3"></i>
                        <h4 class="font-bold text-red-600">يرجى إعادة رفع الفيديو</h4>
                        <p class="text-xs text-red-500 mt-1">{{ $errors->first('verification_video') }}</p>
                    @else
                        <i class="fas fa-video text-3xl text-blue-500 mb-3"></i>
                        <h4 class="font-bold text-blue-700">ارفع فيديو التحقق</h4>
                        <p class="text-xs text-blue-600 mt-1 italic">بحد أقصى 20 ميجابايت</p>
                    @endif
                </div>
                <video id="video-preview" controls class="hidden w-full max-h-64 rounded-xl"></video>
            </div>
            @error('verification_video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="pt-6">
        <button type="submit"
            class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all transform hover:-translate-y-1">
            إرسال طلب الانضمام كشريك
        </button>
    </div>
</form>    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
    const countries = [
        { code: "AF", name: "أفغانستان" }, { code: "AL", name: "ألبانيا" },
        { code: "DZ", name: "الجزائر" }, { code: "AD", name: "أندورا" },
        { code: "AO", name: "أنغولا" }, { code: "AG", name: "أنتيغوا وبربودا" },
        { code: "AR", name: "الأرجنتين" }, { code: "AM", name: "أرمينيا" },
        { code: "AU", name: "أستراليا" }, { code: "AT", name: "النمسا" },
        { code: "AZ", name: "أذربيجان" }, { code: "BS", name: "باهاماس" },
        { code: "BH", name: "البحرين" }, { code: "BD", name: "بنغلاديش" },
        { code: "BB", name: "بربادوس" }, { code: "BY", name: "بيلاروسيا" },
        { code: "BE", name: "بلجيكا" }, { code: "BZ", name: "بليز" },
        { code: "BJ", name: "بنين" }, { code: "BT", name: "بوتان" },
        { code: "BO", name: "بوليفيا" }, { code: "BA", name: "البوسنة والهرسك" },
        { code: "BW", name: "بوتسوانا" }, { code: "BR", name: "البرازيل" },
        { code: "BN", name: "بروناي" }, { code: "BG", name: "بلغاريا" },
        { code: "BF", name: "بوركينا فاسو" }, { code: "BI", name: "بوروندي" },
        { code: "CV", name: "الرأس الأخضر" }, { code: "KH", name: "كمبوديا" },
        { code: "CM", name: "الكاميرون" }, { code: "CA", name: "كندا" },
        { code: "CF", name: "جمهورية أفريقيا الوسطى" }, { code: "TD", name: "تشاد" },
        { code: "CL", name: "تشيلي" }, { code: "CN", name: "الصين" },
        { code: "CO", name: "كولومبيا" }, { code: "KM", name: "جزر القمر" },
        { code: "CG", name: "الكونغو" }, { code: "CD", name: "الكونغو الديمقراطية" },
        { code: "CR", name: "كوستاريكا" }, { code: "CI", name: "ساحل العاج" },
        { code: "HR", name: "كرواتيا" }, { code: "CU", name: "كوبا" },
        { code: "CY", name: "قبرص" }, { code: "CZ", name: "التشيك" },
        { code: "DK", name: "الدنمارك" }, { code: "DJ", name: "جيبوتي" },
        { code: "DM", name: "دومينيكا" }, { code: "DO", name: "الدومينيكان" },
        { code: "EC", name: "الإكوادور" }, { code: "EG", name: "مصر" },
        { code: "SV", name: "السلفادور" }, { code: "GQ", name: "غينيا الاستوائية" },
        { code: "ER", name: "إريتريا" }, { code: "EE", name: "إستونيا" },
        { code: "SZ", name: "إسواتيني" }, { code: "ET", name: "إثيوبيا" },
        { code: "FJ", name: "فيجي" }, { code: "FI", name: "فنلندا" },
        { code: "FR", name: "فرنسا" }, { code: "GA", name: "الغابون" },
        { code: "GM", name: "غامبيا" }, { code: "GE", name: "جورجيا" },
        { code: "DE", name: "ألمانيا" }, { code: "GH", name: "غانا" },
        { code: "GR", name: "اليونان" }, { code: "GD", name: "غرينادا" },
        { code: "GT", name: "غواتيمالا" }, { code: "GN", name: "غينيا" },
        { code: "GW", name: "غينيا بيساو" }, { code: "GY", name: "غيانا" },
        { code: "HT", name: "هايتي" }, { code: "HN", name: "هندوراس" },
        { code: "HU", name: "هنغاريا" }, { code: "IS", name: "آيسلندا" },
        { code: "IN", name: "الهند" }, { code: "ID", name: "إندونيسيا" },
        { code: "IR", name: "إيران" }, { code: "IQ", name: "العراق" },
        { code: "IE", name: "أيرلندا" },
        { code: "IT", name: "إيطاليا" }, { code: "JM", name: "جامايكا" },
        { code: "JP", name: "اليابان" }, { code: "JO", name: "الأردن" },
        { code: "KZ", name: "كازاخستان" }, { code: "KE", name: "كينيا" },
        { code: "KI", name: "كيريباتي" }, { code: "KP", name: "كوريا الشمالية" },
        { code: "KR", name: "كوريا الجنوبية" }, { code: "KW", name: "الكويت" },
        { code: "KG", name: "قيرغيزستان" }, { code: "LA", name: "لاوس" },
        { code: "LV", name: "لاتفيا" }, { code: "LB", name: "لبنان" },
        { code: "LS", name: "ليسوتو" }, { code: "LR", name: "ليبيريا" },
        { code: "LY", name: "ليبيا" }, { code: "LI", name: "ليختنشتاين" },
        { code: "LT", name: "ليتوانيا" }, { code: "LU", name: "لوكسمبورغ" },
        { code: "MG", name: "مدغشقر" }, { code: "MW", name: "مالاوي" },
        { code: "MY", name: "ماليزيا" }, { code: "MV", name: "جزر المالديف" },
        { code: "ML", name: "مالي" }, { code: "MT", name: "مالطا" },
        { code: "MH", name: "جزر مارشال" }, { code: "MR", name: "موريتانيا" },
        { code: "MU", name: "موريشيوس" }, { code: "MX", name: "المكسيك" },
        { code: "FM", name: "ميكرونيزيا" }, { code: "MD", name: "مولدوفا" },
        { code: "MC", name: "موناكو" }, { code: "MN", name: "منغوليا" },
        { code: "ME", name: "الجبل الأسود" }, { code: "MA", name: "المغرب" },
        { code: "MZ", name: "موزمبيق" }, { code: "MM", name: "ميانمار" },
        { code: "NA", name: "ناميبيا" }, { code: "NR", name: "ناورو" },
        { code: "NP", name: "نيبال" }, { code: "NL", name: "هولندا" },
        { code: "NZ", name: "نيوزيلندا" }, { code: "NI", name: "نيكاراغوا" },
        { code: "NE", name: "النيجر" }, { code: "NG", name: "نيجيريا" },
        { code: "MK", name: "مقدونيا الشمالية" }, { code: "NO", name: "النرويج" },
        { code: "OM", name: "عُمان" }, { code: "PK", name: "باكستان" },
        { code: "PW", name: "بالاو" }, { code: "PA", name: "بنما" },
        { code: "PG", name: "بابوا غينيا الجديدة" }, { code: "PY", name: "باراغواي" },
        { code: "PE", name: "بيرو" }, { code: "PH", name: "الفلبين" },
        { code: "PL", name: "بولندا" }, { code: "PT", name: "البرتغال" },
        { code: "QA", name: "قطر" }, { code: "RO", name: "رومانيا" },
        { code: "RU", name: "روسيا" }, { code: "RW", name: "رواندا" },
        { code: "KN", name: "سانت كيتس ونيفيس" }, { code: "LC", name: "سانت لوسيا" },
        { code: "VC", name: "سانت فنسنت" }, { code: "WS", name: "ساموا" },
        { code: "SM", name: "سان مارينو" }, { code: "ST", name: "ساو تومي وبرينسيبي" },
        { code: "SA", name: "المملكة العربية السعودية" }, { code: "SN", name: "السنغال" },
        { code: "RS", name: "صربيا" }, { code: "SC", name: "سيشل" },
        { code: "SL", name: "سيراليون" }, { code: "SG", name: "سنغافورة" },
        { code: "SK", name: "سلوفاكيا" }, { code: "SI", name: "سلوفينيا" },
        { code: "SB", name: "جزر سليمان" }, { code: "SO", name: "الصومال" },
        { code: "ZA", name: "جنوب أفريقيا" }, { code: "SS", name: "جنوب السودان" },
        { code: "ES", name: "إسبانيا" }, { code: "LK", name: "سريلانكا" },
        { code: "SD", name: "السودان" }, { code: "SR", name: "سورينام" },
        { code: "SE", name: "السويد" }, { code: "CH", name: "سويسرا" },
        { code: "SY", name: "سوريا" }, { code: "TW", name: "تايوان" },
        { code: "TJ", name: "طاجيكستان" }, { code: "TZ", name: "تنزانيا" },
        { code: "TH", name: "تايلاند" }, { code: "TL", name: "تيمور الشرقية" },
        { code: "TG", name: "توغو" }, { code: "TO", name: "تونغا" },
        { code: "TT", name: "ترينيداد وتوباغو" }, { code: "TN", name: "تونس" },
        { code: "TR", name: "تركيا" }, { code: "TM", name: "تركمانستان" },
        { code: "TV", name: "توفالو" }, { code: "UG", name: "أوغندا" },
        { code: "UA", name: "أوكرانيا" }, { code: "AE", name: "الإمارات العربية المتحدة" },
        { code: "GB", name: "المملكة المتحدة" }, { code: "US", name: "الولايات المتحدة" },
        { code: "UY", name: "أوروغواي" }, { code: "UZ", name: "أوزبكستان" },
        { code: "VU", name: "فانواتو" }, { code: "VE", name: "فنزويلا" },
        { code: "VN", name: "فيتنام" }, { code: "YE", name: "اليمن" },
        { code: "ZM", name: "زامبيا" }, { code: "ZW", name: "زيمبابوي" },
        { code: "PS", name: "فلسطين" }, { code: "XK", name: "كوسوفو" }
    ];

    const selectElement = $('#country_select2');
    selectElement.empty().append(new Option("اختر دولتك", "", true, true));

    const oldValue = '{{ old('country') }}';

    countries.sort((a, b) => a.name.localeCompare(b.name, 'ar'));

    countries.forEach(country => {
        const selected = oldValue === country.code;
        selectElement.append(new Option(country.name, country.code, selected, selected));
    });

    selectElement.select2({
        placeholder: "اختر دولتك",
        allowClear: true,
        dir: "rtl"
    });
});
</script>    <script>
        function setupPreview(inputId, previewId, placeholderId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
    
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    // إظهار عنصر المعاينة وإخفاء النص التوضيحي
                    placeholder.classList.add('hidden');
                    preview.classList.remove('hidden');
    
                    reader.addEventListener('load', function() {
                        preview.setAttribute('src', this.result);
                    });
    
                    reader.readAsDataURL(file);
                }
            });
        }
    
        // تفعيل المعاينة للصور
        setupPreview('avatar-input', 'avatar-preview', 'avatar-placeholder');
        setupPreview('id-input', 'id-preview', 'id-placeholder');
    
        // تفعيل المعاينة للفيديو (تعامل خاص)
        const videoInput = document.getElementById('video-input');
        const videoPreview = document.getElementById('video-preview');
        const videoPlaceholder = document.getElementById('video-placeholder');
    
        videoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileURL = URL.createObjectURL(file);
                videoPlaceholder.classList.add('hidden');
                videoPreview.classList.remove('hidden');
                videoPreview.src = fileURL;
            }
        });
    </script>
</div>
@endsection