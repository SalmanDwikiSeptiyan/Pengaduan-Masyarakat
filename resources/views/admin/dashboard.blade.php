@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .header-title h2 { font-size: 24px; font-weight: 600; margin-bottom: 4px; }
        .header-title p { color: var(--text-secondary); font-size: 14px; }
        .header-actions { display: flex; align-items: center; gap: 16px; }
        .header-greeting { color: var(--text-secondary); font-size: 14px; }

        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .metric-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 12px; }
        .metric-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .metric-title { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
        .metric-icon { color: var(--text-muted); }
        .metric-value { font-size: 28px; font-weight: 600; }
        
        .charts-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 24px; }
        .donut-container { position: relative; height: 220px; display: flex; justify-content: center;}
        .donut-inner-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
        .donut-inner-text h3 { font-size: 20px; font-weight: 600; }
        .donut-inner-text p { font-size: 11px; color: var(--text-secondary); }
        .legend { margin-top: 16px; display: flex; flex-direction: column; gap: 8px; }
        .legend-item { display: flex; align-items: center; justify-content: space-between; font-size: 12px;}
        .legend-label { display: flex; align-items: center; gap: 8px; color: var(--text-secondary); }
        .legend-value { color: var(--text-primary); }

        .line-chart-container { height: 250px; width: 100%; }

        .filter-dropdown { background: var(--bg-deep); border: 1px solid var(--border-color); color: var(--text-secondary); padding: 6px 12px; border-radius: 6px; font-size: 12px; display: flex; align-items: center; gap: 6px; cursor: pointer; text-decoration: none;}
        .filter-dropdown:hover { background: var(--bg-surface-hover); color: var(--text-primary); }
        .filter-dropdown.active { background: rgba(34, 197, 94, 0.1); border-color: var(--accent-green); color: var(--accent-green); }

        .table-card { flex-grow: 1; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 16px; color: var(--text-secondary); font-weight: 500; font-size: 12px; border-bottom: 1px solid var(--border-color); }
        td { padding: 16px; border-bottom: 1px solid rgba(51, 65, 85, 0.5); font-size: 13px; }
        tr:hover td { background-color: rgba(51, 65, 85, 0.3); }
        .location-info { display: flex; flex-direction: column; gap: 2px; }
        .location-city { font-size: 11px; color: var(--text-muted); }
        .date-info { display: flex; flex-direction: column; gap: 2px; }
        .time-info { font-size: 11px; color: var(--text-muted); }
        .action-btn { background: none; border: none; color: var(--text-muted); cursor: pointer; }
        .action-btn:hover { color: var(--text-primary); }
    </style>
@endsection

