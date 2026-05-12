<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi — Trashpot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 15mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; color: #1e293b; font-size: 13px; line-height: 1.6; }

        /* ── TOP NAVBAR ── */
        .top-nav { background: #1a2332; padding: 0 32px; display: flex; align-items: center; height: 56px; gap: 32px; }
        .nav-brand { display: flex; align-items: center; gap: 10px; margin-right: 16px; }
        .nav-logo { width: 32px; height: 32px; border-radius: 6px; background: #22c55e; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 16px; }
        .nav-brand-text h1 { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.1; }
        .nav-brand-text p { font-size: 9px; color: #94a3b8; font-weight: 400; }
        .nav-links { display: flex; align-items: center; gap: 4px; flex: 1; }
        .nav-link { color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 500; padding: 8px 14px; border-radius: 6px; display: flex; align-items: center; gap: 6px; transition: all 0.2s; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-link.active { color: #fff; background: rgba(34,197,94,0.15); }
        .nav-link svg { width: 16px; height: 16px; }
        .nav-right { display: flex; align-items: center; gap: 16px; margin-left: auto; }
        .nav-bell { color: #94a3b8; cursor: pointer; }
        .nav-user { display: flex; align-items: center; gap: 8px; color: #fff; font-size: 13px; font-weight: 500; }
        .nav-avatar { width: 30px; height: 30px; border-radius: 50%; background: #334155; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: 600; font-size: 13px; }

        /* ── SUB HEADER ── */
        .sub-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 12px 32px; display: flex; align-items: center; justify-content: space-between; }
        .btn-back-link { display: flex; align-items: center; gap: 6px; color: #1e293b; text-decoration: none; font-size: 13px; font-weight: 500; background: #fff; border: 1px solid #e2e8f0; padding: 7px 16px; border-radius: 6px; transition: all 0.2s; }
        .btn-back-link:hover { background: #f1f5f9; }
        .sub-actions { display: flex; gap: 10px; }
        .btn-preview { background: #1e293b; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: inherit; }
        .btn-print { background: #22c55e; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: inherit; transition: background 0.2s; }
        .btn-print:hover { background: #16a34a; }

        /* ── REPORT PAGE ── */
        .report-wrapper { max-width: 850px; margin: 28px auto; padding: 0 20px; }
        .report-page { background: #fff; border-radius: 8px; box-shadow: 0 1px 8px rgba(0,0,0,0.06); padding: 48px 52px; }

        /* ── KOP SURAT ── */
        .kop { display: flex; align-items: center; gap: 16px; padding-bottom: 16px; }
        .kop-logo { width: 52px; height: 52px; border-radius: 10px; background: #22c55e; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 22px; flex-shrink: 0; }
        .kop-text { flex: 1; }
        .kop-text h2 { font-size: 18px; font-weight: 800; color: #1a2332; letter-spacing: 0.5px; }
        .kop-text p { font-size: 12px; color: #475569; font-weight: 500; }
        .kop-text .kop-sub { font-size: 11px; color: #94a3b8; font-weight: 400; font-style: italic; }
        .kop-stamp { text-align: center; flex-shrink: 0; }
        .kop-stamp-box { width: 64px; height: 64px; border: 2px solid #22c55e; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4px; }
        .kop-stamp-box svg { width: 28px; height: 28px; color: #22c55e; }
        .kop-stamp-label { font-size: 7px; color: #64748b; font-weight: 500; margin-top: 2px; line-height: 1.2; text-align: center; }
        .kop-divider { height: 3px; background: linear-gradient(90deg, #22c55e 0%, #3b82f6 100%); border-radius: 2px; margin-bottom: 24px; }

        /* ── TITLE ── */
        .doc-title { text-align: center; margin-bottom: 20px; }
        .doc-title h3 { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; margin-bottom: 4px; }
        .doc-title p { font-size: 11px; color: #94a3b8; }

        /* ── META ── */
        .doc-meta { border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 6px 40px; font-size: 12px; }
        .meta-row { display: flex; gap: 12px; }
        .meta-label { color: #94a3b8; min-width: 100px; font-weight: 500; }
        .meta-val { color: #1e293b; font-weight: 600; }

        /* ── SUMMARY CARDS ── */
        .summary-row { display: flex; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
        .sum-card { flex: 1; display: flex; align-items: center; gap: 12px; padding: 16px 18px; border-right: 1px solid #e2e8f0; }
        .sum-card:last-child { border-right: none; }
        .sum-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sum-icon svg { width: 18px; height: 18px; }
        .sum-icon.ic-total { background: #e0f2fe; color: #0369a1; }
        .sum-icon.ic-wait { background: #fef3c7; color: #d97706; }
        .sum-icon.ic-proc { background: #dbeafe; color: #2563eb; }
        .sum-icon.ic-done { background: #dcfce7; color: #16a34a; }
        .sum-info .sum-val { font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1; }
        .sum-info .sum-label { font-size: 10px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

        /* ── TABLE ── */
        .detail-heading { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #0f172a; margin-bottom: 12px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 24px; }
        .data-table thead th { background: #f8fafc; color: #64748b; padding: 10px 12px; text-align: left; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 2px solid #e2e8f0; }
        .data-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .data-table tbody tr:hover td { background: #fafbfc; }
        .td-num { text-align: center; color: #94a3b8; font-weight: 500; width: 36px; }
        .td-id { font-family: monospace; font-size: 11px; color: #64748b; }
        .td-desc { max-width: 220px; word-wrap: break-word; color: #334155; }
        .td-coords { font-size: 10px; color: #94a3b8; font-family: monospace; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; }
        .badge-w { background: #fef3c7; color: #92400e; }
        .badge-p { background: #dbeafe; color: #1e40af; }
        .badge-s { background: #dcfce7; color: #166534; }

        /* ── EMPTY ── */
        .empty-box { text-align: center; padding: 48px 20px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px; background: #fafbfc; }
        .empty-box svg { width: 40px; height: 40px; color: #cbd5e1; margin-bottom: 10px; }
        .empty-box h4 { font-size: 15px; color: #475569; font-weight: 600; margin-bottom: 4px; }
        .empty-box p { font-size: 12px; color: #94a3b8; }

        /* ── FOOTER ── */
        .doc-footer { display: flex; justify-content: space-between; align-items: flex-start; margin-top: 32px; }
        .foot-note { font-size: 11px; color: #94a3b8; max-width: 280px; line-height: 1.5; }
        .foot-note strong { color: #64748b; }
        .sign-block { text-align: center; min-width: 180px; }
        .sign-block .sign-date { font-size: 12px; color: #475569; }
        .sign-block .sign-title { font-size: 12px; color: #475569; font-weight: 500; }
        .sign-block .sign-space { height: 56px; }
        .sign-block .sign-name { font-size: 13px; font-weight: 700; color: #0f172a; }
        .sign-block .sign-role { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .page-footer { text-align: center; padding: 16px; font-size: 10px; color: #94a3b8; margin-top: 4px; }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .report-wrapper { margin: 0; padding: 0; max-width: 100%; }
            .report-page { box-shadow: none; border-radius: 0; padding: 20px 24px; }
            .sum-card { padding: 10px 12px; }
        }
    </style>
</head>
<body>
    {{-- ═══ TOP NAVBAR ═══ --}}
    <nav class="top-nav no-print">
        <div class="nav-brand">
            <div class="nav-logo" style="background: transparent;">
                <img src="{{ asset('img/trashpot-logo.jpg') }}" alt="Logo" style="width: 100%; height: 100%; border-radius: 6px; object-fit: contain;">
            </div>
            <div class="nav-brand-text">
                <h1>TRASHPOT</h1>
                <p>Waste Community Reporting System</p>
            </div>
        </div>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Pengaduan
            </a>
            <a href="{{ route('admin.rekap') }}" class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                Laporan
            </a>
            <a href="{{ route('admin.map') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                Peta Sebaran
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Kelola User
            </a>
        </div>
        <div class="nav-right">
            <svg class="nav-bell" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <div class="nav-user">
                <div class="nav-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                {{ Auth::user()->name ?? 'Admin' }} ▾
            </div>
        </div>
    </nav>

    {{-- ═══ SUB HEADER ═══ --}}
    <div class="sub-header no-print">
        <a href="{{ route('admin.rekap') }}" class="btn-back-link">← Kembali ke Laporan</a>
        <div class="sub-actions">
            <button class="btn-preview" onclick="document.querySelector('.report-page').scrollIntoView({behavior:'smooth'})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Preview
            </button>
            <button class="btn-print" onclick="window.print()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- ═══ REPORT DOCUMENT ═══ --}}
    <div class="report-wrapper">
        <div class="report-page">

            {{-- Kop Surat --}}
            <div class="kop">
                <div class="kop-logo" style="background: transparent;">
                    <img src="{{ asset('img/trashpot-logo.jpg') }}" alt="Trashpot Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px;">
                </div>
                <div class="kop-text">
                    <h2>TRASHPOT</h2>
                    <p>Sistem Pengaduan Sampah Masyarakat</p>
                    <span class="kop-sub">Waste Community Reporting System</span>
                </div>
                <div class="kop-stamp">
                    <div class="kop-stamp-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                        <span class="kop-stamp-label">Dokumen Resmi<br>Sistem Otomatis</span>
                    </div>
                </div>
            </div>
            <div class="kop-divider"></div>

            {{-- Title --}}
            <div class="doc-title">
                <h3>Laporan Rekapitulasi Data Pengaduan</h3>
                <p>Dicetak pada: {{ now()->translatedFormat('l, d F Y — H:i') }} WIB</p>
            </div>

            {{-- Meta Info --}}
            <div class="doc-meta">
                <div class="meta-row">
                    <span class="meta-label">Periode</span>
                    <span class="meta-val">
                        @if($startDate && $endDate)
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        @elseif($startDate)
                            Dari {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                        @elseif($endDate)
                            Sampai {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        @else
                            Semua Periode
                        @endif
                    </span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Filter Status</span>
                    <span class="meta-val">{{ $status }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Total Data</span>
                    <span class="meta-val">{{ $totalReports }} Laporan</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Dicetak Oleh</span>
                    <span class="meta-val">{{ Auth::user()->name ?? 'Admin' }}</span>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="summary-row">
                <div class="sum-card">
                    <div class="sum-icon ic-total">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div class="sum-info">
                        <div class="sum-val">{{ $totalReports }}</div>
                        <div class="sum-label">Total Laporan</div>
                    </div>
                </div>
                <div class="sum-card">
                    <div class="sum-icon ic-wait">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div class="sum-info">
                        <div class="sum-val">{{ $menunggu }}</div>
                        <div class="sum-label">Menunggu</div>
                    </div>
                </div>
                <div class="sum-card">
                    <div class="sum-icon ic-proc">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </div>
                    <div class="sum-info">
                        <div class="sum-val">{{ $diproses }}</div>
                        <div class="sum-label">Diproses</div>
                    </div>
                </div>
                <div class="sum-card">
                    <div class="sum-icon ic-done">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="sum-info">
                        <div class="sum-val">{{ $selesai }}</div>
                        <div class="sum-label">Selesai</div>
                    </div>
                </div>
            </div>

            {{-- Detail Table --}}
            <div class="detail-heading">Detail Laporan Pengaduan</div>

            @if($reports->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="td-num">No</th>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelapor</th>
                            <th>Deskripsi</th>
                            <th>Koordinat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $i => $r)
                        <tr>
                            <td class="td-num">{{ $i + 1 }}</td>
                            <td class="td-id">RPT-{{ $r->id }}</td>
                            <td style="white-space:nowrap;">{{ $r->created_at->format('d/m/Y') }}<br><span style="font-size:10px;color:#94a3b8">{{ $r->created_at->format('H:i') }}</span></td>
                            <td><strong>{{ $r->user->name }}</strong><br><span style="font-size:10px;color:#94a3b8">{{ $r->user->email }}</span></td>
                            <td class="td-desc">{{ $r->deskripsi }}</td>
                            <td class="td-coords">{{ number_format($r->latitude, 5) }},<br>{{ number_format($r->longitude, 5) }}</td>
                            <td>
                                @if($r->status === 'Menunggu')<span class="badge badge-w">Menunggu</span>
                                @elseif($r->status === 'Diproses')<span class="badge badge-p">Diproses</span>
                                @else<span class="badge badge-s">Selesai</span>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="12.01"/></svg>
                    <h4>Tidak Ada Data</h4>
                    <p>Tidak ada laporan yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif

            {{-- Footer --}}
            <div class="doc-footer">
                <div class="foot-note">
                    <p><strong>Catatan:</strong></p>
                    <p>Dokumen ini digenerate secara otomatis oleh sistem Trashpot. Data yang ditampilkan merupakan rekapitulasi dari pengaduan masyarakat yang masuk melalui aplikasi.</p>
                </div>
                <div class="sign-block">
                    <p class="sign-date">{{ now()->translatedFormat('d F Y') }}</p>
                    <p class="sign-title">Mengetahui,</p>
                    <div class="sign-space"></div>
                    <p class="sign-name">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="sign-role">Admin Trashpot</p>
                </div>
            </div>
        </div>

        {{-- Page Footer --}}
        <div class="page-footer">
            © {{ date('Y') }} TRASHPOT - Waste Community Reporting System<br>
            Dokumen ini sah dan digenerate otomatis oleh sistem.
        </div>
    </div>
</body>
</html>
