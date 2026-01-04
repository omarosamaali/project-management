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
            <!-- طرق سحب الأرباح -->
            <div class="border border-gray-300 rounded-lg p-6 bg-white">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">طرق سحب الأرباح</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- محفظة إلكترونية -->
                    <div
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                        <input {{ old('withdrawal_method', $user->withdrawal_method) == 'wallet' ? 'checked' : '' }}
                        type="radio" id="withdrawal_wallet" name="withdrawal_method" value="wallet"
                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <label for="withdrawal_wallet"
                            class="mr-3 text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                            <svg class="w-5 h-5 ml-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                <path fill-rule="evenodd"
                                    d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            محفظة إلكترونية
                        </label>
                    </div>

                    <!-- PayPal -->
                    <div
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                        <input {{ old('withdrawal_method', $user->withdrawal_method) == 'paypal' ? 'checked'
                        : '' }} type="radio" id="withdrawal_paypal" name="withdrawal_method" value="paypal"
                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <label for="withdrawal_paypal"
                            class="mr-3 text-sm font-medium text-gray-700 cursor-pointer flex items-center">
                            <svg class="w-5 h-5 ml-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            PayPal
                        </label>
                    </div>
                </div>

                <!-- حقل البريد الإلكتروني -->
                <div class="mt-4">
                    <label for="withdrawal_email" class="block text-sm font-medium text-gray-700 mb-2">
                        تحويل الارابح علي <span class="text-red-500">*</span>
                    </label>
                    <input value="{{ old('withdrawal_email', $user->withdrawal_email) }}" type="text"
                        id="withdrawal_email" name="withdrawal_email"
                        placeholder="أدخل البريد الإلكتروني للمحفظة أو PayPal"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">سيتم إرسال الأرباح إلى هذا البريد الإلكتروني</p>
                </div>

                <!-- معلومات إضافية (اختياري) -->
                <div class="mt-4">
                    <label for="withdrawal_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        ملاحظات إضافية (اختياري)
                    </label>
                    <textarea id="withdrawal_notes" name="withdrawal_notes" rows="3"
                        placeholder="أي معلومات إضافية تساعدنا في معالجة السحب"
                        class="placeholder-gray-500 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('withdrawal_notes', $user->withdrawal_notes) }}</textarea>
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
</section>