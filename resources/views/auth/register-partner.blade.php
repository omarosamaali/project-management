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

        <form action="{{ route('register.partner.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-5">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الاسم الثلاثي</label>
                    <input type="text" name="name" required placeholder="محمد أحمد علي"
                        class="placeholder-gray-400 w-full p-3.5 rounded-xl border border-gray-200 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>

                {{-- الدولة --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الدولة</label>
                    <select id="country_select2" name="country"
                        class="!py-3 placeholder-gray-400 block mt-1 w-full rtl:text-right " required>
                        <option value="" disabled selected>... جاري تحميل الدول ...</option>
                    </select>
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">البريد
                        الإلكتروني</label>
                    <input type="email" name="email" required placeholder="example@mail.com"
                        class="placeholder-gray-400 w-full p-3.5 rounded-xl border border-gray-200 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">رقم الهاتف</label>
                    <input type="tel" name="phone" required placeholder="+20 123 456 789"
                        class="placeholder-gray-400 w-full p-3.5 rounded-xl border border-gray-200 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

            </div>
            <div class="grid md:grid-cols-2 gap-5">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">كلمة المرور</label>
                    <input type="password" name="password" required placeholder="********"
                        class="placeholder-gray-400 w-full p-3.5 rounded-xl border border-gray-200 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">تأكيد كلمة
                        المرور</label>
                    <input type="password" name="password_confirmation" required placeholder="********"
                        class="placeholder-gray-400 w-full p-3.5 rounded-xl border border-gray-200 dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
            <div class="pt-4">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">نوع الخدمات
                    والمهارات</label>
                <div class="flex flex-wrap gap-3">
                    @php
                    $skills = ['برمجة ويب', 'تطبيقات جوال', 'تصميم جرافيك', 'تسويق إلكتروني', 'كتابة محتوى', 'ترجمة'];
                    @endphp

                    @foreach($skills as $skill)
                    <label
                        class="inline-flex items-center px-4 py-2 rounded-full border border-gray-200 cursor-pointer hover:bg-blue-50 hover:border-blue-400 transition-all">
                        <input type="checkbox" name="skills[]" value="{{ $skill }}" class="ml-2 rounded text-blue-600">
                        <span class="text-sm font-medium text-gray-700">{{ $skill }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <hr class="my-8 opacity-50">

            <div class="grid md:grid-cols-2 gap-6">
                <div class="relative group">
                    <label class="block text-sm font-bold mb-2">الصورة الشخصية</label>
                    <div id="avatar-container" class="border-2 border-dashed border-gray-200 rounded-2xl p-4 text-center hover:border-blue-400 transition-all 
                        cursor-pointer overflow-hidden h-56 flex items-center justify-center relative">
                        <input type="file" id="avatar-input" name="avatar"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*"
                            required>
                        <div id="avatar-placeholder">
                            <i class="fas fa-camera text-2xl text-gray-400 mb-2"></i>
                            <p class="text-xs text-gray-500">اسحب أو اختر صورة مربعة</p>
                        </div>
                        <img id="avatar-preview" src="#" alt="Preview"
                            class="hidden absolute inset-0 w-full h-full object-cover">
                    </div>
                </div>

                <div class="relative group">
                    <label class="block text-sm font-bold mb-2">صورة البطاقة / الجواز</label>
                    <div id="id-container" class="border-2 border-dashed border-gray-200 rounded-2xl p-4 text-center hover:border-blue-400 transition-all 
                        cursor-pointer overflow-hidden h-56 flex items-center justify-center relative">
                        <input type="file" id="id-input" name="id_image"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*"
                            required>
                        <div id="id-placeholder">
                            <i class="fas fa-id-card-alt text-2xl text-gray-400 mb-2"></i>
                            <p class="text-xs text-gray-500">صورة واضحة للوجهين</p>
                        </div>
                        <img id="id-preview" src="#" alt="Preview"
                            class="hidden absolute inset-0 w-full h-full object-cover">
                    </div>
                </div>

                <div class="col-span-2 relative">
                    <label class="block text-sm font-bold mb-2">فيديو سيلفي مع البطاقة (التحقق)</label>
                    <div id="video-container"
                        class="bg-blue-50 dark:bg-blue-900/10 border-2 border-dashed border-blue-200 rounded-2xl p-4 text-center hover:border-blue-500 transition-all cursor-pointer relative min-h-[200px] flex items-center justify-center">
                        <input type="file" id="video-input" name="selfie_video"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="video/*"
                            >
                        <div id="video-placeholder">
                            <i class="fas fa-video text-3xl text-blue-500 mb-3"></i>
                            <h4 class="font-bold text-blue-700">ارفع فيديو التحقق</h4>
                            <p class="text-xs text-blue-600 mt-1 italic">بحد أقصى 10 ثوانٍ</p>
                        </div>
                        <video id="video-preview" controls class="hidden w-full max-h-64 rounded-xl"></video>
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all transform hover:-translate-y-1">
                    إرسال طلب الانضمام كشريك
                </button>
                <p class="text-center text-sm text-gray-500 mt-4">
                    لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-blue-600 font-bold underline">سجل
                        دخولك</a>
                </p>
            </div>
        </form>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
                const countryDataUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
                fetch(countryDataUrl)
                    .then(response => response.json())
                    .then(data => {
                        const selectElement = $('#country_select2');
                        selectElement.empty();
    
                        selectElement.append(new Option("اختر دولتك", "", true, true));
                        data.forEach(country => {
                            const countryName = country.translations.ara.common || country.name.common;
                            const countryCode = country.cca2;
                            const newOption = new Option(countryName, countryCode, false, false);
                            if ('{{ old('country') }}' === countryCode) {
                                newOption.selected = true;
                            }
    
                            selectElement.append(newOption);
                        });
    
                        selectElement.select2({
                            placeholder: "اختر دولتك",
                            allowClear: true,
                            dir: "rtl"
                        });
                    })
                    .catch(error => {
                        console.error('حدث خطأ أثناء تحميل قائمة الدول:', error);
                        $('#country_select2').empty().append(new Option("تعذر تحميل الدول", "", true, true));
                    });
            });
    </script>
    <script>
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