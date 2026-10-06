@extends('back.layout')

@section('content')
<style>
    /* Card style */
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        transition: transform 0.2s;
        border: none;
        margin-bottom: 1.5rem;
    }
    .card:hover {
        transform: translateY(-5px);
    }

    /* Stat cards */
    .stat-card {
        border-left: 4px solid;
        padding: 1rem;
    }
    .stat-card.primary {
        border-left-color: #0d6efd;
    }
    .stat-card.success {
        border-left-color: #198754;
    }
    .stat-card.warning {
        border-left-color: #ffc107;
    }
    .stat-card.danger {
        border-left-color: #dc3545;
    }

    /* Chart containers */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .chart-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
        text-align: center;
        padding: 1rem;
    }

    .chart-empty i {
        font-size: 2rem;
        opacity: 0.5;
    }

    /* Recent activity */
    .activity-item {
        border-left: 3px solid #0d6efd;
        padding-left: 1rem;
        margin-bottom: 1rem;
    }

    /* Custom colors */
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }

    /* Progress bars */
    .progress {
        height: 8px;
    }

    .campaign-meta {
        color: #6c757d;
        font-size: 0.78rem;
    }

    /* Top waste items */
    .waste-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background-color: #f8f9fa;
    }

    .empty-panel {
        padding: 2rem 1rem;
        color: #6c757d;
        text-align: center;
        font-size: 0.9rem;
    }
</style>

<div class="p-4">
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card primary bg-primary-light">
                <div class="card-body">
                    <div class="d-flex justify-between">
                        <div>
                            <h5 class="card-title">Total Waste</h5>
                            <h2 class="mb-0">{{ number_format($totalWeight, 1) }} kg</h2>
                            <span class="text-muted">{{ $wasteCount }} {{ \Illuminate\Support\Str::plural('entry', $wasteCount) }} recorded</span>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-trash fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card success bg-success-light">
                <div class="card-body">
                    <div class="d-flex justify-between">
                        <div>
                            <h5 class="card-title">Recycled</h5>
                            <h2 class="mb-0">{{ number_format($recycledWeight, 1) }} kg</h2>
                            <span class="text-success">{{ $recycledShare }}% of total weight</span>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-recycle fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card warning bg-warning-light">
                <div class="card-body">
                    <div class="d-flex justify-between">
                        <div>
                            <h5 class="card-title">Active Campaigns</h5>
                            <h2 class="mb-0">{{ $activeCampaigns }}</h2>
                            <span class="text-muted">{{ $participationCount }} {{ \Illuminate\Support\Str::plural('participation', $participationCount) }}</span>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-megaphone fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card danger bg-danger-light">
                <div class="card-body">
                    <div class="d-flex justify-between">
                        <div>
                            <h5 class="card-title">Pending Requests</h5>
                            <h2 class="mb-0">{{ $pendingRequests }}</h2>
                            <span class="text-muted">Orders and reservations awaiting action</span>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Waste Collection Trends</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                        @if(empty($trendChart['datasets']))
                        <div class="chart-empty" id="trendEmpty">
                            <i class="bi bi-graph-up"></i>
                            <p class="mb-0 mt-2">No waste entries recorded yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Waste Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distributionChart"></canvas>
                        @if(empty($distributionChart['data']))
                        <div class="chart-empty" id="distributionEmpty">
                            <i class="bi bi-pie-chart"></i>
                            <p class="mb-0 mt-2">No waste entries recorded yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress and Activities Row -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Campaign Progress</h5>
                </div>
                <div class="card-body">
                    @forelse($campaignProgress as $campaign)
                    <div class="mb-3">
                        <div class="d-flex justify-between mb-1">
                            <span>{{ $campaign['title'] }}</span>
                            <span>{{ $campaign['progress'] }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $campaign['progress'] >= 75 ? 'bg-success' : ($campaign['progress'] >= 40 ? 'bg-warning' : 'bg-info') }}"
                                 role="progressbar"
                                 style="width: {{ $campaign['progress'] }}%"
                                 aria-valuenow="{{ $campaign['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="campaign-meta">{{ $campaign['location'] }} · {{ $campaign['period'] }} · {{ $campaign['participants'] }} participants</small>
                    </div>
                    @empty
                    <div class="empty-panel">
                        <i class="bi bi-megaphone d-block mb-2 fs-4"></i>
                        No active campaigns right now.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                    <div class="activity-item">
                        <h6 class="mb-1">{{ $activity['title'] }}</h6>
                        <p class="mb-1 text-muted">{{ $activity['detail'] }}</p>
                        <small class="text-muted">{{ $activity['time'] }}</small>
                    </div>
                    @empty
                    <div class="empty-panel">
                        <i class="bi bi-clock-history d-block mb-2 fs-4"></i>
                        Nothing has happened yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Top Waste Items -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Waste Categories</h5>
                </div>
                <div class="card-body">
                    @if(empty($topWastes))
                    <div class="empty-panel">
                        <i class="bi bi-trash d-block mb-2 fs-4"></i>
                        No waste entries recorded yet.
                    </div>
                    @else
                    <div class="row">
                        @foreach($topWastes as $index => $category)
                        <div class="col-md-3">
                            <div class="waste-item">
                                <div class="me-3">
                                    <i class="bi bi-recycle fs-4 text-{{ ['primary', 'success', 'warning', 'info'][$index % 4] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $category['name'] }}</h6>
                                    <small class="text-muted">{{ number_format($category['weight'], 1) }} kg · {{ $category['count'] }} {{ \Illuminate\Support\Str::plural('entry', $category['count']) }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ ['primary', 'success', 'warning', 'info'][$index % 4] }}">{{ $category['share'] }}%</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-3 text-md-end">
                            <a href="{{ route('wastes.index') }}" class="btn btn-outline-primary">View All Items</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/chart.js/chart.umd.js') }}"></script>
@if(!file_exists(public_path('vendor/chart.js/chart.umd.js')))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trendData = @json($trendChart);
        const distributionData = @json($distributionChart);
        const trendEmpty = document.getElementById('trendEmpty');
        const distributionEmpty = document.getElementById('distributionEmpty');

        if (typeof Chart === 'undefined') {
            ['trendChart', 'distributionChart'].forEach(id => {
                const canvas = document.getElementById(id);
                canvas.style.display = 'none';
                const notice = document.createElement('div');
                notice.className = 'chart-empty';
                notice.innerHTML = '<i class="bi bi-bar-chart"></i><p class="mb-0 mt-2">Charts could not be loaded.</p>';
                canvas.parentElement.appendChild(notice);
            });
            return;
        }

        if (trendData.datasets.length) {
            new Chart(document.getElementById('trendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendData.labels,
                    datasets: trendData.datasets.map(dataset => ({
                        label: dataset.label,
                        data: dataset.data,
                        borderColor: dataset.borderColor,
                        backgroundColor: dataset.backgroundColor,
                        tension: 0.4,
                        fill: true
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {position: 'top'}
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {display: true, text: 'Waste Collected (kg)'}
                        }
                    }
                }
            });
        } else if (trendEmpty) {
            document.getElementById('trendChart').style.display = 'none';
        }

        if (distributionData.data.length) {
            new Chart(document.getElementById('distributionChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: distributionData.labels,
                    datasets: [{
                        data: distributionData.data,
                        backgroundColor: distributionData.colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {position: 'bottom'}
                    }
                }
            });
        } else if (distributionEmpty) {
            document.getElementById('distributionChart').style.display = 'none';
        }
    });
</script>
@endsection
