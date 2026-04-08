<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Trashpot Admin</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>♻️</text></svg>">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('head')
</head>
<body>
    <div class="admin-layout">
        {{-- Sidebar Overlay (mobile) --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">♻️</div>
                <div>
                    <h2>Trashpot</h2>
                    <small>Admin Panel</small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span>
                    Kelola Laporan
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span>
                    Kelola User
                </a>
                <a href="{{ route('admin.map') }}" class="nav-item {{ request()->routeIs('admin.map') ? 'active' : '' }}">
                    <span class="nav-icon">🗺️</span>
                    Peta Sebaran
                </a>

                <div class="nav-label" style="margin-top:8px;">Tools</div>
                <a href="{{ route('admin.rekap') }}" class="nav-item {{ request()->routeIs('admin.rekap*') ? 'active' : '' }}">
                    <span class="nav-icon">📥</span>
                    Rekap & Export
                </a>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="main-content">
            <header class="content-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <button class="hamburger-btn" onclick="toggleSidebar()" aria-label="Menu">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="header-user">
                    <span>{{ Auth::user()->name }}</span>
                    <div class="user-avatar">👤</div>
                </div>
            </header>

            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Lightbox --}}
    <div class="lightbox-overlay" id="lightbox" onclick="closeLightbox()">
        <img id="lightbox-img" src="" alt="Preview">
    </div>

    {{-- Toast Container --}}
    <div class="toast-container" id="toastContainer"></div>

    {{-- Confirmation Modal --}}
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box">
            <div class="modal-icon" id="modalIcon">⚠️</div>
            <h3 id="modalTitle">Konfirmasi</h3>
            <p id="modalMessage">Apakah Anda yakin?</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button class="btn btn-primary" id="modalConfirmBtn" onclick="modalConfirmAction()">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
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

        // ── Hamburger Sidebar (mobile) ──
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }

        // ── Toast Notifications ──
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const icons = { success: '✅', danger: '❌', warning: '⚠️' };
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <span class="toast-icon">${icons[type] || '✅'}</span>
                <span class="toast-message">${message}</span>
                <button class="toast-close" onclick="dismissToast(this)">&times;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                dismissToast(toast.querySelector('.toast-close'));
            }, 4000);
        }

        function dismissToast(btn) {
            const toast = btn.closest ? btn.closest('.toast') : btn.parentElement;
            if (!toast || toast.classList.contains('removing')) return;
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 400);
        }

        // ── Confirmation Modal ──
        let _modalCallback = null;
        function openModal(options) {
            document.getElementById('modalIcon').textContent = options.icon || '⚠️';
            document.getElementById('modalTitle').textContent = options.title || 'Konfirmasi';
            document.getElementById('modalMessage').textContent = options.message || 'Apakah Anda yakin?';
            const confirmBtn = document.getElementById('modalConfirmBtn');
            confirmBtn.textContent = options.confirmText || 'Ya, Lanjutkan';
            confirmBtn.className = `btn ${options.btnClass || 'btn-primary'}`;
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

        // ── Button loading state ──
        function setLoading(btn) {
            btn.classList.add('loading');
        }

        // ── Flash messages → Toast ──
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
