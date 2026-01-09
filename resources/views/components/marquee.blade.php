<style>
    #banner img {
        align-items: center;
        justify-content: center;
        margin: auto;
        display: flex;
        text-align: center;
        width: 100%;
        max-height: 25vh;
        height: 100%;
        margin-top: 30px;
        object-fit: fill;
    }

    .marquee-container {
        overflow: hidden;
        white-space: nowrap;
        position: relative;
        padding: 10px 0;
    }

    .marquee-content {
        display: inline-flex;
        align-items: center;
        animation: marquee 60s linear infinite;
        /* تكرار المحتوى عشان مايكونش فيه مسافات فارغة */
        width: max-content;
    }

    .marquee-item {
        flex: 0 0 auto;
        margin: 0 40px;
        opacity: 0.7;
        transition: all 0.3s ease;
        filter: grayscale(100%);
    }

    .marquee-item:hover {
        opacity: 1;
        transform: scale(1.1);
        filter: grayscale(0%);
    }

    .marquee-item img {
        height: 80px;
        width: 140px;
        object-fit: contain;
    }

    @keyframes marquee {
        0% {
            transform: translateX(-0);
        }

        100% {
            transform: translateX(50%);
        }
    }

    .parener-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 600;
        color: #3164f5;
        margin-bottom: 2rem;
        position: relative;
    }

    .parener-title::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: #f8cb25;
        border-radius: 2px;
    }

    /* Pause animation on hover */
    .marquee-container:hover .marquee-content {
        animation-play-state: paused;
    }

    /* Fade effects على الأطراف */
    .marquee-container::before,
    .marquee-container::after {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        width: 100px;
        z-index: 2;
        pointer-events: none;
    }

    .marquee-container::before {
        left: 0;
        background: linear-gradient(to right,
                rgba(248, 249, 250, 1),
                rgba(248, 249, 250, 0));
    }

    .marquee-container::after {
        right: 0;
        background: linear-gradient(to left,
                rgba(248, 249, 250, 1),
                rgba(248, 249, 250, 0));
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .marquee-item {
            margin: 0 20px;
        }

        .marquee-item img {
            height: 40px;
            width: 80px;
        }

        .parener-title {
            font-size: 1.5rem;
        }

        .marquee-content {
            animation-duration: 40s;
        }
    }

    .card-body .title {
        font-weight: 900 !important;
        font-size: 27px;
        color: #254ed6;
        text-align: right;
    }

    .btn.btn-primary {
        width: 100%;
        border-radius: 50px;
    }

    .create-store {
        box-shadow: 0px 0px 3px 3px #f8cb256e;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #f8cb25;
        padding: 20px;
        border-radius: 20px;
    }
</style>
<!-- Start Marquee Section -->
<section class="pt-4">
    <div class="container-fluid px-0">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ __('messages.success_partners') }}</h1>
        </div>
        <div class="marquee-container">
            <div class="marquee-content">
                {{-- عرض الشعارات مرتين لضمان استمرارية الحركة --}}
                @foreach($logos->concat($logos) as $logo)
                <div class="marquee-item">
                    <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}"
                        style="filter: invert(0.3); max-height: 50px;">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- End Marquee Section -->