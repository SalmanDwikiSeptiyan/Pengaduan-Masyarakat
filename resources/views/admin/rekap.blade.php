@extends('admin.layouts.app')

@section('title', 'Rekap & Export')

@section('head')
<style>
    .page-header { margin-bottom: 32px; }
    .page-title { font-size: 28px; font-weight: 600; color: var(--text-primary); letter-spacing: -0.5px; margin-bottom: 4px; }
    .page-subtitle { font-size: 14px; color: var(--text-muted); }

    .rekap-grid { display: grid; grid-template-columns: 320px 1fr; gap: 24px; align-items: start; }

    .panel { background: var(--bg-surface); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); overflow: hidden; }
    .panel-header { padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; }
    .panel-title { font-size: 16px; font-weight: 600; color: var(--text-primary); }
    
    /* Left Panel: Form */
    .filter-form { padding: 24px; display: flex; flex-direction: column; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-label { font-size: 13px; font-weight: 500; color: var(--text-secondary); }
    .form-input { background: var(--bg-deep); border: 1px solid rgba(255,255,255,0.08); color: var(--text-primary); padding: 12px 16px; border-radius: 8px; font-size: 14px; outline: none; transition: all 0.2s; font-family: inherit; width: 100%; box-sizing: border-box; }
    .form-input:focus { border-color: var(--text-muted); box-shadow: 0 0 0 2px rgba(255,255,255,0.05); }
    
    .btn-group { display: flex; gap: 12px; margin-top: 8px; }
    .btn-apply { flex: 1; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: var(--text-primary); border-radius: 8px; font-size: 14px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; font-family: inherit; }
    .btn-apply:hover { background: rgba(255,255,255,0.1); }
    .btn-export { flex: 1; padding: 12px; background: var(--accent-green); border: none; color: #fff; border-radius: 8px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; font-family: inherit; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3); }
    .btn-export:hover { background: #16a34a; box-shadow: 0 6px 16px rgba(34, 197, 94, 0.4); transform: translateY(-1px); }

    /* Right Panel: Table */
    .count-badge { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; color: var(--text-secondary); }
    
    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .premium-table th { text-align: left; padding: 16px 24px; color: var(--text-muted); font-weight: 500; font-size: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); text-transform: uppercase; letter-spacing: 0.5px; }
    .premium-table td { padding: 16px 24px; border-bottom: 1px solid rgba(51, 65, 85, 0.4); font-size: 14px; transition: background 0.2s; vertical-align: middle; }
    .premium-table tr:last-child td { border-bottom: none; }
    .premium-table tr { transition: background 0.2s; }
    .premium-table tr:hover td { background-color: rgba(51, 65, 85, 0.3); }

    .col-no { color: var(--text-muted); font-size: 13px; font-family: monospace; }
    .col-date { color: var(--text-muted); font-size: 13px; }
    .col-user { font-weight: 500; color: #fff; }
    .col-desc { color: #fff; }

    .pill-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.2px; }
    .pill-waiting { color: #F59E0B; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); }
    .pill-processing { color: #3B82F6; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); }
    .pill-completed { color: #22C55E; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Rekap & Export</h1>
        <p class="page-subtitle">Generate laporan rekapitulasi data pengaduan secara kustom</p>
    </div>

    <div class="rekap-grid">
        
        {{-- Filter Panel --}}
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Filter Export</span>
            </div>
            
            <form action="{{ route('admin.rekap.report') }}" method="GET" class="filter-form" target="_blank">
                <div class="form-group">
                    <label class="form-label" for="start_date">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="end_date">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status Laporan</label>
                    <select name="status" id="status" class="form-input" style="appearance: auto;">
                        <option value="Semua" {{ request('status') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn-apply" onclick="applyFilter()">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Terapkan
                    </button>
                    <button type="submit" class="btn-export">
                        <i data-lucide="file-text" style="width: 16px; height: 16px;"></i> Cetak Laporan
                    </button>
                </div>
            </form>
        </div>

        {{-- Preview Panel --}}
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Preview Data</span>
                <span class="count-badge">{{ $reports->count() }} Data</span>
            </div>

            @if($reports->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pelapor</th>
                                <th>Laporan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $index => $report)
                                <tr>
                                    <td class="col-no">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="col-date">{{ $report->created_at->format('M d, Y') }}</td>
                                    <td class="col-user">{{ $report->user->name }}</td>
                                    <td class="col-desc">{{ Str::limit($report->deskripsi, 50) }}</td>
                                    <td>
                                        @if($report->status === 'Menunggu')
                                            <span class="pill-badge pill-waiting"><span class="dot dot-waiting"></span> Pending</span>
                                        @elseif($report->status === 'Diproses')
                                            <span class="pill-badge pill-processing"><span class="dot dot-processing"></span> Processing</span>
                                        @else
                                            <span class="pill-badge pill-completed"><span class="dot dot-completed"></span> Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align:center; padding: 80px 20px; color: var(--text-muted);">
                    <i data-lucide="file-x" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 8px; color: var(--text-primary);">Data Tidak Ditemukan</h3>
                    <p>Tidak ada laporan pada rentang tanggal dan status ini.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function applyFilter() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        const status = document.getElementById('status').value;
        
        let url = new URL('{{ route('admin.rekap') }}');
        if(start) url.searchParams.set('start_date', start);
        if(end) url.searchParams.set('end_date', end);
        if(status) url.searchParams.set('status', status);
        
        window.location.href = url.toString();
    }
</script>
@endsection
