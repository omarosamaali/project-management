@extends('layouts.user')

@section('title', 'تسجيل الدخول')

@section('content')
<style>
    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
        display: none !important;
    }
    .select2-container, .iti {
        width: 100%;
    }

    .select2-container--default .select2-selection--single {
        height: 42px !important;
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
<div class="my-10 mx-auto max-w-7xl w-[96%] bg-white rounded-xl shadow-2xl overflow-hidden grid md:grid-cols-2">

    <!-- جانب الصورة -->
    <div class="hidden md:block relative">
        <img src="{{ asset('assets/images/login.png') }}"
            alt="{{ __('messages.register_title') }}" class="w-full h-full object-cover">
        <div
            class="absolute inset-0 flex items-center justify-center p-8">
            <div class="text-white text-center">
                <i class="fas fa-user-plus text-8xl mb-6"></i>
                <h2 class="text-4xl font-bold mb-4">{{ __('messages.create_account') }}</h2>
                <p class="text-xl">{{ __('messages.register_subtitle') }}</p>
            </div>
        </div>
    </div>

    <!-- جانب النموذج -->
    <div class="p-8 md:p-12">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-800 mb-3 ltr:text-left rtl:text-right">
                {{ __('messages.register_title') }}
            </h1>
            <p class="text-gray-600 ltr:text-left rtl:text-right">
                {{ __('messages.register_description') }}
            </p>
        </div>

        <form class="space-y-6" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
            <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
                <div class="flex items-center gap-2 mb-2 font-bold">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>تعذر إنشاء الحساب — يرجى تصحيح الأخطاء التالية:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- نوع العضوية -->
            <div>
                <x-input-label value="نوع العضوية" />
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @php $selectedRole = old('role', 'client'); @endphp
                    <label class="flex items-start gap-2 p-3 border-2 rounded-lg cursor-pointer transition role-option {{ $selectedRole === 'client' ? 'border-black bg-gray-50' : 'border-gray-200' }}">
                        <input type="radio" name="role" value="client" class="mt-1 text-black focus:ring-black shrink-0"
                            {{ $selectedRole === 'client' ? 'checked' : '' }} required>
                        <span class="min-w-0">
                            <span class="block font-bold text-gray-800">عميل</span>
                            <span class="text-xs text-gray-500 leading-snug">للمشاريع والخدمات — بدون الدورات التدريبية</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-2 p-3 border-2 rounded-lg cursor-pointer transition role-option {{ $selectedRole === 'trainer' ? 'border-black bg-gray-50' : 'border-gray-200' }}">
                        <input type="radio" name="role" value="trainer" class="mt-1 text-black focus:ring-black shrink-0"
                            {{ $selectedRole === 'trainer' ? 'checked' : '' }}>
                        <span class="min-w-0">
                            <span class="block font-bold text-gray-800">محاضر</span>
                            <span class="text-xs text-gray-500 leading-snug">إنشاء وإدارة الدورات، والتحضير، والاختبارات</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-2 p-3 border-2 rounded-lg cursor-pointer transition role-option {{ $selectedRole === 'trainee' ? 'border-black bg-gray-50' : 'border-gray-200' }}">
                        <input type="radio" name="role" value="trainee" class="mt-1 text-black focus:ring-black shrink-0"
                            {{ $selectedRole === 'trainee' ? 'checked' : '' }}>
                        <span class="min-w-0">
                            <span class="block font-bold text-gray-800">متدرب</span>
                            <span class="text-xs text-gray-500 leading-snug">الاشتراك في الدورات والاختبارات والشهادات</span>
                        </span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- نوع الحساب -->
            <div>
                <x-input-label :value="__('messages.account_type')" />
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label
                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition account-type-option {{ old('account_type', 'personal') === 'personal' ? 'border-black bg-gray-50' : 'border-gray-200' }}">
                        <input type="radio" name="account_type" value="personal" class="text-black focus:ring-black"
                            {{ old('account_type', 'personal') === 'personal' ? 'checked' : '' }} required>
                        <span>
                            <span class="block font-bold text-gray-800">{{ __('messages.account_personal') }}</span>
                            <span class="text-xs text-gray-500">{{ __('messages.account_personal_hint') }}</span>
                        </span>
                    </label>
                    <label
                        class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition account-type-option {{ old('account_type') === 'business' ? 'border-black bg-gray-50' : 'border-gray-200' }}">
                        <input type="radio" name="account_type" value="business" class="text-black focus:ring-black"
                            {{ old('account_type') === 'business' ? 'checked' : '' }}>
                        <span>
                            <span class="block font-bold text-gray-800">{{ __('messages.account_business') }}</span>
                            <span class="text-xs text-gray-500">{{ __('messages.account_business_hint') }}</span>
                        </span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
            </div>

            <!-- بيانات الشركة (حساب تجاري) -->
            <div id="business-fields" class="space-y-4 {{ old('account_type') === 'business' ? '' : 'hidden' }}">
                <div>
                    <x-input-label for="company_name" :value="__('messages.company_name')" />
                    <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name"
                        :value="old('company_name')" autocomplete="organization" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="company_logo" :value="__('messages.company_logo')" />
                    <input id="company_logo" type="file" name="company_logo" accept="image/*"
                        class="block mt-1 w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-black file:text-white hover:file:bg-gray-800" />
                    <p class="text-xs text-gray-500 mt-1">{{ __('messages.company_logo_hint') }}</p>
                    <x-input-error :messages="$errors->get('company_logo')" class="mt-2" />
                </div>
            </div>

            <!-- الاسم -->
            <div>
                <x-input-label for="name" :value="__('messages.name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                    autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- البريد الإلكتروني -->
            <div>
                <x-input-label for="email" :value="__('messages.email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- رقم الجوال -->
            <div>
                <x-input-label for="phone" :value="__('messages.phone')" />
                <x-text-input id="phone" class="placeholder-gray-500 block mt-1 w-full rtl:text-right" type="tel"
                    name="phone" :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            {{-- الدولة --}}
            <div class="mt-4">
                <x-input-label for="country_select2" :value="__('messages.country')" />
                <select id="country_select2" name="country"
                    class="!py-3 placeholder-gray-500 block mt-1 w-full rtl:text-right " required>
                    <option value="" disabled selected>... جاري تحميل الدول ...</option>
                </select>
                <x-input-error :messages="$errors->get('country')" class="mt-2" />
            </div>

            <!-- كلمة المرور -->
            <div>
                <x-input-label for="password" :value="__('messages.password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- تأكيد كلمة المرور -->
            <div>
                <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- حقول المحاضر -->
            <div id="trainer-fields" class="space-y-5 {{ old('role') === 'trainer' ? '' : 'hidden' }}">
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <i class="fas fa-info-circle ml-1"></i>
                    {{ __('messages.trainer_register_notice') }}
                </div>

                <div>
                    <x-input-label for="course_category_id" :value="__('messages.trainer_category')" />
                    <select id="course_category_id" name="course_category_id"
                        class="block mt-1 w-full border-gray-300 focus:border-black focus:ring-black rounded-md shadow-sm">
                        <option value="">{{ __('messages.trainer_category_placeholder') }}</option>
                        @foreach(($categories ?? []) as $category)
                        <option value="{{ $category->id }}" @selected((string) old('course_category_id') === (string) $category->id)>
                            {{ $category->title(app()->getLocale()) }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('course_category_id')" class="mt-2" />
                </div>

                @include('dashboard.course-categories.partials.drag-image-input', [
                    'name' => 'avatar',
                    'label' => __('messages.trainer_avatar'),
                    'hint' => __('messages.trainer_avatar_hint'),
                    'required' => true,
                    'previewRounded' => true,
                ])

                <div class="grid sm:grid-cols-2 gap-4">
                    @include('dashboard.course-categories.partials.drag-image-input', [
                        'name' => 'id_card_front',
                        'label' => __('messages.trainer_id_front'),
                        'hint' => __('messages.trainer_upload_required_hint'),
                        'required' => true,
                        'previewRounded' => false,
                    ])
                    @include('dashboard.course-categories.partials.drag-image-input', [
                        'name' => 'id_card_back',
                        'label' => __('messages.trainer_id_back'),
                        'hint' => __('messages.trainer_upload_required_hint'),
                        'required' => true,
                        'previewRounded' => false,
                    ])
                </div>

                <div>
                    <label class="inline-flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-gray-300 text-black focus:ring-black"
                            {{ old('accept_terms') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 leading-relaxed">
                            {{ __('messages.trainer_terms_agree') }}
                            <button type="button" id="open-trainer-terms"
                                class="text-black font-bold underline underline-offset-2 hover:text-gray-700">
                                {{ __('messages.trainer_terms_link') }}
                            </button>
                        </span>
                    </label>
                    <x-input-error :messages="$errors->get('accept_terms')" class="mt-2" />
                </div>
            </div>

            <!-- زر التسجيل -->
            <button type="submit" class="w-full bg-black text-white py-4 rounded-lg font-bold text-lg 
                hover:bg-gray-800 transition-all shadow-lg hover:shadow-xl">
                {{ __('messages.register_button') }}
            </button>

        </form>

        <!-- لديك حساب بالفعل؟ -->
        <div class="mt-10 text-center">
            <p class="text-gray-600">
                {{ __('messages.already_registered') }}
                <a href="{{ route('login') }}" class="text-black hover:text-gray-800 font-bold">
                    {{ __('messages.login_here') }}
                </a>
            </p>
        </div>
    </div>
</div>

{{-- Terms modal --}}
<div id="trainer-terms-modal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/50" data-close-terms></div>
    <div class="relative mx-auto my-8 w-[94%] max-w-2xl max-h-[85vh] overflow-hidden rounded-xl bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between gap-3 border-b px-5 py-4">
            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.trainer_terms_title') }}</h3>
            <button type="button" class="text-gray-500 hover:text-gray-800 text-xl leading-none" data-close-terms aria-label="Close">&times;</button>
        </div>
        <div class="px-5 py-4 overflow-y-auto text-sm text-gray-700 leading-7 space-y-3">
            {!! __('messages.trainer_terms_body') !!}
        </div>
        <div class="border-t px-5 py-3 flex justify-end">
            <button type="button" class="px-4 py-2 bg-black text-white rounded-lg text-sm font-medium hover:bg-gray-800" data-close-terms>
                {{ __('messages.close') }}
            </button>
        </div>
    </div>
</div>

@include('dashboard.course-categories.partials.drag-image-script')

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

            const businessFields = document.getElementById('business-fields');
            const companyName = document.getElementById('company_name');
            const companyLogo = document.getElementById('company_logo');
            const accountRadios = document.querySelectorAll('input[name="account_type"]');
            const accountOptions = document.querySelectorAll('.account-type-option');

            function syncAccountType() {
                const isBusiness = document.querySelector('input[name="account_type"]:checked')?.value === 'business';
                businessFields.classList.toggle('hidden', !isBusiness);
                if (companyName) {
                    companyName.required = isBusiness;
                }
                if (companyLogo) {
                    companyLogo.required = isBusiness;
                }
                accountOptions.forEach((label) => {
                    const radio = label.querySelector('input[type="radio"]');
                    const selected = radio?.checked;
                    label.classList.toggle('border-black', selected);
                    label.classList.toggle('bg-gray-50', selected);
                    label.classList.toggle('border-gray-200', !selected);
                });
            }

            accountRadios.forEach((radio) => radio.addEventListener('change', syncAccountType));
            syncAccountType();

            const roleRadios = document.querySelectorAll('input[name="role"]');
            const roleOptions = document.querySelectorAll('.role-option');
            const trainerFields = document.getElementById('trainer-fields');
            const categorySelect = document.getElementById('course_category_id');
            const termsCheckbox = document.querySelector('input[name="accept_terms"]');

            function syncTrainerFields() {
                const isTrainer = document.querySelector('input[name="role"]:checked')?.value === 'trainer';
                trainerFields?.classList.toggle('hidden', !isTrainer);

                trainerFields?.querySelectorAll('.drag-image-input').forEach((input) => {
                    input.disabled = !isTrainer;
                    input.required = isTrainer;
                    if (!isTrainer) {
                        input.value = '';
                        const field = input.closest('.drag-image-field');
                        field?.querySelector('.drag-image-preview')?.classList.add('hidden');
                        field?.querySelector('.drag-image-filename')?.classList.add('hidden');
                        input.closest('.drag-image-dropzone')?.classList.remove('border-red-500', 'bg-red-50');
                    }
                });
                if (categorySelect) {
                    categorySelect.disabled = !isTrainer;
                    categorySelect.required = isTrainer;
                }
                if (termsCheckbox) {
                    termsCheckbox.disabled = !isTrainer;
                    termsCheckbox.required = isTrainer;
                }
            }

            function syncRoleOption() {
                roleOptions.forEach((label) => {
                    const radio = label.querySelector('input[type="radio"]');
                    const selected = radio?.checked;
                    label.classList.toggle('border-black', selected);
                    label.classList.toggle('bg-gray-50', selected);
                    label.classList.toggle('border-gray-200', !selected);
                });
                syncTrainerFields();
            }
            roleRadios.forEach((radio) => radio.addEventListener('change', syncRoleOption));
            syncRoleOption();

            const registerForm = document.querySelector('form[action="{{ route('register') }}"]');
            registerForm?.addEventListener('submit', (e) => {
                const isTrainer = document.querySelector('input[name="role"]:checked')?.value === 'trainer';
                if (!isTrainer) return;

                let valid = true;
                trainerFields?.querySelectorAll('.drag-image-input').forEach((input) => {
                    const zone = input.closest('.drag-image-dropzone');
                    const hasFile = input.files && input.files.length > 0;
                    zone?.classList.toggle('border-red-500', !hasFile);
                    zone?.classList.toggle('bg-red-50', !hasFile);
                    zone?.classList.toggle('border-gray-300', hasFile);
                    if (!hasFile) valid = false;
                });

                if (!valid) {
                    e.preventDefault();
                    trainerFields?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    alert(@json(__('messages.trainer_images_required_alert')));
                }
            });

            const termsModal = document.getElementById('trainer-terms-modal');
            const openTerms = document.getElementById('open-trainer-terms');
            function setTermsOpen(open) {
                if (!termsModal) return;
                termsModal.classList.toggle('hidden', !open);
                termsModal.setAttribute('aria-hidden', open ? 'false' : 'true');
                document.body.classList.toggle('overflow-hidden', open);
            }
            openTerms?.addEventListener('click', (e) => {
                e.preventDefault();
                setTermsOpen(true);
            });
            termsModal?.querySelectorAll('[data-close-terms]').forEach((el) => {
                el.addEventListener('click', () => setTermsOpen(false));
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') setTermsOpen(false);
            });
        });
</script>
@endsection
