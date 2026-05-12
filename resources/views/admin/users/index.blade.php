@extends('admin.layouts.app')

@section('title', 'Kelola User')

@section('head')
<style>
    .page-header { margin-bottom: 32px; }
    .page-title { font-size: 28px; font-weight: 600; color: var(--text-primary); letter-spacing: -0.5px; margin-bottom: 4px; }
    .page-subtitle { font-size: 14px; color: var(--text-muted); }

    .control-bar { display: flex; justify-content: flex-end; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border-color); }
    .search-wrapper { position: relative; width: 350px; }
    .search-wrapper .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--text-muted); pointer-events: none; }
    .search-input { width: 100%; background: rgba(30, 41, 59, 0.5); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 16px 10px 42px; border-radius: 8px; font-size: 14px; outline: none; transition: all 0.2s; font-family: inherit; }
    .search-input:focus { border-color: var(--text-muted); background: var(--bg-surface); }
    .search-input::placeholder { color: var(--text-muted); font-weight: 400; }

    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .premium-table th { text-align: left; padding: 16px 24px; color: var(--text-muted); font-weight: 500; font-size: 12px; border-bottom: 1px solid var(--border-color); text-transform: uppercase; letter-spacing: 0.5px; }
    .premium-table td { padding: 16px 24px; border-bottom: 1px solid rgba(51, 65, 85, 0.4); font-size: 14px; transition: background 0.2s; vertical-align: middle; }
    .premium-table tr { cursor: pointer; transition: background 0.2s; }
    .premium-table tr:hover td { background-color: rgba(51, 65, 85, 0.3); }

    .id-text { color: var(--text-muted); font-family: monospace; font-size: 13px; }
    
    .reporter-cell { display: flex; align-items: center; gap: 16px; }
    .reporter-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--bg-surface-hover); display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--text-primary); font-size: 16px; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.05); }
    .reporter-info { display: flex; flex-direction: column; gap: 2px; }
    .reporter-name { font-weight: 500; color: #fff; font-size: 14px; }
    .reporter-email { font-size: 12px; color: var(--text-muted); }

    .num-badge { padding: 4px 10px; border-radius: 6px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); font-size: 12px; font-weight: 500; color: var(--text-secondary); display: inline-flex; align-items: center; gap: 6px; }
    
    .date-cell { text-align: right; display: flex; flex-direction: column; gap: 2px; align-items: flex-end; }
    .date-text { color: var(--text-secondary); font-size: 13px; }

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
        <h1 class="page-title">Daftar Pengguna</h1>
        <p class="page-subtitle">Total: {{ $totalUsers }} user terdaftar</p>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-surface);">
        <div class="control-bar">
            <div class="search-wrapper">
                <i data-lucide="search" class="search-icon"></i>
                <input
                    type="text"
                    id="searchInput"
                    class="search-input"
                    placeholder="Search users by name or email..."
                    value="{{ request('search') }}"
                    onkeydown="if(event.key==='Enter') doSearch()"
                >
            </div>
        </div>

        @if($users->count() > 0)
            <div style="overflow-x: auto;">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Total Laporan</th>
                            <th style="text-align: right;">Bergabung</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr onclick="window.location='{{ route('admin.users.show', $user->id) }}'">
                                <td class="id-text">#USR-{{ $user->id }}</td>
                                <td>
                                    <div class="reporter-cell">
                                        <div class="reporter-avatar" style="background: linear-gradient(135deg, {{ ['#0F172A','#1E293B','#334155','#0F172A','#1E293B','#334155'][$user->id % 6] }}, {{ ['#334155','#475569','#64748B','#334155','#475569','#64748B'][$user->id % 6] }});">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="reporter-info">
                                            <span class="reporter-name">{{ $user->name }}</span>
                                            <span class="reporter-email">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="num-badge">
                                        {{ $user->reports_count }} Reports
                                    </span>
                                </td>
                                <td>
                                    <div class="date-cell">
                                        <span class="date-text">{{ $user->created_at->format('M d, Y') }}</span>
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

            @if($users->hasPages())
                <div class="pagination-bar">
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                    </div>
                    <div class="pagination-controls">
                        @if($users->onFirstPage())
                            <span class="page-btn disabled">&laquo;</span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="page-btn">&laquo;</a>
                        @endif

                        @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="page-btn {{ $page == $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="page-btn">&raquo;</a>
                        @else
                            <span class="page-btn disabled">&raquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div style="text-align:center; padding: 80px 20px; color: var(--text-muted);">
                <i data-lucide="users" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 8px; color: var(--text-primary);">No Users Found</h3>
                <p>Try adjusting your search criteria.</p>
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
