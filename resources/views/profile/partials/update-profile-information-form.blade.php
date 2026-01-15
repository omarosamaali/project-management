<style>
    .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
        display: none !important;
    }

    .select2-container,
    .iti {
        width: 100% !important;
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
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            معلومات الملف الشخصي
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            قم بتحديث معلومات حسابك وعنوان بريدك الإلكتروني
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <x-input-label for="name" value="الاسم" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- country --}}
        <div>
            <x-input-label for="country" value="الدولة" />
            <div class="flex items-center gap-2 mt-1">
                @if($user->country)
                <img src="https://flagcdn.com/w40/{{ strtolower($user->country) }}.png" alt="علم {{ $user->country }}"
                    class="w-8 h-6 object-cover rounded">
                @endif
                <select id="country_select2" name="country"
                    class="!py-3 placeholder-gray-500 block mt-1 w-full rtl:text-right " required>
                    <option :value="old('country', $user->country)" disabled selected>... جاري تحميل الدول ...</option>
                </select>
            </div>
            <x-input-error :messages="$errors->get('country')" class="mt-2" />
        </div>

        {{-- email --}}
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    بريدك الإلكتروني غير موثق.

                    <button form="send-verification"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        اضغط هنا لإعادة إرسال رسالة التحقق
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600">
                    تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- role --}}
        <div>
            <x-input-label for="role" value="الصلاحية" />
            <div id="role" name="role" type="text" class="rounded border-gray-300 mt-1 block w-full">{{ $user->role_name
                }}</div>
            <x-input-error class="mt-2" :messages="$errors->get('role')" />
        </div>

        @if(Auth::user()->role == 'partner')
        {{-- payment --}}
        <div class="space-y-6">
            <div class="border border-gray-300 rounded-lg p-6 bg-white shadow-sm">
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

        <div class="flex items-center gap-4">
            <x-primary-button>حفظ</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600">تم الحفظ بنجاح.</p>
            @endif
        </div>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
        const countryDataUrl = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';
        const currentCountry = '{{ old('country', $user->country) }}'; // هنا الإصلاح
        
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
    </script>
</section>