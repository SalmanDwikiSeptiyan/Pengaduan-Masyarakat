@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

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
