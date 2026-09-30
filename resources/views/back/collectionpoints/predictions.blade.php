@extends('back.layout')
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Predictions - Collection Points | Biodex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1a1f36;
            --sidebar-hover: #2d3748;
            --primary-color: #10b981;
            --text-light: #e2e8f0;
            --text-muted: #94a3b8;
        }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #f8fafc;
        }
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #0f172a 100%);
            width: 280px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .sidebar-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }
        .brand-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), #059669);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .brand-text {
            font-weight: 700;
            font-size: 1.25rem;
            background: linear-gradient(135deg, #fff, var(--text-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .nav-section {
            margin-bottom: 1.5rem;
        }
        .nav-section-title {
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 1.5rem 0.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-item {
            margin: 0.25rem 0.75rem;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary-color);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        .nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(4px);
        }
        .nav-link:hover::before {
            transform: scaleY(1);
        }
        .nav-link.active {
            background: rgba(16, 185, 129, 0.15);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }
        .nav-link.active::before {
            transform: scaleY(1);
        }
        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .nav-badge {
            margin-left: auto;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            background: #f8fafc;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-menu-btn {
                display: block !important;
            }
        }
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--sidebar-bg);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 0.5rem;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Styles for AI prediction content */
        .status-badge {
            @apply px-3 py-1 rounded-full text-sm font-medium;
        }
        .status-full {
            @apply bg-red-100 text-red-800;
        }
        .status-almost-full {
            @apply bg-orange-100 text-orange-800;
        }
        .status-normal {
            @apply bg-green-100 text-green-800;
        }
        .status-unknown {
            @apply bg-gray-100 text-gray-800;
        }
        .card {
            @apply bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100;
        }
        .loading-pulse {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Design enhancements */
        .btn {
            @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center justify-center;
        }
        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700 shadow-sm;
        }
        .btn-outline {
            @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 shadow-sm;
        }
        .table-header {
            @apply bg-gray-50 text-left text-sm font-semibold text-gray-600;
        }
        .table-cell {
            @apply p-4 text-sm;
        }
        .progress-bar {
            @apply h-2 bg-gray-200 rounded-full overflow-hidden;
        }
        .progress-fill {
            @apply h-full rounded-full transition-all duration-500;
        }
    </style>
