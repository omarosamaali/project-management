<style>
    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
        display: none !important;
    }

    .select2-container,
    .iti {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 44px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
        position: relative !important;
        top: 5px !important;
    }

    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
        top: 9px !important;
    }

    .profile-form-field input[type="text"],
    .profile-form-field input[type="email"],
    .profile-form-field input[type="url"],
    .profile-form-field input[type="tel"],
    .profile-form-field select,
    .profile-form-field textarea {
        border-radius: 0.75rem;
        border-color: #cbd5e1;
    }

    .profile-form-field input:focus,
    .profile-form-field select:focus,
    .profile-form-field textarea:focus {
        border-color: #0b8f7f;
        box-shadow: 0 0 0 3px rgba(11, 143, 127, 0.15);
    }

    .profile-avatar-picker input[type="file"] {
        font-size: 0.8125rem;
    }
</style>

<section class="profile-form-field">
    <header class="mb-6 pb-5 border-b border-slate-100">
        <div class="flex flex-wrap items-start gap-3">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-white shadow-sm"
                style="background:linear-gradient(135deg,#0b8f7f,#0D2444);">
                <i class="fas fa-user-pen"></i>
            </span>
            <div class="min-w-0">
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                    معلومات الملف الشخصي
                </h2>
                <p class="mt-1 text-sm text-slate-500 leading-relaxed">
                    حدّث بيانات حسابك الظاهرة للمتدربين والإدارة
                </p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4 sm:p-5 space-y-5">
            <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <i class="fas fa-id-card text-teal-600"></i>
                البيانات الأساسية
            </h3>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                        <x-input-label for="name" value="الاسم" />
                        <span class="text-xs text-slate-500 font-medium">(يفضل باللغة العربية، سيتم إضافته للشهادة)</span>
                    </div>
                    <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full" :value="old('name', $user->name)"
                        required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                @if(Auth::user()->role === 'client')
                <div class="sm:col-span-2">
                    <x-client-company-fields :user="$user" :logo-required="($user->account_type ?? 'personal') === 'business' && !$user->company_logo" />
                </div>
                @endif

                <div>
                    <x-input-label for="country" value="الدولة" />
                    <div class="flex items-center gap-2 mt-1.5">
                        @if($user->country)
                        <img src="https://flagcdn.com/w40/{{ strtolower($user->country) }}.png" alt="علم {{ $user->country }}"
                            class="w-8 h-6 object-cover rounded shadow-sm border border-slate-200 shrink-0">
                        @endif
                        <select id="country_select2" name="country"
                            class="!py-3 placeholder-gray-500 block w-full rtl:text-right" required>
                            <option :value="old('country', $user->country)" disabled selected>... جاري تحميل الدول ...</option>
                        </select>
                    </div>
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" value="رقم الهاتف" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1.5 block w-full" dir="ltr"
                        :value="old('phone', $user->phone)" autocomplete="tel" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="email" value="البريد الإلكتروني" />
                    @if($user->isTrainer())
                    <div class="relative mt-1.5">
                        <x-text-input id="email" type="email" class="block w-full bg-slate-100 text-slate-600 pe-10"
                            :value="$user->email" disabled readonly autocomplete="username" />
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                            <i class="fas fa-lock text-xs"></i>
                        </span>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 flex items-start gap-1.5">
                        <i class="fas fa-info-circle mt-0.5 text-teal-600"></i>
                        <span>{{ __('messages.trainer_email_readonly_hint') }}</span>
                    </p>
                    @else
                    <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full"
                        :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    @endif

                    @if (!$user->isTrainer() && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5">
                        <p class="text-sm text-amber-900">
                            بريدك الإلكتروني غير موثق.
                            <button form="send-verification"
                                class="underline font-semibold text-amber-800 hover:text-amber-950 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-amber-500">
                                اضغط هنا لإعادة إرسال رسالة التحقق
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-700">
                            تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني
                        </p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($user->isTrainer())
        <div class="rounded-2xl border border-teal-100 bg-gradient-to-b from-teal-50/60 to-white p-4 sm:p-5 space-y-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-teal-600 text-white text-sm">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </span>
                    {{ __('messages.trainer_profile_section') }}
                </h3>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm font-bold text-slate-800 mb-3">{{ __('messages.trainer_avatar') }}</p>
                <div class="flex flex-col sm:flex-row gap-4 sm:items-start">
                    <div class="flex items-center gap-4 shrink-0">
                        <div class="text-center">
                            <img id="avatar_preview" src="{{ $user->avatarUrl() }}" alt=""
                                class="w-20 h-20 rounded-full object-cover border-2 border-white shadow ring-1 ring-slate-200">
                            <p class="text-[11px] font-semibold text-slate-500 mt-1.5">صورتك الحالية</p>
                        </div>
                        <div class="text-slate-300 text-lg" aria-hidden="true">
                            <i class="fas fa-arrow-left-long"></i>
                        </div>
                        <div class="text-center">
                            <img src="{{ asset('images/trainer-avatar-example.jpg') }}"
                                alt="{{ __('messages.trainer_avatar_example_alt') }}"
                                class="w-20 h-20 rounded-full object-cover border-2 border-teal-200 shadow-sm ring-1 ring-teal-100">
                            <p class="text-[11px] font-semibold text-teal-700 mt-1.5">مثال مطلوب</p>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 profile-avatar-picker">
                        <label for="avatar"
                            class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-5 cursor-pointer hover:border-teal-400 hover:bg-teal-50/40 transition">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white border border-slate-200 text-teal-700">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </span>
                            <span class="text-sm font-bold text-slate-800">اختر صورة جديدة</span>
                            <span class="text-xs text-slate-500">PNG أو JPG — يفضّل خلفية بسيطة</span>
                            <input id="avatar" type="file" name="avatar" accept="image/*" class="sr-only">
                        </label>
                        <p id="avatar_file_name" class="text-xs text-slate-500 mt-2 truncate hidden"></p>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ __('messages.trainer_avatar_professional_note') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-input-label for="linkedin_url" :value="__('messages.trainer_linkedin')" />
                    <x-text-input id="linkedin_url" name="linkedin_url" type="url" class="mt-1.5 block w-full" dir="ltr"
                        :value="old('linkedin_url', $user->linkedin_url)" placeholder="https://www.linkedin.com/in/..." />
                    <x-input-error class="mt-2" :messages="$errors->get('linkedin_url')" />
                </div>

                <div>
                    <x-input-label for="course_category_id" :value="__('messages.trainer_category')" />
                    <select id="course_category_id" name="course_category_id"
                        class="mt-1.5 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                        <option value="">{{ __('messages.trainer_category_placeholder') }}</option>
                        @foreach(($categories ?? []) as $category)
                        <option value="{{ $category->id }}" @selected((string) old('course_category_id', $user->course_category_id) === (string) $category->id)>
                            {{ $category->title(app()->getLocale()) }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('course_category_id')" />
                </div>

                <div>
                    <x-input-label for="teaching_language" :value="__('messages.become_trainer_teaching_lang')" />
                    <select id="teaching_language" name="teaching_language"
                        class="mt-1.5 block w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                        <option value="ar" @selected(old('teaching_language', $user->teaching_language ?? 'ar') === 'ar')>{{ __('messages.become_trainer_teaching_lang_ar') }}</option>
                        <option value="en" @selected(old('teaching_language', $user->teaching_language ?? 'ar') === 'en')>{{ __('messages.become_trainer_teaching_lang_en') }}</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="trainer_bio" :value="__('messages.trainer_bio')" />
                    <textarea id="trainer_bio" name="trainer_bio" rows="5" minlength="120" maxlength="2000"
                        class="mt-1.5 block w-full rounded-xl border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('trainer_bio', $user->trainer_bio) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('trainer_bio')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label :value="__('messages.trainer_sample')" />
                    <p class="text-xs text-slate-500 mt-1 mb-1">{{ __('messages.trainer_sample_hint_optional') }}</p>
                    @include('academy.partials.teaching-sample-input', [
                        'variant' => 'profile',
                        'sampleType' => old(
                            'teaching_sample_type',
                            $user->teachingSampleIsExternal() ? 'link' : 'upload'
                        ),
                        'existingSampleUrl' => $user->teachingSampleUrl(),
                        'existingIsExternal' => $user->teachingSampleIsExternal(),
                    ])
                </div>
            </div>

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-4 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-university text-teal-600"></i>
                        {{ __('messages.trainer_payment_config_tab') }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ __('messages.trainer_payment_config_hint') }}</p>
                </div>
                <a href="{{ route('dashboard.academy.payment-profile.edit') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white shrink-0 shadow-sm"
                    style="background:linear-gradient(135deg,#0b8f7f,#0D2444);">
                    <i class="fas fa-sliders"></i>
                    {{ __('messages.trainer_payment_config_manage') }}
                </a>
            </div>
        </div>
        @endif

        @if(Auth::user()->role == 'partner')
        <div class="space-y-6">
            <div class="border border-gray-300 rounded-2xl p-6 bg-white shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-wallet text-blue-600"></i> طرق سحب الأرباح
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition cursor-pointer">
                        <input {{ old('withdrawal_method', $user->withdrawal_method) == 'wallet' ? 'checked' : '' }}
                        type="radio" id="withdrawal_wallet" name="withdrawal_method" value="wallet"
                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 method-radio">
                        <label for="withdrawal_wallet"
                            class="mr-3 text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                            <i class="fas fa-mobile-alt ml-2 text-green-600"></i> محفظة إلكترونية
                        </label>
                    </div>

                    <div
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition cursor-pointer">
                        <input {{ old('withdrawal_method', $user->withdrawal_method) == 'paypal' ? 'checked' : '' }}
                        type="radio" id="withdrawal_paypal" name="withdrawal_method" value="paypal"
                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 method-radio">
                        <label for="withdrawal_paypal"
                            class="mr-3 text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                            <i class="fab fa-paypal ml-2 text-blue-600"></i> PayPal
                        </label>
                    </div>
                </div>

                <div id="wallet_details_section"
                    class="{{ old('withdrawal_method', $user->withdrawal_method) == 'wallet' ? '' : 'hidden' }} space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع المحفظة (مثل: Vodafone Cash)
                                <span class="text-black">*</span></label>
                            <input type="text" name="wallet_type" value="{{ old('wallet_type', $user->wallet_type) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500"
                                placeholder="e.g. Orange Money">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم بالكامل بالإنجليزية <span
                                    class="text-black">*</span></label>
                            <input type="text" name="wallet_full_name"
                                value="{{ old('wallet_full_name', $user->wallet_full_name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500"
                                placeholder="Full Name in English">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="withdrawal_email" id="email_label" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ old('withdrawal_method', $user->withdrawal_method) == 'wallet' ? 'رقم المحفظة' : 'بريد
                        PayPal' }}
                        <span class="text-black">*</span>
                    </label>
                    <input value="{{ old('withdrawal_email', $user->withdrawal_email) }}" type="text"
                        id="withdrawal_email" name="withdrawal_email" placeholder="أدخل البيانات المطلوبة هنا"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mt-4">
                    <label for="withdrawal_notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات
                        إضافية</label>
                    <textarea id="withdrawal_notes" name="withdrawal_notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('withdrawal_notes', $user->withdrawal_notes) }}</textarea>
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="role" value="نسبة الشراكة" />
            <div id="role" name="role" type="text" class="rounded border-gray-300 mt-1 block w-full">{{
                $user->percentage }}%</div>
            <x-input-error class="mt-2" :messages="$errors->get('role')" />
        </div>

        @if(Auth::user()->role == 'partner' && Auth::user()->can_view_notes == 1)
        <div>
            <x-input-label for="role" value="ملاحظة" />
            <div id="role" name="role" type="text" class="rounded border-gray-300 mt-1 block w-full">{{
                $user->salary_notes }}
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('role')" />
        </div>
        @endif

        @endif

        <div class="sticky bottom-3 z-10 flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white/95 backdrop-blur px-4 py-3 shadow-lg">
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-sm"
                style="background:linear-gradient(135deg,#0b8f7f,#0D2444);">
                <i class="fas fa-floppy-disk"></i>
                حفظ التغييرات
            </button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                class="text-sm font-semibold text-emerald-700 flex items-center gap-1.5">
                <i class="fas fa-circle-check"></i>
                تم الحفظ بنجاح.
            </p>
            @endif
        </div>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
        const countryDataUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
        const currentCountry = '{{ old('country', $user->country) }}';

        fetch(countryDataUrl)
            .then(response => response.json())
            .then(data => {
                const selectElement = $('#country_select2');
                selectElement.empty();

                selectElement.append(new Option("اختر دولتك", "", false, false));

                data.forEach(country => {
                    const countryName = country.translations.ara.common || country.name.common;
                    const countryCode = country.cca2;

                    const isSelected = currentCountry === countryCode;
                    const newOption = new Option(countryName, countryCode, isSelected, isSelected);

                    selectElement.append(newOption);
                });

                selectElement.select2({
                    placeholder: "اختر دولتك",
                    allowClear: true,
                    dir: "rtl"
                });

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
    <script>
        document.querySelectorAll('.method-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const walletSection = document.getElementById('wallet_details_section');
                const emailLabel = document.getElementById('email_label');
                const emailInput = document.getElementById('withdrawal_email');

                if (this.value === 'wallet') {
                    walletSection.classList.remove('hidden');
                    emailLabel.innerHTML = 'رقم المحفظة (Phone Number) <span class="text-black">*</span>';
                    emailInput.placeholder = '01xxxxxxxxx';
                } else {
                    walletSection.classList.add('hidden');
                    emailLabel.innerHTML = 'بريد PayPal الإلكتروني <span class="text-black">*</span>';
                    emailInput.placeholder = 'example@paypal.com';
                }
            });
        });

        (function () {
            const input = document.getElementById('avatar');
            const preview = document.getElementById('avatar_preview');
            const nameEl = document.getElementById('avatar_file_name');
            if (!input || !preview) return;

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                if (nameEl) {
                    nameEl.textContent = file.name;
                    nameEl.classList.remove('hidden');
                }
                const url = URL.createObjectURL(file);
                preview.src = url;
            });
        })();
    </script>
</section>
