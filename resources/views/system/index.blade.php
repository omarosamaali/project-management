@extends('layouts.user')

@section('title', 'الأنظمة')

@section('content')

<x-hero-section />

<x-marquee :logos="$logos" />

<section id="systems" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ __('messages.systems') }}</h1>
        <p class="text-xl text-gray-600">{{ __('messages.system_description') }}</p>
    </div>

    <div class="flex flex-wrap justify-center gap-4 mb-10">
        <button onclick="filterSystems('all')"
            class="service-filter-btn active-filter bg-black text-white px-6 py-2 rounded-full shadow-md transition-all"
            data-id="all">
            {{ __('الكل') }}
        </button>
        @foreach($services as $service)
        <button onclick="filterSystems({{ $service->id }})"
            class="service-filter-btn bg-white text-gray-700 border border-gray-200 px-6 py-2 rounded-full shadow-sm hover:bg-gray-50 transition-all"
            data-id="{{ $service->id }}">
            {{ app()->getLocale() == 'en' ? $service->name_en : $service->name_ar }}
        </button>
        @endforeach
    </div>

    <div id="systems-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($systems as $system)
        <div class="system-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col"
            data-service="{{ $system->service_id }}">

            <div class="relative h-48 overflow-hidden">
                @if($system->service_id)
                <span class="absolute top-2 right-2 bg-black text-white text-xs px-2 py-1 rounded">
                    {{ $system->service->name_ar }}
                </span>
                @endif
                <img src="{{ asset($system->main_image) }}" alt="..." class="w-full h-full object-cover">
            </div>

            <div class="p-6 flex flex-col flex-grow">
                <h3 class="text-2xl font-bold text-gray-800 mb-3 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $system->name_en : $system->name_ar }}
                </h3>
                <p class="text-gray-600 mb-4 line-clamp-2 ltr:text-left rtl:text-right">
                    {{ app()->getLocale() == 'en' ? $system->description_en : $system->description_ar }}
                </p>

                {{-- display price --}}
                <div>
                    <span class="text-xl font-bold text-black flex gap-2 items-center justify-center">
                        {{ __('messages.price') }} {{ $system->price }}
                        <svg width="{{ $width ?? 18 }}" height="{{ $height ?? 18 }}" xmlns="http://www.w3.org/2000/svg" width="1000"
                            height="870" viewBox="0 0 1000 870" fill="none">
                            <path
                                d="M88.3 1C88.7 1.6 90.9 4.3 93 6.9C108.3 25.1 119.8 54.7 126 92C130.1 116.5 130.3 124.2 130.3 217.6V304.6H88.5C50.3 304.6 45.9 304.4 38.4 302.9C26.6 300.4 14.4 293.7 6.2 285.1C-0.3 278.2 -0.1 277.8 0.3 298.7C0.8 316 1 317.9 3.5 327.3C7.5 342.2 13 353.3 21.3 363.2C32.6 376.8 44.1 384.4 60.5 389.5C64 390.5 71.4 390.9 97.6 391.1L130.3 391.6V434.9V478.3L84.2 478L37.9 477.7L29.9 474.5C20.4 470.7 16.1 467.9 6.8 459.6L0 453.5L0.4 472.6C0.9 490.3 1 492.3 3.5 501.3C12.2 533.1 33.2 555.8 60.9 563.2C67.8 565.1 70.5 565.2 99.4 565.6L130.3 566V655.6C130.3 709.7 130 749.6 129.5 756.4C129 762.6 127.4 774.2 126 782.3C119.5 819.6 107.8 847.7 91 865.9L87.6 869.6H256.7C357.8 869.6 433.4 869.2 444.5 868.7C464 867.7 507.5 863.4 517.3 861.3C520.4 860.7 526.2 859.8 530 859.2C538.1 858 551.5 855.2 570.8 850.3C598 843.5 622.8 835 647.1 824.2C654.7 820.8 676.5 809.7 682.3 806.2C685.4 804.4 689.1 802.2 690.5 801.5C694.4 799.4 700.9 795.2 710.4 788.4C715.1 785 719.8 781.7 720.8 781C725 778.2 739.5 766.1 746.1 760C771.2 736.9 792.2 711.2 808.5 683.7C810.8 679.7 813.8 674.7 815.1 672.6C818.4 667 832 639 833.3 634.8C833.9 632.9 834.7 630.9 835.1 630.5C837.7 627.1 852.7 579.9 854.5 569.6C855.1 566.3 855.4 565.8 857.9 565.3C859.5 565 882.8 565 909.7 565.2C963.5 565.6 963.5 565.6 975.4 571.1C982.1 574.2 984.1 575.6 991.5 582.3C1001.2 591 1000.3 592.4 999.7 570.6C999.3 557.8 998.8 549.9 997.9 546.7C994.5 534.4 993.7 531.8 990.7 525.6C980.9 504.2 964.5 488.9 943.5 481.6L935.3 478.6L901.9 478.2L868.6 477.7L869 466C869.4 450.6 869.4 420.1 868.9 404.4L868.5 391.8L913.1 391.6C951.3 391.4 958.4 391.6 962.6 392.7C975.2 396.2 983.7 401 994.1 410.5L999.9 415.9V401.1C999.9 383.5 999 375.7 995.4 364.1C988.3 340.6 974.3 323.1 954.3 312.3C941.3 305.3 940.5 305.1 895.8 304.8C869.6 304.6 855.9 304.2 855.2 303.6C854.6 303 854.1 302 854.1 301.2C854.1 300.4 852.6 294.1 850.6 287.3C827.2 204.6 783.5 138.9 719.6 90.2C710.9 83.5 689.6 69.4 681 64.6C677.7 62.7 674.1 60.7 673.2 60.1C669 57.8 644.9 46 638.9 43.5C635.3 41.9 630.6 39.9 628.5 39.1C593.2 23.8 534 9.3 488.8 4.8C481.4 4.1 471.6 3 467.1 2.6C446.7 0.3 418.4 0 257.7 0C121.9 0 87.8 0.3 88.3 1ZM419 44.3C452.8 46.3 473.6 48.9 497.9 54.8C572.1 72.4 624.3 109.6 662.2 171.8C665.7 177.6 680.5 207.8 682.7 213.9C693.2 242.2 698.3 259 702.8 281.2C703.9 286.6 705.4 293.8 706.1 297.2C706.8 300.5 707.1 303.6 706.8 303.9C706.3 304.3 605.9 304.5 483.5 304.4L261 304.2L260.7 175.7C260.6 105.1 260.7 46.4 261 45.3L261.4 43.4H332.5C371.5 43.4 410.5 43.8 419 44.3ZM716.5 394.6C717.2 398.9 717.2 471.9 716.5 475.5L715.9 478.2L488.4 478L261 477.7L260.8 435.3C260.6 412 260.8 392.6 261 392.2C261.3 391.7 358.2 391.4 488.7 391.4H715.9L716.5 394.6ZM706.3 566.3C706.8 567.8 704.4 580.1 699.5 600.1C693.9 622.6 686.3 645.3 678.6 662.1C674.8 670.7 665.3 689.3 663 692.8C661.9 694.4 658.7 699.5 655.9 704C637.9 732.2 612.2 757.9 582.9 776.9C572.2 783.7 550.2 795.3 544.3 797.1C543.1 797.4 541.8 798 541.3 798.4C540.6 799 531.5 802.4 520.9 806.2C501.4 813.1 464.3 820.6 434.5 823.7C415.2 825.6 412.1 825.7 337.8 825.7H260.9V696V566.2L481.8 565.8C603.3 565.6 703.4 565.3 704.2 565.1C705.1 565 706 565.6 706.3 566.3Z"
                                fill="{{ $color ?? 'black' }}" />
                        </svg>
                    </span>
                </div>
                <p class="text-center text-sm text-gray-500">{{ __('messages.get_it_in') }} {{ $system->execution_days_to }} {{ __('messages.day') }}</p>

                <div class="mt-auto pt-4">
                    <a href="{{ route('system.show', $system) }}"
                        class="block text-center w-full bg-black text-white py-3 rounded-lg font-semibold">
                        {{ __('messages.show_details') }}
                    </a>
                </div>

                <div
                    class="mt-4 flex items-center justify-center gap-2 text-gray-600 bg-gray-50 py-2.5 px-4 rounded-lg border border-gray-200">
                    <i class="fa-solid fa-shopping-bag text-red-600 text-lg"></i>
                    @if($system->counter > 0)
                    <span class="text-sm font-medium">
                        {{ __('messages.purchase') }}
                        <span class="font-bold text-red-600">
                            {{ $system->counter }}
                        </span>
                        {{ __('messages.times') }}
                    </span>
                    @else
                    <span class="text-sm font-medium">
                        {{ __('messages.no_purchases') }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="no-systems-msg" class="hidden text-center py-20">
        <p class="text-gray-500 text-lg">لا توجد أنظمة متاحة لهذا القسم حالياً.</p>
    </div>
</section>

<style>
    .active-filter {
        background-color: #000 !important;
        /* black */
        color: white !important;
        border-color: #000 !important;
    }
</style>

<script>
    function filterSystems(serviceId) {
    const cards = document.querySelectorAll('.system-card');
    const buttons = document.querySelectorAll('.service-filter-btn');
    let visibleCount = 0;

    // تحديث شكل الأزرار
    buttons.forEach(btn => {
        if(btn.getAttribute('data-id') == serviceId) {
            btn.classList.add('active-filter', 'bg-black', 'text-white');
            btn.classList.remove('bg-white', 'text-gray-700');
        } else {
            btn.classList.remove('active-filter', 'bg-black', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700');
        }
    });

    // تصفية الكروت
    cards.forEach(card => {
        if (serviceId === 'all' || card.getAttribute('data-service') == serviceId) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // إظهار رسالة "لا يوجد" إذا كانت النتائج صفر
    const msg = document.getElementById('no-systems-msg');
    if(visibleCount === 0) {
        msg.classList.remove('hidden');
    } else {
        msg.classList.add('hidden');
    }
}
</script>

@endsection