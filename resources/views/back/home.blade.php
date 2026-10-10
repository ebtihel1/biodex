@extends('back.layout')

@section('title', 'Dashboard')

@section('content')
@php
    // Couleur sémantique des statuts (commandes, réservations, dons, campagnes…)
    $statusTone = fn ($s) => match (strtolower((string) $s)) {
        'pending', 'en_attente', 'waiting'                      => 'warn',
        'confirmed', 'processing', 'in_progress', 'shipped'     => 'info',
        'completed', 'delivered', 'accepted', 'approved',
        'available', 'active', 'done'                           => 'ok',
        'cancelled', 'canceled', 'failed', 'rejected'           => 'bad',
        default                                                 => 'mute',
    };

    // Répartition de la barre de recyclage (floor pour ne jamais dépasser 100 %)
    $rpTotal       = max(1, (int) ($recyclingProcessStats['total'] ?? 0));
    $pctCompleted  = floor(($recyclingProcessStats['completed']   ?? 0) / $rpTotal * 100);
    $pctInProgress = floor(($recyclingProcessStats['in_progress'] ?? 0) / $rpTotal * 100);
    $pctPending    = floor(($recyclingProcessStats['pending']     ?? 0) / $rpTotal * 100);
    $pctFailed     = floor(($recyclingProcessStats['failed']      ?? 0) / $rpTotal * 100);

    // Détection des graphiques vides (pour afficher un état vide propre)
    $revenueEmpty  = empty($revenueChart['labels'] ?? []);
    $moduleEmpty   = array_sum($moduleChart['data'] ?? []) == 0;
    $campaignEmpty = array_sum($campaignStatusChart['data'] ?? []) == 0;
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* =========================================================
   Design tokens
   ========================================================= */
.bi-wrapper {
    --ink:        #15271f;
    --ink-soft:   #5b6b62;
    --ink-faint:  #8e9a93;
    --forest:     #1b4332;
    --leaf:       #2d6a4f;
    --mint:       #e6f0ea;
    --sun:        #f2dd94;
    --canvas:     #f4f6f2;
    --surface:    #ffffff;
    --line:       #e3e8e2;

    --blue:   #2f6fdb;  --blue-bg:   #e8f0fd;
    --green:  #1f8a5b;  --green-bg:  #e3f3ea;
    --amber:  #c98a00;  --amber-bg:  #fdf3d6;
    --red:    #cc3a47;  --red-bg:    #fbe7e9;
    --cyan:   #0f95b0;  --cyan-bg:   #def3f8;
    --violet: #6f42c1;  --violet-bg: #efe9fa;
    --orange: #e0701a;  --orange-bg: #fdeadb;
    --teal:   #14a07a;  --teal-bg:   #ddf4ec;

    --radius-lg: 22px;
    --radius-md: 14px;
    --radius-sm: 10px;

    font-family: 'Figtree', system-ui, -apple-system, 'Segoe UI', sans-serif;
    color: var(--ink);
    background: var(--canvas);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
}
.bi-wrapper h1, .bi-wrapper h5, .bi-wrapper .display-font {
    font-family: 'Bricolage Grotesque', 'Figtree', system-ui, sans-serif;
}
.bi-wrapper :focus-visible {
    outline: 3px solid var(--sun);
    outline-offset: 2px;
}

/* =========================================================
   En-tête (hero)
   ========================================================= */
.hero {
    background: var(--forest);
    background-image:
        radial-gradient(120% 140% at 100% 0%, rgba(242,221,148,.16) 0%, transparent 55%),
        radial-gradient(90% 120% at 0% 100%, rgba(45,106,79,.9) 0%, transparent 60%);
    border-radius: var(--radius-lg);
    padding: 2rem 2.25rem;
    color: #fff;
    margin-bottom: 1.5rem;
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 2rem;
    align-items: end;
}
.hero-title { font-size: clamp(1.4rem, 2.2vw, 1.9rem); font-weight: 700; color: var(--sun); margin: 0 0 .35rem; letter-spacing: -.01em; }
.hero-sub   { margin: 0; color: rgba(255,255,255,.78); font-size: .95rem; }
.hero-meta  { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1.25rem; }
.hero-chip {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .35rem .8rem; border-radius: 999px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.16);
    font-size: .8rem; color: #fff;
}
.hero-chip .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--sun); }

