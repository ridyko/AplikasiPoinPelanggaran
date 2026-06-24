<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIKAT') - SMKN 2 Jakarta</title>
    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Theme Script to avoid flash of wrong theme -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    @yield('styles')
</head>
<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <button id="sidebar-toggle" class="sidebar-toggle-btn" title="Buka Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="mobile-brand">SIKAT</a>
        <!-- Mobile Theme Toggle -->
        <button id="theme-toggle" class="sidebar-toggle-btn" title="Ubah Tema" style="font-size: 1.1rem; width: 40px; height: 40px;">
            <i class="fa-solid fa-moon"></i>
        </button>
    </header>

    <!-- Sidebar Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="glass-sidebar" id="glass-sidebar">
            <div class="sidebar-brand">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="nav-logo">
                    <i class="fa-solid fa-shield-halved"></i> SIKAT
                </a>
                
                <!-- Close Button for Mobile -->
                <button id="sidebar-close" class="sidebar-close-btn" title="Tutup Menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <ul class="sidebar-links">
                @auth
                    <li>
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('violations.create') }}" class="sidebar-link {{ request()->routeIs('violations.create') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-exclamation"></i> Catat Pelanggaran
                        </a>
                    </li>
                    @if(auth()->user()->isBkOrAbove())
                        <li>
                            <a href="{{ route('students') }}" class="sidebar-link {{ request()->routeIs('students') ? 'active' : '' }}">
                                <i class="fa-solid fa-graduation-cap"></i> Siswa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-invoice"></i> Laporan Rekap
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->role === 'super_admin')
                        <li>
                            <a href="{{ route('violations.index') }}" class="sidebar-link {{ request()->routeIs('violations.index') || request()->routeIs('violations.edit') ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check"></i> Jenis Pelanggaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users') }}" class="sidebar-link {{ request()->routeIs('users') ? 'active' : '' }}">
                                <i class="fa-solid fa-users-gear"></i> Kelola User
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kelas.index') }}" class="sidebar-link {{ request()->routeIs('kelas.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-school"></i> Kelola Kelas
                            </a>
                        </li>
                    @endif
                @else
                    <li>
                        <a href="{{ route('siswa.check') }}" class="sidebar-link {{ request()->routeIs('siswa.check') ? 'active' : '' }}">
                            <i class="fa-solid fa-magnifying-glass"></i> Cek Poin Siswa
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="sidebar-link {{ request()->routeIs('login') ? 'active' : '' }}">
                            <i class="fa-solid fa-lock"></i> Login Staff
                        </a>
                    </li>
                @endauth
            </ul>

            <div class="sidebar-footer">
                @auth
                    <div style="font-size: 0.88rem; opacity: 0.9; display: flex; flex-direction: column; gap: 4px; overflow: hidden;">
                        <span style="font-weight: 600; color: var(--text-primary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ auth()->user()->name }}">
                            <i class="fa-solid fa-user-tie"></i> {{ auth()->user()->name }}
                        </span>
                        <span style="font-size: 0.78rem; color: var(--text-secondary);">
                            {{ auth()->user()->roleLabel() }}
                        </span>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" style="width: 100%; margin: 0;">
                        @csrf
                        <button type="submit" class="btn-danger" style="width: 100%; padding: 10px; font-size: 0.85rem; border-radius: 10px; min-height: 40px; height: 40px;">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </form>
                @endauth
                
                <!-- Desktop Theme Toggle -->
                <button id="theme-toggle-desktop" class="theme-toggle-btn desktop-theme-btn" title="Ubah Tema" style="width: 100%; border-radius: 10px; padding: 10px; min-height: 40px; height: 40px; gap: 8px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-moon"></i> <span>Ubah Tema</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-content">
            <main class="container">
                @if(session('success'))
                    <div class="alert-glass-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-glass">
                        <i class="fa-solid fa-triangle-exclamation"></i> {!! session('error') !!}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggles = [
                document.getElementById('theme-toggle'),
                document.getElementById('theme-toggle-desktop')
            ];
            
            // Function to update toggle icon
            const updateIcon = (theme) => {
                themeToggles.forEach(btn => {
                    if (btn) {
                        const icon = btn.querySelector('i');
                        const span = btn.querySelector('span');
                        if (theme === 'light') {
                            icon.className = 'fa-solid fa-sun';
                            icon.style.color = '#f59e0b'; // Sun amber color
                            if (span) span.textContent = 'Mode Terang';
                        } else {
                            icon.className = 'fa-solid fa-moon';
                            icon.style.color = ''; // Default color
                            if (span) span.textContent = 'Mode Gelap';
                        }
                    }
                });
            };
            
            // Set initial state
            let currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            updateIcon(currentTheme);

            // Click listener for all toggles
            themeToggles.forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', () => {
                        currentTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                        document.documentElement.setAttribute('data-theme', currentTheme);
                        localStorage.setItem('theme', currentTheme);
                        updateIcon(currentTheme);
                    });
                }
            });

            // Sidebar Toggle Logic for Mobile
            const sidebar = document.getElementById('glass-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const openBtn = document.getElementById('sidebar-toggle');
            const closeBtn = document.getElementById('sidebar-close');

            if (sidebar && overlay) {
                const openSidebar = () => {
                    sidebar.classList.add('active');
                    overlay.classList.add('active');
                };

                const closeSidebar = () => {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                };

                if (openBtn) openBtn.addEventListener('click', openSidebar);
                if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
                overlay.addEventListener('click', closeSidebar);

                // Auto-close sidebar on link click (helpful on mobile)
                const sidebarLinks = sidebar.querySelectorAll('.sidebar-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', closeSidebar);
                });
            }

            // Initialize all Searchable Dropdowns
            const initSearchableDropdowns = () => {
                const selectWrappers = document.querySelectorAll('.searchable-select');
                
                selectWrappers.forEach(wrapper => {
                    const select = wrapper.querySelector('.hidden-select');
                    const trigger = wrapper.querySelector('.searchable-select-trigger');
                    const dropdown = wrapper.querySelector('.searchable-select-dropdown');
                    const searchInput = wrapper.querySelector('.dropdown-search-input');
                    const optionsContainer = wrapper.querySelector('.dropdown-options');
                    const triggerText = wrapper.querySelector('.trigger-text');
                    
                    if (!select || !trigger || !dropdown) return;
                    
                    const optionItems = optionsContainer.querySelectorAll('.dropdown-option-item');
                    
                    // Helper to set selected value
                    const selectOption = (value, label, element) => {
                        select.value = value;
                        triggerText.textContent = label;
                        
                        // Fire change event on native select
                        select.dispatchEvent(new Event('change'));
                        
                        optionItems.forEach(item => item.classList.remove('selected'));
                        if (element) {
                            element.classList.add('selected');
                        }
                        
                        closeDropdown();
                    };
                    
                    // Set initial label based on old/preselected option
                    const initialSelected = select.querySelector('option:checked');
                    if (initialSelected && initialSelected.value !== "") {
                        triggerText.textContent = initialSelected.textContent.trim();
                        // Find matching item and highlight it
                        const matchingItem = Array.from(optionItems).find(item => item.getAttribute('data-value') == initialSelected.value);
                        if (matchingItem) {
                            matchingItem.classList.add('selected');
                        }
                    } else {
                        const defaultPlaceholder = select.querySelector('option[disabled][selected]') || select.querySelector('option[value=""]');
                        triggerText.textContent = defaultPlaceholder ? defaultPlaceholder.textContent : 'Pilih...';
                    }
                    
                    const openDropdown = () => {
                        // Close all other open searchable select dropdowns first
                        document.querySelectorAll('.searchable-select.open').forEach(openWrapper => {
                            if (openWrapper !== wrapper) {
                                openWrapper.classList.remove('open');
                                openWrapper.querySelector('.searchable-select-dropdown').style.display = 'none';
                            }
                        });
                        
                        wrapper.classList.add('open');
                        dropdown.style.display = 'block';
                        searchInput.value = '';
                        // Reset visibility of all options
                        optionItems.forEach(item => {
                            item.style.display = 'flex';
                        });
                        
                        // Remove no-results message if any
                        const noRes = optionsContainer.querySelector('.no-results');
                        if (noRes) noRes.remove();
                        
                        setTimeout(() => searchInput.focus(), 50);
                    };
                    
                    const closeDropdown = () => {
                        wrapper.classList.remove('open');
                        dropdown.style.display = 'none';
                    };
                    
                    // Toggle dropdown on trigger click
                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isOpen = wrapper.classList.contains('open');
                        if (isOpen) {
                            closeDropdown();
                        } else {
                            openDropdown();
                        }
                    });
                    
                    // Stop propagation when clicking inside dropdown
                    dropdown.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                    
                    // Filter options on search input
                    searchInput.addEventListener('input', (e) => {
                        const query = e.target.value.toLowerCase().trim();
                        let matchCount = 0;
                        
                        optionItems.forEach(item => {
                            if (item.classList.contains('no-results')) return;
                            const searchData = item.getAttribute('data-search') || '';
                            if (searchData.includes(query)) {
                                item.style.display = 'flex';
                                matchCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        
                        // Handle no results
                        let noRes = optionsContainer.querySelector('.no-results');
                        if (matchCount === 0) {
                            if (!noRes) {
                                noRes = document.createElement('div');
                                noRes.className = 'dropdown-option-item no-results';
                                noRes.innerHTML = '<i class="fa-solid fa-face-frown"></i><span class="option-title" style="margin-top: 5px;">Tidak ada hasil ditemukan</span>';
                                optionsContainer.appendChild(noRes);
                            }
                        } else {
                            if (noRes) noRes.remove();
                        }
                    });
                    
                    // Option item click
                    optionItems.forEach(item => {
                        item.addEventListener('click', () => {
                            const val = item.getAttribute('data-value');
                            // Get text content of only the option title
                            const titleEl = item.querySelector('.option-title');
                            const label = titleEl.innerText || titleEl.textContent;
                            selectOption(val, label.trim(), item);
                        });
                    });
                });
                
                // Click outside closes all dropdowns
                document.addEventListener('click', () => {
                    document.querySelectorAll('.searchable-select.open').forEach(wrapper => {
                        wrapper.classList.remove('open');
                        wrapper.querySelector('.searchable-select-dropdown').style.display = 'none';
                    });
                });
            };
            
            initSearchableDropdowns();
        });
    </script>
    @yield('scripts')
</body>
</html>