@section('content')
    <header class="header">
        <div class="header-title">
            <h2>Dashboard</h2>
            <p>Overview of community waste reports and activity.</p>
        </div>
        <div class="header-actions">
            <span class="header-greeting" id="dynamicGreeting">
                Good morning, {{ Auth::user()->name ?? 'Admin' }} 👋
            </span>
            <button class="btn btn-icon" style="position:relative;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            </button>
        </div>
    </header>

    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-title">Total Reports</span>
                <i data-lucide="clipboard-list" class="metric-icon"></i>
            </div>
            <div class="metric-value">{{ number_format($totalReports) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-title">Pending Reports</span>
                <i data-lucide="clock" class="metric-icon"></i>
            </div>
            <div class="metric-value" style="color: var(--accent-yellow)">{{ number_format($menunggu) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-title">Processing Reports</span>
                <i data-lucide="refresh-cw" class="metric-icon"></i>
            </div>
            <div class="metric-value" style="color: var(--accent-blue)">{{ number_format($diproses) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-title">Completed Reports</span>
                <i data-lucide="check-circle" class="metric-icon"></i>
            </div>
            <div class="metric-value" style="color: var(--accent-green)">{{ number_format($selesai) }}</div>
        </div>
    </div>

    <div class="charts-grid">
        <!-- Donut Chart -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Report Status</span>
            </div>
            <div class="flex gap-6 items-center">
                <div class="donut-container" style="width: 160px; height: 160px; margin: 0 auto;">
                    <canvas id="donutChart"></canvas>
                    <div class="donut-inner-text">
                        <h3>{{ number_format($totalReports) }}</h3>
                        <p>Total</p>
                    </div>
                </div>
                <div class="legend" style="width: 130px;">
                    <div class="legend-item">
                        <span class="legend-label"><span class="dot dot-waiting"></span> Pending</span>
                        <span class="legend-value text-secondary">{{ $totalReports > 0 ? round(($menunggu / $totalReports) * 100) : 0 }}%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-label"><span class="dot dot-processing"></span> Processing</span>
                        <span class="legend-value text-secondary">{{ $totalReports > 0 ? round(($diproses / $totalReports) * 100) : 0 }}%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-label"><span class="dot dot-completed"></span> Completed</span>
                        <span class="legend-value text-secondary">{{ $totalReports > 0 ? round(($selesai / $totalReports) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Chart -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Reporting Trends</span>
                <select class="filter-dropdown" style="background: var(--bg-deep); color: var(--text-primary); border: 1px solid var(--border-color); outline: none; appearance: auto;" onchange="window.location.href=this.value">
                    <option value="{{ route('admin.dashboard', ['period' => 'week']) }}" {{ $period === 'week' ? 'selected' : '' }}>Week</option>
                    <option value="{{ route('admin.dashboard', ['period' => 'month']) }}" {{ $period === 'month' ? 'selected' : '' }}>Month</option>
                    <option value="{{ route('admin.dashboard', ['period' => 'year']) }}" {{ $period === 'year' ? 'selected' : '' }}>Year</option>
                </select>
            </div>
            <div class="line-chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <span class="card-title">Recent Reports</span>
            <div class="flex gap-2">
                <select class="filter-dropdown" style="background: var(--bg-deep); color: var(--text-primary); border: 1px solid var(--border-color); outline: none; appearance: auto;">
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option>
                </select>
                <a href="{{ route('admin.reports.index') }}" class="filter-dropdown">View All <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i></a>
            </div>
        </div>
        @if($recentReports->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Reporter</th>
                    <th>Status</th>
                    <th>Reported At</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentReports as $report)
                <tr class="cursor-pointer" onclick="window.location='{{ route('admin.reports.show', $report->id) }}'">
                    <td><span style="color:var(--text-secondary)">#</span>RPT-{{ $report->id }}</td>
                    <td>
                        <div class="location-info">
                            <span>{{ Str::limit($report->deskripsi, 40) }}</span>
                            <span class="location-city">{{ $report->latitude }}, {{ $report->longitude }}</span>
                        </div>
                    </td>
                    <td>{{ $report->user->name }}</td>
                    <td>
                        @if($report->status === 'Menunggu')
                            <span class="tag tag-waiting"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> Pending</span>
                        @elseif($report->status === 'Diproses')
                            <span class="tag tag-processing"><i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i> Processing</span>
                        @else
                            <span class="tag tag-completed"><i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Completed</span>
                        @endif
                    </td>
                    <td>
                        <div class="date-info">
                            <span>{{ $report->created_at->format('M d, Y') }}</span>
                            <span class="time-info">{{ $report->created_at->format('h:i A') }}</span>
                        </div>
                    </td>
                    <td><button class="action-btn"><i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i></button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align:center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
            <p>No reports found.</p>
        </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    // Donut Chart
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Completed'],
            datasets: [{
                data: [{{ $menunggu }}, {{ $diproses }}, {{ $selesai }}],
                backgroundColor: ['#EAB308', '#3B82F6', '#22C55E'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed !== null) {
                                label += context.parsed;
                            }
                            return label;
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Line Chart
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    let gradient = lineCtx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(34, 197, 94, 0.2)');
    gradient.addColorStop(1, 'rgba(34, 197, 94, 0)');

    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Reports',
                    data: @json($chartCounts),
                    borderColor: '#22C55E',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#22C55E',
                    pointBorderColor: '#1E293B',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(51, 65, 85, 0.3)',
                        drawBorder: false,
                    },
                    ticks: { color: '#64748B', font: { size: 11 }, stepSize: 1 }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#64748B', font: { size: 11 }, maxTicksLimit: 7 }
                }
            }
        }
    });
    // Dynamic Greeting based on time of day
    (function() {
        const hour = new Date().getHours();
        let greeting;
        if (hour >= 5 && hour < 12) {
            greeting = 'Good morning';
        } else if (hour >= 12 && hour < 18) {
            greeting = 'Good afternoon';
        } else if (hour >= 18 && hour < 21) {
            greeting = 'Good evening';
        } else {
            greeting = 'Good night';
        }
        const name = @json(Auth::user()->name ?? 'Admin');
        document.getElementById('dynamicGreeting').textContent = greeting + ', ' + name + ' 👋';
    })();
</script>
@endsection
