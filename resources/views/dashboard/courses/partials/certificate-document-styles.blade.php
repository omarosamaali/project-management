{{-- Shared certificate look — keep in sync with the real printable certificate. --}}
<style>
    .certificate {
        --cert-gold: #000000;
        --cert-gold-light: #000000;
        --cert-gold-dark: #0f0f0f;
        --cert-navy: #0D2444;
        --cert-navy-mid: #1A3A6E;
        --cert-parchment: #FDFAF3;
        --cert-ink: #1C1C2E;
        --cert-ink-soft: #4A4A6A;

        position: relative;
        background: var(--cert-parchment);
        background-image:
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='1' fill='%23000' fill-opacity='.07'/%3E%3C/svg%3E"),
            linear-gradient(160deg, rgba(255, 255, 255, 0.6) 0%, rgba(253, 250, 243, 1) 40%, rgba(248, 242, 225, 0.8) 100%);
        width: 900px;
        max-width: none;
        min-height: 640px;
        padding: 60px 80px 70px;
        box-shadow:
            0 4px 6px rgba(0, 0, 0, .06),
            0 20px 60px rgba(13, 36, 68, .18),
            inset 0 0 120px rgba(184, 150, 12, .04);
        overflow: hidden;
        box-sizing: border-box;
        direction: rtl;
        font-family: 'Amiri', serif;
        color: var(--cert-ink);
    }

    .certificate .outer-border {
        position: absolute;
        inset: 10px;
        border: 2px solid var(--cert-gold);
        pointer-events: none;
        z-index: 1;
    }

    .certificate .inner-border {
        position: absolute;
        inset: 16px;
        border: 1px solid rgba(184, 150, 12, .35);
        pointer-events: none;
        z-index: 1;
    }

    .certificate .corner {
        position: absolute;
        width: 80px;
        height: 80px;
        z-index: 2;
        pointer-events: none;
    }

    .certificate .corner-tl { top: 6px; left: 6px; }
    .certificate .corner-tr { top: 6px; right: 6px; transform: scaleX(-1); }
    .certificate .corner-bl { bottom: 6px; left: 6px; transform: scaleY(-1); }
    .certificate .corner-br { bottom: 6px; right: 6px; transform: scale(-1); }

    .certificate .bg-rays {
        position: absolute;
        inset: 0;
        background-image: repeating-conic-gradient(from 0deg at 50% 50%,
                transparent 0deg,
                transparent 8.5deg,
                rgba(184, 150, 12, .025) 8.5deg,
                rgba(184, 150, 12, .025) 9deg);
        pointer-events: none;
        z-index: 0;
    }

    .certificate .watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 0;
    }

    .certificate .watermark img {
        width: 45%;
        opacity: .045;
        filter: grayscale(100%);
    }

    .certificate .cert-content {
        position: relative;
        z-index: 5;
        text-align: center;
        padding: 8px 12px 4px;
    }

    .certificate .cert-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .75rem;
        gap: 1rem;
    }

    .certificate .logo-wrap img {
        height: 72px;
        object-fit: contain;
    }

    .certificate .cert-title { text-align: right; }

    .certificate .cert-title h2 {
        font-family: 'Amiri', serif;
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--cert-navy);
        margin: 0;
        letter-spacing: .02em;
    }

    .certificate .cert-title p {
        font-family: 'Cormorant Garamond', serif;
        font-size: .95rem;
        color: var(--cert-gold-dark);
        letter-spacing: .15em;
        text-transform: uppercase;
        margin: .1rem 0 0;
    }

    .certificate .divider-ornament {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .35rem;
    }

    .certificate .divider-ornament span {
        display: block;
        width: 1px;
        height: 28px;
        background: linear-gradient(to bottom, transparent, var(--cert-gold), transparent);
    }

    .certificate .divider-ornament svg { width: 40px; }

    .certificate .ornamental-rule {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: .9rem 0;
    }

    .certificate .rule-line {
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, transparent, var(--cert-gold), transparent);
    }

    .certificate .rule-diamond {
        width: 10px;
        height: 10px;
        background: var(--cert-gold);
        transform: rotate(45deg);
        flex-shrink: 0;
    }

    .certificate .rule-diamond::before {
        content: '';
        display: block;
        width: 6px;
        height: 6px;
        background: var(--cert-parchment);
        transform: rotate(45deg) translate(-50%, -50%);
        margin: 2px auto;
    }

    .certificate .trainee-section { margin: 1rem 0 .5rem; }

    .certificate .certify-text {
        font-family: 'Amiri', serif;
        font-size: 1.15rem;
        color: var(--cert-ink-soft);
        margin-bottom: .6rem;
    }

    .certificate .certify-text em {
        font-family: 'Cormorant Garamond', serif;
        font-style: italic;
        letter-spacing: .05em;
    }

    .certificate .trainee-name {
        font-family: 'Amiri', serif;
        font-size: 3.6rem;
        font-weight: 700;
        color: var(--cert-navy);
        margin: .1rem 0 .3rem;
        line-height: 1.1;
        text-shadow: 0 2px 12px rgba(13, 36, 68, .08);
        letter-spacing: .02em;
    }

    .certificate .name-underline {
        width: 320px;
        margin: 0 auto;
    }

    .certificate .name-underline svg {
        width: 100%;
        height: 12px;
    }

    .certificate .course-section { margin: .6rem 0 .5rem; }

    .certificate .completion-text {
        font-family: 'Amiri', serif;
        font-size: 1.1rem;
        color: var(--cert-ink-soft);
        margin-bottom: .6rem;
    }

    .certificate .course-name-ar {
        font-family: 'Amiri', serif;
        font-size: 2rem;
        font-weight: 700;
        color: var(--cert-navy-mid);
        margin: .3rem 0 .25rem;
        line-height: 1.3;
    }

    .certificate .course-name-en {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.1rem;
        font-style: italic;
        color: var(--cert-gold-dark);
        letter-spacing: .06em;
        margin: 0;
    }

    .certificate .cert-footer {
        display: grid;
        grid-template-columns: 1fr 1.4fr 1fr;
        align-items: end;
        gap: 1rem;
        margin-top: 1.25rem;
        padding: 0 8px 4px;
        position: relative;
        z-index: 6;
    }

    .certificate .footer-date {
        text-align: right;
        justify-self: start;
        padding-inline-end: 28px;
        padding-bottom: 6px;
        max-width: 100%;
    }

    .certificate .footer-org {
        text-align: center;
        justify-self: center;
        padding-bottom: 4px;
    }

    .certificate .footer-spacer {
        min-height: 1px;
        padding-inline-start: 28px;
    }

    .certificate .footer-label {
        font-family: 'Amiri', serif;
        font-size: .85rem;
        color: var(--cert-gold-dark);
        letter-spacing: .1em;
        text-transform: uppercase;
        margin: 0 0 .25rem;
    }

    .certificate .footer-value {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.1rem;
        color: var(--cert-ink);
        font-weight: 600;
        margin: 0;
        letter-spacing: .04em;
    }

    .certificate .footer-org-ar {
        font-family: 'Amiri', serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--cert-navy);
        margin: 0;
        text-align: center;
    }

    .certificate .footer-org-ar span { font-weight: 700; }

    .certificate .footer-org-en {
        font-family: 'Cormorant Garamond', serif;
        font-size: .85rem;
        letter-spacing: .15em;
        color: var(--cert-gold-dark);
        margin: .2rem 0 0;
        text-align: center;
    }

    .certificate .footer-seal {
        display: flex;
        justify-content: center;
    }

    .certificate .seal-ring {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 2px dashed rgba(184, 150, 12, .4);
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle, rgba(184, 150, 12, .04) 0%, transparent 70%);
    }

    .certificate .seal-ring img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        opacity: .82;
    }
</style>
