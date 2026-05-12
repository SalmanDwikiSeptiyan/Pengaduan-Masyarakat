@extends('admin.layouts.app')

@section('title', 'Detail User — ' . $user->name)

@section('head')
<style>
    .page-header { margin-bottom: 32px; display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
    .back-btn { display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 14px; text-decoration: none; font-weight: 500; transition: color 0.2s; margin-bottom: 16px; }
    .back-btn:hover { color: var(--text-primary); }
    
    .profile-header { display: flex; align-items: center; gap: 24px; }
    .profile-avatar { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 600; color: #fff; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.2); border: 2px solid rgba(255,255,255,0.05); }
    .profile-info { display: flex; flex-direction: column; gap: 4px; }
    .profile-name { font-size: 28px; font-weight: 700; color: #fff; letter-spacing: -0.5px; line-height: 1.2; }
    .profile-email { font-size: 15px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
    
    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
    .stat-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-info { display: flex; flex-direction: column; gap: 4px; }
    .stat-value { font-size: 24px; font-weight: 700; color: #fff; line-height: 1; }
    .stat-label { font-size: 13px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

    .stat-total { background: rgba(255,255,255,0.03); }
    .stat-total .stat-icon { background: rgba(255,255,255,0.1); color: #fff; }
    
    .stat-waiting { background: rgba(245, 158, 11, 0.05); border-color: rgba(245, 158, 11, 0.1); }
    .stat-waiting .stat-icon { background: rgba(245, 158, 11, 0.15); color: #F59E0B; }
    
    .stat-processing { background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.1); }
    .stat-processing .stat-icon { background: rgba(59, 130, 246, 0.15); color: #3B82F6; }
    
    .stat-completed { background: rgba(34, 197, 94, 0.05); border-color: rgba(34, 197, 94, 0.1); }
    .stat-completed .stat-icon { background: rgba(34, 197, 94, 0.15); color: #22C55E; }

    .section-title { font-size: 18px; font-weight: 600; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .premium-table th { text-align: left; padding: 16px 24px; color: var(--text-muted); font-weight: 500; font-size: 12px; border-bottom: 1px solid var(--border-color); text-transform: uppercase; letter-spacing: 0.5px; }
    .premium-table td { padding: 16px 24px; border-bottom: 1px solid rgba(51, 65, 85, 0.4); font-size: 14px; transition: background 0.2s; vertical-align: middle; }
    .premium-table tr { cursor: pointer; transition: background 0.2s; }
    .premium-table tr:hover td { background-color: rgba(51, 65, 85, 0.3); }

    .id-text { color: var(--text-muted); font-family: monospace; font-size: 13px; }

    .desc-cell { display: flex; align-items: center; gap: 16px; }
    .desc-img { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
    .desc-text { color: var(--text-secondary); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; }

    .pill-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.2px; }
    .pill-waiting { color: #F59E0B; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); }
    .pill-processing { color: #3B82F6; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); }
    .pill-completed { color: #22C55E; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); }

    .date-cell { text-align: right; display: flex; flex-direction: column; gap: 2px; align-items: flex-end; }
    .date-text { color: var(--text-secondary); font-size: 13px; }
    .time-text { color: var(--text-muted); font-size: 11px; }

    .action-icon { color: var(--text-muted); transition: color 0.2s; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; }
    .premium-table tr:hover .action-icon { color: var(--text-primary); background: rgba(255,255,255,0.05); }
</style>
@endsection

@section('content')
    <a href="{{ route('admin.users.index') }}" class="back-btn">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
        Kembali ke Daftar User
    </a>

    <div class="page-header">
        <div class="profile-header">
            <div class="profile-avatar" style="background: linear-gradient(135deg, {{ ['#0F172A','#1E293B','#334155','#0F172A','#1E293B','#334155'][$user->id % 6] }}, {{ ['#334155','#475569','#64748B','#334155','#475569','#64748B'][$user->id % 6] }});">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <h1 class="profile-name">{{ $user->name }}</h1>
                <div class="profile-email">
                    <i data-lucide="mail" style="width: 14px; height: 14px;"></i>
                    {{ $user->email }}
                    <span style="margin: 0 8px; color: var(--border-color);">|</span>
                    <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                    Bergabung {{ $user->created_at->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="stat-cards">
        <div class="stat-card stat-total">
            <div class="stat-icon">
                <i data-lucide="file-text"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $user->reports_count }}</span>
                <span class="stat-label">Total Laporan</span>
            </div>
        </div>
        <div class="stat-card stat-waiting">
            <div class="stat-icon">
                <i data-lucide="clock"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $countMenunggu }}</span>
                <span class="stat-label">Menunggu</span>
            </div>
        </div>
        <div class="stat-card stat-processing">
            <div class="stat-icon">
                <i data-lucide="loader"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $countDiproses }}</span>
                <span class="stat-label">Diproses</span>
            </div>
        </div>
        <div class="stat-card stat-completed">
            <div class="stat-icon">
                <i data-lucide="check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $countSelesai }}</span>
                <span class="stat-label">Selesai</span>
            </div>
        </div>
    </div>

    <h2 class="section-title">
        <i data-lucide="list" style="width: 20px; height: 20px; color: var(--text-muted);"></i>
        Riwayat Laporan {{ $user->name }}
    </h2>

    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-surface);">
        @if($reports->count() > 0)
            <div style="overflow-x: auto;">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto & Deskripsi</th>
                            <th>Status</th>
                            <th style="text-align: right;">Tanggal</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr onclick="window.location='{{ route('admin.reports.show', $report->id) }}'">
                                <td class="id-text">#RPT-{{ $report->id }}</td>
                                <td>
                                    <div class="desc-cell">
                                        <img src="{{ asset('storage/' . $report->foto_before) }}" alt="Foto" class="desc-img">
                                        <span class="desc-text">{{ $report->deskripsi }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($report->status === 'Menunggu')
                                        <span class="pill-badge pill-waiting"><span class="dot dot-waiting"></span> Pending</span>
                                    @elseif($report->status === 'Diproses')
                                        <span class="pill-badge pill-processing"><span class="dot dot-processing"></span> Processing</span>
                                    @else
                                        <span class="pill-badge pill-completed"><span class="dot dot-completed"></span> Completed</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="date-cell">
                                        <span class="date-text">{{ $report->created_at->format('M d, Y') }}</span>
                                        <span class="time-text">{{ $report->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-icon">
                                        <i data-lucide="chevron-right" style="width: 18px; height: 18px;"></i>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align:center; padding: 80px 20px; color: var(--text-muted);">
                <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 8px; color: var(--text-primary);">Belum ada laporan</h3>
                <p>User ini belum membuat laporan apapun.</p>
            </div>
        @endif
    </div>
@endsection
