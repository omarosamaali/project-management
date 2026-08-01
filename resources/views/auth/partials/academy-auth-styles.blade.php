{{-- Shared academy auth shell styles --}}
<style>
    .academy-auth-page {
        --ink: #061525;
        --ink-soft: #0e3a5c;
        --line: #d4e0ec;
        --gold: #d4a017;
        --teal: #0b8f7f;
        --teal-deep: #087a6c;
        --muted: #5a6d82;
        --sand: #f0f4f8;
        --font: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
        --display: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif;
        font-family: var(--font);
        color: var(--ink);
        min-height: calc(100dvh - 4.35rem);
        padding: clamp(1.25rem, 3vw, 2.5rem) clamp(1rem, 3vw, 2rem) clamp(2rem, 4vw, 3.5rem);
        background:
            radial-gradient(900px 420px at 100% -10%, rgba(212, 160, 23, .12), transparent 55%),
            radial-gradient(800px 380px at -10% 20%, rgba(11, 143, 127, .1), transparent 50%),
            linear-gradient(180deg, #f0f4f8 0%, #e8eef5 45%, #f7fafc 100%);
        box-sizing: border-box;
    }
    .academy-auth-page *,
    .academy-auth-page *::before,
    .academy-auth-page *::after { box-sizing: border-box; }

    .academy-auth-shell {
        max-width: 68rem;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr;
        background: #fff;
        border-radius: 1.35rem;
        overflow: hidden;
        border: 1px solid rgba(6, 21, 37, .08);
        box-shadow: 0 28px 60px rgba(6, 21, 37, .12);
        min-height: min(640px, calc(100dvh - 8rem));
    }
    .academy-auth-shell--wide { max-width: 76rem; }
    .academy-auth-shell--narrow { max-width: 32rem; min-height: auto; }
    @media (min-width: 900px) {
        .academy-auth-shell:not(.academy-auth-shell--narrow) {
            grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
        }
    }

    .academy-auth-visual {
        display: none;
        position: relative;
        min-height: 100%;
        background: #061525;
        overflow: hidden;
    }
    @media (min-width: 900px) {
        .academy-auth-shell:not(.academy-auth-shell--narrow) .academy-auth-visual { display: block; }
    }
    .academy-auth-visual__bg {
        position: absolute; inset: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        opacity: .55;
        transform: scale(1.04);
    }
    .academy-auth-visual__veil {
        position: absolute; inset: 0;
        background:
            linear-gradient(165deg, rgba(6, 21, 37, .55) 0%, rgba(6, 21, 37, .78) 55%, rgba(11, 143, 127, .45) 100%);
    }
    .academy-auth-visual__content {
        position: relative; z-index: 1;
        height: 100%;
        display: flex; flex-direction: column;
        justify-content: flex-end;
        gap: 1rem;
        padding: clamp(1.75rem, 3vw, 2.75rem);
        color: #fff;
    }
    .academy-auth-kicker {
        display: inline-flex; align-items: center; gap: .5rem;
        color: #f7e7b8; font-size: .72rem; font-weight: 800;
        letter-spacing: .14em; text-transform: uppercase;
    }
    .academy-auth-kicker::before {
        content: ''; width: 1.25rem; height: 3px; border-radius: 99px;
        background: linear-gradient(90deg, var(--gold), var(--teal));
    }
    .academy-auth-visual__content h2 {
        font-family: var(--display);
        font-size: clamp(1.65rem, 2.6vw, 2.35rem);
        font-weight: 800; line-height: 1.25; margin: 0;
        letter-spacing: -.015em;
    }
    .academy-auth-visual__content p {
        margin: 0; color: rgba(255,255,255,.82);
        font-size: 1rem; line-height: 1.65; max-width: 22rem;
    }
    .academy-auth-visual__badge {
        display: inline-flex; align-items: center; gap: .45rem;
        width: fit-content;
        padding: .45rem .85rem; border-radius: 999px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.18);
        font-size: .8rem; font-weight: 700;
        backdrop-filter: blur(8px);
    }

    .academy-auth-form {
        padding: clamp(1.5rem, 3vw, 2.75rem);
        display: flex; flex-direction: column; gap: 1.35rem;
        background: #fff;
    }
    .academy-auth-form__head h1 {
        font-family: var(--display);
        font-size: clamp(1.55rem, 2.4vw, 2.1rem);
        font-weight: 800; color: var(--ink);
        margin: .35rem 0 .4rem; line-height: 1.25;
    }
    .academy-auth-form__head p {
        margin: 0; color: var(--muted); font-size: .95rem; line-height: 1.6;
    }
    .academy-auth-form label.block,
    .academy-auth-form .academy-auth-label {
        display: block;
        font-size: .875rem; font-weight: 700; color: #1f3347;
        margin-bottom: .4rem;
    }
    .academy-auth-form input[type="text"],
    .academy-auth-form input[type="email"],
    .academy-auth-form input[type="password"],
    .academy-auth-form input[type="tel"],
    .academy-auth-form input[type="number"],
    .academy-auth-form select,
    .academy-auth-form textarea {
        width: 100%;
        border: 1px solid #cfd9e6 !important;
        border-radius: .85rem !important;
        padding: .85rem 1rem !important;
        background: #fff !important;
        color: #0f172a !important;
        box-shadow: none !important;
        transition: border-color .18s, box-shadow .18s;
    }
    .academy-auth-form input::placeholder,
    .academy-auth-form textarea::placeholder {
        color: #0f172a !important;
        opacity: 0.35;
    }
    .academy-auth-form input::-webkit-input-placeholder,
    .academy-auth-form textarea::-webkit-input-placeholder {
        color: #0f172a !important;
        opacity: 0.35;
    }
    .academy-auth-form input::-moz-placeholder,
    .academy-auth-form textarea::-moz-placeholder {
        color: #0f172a !important;
        opacity: 0.35;
    }
    .academy-auth-form input:focus,
    .academy-auth-form select:focus,
    .academy-auth-form textarea:focus {
        border-color: var(--teal) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(11, 143, 127, .18) !important;
    }
    .academy-auth-form .rounded-md { border-radius: .85rem !important; }

    .academy-auth-choice {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .95rem 1rem;
        border: 1.5px solid #d4e0ec;
        border-radius: .95rem;
        cursor: pointer;
        transition: border-color .18s, background .18s, box-shadow .18s;
        background: #fff;
    }
    .academy-auth-choice:hover { border-color: rgba(11, 143, 127, .45); }
    .academy-auth-choice.is-selected {
        border-color: var(--teal);
        background: rgba(11, 143, 127, .06);
        box-shadow: 0 0 0 3px rgba(11, 143, 127, .1);
    }
    .academy-auth-choice input { accent-color: #0b8f7f; margin-top: .15rem; }

    .academy-auth-submit {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        width: 100%;
        padding: 1rem 1.25rem;
        border: 0; border-radius: 999px;
        background: linear-gradient(135deg, #0b8f7f, #0e6e63);
        color: #fff; font-weight: 800; font-size: 1.05rem;
        box-shadow: 0 14px 28px rgba(11, 143, 127, .32);
        cursor: pointer;
        transition: transform .2s, filter .2s, box-shadow .2s;
    }
    .academy-auth-submit:hover {
        filter: brightness(1.05);
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(11, 143, 127, .38);
    }
    .academy-auth-link {
        color: var(--teal); font-weight: 800; text-decoration: none;
    }
    .academy-auth-link:hover { color: var(--teal-deep); text-decoration: underline; }
    .academy-auth-foot {
        text-align: center; color: var(--muted); font-size: .95rem;
        padding-top: .25rem;
    }
    .academy-auth-alert {
        padding: .9rem 1rem; border-radius: .85rem; font-size: .875rem;
    }
    .academy-auth-alert--ok {
        background: rgba(11, 143, 127, .1); border: 1px solid rgba(11, 143, 127, .25); color: #0a5c52;
    }
    .academy-auth-alert--err {
        background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;
    }
    .academy-auth-alert--warn {
        background: #fffbeb; border: 1px solid #fde68a; color: #92400e;
    }

    /* Select2 / file inputs inside auth */
    .academy-auth-form .select2-container--default .select2-selection--single {
        height: 3.05rem !important;
        border: 1px solid #cfd9e6 !important;
        border-radius: .85rem !important;
        padding: .35rem .35rem !important;
    }
    .academy-auth-form .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--teal) !important;
    }
    .academy-auth-form input[type="file"] {
        width: 100%;
        font-size: .875rem; color: var(--muted);
    }
    .academy-auth-form input[type="file"]::file-selector-button {
        margin-inline-end: .75rem;
        padding: .55rem 1rem;
        border: 0; border-radius: .65rem;
        background: var(--ink); color: #fff; font-weight: 700;
        cursor: pointer;
    }
    .academy-auth-form input[type="checkbox"] {
        accent-color: #0b8f7f;
        width: 1.1rem; height: 1.1rem;
        border-radius: .3rem;
    }
</style>
