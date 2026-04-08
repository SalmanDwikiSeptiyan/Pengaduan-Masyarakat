@extends('admin.layouts.app')

@section('title', 'Detail User — ' . $user->name)
@section('page-title', 'Detail User')

@section('content')
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <span class="separator">›</span>
        <a href="{{ route('admin.users.index') }}">Kelola User</a>
        <span class="separator">›</span>
        <span>{{ $user->name }}</span>
    </div>

    <a href="{{ route('admin.users.index') }}" class="back-link">← Kembali ke Daftar User</a>

    {{-- User Profile Card --}}
    <div class="detail-card animate-in" style="margin-bottom: 24px;">
        <div class="card-body" style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
            <div class="user-profile-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="flex:1; min-width:200px;">
                <h2 style="font-size:22px; font-weight:700; color:var(--text); margin-bottom:4px;">{{ $user->name }}</h2>
                <p style="font-size:14px; color:var(--text-muted); margin-bottom:12px;">{{ $user->email }}</p>
                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                    <div class="user-meta-item">
                        <span class="meta-label">Bergabung</span>
                        <span class="meta-value">{{ $user->created_at->format('d F Y') }}</span>
                    </div>
                    <div class="user-meta-item">
                        <span class="meta-label">Total Laporan</span>
                        <span class="meta-value">{{ $user->reports_count }}</span>
                    </div>
                </div>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <div class="user-stat-badge menunggu">
                    <span class="stat-num">{{ $countMenunggu }}</span>
                    <span class="stat-txt">Menunggu</span>
                </div>
                <div class="user-stat-badge diproses">
                    <span class="stat-num">{{ $countDiproses }}</span>
                    <span class="stat-txt">Diproses</span>
                </div>
                <div class="user-stat-badge selesai">
                    <span class="stat-num">{{ $countSelesai }}</span>
                    <span class="stat-txt">Selesai</span>
                </div>
            </div>
        </div>
    </div>

    {{-- User's Reports --}}
    <div class="dashboard-section animate-in">
        <div class="section-header">
            <h2>📋 Laporan dari {{ $user->name }}</h2>
            <span class="text-muted text-sm">{{ $reports->count() }} laporan</span>
        </div>

        @if($reports->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td><strong>#{{ $report->id }}</strong></td>
                            <td>
                                <img
                                    src="{{ asset('storage/' . $report->foto_before) }}"
                                    alt="Foto laporan"
                                    class="photo-thumb"
                                    onclick="openLightbox(this.src)"
                                >
                            </td>
                            <td>{{ Str::limit($report->deskripsi, 50) }}</td>
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
                            <td>
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-primary btn-sm">
                                    Detail
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
                <p>User ini belum membuat laporan apapun.</p>
            </div>
        @endif
    </div>
@endsection
