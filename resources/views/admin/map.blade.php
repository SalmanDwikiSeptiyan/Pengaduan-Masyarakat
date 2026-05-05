@extends('admin.layouts.app')

@section('title', 'Peta Sebaran Laporan')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .map-wrapper { position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; overflow: hidden; }
        .leaflet-container { background: #0F172A; }
        .main-content { padding: 0 !important; } /* Override default padding for full screen */

        .glass-panel { background: rgba(30, 41, 59, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5); border-radius: 12px; z-index: 1000; position: absolute; color: var(--text-primary); }

        .stats-card { top: 24px; left: 24px; padding: 20px; display: flex; flex-direction: column; gap: 16px; min-width: 220px; }
        .stats-header { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .stats-sub { font-size: 12px; color: var(--text-muted); }
        .stat-row { display: flex; justify-content: space-between; align-items: center; }
        .stat-label { font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px; }
        .stat-val { font-size: 14px; font-weight: 600; }
        .stat-dot { width: 8px; height: 8px; border-radius: 50%; }

        .filter-control { top: 24px; right: 24px; padding: 6px; display: flex; gap: 4px; }
        .filter-btn { background: transparent; border: none; padding: 8px 16px; border-radius: 8px; color: var(--text-secondary); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; font-family: inherit; }
        .filter-btn:hover { color: var(--text-primary); }
        .filter-btn.active { background: rgba(255, 255, 255, 0.1); color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }

        .legend-card { bottom: 24px; left: 24px; padding: 16px; min-width: 200px; }
        .legend-title { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 12px; }
        .legend-row { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; font-size: 13px; color: var(--text-secondary); }
        .legend-row:last-child { margin-bottom: 0; }
        .legend-icon { width: 16px; height: 16px; border-radius: 50%; position: relative; }
        .legend-icon::after { content: ''; position: absolute; inset: -4px; border-radius: 50%; opacity: 0.2; }
        .legend-icon.waiting { background: #EAB308; box-shadow: 0 0 10px rgba(234, 179, 8, 0.5); }
        .legend-icon.waiting::after { background: #EAB308; }
        .legend-icon.processing { background: #3B82F6; box-shadow: 0 0 10px rgba(59, 130, 246, 0.5); }
        .legend-icon.processing::after { background: #3B82F6; }
        .legend-icon.completed { background: #22C55E; box-shadow: 0 0 10px rgba(34, 197, 94, 0.5); }
        .legend-icon.completed::after { background: #22C55E; }

        /* Custom Popup styling */
        .leaflet-popup-content-wrapper { background: var(--bg-surface); color: var(--text-primary); border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        .leaflet-popup-tip { background: var(--bg-surface); border-top: 1px solid var(--border-color); border-left: 1px solid var(--border-color); }
        .leaflet-popup-content { margin: 16px; line-height: 1.5; }
        
        .glowing-pin { display: flex; align-items: center; justify-content: center; position: relative; }
        .glowing-pin-core { width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; position: relative; z-index: 2; box-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .glowing-pin-halo { position: absolute; width: 32px; height: 32px; border-radius: 50%; opacity: 0.3; animation: pulse 2s infinite; z-index: 1; }
        
        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.2; }
            100% { transform: scale(0.8); opacity: 0.5; }
        }
    </style>
@endsection

@section('content')
    <div class="map-wrapper">
        <div id="sebaranMap" style="width: 100%; height: 100%;"></div>

        <!-- Floating Stats -->
        <div class="glass-panel stats-card">
            <div>
                <h2 class="stats-header">Peta Sebaran</h2>
                <p class="stats-sub">Live monitoring reports</p>
            </div>
            
            <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 4px 0;"></div>

            <div class="stat-row">
                <span class="stat-label"><i data-lucide="map" style="width: 14px; height: 14px;"></i> Total Laporan</span>
                <span class="stat-val">{{ $countAll }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label"><span class="stat-dot" style="background: #EAB308;"></span> Menunggu</span>
                <span class="stat-val">{{ $countMenunggu }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label"><span class="stat-dot" style="background: #3B82F6;"></span> Diproses</span>
                <span class="stat-val">{{ $countDiproses }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label"><span class="stat-dot" style="background: #22C55E;"></span> Selesai</span>
                <span class="stat-val">{{ $countSelesai }}</span>
            </div>
        </div>

        <!-- Floating Filters -->
        <div class="glass-panel filter-control">
            <button class="filter-btn active" onclick="filterMarkers('all', this)">Semua</button>
            <button class="filter-btn" onclick="filterMarkers('Menunggu', this)">Menunggu</button>
            <button class="filter-btn" onclick="filterMarkers('Diproses', this)">Diproses</button>
            <button class="filter-btn" onclick="filterMarkers('Selesai', this)">Selesai</button>
        </div>

        <!-- Floating Legend -->
        <div class="glass-panel legend-card">
            <div class="legend-title">Status Indicator</div>
            <div class="legend-row">
                <div class="legend-icon waiting"></div>
                <span>Pending Review</span>
            </div>
            <div class="legend-row">
                <div class="legend-icon processing"></div>
                <span>In Progress</span>
            </div>
            <div class="legend-row">
                <div class="legend-icon completed"></div>
                <span>Resolved</span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const reports = @json($reports);

    const statusColors = {
        'Menunggu': '#EAB308',
        'Diproses': '#3B82F6',
        'Selesai':  '#22C55E'
    };

    const map = L.map('sebaranMap', { zoomControl: false }).setView([-2.5, 118], 5);
    
    // High-end Dark Map Style
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Reposition zoom control
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    let allMarkers = [];

    reports.forEach(r => {
        const color = statusColors[r.status] || '#64748B';
        const icon = L.divIcon({
            className: 'glowing-pin',
            html: `         
                <div class="glowing-pin-halo" style="background: ${color};"></div>
                <div class="glowing-pin-core" style="background: ${color};"></div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -16]
        });

        const popupContent = `
            <div style="font-family: 'Inter', sans-serif; min-width: 220px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">
                    <span style="font-family:monospace; color:var(--text-muted); font-size:12px;">#RPT-${r.id}</span>
                    <span style="font-size:10px; font-weight:600; padding:2px 6px; border-radius:4px; background:rgba(255,255,255,0.1); color:${color}; border:1px solid ${color}40;">${r.status}</span>
                </div>
                <div style="font-size: 13px; color: var(--text-primary); margin-bottom: 12px; line-height: 1.4;">
                    ${r.deskripsi}
                </div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                    <div style="width:24px; height:24px; border-radius:50%; background:var(--bg-deep); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">${r.user.charAt(0).toUpperCase()}</div>
                    <div style="display:flex; flex-direction:column;">
                        <span style="font-size:12px; font-weight:500;">${r.user}</span>
                        <span style="font-size:10px; color:var(--text-muted);">${r.tanggal}</span>
                    </div>
                </div>
                <a href="${r.url}" style="display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 12px; color: #fff; background: rgba(255,255,255,0.05); padding: 8px; border-radius: 6px; text-decoration: none; transition: background 0.2s;">
                    View Details →
                </a>
            </div>
        `;

        const marker = L.marker([r.lat, r.lng], { icon }).bindPopup(popupContent);
        marker._status = r.status;
        marker.addTo(map);
        allMarkers.push(marker);
    });

    if (allMarkers.length > 0) {
        const group = L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.15));
    }

    function filterMarkers(status, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        allMarkers.forEach(m => {
            if (status === 'all' || m._status === status) {
                m.addTo(map);
            } else {
                map.removeLayer(m);
            }
        });
    }
</script>
@endsection
