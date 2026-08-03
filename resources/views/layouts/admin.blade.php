<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $appName = \App\Models\WebsiteSetting::getByKey('app_name', 'Lacete Coffeeshop');
        $footerText = \App\Models\WebsiteSetting::getByKey('footer_text', '© 2026 Lacete Coffeeshop. All rights reserved.');
        $logo = \App\Models\WebsiteSetting::getByKey('logo_path');
        $favicon = \App\Models\WebsiteSetting::getByKey('favicon_path');
    @endphp

    <title>@yield('title', 'Admin Panel') - {{ $appName }}</title>
    
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- SweetAlert2 & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Inline script to prevent theme flash -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body>

    <!-- Loader -->
    <div id="loader" class="loading-screen">
        <div class="loader-spinner"></div>
        <p style="margin-top: 1.2rem; font-weight: 700; color: #ffffff; font-size: 1.1rem; letter-spacing: 0.5px;">Memproses data...</p>
    </div>

    <!-- Main Wrapper -->
    <div id="main-app" style="display: flex; min-height: 100vh;">
        
        <!-- Sidebar -->
        <aside class="sidebar" id="app-sidebar">
            <div class="sidebar-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($logo)
                        <img src="{{ asset($logo) }}" alt="Logo" style="width: 35px; height: 35px; border-radius: 8px; object-fit: cover;">
                    @else
                        <div style="width: 35px; height: 35px; border-radius: 8px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-coffee"></i>
                        </div>
                    @endif
                    <h2 id="sidebar-title">{{ $appName }}</h2>
                </div>
                <button id="sidebar-collapse-btn" class="btn-icon" style="box-shadow: none; background: transparent; border: none;"><i class="fas fa-chevron-left"></i></button>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kriteria.index') }}" class="nav-link {{ Route::is('admin.kriteria.*') && !Route::is('admin.bobot.*') ? 'active' : '' }}">
                            <i class="fas fa-list-check"></i>
                            <span>Data Kriteria</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.bobot.index') }}" class="nav-link {{ Route::is('admin.bobot.*') ? 'active' : '' }}">
                            <i class="fas fa-scale-balanced"></i>
                            <span>Data Bobot</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.supplier.index') }}" class="nav-link {{ Route::is('admin.supplier.*') ? 'active' : '' }}">
                            <i class="fas fa-truck-ramp-box"></i>
                            <span>Data Supplier</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.penilaian.index') }}" class="nav-link {{ Route::is('admin.penilaian.*') ? 'active' : '' }}">
                            <i class="fas fa-pen-to-square"></i>
                            <span>Nilai Penilaian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.smart.index') }}" class="nav-link {{ Route::is('admin.smart.*') ? 'active' : '' }}">
                            <i class="fas fa-calculator"></i>
                            <span>Proses SMART</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.history.index') }}" class="nav-link {{ Route::is('admin.history.*') ? 'active' : '' }}">
                            <i class="fas fa-clock-rotate-left"></i>
                            <span>Riwayat Hitung</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link submenu-trigger" aria-expanded="false">
                            <i class="fas fa-file-invoice"></i>
                            <span>Laporan</span>
                            <i class="fas fa-chevron-down ms-auto animate-chevron" style="font-size: 0.8rem; transition: transform 0.3s;"></i>
                        </a>
                        <ul id="laporanSubmenu" style="display: none; list-style: none; padding-left: 1.5rem; margin-top: 0.2rem; margin-bottom: 0.2rem;">
                            <li style="margin-bottom: 0.2rem;">
                                <a href="{{ route('admin.supplier.report') }}" target="_blank" class="nav-link" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    <i class="fas fa-circle" style="font-size: 0.4rem; color: var(--text-muted); min-width: auto; width: auto; margin-right: 0.5rem;"></i>
                                    <span>Laporan Supplier</span>
                                </a>
                            </li>
                            <li style="margin-bottom: 0.2rem;">
                                <a href="{{ route('admin.penilaian.report') }}" target="_blank" class="nav-link" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    <i class="fas fa-circle" style="font-size: 0.4rem; color: var(--text-muted); min-width: auto; width: auto; margin-right: 0.5rem;"></i>
                                    <span>Laporan Penilaian</span>
                                </a>
                            </li>
                            <li style="margin-bottom: 0.2rem;">
                                <a href="{{ route('admin.smart.report') }}" target="_blank" class="nav-link" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    <i class="fas fa-circle" style="font-size: 0.4rem; color: var(--text-muted); min-width: auto; width: auto; margin-right: 0.5rem;"></i>
                                    <span>Laporan Hasil SMART</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ Route::is('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-sliders"></i>
                            <span>Pengaturan</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="nav-link text-danger" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="app-main-content">
            
            <!-- Navbar -->
            <header class="top-navbar">
                <div class="nav-left">
                    <button id="mobile-toggle" class="btn-icon" style="display: none;"><i class="fas fa-bars"></i></button>
                    
                    <!-- Breadcrumbs & Dynamic Title -->
                    <div class="breadcrumb-container">
                        <span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></span>
                        @yield('breadcrumbs')
                    </div>
                </div>
                
                <div class="nav-right">
                    <!-- Theme Switcher -->
                    <label class="theme-switch" for="checkbox-theme">
                        <input type="checkbox" id="checkbox-theme" />
                        <div class="slider">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                        </div>
                    </label>

                    <!-- User Profile Dropdown -->
                    <div class="user-profile" id="user-profile-menu">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=5B86E5&color=fff" alt="Avatar">
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            <!-- Inner Page Panel -->
            <div class="content-section">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer style="padding: 1.5rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); border-top: 1px solid var(--border-color); background: rgba(255,255,255,0.01); backdrop-filter: blur(10px);">
                {{ $footerText }}
            </footer>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Loader Control
        window.addEventListener('load', () => {
            const loader = document.getElementById('loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 400);
            }
        });

        // Sidebar Collapse
        const sidebar = document.getElementById('app-sidebar');
        const mainContent = document.getElementById('app-main-content');
        const collapseBtn = document.getElementById('sidebar-collapse-btn');
        
        // Restore sidebar state
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            collapseBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        }

        collapseBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
            collapseBtn.innerHTML = isCollapsed ? '<i class="fas fa-chevron-right"></i>' : '<i class="fas fa-chevron-left"></i>';
        });

        // Mobile sidebar trigger
        const mobileToggle = document.getElementById('mobile-toggle');
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });

        // Theme Switch Handler
        const themeSwitch = document.querySelector('#checkbox-theme');
        const currentTheme = localStorage.getItem('theme') || 'light';

        if (currentTheme === 'dark') {
            themeSwitch.checked = true;
        }

        themeSwitch.addEventListener('change', (e) => {
            if (e.target.checked) {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            }
            // Trigger custom event to re-theme charts if needed
            window.dispatchEvent(new Event('theme-changed'));
        });

        // Profile Menu click redirect
        document.getElementById('user-profile-menu').addEventListener('click', () => {
            window.location.href = "{{ route('admin.settings.index') }}";
        });

        // Logout Confirmation Popup
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const logoutUrl = this.getAttribute('href');
                
                Swal.fire({
                    title: 'Ingin Keluar?',
                    text: 'Sesi Anda saat ini akan diakhiri.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4B3935', // Mocha color from theme
                    cancelButtonColor: '#7A6C68',
                    confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Ya, Keluar',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    padding: '1.5em',
                    customClass: {
                        popup: 'swal2-pro-popup',
                        title: 'swal2-pro-title',
                        confirmButton: 'swal2-pro-btn',
                        cancelButton: 'swal2-pro-btn'
                    },
                    backdrop: `rgba(0, 0, 0, 0.4)`
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = logoutUrl;
                    }
                });
            });
        }

        // Submenu Laporan Collapse Toggle
        const submenuTrigger = document.querySelector('.submenu-trigger');
        const submenu = document.getElementById('laporanSubmenu');
        if (submenuTrigger && submenu) {
            submenuTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                const isCollapsed = submenu.style.display === 'none';
                submenu.style.display = isCollapsed ? 'block' : 'none';
                submenuTrigger.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');
                const chevron = submenuTrigger.querySelector('.animate-chevron');
                if (chevron) {
                    chevron.style.transform = isCollapsed ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            });
        }

        // Placeholder Modal for Laporan Menu
        window.showReportPlaceholder = function(reportName) {
            Swal.fire({
                icon: 'info',
                title: 'Halaman Laporan',
                text: 'Fitur Laporan ' + reportName + ' sedang dalam pengembangan dan akan segera hadir!',
                confirmButtonColor: 'var(--mustard-gold)'
            });
        }
    </script>

    <!-- SweetAlert Messages -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            confirmButtonColor: 'var(--primary)'
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: 'var(--primary)'
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Validasi',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: 'var(--primary)'
        });
    </script>
    @endif
    
    @yield('scripts')
</body>
</html>