</head>
<body class="h-100">
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container mx-auto px-4 py-6">
            <!-- Header with information -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-blue-500 mr-3"></i>
                        AI Predictions for Collection Points
                    </h1>
                    <p class="text-gray-600 mt-2">Predictive analysis of waste volumes to optimize collection</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center text-blue-700">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="font-medium">Last update:</span>
                        <span id="lastUpdateTime" class="ml-2">{{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistics cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="card p-4 border-l-4 border-blue-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Monitored points</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ count($collectionPoints) }}</h3>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-map-marker-alt text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="card p-4 border-l-4 border-green-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Normal status</p>
                            <h3 id="normalCount" class="text-2xl font-bold text-gray-800">-</h3>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="card p-4 border-l-4 border-orange-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Almost full</p>
                            <h3 id="almostFullCount" class="text-2xl font-bold text-gray-800">-</h3>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="card p-4 border-l-4 border-red-500">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Full</p>
                            <h3 id="fullCount" class="text-2xl font-bold text-gray-800">-</h3>
                        </div>
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-times-circle text-red-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main chart at the top -->
            <div class="card p-5 mb-6 fade-in">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Evolution of Predicted Volumes</h2>
                        <p class="text-gray-600 text-sm">Predictions of collection point fill levels</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button id="chartDay" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg font-medium">Day</button>
                        <button id="chartWeek" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Week</button>
                        <button id="chartMonth" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Month</button>
                    </div>
                </div>
                <div class="relative">
                    <canvas id="volumeChart" height="280"></canvas>
                    <div id="noDataMessage" class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 bg-white bg-opacity-90 hidden">
                        <i class="fas fa-chart-bar text-4xl mb-2 text-gray-300"></i>
                        <p class="text-lg">Not enough data to display the chart</p>
                        <p class="text-sm mt-1">Data will appear after the first update</p>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div class="flex flex-wrap gap-2">
                    <button id="refreshPredictions" class="btn btn-primary">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh Predictions
                        <span id="loadingSpinner" class="ml-2 hidden">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                    
                    <button id="exportData" class="btn btn-outline">
                        <i class="fas fa-download mr-2"></i>
                        Export Data
                    </button>
                </div>
                
                <div class="flex items-center bg-white rounded-lg border border-gray-300 px-3 py-2 shadow-sm">
                    <i class="fas fa-search text-gray-400 mr-2"></i>
                    <input type="text" id="searchInput" placeholder="Search for a point..." class="outline-none bg-transparent w-full md:w-64">
                </div>
            </div>

            <!-- Predictions table and priority points -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Predictions table -->
                <div class="lg:col-span-2 card mb-8 fade-in">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800">Collection Points</h2>
                        <p class="text-gray-600 text-sm">Volume predictions and real-time statuses</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-cell">Collection Point</th>
                                    <th class="table-cell">Predicted Volume</th>
                                    <th class="table-cell">Status</th>
                                    <th class="table-cell">Last Collection</th>
                                    <th class="table-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="predictionsBody" class="divide-y divide-gray-200">
                                @foreach($collectionPoints as $point)
                                <tr id="row-{{ $point->id }}" class="hover:bg-gray-50 transition duration-150">
                                    <td class="table-cell">
                                        <div class="flex items-center">
                                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                                <i class="fas fa-trash-alt text-blue-500"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 point-name">{{ $point->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $point->address ?? 'Address not specified' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td id="volume-{{ $point->id }}" class="table-cell">
                                        <div class="loading-pulse bg-gray-200 h-6 w-16 rounded"></div>
                                    </td>
                                    <td id="status-{{ $point->id }}" class="table-cell">
                                        <div class="loading-pulse bg-gray-200 h-6 w-24 rounded"></div>
                                    </td>
                                    <td id="lastCollection-{{ $point->id }}" class="table-cell text-gray-500">
                                        <div class="loading-pulse bg-gray-200 h-6 w-32 rounded"></div>
                                    </td>
                                    <td class="table-cell">
                                        <button class="text-blue-500 hover:text-blue-700 transition duration-150 p-1 rounded" title="View details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-200 text-sm text-gray-500 flex justify-between items-center">
                        <div>Displaying <span id="visibleCount">{{ count($collectionPoints) }}</span> points out of {{ count($collectionPoints) }}</div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 transition">Previous</button>
                            <button class="px-3 py-1 border border-gray-300 rounded bg-blue-50 text-blue-600 border-blue-200">1</button>
                            <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50 transition">Next</button>
                        </div>
                    </div>
                </div>
                
                <!-- Priority points -->
                <div class="card p-5 h-fit">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Priority Points</h2>
                    <p class="text-gray-600 mb-4">Points requiring immediate attention</p>
                    
                    <div id="priorityList" class="space-y-3">
                        <div class="loading-pulse bg-gray-200 h-16 rounded-lg"></div>
                        <div class="loading-pulse bg-gray-200 h-16 rounded-lg"></div>
                        <div class="loading-pulse bg-gray-200 h-16 rounded-lg"></div>
                    </div>
                    
                    <!-- Status legend -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Status Legend</h3>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <span class="status-badge status-normal mr-2"></span>
                                <span class="text-sm text-gray-600">Normal</span>
                            </div>
                            <div class="flex items-center">
                                <span class="status-badge status-almost-full mr-2"></span>
                                <span class="text-sm text-gray-600">Almost full</span>
                            </div>
                            <div class="flex items-center">
                                <span class="status-badge status-full mr-2"></span>
                                <span class="text-sm text-gray-600">Full</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar management
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');
        
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
            sidebarOverlay.classList.toggle('show');
        });
        
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('show');
        });

        // AI predictions script
        const chartCtx = document.getElementById('volumeChart');
        const noDataMessage = document.getElementById('noDataMessage');
        let chart;
        let allPointsData = {};

        // Update last update time
        function updateLastUpdateTime() {
            const now = new Date();
            document.getElementById('lastUpdateTime').textContent = 
                `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
        }

        // Update status counters
        function updateStatusCounters() {
            const normalCount = document.querySelectorAll('.status-normal').length;
            const almostFullCount = document.querySelectorAll('.status-almost-full').length;
            const fullCount = document.querySelectorAll('.status-full').length;
            
            document.getElementById('normalCount').textContent = normalCount;
            document.getElementById('almostFullCount').textContent = almostFullCount;
            document.getElementById('fullCount').textContent = fullCount;
        }

        // Function to create a status badge
        function createStatusBadge(status) {
            let badgeClass, badgeText, icon;
            
            switch(status) {
                case "full":
                    badgeClass = "status-full";
                    badgeText = "Full";
                    icon = "fas fa-times-circle";
                    break;
                case "almost full":
                    badgeClass = "status-almost-full";
                    badgeText = "Almost full";
                    icon = "fas fa-exclamation-triangle";
                    break;
                case "normal":
                    badgeClass = "status-normal";
                    badgeText = "Normal";
                    icon = "fas fa-check-circle";
                    break;
                default:
                    badgeClass = "status-unknown";
                    badgeText = "Unknown";
                    icon = "fas fa-question-circle";
            }
            
            return `<span class="status-badge ${badgeClass} flex items-center">
                      <i class="${icon} mr-1"></i>
                      ${badgeText}
                    </span>`;
        }

        // Function to update the priority list
        function updatePriorityList() {
            const priorityList = document.getElementById('priorityList');
            const fullPoints = [];
            const almostFullPoints = [];
            
            // Retrieve priority points
            for (const [id, data] of Object.entries(allPointsData)) {
                if (data.status === "full") {
                    fullPoints.push({id, ...data});
                } else if (data.status === "almost full") {
                    almostFullPoints.push({id, ...data});
                }
            }
            
            // Sort by volume (descending)
            fullPoints.sort((a, b) => b.predicted_volume - a.predicted_volume);
            almostFullPoints.sort((a, b) => b.predicted_volume - a.predicted_volume);
            
            const priorityPoints = [...fullPoints, ...almostFullPoints].slice(0, 3);
            
            if (priorityPoints.length === 0) {
                priorityList.innerHTML = `
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                        <p>No priority points</p>
                        <p class="text-sm mt-1">All points are in a normal state</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            priorityPoints.forEach(point => {
                const statusClass = point.status === "full" ? "status-full" : "status-almost-full";
                const statusIcon = point.status === "full" ? "fas fa-times-circle" : "fas fa-exclamation-triangle";
                
                html += `
                <div class="border border-gray-200 rounded-lg p-3 hover:shadow-sm transition duration-150 bg-white">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 truncate">${point.name}</h3>
                            <p class="text-sm text-gray-500 truncate">${point.address || 'Address not specified'}</p>
                        </div>
                        <span class="status-badge ${statusClass} flex items-center text-xs ml-2 flex-shrink-0">
                            <i class="${statusIcon} mr-1"></i>
                            ${point.status === "full" ? "Full" : "Almost full"}
                        </span>
                    </div>
                    <div class="mt-2 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">${Math.round(point.predicted_volume)} kg</span>
                        <button class="text-blue-500 hover:text-blue-700 text-sm font-medium transition">
                            Schedule <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
                `;
            });
            
            priorityList.innerHTML = html;
        }

        async function fetchPredictions() {
            const refreshButton = document.getElementById('refreshPredictions');
            const loadingSpinner = document.getElementById('loadingSpinner');
            refreshButton.disabled = true;
            loadingSpinner.classList.remove('hidden');

            const rows = document.querySelectorAll('#predictionsBody tr');
            const labels = [];
            const dataValues = [];

            for (const row of rows) {
                const id = row.id.replace('row-', '');
                try {
                    const res = await fetch(`/collection-ai/predict/${id}`);
                    const data = await res.json();

                    const volumeElement = document.getElementById(`volume-${id}`);
                    const statusElement = document.getElementById(`status-${id}`);
                    const lastCollectionElement = document.getElementById(`lastCollection-${id}`);
                    const pointNameElement = row.querySelector('.point-name');
                    
                    // Store data for later use
                    allPointsData[id] = {
                        ...data,
                        name: pointNameElement.textContent,
                        address: row.querySelector('td:first-child .text-sm').textContent
                    };
                    
                    // Update volume
                    volumeElement.innerHTML = `
                        <div class="font-bold text-gray-800">${Math.round(data.predicted_volume)} kg</div>
                    `;
                    
                    // Update status with badge
                    statusElement.innerHTML = createStatusBadge(data.status);
                    
                    // Simulate a last collection date (to be replaced with real data)
                    const daysAgo = Math.floor(Math.random() * 7) + 1;
                    lastCollectionElement.innerHTML = `
                        <div class="text-gray-700">${daysAgo} day${daysAgo > 1 ? 's' : ''} ago</div>
                    `;
                    
                    // Add data for the chart - use the real point name
                    labels.push(pointNameElement.textContent);
                    dataValues.push(Math.round(data.predicted_volume));

                } catch (error) {
                    document.getElementById(`status-${id}`).innerHTML = `
                        <span class="status-badge status-unknown flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Error
                        </span>
                    `;
                    console.error(`Error for point ${id}:`, error);
                }
            }

            // Update the chart
            if (chart) chart.destroy();
            if (dataValues.length > 0) {
                noDataMessage.classList.add('hidden');
                
                // Configuration to avoid overly long labels
                const shortenedLabels = labels.map(label => {
                    if (label.length > 15) {
                        return label.substring(0, 15) + '...';
                    }
                    return label;
                });
                
                chart = new Chart(chartCtx, {
                    type: 'bar',
                    data: {
                        labels: shortenedLabels,
                        datasets: [{
                            label: "Predicted Volume (kg)",
                            data: dataValues,
                            backgroundColor: dataValues.map((v, i) => {
                                const id = rows[i].id.replace('row-', '');
                                const status = allPointsData[id]?.status;
                                return status === "full" ? 'rgba(239, 68, 68, 0.7)' :
                                       status === "almost full" ? 'rgba(249, 115, 22, 0.7)' :
                                       status === "normal" ? 'rgba(34, 197, 94, 0.7)' : 'rgba(156, 163, 175, 0.7)';
                            }),
                            borderColor: dataValues.map(() => 'rgba(255, 255, 255, 1)'),
                            borderWidth: 2,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                title: { 
                                    display: true, 
                                    text: 'Volume (kg)',
                                    font: { weight: 'bold', size: 12 }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: { size: 11 }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: { size: 11 },
                                    callback: function(value, index) {
                                        return shortenedLabels[index];
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { 
                                display: false
                            },
                            tooltip: { 
                                callbacks: { 
                                    title: function(context) {
                                        return labels[context[0].dataIndex];
                                    },
                                    label: ctx => `${ctx.raw} kg` 
                                },
                                backgroundColor: 'rgba(0, 0, 0, 0.7)',
                                titleFont: { size: 12 },
                                bodyFont: { size: 12 },
                                padding: 8,
                                cornerRadius: 6
                            }
                        }
                    }
                });
            } else {
                noDataMessage.classList.remove('hidden');
            }

            // Update counters and priority list
            updateStatusCounters();
            updatePriorityList();
            
            // Update last update time
            updateLastUpdateTime();

            refreshButton.disabled = false;
            loadingSpinner.classList.add('hidden');
            
            // Add a visual update effect
            document.querySelectorAll('#predictionsBody tr').forEach(row => {
                row.classList.add('fade-in');
                setTimeout(() => row.classList.remove('fade-in'), 500);
            });
        }

        // Initialization
        document.getElementById('refreshPredictions').addEventListener('click', fetchPredictions);
        
        // Search
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#predictionsBody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const pointName = row.querySelector('.point-name').textContent.toLowerCase();
                if (pointName.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            document.getElementById('visibleCount').textContent = visibleCount;
        });
        
        // Chart period buttons
        function setActiveChartButton(activeButton) {
            document.querySelectorAll('[id^="chart"]').forEach(btn => {
                btn.classList.remove('bg-blue-100', 'text-blue-700');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            });
            activeButton.classList.add('bg-blue-100', 'text-blue-700');
            activeButton.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        }
        
        document.getElementById('chartDay').addEventListener('click', function() {
            setActiveChartButton(this);
            // Here, you can reload data for the selected period
        });
        
        document.getElementById('chartWeek').addEventListener('click', function() {
            setActiveChartButton(this);
        });
        
        document.getElementById('chartMonth').addEventListener('click', function() {
            setActiveChartButton(this);
        });

        // Initialize Day button as active
        setActiveChartButton(document.getElementById('chartDay'));

        // Initial load
        setTimeout(() => {
            fetchPredictions();
        }, 1000);
    </script>
</body>
</html>