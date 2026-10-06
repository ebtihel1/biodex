@extends('front.layout')

@section('title', 'Home')

@push('head')
<style>
    /* =========================================================
       Biodex — futuristic ecological landing page
       ========================================================= */

    /* ---------- Hero ---------- */
    .home-hero {
        position: relative;
        min-height: 100vh;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        padding: 150px 0 110px;
        overflow: hidden;
        color: #fff;
        background:
            radial-gradient(820px 480px at 76% 22%, rgba(16, 185, 129, .34), transparent 62%),
            radial-gradient(720px 520px at 14% 84%, rgba(45, 212, 191, .26), transparent 64%),
            linear-gradient(155deg, rgba(4, 21, 14, .93) 0%, rgba(4, 21, 14, .72) 46%, rgba(6, 42, 29, .92) 100%),
            url("{{ asset('images/EarthFront.png') }}") center / cover no-repeat;
    }

    .home-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(167, 243, 208, .055) 1px, transparent 1px),
            linear-gradient(90deg, rgba(167, 243, 208, .055) 1px, transparent 1px);
        background-size: 64px 64px;
        mask-image: radial-gradient(circle at 50% 45%, #000 0%, transparent 78%);
        -webkit-mask-image: radial-gradient(circle at 50% 45%, #000 0%, transparent 78%);
        pointer-events: none;
    }

    .home-hero__inner {
        position: relative;
        z-index: 3;
        max-width: 880px;
        text-align: center;
        margin: 0 auto;
    }

    .home-badge {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .55rem 1.15rem;
        font-size: .8rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #d7ffef;
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(167, 243, 208, .35);
        border-radius: 999px;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, .3);
        animation: hx-rise .8s cubic-bezier(.22, 1, .36, 1) both;
    }

    .home-badge i { color: #6ee7b7; }

    .home-hero__title {
        margin: 26px 0 0;
        font-size: clamp(2.4rem, 6vw, 4.6rem);
        font-weight: 800;
        line-height: 1.06;
        letter-spacing: -.03em;
        text-shadow: 0 8px 40px rgba(0, 0, 0, .45);
        animation: hx-rise .8s .1s cubic-bezier(.22, 1, .36, 1) both;
    }

    .home-hero__title em {
        font-style: normal;
        background: linear-gradient(92deg, #a7f3d0 0%, #34d399 45%, #22d3ee 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
        animation: hx-pan 7s ease-in-out infinite;
    }

    .home-hero__lead {
        max-width: 640px;
        margin: 22px auto 0;
        font-size: clamp(1.02rem, 1.7vw, 1.2rem);
        line-height: 1.7;
        color: rgba(233, 255, 245, .86);
        animation: hx-rise .8s .2s cubic-bezier(.22, 1, .36, 1) both;
    }

    .home-hero__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        justify-content: center;
        margin-top: 34px;
        animation: hx-rise .8s .3s cubic-bezier(.22, 1, .36, 1) both;
    }

    .home-hero__chips {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin: 40px 0 0;
        padding: 0;
        list-style: none;
        animation: hx-rise .8s .4s cubic-bezier(.22, 1, .36, 1) both;
    }

    .home-hero__chips li {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .95rem;
        font-size: .85rem;
        color: rgba(233, 255, 245, .92);
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 999px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        transition: transform .3s ease, border-color .3s ease, background .3s ease;
    }

    .home-hero__chips li:hover {
        transform: translateY(-4px);
        border-color: rgba(52, 211, 153, .55);
        background: rgba(52, 211, 153, .16);
    }

    .home-hero__chips li i { color: #6ee7b7; }

    /* floating orbs */
    .home-orb {
        position: absolute;
        z-index: 1;
        border-radius: 50%;
        filter: blur(58px);
        opacity: .55;
        pointer-events: none;
        animation: hx-float 14s ease-in-out infinite;
    }

    .home-orb--a { width: 340px; height: 340px; top: -60px; left: -80px; background: #10b981; }
    .home-orb--b { width: 280px; height: 280px; bottom: -70px; right: -60px; background: #22d3ee; animation-delay: -5s; }
    .home-orb--c { width: 200px; height: 200px; top: 40%; right: 14%; background: #a7f3d0; opacity: .3; animation-delay: -9s; }

    /* scroll cue */
    .home-scroll {
        position: absolute;
        z-index: 4;
        left: 50%;
        bottom: 26px;
        transform: translateX(-50%);
        width: 26px;
        height: 44px;
        border: 2px solid rgba(233, 255, 245, .45);
        border-radius: 999px;
        display: grid;
        justify-items: center;
        padding-top: 7px;
    }

    .home-scroll span {
        width: 4px;
        height: 9px;
        border-radius: 999px;
        background: #a7f3d0;
        animation: hx-wheel 1.8s ease-in-out infinite;
    }

    /* ---------- Buttons ---------- */
    .btn-glow,
    .btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .95rem 1.9rem;
        font-weight: 700;
        font-size: 1rem;
        border-radius: 999px;
        text-decoration: none;
        transition: transform .3s ease, box-shadow .3s ease, background .3s ease, border-color .3s ease;
    }

    .btn-glow {
        color: #04150e;
        border: none;
        background: linear-gradient(120deg, #a7f3d0, #34d399 55%, #22d3ee);
        background-size: 180% auto;
        box-shadow: 0 14px 34px rgba(52, 211, 153, .4);
        animation: hx-pan 6s ease-in-out infinite;
    }

    .btn-glow:hover,
    .btn-glow:focus {
        color: #04150e;
        transform: translateY(-3px);
        box-shadow: 0 20px 44px rgba(52, 211, 153, .55);
    }

    .btn-ghost {
        color: #e9fff5;
        border: 1px solid rgba(233, 255, 245, .4);
        background: rgba(255, 255, 255, .06);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .btn-ghost:hover,
    .btn-ghost:focus {
        color: #fff;
        transform: translateY(-3px);
        border-color: rgba(167, 243, 208, .8);
        background: rgba(52, 211, 153, .18);
    }

    /* ---------- Shell (full bleed over main) ---------- */
    .home-shell {
        position: relative;
        isolation: isolate;
        margin: -3rem -0.75rem;
        padding: clamp(54px, 7vw, 96px) 0 clamp(58px, 7vw, 100px);
        overflow: hidden;
        color: #e9fff5;
        background:
            radial-gradient(1100px 520px at 6% -8%, rgba(52, 211, 153, .2), transparent 60%),
            radial-gradient(900px 620px at 98% 10%, rgba(45, 212, 191, .16), transparent 55%),
            radial-gradient(1000px 700px at 50% 115%, rgba(16, 185, 129, .2), transparent 62%),
            linear-gradient(180deg, #04150e 0%, #062a1d 52%, #04150e 100%);
    }

    .home-sec { position: relative; z-index: 2; padding: clamp(38px, 5vw, 62px) 0; }

    .home-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        margin: 0 0 14px;
        font-size: .76rem;
        font-weight: 700;
        letter-spacing: .24em;
        text-transform: uppercase;
        color: #6ee7b7;
    }

    .home-eyebrow::before {
        content: "";
        width: 34px;
        height: 2px;
        background: linear-gradient(90deg, #34d399, transparent);
    }

    .home-h2 {
        margin: 0;
        font-size: clamp(1.85rem, 3.4vw, 2.85rem);
        font-weight: 800;
        line-height: 1.16;
        letter-spacing: -.02em;
        color: #f2fffa;
    }

    .home-h2 span {
        background: linear-gradient(92deg, #a7f3d0, #34d399 55%, #22d3ee);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
    }

    .home-sub {
        max-width: 640px;
        margin: 16px 0 0;
        color: rgba(233, 255, 245, .72);
        line-height: 1.7;
    }

    .home-head { text-align: center; display: flex; flex-direction: column; align-items: center; }
    .home-head .home-eyebrow::before { display: none; }

    /* ---------- Eco marquee ---------- */
    .home-marquee {
        position: relative;
        z-index: 2;
        overflow: hidden;
        padding: 16px 0;
        border-top: 1px solid rgba(167, 243, 208, .16);
        border-bottom: 1px solid rgba(167, 243, 208, .16);
        background: rgba(255, 255, 255, .03);
        -webkit-mask-image: linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent);
        mask-image: linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent);
    }

    .home-marquee__track {
        display: flex;
        width: max-content;
        gap: 46px;
        animation: hx-marquee 26s linear infinite;
    }

    .home-marquee__track span {
        display: inline-flex;
        align-items: center;
        gap: 46px;
        font-size: .82rem;
        font-weight: 700;
        letter-spacing: .3em;
        text-transform: uppercase;
        color: rgba(167, 243, 208, .75);
        white-space: nowrap;
    }

    .home-marquee__track span::after {
        content: "✦";
        color: #34d399;
        letter-spacing: 0;
    }

    /* ---------- Glass cards ---------- */
    .home-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
        margin-top: 44px;
    }

    .g-card {
        position: relative;
        display: flex;
        flex-direction: column;
        padding: 30px 26px;
        border-radius: 24px;
        background: linear-gradient(155deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .035));
        border: 1px solid rgba(167, 243, 208, .16);
        backdrop-filter: blur(16px) saturate(140%);
        -webkit-backdrop-filter: blur(16px) saturate(140%);
        box-shadow: 0 22px 46px rgba(0, 0, 0, .34), inset 0 1px 0 rgba(255, 255, 255, .14);
        overflow: hidden;
        transition: transform .4s cubic-bezier(.22, 1, .36, 1), border-color .4s ease, box-shadow .4s ease;
    }

    .g-card::after {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity .45s ease;
        background: radial-gradient(340px 200px at var(--mx, 50%) var(--my, 0%), rgba(52, 211, 153, .3), transparent 72%);
        pointer-events: none;
    }

    .g-card > * { position: relative; z-index: 1; }

    .g-card:hover {
        transform: translateY(-10px);
        border-color: rgba(52, 211, 153, .55);
        box-shadow: 0 30px 62px rgba(0, 0, 0, .45), 0 0 26px rgba(52, 211, 153, .22), inset 0 1px 0 rgba(255, 255, 255, .18);
    }

    .g-card:hover::after { opacity: 1; }

    .g-card__icon {
        width: 56px;
        height: 56px;
        display: grid;
        place-items: center;
        margin-bottom: 20px;
        font-size: 1.5rem;
        color: #04150e;
        border-radius: 17px;
        background: linear-gradient(135deg, #d1fae5, #34d399);
        box-shadow: 0 12px 26px rgba(52, 211, 153, .38);
    }

    .g-card h3 {
        margin: 0 0 10px;
        font-size: 1.22rem;
        font-weight: 700;
        color: #f2fffa;
    }

    .g-card p {
        margin: 0 0 22px;
        flex: 1;
        font-size: .95rem;
        line-height: 1.68;
        color: rgba(233, 255, 245, .7);
    }

    .g-link {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        font-weight: 700;
        font-size: .93rem;
        text-decoration: none;
        color: #6ee7b7;
        transition: gap .3s ease, color .3s ease;
    }

    .g-link:hover { gap: .9rem; color: #a7f3d0; }

    /* ---------- Steps ---------- */
    .home-steps {
        position: relative;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
        margin-top: 46px;
    }

    .home-steps::before {
        content: "";
        position: absolute;
        top: 34px;
        left: 6%;
        right: 6%;
        height: 2px;
        background: repeating-linear-gradient(90deg, rgba(52, 211, 153, .55) 0 10px, transparent 10px 20px);
    }

    .home-step {
        position: relative;
        padding: 26px 22px;
        border-radius: 22px;
        background: rgba(255, 255, 255, .05);
        border: 1px solid rgba(255, 255, 255, .1);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        transition: transform .35s ease, border-color .35s ease;
    }

    .home-step:hover { transform: translateY(-8px); border-color: rgba(52, 211, 153, .5); }

    .home-step__n {
        position: relative;
        z-index: 1;
        width: 52px;
        height: 52px;
        display: grid;
        place-items: center;
        margin-bottom: 18px;
        font-weight: 800;
        color: #a7f3d0;
        border-radius: 50%;
        background: #062a1d;
        border: 1px solid rgba(52, 211, 153, .55);
        box-shadow: 0 0 0 6px rgba(4, 21, 14, .9), 0 0 22px rgba(52, 211, 153, .35);
    }

    .home-step h3 { margin: 0 0 8px; font-size: 1.08rem; font-weight: 700; color: #f2fffa; }
    .home-step p { margin: 0; font-size: .92rem; line-height: 1.65; color: rgba(233, 255, 245, .68); }

    /* ---------- CTA ---------- */
    .home-cta {
        position: relative;
        overflow: hidden;
        padding: clamp(38px, 5vw, 62px) clamp(24px, 4vw, 54px);
        text-align: center;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(52, 211, 153, .2), rgba(6, 42, 29, .55));
        border: 1px solid rgba(167, 243, 208, .3);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow: 0 34px 74px rgba(0, 0, 0, .42), inset 0 1px 0 rgba(255, 255, 255, .16);
    }

    .home-cta::before {
        content: "";
        position: absolute;
        width: 460px;
        height: 460px;
        top: -230px;
        right: -160px;
        border-radius: 50%;
        background: conic-gradient(from 0deg, #34d399, #22d3ee, #a7f3d0, #34d399);
        filter: blur(90px);
        opacity: .45;
        animation: hx-spin 18s linear infinite;
    }

    .home-cta > * { position: relative; z-index: 1; }

    .home-cta h2 {
        margin: 0;
        font-size: clamp(1.7rem, 3.2vw, 2.6rem);
        font-weight: 800;
        letter-spacing: -.02em;
        color: #f2fffa;
    }

    .home-cta p {
        max-width: 560px;
        margin: 14px auto 0;
        color: rgba(233, 255, 245, .78);
        line-height: 1.7;
    }

    .home-cta__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        justify-content: center;
        margin-top: 30px;
    }

    /* ---------- Reveal on scroll ---------- */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity .75s cubic-bezier(.22, 1, .36, 1), transform .75s cubic-bezier(.22, 1, .36, 1);
        transition-delay: var(--d, 0ms);
    }

    .reveal.is-in { opacity: 1; transform: none; }

    /* ---------- Keyframes ---------- */
    @keyframes hx-rise { from { opacity: 0; transform: translateY(26px); } to { opacity: 1; transform: none; } }
    @keyframes hx-float { 0%, 100% { transform: translate3d(0, 0, 0) scale(1); } 50% { transform: translate3d(0, -34px, 0) scale(1.08); } }
    @keyframes hx-pan { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
    @keyframes hx-marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    @keyframes hx-wheel { 0% { opacity: 0; transform: translateY(0); } 40% { opacity: 1; } 100% { opacity: 0; transform: translateY(16px); } }
    @keyframes hx-spin { to { transform: rotate(360deg); } }

    /* ---------- Responsive ---------- */
    @media (max-width: 1199px) {
        .home-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 991px) {
        .home-steps { grid-template-columns: repeat(2, 1fr); }
        .home-steps::before { display: none; }
        .home-hero { padding-top: 130px; }
    }

    @media (max-width: 575px) {
        .home-grid,
        .home-steps { grid-template-columns: 1fr; }
        .home-hero { min-height: auto; padding: 128px 0 84px; }
        .home-orb--c { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
        .home-orb,
        .home-scroll span,
        .home-marquee__track,
        .home-badge,
        .home-hero__title,
        .home-hero__lead,
        .home-hero__actions,
        .home-hero__chips,
        .btn-glow,
        .home-cta::before,
        .home-hero__title em { animation: none !important; }
    }
</style>
<noscript><style>.reveal { opacity: 1 !important; transform: none !important; }</style></noscript>
@endpush

@section('hero')
<section class="home-hero">
    <div class="home-orb home-orb--a"></div>
    <div class="home-orb home-orb--b"></div>
    <div class="home-orb home-orb--c"></div>

    <div class="container home-hero__inner">
        <span class="home-badge"><i class="bi bi-leaf-fill"></i> Circular economy, powered by AI</span>

        <h1 class="home-hero__title">Give every waste<br>a <em>second life</em></h1>

        <p class="home-hero__lead">
            Biodex connects citizens, recyclers, and companies to turn waste into new
            opportunities — for people, planet, and progress.
        </p>

        <div class="home-hero__actions">
            <a href="{{ url('/register') }}" class="btn-glow">Join Biodex <i class="bi bi-arrow-right"></i></a>
            <a href="{{ route('front.products.index') }}" class="btn-ghost">Explore marketplace</a>
        </div>

        <ul class="home-hero__chips">
            <li><i class="bi bi-recycle"></i> Smart recycling</li>
            <li><i class="bi bi-geo-alt"></i> Collection points</li>
            <li><i class="bi bi-megaphone"></i> Community campaigns</li>
            <li><i class="bi bi-cpu"></i> AI waste classification</li>
        </ul>
    </div>

    <div class="home-scroll" aria-hidden="true"><span></span></div>
</section>
@endsection

@section('content')
<div class="home-shell">
    <div class="home-orb home-orb--a"></div>
    <div class="home-orb home-orb--b"></div>

    <div class="home-marquee">
        <div class="home-marquee__track">
            <span>Reduce</span><span>Reuse</span><span>Recycle</span><span>Repair</span><span>Regenerate</span><span>Rethink</span>
            <span>Reduce</span><span>Reuse</span><span>Recycle</span><span>Repair</span><span>Regenerate</span><span>Rethink</span>
        </div>
    </div>

    <!-- Missions -->
    <section class="home-sec">
        <div class="container">
            <div class="home-head reveal">
                <p class="home-eyebrow">One platform, four missions</p>
                <h2 class="home-h2">Everything you need to <span>close the loop</span></h2>
                <p class="home-sub">Sort it, drop it, trade it, or rally your community around it — every action on Biodex keeps materials in circulation.</p>
            </div>

            <div class="home-grid">
                <article class="g-card reveal" style="--d: 0ms">
                    <div class="g-card__icon"><i class="bi bi-recycle"></i></div>
                    <h3>Recycling</h3>
                    <p>Give waste a new life through smart, sustainable recycling methods and track where your materials end up.</p>
                    <a href="{{ url('/wastess') }}" class="g-link">Join now <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="g-card reveal" style="--d: 90ms">
                    <div class="g-card__icon"><i class="bi bi-megaphone-fill"></i></div>
                    <h3>Community</h3>
                    <p>Join a movement of citizens, schools, and businesses running campaigns for a greener, cleaner world.</p>
                    <a href="{{ url('/campaignsFront') }}" class="g-link">Join now <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="g-card reveal" style="--d: 180ms">
                    <div class="g-card__icon"><i class="bi bi-bag-check-fill"></i></div>
                    <h3>Marketplace</h3>
                    <p>Shop verified eco-products and recovered materials, or list your own circular goods for the community.</p>
                    <a href="{{ route('front.products.index') }}" class="g-link">Browse products <i class="bi bi-arrow-right"></i></a>
                </article>

                <article class="g-card reveal" style="--d: 270ms">
                    <div class="g-card__icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <h3>Collection points</h3>
                    <p>Find the nearest drop-off location, see what it accepts, and reserve a slot before you arrive.</p>
                    <a href="{{ url('/biodex/collectionpoints') }}" class="g-link">Find a point <i class="bi bi-arrow-right"></i></a>
                </article>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="home-sec">
        <div class="container">
            <div class="home-head reveal">
                <p class="home-eyebrow">How it works</p>
                <h2 class="home-h2">Four steps to <span>real impact</span></h2>
            </div>

            <div class="home-steps">
                <article class="home-step reveal" style="--d: 0ms">
                    <div class="home-step__n">1</div>
                    <h3>Discover</h3>
                    <p>Explore collection points, eco-products, and sustainability opportunities nearby.</p>
                </article>
                <article class="home-step reveal" style="--d: 90ms">
                    <div class="home-step__n">2</div>
                    <h3>Participate</h3>
                    <p>Join campaigns, donate, reserve a service, or contribute to a waste-reduction effort.</p>
                </article>
                <article class="home-step reveal" style="--d: 180ms">
                    <div class="home-step__n">3</div>
                    <h3>Track</h3>
                    <p>Follow your orders, donations, and reservations from one transparent dashboard.</p>
                </article>
                <article class="home-step reveal" style="--d: 270ms">
                    <div class="home-step__n">4</div>
                    <h3>Impact</h3>
                    <p>Watch small habits add up into measurable results for your community.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="home-sec">
        <div class="container">
            <div class="home-cta reveal">
                <h2>Ready to make a difference?</h2>
                <p>Create your free Biodex account and turn everyday waste into a cleaner, greener future.</p>
                <div class="home-cta__actions">
                    <a href="{{ url('/register') }}" class="btn-glow">Create account <i class="bi bi-arrow-right"></i></a>
                    <a href="{{ url('/contact') }}" class="btn-ghost">Talk to us</a>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    (function () {
        var items = document.querySelectorAll('.reveal');

        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-in'); });
        } else {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

            items.forEach(function (el) { observer.observe(el); });
        }

        if (window.matchMedia('(hover: hover)').matches) {
            document.querySelectorAll('.g-card').forEach(function (card) {
                card.addEventListener('pointermove', function (event) {
                    var box = card.getBoundingClientRect();
                    card.style.setProperty('--mx', ((event.clientX - box.left) / box.width * 100) + '%');
                    card.style.setProperty('--my', ((event.clientY - box.top) / box.height * 100) + '%');
                });
            });
        }
    })();
</script>
@endsection
