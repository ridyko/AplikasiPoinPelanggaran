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
<body class="guest-body">
    <!-- Guest Layout Container -->
    <div class="guest-container">
        <!-- Logo & Title Section -->
        <div class="guest-header">
            <img src="{{ asset('images/smkn2-logo.png') }}" alt="SMK Negeri 2 Jakarta Logo" class="guest-logo">
            <h1 class="guest-title">SIKAT</h1>
            <p class="guest-subtitle">SMK NEGERI 2 JAKARTA</p>
        </div>

        <!-- Main Card Section -->
        <div class="guest-card-wrapper">
            @yield('content')
        </div>

        <!-- Footer Section -->
        <div class="guest-footer">
            @yield('footer_links')
            <div class="guest-copyright">
                &copy; {{ date('Y') }} SMK NEGERI 2 JAKARTA
            </div>
        </div>
    </div>

    <!-- Theme Toggle at Top Right -->
    <div class="guest-theme-toggle">
        <button id="theme-toggle-guest" class="theme-toggle-btn" title="Ubah Tema">
            <i class="fa-solid fa-moon"></i>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeBtn = document.getElementById('theme-toggle-guest');
            if (themeBtn) {
                const updateIcon = (theme) => {
                    const icon = themeBtn.querySelector('i');
                    if (theme === 'light') {
                        icon.className = 'fa-solid fa-sun';
                        icon.style.color = '#f59e0b';
                    } else {
                        icon.className = 'fa-solid fa-moon';
                        icon.style.color = '';
                    }
                };
                
                let currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                updateIcon(currentTheme);

                themeBtn.addEventListener('click', () => {
                    currentTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', currentTheme);
                    localStorage.setItem('theme', currentTheme);
                    updateIcon(currentTheme);
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