.hero-figure { text-align: right; }
.hero-figure .label { font-size: .9rem; color: rgba(255,255,255,.75); margin-bottom: .1rem; }
.hero-figure .value {
    font-family: 'Bricolage Grotesque', sans-serif;
    font-size: clamp(2.6rem, 6vw, 4.4rem);
    line-height: 1;
    font-weight: 700;
    color: #fff;
    letter-spacing: -.03em;
}
.hero-figure .value small { font-size: .4em; font-weight: 500; color: var(--sun); margin-left: .35rem; letter-spacing: 0; }
.hero-figure .note { margin-top: .5rem; font-size: .85rem; color: var(--sun); }

/* =========================================================
   KPI
   ========================================================= */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.kpi-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: 1.1rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    --tone: var(--blue); --tone-bg: var(--blue-bg);
}
.kpi-icon {
    width: 46px; height: 46px;
    border-radius: 13px;
    background: var(--tone-bg);
    color: var(--tone);
    display: grid; place-items: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}
.kpi-label { font-size: .85rem; color: var(--ink-soft); font-weight: 500; }
.kpi-value { font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.65rem; font-weight: 700; line-height: 1.15; color: var(--ink); letter-spacing: -.02em; }
.kpi-delta { font-size: .78rem; color: var(--ink-faint); margin-top: .1rem; }
.kpi-delta i { color: var(--tone); margin-right: .2rem; }

.kpi-users        { --tone: var(--blue);   --tone-bg: var(--blue-bg); }
.kpi-revenue      { --tone: var(--amber);  --tone-bg: var(--amber-bg); }
.kpi-campaigns    { --tone: var(--red);    --tone-bg: var(--red-bg); }
.kpi-donations    { --tone: var(--cyan);   --tone-bg: var(--cyan-bg); }
.kpi-reservations { --tone: var(--teal);   --tone-bg: var(--teal-bg); }
.kpi-products     { --tone: var(--violet); --tone-bg: var(--violet-bg); }
.kpi-points       { --tone: var(--orange); --tone-bg: var(--orange-bg); }

/* =========================================================
   Cartes de section
   ========================================================= */
.section-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    overflow: hidden;
    height: 100%;
}
.section-card .card-header {
    background: transparent;
    border-bottom: 1px solid var(--line);
    padding: 1.1rem 1.4rem;
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
}
.section-card .card-header h5 {
    margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--ink);
    display: flex; align-items: center; gap: .55rem; letter-spacing: -.01em;
}
.section-card .card-header h5 i { color: var(--leaf); font-size: 1.1rem; }
.section-card .card-body { padding: 1.4rem; }

.module-tag {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .25rem .7rem;
    background: var(--mint); color: var(--leaf);
    border-radius: 999px; font-size: .78rem; font-weight: 600;
}

.btn-soft {
    display: inline-flex; align-items: center;
    padding: .35rem .9rem; border-radius: 999px;
    background: var(--mint); color: var(--forest);
    font-size: .82rem; font-weight: 600; text-decoration: none;
    border: 1px solid transparent;
    transition: background .15s ease, color .15s ease;
}
.btn-soft:hover { background: var(--forest); color: #fff; }

/* =========================================================
   Graphiques
   ========================================================= */
.chart-container, .chart-container-sm { position: relative; width: 100%; }
.chart-container    { height: 300px; }
.chart-container-sm { height: 250px; }

.chart-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    text-align: center; color: var(--ink-faint); gap: .35rem;
}
.chart-empty i { font-size: 2.2rem; opacity: .5; }
.chart-empty p { margin: 0; font-size: .9rem; }
.chart-container .chart-empty,
.chart-container-sm .chart-empty { position: absolute; inset: 0; background: var(--surface); z-index: 1; }
.card-body > .chart-empty { padding: 2rem 0; }

