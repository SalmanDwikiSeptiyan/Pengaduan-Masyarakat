@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@endsection

@section('content')
    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card total animate-in">
            <div class="stat-icon">📊</div>
            <div class="stat-value">{{ $totalReports }}</div>
            <div class="stat-label">Total Laporan</div>
        </div>
        <div class="stat-card menunggu animate-in">
            <div class="stat-icon">⏳</div>
            <div class="stat-value">{{ $menunggu }}</div>
            <div class="stat-label">Menunggu</div>
        </div>
        <div class="stat-card diproses animate-in">
            <div class="stat-icon">🔄</div>
            <div class="stat-value">{{ $diproses }}</div>
            <div class="stat-label">Diproses</div>
        </div>
        <div class="stat-card selesai animate-in">
            <div class="stat-icon">✅</div>
            <div class="stat-value">{{ $selesai }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="charts-grid">
        <div class="chart-card animate-in">
            <div class="card-header">🍩 Distribusi Status</div>
            <div class="card-body chart-donut">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
        <div class="chart-card animate-in">
            <div class="card-header" style="justify-content: space-between;">
                <span>📈 Tren Laporan</span>
                <div class="period-selector">
                    <a href="{{ route('admin.dashboard', ['period' => 'week']) }}"
                       class="period-btn {{ $period === 'week' ? 'active' : '' }}">Minggu</a>
                    <a href="{{ route('admin.dashboard', ['period' => 'month']) }}"
                       class="period-btn {{ $period === 'month' ? 'active' : '' }}">Bulan</a>
                    <a href="{{ route('admin.dashboard', ['period' => 'year']) }}"
                       class="period-btn {{ $period === 'year' ? 'active' : '' }}">Tahun</a>
                </div>
            </div>
            <div class="card-body chart-area" style="width:100%;">
                <canvas id="areaChart" style="width:100%;"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Reports --}}
    <div class="dashboard-section animate-in">
        <div class="section-header">
            <h2>📋 Laporan Terbaru</h2>
            <a href="{{ route('admin.reports.index') }}">Lihat Semua →</a>
        </div>

        @if($recentReports->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelapor</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentReports as $report)
                        <tr>
                            <td><strong>#{{ $report->id }}</strong></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">{{ strtoupper(substr($report->user->name, 0, 1)) }}</div>
                                    <div>
                                        <div class="user-name">{{ $report->user->name }}</div>
                                        <div class="user-email">{{ $report->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ Str::limit($report->deskripsi, 40) }}</td>
                            <td>
                                @if($report->status === 'Menunggu')
                                    <span class="badge badge-menunggu">Menunggu</span>
                                @elseif($report->status === 'Diproses')
                                    <span class="badge badge-diproses">Diproses</span>
                                @else
                                    <span class="badge badge-selesai">Selesai</span>
                                @endif
                            </td>
                            <td class="text-muted text-sm">{{ $report->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-ghost btn-sm">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Belum ada laporan</h3>
                <p>Laporan dari masyarakat akan muncul di sini.</p>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    // ── Donut Chart ──
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Menunggu', 'Diproses', 'Selesai'],
            datasets: [{
                data: [{{ $menunggu }}, {{ $diproses }}, {{ $selesai }}],
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 10,
                        font: { family: 'Inter', size: 12, weight: 500 }
                    }
                }
            }
        }
    });

    // ── Area Chart ──
    const areaCtx = document.getElementById('areaChart').getContext('2d');
    const gradient = areaCtx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

    new Chart(areaCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Laporan Masuk',
                data: @json($chartCounts),
                backgroundColor: gradient,
                borderColor: '#10b981',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: {{ $period === 'month' ? 2 : 5 }},
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { family: 'Inter', size: 11 },
                        color: '#7c9486'
                    },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: {
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#7c9486',
                        maxRotation: {{ $period === 'month' ? 45 : 0 }},
                        autoSkip: true,
                        maxTicksLimit: {{ $period === 'month' ? 10 : 20 }}
                    },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
