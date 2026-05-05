@extends('admin.layouts.app')

@section('title', 'Detail Laporan #' . $report->id)

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .detail-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .back-btn { display: flex; align-items: center; gap: 8px; color: var(--text-secondary); background: none; border: none; cursor: pointer; font-size: 14px; transition: color 0.2s; font-family: inherit; }
        .back-btn:hover { color: var(--text-primary); }
        
        .report-title-section { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-start; }
        .report-tags { display: flex; gap: 12px; margin-bottom: 12px; }
        .report-id { color: var(--text-secondary); font-family: monospace; font-size: 14px; }
        .report-title { font-size: 24px; font-weight: 600; margin-bottom: 12px; }
        .report-meta { display: flex; flex-direction: column; gap: 8px; color: var(--text-secondary); font-size: 13px; }
        .report-meta-item { display: flex; align-items: center; gap: 8px; }
        .report-author-date { text-align: right; font-size: 13px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 6px; align-items: flex-end;}
        .report-author-date span { color: var(--text-primary); }
        
        .detail-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
        
        .map-container { height: 260px; border-radius: 8px; overflow: hidden; margin-bottom: 24px; border: 1px solid var(--border-color); position: relative;}
        .leaflet-container { background: #1a2333; }
        .description-text { color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px; font-size: 14px;}
        
        .photos-grid { display: flex; gap: 12px; margin-bottom: 32px; flex-wrap: wrap; }
        .photo-item { width: 120px; height: 120px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); position: relative; cursor: pointer; }
        .photo-item img { width: 100%; height: 100%; object-fit: cover; }
        .add-photo { width: 120px; height: 120px; border-radius: 8px; border: 1px dashed var(--text-muted); display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); cursor: pointer; font-size: 11px; gap: 4px; background: rgba(255,255,255,0.02); }
        .add-photo:hover { border-color: var(--text-secondary); color: var(--text-secondary); background: rgba(255,255,255,0.05); }

        .status-control { display: flex; flex-direction: column; gap: 8px; }
        .status-option { padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 4px; cursor: pointer; transition: all 0.2s; position: relative; }
        .status-option:hover { background: var(--bg-surface-hover); }
        .status-option.active.menunggu { border-color: var(--accent-yellow); background: rgba(234, 179, 8, 0.05); }
        .status-option.active.diproses { border-color: var(--accent-blue); background: rgba(59, 130, 246, 0.05); }
        .status-option.active.selesai { border-color: var(--accent-green); background: rgba(34, 197, 94, 0.05); }
        
        .status-option-header { display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 13px;}
        .status-option-desc { font-size: 11px; color: var(--text-muted); margin-left: 24px;}
        .status-check { position: absolute; right: 16px; top: 12px; color: var(--text-primary); display: none;}
        .status-option.active .status-check { display: block; }
        
        .status-option.disabled { opacity: 0.5; cursor: not-allowed; }
        .status-option.disabled:hover { background: none; }

        .data-grid { display: flex; flex-direction: column; gap: 16px; }
        .data-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px;}
        .data-label { color: var(--text-secondary); }
        .data-value { display: flex; align-items: center; gap: 6px; font-weight: 500;}
        
        .timeline { position: relative; padding-left: 20px; display: flex; flex-direction: column; gap: 20px;}
        .timeline::before { content: ''; position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px; background: var(--border-color); }
        .timeline-item { position: relative; }
        .timeline-icon { position: absolute; left: -26px; top: 0; width: 14px; height: 14px; border-radius: 50%; border: 2px solid var(--bg-deep); display: flex; align-items: center; justify-content: center; z-index: 2; }
        .timeline-icon.yellow { background: var(--accent-yellow); }
        .timeline-icon.blue { background: var(--accent-blue); }
        .timeline-icon.green { background: var(--accent-green); }
        .timeline-icon.gray { background: var(--text-muted); }
        .timeline-content { display: flex; justify-content: space-between; }
        .timeline-text h4 { font-size: 13px; font-weight: 500; margin-bottom: 2px; }
        .timeline-text p { font-size: 12px; color: var(--text-secondary); }
    </style>
