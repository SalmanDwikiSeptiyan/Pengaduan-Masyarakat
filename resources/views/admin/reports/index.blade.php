@extends('admin.layouts.app')

@section('title', 'Kelola Laporan')

@section('head')
<style>
    .page-header { margin-bottom: 32px; }
    .page-title { font-size: 28px; font-weight: 600; color: var(--text-primary); letter-spacing: -0.5px; margin-bottom: 4px; }
    .page-subtitle { font-size: 14px; color: var(--text-muted); }

    .control-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; }
    .search-wrapper { position: relative; width: 320px; }
    .search-wrapper .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--text-muted); pointer-events: none; }
    .search-input { width: 100%; background: rgba(30, 41, 59, 0.5); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 16px 10px 42px; border-radius: 8px; font-size: 14px; outline: none; transition: all 0.2s; font-family: inherit; }
    .search-input:focus { border-color: var(--text-muted); background: var(--bg-surface); }
    .search-input::placeholder { color: var(--text-muted); font-weight: 400; }

    .segmented-control { display: flex; background: rgba(30, 41, 59, 0.5); padding: 4px; border-radius: 8px; border: 1px solid var(--border-color); }
    .segment-btn { padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; color: var(--text-secondary); text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
    .segment-btn:hover { color: var(--text-primary); }
    .segment-btn.active { background: var(--bg-surface-hover); color: var(--text-primary); box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
    .segment-count { background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 600; }

    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .premium-table th { text-align: left; padding: 16px 24px; color: var(--text-muted); font-weight: 500; font-size: 12px; border-bottom: 1px solid var(--border-color); text-transform: uppercase; letter-spacing: 0.5px; }
    .premium-table td { padding: 16px 24px; border-bottom: 1px solid rgba(51, 65, 85, 0.4); font-size: 14px; transition: background 0.2s; vertical-align: middle; }
    .premium-table tr { cursor: pointer; transition: background 0.2s; }
    .premium-table tr:hover td { background-color: rgba(51, 65, 85, 0.3); }

    .id-text { color: var(--text-muted); font-family: monospace; font-size: 13px; }
    
    .reporter-cell { display: flex; align-items: center; gap: 16px; }
    .reporter-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--bg-surface-hover); display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--text-primary); font-size: 15px; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.05); }
    .reporter-info { display: flex; flex-direction: column; gap: 2px; }
    .reporter-name { font-weight: 500; color: #fff; font-size: 14px; }
    .reporter-email { font-size: 12px; color: var(--text-muted); }

    .desc-cell { display: flex; align-items: center; gap: 16px; }
    .desc-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
    .desc-text { color: var(--text-secondary); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; }

    .pill-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.2px; }
    .pill-waiting { color: #F59E0B; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); }
    .pill-processing { color: #3B82F6; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); }
    .pill-completed { color: #22C55E; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); }

    .date-cell { text-align: right; display: flex; flex-direction: column; gap: 2px; align-items: flex-end; }
    .date-text { color: var(--text-secondary); font-size: 13px; }
    .time-text { color: var(--text-muted); font-size: 11px; }

    .action-icon { color: var(--text-muted); transition: color 0.2s; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; }
    .premium-table tr:hover .action-icon { color: var(--text-primary); background: rgba(255,255,255,0.05); }

    .pagination-bar { padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); }
    .pagination-info { font-size: 13px; color: var(--text-muted); }
    .pagination-controls { display: flex; gap: 4px; }
    .page-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; color: var(--text-secondary); text-decoration: none; font-size: 13px; transition: all 0.2s; }
    .page-btn:hover { background: var(--bg-surface-hover); color: var(--text-primary); }
    .page-btn.active { background: var(--accent-green); color: #fff; font-weight: 500; }
    .page-btn.disabled { opacity: 0.5; cursor: not-allowed; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Kelola Laporan</h1>
        <p class="page-subtitle">Total: {{ $reports->total() }} laporan terdaftar</p>
    </div>

    <div class="control-bar">
        <div class="search-wrapper">
            <i data-lucide="search" class="search-icon"></i>
            <input
                type="text"
                id="searchInput"
                class="search-input"
                placeholder="Cari laporan..."
                value="{{ request('search') }}"
                onkeydown="if(event.key==='Enter') doSearch()"
            >
        </div>
        
        <div class="segmented-control">
            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'Semua'])) }}"
               class="segment-btn {{ !request('status') || request('status') === 'Semua' ? 'active' : '' }}">
                Semua <span class="segment-count">{{ $countAll }}</span>
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except('page'), ['status' => 'Menunggu'])) }}"
               class="segment-btn {{ request('status') === 'Menunggu' ? 'active' : '' }}">
                Menunggu <span class="segment-count">{{ $countMenunggu }}</span>
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except('page'), ['status' => 'Diproses'])) }}"
               class="segment-btn {{ request('status') === 'Diproses' ? 'active' : '' }}">
                Diproses <span class="segment-count">{{ $countDiproses }}</span>
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->except('page'), ['status' => 'Selesai'])) }}"
               class="segment-btn {{ request('status') === 'Selesai' ? 'active' : '' }}">
                Selesai <span class="segment-count">{{ $countSelesai }}</span>
            </a>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-surface);">
        @if($reports->count() > 0)
            <div style="overflow-x: auto;">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelapor</th>
                            <th>Deskripsi</th>
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
                                    <div class="reporter-cell">
                                        <div class="reporter-avatar">
                                            {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                        </div>
                                        <div class="reporter-info">
                                            <span class="reporter-name">{{ $report->user->name }}</span>
                                            <span class="reporter-email">{{ $report->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
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

            @if($reports->hasPages())
                <div class="pagination-bar">
                    <div class="pagination-info">
                        Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} results
                    </div>
                    <div class="pagination-controls">
                        @if($reports->onFirstPage())
                            <span class="page-btn disabled">&laquo;</span>
                        @else
                            <a href="{{ $reports->previousPageUrl() }}" class="page-btn">&laquo;</a>
                        @endif

                        @foreach($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="page-btn {{ $page == $reports->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($reports->hasMorePages())
                            <a href="{{ $reports->nextPageUrl() }}" class="page-btn">&raquo;</a>
                        @else
                            <span class="page-btn disabled">&raquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div style="text-align:center; padding: 80px 20px; color: var(--text-muted);">
                <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 8px; color: var(--text-primary);">No Reports Found</h3>
                <p>Try adjusting your search or filters.</p>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    function doSearch() {
        const val = document.getElementById('searchInput').value.trim();
        const url = new URL(window.location.href);
        if (val) {
            url.searchParams.set('search', val);
        } else {
            url.searchParams.delete('search');
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
</script>
@endsection
