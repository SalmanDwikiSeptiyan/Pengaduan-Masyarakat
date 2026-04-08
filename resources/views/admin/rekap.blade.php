<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Laporan — Trashpot Admin</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>♻️</text></svg>">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        /* ── Print-specific overrides ── */
        .rekap-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 32px;
            background: #ffffff;
        }

        .rekap-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 3px solid var(--primary);
        }

        .rekap-header .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 12px;
        }

        .rekap-header .logo-row .brand-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-darker));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        }

        .rekap-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }

        .rekap-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .rekap-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .rekap-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 32px;
        }

        .rekap-toolbar .period-selector {
            display: flex;
        }

        .rekap-toolbar .export-buttons {
            display: flex;
            gap: 10px;
        }

        .rekap-section {
            margin-bottom: 36px;
        }

        .rekap-section h2 {
            font-size: 17px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rekap-charts-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 32px;
        }

        .rekap-table-wrapper {
            overflow-x: auto;
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
        }

        .back-to-admin {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 24px;
            transition: var(--transition);
        }

        .back-to-admin:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }

        /* ── Print Styles ── */
        @media print {
            body {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .rekap-page {
                padding: 0;
                max-width: none;
            }

            .rekap-header {
                border-bottom: 2px solid #059669;
            }

            .stat-card {
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }

            .stat-card::before {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .chart-card {
                box-shadow: none;
                border: 1px solid #e5e7eb;
                page-break-inside: avoid;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table thead th {
                background: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .rekap-table-wrapper {
                page-break-inside: auto;
            }

            .rekap-table-wrapper tr {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .rekap-charts-grid {
                grid-template-columns: 1fr;
            }

            .rekap-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="rekap-page">
        {{-- Back link --}}
        <a href="{{ route('admin.dashboard') }}" class="back-to-admin no-print">← Kembali ke Dashboard</a>

        {{-- Header --}}
        <div class="rekap-header">
            <div class="logo-row">
                <div class="brand-icon">♻️</div>
                <h1>Rekap Laporan Trashpot</h1>
            </div>
            <p>Sistem Pengaduan Sampah Masyarakat</p>
            <div class="rekap-subtitle">
                Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="rekap-toolbar no-print">
            <div class="period-selector">
                <a href="{{ route('admin.rekap', ['period' => 'week']) }}"
                   class="period-btn {{ $period === 'week' ? 'active' : '' }}">Minggu</a>
                <a href="{{ route('admin.rekap', ['period' => 'month']) }}"
                   class="period-btn {{ $period === 'month' ? 'active' : '' }}">Bulan</a>
                <a href="{{ route('admin.rekap', ['period' => 'year']) }}"
                   class="period-btn {{ $period === 'year' ? 'active' : '' }}">Tahun</a>
            </div>
            <div class="export-buttons">
                <button class="btn btn-primary" onclick="window.print()">
                    🖨️ Download PDF
                </button>
                <a href="{{ route('admin.rekap.csv') }}" class="btn btn-success">
                    📊 Download Excel (CSV)
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="rekap-section">
            <h2>📊 Ringkasan Statistik</h2>
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-icon">📊</div>
                    <div class="stat-value">{{ $totalReports }}</div>
                    <div class="stat-label">Total Laporan</div>
                </div>
                <div class="stat-card menunggu">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-value">{{ $menunggu }}</div>
                    <div class="stat-label">Menunggu</div>
                </div>
                <div class="stat-card diproses">
                    <div class="stat-icon">🔄</div>
                    <div class="stat-value">{{ $diproses }}</div>
                    <div class="stat-label">Diproses</div>
                </div>
                <div class="stat-card selesai">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value">{{ $selesai }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="rekap-section">
            <h2>📈 Grafik Laporan
                <span style="font-size:13px; font-weight:400; color:var(--text-muted); margin-left:8px;">
                    ({{ $period === 'week' ? '7 Hari Terakhir' : ($period === 'month' ? '30 Hari Terakhir' : '12 Bulan Terakhir') }})
                </span>
            </h2>
            <div class="rekap-charts-grid">
                <div class="chart-card">
                    <div class="card-header">🍩 Distribusi Status</div>
                    <div class="card-body chart-donut">
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <div class="card-header">📈 Tren Laporan Masuk</div>
                    <div class="card-body chart-area" style="width:100%;">
                        <canvas id="areaChart" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="rekap-section">
            <h2>📋 Daftar Semua Laporan <span style="font-size:13px; font-weight:400; color:var(--text-muted);">({{ $reports->count() }} laporan)</span></h2>
            <div class="rekap-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelapor</th>
                            <th>Email</th>
                            <th>Deskripsi</th>
                            <th>Koordinat</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td><strong>#{{ $report->id }}</strong></td>
                                <td>{{ $report->user->name }}</td>
                                <td class="text-muted text-sm">{{ $report->user->email }}</td>
                                <td>{{ Str::limit($report->deskripsi, 40) }}</td>
                                <td class="text-sm">{{ $report->latitude }}, {{ $report->longitude }}</td>
                                <td>
                                    @if($report->status === 'Menunggu')
                                        <span class="badge badge-menunggu">Menunggu</span>
                                    @elseif($report->status === 'Diproses')
                                        <span class="badge badge-diproses">Diproses</span>
                                    @else
                                        <span class="badge badge-selesai">Selesai</span>
                                    @endif
                                </td>
                                <td class="text-muted text-sm">{{ $report->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // ── Donut Chart ──
        new Chart(document.getElementById('donutChart').getContext('2d'), {
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
                animation: { duration: 0 },
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
        const ctx = document.getElementById('areaChart').getContext('2d');
        const grad = ctx.createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        grad.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Laporan Masuk',
                    data: @json($chartCounts),
                    backgroundColor: grad,
                    borderColor: '#10b981',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: {{ $period === 'month' ? 2 : 4 }},
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 0 },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: 'Inter', size: 11 }, color: '#7c9486' },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Inter', size: 10 }, color: '#7c9486',
                            maxRotation: {{ $period === 'month' ? 45 : 0 }},
                            autoSkip: true, maxTicksLimit: {{ $period === 'month' ? 10 : 15 }}
                        },
                        grid: { display: false }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>
</html>
