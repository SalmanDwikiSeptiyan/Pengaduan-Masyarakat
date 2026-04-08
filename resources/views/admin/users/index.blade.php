@extends('admin.layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
    <div class="dashboard-section animate-in">
        <div class="section-header">
            <h2>👥 Daftar Pengguna</h2>
            <span class="text-muted text-sm">Total: {{ $totalUsers }} user terdaftar</span>
        </div>

        {{-- Search --}}
        <div class="table-toolbar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari nama atau email user..."
                    value="{{ request('search') }}"
                    onkeydown="if(event.key==='Enter') doSearch()"
                >
            </div>
        </div>

        @if($users->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total Laporan</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><strong>#{{ $user->id }}</strong></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm" style="background: linear-gradient(135deg, {{ ['#a7f3d0','#bfdbfe','#fde68a','#fecaca','#c7d2fe','#fbcfe8'][$user->id % 6] }}, {{ ['#6ee7b7','#93c5fd','#fcd34d','#fca5a5','#a5b4fc','#f9a8d4'][$user->id % 6] }});">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <strong>{{ $user->reports_count }}</strong>
                                    <span class="text-muted text-sm">laporan</span>
                                </div>
                            </td>
                            <td class="text-muted text-sm">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} user
                    </div>
                    <div class="pagination-links">
                        @if($users->onFirstPage())
                            <span class="dots">‹</span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}">‹</a>
                        @endif

                        @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if($page == $users->currentPage())
                                <span class="current">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}">›</a>
                        @else
                            <span class="dots">›</span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">👤</div>
                <h3>Tidak ada user ditemukan</h3>
                <p>Coba ubah kata kunci pencarian.</p>
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
