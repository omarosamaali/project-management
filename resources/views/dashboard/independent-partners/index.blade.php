@extends('layouts.app')

@section('title', 'الشركاء المستقلين')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard') }}" second="الشركاء المستقلين" />

    <div class="mx-auto w-full">
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                {{-- البحث --}}
                <div class="w-full md:w-1/2">
                    <form action="{{ route('dashboard.independent-partners.index') }}" method="GET"
                        class="flex items-center">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                @if(request()->search == null)
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewbox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                                @else
                                <a href="{{ route('dashboard.independent-partners.index') }}"><i
                                        class="fa-solid fa-arrow-rotate-right text-gray-500"></i></a>
                                @endif
                            </div>
                            <input value="{{ request()->search }}" type="text" name="search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:text-white"
                                placeholder="بحث بالإسم، البريد أو الهاتف">
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                {{-- رسائل النجاح والخطأ --}}
                @if(session('success'))
                <div class="mx-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
                @endif

                <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">الشريك</th>
                            <th class="px-4 py-3">الدولة</th>
                            <th class="px-4 py-3">الهاتف</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3 text-left">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partners as $partner)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 flex items-center">
                                <img src="{{ asset('storage/' . $partner->avatar) }}"
                                    class="w-10 h-10 rounded-full ml-3 object-cover border"
                                    onerror="this.src='/assets/img/default-avatar.png'">
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $partner->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $partner->email }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{-- {{ $partner->country ?? 'غير محدد' }} --}}
                                <div class="flex items-center gap-1 w-[200px] ">
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
                                    <img src="https://flagcdn.com/w40/{{ strtolower($partner->country) }}.png"
                                    alt="علم {{ $partner->country }}" class="w-8 h-6 object-cover rounded">
                                    @endif
                                    <select disabled id="country_select2" name="country"
                                    class="!py-3 placeholder-gray-500 block mt-1 w-full rtl:text-right " required>
                                    <option :value="old('country', $partner->country)" disabled selected>... جاري تحميل
                                        الدول ...</option>
                                </select>
                            </div>
                                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
                                rel="stylesheet" />
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

                            </td>
                            <td class="px-4 py-3" dir="ltr">{{ $partner->phone }}</td>
                            <td class="px-4 py-3">
                                @php
                                $statusClasses = [
                                'active' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'blocked' => 'bg-red-100 text-red-800',
                                'inactive' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusLabels = [
                                'active' => 'نشط',
                                'pending' => 'قيد المراجعة',
                                'blocked' => 'محظور',
                                'inactive' => 'غير نشط',
                                ];
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClasses[$partner->status] ?? 'bg-gray-100' }}">
                                    {{ $statusLabels[$partner->status] ?? $partner->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <div class="flex items-center justify-end space-x-2 space-x-reverse">
                                    {{-- زر المعاينة --}}
                                    <a href="{{ route('dashboard.independent-partners.show', $partner->id) }}"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"
                                        title="عرض التفاصيل والتحقق">
                                        <i class="fas fa-id-card text-lg"></i>
                                    </a>

                                    {{-- زر التعديل --}}
                                    <a href="{{ route('dashboard.independent-partners.edit', $partner->id) }}"
                                        class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                        <i class="fas fa-user-edit"></i>
                                    </a>

                                    {{-- زر الحذف --}}
                                    <form action="{{ route('dashboard.independent-partners.destroy', $partner->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا الشريك نهائياً؟ لا يمكن التراجع عن هذا الإجراء.')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 bg-gray-50 dark:bg-gray-900">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users-slash text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">لا يوجد شركاء مستقلين حالياً.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $partners->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

@endsection