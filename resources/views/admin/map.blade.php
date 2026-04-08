@extends('admin.layouts.app')

@section('title', 'Peta Sebaran Laporan')
@section('page-title', 'Peta Sebaran')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .map-fullpage {
            height: calc(100vh - var(--header-height) - 64px);
            min-height: 500px;
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow);
            position: relative;
        }

        .map-legend {
            position: absolute;
            bottom: 24px;
            left: 24px;
            z-index: 1000;
            background: var(--card-bg);
            border-radius: var(--radius-sm);
            padding: 16px 20px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-light);
        }

        .map-legend h4 {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 10px;
        }

        .map-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .map-legend-item:last-child {
            margin-bottom: 0;
        }

        .map-legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }

        .map-stats-bar {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .map-stat-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--card-bg);
            border: 1px solid var(--border-light);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            box-shadow: var(--shadow-sm);
        }

        .map-stat-chip strong {
            color: var(--text);
            font-size: 15px;
        }

        .map-filter-bar {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 1000;
            display: flex;
            gap: 4px;
            background: var(--card-bg);
            border-radius: var(--radius-sm);
            padding: 4px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-light);
        }

        .map-filter-btn {
            padding: 6px 12px;
            border: none;
            border-radius: var(--radius-xs);
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            background: transparent;
            color: var(--text-muted);
        }

        .map-filter-btn:hover {
            background: var(--bg);
            color: var(--text);
        }

        .map-filter-btn.active {
            background: var(--primary);
            color: #fff;
        }
    </style>
@endsection

@section('content')
    {{-- Stats Bar --}}
    <div class="map-stats-bar">
        <div class="map-stat-chip">
            📍 <strong>{{ $countAll }}</strong> Total Laporan
        </div>
        <div class="map-stat-chip">
            <span class="map-legend-dot" style="background:#f59e0b;"></span>
            <strong>{{ $countMenunggu }}</strong> Menunggu
        </div>
        <div class="map-stat-chip">
            <span class="map-legend-dot" style="background:#3b82f6;"></span>
            <strong>{{ $countDiproses }}</strong> Diproses
        </div>
        <div class="map-stat-chip">
            <span class="map-legend-dot" style="background:#10b981;"></span>
            <strong>{{ $countSelesai }}</strong> Selesai
        </div>
    </div>

    {{-- Map --}}
    <div class="map-fullpage" id="mapContainer">
        <div id="sebaranMap" style="width:100%;height:100%;"></div>

        {{-- Filter overlay --}}
        <div class="map-filter-bar">
            <button class="map-filter-btn active" onclick="filterMarkers('all', this)">Semua</button>
            <button class="map-filter-btn" onclick="filterMarkers('Menunggu', this)">Menunggu</button>
            <button class="map-filter-btn" onclick="filterMarkers('Diproses', this)">Diproses</button>
            <button class="map-filter-btn" onclick="filterMarkers('Selesai', this)">Selesai</button>
        </div>

        {{-- Legend --}}
        <div class="map-legend">
            <h4>Keterangan Status</h4>
            <div class="map-legend-item">
                <span class="map-legend-dot" style="background:#f59e0b;"></span> Menunggu
            </div>
            <div class="map-legend-item">
                <span class="map-legend-dot" style="background:#3b82f6;"></span> Diproses
            </div>
            <div class="map-legend-item">
                <span class="map-legend-dot" style="background:#10b981;"></span> Selesai
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const reports = @json($reports);

    // Color map per status
    const statusColors = {
        'Menunggu': '#f59e0b',
        'Diproses': '#3b82f6',
        'Selesai':  '#10b981'
    };

    // Init map — center on Indonesia
    const map = L.map('sebaranMap').setView([-2.5, 118], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Create markers
    let allMarkers = [];

    reports.forEach(r => {
        const color = statusColors[r.status] || '#6b7280';
        const icon = L.divIcon({
            className: '',
            html: `<div style="
                width: 14px; height: 14px;
                background: ${color};
                border: 3px solid #fff;
                border-radius: 50%;
                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            "></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });

        const marker = L.marker([r.lat, r.lng], { icon })
            .bindPopup(`
                <div style="font-family:Inter,sans-serif; min-width:180px;">
                    <div style="font-weight:700; font-size:14px; margin-bottom:4px;">
                        Laporan #${r.id}
                    </div>
                    <div style="font-size:12px; color:#666; margin-bottom:6px;">
                        ${r.deskripsi}
                    </div>
                    <div style="font-size:11px; color:#999; margin-bottom:8px;">
                        👤 ${r.user} · 📅 ${r.tanggal}
                    </div>
                    <div style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; color:#fff; background:${color};">
                        ${r.status}
                    </div>
                    <br>
                    <a href="${r.url}" style="display:inline-block; margin-top:8px; font-size:12px; color:#059669; font-weight:500;">
                        Lihat Detail →
                    </a>
                </div>
            `);

        marker._status = r.status;
        marker.addTo(map);
        allMarkers.push(marker);
    });

    // Auto fit bounds
    if (allMarkers.length > 0) {
        const group = L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.15));
    }

    // Filter function
    function filterMarkers(status, btn) {
        // Update active button
        document.querySelectorAll('.map-filter-btn').forEach(b => b.classList.remove('active'));
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
