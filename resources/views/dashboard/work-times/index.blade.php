@extends('layouts.app')
@section('title', 'الحضور والإنصراف')
@section('content')
<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.work-times.index') }}" second="الحضور والإنصراف" />

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        {{-- إحصائية الحضور --}}
        <div class="flex bg-black justify-between rounded-lg p-4">
            <div class="text-white">
                <h1 class="text-md font-bold">إجمالي السجلات</h1>
                <p class="text-2xl">{{ $allCount }} سجل</p>
            </div>
            <i class="fas fa-history text-white opacity-50 text-3xl"></i>
        </div>
        {{-- إحصائية الحضور --}}
        <div class="flex bg-green-700 justify-between rounded-lg p-4">
            <div class="text-white">
                <h1 class="text-md font-bold">تسجيلات الحضور</h1>
                <p class="text-2xl">{{ $attendanceCount }}</p>
            </div>
            <i class="fas fa-sign-in-alt text-white opacity-50 text-3xl"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden">
        <div class="flex justify-between items-center p-4">
            <form action="{{ route('dashboard.work-times.index') }}" method="GET" class="w-full md:w-1/2">
                <input type="text" name="search" placeholder="ابحث باسم الموظف..."
                    class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2">
            </form>
            <a href="{{ route('dashboard.work-times.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                + إضافة سجل وقت
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">الموظف</th>
                        <th class="px-4 py-3">البلد</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الوقت</th>
                        <th class="px-4 py-3">الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workTimes as $time)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $time->user->name }}</td>

                        <td class="px-4 py-3 flex flex-col items-start gap-2">
                            <!-- غيرت إلى flex-col عشان الوقت يبقى تحت -->
                            <div class="flex items-center gap-2">
                                <img src="https://flagcdn.com/w40/{{ strtolower($time->country) }}.png"
                                    class="w-6 h-auto rounded-sm shadow-sm" alt="{{ $time->country_name }}">
                                <span class="font-medium text-gray-700 dark:text-gray-200">
                                    {{ $time->country_name }}
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400 local-time mr-3"
                                data-country-code="{{ strtoupper($time->country) }}">
                                الوقت المحلي
                                <!-- هنا الكود اللي هيستخدمه JS -->
                                جاري تحميل الوقت...
                            </span>
                        </td><!-- تحميل المكتبة (خفيفة جدًا) -->

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-800">{{ $time->type }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $time->date }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($time->start_time)->format('g:i A') }}</td>
                        <td class="px-4 py-3">{{ $time->notes }}</td>
                        <td class="px-4 py-3 text-center flex justify-center gap-2">

                            <div class="relative group">
                                <a href="{{ route('dashboard.work-times.edit', $time->id) }}"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-100 p-2 rounded-lg transition block">
                                    <i class="fas fa-hand-paper"></i>
                                </a>

                                <div
                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:flex flex-col items-center">
                                    <div
                                        class="bg-gray-800 text-white text-xs py-1.5 px-3 rounded-md flex items-center gap-2 shadow-xl whitespace-nowrap">
                                        <i class="fas fa-hand-paper text-yellow-400 text-[10px]"></i>
                                        <span class="font-medium"> يدوي</span>
                                    </div>
                                    <div class="w-2 h-2 bg-gray-800 rotate-45 -mt-1"></div>
                                </div>
                            </div>

                            <form action="{{ route('dashboard.work-times.destroy', $time->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-black hover:text-red-900 bg-red-100 p-2 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </td>

                    </tr>
                    @endforeach
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $time->user->name }}</td>

                        <td class="px-4 py-3 flex flex-col items-start gap-2">
                            <!-- غيرت إلى flex-col عشان الوقت يبقى تحت -->
                            <div class="flex items-center gap-2">
                                <img src="https://flagcdn.com/w40/{{ strtolower($time->country) }}.png"
                                    class="w-6 h-auto rounded-sm shadow-sm" alt="{{ $time->country_name }}">
                                <span class="font-medium text-gray-700 dark:text-gray-200">
                                    {{ $time->country_name }}
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400 local-time mr-3"
                                data-country-code="{{ strtoupper($time->country) }}">
                                الوقت المحلي
                                <!-- هنا الكود اللي هيستخدمه JS -->
                                جاري تحميل الوقت...
                            </span>
                        </td><!-- تحميل المكتبة (خفيفة جدًا) -->

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-800">{{ $time->type }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $time->date }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($time->start_time)->format('g:i A') }}</td>
                        <td class="px-4 py-3">{{ $time->notes }}</td>
                        <td class="px-4 py-3 text-center flex justify-center gap-2">

                            <div class="relative group">
                                <a href="{{ route('dashboard.work-times.edit', $time->id) }}"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-100 p-2 rounded-lg transition block">
                                    <i class="fab fa-windows"></i>
                                </a>

                                <div
                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:flex flex-col items-center">
                                    <div
                                        class="bg-gray-800 text-white text-xs py-1 px-2 rounded flex items-center gap-1 shadow-xl">
                                        <i class="fab fa-windows text-blue-400"></i> <span>Windows</span>
                                    </div>
                                    <div class="w-2 h-2 bg-gray-800 rotate-45 -mt-1"></div>
                                </div>
                            </div>
                            <form action="{{ route('dashboard.work-times.destroy', $time->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-black hover:text-red-900 bg-red-100 p-2 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                </tbody>
            </table>
            <div class="p-4">
                {{ $workTimes->links() }}
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/gh/manuelmhtr/countries-and-timezones@latest/dist/index.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
                                                // نجيب كل العناصر اللي فيها data-country-code
                                                const timeElements = document.querySelectorAll('.local-time');
                                        
                                                timeElements.forEach(function (el) {
                                                    const countryCode = el.getAttribute('data-country-code');
                                                    if (!countryCode) {
                                                        el.textContent = 'غير معروف';
                                                        return;
                                                    }
                                                    const country = ct.getCountry(countryCode);
                                                    if (!country || !country.timezones || country.timezones.length === 0) {
                                                        el.textContent = 'لا توجد منطقة زمنية';
                                                        return;
                                                    }
                                                    const timezone = country.timezones[0];
                                                    const options = {
                                                        hour: '2-digit',
                                                        minute: '2-digit',
                                                        second: '2-digit',
                                                        hour12: false,
                                                        timeZone: timezone
                                                    };
                                                    const localTime = new Intl.DateTimeFormat('en-US', options).format(new Date());
                                                    el.textContent = localTime;
                                                    setInterval(function () {
                                                        el.textContent = new Intl.DateTimeFormat('en-US', options).format(new Date());
                                                    }, 1000);
                                                });
                                            });
</script>
@endsection