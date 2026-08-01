<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap');

    .academy-page {
        --ink: #061525;
        --ink-soft: #0e3a5c;
        --ink-mist: #1a547a;
        --line: #d4e0ec;
        --gold: #f0a202;
        --gold-soft: #fff0c8;
        --teal: #0b8f7f;
        --coral: #ff4d6d;
        --action: #ff3d7a;
        --action-deep: #e11d62;
        --accent-orange: #ff6b3d;
        --accent-purple: #8b5cf6;
        --accent-pink: #ec4899;
        --accent-mint: #12c8a0;
        --accent-blue: #3b82f6;
        --accent-sky: #0ea5e9;
        --muted: #5a6d82;
        --sand: #f0f4f8;
        --card: #ffffff;
        --glow: rgba(255, 61, 122, .16);
        --glow-teal: rgba(11, 143, 127, .14);
        --shadow: 0 22px 48px rgba(6, 21, 37, .12);
        --radius: 1.35rem;
        --radius-sm: .85rem;
        --font: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
        --display: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif;
        --page-max: min(92rem, 100%);
        background:
            radial-gradient(980px 420px at 100% -6%, rgba(255, 61, 122, .08), transparent 55%),
            radial-gradient(860px 400px at -6% 12%, rgba(139, 92, 246, .08), transparent 52%),
            radial-gradient(720px 360px at 70% 40%, rgba(18, 200, 160, .07), transparent 50%),
            radial-gradient(900px 420px at 40% 100%, rgba(59, 130, 246, .06), transparent 55%),
            linear-gradient(180deg, #f4f7fb 0%, #eef3f8 38%, #ffffff 100%);
        color: var(--ink);
        font-family: var(--font);
        width: 100%;
        max-width: 100%;
        overflow-x: clip;
    }
    .academy-page .display {
        font-family: var(--display);
        letter-spacing: -0.015em;
        font-weight: 800;
    }

    /* —— Section shell —— */
    .academy-section {
        max-width: var(--page-max);
        margin: 0 auto;
        padding: clamp(2.5rem, 5vw, 4.5rem) clamp(1rem, 3vw, 2rem);
        min-width: 0; width: 100%; box-sizing: border-box;
        position: relative;
    }
    .academy-section.is-tight { padding-block: clamp(2rem, 4vw, 3.25rem); }

    .academy-sec-head {
        display: grid;
        gap: 1rem;
        margin-bottom: 1.75rem;
        align-items: end;
    }
    @media (min-width: 768px) {
        .academy-sec-head {
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
            gap: 1.5rem 2rem;
        }
        .academy-sec-head .academy-sub { margin-bottom: 0; justify-self: end; text-align: end; }
    }
    .academy-kicker {
        color: var(--action); font-size: .72rem; font-weight: 800;
        letter-spacing: .16em; text-transform: uppercase; margin-bottom: .45rem;
        display: inline-flex; align-items: center; gap: .5rem;
    }
    .academy-kicker::before {
        content: ''; width: 1.4rem; height: 3px; border-radius: 99px;
        background: linear-gradient(90deg, var(--action), var(--accent-purple), var(--accent-mint));
    }
    .academy-section:nth-of-type(3n+1) .academy-kicker { color: var(--accent-purple); }
    .academy-section:nth-of-type(3n+2) .academy-kicker { color: var(--accent-mint); }
    .academy-section:nth-of-type(3n) .academy-kicker { color: var(--accent-blue); }
    .academy-h2 {
        font-size: clamp(1.55rem, 2.8vw, 2.45rem); font-weight: 800;
        margin: 0; line-height: 1.22; color: var(--ink);
    }
    .academy-sub { color: var(--muted); max-width: 32rem; margin: .45rem 0 0; line-height: 1.65; font-size: .95rem; }

    .academy-cta {
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .95rem 1.45rem; border-radius: 999px;
        background: linear-gradient(135deg, var(--action), var(--action-deep));
        color: #fff; font-weight: 800; text-decoration: none;
        box-shadow: 0 12px 30px rgba(255,61,122,.35);
        transition: transform .25s ease, box-shadow .25s ease, filter .2s;
    }
    .academy-cta:hover { transform: translateY(-3px) scale(1.02); filter: brightness(1.05); }
    .academy-cta-ghost {
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .95rem 1.3rem; border-radius: 999px;
        border: 1.5px solid rgba(255,255,255,.4); color: #fff; font-weight: 700; text-decoration: none;
        backdrop-filter: blur(8px);
        transition: background .2s, transform .2s;
    }
    .academy-cta-ghost:hover { background: rgba(255,255,255,.12); transform: translateY(-2px); }

    /* —— Snap sliders —— */
    .snap-slider-wrap {
        position: relative; padding-inline: 2.9rem; transition: padding-inline .2s ease;
        min-width: 0; max-width: 100%; width: 100%; box-sizing: border-box;
    }
    @media (max-width: 639px) {
        .snap-slider-wrap { padding-inline: 2.35rem; }
        /* Let JS equalize widths; only nudge a usable min size on small screens */
        .cat-slide {
            min-width: 13rem !important;
        }
    }
    .snap-slider-wrap.is-nav-hidden { padding-inline: 0; }
    /* Padding keeps card shadows inside the clip box so they fade into the page bg
       instead of being cut into a hard horizontal line. Negative margin preserves layout. */
    .snap-slider-viewport {
        overflow: hidden; width: 100%; max-width: 100%; min-width: 0;
        padding-block: 1.15rem 2.35rem;
        margin-block: -1.15rem -2.35rem;
    }
    .snap-slider {
        display: flex; gap: 1rem; width: max-content; max-width: none; min-width: 100%;
        overflow: visible; scroll-snap-type: none;
        padding: .15rem 0 .35rem; scrollbar-width: none; scroll-behavior: auto;
    }
    .snap-slider.is-transform-slider { transform: translate3d(0,0,0); }
    .snap-slider::-webkit-scrollbar { display: none; }
    .snap-slide { flex: 0 0 auto; box-sizing: border-box; }
    .snap-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 2.4rem; height: 2.4rem; border-radius: 999px; border: 0;
        background: var(--action); color: #fff; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 12px 26px rgba(255,61,122,.28); z-index: 6;
        padding: 0; margin: 0; line-height: 0; font-size: 0; overflow: hidden;
        transition: transform .2s, background .2s;
        pointer-events: auto;
    }
    .snap-nav:hover { background: var(--action-deep); transform: translateY(-50%) scale(1.07); }
    .snap-nav i {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        margin: 0 !important; padding: 0; font-size: .72rem; line-height: 1;
    }
    .snap-nav i::before { margin: 0 !important; line-height: 1; width: 1em; text-align: center; }
    .snap-nav.prev { inset-inline-start: 0; }
    .snap-nav.next { inset-inline-end: 0; }
    .snap-nav[hidden] { display: none !important; }
    .reviews-band .snap-nav { background: var(--gold); box-shadow: 0 12px 26px rgba(240,162,2,.3); }
    .reviews-band .snap-nav:hover { background: #e0b53a; }

    /* Categories — colorful chips; JS equalizes width to the widest card.
       Shadows use soft ink + a light color tint so they dissolve into --sand / page bg. */
    .cat-slide {
        display: inline-flex; flex-direction: row; align-items: center; justify-content: flex-start;
        gap: 1.15rem; text-align: start;
        width: max-content; min-width: 14rem; max-width: none;
        height: 4.6rem; box-sizing: border-box;
        padding: .85rem 1.15rem .85rem 1rem;
        border: 0; text-decoration: none; color: #fff;
        border-radius: 1.25rem;
        background: linear-gradient(145deg, var(--accent-purple), #6d28d9);
        box-shadow:
            0 8px 18px rgba(6, 21, 37, .08),
            0 16px 36px rgba(109, 40, 217, .14);
        position: relative; overflow: hidden;
        transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s, filter .2s;
    }
    .cat-slide::before { display: none; }
    .cat-slide:hover, .cat-slide.is-active {
        transform: translateY(-4px) scale(1.02);
        filter: brightness(1.05);
        box-shadow:
            0 12px 24px rgba(6, 21, 37, .1),
            0 22px 44px rgba(6, 21, 37, .12);
    }
    .cat-slide img {
        width: 1.85rem; height: 1.85rem; border-radius: 0; object-fit: contain;
        border: 0; box-shadow: none; background: transparent;
        flex-shrink: 0; transition: transform .35s ease;
        /* Force uploaded SVG/PNG icons to white on colored chips */
        filter: brightness(0) invert(1);
    }
    .cat-slide:hover img { transform: scale(1.08); }
    .cat-slide > span {
        min-width: 0; flex: 1 1 auto;
        text-align: start; display: block;
    }
    .cat-slide .cat-slide-title {
        display: block;
        font-weight: 800; font-size: .9rem; font-family: var(--display);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-align: start; line-height: 1.35; color: #fff;
    }
    .cat-slide.is-active { outline: 3px solid rgba(255,255,255,.55); outline-offset: 3px; }
    .cat-tone-0 {
        background: linear-gradient(145deg, #ff6b3d, #e85d04);
        box-shadow: 0 8px 18px rgba(6, 21, 37, .08), 0 16px 36px rgba(232, 93, 4, .14);
    }
    .cat-tone-1 {
        background: linear-gradient(145deg, #8b5cf6, #6d28d9);
        box-shadow: 0 8px 18px rgba(6, 21, 37, .08), 0 16px 36px rgba(109, 40, 217, .14);
    }
    .cat-tone-2 {
        background: linear-gradient(145deg, #ec4899, #db2777);
        box-shadow: 0 8px 18px rgba(6, 21, 37, .08), 0 16px 36px rgba(219, 39, 119, .14);
    }
    .cat-tone-3 {
        background: linear-gradient(145deg, #12c8a0, #0b8f7f);
        box-shadow: 0 8px 18px rgba(6, 21, 37, .08), 0 16px 36px rgba(11, 143, 127, .14);
    }
    .cat-tone-4 {
        background: linear-gradient(145deg, #3b82f6, #2563eb);
        box-shadow: 0 8px 18px rgba(6, 21, 37, .08), 0 16px 36px rgba(37, 99, 235, .14);
    }
    .cat-tone-5 {
        background: linear-gradient(145deg, #f0a202, #d97706);
        box-shadow: 0 8px 18px rgba(6, 21, 37, .08), 0 16px 36px rgba(217, 119, 6, .14);
    }
    .cat-slide-all {
        background: linear-gradient(160deg, var(--ink) 0%, var(--ink-soft) 100%) !important;
        color: #fff; padding-inline: 1.25rem;
        box-shadow: 0 8px 18px rgba(6, 21, 37, .08), 0 16px 36px rgba(6, 21, 37, .16) !important;
    }
    .cat-slide-all .cat-slide-title { color: #fff; }

    /* Filters / search (courses pages) */
    .academy-filters { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.1rem; }
    .academy-chip {
        display: inline-flex; align-items: center; padding: .5rem 1rem; border-radius: 999px;
        border: 1px solid var(--line); background: #fff; color: var(--ink-soft);
        font-size: .85rem; font-weight: 700; text-decoration: none;
        transition: background .2s, color .2s, border-color .2s, transform .2s;
    }
    .academy-chip:hover { transform: translateY(-1px); border-color: var(--action); color: var(--action); }
    .academy-chip.is-active {
        background: var(--action); border-color: var(--action); color: #fff;
        box-shadow: 0 8px 18px rgba(255,61,122,.25);
    }
    .academy-search { display: flex; gap: .5rem; margin-bottom: 1.4rem; }
    .academy-search input {
        flex: 1; min-width: 0; border: 1px solid var(--line); border-radius: 999px;
        padding: .85rem 1.15rem; background: #fff; font-family: inherit;
        transition: border-color .2s, box-shadow .2s;
    }
    .academy-search input:focus {
        outline: none; border-color: var(--action); box-shadow: 0 0 0 4px var(--glow);
    }
    .academy-search button {
        border: 0; border-radius: 999px; padding: .85rem 1.25rem;
        background: var(--action); color: #fff; font-weight: 800; cursor: pointer;
        font-family: inherit; transition: background .2s, transform .2s;
        box-shadow: 0 8px 18px rgba(255,61,122,.22);
    }
    .academy-search button:hover { background: var(--action-deep); transform: translateY(-1px); }

    /* Course grid — bento on large screens */
    .soni-grid {
        display: grid; grid-template-columns: 1fr; gap: 1.15rem;
        align-items: stretch;
        width: 100%;
        min-width: 0;
    }
    @media (min-width: 640px) { .soni-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (min-width: 1100px) { .soni-grid { grid-template-columns: repeat(3, minmax(0,1fr)); gap: 1.35rem; } }
    @media (min-width: 1500px) {
        .soni-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }
        .soni-grid.is-bento > .soni-card:first-child {
            grid-column: span 2; grid-row: span 1;
        }
        .soni-grid.is-bento > .soni-card:first-child .soni-card-media img { aspect-ratio: 21/10; }
    }
    .soni-card {
        display: flex; flex-direction: column; height: 100%;
        background: var(--card);
        border: 1px solid transparent;
        border-radius: calc(var(--radius) + .15rem);
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(6,21,37,.07);
        transition: transform .35s cubic-bezier(.2,.8,.2,1), box-shadow .35s ease;
        will-change: transform;
        transform-style: preserve-3d;
    }
    .soni-card:hover {
        transform: translateY(-7px);
        box-shadow: var(--shadow);
    }
    .soni-card-media { position: relative; display: block; flex-shrink: 0; overflow: hidden; }
    .soni-card-media-link { display: block; color: inherit; text-decoration: none; }
    .soni-card-media img {
        width: 100%; aspect-ratio: 16/10; object-fit: cover; background: #d5e0eb; display: block;
        clip-path: polygon(0 0, 100% 0, 100% 88%, 0 100%);
        transition: transform .55s cubic-bezier(.2,.8,.2,1);
    }
    .soni-card:hover .soni-card-media img { transform: scale(1.05); }
    /* Locale-aware logo: AR → left, EN → right (via inset-inline-end) */
    .soni-card-media::after {
        content: "";
        position: absolute;
        top: .7rem;
        inset-inline-end: .7rem;
        inset-inline-start: auto;
        width: min(34%, 5.85rem);
        aspect-ratio: 644 / 451;
        background: url("{{ asset('assets/images/academy_watermark.png') }}") center / contain no-repeat;
        opacity: {{ config('watermark.opacity', 0.38) }};
        pointer-events: none;
        z-index: 3;
    }
    .soni-card-badges {
        position: absolute; top: .75rem; inset-inline-start: .75rem;
        display: flex; flex-wrap: wrap; gap: .35rem; z-index: 1;
    }
    .soni-wish-btn {
        position: absolute; bottom: 1.15rem; inset-inline-start: .85rem; z-index: 2;
        width: 2.45rem; height: 2.45rem; border-radius: 999px; border: 1.5px solid rgba(255,255,255,.85);
        background: rgba(6,21,37,.55); color: #fff; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        backdrop-filter: blur(8px); box-shadow: 0 8px 18px rgba(6,21,37,.22);
        transition: transform .2s, background .2s, color .2s, border-color .2s;
        padding: 0; line-height: 1;
    }
    .soni-wish-btn i { font-size: .95rem; }
    .soni-wish-btn:hover { transform: scale(1.08); background: rgba(6,21,37,.78); }
    .soni-wish-btn.is-on {
        background: #fff; color: var(--action); border-color: #fff;
    }
    .soni-wish-btn.is-on:hover { background: #fff5f7; }
    .soni-wish-btn.is-busy { opacity: .7; pointer-events: none; }
    .soni-badge {
        background: rgba(6,21,37,.88); color: #fff; font-size: .68rem; font-weight: 800;
        padding: .3rem .6rem; border-radius: 999px; backdrop-filter: blur(6px);
    }
    .soni-badge.is-beginner {
        background: linear-gradient(135deg, #12c8a0, #0b8f7f);
        color: #fff;
        box-shadow: 0 6px 14px rgba(11, 143, 127, .28);
    }
    .soni-badge.is-intermediate {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        box-shadow: 0 6px 14px rgba(37, 99, 235, .28);
    }
    .soni-badge.is-advanced {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff;
        box-shadow: 0 6px 14px rgba(109, 40, 217, .28);
    }
    .soni-badge.is-all {
        background: linear-gradient(135deg, #f0a202, #d97706);
        color: #fff;
        box-shadow: 0 6px 14px rgba(217, 119, 6, .28);
    }
    .soni-badge-free {
        background: var(--accent-mint); color: #053b32;
        box-shadow: 0 6px 14px rgba(18,200,160,.3);
    }
    .soni-owned {
        margin: 0; font-size: .78rem; font-weight: 800; color: #0f766e;
        background: #ccfbf1; border-radius: 999px; padding: .28rem .65rem;
        width: fit-content; height: 1.55rem;
        display: inline-flex; align-items: center; box-sizing: border-box; flex-shrink: 0;
    }
    .soni-card-status {
        display: flex; align-items: center; justify-content: space-between; gap: .5rem;
        min-height: 1.55rem;
    }
    .soni-type-badge {
        display: inline-flex; align-items: center; flex-shrink: 0; height: 1.55rem;
        box-sizing: border-box; padding: .28rem .65rem; border-radius: 999px;
        background: #ede9fe; color: #6d28d9; font-size: .72rem; font-weight: 800;
    }
    .soni-category {
        margin: 0; font-size: .78rem; font-weight: 700; color: var(--ink-soft);
        max-width: 55%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-align: end;
    }
    .soni-category.is-empty { visibility: hidden; }
    .soni-card-body {
        padding: 1rem 1.15rem 1.2rem;
        display: flex; flex-direction: column; gap: .5rem;
        flex: 1; min-height: 0;
    }
    .soni-card-title {
        margin: 0; font-size: 1.02rem; font-weight: 800; line-height: 1.4;
        height: calc(1.4em * 2);
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        font-family: var(--display);
    }
    .soni-card-title a { color: inherit; text-decoration: none; }
    .soni-card-meta {
        display: flex; align-items: center; gap: .5rem;
        font-size: .8rem; color: var(--muted); height: 1.4rem; flex-shrink: 0;
    }
    .soni-stars { display: inline-flex; align-items: center; gap: .15rem; color: var(--gold); }
    .soni-stars .is-empty { color: #d5dbe5; }
    .soni-stars strong { color: var(--ink); margin-inline-start: .3rem; font-size: .78rem; }
    .soni-card-footer {
        margin-top: auto; display: flex; flex-direction: column; gap: .55rem;
        padding-top: .25rem; flex-shrink: 0;
    }
    .soni-card-actions {
        display: grid; grid-template-columns: 1fr 1fr; gap: .45rem;
        align-items: stretch; min-height: 2.5rem;
    }
    .soni-card-actions.is-single { grid-template-columns: 1fr; }
    .soni-btn-primary,
    .soni-btn-ghost {
        display: inline-flex; align-items: center; justify-content: center;
        width: 100%; height: 2.5rem; min-height: 2.5rem; padding: .55rem .5rem;
        border-radius: 999px; font-size: .8rem; font-weight: 800;
        text-decoration: none; text-align: center; line-height: 1.2;
        box-sizing: border-box; transition: transform .2s, background .2s, box-shadow .2s;
    }
    .soni-btn-primary {
        background: linear-gradient(135deg, var(--action) 0%, var(--action-deep) 100%);
        color: #fff; box-shadow: 0 10px 22px rgba(255,61,122,.28);
    }
    .soni-btn-primary:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ff5a90 0%, var(--action) 100%);
    }
    .soni-btn-ghost {
        border: 1px solid #f9a8d4; color: var(--action-deep); background: #fff5f8;
    }
    .soni-btn-ghost:hover { border-color: var(--action); background: #ffe4ef; }
    .soni-card-price-row { height: 1.55rem; display: flex; align-items: center; }
    .soni-card-price {
        margin: 0; font-weight: 800; color: var(--ink); height: 1.55rem;
        display: flex; align-items: center;
    }
    .soni-card-price.is-empty { visibility: hidden; }

    /* Reviews — angled dark band */
    .reviews-band {
        background:
            radial-gradient(900px 380px at 85% 0%, rgba(212,160,23,.22), transparent 55%),
            linear-gradient(155deg, #041018 0%, var(--ink) 45%, #0a3d4a 100%);
        color: #fff;
        padding: clamp(2.75rem, 5vw, 4.25rem) 0;
        position: relative;
        overflow: hidden;
        clip-path: polygon(0 3%, 100% 0, 100% 97%, 0 100%);
    }
    .reviews-band::before {
        content: ''; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(255,255,255,.055) 1px, transparent 1px);
        background-size: 20px 20px; opacity: .4;
    }
    .reviews-band .academy-section { padding-top: 1rem; padding-bottom: 1rem; position: relative; z-index: 1; }
    .reviews-band .academy-kicker { color: var(--gold); }
    .reviews-band .academy-kicker::before { background: var(--gold); }
    .reviews-band .academy-h2 { color: #fff; }
    .reviews-band .academy-sub { color: rgba(255,255,255,.7); }
    .review-card {
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 1.25rem 1.25rem 1.25rem .4rem;
        padding: 1.15rem 1.05rem;
        display: flex; flex-direction: column; gap: .7rem; min-height: 240px;
        box-sizing: border-box; position: relative;
        transition: transform .3s ease, background .3s, border-color .3s;
    }
    .review-card::before {
        content: '\201C'; position: absolute; top: .35rem; inset-inline-end: .85rem;
        font-size: 3.5rem; line-height: 1; color: rgba(212,160,23,.35);
        font-family: Georgia, serif; pointer-events: none;
    }
    .review-card:hover {
        transform: translateY(-5px);
        background: rgba(255,255,255,.11);
        border-color: rgba(212,160,23,.4);
    }
    .review-head { display: flex; align-items: center; gap: .55rem; min-width: 0; }
    .review-avatar {
        width: 2.5rem; height: 2.5rem; border-radius: .85rem; object-fit: cover;
        background: var(--gold); color: #fff; display: flex; align-items: center;
        justify-content: center; font-weight: 800; font-size: .7rem; flex-shrink: 0;
        border: 2px solid rgba(255,255,255,.2);
    }
    .review-head > div { min-width: 0; }
    .review-name {
        font-weight: 800; font-size: .84rem;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .review-stars { color: var(--gold); font-size: .68rem; letter-spacing: .04em; }
    .review-text {
        color: rgba(255,255,255,.9); font-size: .82rem; line-height: 1.55;
        flex: 1; display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical; overflow: hidden;
    }
    .review-course {
        border-top: 1px solid rgba(255,255,255,.12); padding-top: .65rem;
        display: flex; align-items: center; gap: .45rem;
        color: rgba(255,255,255,.85); font-size: .72rem; text-decoration: none; min-width: 0;
        transition: color .2s;
    }
    .review-course:hover { color: var(--gold); }
    .review-course span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .review-course i {
        width: 1.4rem; height: 1.4rem; border-radius: 999px;
        background: var(--gold); color: #fff; display: inline-flex;
        align-items: center; justify-content: center; font-size: .55rem; flex-shrink: 0;
    }

    /* Trainers — modern simple portrait cards */
    .trainer-card {
        width: 210px; min-width: 210px; max-width: 210px;
        display: flex; flex-direction: column;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 1.35rem;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(6,21,37,.07);
        transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s ease, border-color .3s;
    }
    @media (max-width: 480px) {
        .trainer-card {
            width: min(190px, calc(100vw - 5.5rem));
            min-width: min(190px, calc(100vw - 5.5rem));
            max-width: min(190px, calc(100vw - 5.5rem));
        }
    }
    .trainer-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(6,21,37,.12);
        border-color: rgba(11,143,127,.28);
    }
    .trainer-frame {
        display: flex; flex-direction: column;
        height: 100%;
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        overflow: hidden;
    }
    .trainer-frame::before { display: none; }
    .trainer-photo-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1.05;
        overflow: hidden;
        background: #e8eef5;
        border-radius: 0;
    }
    .trainer-photo {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform .5s ease;
    }
    .trainer-card:hover .trainer-photo { transform: scale(1.05); }
    .trainer-photo-veil {
        position: absolute; inset: auto 0 0 0; height: 42%;
        background: linear-gradient(180deg, transparent, rgba(6,21,37,.45));
        pointer-events: none;
    }
    .trainer-flag {
        position: absolute; top: .75rem; inset-inline-end: .75rem;
        width: 1.55rem; height: auto; border-radius: .35rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.22);
        z-index: 2; background: #fff;
    }
    .trainer-score {
        position: absolute;
        bottom: .7rem; inset-inline-start: .7rem;
        z-index: 2;
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .55rem;
        border-radius: .65rem;
        background: rgba(255,255,255,.95);
        color: var(--ink);
        font-weight: 800; font-size: .75rem;
        box-shadow: 0 6px 16px rgba(0,0,0,.16);
        white-space: nowrap;
    }
    .trainer-score i { color: var(--gold); font-size: .65rem; }
    .trainer-meta {
        display: flex; flex-direction: column; align-items: flex-start;
        text-align: start;
        padding: .95rem .95rem 1.1rem;
        gap: .2rem;
        min-width: 0;
        flex: 1;
    }
    .trainer-name {
        font-weight: 800; margin: 0; font-size: .95rem; line-height: 1.35;
        color: var(--ink); font-family: var(--display);
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        max-width: 100%;
    }
    .trainer-cat {
        color: var(--muted); font-size: .76rem; margin: 0; line-height: 1.35;
        font-weight: 600;
        max-width: 100%;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .trainer-stars {
        color: var(--gold); font-size: .7rem; margin-top: .55rem;
        display: inline-flex; align-items: center; gap: .1rem;
    }
    a.trainer-card-link { text-decoration: none; color: inherit; }
    .trainer-card-stats {
        display: flex; align-items: center; gap: .75rem;
        margin-top: .45rem; color: var(--ink-soft); font-size: .72rem; font-weight: 700;
    }
    .trainer-card-stats i { color: var(--action); margin-inline-end: .2rem; font-size: .65rem; }
    .trainer-grid {
        display: grid; gap: 1.15rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    @media (min-width: 640px) { .trainer-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .trainer-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    .trainer-grid .trainer-card {
        width: 100%; min-width: 0; max-width: none;
    }
    .trainer-profile {
        display: grid; gap: 1.5rem; align-items: center;
        background: #fff; border: 1px solid var(--line); border-radius: 1.6rem;
        padding: 1.25rem; box-shadow: 0 14px 36px rgba(6,21,37,.07);
    }
    @media (min-width: 768px) {
        .trainer-profile {
            grid-template-columns: minmax(220px, 280px) 1fr;
            padding: 1.5rem;
            gap: 2rem;
        }
    }
    .trainer-profile-photo {
        position: relative; border-radius: 1.25rem; overflow: hidden;
        aspect-ratio: 1; background: #e8eef5;
    }
    .trainer-profile-photo img:first-child {
        width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .trainer-profile-flag {
        position: absolute; top: .85rem; inset-inline-end: .85rem;
        width: 2rem; height: auto; border-radius: .4rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.22); background: #fff;
    }
    .trainer-profile-name {
        margin: .35rem 0 .85rem; font-size: clamp(1.6rem, 3vw, 2.35rem);
        font-weight: 800; color: var(--ink); line-height: 1.25;
    }
    .trainer-profile-stats {
        display: flex; flex-wrap: wrap; gap: .65rem; margin-bottom: .85rem;
    }
    .trainer-stat {
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .55rem .9rem; border-radius: 999px;
        background: #f3f7fb; color: var(--ink); font-size: .85rem; font-weight: 700;
    }
    .trainer-stat i { color: var(--action); }
    .trainer-profile-stars { color: var(--gold); font-size: .95rem; margin-bottom: .9rem; }
    .trainer-skill-chips {
        display: flex; flex-wrap: wrap; gap: .45rem; margin-bottom: 1rem;
    }
    .trainer-skill-chip {
        padding: .35rem .75rem; border-radius: 999px;
        background: #ede9fe; color: #6d28d9; font-size: .75rem; font-weight: 800;
    }
    .trainer-profile-lead {
        color: var(--muted); font-size: .95rem; line-height: 1.7; margin: 0 0 1.15rem;
        max-width: 42rem;
    }
    .academy-empty {
        text-align: center; padding: 3rem 1.25rem;
        background: #fff; border: 1px dashed var(--line); border-radius: 1.25rem;
        color: var(--muted);
    }
    .academy-empty p { margin: 0 0 1rem; font-weight: 700; }
    .academy-pagination { display: flex; justify-content: center; }

    /* Trust showcase — full-bleed certificate band + feature strip (no rounded section) */
    .trust-showcase {
        margin: 2.5rem 0 1rem;
        border-radius: 0;
        overflow: hidden;
        background:
            radial-gradient(700px 320px at 10% 0%, rgba(255,61,122,.18), transparent 55%),
            radial-gradient(600px 280px at 90% 100%, rgba(212,160,23,.16), transparent 50%),
            linear-gradient(145deg, #061525 0%, #0a2f45 55%, #0c3d48 100%);
        color: #fff;
        box-shadow: none;
    }
    .trust-showcase-inner {
        padding: clamp(1.4rem, 3vw, 2.25rem);
        display: flex; flex-direction: column; gap: 1.5rem;
        max-width: var(--page-max);
        margin: 0 auto;
        width: 100%;
        box-sizing: border-box;
    }
    .trust-showcase-main {
        display: grid; gap: 1.75rem; align-items: center;
    }
    @media (min-width: 900px) {
        .trust-showcase-main {
            grid-template-columns: .95fr 1.05fr;
            gap: 2.25rem;
        }
    }
    .trust-showcase-kicker {
        margin: 0 0 .45rem; color: var(--gold); font-size: .78rem; font-weight: 800;
        letter-spacing: .04em;
    }
    .trust-showcase-title {
        margin: 0 0 .65rem; font-size: clamp(1.45rem, 2.6vw, 2.1rem); font-weight: 800; line-height: 1.3;
    }
    .trust-showcase-sub {
        margin: 0 0 1.25rem; color: rgba(255,255,255,.72); font-size: .95rem; line-height: 1.7;
        max-width: 34rem;
    }
    .trust-highlights { display: grid; gap: 1rem; margin-bottom: 1.35rem; }
    .trust-highlight {
        display: grid; grid-template-columns: auto 1fr; gap: .85rem; align-items: start;
    }
    .trust-highlight-icon {
        width: 2.85rem; height: 2.85rem; border-radius: 999px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.05rem; flex-shrink: 0;
    }
    .trust-highlight-icon.is-cert {
        background: rgba(212,160,23,.18); color: var(--gold);
        border: 1px solid rgba(212,160,23,.35);
    }
    .trust-highlight-icon.is-guarantee {
        background: rgba(18,200,160,.16); color: #5eead4;
        border: 1px solid rgba(18,200,160,.35);
    }
    .trust-highlight h3 {
        margin: 0 0 .25rem; font-size: 1rem; font-weight: 800; color: #99f6e4;
        font-family: var(--display);
    }
    .trust-highlight p {
        margin: 0; color: rgba(255,255,255,.78); font-size: .88rem; line-height: 1.6;
    }
    .trust-showcase-cta {
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .95rem 1.45rem; border-radius: 999px;
        background: linear-gradient(135deg, var(--action), var(--action-deep));
        color: #fff; font-weight: 800; text-decoration: none;
        box-shadow: 0 14px 30px rgba(255,61,122,.35);
        transition: transform .2s, filter .2s;
    }
    .trust-showcase-cta:hover { transform: translateY(-2px); filter: brightness(1.05); color: #fff; }

    /* Real certificate scaled into the trust band (container size stays fixed) */
    .trust-cert-preview {
        display: flex; justify-content: center; width: 100%;
    }
    .trust-cert-live {
        width: min(100%, 28rem);
        aspect-ratio: 900 / 640;
        overflow: hidden;
        border-radius: 1.15rem;
        box-shadow: 0 24px 50px rgba(0,0,0,.35);
        background: #FDFAF3;
        /* LTR so scale origin/geometry stay predictable on RTL pages */
        direction: ltr;
        position: relative;
    }
    .trust-cert-live-scale {
        position: absolute;
        top: 0;
        left: 0;
        width: 900px;
        transform-origin: top left;
        transform: scale(0.466);
        pointer-events: none;
        user-select: none;
        direction: rtl;
    }
    .trust-cert-live .certificate {
        box-shadow: none;
        width: 900px;
        min-height: 640px;
        height: 640px;
        padding: 48px 64px 52px;
    }
    .trust-cert-live .certificate .trainee-name {
        font-size: 2.6rem;
    }
    .trust-cert-live .certificate .course-name-ar {
        font-size: 1.55rem;
    }

    .trust-feature-bar {
        display: grid; gap: .85rem;
        grid-template-columns: 1fr;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 0;
        padding: 1rem 1.1rem;
    }
    @media (min-width: 700px) {
        .trust-feature-bar { grid-template-columns: repeat(2, minmax(0,1fr)); }
    }
    @media (min-width: 1100px) {
        .trust-feature-bar { grid-template-columns: repeat(4, minmax(0,1fr)); gap: 1rem; }
    }
    .trust-feature {
        display: grid; grid-template-columns: auto 1fr; gap: .7rem; align-items: start;
        color: #fff;
    }
    .trust-feature i {
        width: 2.2rem; height: 2.2rem; border-radius: .7rem;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.1); color: var(--gold); font-size: .95rem;
        border: 1px solid rgba(255,255,255,.12);
    }
    .trust-feature span {
        display: flex; flex-direction: column; gap: .2rem;
        font-size: .88rem; font-weight: 800; line-height: 1.35;
    }
    .trust-feature small {
        font-size: .72rem; font-weight: 600; color: rgba(255,255,255,.65);
        line-height: 1.45;
    }

    .trust-grid {
        display: grid; gap: .85rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px) { .trust-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (min-width: 1100px) { .trust-grid { grid-template-columns: repeat(4, minmax(0,1fr)); } }
    .trust-item {
        display: grid; grid-template-columns: auto 1fr; gap: .85rem 1rem; align-items: start;
        background: #fff;
        border: 0;
        border-radius: 1.2rem;
        padding: 1.2rem 1.15rem;
        box-shadow: 0 8px 24px rgba(6,21,37,.06);
        border-inline-start: 4px solid var(--action);
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .trust-item:nth-child(1) { border-inline-start-color: var(--action); }
    .trust-item:nth-child(2) { border-inline-start-color: var(--accent-purple); }
    .trust-item:nth-child(3) { border-inline-start-color: var(--accent-mint); }
    .trust-item:nth-child(4) { border-inline-start-color: var(--accent-blue); }
    .trust-item:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
    .trust-icon {
        width: 2.85rem; height: 2.85rem; border-radius: 1rem;
        background: #ffe4ef; color: var(--action);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.05rem; grid-row: span 2;
        transition: transform .3s ease, background .3s;
    }
    .trust-item:nth-child(2) .trust-icon { background: #ede9fe; color: var(--accent-purple); }
    .trust-item:nth-child(3) .trust-icon { background: #d1fae5; color: var(--accent-mint); }
    .trust-item:nth-child(4) .trust-icon { background: #dbeafe; color: var(--accent-blue); }
    .trust-item:hover .trust-icon { transform: scale(1.06); }
    .trust-item h3 { font-weight: 800; margin: 0; font-size: .98rem; font-family: var(--display); }
    .trust-item p { margin: 0; color: var(--muted); font-size: .84rem; line-height: 1.55; grid-column: 2; }

    /* How it works — connected timeline */
    .academy-steps {
        display: grid; gap: 0; position: relative;
    }
    @media (min-width: 768px) {
        .academy-steps {
            grid-template-columns: repeat(3, minmax(0,1fr));
            gap: 1.25rem;
        }
        .academy-steps::before {
            content: ''; position: absolute; top: 2.1rem;
            inset-inline: 12% 12%; height: 2px;
            background: linear-gradient(90deg, var(--action), var(--accent-purple), var(--accent-mint), var(--accent-blue));
            opacity: .45; z-index: 0;
        }
    }
    .academy-step {
        position: relative; z-index: 1;
        padding: 1.5rem 1.25rem 1.4rem;
        border-radius: 1.35rem;
        background: #fff;
        box-shadow: 0 10px 28px rgba(6,21,37,.07);
        margin-bottom: .85rem;
        transition: transform .3s ease, box-shadow .3s ease;
    }
    @media (min-width: 768px) { .academy-step { margin-bottom: 0; } }
    .academy-step:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
    .academy-step-num {
        width: 2.75rem; height: 2.75rem; border-radius: 999px;
        display: inline-flex; align-items: center; justify-content: center;
        font-family: var(--display); font-size: .95rem; color: #fff;
        background: linear-gradient(135deg, var(--action), var(--accent-purple));
        margin-bottom: .85rem; font-weight: 800;
        box-shadow: 0 8px 18px rgba(255,61,122,.25);
    }
    .academy-step:nth-child(2) .academy-step-num {
        background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
        box-shadow: 0 8px 18px rgba(139,92,246,.25);
    }
    .academy-step:nth-child(3) .academy-step-num {
        background: linear-gradient(135deg, var(--accent-mint), var(--teal));
        box-shadow: 0 8px 18px rgba(18,200,160,.25);
    }

    /* Banner — full-bleed skewed CTA */
    .academy-banner {
        margin: 0 clamp(1rem, 3vw, 2rem) clamp(2.5rem, 5vw, 4rem);
        max-width: var(--page-max); margin-inline: auto;
        border-radius: 1.75rem;
        padding: clamp(2rem, 4vw, 3rem) clamp(1.35rem, 3vw, 2.5rem);
        background:
            radial-gradient(700px 260px at 100% 0%, rgba(255,61,122,.35), transparent 55%),
            radial-gradient(500px 220px at 0% 100%, rgba(139,92,246,.28), transparent 55%),
            linear-gradient(125deg, var(--ink) 0%, #1a1040 45%, #0c4a55 100%);
        color: #fff;
        display: grid; gap: 1.25rem;
        align-items: center;
        box-shadow: 0 28px 56px rgba(6,21,37,.24);
        position: relative; overflow: hidden;
    }
    @media (min-width: 768px) {
        .academy-banner { grid-template-columns: 1.4fr auto; }
    }
    .academy-banner::after {
        content: ''; position: absolute; inset: auto -50px -60px auto;
        width: 220px; height: 220px; border-radius: 999px;
        background: rgba(255,255,255,.06); pointer-events: none;
    }

    .reveal {
        opacity: 0; transform: translateY(26px);
        transition: opacity .7s cubic-bezier(.2,.8,.2,1), transform .7s cubic-bezier(.2,.8,.2,1);
    }
    .reveal.is-in { opacity: 1; transform: none; }

    .academy-back {
        display: inline-flex; align-items: center; gap: .45rem;
        color: var(--ink); font-weight: 800; font-size: .9rem; text-decoration: none;
        margin-bottom: 1.25rem; transition: color .2s;
    }
    .academy-back:hover { color: var(--action); }
    .academy-toolbar {
        display: flex; flex-direction: column; gap: .85rem; margin-bottom: 1.5rem;
        width: 100%; min-width: 0;
    }
    .academy-toolbar .academy-search,
    .academy-toolbar .academy-filters {
        width: 100%; min-width: 0;
    }
    @media (min-width: 900px) and (max-width: 1199px) {
        .academy-chip { padding: .45rem .8rem; font-size: .8rem; }
    }
    .academy-more-wrap { display: flex; justify-content: center; margin-top: 2rem; }
    .academy-more-btn {
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .95rem 1.7rem; border-radius: 999px;
        background: linear-gradient(135deg, var(--action), var(--action-deep));
        color: #fff; font-weight: 800; text-decoration: none;
        box-shadow: 0 14px 30px rgba(255,61,122,.28);
        transition: transform .25s ease, background .25s, filter .2s;
    }
    .academy-more-btn:hover {
        transform: translateY(-3px);
        filter: brightness(1.06); color: #fff;
    }

    .academy-dots {
        position: fixed; inset-inline-end: 1.1rem; top: 50%; transform: translateY(-50%);
        z-index: 30; display: none; flex-direction: column; gap: .55rem;
    }
    @media (min-width: 1280px) { .academy-dots { display: flex; } }
    .academy-dot {
        width: .65rem; height: .65rem; border-radius: 999px; border: 0;
        background: rgba(6,21,37,.45); cursor: pointer; padding: 0;
        box-shadow: 0 0 0 2px rgba(255,255,255,.85);
        transition: transform .2s, background .2s, height .2s, width .2s;
    }
    .academy-dot:hover { background: var(--action); transform: scale(1.12); }
    .academy-dot.is-active {
        background: var(--action); width: .65rem; height: 1.55rem; border-radius: .85rem;
        box-shadow: 0 0 0 2px rgba(255,255,255,.9), 0 4px 12px rgba(255,61,122,.35);
    }
</style>
