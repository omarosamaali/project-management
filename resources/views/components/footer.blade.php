<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.show {
        display: flex;
        opacity: 1;
        z-index: 99999999999999999999999999999999999;
    }

    .modal {
        background: rgb(0, 0, 0);
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        color: white;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        transform: scale(0.7);
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .modal-overlay.show .modal {
        transform: scale(1);
    }

    .modal::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
        }

        100% {
            transform: translateX(100%) translateY(100%) rotate(45deg);
        }
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        background: none;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        z-index: 1;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close-btn:hover {
        transform: rotate(90deg);
    }

    .modal-content {
        position: relative;
        z-index: 1;
    }

    .kodo-logo {
        width: 120px;
        height: 120px;
        margin: 0 auto 30px;
        background: white !important;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        color: white;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .modal h2 {
        font-size: 28px;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .modal-info {
        font-size: 18px;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .contact-info {
        border-radius: 15px;
        padding: 20px;
        margin: 25px 0;
        backdrop-filter: blur(10px);
    }

    .phone {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #ffd700;
    }

    .website {
        font-size: 18px;
        color: #87ceeb;
    }

    .website a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .website a:hover {
        color: #ffd700;
    }

    .decorative-elements {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .floating-icon {
        position: absolute;
        animation: float 6s ease-in-out infinite;
        opacity: 0.3;
    }

    .floating-icon:nth-child(1) {
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .floating-icon:nth-child(2) {
        top: 20%;
        right: 10%;
        animation-delay: 2s;
    }

    .floating-icon:nth-child(3) {
        bottom: 20%;
        left: 15%;
        animation-delay: 4s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px) rotate(0deg);
        }

        33% {
            transform: translateY(-20px) rotate(5deg);
        }

        66% {
            transform: translateY(10px) rotate(-5deg);
        }
    }
    footer {
    margin-top: auto !important;
    }
    
    body {
    display: flex !important;
    flex-direction: column !important;
    min-height: 100vh !important;
    }
    
    main, .content, #app > div:first-child {
    flex: 1 !important;
    }
</style>

<footer
        class="bg-black text-white p-4 flex items-center justify-between md:flex-row flex-col text-center gap-2 mt-auto">
        <div>
            {{ __('messages.all_rights_reserved') }} &copy; {{ date('Y') }}
        </div>
    
        <div style="text-transform: uppercase">
            <a href="https://evorq.com/" target="_blank" id="kodoLink" class="hover:text-gray-300 transition-colors">
                {{ __('messages.developed_by') }}
            </a>
        </div>
    </footer>
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="decorative-elements">
            <div class="floating-icon">💻</div>
            <div class="floating-icon">🚀</div>
            <div class="floating-icon">⭐</div>
        </div>

        <button class="close-btn" id="closeBtn">&times;</button>

        <div class="modal-content">
            <div class="kodo-logo">
                <img style="width: 82px;" src="https://evorq.com/storage/footer-logo-1-1.png" alt="Evorq Logo">
            </div>
            <h2>{{ __('messages.implemented_by_evorq') }}</h2>

            <div class="contact-info">
                <div class="phone">{{ __('messages.tareq_mohamed_bn_kalban') }}</div>
                <div class="phone">📞 contact@evorq.com</div>
                <div class="website">🌐 <a href="https://evorq.com/" target="_blank">https://evorq.com</a></div>
            </div>

            <div class="modal-info">
                {{ __('messages.evorq_company_slogan') }}<br>
                {{ __('messages.evorq_description') }}
            </div>
        </div>
    </div>
</div>

<script>
    const kodoLink = document.getElementById('kodoLink');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeBtn = document.getElementById('closeBtn');

    // فتح المودال
    kodoLink.addEventListener('click', function(e) {
        e.preventDefault();
        modalOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    });

    // إغلاق المودال عند الضغط على زر الإغلاق
    closeBtn.addEventListener('click', function() {
        modalOverlay.classList.remove('show');
        document.body.style.overflow = 'auto';
    });

    // إغلاق المودال عند الضغط خارج المحتوى
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    });

    // إغلاق المودال بضغط مفتاح Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('show')) {
            modalOverlay.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    });
</script>