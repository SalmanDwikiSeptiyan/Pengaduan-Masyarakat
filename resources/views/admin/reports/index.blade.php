@extends('admin.layouts.app')

@section('title', 'Kelola Laporan')
@section('page-title', 'Kelola Laporan')

@section('content')
    <div class="dashboard-section animate-in">
        <div class="section-header">
            <h2>📋 Semua Laporan</h2>
            <span class="text-muted text-sm">Total: {{ $reports->total() }} laporan</span>
        </div>

        {{-- Toolbar: Search + Filter --}}
        <div class="table-toolbar">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari nama, email, atau deskripsi..."
                    value="{{ request('search') }}"
                    onkeydown="if(event.key==='Enter') doSearch()"
                >
            </div>
            <div class="filter-tabs">
                <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'Semua'])) }}"
                   class="filter-tab {{ !request('status') || request('status') === 'Semua' ? 'active' : '' }}">
                    Semua <span class="tab-count">{{ $countAll }}</span>
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->except('page'), ['status' => 'Menunggu'])) }}"
                   class="filter-tab {{ request('status') === 'Menunggu' ? 'active' : '' }}">
                    Menunggu <span class="tab-count">{{ $countMenunggu }}</span>
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->except('page'), ['status' => 'Diproses'])) }}"
                   class="filter-tab {{ request('status') === 'Diproses' ? 'active' : '' }}">
                    Diproses <span class="tab-count">{{ $countDiproses }}</span>
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->except('page'), ['status' => 'Selesai'])) }}"
                   class="filter-tab {{ request('status') === 'Selesai' ? 'active' : '' }}">
                    Selesai <span class="tab-count">{{ $countSelesai }}</span>
                </a>
            </div>
        </div>

        @if($reports->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelapor</th>
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
                                <div class="user-cell">
                                    <div class="user-avatar-sm">{{ strtoupper(substr($report->user->name, 0, 1)) }}</div>
                                    <div>
                                        <div class="user-name">{{ $report->user->name }}</div>
                                        <div class="user-email">{{ $report->user->email }}</div>
                                    </div>
                                </div>
                            </td>
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

            {{-- Pagination --}}
            @if($reports->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
                    </div>
                    <div class="pagination-links">
                        {{-- Previous --}}
                        @if($reports->onFirstPage())
                            <span class="dots">‹</span>
                        @else
                            <a href="{{ $reports->previousPageUrl() }}">‹</a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                            @if($page == $reports->currentPage())
                                <span class="current">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($reports->hasMorePages())
                            <a href="{{ $reports->nextPageUrl() }}">›</a>
                        @else
                            <span class="dots">›</span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Tidak ada laporan ditemukan</h3>
                <p>Coba ubah kata kunci pencarian atau filter status.</p>
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
