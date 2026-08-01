<style>
    @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@600;700;800&display=swap');

    .academy-page {
        --ink: #061525;
        --ink-soft: #0e3a5c;
        --ink-mist: #1a547a;
        --line: #d4e0ec;
        --gold: #d4a017;
        --gold-soft: #f7e7b8;
        --teal: #0b8f7f;
        --coral: #e85d4c;
        --muted: #5a6d82;
        --sand: #f0f4f8;
        --card: #ffffff;
        --glow: rgba(11, 143, 127, .16);
        --shadow: 0 22px 48px rgba(6, 21, 37, .12);
        --radius: 1.35rem;
        --radius-sm: .85rem;
        --font: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
        --display: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif;
        --page-max: min(92rem, 100%);
        background:
            radial-gradient(1100px 480px at 100% -8%, rgba(212, 160, 23, .11), transparent 55%),
            radial-gradient(900px 420px at -8% 18%, rgba(11, 143, 127, .09), transparent 52%),
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
        color: var(--teal); font-size: .72rem; font-weight: 800;
        letter-spacing: .16em; text-transform: uppercase; margin-bottom: .45rem;
        display: inline-flex; align-items: center; gap: .5rem;
    }
    .academy-kicker::before {
        content: ''; width: 1.4rem; height: 3px; border-radius: 99px;
        background: linear-gradient(90deg, var(--gold), var(--teal));
    }
    .academy-h2 {
        font-size: clamp(1.55rem, 2.8vw, 2.45rem); font-weight: 800;
        margin: 0; line-height: 1.22; color: var(--ink);
    }
    .academy-sub { color: var(--muted); max-width: 32rem; margin: .45rem 0 0; line-height: 1.65; font-size: .95rem; }

    .academy-cta {
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .95rem 1.45rem; border-radius: 999px;
        background: #fff; color: var(--ink); font-weight: 800; text-decoration: none;
        box-shadow: 0 12px 30px rgba(0,0,0,.2);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .academy-cta:hover { transform: translateY(-3px) scale(1.02); }
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
        .cat-slide {
            width: min(168px, calc(100vw - 5.5rem)) !important;
            min-width: min(168px, calc(100vw - 5.5rem)) !important;
            max-width: min(168px, calc(100vw - 5.5rem)) !important;
        }
    }
    .snap-slider-wrap.is-nav-hidden { padding-inline: 0; }
    .snap-slider-viewport { overflow: hidden; width: 100%; max-width: 100%; min-width: 0; }
    .snap-slider {
        display: flex; gap: 1rem; width: 100%; max-width: 100%; min-width: 0;
        overflow-x: hidden; scroll-snap-type: none;
        padding: .4rem 0 1rem; scrollbar-width: none; scroll-behavior: auto;
    }
    .snap-slider::-webkit-scrollbar { display: none; }
    .snap-slide { flex: 0 0 auto; box-sizing: border-box; }
    .snap-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 2.4rem; height: 2.4rem; border-radius: 999px; border: 0;
        background: var(--ink); color: #fff; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 12px 26px rgba(6,21,37,.22); z-index: 5;
        padding: 0; margin: 0; line-height: 0; font-size: 0; overflow: hidden;
        transition: transform .2s, background .2s;
    }
    .snap-nav:hover { background: var(--teal); transform: translateY(-50%) scale(1.07); }
    .snap-nav i {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        margin: 0 !important; padding: 0; font-size: .72rem; line-height: 1;
    }
    .snap-nav i::before { margin: 0 !important; line-height: 1; width: 1em; text-align: center; }
    .snap-nav.prev { inset-inline-start: 0; }
    .snap-nav.next { inset-inline-end: 0; }
    .snap-nav[hidden] { display: none !important; }
    .reviews-band .snap-nav { background: var(--gold); }
    .reviews-band .snap-nav:hover { background: #e0b53a; }

    /* Categories — tall icon tiles */
    .cat-slide {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: .85rem; text-align: center;
        width: 168px; min-width: 168px; max-width: 168px;
        height: 210px; padding: 1.35rem .9rem 1.25rem;
        border: 0; text-decoration: none; color: inherit;
        border-radius: 1.5rem 1.5rem 1.1rem 1.1rem;
        background:
            linear-gradient(165deg, #ffffff 0%, #f3f8fb 70%, #e8f4f2 100%);
        box-shadow: 0 10px 28px rgba(6,21,37,.08);
        position: relative; overflow: hidden;
        transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s;
    }
    .cat-slide::before {
        content: ''; position: absolute; inset: 0 0 auto 0; height: 4px;
        background: linear-gradient(90deg, var(--teal), var(--gold));
    }
    .cat-slide:hover, .cat-slide.is-active {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(6,21,37,.14);
    }
    .cat-slide img {
        width: 4.25rem; height: 4.25rem; border-radius: 1.15rem; object-fit: cover;
        border: 3px solid #fff; box-shadow: 0 8px 20px rgba(6,21,37,.12);
        flex-shrink: 0; transition: transform .35s ease;
    }
    .cat-slide:hover img { transform: scale(1.08) rotate(-4deg); }
    .cat-slide > span { min-width: 0; width: 100%; text-align: center; }
    .cat-slide .cat-slide-title {
        display: block; font-weight: 800; font-size: .88rem; font-family: var(--display);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        text-align: center;
    }
    .cat-slide.is-active { outline: 2px solid var(--teal); outline-offset: 3px; }
    .cat-slide-all {
        background: linear-gradient(160deg, var(--ink) 0%, var(--ink-soft) 100%);
        color: #fff;
    }
    .cat-slide-all::before { background: var(--gold); }
    .cat-slide-all .cat-slide-title { color: #fff; }

    /* Filters / search (courses pages) */
    .academy-filters { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.1rem; }
    .academy-chip {
        display: inline-flex; align-items: center; padding: .5rem 1rem; border-radius: 999px;
        border: 1px solid var(--line); background: #fff; color: var(--ink-soft);
        font-size: .85rem; font-weight: 700; text-decoration: none;
        transition: background .2s, color .2s, border-color .2s, transform .2s;
    }
    .academy-chip:hover { transform: translateY(-1px); border-color: var(--teal); }
    .academy-chip.is-active { background: var(--ink); border-color: var(--ink); color: #fff; }
    .academy-search { display: flex; gap: .5rem; margin-bottom: 1.4rem; }
    .academy-search input {
        flex: 1; min-width: 0; border: 1px solid var(--line); border-radius: 999px;
        padding: .85rem 1.15rem; background: #fff; font-family: inherit;
        transition: border-color .2s, box-shadow .2s;
    }
    .academy-search input:focus {
        outline: none; border-color: var(--teal); box-shadow: 0 0 0 4px var(--glow);
    }
    .academy-search button {
        border: 0; border-radius: 999px; padding: .85rem 1.25rem;
        background: var(--ink); color: #fff; font-weight: 800; cursor: pointer;
        font-family: inherit; transition: background .2s, transform .2s;
    }
    .academy-search button:hover { background: var(--teal); transform: translateY(-1px); }

    /* Course grid — bento on large screens */
    .soni-grid {
        display: grid; grid-template-columns: 1fr; gap: 1.15rem;
        align-items: stretch;
    }
    @media (min-width: 640px) { .soni-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (min-width: 1024px) { .soni-grid { grid-template-columns: repeat(3, minmax(0,1fr)); gap: 1.35rem; } }
    @media (min-width: 1400px) {
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
    .soni-card-media img {
        width: 100%; aspect-ratio: 16/10; object-fit: cover; background: #d5e0eb; display: block;
        clip-path: polygon(0 0, 100% 0, 100% 88%, 0 100%);
        transition: transform .55s cubic-bezier(.2,.8,.2,1);
    }
    .soni-card:hover .soni-card-media img { transform: scale(1.05); }
    .soni-card-badges {
        position: absolute; top: .75rem; inset-inline-start: .75rem;
        display: flex; flex-wrap: wrap; gap: .35rem; z-index: 1;
    }
    .soni-badge {
        background: rgba(6,21,37,.88); color: #fff; font-size: .68rem; font-weight: 800;
        padding: .3rem .6rem; border-radius: 999px; backdrop-filter: blur(6px);
    }
    .soni-badge-free { background: var(--coral); color: #fff; }
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
        background: #e4f6f3; color: var(--teal); font-size: .72rem; font-weight: 800;
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
        background: linear-gradient(135deg, var(--ink) 0%, var(--ink-soft) 100%);
        color: #fff; box-shadow: 0 8px 18px rgba(6,21,37,.16);
    }
    .soni-btn-primary:hover { transform: translateY(-1px); background: var(--teal); }
    .soni-btn-ghost {
        border: 1px solid var(--line); color: var(--ink); background: #fff;
    }
    .soni-btn-ghost:hover { border-color: var(--ink); background: #f8fafc; }
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

    /* Trust — horizontal ribbon tiles */
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
        border-inline-start: 4px solid var(--teal);
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .trust-item:nth-child(2) { border-inline-start-color: var(--gold); }
    .trust-item:nth-child(3) { border-inline-start-color: var(--ink-soft); }
    .trust-item:nth-child(4) { border-inline-start-color: var(--coral); }
    .trust-item:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
    .trust-icon {
        width: 2.85rem; height: 2.85rem; border-radius: 1rem;
        background: var(--sand); color: var(--ink);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.05rem; grid-row: span 2;
        transition: transform .3s ease, background .3s;
    }
    .trust-item:hover .trust-icon { transform: scale(1.06); background: #e4f6f3; color: var(--teal); }
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
            background: linear-gradient(90deg, var(--gold), var(--teal), var(--ink-soft));
            opacity: .35; z-index: 0;
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
        background: linear-gradient(135deg, var(--ink), var(--teal));
        margin-bottom: .85rem; font-weight: 800;
        box-shadow: 0 8px 18px rgba(6,21,37,.18);
    }

    /* Banner — full-bleed skewed CTA */
    .academy-banner {
        margin: 0 clamp(1rem, 3vw, 2rem) clamp(2.5rem, 5vw, 4rem);
        max-width: var(--page-max); margin-inline: auto;
        border-radius: 1.75rem;
        padding: clamp(2rem, 4vw, 3rem) clamp(1.35rem, 3vw, 2.5rem);
        background:
            radial-gradient(700px 260px at 100% 0%, rgba(212,160,23,.3), transparent 55%),
            linear-gradient(125deg, var(--ink) 0%, #0c4a55 55%, var(--teal) 140%);
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
    .academy-back:hover { color: var(--teal); }
    .academy-toolbar { display: flex; flex-direction: column; gap: .85rem; margin-bottom: 1.5rem; }
    .academy-more-wrap { display: flex; justify-content: center; margin-top: 2rem; }
    .academy-more-btn {
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .95rem 1.7rem; border-radius: 999px;
        background: linear-gradient(135deg, var(--ink), var(--ink-soft));
        color: #fff; font-weight: 800; text-decoration: none;
        box-shadow: 0 14px 30px rgba(6,21,37,.16);
        transition: transform .25s ease, background .25s;
    }
    .academy-more-btn:hover {
        transform: translateY(-3px);
        background: var(--teal); color: #fff;
    }

    .academy-dots {
        position: fixed; inset-inline-end: 1.1rem; top: 50%; transform: translateY(-50%);
        z-index: 30; display: none; flex-direction: column; gap: .5rem;
    }
    @media (min-width: 1280px) { .academy-dots { display: flex; } }
    .academy-dot {
        width: .5rem; height: .5rem; border-radius: 999px; border: 0;
        background: rgba(6,21,37,.22); cursor: pointer; padding: 0;
        transition: transform .2s, background .2s, height .2s;
    }
    .academy-dot.is-active {
        background: var(--teal); height: 1.4rem; border-radius: .75rem;
    }

    /* Homepage sticky section rail (desktop) */
    .academy-section-rail {
        display: none;
        position: sticky; top: 4.75rem; z-index: 25;
        background: rgba(255,255,255,.88);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--line);
        margin-bottom: -.5rem;
    }
    @media (min-width: 900px) { .academy-section-rail { display: block; } }
    .academy-section-rail-inner {
        max-width: var(--page-max); margin: 0 auto;
        padding: .55rem clamp(1rem, 3vw, 2rem);
        display: flex; gap: .35rem; overflow-x: auto; scrollbar-width: none;
    }
    .academy-section-rail-inner::-webkit-scrollbar { display: none; }
    .academy-rail-link {
        flex: 0 0 auto;
        padding: .45rem .95rem; border-radius: 999px;
        font-size: .8rem; font-weight: 800; text-decoration: none;
        color: var(--muted); background: transparent;
        transition: background .2s, color .2s;
    }
    .academy-rail-link:hover,
    .academy-rail-link.is-active {
        background: var(--ink); color: #fff;
    }
</style>
