@extends('admin.layouts.app')

@section('title', 'Detail Laporan #' . $report->id)
@section('page-title', 'Detail Laporan')

@section('content')
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <span class="separator">›</span>
        <a href="{{ route('admin.reports.index') }}">Kelola Laporan</a>
        <span class="separator">›</span>
        <span>Laporan #{{ $report->id }}</span>
    </div>

    <a href="{{ route('admin.reports.index') }}" class="back-link">← Kembali ke Daftar Laporan</a>

    {{-- Photo Grid --}}
    <div class="detail-grid">
        {{-- Foto Before --}}
        <div class="detail-card animate-in">
            <div class="card-header">📸 Foto Before</div>
            <div class="photo-container">
                <img
                    src="{{ asset('storage/' . $report->foto_before) }}"
                    alt="Foto Before"
                    onclick="openLightbox(this.src)"
                    style="cursor: pointer;"
                >
            </div>
        </div>

        {{-- Foto After --}}
        <div class="detail-card animate-in">
            <div class="card-header">📸 Foto After</div>
            <div class="photo-container">
                @if($report->foto_after)
                    <img
                        src="{{ asset('storage/' . $report->foto_after) }}"
                        alt="Foto After"
                        onclick="openLightbox(this.src)"
                        style="cursor: pointer;"
                    >
                @else
                    <div class="photo-placeholder">
                        <div class="placeholder-icon">🖼️</div>
                        <p>Belum ada foto after</p>
                        <p class="text-muted text-sm mt-2">Upload saat menyelesaikan laporan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Detail --}}
    <div class="detail-grid">
        <div class="detail-card animate-in">
            <div class="card-header">📝 Informasi Laporan</div>
            <div class="card-body">
                <div class="detail-info">
                    <div class="info-row">
                        <div class="info-icon">👤</div>
                        <div>
                            <div class="info-label">Pelapor</div>
                            <div class="info-value">{{ $report->user->name }} ({{ $report->user->email }})</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon">📄</div>
                        <div>
                            <div class="info-label">Deskripsi</div>
                            <div class="info-value">{{ $report->deskripsi }}</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon">📅</div>
                        <div>
                            <div class="info-label">Tanggal Laporan</div>
                            <div class="info-value">{{ $report->created_at->format('d F Y, H:i') }} WIB</div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-icon">🏷️</div>
                        <div>
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @if($report->status === 'Menunggu')
                                    <span class="badge badge-menunggu">Menunggu</span>
                                @elseif($report->status === 'Diproses')
                                    <span class="badge badge-diproses">Diproses</span>
                                @else
                                    <span class="badge badge-selesai">Selesai</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card animate-in">
            <div class="card-header">📍 Lokasi</div>
            <div class="card-body">
                <div class="detail-info">
                    <div class="info-row">
                        <div class="info-icon">🌐</div>
                        <div>
                            <div class="info-label">Koordinat</div>
                            <div class="info-value">{{ $report->latitude }}, {{ $report->longitude }}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="btn btn-info" style="width: 100%;">
                        📍 Lihat Lokasi di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Panel --}}
    @if($report->status !== 'Selesai')
        <div class="action-panel animate-in">
            <div class="card-header">⚡ Aksi</div>
            <div class="card-body">
                <div class="action-buttons">
                    @if($report->status === 'Menunggu')
                        <form action="{{ route('admin.reports.updateStatus', $report->id) }}" method="POST" style="flex:1; display:flex;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="Diproses">
                            <button type="submit" class="btn btn-warning" style="flex:1;">
                                🔄 Ubah ke Diproses
                            </button>
                        </form>
                    @endif

                    @if($report->status === 'Menunggu' || $report->status === 'Diproses')
                        <button type="button" class="btn btn-success" style="flex:1;" onclick="toggleUpload()">
                            ✅ Selesaikan Laporan
                        </button>
                    @endif
                </div>

                {{-- Upload Foto After --}}
                <div class="upload-section" id="uploadSection">
                    <h3 style="font-size:15px; font-weight:600; margin-bottom:16px; color: var(--text);">
                        📸 Upload Foto After (Wajib)
                    </h3>
                    <form action="{{ route('admin.reports.complete', $report->id) }}" method="POST" enctype="multipart/form-data" id="completeForm">
                        @csrf
                        <div class="upload-area" id="uploadArea" onclick="document.getElementById('fotoAfter').click()">
                            <div class="upload-icon">📁</div>
                            <div class="upload-text">Klik untuk memilih foto atau drag & drop</div>
                            <div class="upload-hint">Format: JPG, PNG (Maks. 2MB)</div>
                        </div>
                        <input type="file" id="fotoAfter" name="foto_after" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="previewImage(this)">

                        <div class="upload-preview" id="uploadPreview">
                            <img id="previewImg" src="" alt="Preview">
                        </div>

                        @error('foto_after')
                            <div class="alert alert-danger mt-4">{{ $message }}</div>
                        @enderror

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success" style="width:100%; padding: 16px;" id="submitBtn" disabled>
                                ✅ Konfirmasi & Selesaikan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    function toggleUpload() {
        const section = document.getElementById('uploadSection');
        section.classList.toggle('visible');
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('uploadPreview').classList.add('visible');
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('uploadArea').style.borderColor = 'var(--primary-light)';
                document.getElementById('uploadArea').style.background = 'var(--primary-50)';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Drag and drop
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const input = document.getElementById('fotoAfter');
            input.files = e.dataTransfer.files;
            previewImage(input);
        });
    }

    @error('foto_after')
        document.getElementById('uploadSection').classList.add('visible');
    @enderror
</script>
@endsection