@endsection

@section('content')
    <div class="detail-header">
        <button class="back-btn" onclick="window.location='{{ route('admin.reports.index') }}'">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            Back to Reports
        </button>
        <div class="flex gap-2">
            <form id="formDelete" action="{{ route('admin.reports.destroy', $report->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Delete Report
                </button>
            </form>
        </div>
    </div>

    <div class="report-title-section">
        <div>
            <div class="report-tags">
                <span class="report-id">#RPT-{{ $report->id }}</span>
                @if($report->status === 'Menunggu')
                    <span class="tag tag-waiting"><i data-lucide="clock" style="width: 12px; height: 12px;"></i> Pending</span>
                @elseif($report->status === 'Diproses')
                    <span class="tag tag-processing"><i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i> Processing</span>
                @else
                    <span class="tag tag-completed"><i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> Completed</span>
                @endif
            </div>
            <h1 class="report-title">Waste Report Location</h1>
            <div class="report-meta">
                <div class="report-meta-item">
                    <i data-lucide="map-pin" style="width: 14px; height: 14px; color: var(--accent-green)"></i>
                    Coordinates: {{ $report->latitude }}, {{ $report->longitude }}
                </div>
            </div>
        </div>
        <div class="report-author-date">
            <div>Reported on<br><span>{{ $report->created_at->format('M d, Y, h:i A') }}</span></div>
            <div class="flex items-center gap-2 mt-2"><i data-lucide="user" style="width:14px; height:14px"></i> by <span>{{ $report->user->name }}</span></div>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Left Column -->
        <div>
            <div class="card" style="margin-bottom: 16px;">
                <div class="card-header" style="margin-bottom: 12px;">
                    <span class="section-title" style="margin:0;">Location</span>
                </div>
                <div id="reportMap" class="map-container"></div>
                
                <div style="font-size: 11px; color: var(--text-muted); text-align: right; display:flex; justify-content:space-between;">
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" style="color:var(--text-muted); text-decoration:none; display:flex; align-items:center; gap:4px;"><i data-lucide="external-link" style="width:12px; height:12px;"></i> Open in Google Maps</a>
                    <span>© OpenStreetMap contributors</span>
                </div>
            </div>

            <div class="card" style="margin-bottom: 16px;">
                <h3 class="section-title">Description</h3>
                <p class="description-text">
                    {{ $report->deskripsi }}
                </p>
                
                <h3 class="section-title">Photos</h3>
                <div class="photos-grid">
                    <div class="photo-item" onclick="openLightbox('{{ asset('storage/' . $report->foto_before) }}')">
                        <img src="{{ asset('storage/' . $report->foto_before) }}" alt="Foto Before">
                        <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:white; font-size:10px; padding:4px; text-align:center;">Before</div>
                        <i data-lucide="maximize-2" style="position:absolute; top:4px; right:4px; width:14px; height:14px; color:white; filter:drop-shadow(0 0 2px rgba(0,0,0,0.5));"></i>
                    </div>
                    
                    @if($report->foto_after)
                        <div class="photo-item" onclick="openLightbox('{{ asset('storage/' . $report->foto_after) }}')">
                            <img src="{{ asset('storage/' . $report->foto_after) }}" alt="Foto After">
                            <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:white; font-size:10px; padding:4px; text-align:center;">After</div>
                            <i data-lucide="maximize-2" style="position:absolute; top:4px; right:4px; width:14px; height:14px; color:white; filter:drop-shadow(0 0 2px rgba(0,0,0,0.5));"></i>
                        </div>
                    @else
                        @if($report->status === 'Menunggu' || $report->status === 'Diproses')
                            <div class="add-photo" onclick="document.getElementById('fotoAfter').click()">
                                <i data-lucide="camera" style="width: 20px; height: 20px;"></i>
                                Upload After
                            </div>
                        @else
                            <div class="photo-item" style="display:flex; align-items:center; justify-content:center; flex-direction:column; background:var(--bg-deep); color:var(--text-muted); font-size:11px;">
                                <i data-lucide="image-off" style="width:20px; height:20px; margin-bottom:4px;"></i>
                                No After Photo
                            </div>
                        @endif
                    @endif
                </div>
                
                <!-- Hidden form for Complete action -->
                <form id="formComplete" action="{{ route('admin.reports.complete', $report->id) }}" method="POST" enctype="multipart/form-data" style="display:none;">
                    @csrf
                    <input type="file" id="fotoAfter" name="foto_after" accept="image/*" onchange="autoSubmitComplete()">
                </form>
                @error('foto_after')
                    <div style="color: #ef4444; font-size: 12px; margin-top: -16px; margin-bottom: 16px;">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="card">
                <h3 class="section-title">Activity Timeline</h3>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon yellow"><i data-lucide="clock" style="width: 8px; height: 8px; color: #fff;"></i></div>
                        <div class="timeline-content">
                            <div class="timeline-text">
                                <h4>Report submitted</h4>
                                <p>{{ $report->created_at->format('M d, Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($report->status === 'Diproses' || $report->status === 'Selesai')
                    <div class="timeline-item">
                        <div class="timeline-icon blue"><i data-lucide="refresh-cw" style="width: 8px; height: 8px; color: #fff;"></i></div>
                        <div class="timeline-content">
                            <div class="timeline-text">
                                <h4>Status updated to Processing</h4>
                                <p>{{ $report->updated_at->format('M d, Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($report->status === 'Selesai')
                    <div class="timeline-item">
                        <div class="timeline-icon green"><i data-lucide="check" style="width: 8px; height: 8px; color: #fff;"></i></div>
                        <div class="timeline-content">
                            <div class="timeline-text">
                                <h4>Status updated to Completed</h4>
                                <p>{{ $report->updated_at->format('M d, Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="flex-col gap-4" style="gap: 16px;">
            <div class="card">
                <h3 class="section-title">Update Status</h3>
                
                <div style="display:flex; gap: 12px; position:relative;">
                    <div style="position:absolute; left:11px; top:12px; bottom:12px; width:2px; background:var(--border-color); z-index:0;"></div>
                    <div style="display:flex; flex-direction:column; gap:40px; margin-top:12px; z-index:1;">
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--bg-deep); border:2px solid {{ $report->status === 'Menunggu' ? 'var(--accent-yellow)' : 'var(--text-muted)' }}; display:flex; align-items:center; justify-content:center;">
                            @if($report->status === 'Menunggu') <div style="width:10px; height:10px; border-radius:50%; background:var(--accent-yellow);"></div> @endif
                        </div>
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--bg-deep); border:2px solid {{ $report->status === 'Diproses' ? 'var(--accent-blue)' : 'var(--text-muted)' }}; display:flex; align-items:center; justify-content:center;">
                            @if($report->status === 'Diproses') <div style="width:10px; height:10px; border-radius:50%; background:var(--accent-blue);"></div> @endif
                        </div>
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--bg-deep); border:2px solid {{ $report->status === 'Selesai' ? 'var(--accent-green)' : 'var(--text-muted)' }}; display:flex; align-items:center; justify-content:center;">
                            @if($report->status === 'Selesai') <i data-lucide="check" style="width:12px; height:12px; color:var(--accent-green);"></i> @endif
                        </div>
                    </div>

                    <div class="status-control" style="flex-grow:1;">
                        <!-- Waiting Form -->
                        <div class="status-option {{ $report->status === 'Menunggu' ? 'active menunggu' : 'disabled' }}">
                            <div class="status-option-header text-yellow">
                                <i data-lucide="clock" style="width: 14px; height: 14px;"></i> Pending
                            </div>
                            <div class="status-option-desc">Report has been received</div>
                            <i data-lucide="check" class="status-check" style="width:14px; height:14px; color:var(--text-primary)"></i>
                        </div>
                        
                        <!-- Processing Form -->
                        <form id="formDiproses" action="{{ route('admin.reports.updateStatus', $report->id) }}" method="POST" style="margin:0;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="Diproses">
                            <div class="status-option {{ $report->status === 'Diproses' ? 'active diproses' : '' }} {{ $report->status === 'Selesai' ? 'disabled' : '' }}" onclick="{{ $report->status === 'Menunggu' ? 'confirmDiproses()' : '' }}">
                                <div class="status-option-header text-blue">
                                    <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Processing
                                </div>
                                <div class="status-option-desc">Report is being reviewed</div>
                                <i data-lucide="check" class="status-check" style="width:14px; height:14px; color:var(--text-primary)"></i>
                            </div>
                        </form>
                        
                        <!-- Completed Form -->
                        <div class="status-option {{ $report->status === 'Selesai' ? 'active selesai' : '' }}" onclick="{{ $report->status !== 'Selesai' ? 'triggerCompleteFlow()' : '' }}">
                            <div class="status-option-header text-green">
                                <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Completed
                            </div>
                            <div class="status-option-desc">Issue has been resolved (Requires Photo)</div>
                            <i data-lucide="check" class="status-check" style="width:14px; height:14px; color:var(--text-primary)"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="section-title">Report Details</h3>
                <div class="data-grid">
                    <div class="data-row">
                        <span class="data-label">Report ID</span>
                        <span class="data-value">#RPT-{{ $report->id }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Reported At</span>
                        <span class="data-value">{{ $report->created_at->format('M d, Y, h:i A') }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Reporter</span>
                        <span class="data-value">{{ $report->user->name }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Email</span>
                        <span class="data-value" style="color:var(--text-muted); font-weight:normal;">{{ $report->user->email }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Leaflet Map
    const lat = {{ $report->latitude }};
    const lng = {{ $report->longitude }};
    let map;
    
    setTimeout(() => {
        map = L.map('reportMap', { center: [lat, lng], zoom: 15, zoomControl: false });
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);
        L.control.zoom({ position: 'topright' }).addTo(map);

        const leafIcon = L.divIcon({
            className: 'custom-pin',
            html: `<div style="background:var(--accent-green); width:32px; height:32px; border-radius:50% 50% 50% 0; transform:rotate(-45deg); display:flex; align-items:center; justify-content:center; box-shadow:0 4px 8px rgba(0,0,0,0.3); border:2px solid white;">
                     <div style="transform:rotate(45deg); width:12px; height:12px; background:white; border-radius:50%;"></div>
                   </div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        L.marker([lat, lng], { icon: leafIcon }).addTo(map);
    }, 100);

    // Form Submissions
    function confirmDiproses() {
        if ('{{ $report->status }}' !== 'Menunggu') return;
        
        openModal({
            title: 'Update Status to Processing?',
            message: 'This will change the status to Processing and notify the user.',
            confirmText: 'Yes, Update',
            btnClass: 'btn-success', // Using default green
            onConfirm: function() {
                document.getElementById('formDiproses').submit();
            }
        });
    }

    function triggerCompleteFlow() {
        if ('{{ $report->status }}' === 'Selesai') return;
        
        openModal({
            title: 'Complete Report',
            message: 'You need to upload an "After" photo to mark this report as Completed. Proceed to upload?',
            confirmText: 'Yes, Upload Photo',
            btnClass: 'btn-success',
            onConfirm: function() {
                document.getElementById('fotoAfter').click();
            }
        });
    }

    function autoSubmitComplete() {
        const input = document.getElementById('fotoAfter');
        if (input.files && input.files.length > 0) {
            document.getElementById('formComplete').submit();
        }
    }

    function confirmDelete() {
        openModal({
            title: 'Delete Report?',
            message: 'This report will be permanently deleted. This action cannot be undone.',
            confirmText: 'Yes, Delete',
            btnClass: 'btn-danger',
            onConfirm: function() {
                document.getElementById('formDelete').submit();
            }
        });
    }
</script>
@endsection
