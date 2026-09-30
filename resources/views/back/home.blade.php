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
        border-radius: 4px;
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
</style>

<div class="p-4">
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card primary bg-primary-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Waste</h5>
                            <h2 class="mb-0">1,254</h2>
                            <span class="text-success"><i class="bi bi-arrow-up"></i> 12.5%</span>
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
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Recycled</h5>
                            <h2 class="mb-0">842</h2>
                            <span class="text-success"><i class="bi bi-arrow-up"></i> 8.3%</span>
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
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Active Campaigns</h5>
                            <h2 class="mb-0">15</h2>
                            <span class="text-danger"><i class="bi bi-arrow-down"></i> 2.1%</span>
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
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Pending Requests</h5>
                            <h2 class="mb-0">23</h2>
                            <span class="text-success"><i class="bi bi-arrow-up"></i> 5.7%</span>
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
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Plastic Collection Drive</span>
                            <span>75%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>E-Waste Awareness</span>
                            <span>45%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 45%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Composting Initiative</span>
                            <span>90%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 90%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Paper Recycling</span>
                            <span>60%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 60%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="activity-item">
                        <h6 class="mb-1">New waste collection request</h6>
                        <p class="mb-1 text-muted">Plastic bottles from Green Office Ltd.</p>
                        <small class="text-muted">2 hours ago</small>
                    </div>
                    <div class="activity-item">
                        <h6 class="mb-1">Campaign completed</h6>
                        <p class="mb-1 text-muted">"Recycle Electronics" reached its target</p>
                        <small class="text-muted">Yesterday</small>
                    </div>
                    <div class="activity-item">
                        <h6 class="mb-1">New category added</h6>
                        <p class="mb-1 text-muted">"Biodegradable Packaging" category created</p>
                        <small class="text-muted">2 days ago</small>
                    </div>
                    <div class="activity-item">
                        <h6 class="mb-1">Monthly report generated</h6>
                        <p class="mb-1 text-muted">August 2023 waste management report</p>
                        <small class="text-muted">3 days ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Waste Items -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Waste Items</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="waste-item">
                                <div class="me-3">
                                    <i class="bi bi-cup-straw text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Plastic Bottles</h6>
                                    <small class="text-muted">245 items collected</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">32%</span>
                                </div>
                            </div>
                            <div class="waste-item">
                                <div class="me-3">
                                    <i class="bi bi-cpu text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Electronics</h6>
                                    <small class="text-muted">189 items collected</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">25%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="waste-item">
                                <div class="me-3">
                                    <i class="bi bi-newspaper text-warning fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Paper & Cardboard</h6>
                                    <small class="text-muted">176 items collected</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning">23%</span>
                                </div>
                            </div>
                            <div class="waste-item">
                                <div class="me-3">
                                    <i class="bi bi-cup text-info fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Glass</h6>
                                    <small class="text-muted">98 items collected</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">13%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="waste-item">
                                <div class="me-3">
                                    <i class="bi bi-bag text-danger fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Textiles</h6>
                                    <small class="text-muted">47 items collected</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger">6%</span>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-primary">View All Items</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleSidebarBtn = document.getElementById('toggleSidebar');

    toggleSidebarBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });

    function toggleSubmenu(e) {
        e.preventDefault();
        const parent = e.target.closest('.nav-item');
        parent.classList.toggle('show');
    }

    // Charts initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [
                    {
                        label: 'Plastic Waste',
                        data: [65, 59, 80, 81, 56, 55, 40, 75],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Electronic Waste',
                        data: [28, 48, 40, 19, 86, 27, 90, 65],
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Paper Waste',
                        data: [45, 25, 60, 35, 70, 45, 55, 40],
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Waste Collected (kg)'
                        }
                    }
                }
            }
        });

        // Distribution Chart
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        const distributionChart = new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Plastic', 'Electronics', 'Paper', 'Glass', 'Textiles', 'Other'],
                datasets: [{
                    data: [32, 25, 23, 13, 6, 1],
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#0dcaf0',
                        '#dc3545',
                        '#6c757d'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });
</script>
@endsection
