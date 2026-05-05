<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Trashpot Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @yield('head')
    <style>
        :root {
            --bg-deep: #0F172A;
            --bg-surface: #1E293B;
            --bg-surface-hover: #334155;
            --border-color: #334155;
            
            --accent-green: #22C55E;
            --accent-green-light: rgba(34, 197, 94, 0.15);
            --accent-blue: #3B82F6;
            --accent-blue-light: rgba(59, 130, 246, 0.15);
            --accent-yellow: #EAB308;
            --accent-yellow-light: rgba(234, 179, 8, 0.15);
            --accent-purple: #A855F7;
            --accent-purple-light: rgba(168, 85, 247, 0.15);
            
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
            
            --font-family: 'Inter', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-deep);
            color: var(--text-primary);
            display: flex;
            height: 100vh;
            overflow: hidden;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
        }

        /* Utilities */
        .flex { display: flex; }
        .flex-col { display: flex; flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-1 { gap: 0.25rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .w-full { width: 100%; }
        .h-full { height: 100%; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }
        .text-secondary { color: var(--text-secondary); }
        .text-muted { color: var(--text-muted); }
        .text-green { color: var(--accent-green); }
        .text-yellow { color: var(--accent-yellow); }
        .text-blue { color: var(--accent-blue); }
        .mt-4 { margin-top: 1rem; }
        .cursor-pointer { cursor: pointer; }

        a { text-decoration: none; color: inherit; }

        /* Tag Colors */
        .tag {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .tag-waiting { color: var(--accent-yellow); background: var(--accent-yellow-light); }
        .tag-processing { color: var(--accent-blue); background: var(--accent-blue-light); }
        .tag-completed { color: var(--accent-green); background: var(--accent-green-light); }
        
        .tag-gray { color: var(--text-secondary); background: var(--bg-surface-hover); }

        .dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        .dot-waiting { background: var(--accent-yellow); }
        .dot-processing { background: var(--accent-blue); }
        .dot-completed { background: var(--accent-green); }

        /* Layout */
        .sidebar {
            width: 260px;
            background-color: var(--bg-deep);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 24px;
            flex-shrink: 0;
            z-index: 10;
        }

        .main-content {
            flex-grow: 1;
            padding: 32px 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Sidebar Elements */
        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }
        .logo-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            object-fit: cover;
        }
        .logo-text h1 {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.1;
        }
        .logo-text p {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: none;
            font-family: inherit;
            font-size: 14px;
            width: 100%;
            text-align: left;
        }

        .nav-item:hover, .nav-item.active {
            background-color: var(--bg-surface);
            color: var(--text-primary);
        }

        .nav-item.active {
            border-left: 3px solid var(--accent-green);
            border-radius: 0 8px 8px 0;
            background: linear-gradient(90deg, rgba(34, 197, 94, 0.1) 0%, transparent 100%);
        }

        .nav-item i { width: 20px; height: 20px; }

        .sidebar-footer {
            margin-top: auto;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background-color: var(--bg-surface);
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0F172A;
            font-weight: 600;
            font-size: 14px;
        }
        .user-info h4 { font-size: 13px; font-weight: 500; }
        .user-info p { font-size: 11px; color: var(--text-muted); }
        .copyright { font-size: 11px; color: var(--text-muted); text-align: center; }

        /* General UI Elements */
        .card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card-title { font-weight: 600; font-size: 15px; }
        .section-title { font-size: 15px; font-weight: 600; margin-bottom: 16px; }
        
        .btn {
            background: var(--bg-surface); border: 1px solid var(--border-color);
            color: var(--text-primary); padding: 8px 16px; border-radius: 6px;
            font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn:hover { background: var(--bg-surface-hover); }
        .btn-icon { padding: 8px; }
        .btn-success { background: var(--accent-green); border-color: var(--accent-green); color: #fff; }
        .btn-success:hover { background: #16a34a; }
        .btn-danger { background: #ef4444; border-color: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .header-title h2 { font-size: 24px; font-weight: 600; margin-bottom: 4px; }
        .header-title p { color: var(--text-secondary); font-size: 14px; }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .header-greeting { color: var(--text-secondary); font-size: 14px; }

        /* Lightbox */
        .lightbox-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.9); z-index: 9999;
            display: none; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s;
        }
        .lightbox-overlay.active { display: flex; opacity: 1; }
        .lightbox-overlay img { max-width: 90%; max-height: 90%; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            display: flex; flex-direction: column; gap: 8px;
        }
        .toast {
            background: var(--bg-surface); border: 1px solid var(--border-color);
            padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease forwards;
        }
        .toast-success { border-left: 4px solid var(--accent-green); }
        .toast-danger { border-left: 4px solid #ef4444; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        
        /* Modal */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.8); z-index: 10000;
            display: none; align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: var(--bg-surface); border: 1px solid var(--border-color);
            padding: 24px; border-radius: 12px; max-width: 400px; width: 90%;
            display: flex; flex-direction: column; align-items: center; text-align: center; gap: 12px;
        }
        .modal-actions { display: flex; gap: 12px; width: 100%; margin-top: 16px; }
        .modal-actions .btn { flex: 1; justify-content: center; }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-area">
            <img src="{{ asset('img/trashpot-logo.jpg') }}" alt="Trashpot Logo" class="logo-icon" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' fill=\'%2314532D\'/><path d=\'M30 70 Q 50 30 70 30 Q 70 50 50 70 Q 30 70 30 70 Z\' fill=\'%23FFFFFF\'/></svg>'">
            <div class="logo-text">
                <h1>Trashpot</h1>
                <p>Admin Panel</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i data-lucide="file-text"></i>
                <span>Kelola Laporan</span>
            </a>
            <a href="{{ route('admin.map') }}" class="nav-item {{ request()->routeIs('admin.map') ? 'active' : '' }}">
                <i data-lucide="map"></i>
                <span>Peta Sebaran</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i data-lucide="users"></i>
                <span>Kelola User</span>
            </a>
            <a href="{{ route('admin.rekap') }}" class="nav-item {{ request()->routeIs('admin.rekap') ? 'active' : '' }}">
                <i data-lucide="bar-chart-2"></i>
                <span>Rekap & Export</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'Admin', 0, 1)) }}
                </div>
                <div class="user-info">
                    <h4>{{ Auth::user()->name ?? 'Admin User' }}</h4>
                    <p>Administrator</p>
                </div>
            </div>
            
            <form action="{{ route('admin.logout') }}" method="POST" style="margin-bottom: 16px;">
                @csrf
                <button type="submit" class="nav-item" style="color: #ef4444;">
                    <i data-lucide="log-out"></i>
                    <span>Logout</span>
                </button>
            </form>

            <p class="copyright">
                © {{ date('Y') }} Trashpot. All rights reserved.
            </p>
        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    {{-- Lightbox --}}
    <div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
        <img id="lightbox-img" src="" alt="Preview">
    </div>

    {{-- Toast Container --}}
    <div class="toast-container" id="toastContainer"></div>

    {{-- Confirmation Modal --}}
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box">
            <div id="modalIcon" style="font-size: 32px; margin-bottom: 8px;">⚠️</div>
            <h3 id="modalTitle" style="font-size: 18px; font-weight: 600;">Konfirmasi</h3>
            <p id="modalMessage" style="color: var(--text-secondary); font-size: 14px;">Apakah Anda yakin?</p>
            <div class="modal-actions">
                <button class="btn" onclick="closeModal()">Batal</button>
                <button class="btn btn-success" id="modalConfirmBtn" onclick="modalConfirmAction()">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // ── Lightbox ──
        function openLightbox(src) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox').classList.add('active');
        }
        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
                closeModal();
            }
        });

        // ── Toast Notifications ──
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const icons = { success: 'check-circle', danger: 'alert-circle' };
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <i data-lucide="${icons[type] || 'info'}" style="color: ${type === 'success' ? 'var(--accent-green)' : '#ef4444'}"></i>
                <span style="font-size: 13px; font-weight: 500;">${message}</span>
            `;
            container.appendChild(toast);
            lucide.createIcons();
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // ── Confirmation Modal ──
        let _modalCallback = null;
        function openModal(options) {
            document.getElementById('modalTitle').textContent = options.title || 'Konfirmasi';
            document.getElementById('modalMessage').textContent = options.message || 'Apakah Anda yakin?';
            const confirmBtn = document.getElementById('modalConfirmBtn');
            confirmBtn.textContent = options.confirmText || 'Ya, Lanjutkan';
            
            // remove all classes and add base btn
            confirmBtn.className = 'btn';
            if(options.btnClass) confirmBtn.classList.add(options.btnClass);
            else confirmBtn.classList.add('btn-success');
            
            _modalCallback = options.onConfirm || null;
            document.getElementById('confirmModal').classList.add('active');
        }
        function closeModal() {
            document.getElementById('confirmModal').classList.remove('active');
            _modalCallback = null;
        }
        function modalConfirmAction() {
            if (_modalCallback) _modalCallback();
            closeModal();
        }

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
        @endif
        @if(session('error'))
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'danger'));
        @endif
    </script>
    
    @yield('scripts')
</body>
</html>
