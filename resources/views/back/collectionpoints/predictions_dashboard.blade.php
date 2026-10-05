@extends('back.layout')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .collection-ai {
        --ai-ink: #153f3a;
        --ai-muted: #68817c;
        --ai-line: rgba(18, 63, 55, 0.1);
        color: var(--ai-ink);
    }

    .collection-ai-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .collection-ai-header h1 {
        margin: 0;
        color: var(--ai-ink);
        font-size: 1.55rem;
        font-weight: 700;
    }

    .collection-ai-header p {
        margin: 0.35rem 0 0;
        color: var(--ai-muted);
        font-size: 0.9rem;
    }

    .collection-ai-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .collection-ai .btn-primary {
        border-color: #137d52;
        background: #137d52;
    }

    .collection-ai .btn-primary:hover {
        border-color: #0b6843;
        background: #0b6843;
    }

    .ai-metrics {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .ai-metric {
        min-width: 0;
        padding: 0.9rem 1rem;
        border: 1px solid var(--ai-line);
        border-left: 3px solid var(--metric-accent, #1fa26a);
        border-radius: 8px;
        background: #fff;
    }

    .ai-metric-label {
        color: var(--ai-muted);
        font-size: 0.78rem;
    }

    .ai-metric-value {
        margin-top: 0.2rem;
        color: var(--ai-ink);
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .ai-panel {
        border: 1px solid var(--ai-line);
        border-radius: 8px;
        background: #fff;
    }

    .ai-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem;
        border-bottom: 1px solid var(--ai-line);
    }

    .ai-panel-header h2 {
        margin: 0;
        color: var(--ai-ink);
        font-size: 1rem;
        font-weight: 700;
    }

    .ai-panel-header p {
        margin: 0.25rem 0 0;
        color: var(--ai-muted);
        font-size: 0.8rem;
    }

    .ai-chart-wrap {
        position: relative;
        min-height: 270px;
        padding: 1rem;
    }

    .ai-content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) minmax(250px, 0.8fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .ai-table-tools {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--ai-line);
    }

    .ai-search {
        width: min(100%, 270px);
    }

    .ai-table-wrap {
        overflow-x: auto;
    }

    .ai-table {
        width: 100%;
        margin: 0;
        vertical-align: middle;
    }

    .ai-table th {
        padding: 0.7rem 1rem;
        border-bottom-color: var(--ai-line);
        background: #f6faf8;
        color: var(--ai-muted);
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ai-table td {
        padding: 0.8rem 1rem;
        border-bottom-color: var(--ai-line);
        color: var(--ai-ink);
        font-size: 0.84rem;
    }

    .ai-table tr:last-child td {
        border-bottom: 0;
    }

    .ai-point-name {
        font-weight: 650;
    }

    .ai-point-address,
    .ai-secondary {
        display: block;
        margin-top: 0.18rem;
        color: var(--ai-muted);
        font-size: 0.74rem;
    }

    .ai-volume {
        min-width: 150px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ai-volume small {
        display: block;
        margin-top: 0.2rem;
        color: var(--ai-muted);
        font-size: 0.7rem;
        font-weight: 400;
        white-space: normal;
    }

    .ai-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 650;
        white-space: nowrap;
    }

    .ai-status-normal { background: #e6f5ed; color: #176b43; }
    .ai-status-almost { background: #fff3dc; color: #8a5700; }
    .ai-status-full { background: #fde9e7; color: #a02d25; }
    .ai-status-unknown { background: #eef2f1; color: #526762; }

    .ai-row-insufficient {
        background: #fffdf7;
    }

    .ai-empty {
        padding: 1.25rem;
        color: var(--ai-muted);
        font-size: 0.84rem;
        text-align: center;
    }

    .ai-priority-list {
        display: grid;
        gap: 0.65rem;
        padding: 0.85rem;
    }

    .ai-priority-item {
        padding: 0.75rem;
        border: 1px solid var(--ai-line);
        border-left: 3px solid var(--priority-accent, #d89b28);
        border-radius: 6px;
    }

    .ai-priority-item strong {
        display: block;
        font-size: 0.84rem;
    }

    .ai-priority-item small {
        display: block;
        margin-top: 0.2rem;
        color: var(--ai-muted);
    }

    .ai-note {
        margin: 0.75rem 1rem 1rem;
        padding: 0.65rem 0.75rem;
        border-radius: 6px;
        background: #f4f8f6;
        color: var(--ai-muted);
        font-size: 0.74rem;
    }

    .ai-update-time {
        color: var(--ai-muted);
        font-size: 0.75rem;
        white-space: nowrap;
    }

    @media (max-width: 1100px) {
        .ai-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .ai-content-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 650px) {
        .collection-ai-header { flex-direction: column; }
        .collection-ai-header h1 { font-size: 1.3rem; }
        .ai-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ai-table-tools { align-items: stretch; flex-direction: column; }
        .ai-search { width: 100%; }
        .ai-chart-wrap { min-height: 220px; padding: 0.65rem; }
    }
</style>

<div class="collection-ai">
    <header class="collection-ai-header">
        <div>
            <h1><i class="bi bi-graph-up-arrow me-2" aria-hidden="true"></i>Collection forecasts</h1>
            <p>Next-day volume estimates and collection-point capacity status</p>
        </div>
        <div class="collection-ai-actions">
            <span class="ai-update-time align-self-center">Updated <time id="lastUpdateTime">--:--</time></span>
            <button id="exportData" type="button" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-1" aria-hidden="true"></i>Export CSV
            </button>
            <button id="refreshPredictions" type="button" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>Refresh
                <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-1 d-none" aria-hidden="true"></span>
            </button>
        </div>
    </header>

    <section class="ai-metrics" aria-label="Forecast summary">
        <div class="ai-metric" style="--metric-accent:#1fa26a">
            <div class="ai-metric-label">Monitored points</div>
            <div class="ai-metric-value" id="monitoredCount">{{ count($collectionPoints) }}</div>
        </div>
        <div class="ai-metric" style="--metric-accent:#1fa26a">
            <div class="ai-metric-label">Normal</div>
            <div class="ai-metric-value" id="normalCount">0</div>
        </div>
        <div class="ai-metric" style="--metric-accent:#d89b28">
            <div class="ai-metric-label">Almost full</div>
            <div class="ai-metric-value" id="almostFullCount">0</div>
        </div>
        <div class="ai-metric" style="--metric-accent:#bd4b43">
            <div class="ai-metric-label">Full</div>
            <div class="ai-metric-value" id="fullCount">0</div>
        </div>
        <div class="ai-metric" style="--metric-accent:#758984">
            <div class="ai-metric-label">Needs more data</div>
            <div class="ai-metric-value" id="insufficientCount">0</div>
        </div>
    </section>

    <section class="ai-panel" aria-labelledby="forecast-chart-title">
        <div class="ai-panel-header">
            <div>
                <h2 id="forecast-chart-title">Predicted volume by collection point</h2>
                <p>Forecasted kilograms for the next day; bars are colored by capacity status</p>
            </div>
        </div>
        <div class="ai-chart-wrap">
            <canvas id="volumeChart" role="img" aria-label="Predicted volume chart"></canvas>
            <div id="noDataMessage" class="ai-empty d-none">No reliable predictions are available yet.</div>
        </div>
    </section>

    <div class="ai-content-grid">
        <section class="ai-panel" aria-labelledby="points-table-title">
            <div class="ai-panel-header">
                <div>
                    <h2 id="points-table-title">Collection points</h2>
                    <p>Forecast, model, data coverage and latest collection</p>
                </div>
            </div>
            <div class="ai-table-tools">
                <span class="ai-secondary mt-0">Showing <span id="visibleCount">{{ count($collectionPoints) }}</span> of {{ count($collectionPoints) }} points</span>
                <input type="search" id="searchInput" class="form-control form-control-sm ai-search" placeholder="Search collection points" aria-label="Search collection points">
            </div>
            <div class="ai-table-wrap">
                <table class="table ai-table">
                    <thead>
                        <tr>
                            <th scope="col">Collection point</th>
                            <th scope="col">Forecast</th>
                            <th scope="col">Status</th>
                            <th scope="col">Last collection</th>
                        </tr>
                    </thead>
                    <tbody id="predictionsBody" aria-live="polite">
                        @forelse($collectionPoints as $point)
                        <tr id="row-{{ $point->id }}" data-id="{{ $point->id }}">
                            <td>
                                <span class="ai-point-name">{{ $point->name }}</span>
                                <span class="ai-point-address">{{ $point->address ?? 'Address not specified' }}</span>
                            </td>
                            <td id="volume-{{ $point->id }}" class="ai-volume"><span class="spinner-border spinner-border-sm" aria-label="Loading forecast"></span></td>
                            <td id="status-{{ $point->id }}"><span class="ai-status ai-status-unknown">Loading</span></td>
                            <td id="lastCollection-{{ $point->id }}" class="text-muted small">--</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="ai-empty">No collection points are configured.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="ai-note">Daily gaps in the source history are treated as zero volume. Coverage is shown to make that assumption visible.</p>
        </section>

        <aside class="ai-panel" aria-labelledby="priority-title">
            <div class="ai-panel-header">
                <div>
                    <h2 id="priority-title">Priority points</h2>
                    <p>Highest predicted capacity usage</p>
                </div>
            </div>
            <div id="priorityList" class="ai-priority-list">
                <div class="ai-empty">Loading priority points…</div>
            </div>
        </aside>
    </div>
</div>

<script>
(() => {
    const rows = Array.from(document.querySelectorAll('#predictionsBody tr[data-id]'));
    const chartCanvas = document.getElementById('volumeChart');
    const noDataMessage = document.getElementById('noDataMessage');
    const refreshButton = document.getElementById('refreshPredictions');
    const spinner = document.getElementById('loadingSpinner');
    let chart;
    const points = new Map();

    function statusLabel(status) {
        return ({normal: 'Normal', almost_full: 'Almost full', full: 'Full', insufficient_data: 'Needs data', unknown: 'Unavailable'})[status] || 'Unavailable';
    }

    function statusClass(status) {
        return ({normal: 'ai-status-normal', almost_full: 'ai-status-almost', full: 'ai-status-full'})[status] || 'ai-status-unknown';
    }

    function setCellText(element, text) {
        element.replaceChildren(document.createTextNode(text));
    }

    function updateCounters() {
        const values = Array.from(points.values());
        document.getElementById('normalCount').textContent = values.filter(point => point.status === 'normal').length;
        document.getElementById('almostFullCount').textContent = values.filter(point => point.status === 'almost_full').length;
        document.getElementById('fullCount').textContent = values.filter(point => point.status === 'full').length;
        document.getElementById('insufficientCount').textContent = values.filter(point => point.status === 'insufficient_data').length;
    }

    function updatePriorityList() {
        const priorityList = document.getElementById('priorityList');
        const priorityPoints = Array.from(points.values())
            .filter(point => point.status === 'full' || point.status === 'almost_full')
            .sort((left, right) => right.ratio - left.ratio)
            .slice(0, 5);

        if (!priorityPoints.length) {
            setCellText(priorityList, 'No points currently need priority collection.');
            priorityList.classList.add('ai-empty');
            return;
        }

        priorityList.classList.remove('ai-empty');
        priorityList.replaceChildren(...priorityPoints.map(point => {
            const item = document.createElement('div');
            item.className = 'ai-priority-item';
            item.style.setProperty('--priority-accent', point.status === 'full' ? '#bd4b43' : '#d89b28');
            const name = document.createElement('strong');
            name.textContent = point.name;
            const detail = document.createElement('small');
            detail.textContent = `${statusLabel(point.status)} · ${Math.round(point.ratio)}% capacity · ${Math.round(point.volume)} kg`;
            item.append(name, detail);
            return item;
        }));
    }

    function updateChart() {
        const chartPoints = Array.from(points.values()).filter(point => Number.isFinite(point.volume));
        if (chart) chart.destroy();
        noDataMessage.classList.toggle('d-none', chartPoints.length > 0);
        if (!chartCanvas || !chartPoints.length || typeof Chart === 'undefined') return;

        chart = new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: chartPoints.map(point => point.name),
                datasets: [{
                    label: 'Predicted volume (kg)',
                    data: chartPoints.map(point => point.volume),
                    backgroundColor: chartPoints.map(point => point.status === 'full' ? '#bd4b43' : point.status === 'almost_full' ? '#d89b28' : '#1fa26a'),
                    borderRadius: 4,
                    maxBarThickness: 54,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {beginAtZero: true, title: {display: true, text: 'Kilograms'}},
                    x: {grid: {display: false}, ticks: {maxRotation: 35, minRotation: 0}},
                },
                plugins: {
                    legend: {display: false},
                    tooltip: {callbacks: {label: context => `${context.raw} kg`}},
                },
            },
        });
    }

    function renderLastCollection(id, value) {
        const cell = document.getElementById(`lastCollection-${id}`);
        const date = value ? new Date(value) : null;
        setCellText(cell, date && !Number.isNaN(date.getTime()) ? date.toLocaleDateString() : 'No record');
    }

    async function loadPoint(row) {
        const id = row.dataset.id;
        const volumeCell = document.getElementById(`volume-${id}`);
        const statusCell = document.getElementById(`status-${id}`);
        const response = await fetch(`/collection-ai/predict/${encodeURIComponent(id)}`, {headers: {Accept: 'application/json'}});
        const data = await response.json().catch(() => ({}));

        if (response.status === 422 && data.status === 'insufficient_data') {
            row.classList.add('ai-row-insufficient');
            points.set(id, {
                id,
                name: row.querySelector('.ai-point-name').textContent,
                status: 'insufficient_data',
                volume: null,
                ratio: 0,
            });
            setCellText(volumeCell, `${data.training_days ?? 0}/${data.required_training_days ?? 2} recent days required`);
            const badge = document.createElement('span');
            badge.className = `ai-status ${statusClass('insufficient_data')}`;
            badge.textContent = statusLabel('insufficient_data');
            statusCell.replaceChildren(badge);
            renderLastCollection(id, data.last_collection_at);
            return;
        }

        if (!response.ok) throw new Error(data.error || `HTTP ${response.status}`);

        const volume = Number(data.predicted_volume);
        const ratio = Number.parseFloat(data.ratio) || (Number(data.capacity) ? volume / Number(data.capacity) * 100 : 0);
        points.set(id, {
            id,
            name: row.querySelector('.ai-point-name').textContent,
            status: data.status,
            volume,
            ratio,
            forecastModel: data.forecast_model,
            observationCoverage: Number(data.observation_coverage),
            confidence: Number(data.confidence),
            lowerBound: Number(data.lower_bound),
            upperBound: Number(data.upper_bound),
        });

        const volumeLabel = document.createElement('span');
        volumeLabel.textContent = `${Math.round(volume)} kg`;
        const details = document.createElement('small');
        const parts = [];
        if (Number.isFinite(data.lower_bound) && Number.isFinite(data.upper_bound)) {
            parts.push(`${Math.round(data.lower_bound)}–${Math.round(data.upper_bound)} kg range`);
        }
        if (data.forecast_model) parts.push(data.forecast_model.replaceAll('_', ' '));
        if (Number.isFinite(data.observation_coverage)) parts.push(`${Math.round(data.observation_coverage * 100)}% observed`);
        details.textContent = parts.join(' · ');
        volumeCell.replaceChildren(volumeLabel, details);

        const badge = document.createElement('span');
        badge.className = `ai-status ${statusClass(data.status)}`;
        badge.textContent = statusLabel(data.status);
        statusCell.replaceChildren(badge);
        row.classList.remove('ai-row-insufficient');
        renderLastCollection(id, data.last_collection_at);
    }

    async function refreshPredictions() {
        refreshButton.disabled = true;
        spinner.classList.remove('d-none');
        points.clear();
        await Promise.all(rows.map(async row => {
            try {
                await loadPoint(row);
            } catch (error) {
                const id = row.dataset.id;
                points.delete(id);
                const statusCell = document.getElementById(`status-${id}`);
                const badge = document.createElement('span');
                badge.className = `ai-status ${statusClass('unknown')}`;
                badge.textContent = 'Service unavailable';
                statusCell.replaceChildren(badge);
                setCellText(document.getElementById(`volume-${id}`), 'No forecast');
                renderLastCollection(id, null);
                console.warn(`Forecast unavailable for collection point ${id}:`, error.message);
            }
        }));
        updateCounters();
        updatePriorityList();
        updateChart();
        document.getElementById('lastUpdateTime').textContent = new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
        refreshButton.disabled = false;
        spinner.classList.add('d-none');
    }

    document.getElementById('searchInput').addEventListener('input', event => {
        const search = event.target.value.trim().toLocaleLowerCase();
        let visible = 0;
        for (const row of rows) {
            const show = row.querySelector('.ai-point-name').textContent.toLocaleLowerCase().includes(search);
            row.hidden = !show;
            if (show) visible += 1;
        }
        document.getElementById('visibleCount').textContent = visible;
    });

    document.getElementById('exportData').addEventListener('click', () => {
        const csvRows = [['Collection point', 'Predicted kg', 'Status', 'Capacity ratio']];
        for (const point of points.values()) {
            csvRows.push([point.name, point.volume ?? '', statusLabel(point.status), point.volume === null ? '' : `${point.ratio.toFixed(1)}%`]);
        }
        const csv = csvRows.map(row => row.map(value => `"${String(value).replaceAll('"', '""')}"`).join(',')).join('\r\n');
        const url = URL.createObjectURL(new Blob([csv], {type: 'text/csv;charset=utf-8'}));
        const link = document.createElement('a');
        link.href = url;
        link.download = 'collection-forecasts.csv';
        link.click();
        URL.revokeObjectURL(url);
    });

    refreshButton.addEventListener('click', refreshPredictions);
    refreshPredictions();
})();
</script>
@endsection
