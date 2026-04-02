@extends('admin.layouts.app')

@section('title', 'Kelola Laporan')
@section('page-title', 'Kelola Laporan')

@section('content')
    <div class="dashboard-section animate-in">
        <div class="section-header">
            <h2>📋 Semua Laporan</h2>
            <span class="text-muted text-sm">Total: {{ $reports->count() }} laporan</span>
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
        @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Belum ada laporan</h3>
                <p>Laporan dari masyarakat akan muncul di sini.</p>
            </div>
        @endif
    </div>
@endsection
