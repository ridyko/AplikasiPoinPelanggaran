<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAT - SMKN 2 Jakarta</title>
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
</head>
<body class="landing-body">
    <!-- Navigation Bar -->
    <nav class="landing-nav">
        <div class="landing-brand">
            <img src="{{ asset('images/smkn2-logo.png') }}" alt="Logo SMK Negeri 2 Jakarta" class="landing-logo">
            <div class="landing-brand-text">
                <span class="landing-brand-title">SIKAT</span>
                <span class="landing-brand-subtitle">SMK NEGERI 2 JAKARTA</span>
            </div>
        </div>
        <div class="landing-nav-actions">
            <a href="{{ route('siswa.lengkapi_data') }}" class="landing-btn-secondary" style="padding: 10px 18px; border-radius: 12px; font-size: 0.9rem; margin-right: 12px; border: 1px solid rgba(14, 165, 233, 0.3); background: rgba(14, 165, 233, 0.05); color: #0ea5e9;">
                LENGKAPI DATA WALI
            </a>
            <a href="{{ route('login') }}" class="landing-btn-primary" style="padding: 10px 24px; border-radius: 12px; font-size: 0.9rem; box-shadow: none;">
                MASUK
            </a>
            <button id="theme-toggle-landing" class="theme-toggle-btn" title="Ubah Tema">
                <i class="fa-solid fa-moon"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="landing-hero">
        <div class="landing-badge">
            <i class="fa-solid fa-shield-halved" style="margin-right: 4px;"></i> SIKAT v2.0 • Sistem Informasi Ketertiban & Aturan Sekolah
        </div>
        
        <h1 class="landing-title">
            Pantau Kedisiplinan Siswa Jadi<br>
            <span>Lebih Terkendali.</span>
        </h1>
        
        <p class="landing-subtitle">
            <strong>SIKAT</strong> adalah <strong>Sistem Informasi Ketertiban & Aturan Sekolah</strong> SMKN 2 Jakarta yang dirancang untuk mengelola pencatatan pelanggaran, akumulasi poin kedisiplinan, serta pengiriman notifikasi WhatsApp otomatis ke orang tua secara akurat dan transparan.
        </p>
        
        <div class="landing-buttons">
            <a href="{{ route('siswa.check') }}" class="landing-btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> PERIKSA POIN SISWA
            </a>
            <a href="{{ route('login') }}" class="landing-btn-secondary">
                <i class="fa-solid fa-lock"></i> MASUK SEBAGAI STAFF
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="landing-footer">
        &copy; {{ date('Y') }} SMK NEGERI 2 JAKARTA
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeBtn = document.getElementById('theme-toggle-landing');
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
</body>
</html>