/* =========================================================
   Classements
   ========================================================= */
.top-list { display: flex; flex-direction: column; }
.top-list-item {
    display: flex; align-items: center; gap: .85rem;
    padding: .8rem 0;
    border-bottom: 1px solid var(--line);
}
.top-list-item:last-child { border-bottom: 0; padding-bottom: 0; }
.top-list-item:first-child { padding-top: 0; }
.top-list-rank {
    width: 30px; height: 30px; border-radius: 9px;
    background: var(--mint); color: var(--leaf);
    display: grid; place-items: center;
    font-family: 'Bricolage Grotesque', sans-serif; font-weight: 700; font-size: .9rem;
    flex-shrink: 0;
}
.top-list-item:first-child .top-list-rank { background: var(--forest); color: var(--sun); }
.top-list-name { font-weight: 600; line-height: 1.25; }
.top-list-meta { font-size: .8rem; color: var(--ink-soft); }

/* =========================================================
   Pastilles de statut
   ========================================================= */
.pill {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .2rem .65rem; border-radius: 999px;
    font-size: .76rem; font-weight: 600; white-space: nowrap;
}
.pill::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.pill-ok   { background: var(--green-bg); color: var(--green); }
.pill-info { background: var(--blue-bg);  color: var(--blue); }
.pill-warn { background: var(--amber-bg); color: var(--amber); }
.pill-bad  { background: var(--red-bg);   color: var(--red); }
.pill-mute { background: #eceeed;         color: var(--ink-soft); }
.pill-share { background: var(--mint); color: var(--leaf); }
.pill-share::before { display: none; }

/* =========================================================
   Campagnes
   ========================================================= */
.campaign-row + .campaign-row { margin-top: 1.25rem; }
.campaign-head { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; margin-bottom: .45rem; }
.campaign-title { font-weight: 600; }
.campaign-pct { font-family: 'Bricolage Grotesque', sans-serif; font-weight: 700; color: var(--leaf); }
.track { height: 8px; border-radius: 999px; background: #edf0ec; overflow: hidden; display: flex; }
.track > span { display: block; height: 100%; }
.track .t-ok   { background: var(--green); }
.track .t-info { background: var(--blue); }
.track .t-warn { background: #e8b020; }
.track .t-bad  { background: var(--red); }
.campaign-meta { display: flex; flex-wrap: wrap; gap: .4rem 1rem; margin-top: .5rem; font-size: .8rem; color: var(--ink-soft); }
.campaign-meta i { color: var(--ink-faint); margin-right: .2rem; }

/* =========================================================
   Activité
   ========================================================= */
.activity-item { display: flex; gap: .9rem; padding: .8rem 0; border-bottom: 1px solid var(--line); }
.activity-item:first-child { padding-top: 0; }
.activity-item:last-child  { border-bottom: 0; padding-bottom: 0; }
.activity-icon {
    width: 38px; height: 38px; border-radius: 11px;
    display: grid; place-items: center; flex-shrink: 0;
    color: #fff; font-size: 1rem;
}
.activity-title  { font-weight: 600; font-size: .92rem; line-height: 1.3; }
.activity-detail { color: var(--ink-soft); font-size: .84rem; }
.activity-time   { color: var(--ink-faint); font-size: .76rem; margin-top: .1rem; }

/* =========================================================
   Recyclage
   ========================================================= */
.recycle-total { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: .75rem; }
.recycle-total strong { font-family: 'Bricolage Grotesque', sans-serif; font-size: 2rem; line-height: 1; }
.recycle-total span { color: var(--ink-soft); }
.track-lg { height: 14px; }
.legend-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem 1rem; margin-top: 1.1rem; font-size: .88rem; }
.legend-grid .lg { display: flex; align-items: center; gap: .5rem; color: var(--ink-soft); }
.legend-grid .lg b { color: var(--ink); margin-left: auto; }
.legend-grid .sw { width: 10px; height: 10px; border-radius: 3px; }
.recycle-output { margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed var(--line); display: flex; justify-content: space-between; align-items: baseline; }
.recycle-output span { color: var(--ink-soft); }
.recycle-output strong { font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.25rem; }

/* =========================================================
   Tableaux
   ========================================================= */
.bi-table { width: 100%; font-size: .9rem; border-collapse: collapse; }
.bi-table thead th {
    padding: .7rem .75rem; font-size: .8rem; font-weight: 600;
    color: var(--ink-soft); background: #fafbf9;
    border-bottom: 1px solid var(--line); white-space: nowrap; text-align: left;
}
.bi-table tbody td { padding: .8rem .75rem; vertical-align: middle; border-bottom: 1px solid #f0f2ef; }
.bi-table tbody tr:last-child td { border-bottom: 0; }
.bi-table tbody tr:hover { background: #fafbf9; }
.bi-table th:first-child, .bi-table td:first-child { padding-left: 1.4rem; }
.bi-table th:last-child,  .bi-table td:last-child  { padding-right: 1.4rem; }
.bi-table .num { font-variant-numeric: tabular-nums; }
.bi-table .id  { font-weight: 700; color: var(--leaf); }
.table-empty { text-align: center; color: var(--ink-faint); padding: 1.75rem 1rem !important; }

/* =========================================================
   Responsive & accessibilité
   ========================================================= */
@media (max-width: 991.98px) {
    .hero { grid-template-columns: 1fr; gap: 1.25rem; }
    .hero-figure { text-align: left; }
}
@media (max-width: 575.98px) {
    .bi-wrapper { padding: .75rem; border-radius: 0; }
    .hero { padding: 1.4rem 1.25rem; }
    .kpi-value { font-size: 1.4rem; }
    .section-card .card-body { padding: 1.1rem; }
}
@media (prefers-reduced-motion: reduce) {
    .bi-wrapper * { transition: none !important; animation: none !important; }
}
</style>

<div class="bi-wrapper">

    {{-- ============ HERO ============ --}}
    <header class="hero">
        <div>
            <h1 class="hero-title"><i class="bi bi-graph-up-arrow me-2"></i>Tableau de bord</h1>
            <p class="hero-sub">Vue consolidée de tous vos modules</p>
            <div class="hero-meta">
                <span class="hero-chip"><i class="bi bi-calendar3"></i> {{ now()->translatedFormat('d F Y') }}</span>
                <span class="hero-chip"><span class="dot"></span> Mis à jour à {{ now()->format('H:i') }}</span>
                <span class="hero-chip"><i class="bi bi-megaphone-fill"></i> {{ $activeCampaigns }} {{ $activeCampaigns > 1 ? 'campagnes actives' : 'campagne active' }}</span>
            </div>
        </div>
        <div class="hero-figure">
            <div class="label">Déchets collectés au total</div>
            <div class="value">{{ number_format($kpis['waste_weight'], 1, ',', ' ') }}<small>kg</small></div>
            <div class="note">
                <i class="bi bi-recycle"></i>
                {{ $kpis['waste_weight'] > 0 ? 'Recyclage actif' : 'Aucune collecte enregistrée' }}
            </div>
        </div>
    </header>

    {{-- ============ KPI ============ --}}
    <section class="kpi-grid" aria-label="Indicateurs clés">
        <div class="kpi-card kpi-users">
            <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="kpi-label">Utilisateurs</div>
                <div class="kpi-value">{{ number_format($kpis['users'], 0, ',', ' ') }}</div>
                <div class="kpi-delta">Comptes enregistrés</div>
            </div>
        </div>

        <div class="kpi-card kpi-revenue">
            <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="kpi-label">Revenus des commandes</div>
                <div class="kpi-value">{{ number_format($kpis['orders_revenue'], 0, ',', ' ') }} DT</div>
                <div class="kpi-delta">Cumulés</div>
            </div>
        </div>

        <div class="kpi-card kpi-campaigns">
            <div class="kpi-icon"><i class="bi bi-megaphone-fill"></i></div>
            <div>
                <div class="kpi-label">Campagnes</div>
                <div class="kpi-value">{{ number_format($kpis['campaigns'], 0, ',', ' ') }}</div>
                <div class="kpi-delta"><i class="bi bi-circle-fill" style="font-size:.45rem;"></i>{{ $activeCampaigns }} en cours</div>
            </div>
        </div>

        <div class="kpi-card kpi-donations">
            <div class="kpi-icon"><i class="bi bi-gift-fill"></i></div>
            <div>
                <div class="kpi-label">Dons</div>
                <div class="kpi-value">{{ number_format($kpis['donations'], 0, ',', ' ') }}</div>
                <div class="kpi-delta">Dons reçus</div>
            </div>
        </div>

        <div class="kpi-card kpi-reservations">
            <div class="kpi-icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div>
                <div class="kpi-label">Réservations</div>
                <div class="kpi-value">{{ number_format($kpis['reservations'], 0, ',', ' ') }}</div>
                <div class="kpi-delta">Enregistrées</div>
            </div>
        </div>

        <div class="kpi-card kpi-products">
            <div class="kpi-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div>
                <div class="kpi-label">Produits</div>
                <div class="kpi-value">{{ number_format($kpis['products'], 0, ',', ' ') }}</div>
                <div class="kpi-delta">Au catalogue</div>
            </div>
        </div>

        <div class="kpi-card kpi-points">
            <div class="kpi-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
                <div class="kpi-label">Points de collecte</div>
                <div class="kpi-value">{{ number_format($kpis['collection_points'], 0, ',', ' ') }}</div>
                <div class="kpi-delta">Actifs</div>
            </div>
        </div>
    </section>

    {{-- ============ LIGNE 1 — Tendance + Répartition ============ --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-graph-up"></i>Évolution de la collecte sur 6 mois</h5>
                    <span class="module-tag"><i class="bi bi-recycle"></i> Déchets</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendChart" role="img" aria-label="Courbe de l'évolution de la collecte sur 6 mois"></canvas>
                        @if(empty($trendChart['datasets']))
                            <div class="chart-empty">
                                <i class="bi bi-graph-up"></i>
                                <p>Aucune collecte sur la période</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-pie-chart"></i>Répartition par catégorie</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distributionChart" role="img" aria-label="Répartition des déchets par catégorie"></canvas>
                        @if(empty($distributionChart['data']))
                            <div class="chart-empty">
                                <i class="bi bi-pie-chart"></i>
                                <p>Aucune donnée</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ LIGNE 2 — Activité mensuelle + Modules + Campagnes ============ --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-bar-chart-line"></i>Activité mensuelle</h5>
                    <span class="module-tag"><i class="bi bi-cart-check"></i> Commandes</span>
                </div>
                <div class="card-body">
                    <div class="chart-container-sm">
                        <canvas id="revenueChart" role="img" aria-label="Revenus, dons et réservations par mois"></canvas>
                        @if($revenueEmpty)
                            <div class="chart-empty"><i class="bi bi-bar-chart-line"></i><p>Aucune activité</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-bar-chart-fill"></i>Activité par module</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container-sm">
                        <canvas id="moduleChart" role="img" aria-label="Volume d'activité par module"></canvas>
                        @if($moduleEmpty)
                            <div class="chart-empty"><i class="bi bi-bar-chart-fill"></i><p>Aucune donnée</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-megaphone"></i>Statut des campagnes</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container-sm">
                        <canvas id="campaignStatusChart" role="img" aria-label="Répartition des campagnes par statut"></canvas>
                        @if($campaignEmpty)
                            <div class="chart-empty"><i class="bi bi-megaphone"></i><p>Aucune campagne</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ LIGNE 3 — Classements ============ --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-trophy"></i>Catégories de déchets</h5>
                </div>
                <div class="card-body">
                    @if(count($topWastes))
                        <div class="top-list">
                            @foreach($topWastes as $index => $category)
                                <div class="top-list-item">
                                    <div class="top-list-rank">{{ $index + 1 }}</div>
                                    <div class="flex-grow-1">
                                        <div class="top-list-name">{{ $category['name'] }}</div>
                                        <div class="top-list-meta">{{ number_format($category['weight'], 1, ',', ' ') }} kg · {{ $category['count'] }} entrées</div>
                                    </div>
                                    <span class="pill pill-share">{{ $category['share'] }} %</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="chart-empty"><i class="bi bi-trophy"></i><p>Aucune donnée</p></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-geo-alt"></i>Points de collecte</h5>
                </div>
                <div class="card-body">
                    @if(count($topCollectionPoints))
                        <div class="top-list">
                            @foreach($topCollectionPoints as $index => $cp)
                                <div class="top-list-item">
                                    <div class="top-list-rank">{{ $index + 1 }}</div>
                                    <div class="flex-grow-1">
                                        <div class="top-list-name">{{ $cp['name'] }}</div>
                                        <div class="top-list-meta"><i class="bi bi-geo-alt-fill"></i> {{ $cp['city'] }} · {{ $cp['count'] }} déchets</div>
                                    </div>
                                    <span class="pill pill-{{ $statusTone($cp['status']) }}">{{ ucfirst($cp['status']) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="chart-empty"><i class="bi bi-geo-alt"></i><p>Aucun point de collecte</p></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-box-seam"></i>Produits</h5>
                </div>
                <div class="card-body">
                    @if(count($topProducts))
                        <div class="top-list">
                            @foreach($topProducts as $index => $p)
                                <div class="top-list-item">
                                    <div class="top-list-rank">{{ $index + 1 }}</div>
                                    <div class="flex-grow-1">
                                        <div class="top-list-name">{{ $p['name'] }}</div>
                                        <div class="top-list-meta">Stock : {{ $p['stock'] }} · {{ number_format($p['price'], 2, ',', ' ') }} DT</div>
                                    </div>
                                    <span class="pill pill-{{ $p['available'] ? 'ok' : 'mute' }}">{{ $p['available'] ? 'Disponible' : 'Indisponible' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="chart-empty"><i class="bi bi-box-seam"></i><p>Aucun produit</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============ LIGNE 4 — Campagnes + Activité récente ============ --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-megaphone-fill"></i>Progression des campagnes</h5>
                    <a href="{{ route('back.campaigns') }}" class="btn-soft">Voir tout</a>
                </div>
                <div class="card-body">
                    @forelse($campaignProgress as $campaign)
                        @php
                            $cTone = $campaign['progress'] >= 75 ? 't-ok' : ($campaign['progress'] >= 40 ? 't-warn' : 't-info');
                            $cPct  = max(0, min(100, $campaign['progress']));
                        @endphp
                        <div class="campaign-row">
                            <div class="campaign-head">
                                <span class="campaign-title">{{ $campaign['title'] }}</span>
                                <span class="campaign-pct">{{ $campaign['progress'] }} %</span>
                            </div>
                            <div class="track" role="progressbar" aria-valuenow="{{ $cPct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progression de {{ $campaign['title'] }}">
                                <span class="{{ $cTone }}" style="width: {{ $cPct }}%"></span>
                            </div>
                            <div class="campaign-meta">
                                <span><i class="bi bi-geo-alt-fill"></i>{{ $campaign['location'] }}</span>
                                <span><i class="bi bi-calendar3"></i>{{ $campaign['period'] }}</span>
                                <span><i class="bi bi-people-fill"></i>{{ $campaign['participants'] }} participants</span>
                            </div>
                        </div>
                    @empty
                        <div class="chart-empty"><i class="bi bi-megaphone"></i><p>Aucune campagne active</p></div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-activity"></i>Activité récente</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                        <div class="activity-item">
                            <div class="activity-icon bg-{{ $activity['color'] }}">
                                <i class="bi bi-{{ $activity['icon'] }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ $activity['title'] }}</div>
                                <div class="activity-detail">{{ $activity['detail'] }}</div>
                                <div class="activity-time"><i class="bi bi-clock"></i> {{ $activity['time'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="chart-empty"><i class="bi bi-clock-history"></i><p>Aucune activité récente</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ============ LIGNE 5 — Recyclage + Commandes + Dons ============ --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-arrow-repeat"></i>Processus de recyclage</h5>
                </div>
                <div class="card-body">
                    <div class="recycle-total">
                        <span>Total des processus</span>
                        <strong>{{ $recyclingProcessStats['total'] }}</strong>
                    </div>
                    <div class="track track-lg" role="img" aria-label="Répartition des processus de recyclage par statut">
                        <span class="t-ok"   style="width: {{ $pctCompleted }}%"  title="Terminés"></span>
                        <span class="t-info" style="width: {{ $pctInProgress }}%" title="En cours"></span>
                        <span class="t-warn" style="width: {{ $pctPending }}%"    title="En attente"></span>
                        <span class="t-bad"  style="width: {{ $pctFailed }}%"     title="Échoués"></span>
                    </div>
                    <div class="legend-grid">
                        <div class="lg"><span class="sw" style="background:var(--green)"></span>Terminés <b>{{ $recyclingProcessStats['completed'] }}</b></div>
                        <div class="lg"><span class="sw" style="background:var(--blue)"></span>En cours <b>{{ $recyclingProcessStats['in_progress'] }}</b></div>
                        <div class="lg"><span class="sw" style="background:#e8b020"></span>En attente <b>{{ $recyclingProcessStats['pending'] }}</b></div>
                        <div class="lg"><span class="sw" style="background:var(--red)"></span>Échoués <b>{{ $recyclingProcessStats['failed'] }}</b></div>
                    </div>
                    <div class="recycle-output">
                        <span>Quantité produite</span>
                        <strong>{{ number_format($recyclingProcessStats['output_total'], 2, ',', ' ') }} kg</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-cart-check"></i>Dernières commandes</h5>
                    <a href="{{ route('back.orders.index') }}" class="btn-soft">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="bi-table">
                            <thead>
                                <tr><th>N°</th><th>Client</th><th>Montant</th><th>Statut</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $o)
                                    <tr>
                                        <td class="id">{{ $o['id'] }}</td>
                                        <td>{{ $o['user'] }}</td>
                                        <td class="num fw-semibold">{{ number_format($o['amount'], 2, ',', ' ') }} DT</td>
                                        <td><span class="pill pill-{{ $statusTone($o['status']) }}">{{ ucfirst($o['status']) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="table-empty">Aucune commande</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-gift"></i>Derniers dons</h5>
                    <a href="{{ route('back.donations.index') }}" class="btn-soft">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="bi-table">
                            <thead>
                                <tr><th>N°</th><th>Article</th><th>État</th><th>Statut</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentDonations as $d)
                                    <tr>
                                        <td class="id">{{ $d['id'] }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($d['item'], 20) }}</td>
                                        <td><span class="pill pill-mute">{{ $d['condition'] }}</span></td>
                                        <td><span class="pill pill-{{ $statusTone($d['status']) === 'mute' ? 'ok' : $statusTone($d['status']) }}">{{ ucfirst($d['status']) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="table-empty">Aucun don</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ LIGNE 6 — Réservations ============ --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="section-card">
                <div class="card-header">
                    <h5><i class="bi bi-calendar-check"></i>Dernières réservations</h5>
                    <a href="{{ route('back.reservations.index') }}" class="btn-soft">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="bi-table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Utilisateur</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReservations as $r)
                                    <tr>
                                        <td class="id">{{ $r['id'] }}</td>
                                        <td>{{ $r['user'] }}</td>
                                        <td class="num">{{ $r['product_id'] }}</td>
                                        <td class="num">{{ $r['quantity'] }}</td>
                                        <td><span class="pill pill-{{ $statusTone($r['status']) }}">{{ ucfirst($r['status']) }}</span></td>
                                        <td class="num" style="color:var(--ink-soft)">{{ \Carbon\Carbon::parse($r['date'])->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="table-empty">Aucune réservation</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ============ CHART.JS ============ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trendData          = @json($trendChart);
    const distributionData   = @json($distributionChart);
    const revenueData        = @json($revenueChart);
    const moduleData         = @json($moduleChart);
    const campaignStatusData = @json($campaignStatusChart);

    // ---- Réglages globaux ----
    Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#5b6b62';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.boxWidth      = 8;
    Chart.defaults.plugins.legend.labels.padding       = 14;
    Chart.defaults.plugins.tooltip.backgroundColor     = '#15271f';
    Chart.defaults.plugins.tooltip.padding             = 10;
    Chart.defaults.plugins.tooltip.cornerRadius        = 8;
    Chart.defaults.plugins.tooltip.titleFont           = { weight: '600' };

    const grid = { color: '#eef1ed', drawBorder: false };
    const mk = (id, config) => {
        const el = document.getElementById(id);
        return el ? new Chart(el, config) : null;
    };

    // ---- Évolution de la collecte ----
    if (trendData?.datasets?.length) {
        mk('trendChart', {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: trendData.datasets.map(d => ({
                    label: d.label,
                    data: d.data,
                    borderColor: d.borderColor,
                    backgroundColor: d.backgroundColor,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'top', align: 'end' } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid, title: { display: true, text: 'kg' } }
                }
            }
        });
    }

    // ---- Répartition par catégorie ----
    if (distributionData?.data?.length) {
        mk('distributionChart', {
            type: 'doughnut',
            data: {
                labels: distributionData.labels,
                datasets: [{
                    data: distributionData.data,
                    backgroundColor: distributionData.colors,
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // ---- Activité mensuelle ----
    if (revenueData?.labels?.length) {
        mk('revenueChart', {
            type: 'bar',
            data: {
                labels: revenueData.labels,
                datasets: [
                    { label: 'Revenus (DT)', data: revenueData.orders,       backgroundColor: '#2d6a4f', borderRadius: 6, yAxisID: 'y' },
                    { label: 'Dons',         data: revenueData.donations,    backgroundColor: '#0f95b0', borderRadius: 6, yAxisID: 'y1' },
                    { label: 'Réservations', data: revenueData.reservations, backgroundColor: '#e8b020', borderRadius: 6, yAxisID: 'y1' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'top', align: 'end' } },
                scales: {
                    x:  { grid: { display: false } },
                    y:  { type: 'linear', position: 'left',  beginAtZero: true, grid, title: { display: true, text: 'DT' } },
                    y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'Nombre' } }
                }
            }
        });
    }

    // ---- Activité par module ----
    if (moduleData?.data?.length) {
        mk('moduleChart', {
            type: 'bar',
            data: {
                labels: moduleData.labels,
                datasets: [{ data: moduleData.data, backgroundColor: moduleData.colors, borderRadius: 6 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid }
                }
            }
        });
    }

    // ---- Statut des campagnes ----
    if (campaignStatusData?.data?.length) {
        mk('campaignStatusChart', {
            type: 'doughnut',
            data: {
                labels: campaignStatusData.labels,
                datasets: [{
                    data: campaignStatusData.data,
                    backgroundColor: campaignStatusData.colors,
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>
@endsection