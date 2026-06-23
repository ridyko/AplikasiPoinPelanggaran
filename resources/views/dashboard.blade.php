@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">Dashboard</h1>
        <p style="color: var(--text-secondary);">
            @php
                $roleSubtitle = match(auth()->user()->role) {
                    'wali_kelas'      => 'Memantau kelas ' . ($myClass->class_name ?? '-'),
                    'guru'            => 'Catat & pantau pelanggaran siswa',
                    'wakil_kesiswaan' => 'Monitoring Kedisiplinan & Rekap Pelanggaran',
                    'guru_bk'         => 'Pusat Kontrol Kedisiplinan Siswa',
                    default           => 'Pusat Kontrol Kedisiplinan Siswa',
                };
            @endphp
            {{ $roleSubtitle }}
        </p>
    </div>
    
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        @if($isWaliKelas && $myClass)
            <a href="{{ route('reports.export_excel', ['class_id' => $myClass->id]) }}" class="btn-primary" style="padding: 10px 18px; font-size: 0.88rem; background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); box-shadow: none; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; border-radius: 10px; height: 40px; min-height: 40px; align-self: center;">
                <i class="fa-solid fa-file-excel"></i> Unduh Rekap Kelas
            </a>
        @endif

        @if(auth()->user()->role === 'guru')
            <a href="{{ route('violations.create') }}" class="btn-primary" style="padding: 10px 20px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border-radius: 10px; height: 40px; min-height: 40px; align-self: center; background: linear-gradient(135deg, rgba(139,92,246,0.3), rgba(6,182,212,0.2)); border: 1px solid rgba(139,92,246,0.4);">
                <i class="fa-solid fa-circle-plus"></i> Catat Pelanggaran
            </a>
        @endif

        <!-- WA Gateway Status Badge -->
        <div class="glass-panel" style="padding: 10px 20px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 0.9rem; color: var(--text-secondary); font-weight: 500;">Status WA Gateway:</span>
            @if($waStatus['status'] === 'connected')
                <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Terhubung</span>
            @elseif($waStatus['status'] === 'connecting')
                <span class="badge badge-warning"><i class="fa-solid fa-spinner fa-spin"></i> Menghubungkan</span>
            @elseif($waStatus['status'] === 'disconnected' && !empty($waStatus['qr']))
                <span class="badge badge-danger"><i class="fa-solid fa-qrcode"></i> Scan QR</span>
            @else
                <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Offline</span>
            @endif
        </div>
    </div>
</div>

<!-- QR Code scanner panel for WhatsApp (BK only) -->
@if(auth()->user()->role === 'super_admin' && $waStatus['status'] !== 'connected')
<div class="glass-panel" style="padding: 30px; margin-bottom: 30px; border-radius: 20px; background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2);">
    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 20px;">
        <div style="max-width: 600px;">
            <h3 style="font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">
                <i class="fa-brands fa-whatsapp" style="color: #25D366; font-size: 1.8rem; vertical-align: middle;"></i> Hubungkan WhatsApp Gateway Lokal
            </h3>
            <p style="color: var(--text-secondary); font-size: 0.95rem;">
                Agar sistem dapat mengirimkan notifikasi pelanggaran ke orang tua secara otomatis, silakan scan QR Code di bawah ini menggunakan aplikasi WhatsApp di HP Anda (Pilih Perangkat Tertaut > Tautkan Perangkat).
            </p>
        </div>

        @if(!empty($waStatus['qr']))
            <div style="background: #fff; padding: 15px; border-radius: 12px; display: inline-block; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($waStatus['qr']) }}" alt="WhatsApp QR Code" style="display: block;">
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted);">
                <i class="fa-solid fa-arrows-rotate fa-spin"></i> Halaman ini akan memuat ulang secara otomatis setelah terhubung.
            </p>
            <script>
                // Auto-refresh script to check WA gateway connection status
                setInterval(function() {
                    fetch('http://localhost:3000/status')
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'connected') {
                                window.location.reload();
                            }
                        })
                        .catch(err => console.log('Node.js gateway offline'));
                }, 3000);
            </script>
        @else
            <div style="padding: 40px; text-align: center;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; color: var(--danger); margin-bottom: 15px;"></i>
                <p style="color: var(--text-primary); font-weight: 600; margin-bottom: 15px;">Server Node.js WhatsApp Gateway Offline</p>
                
                <form action="{{ route('whatsapp.start') }}" method="POST" id="start-wa-form" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn-primary" id="btn-start-wa" style="padding: 12px 30px;">
                        <i class="fa-solid fa-play"></i> Jalankan WhatsApp Gateway
                    </button>
                </form>

                <p id="wa-loading-text" style="display: none; color: var(--accent-cyan); font-weight: 600; margin-top: 15px;">
                    <i class="fa-solid fa-spinner fa-spin"></i> Menjalankan server WhatsApp Gateway, silakan tunggu...
                </p>
            </div>
            
            <script>
                document.getElementById('start-wa-form').addEventListener('submit', function() {
                    const btn = document.getElementById('btn-start-wa');
                    const loadingText = document.getElementById('wa-loading-text');
                    btn.style.display = 'none';
                    loadingText.style.display = 'block';
                });
            </script>
        @endif
    </div>
</div>
@endif

<!-- Control Panel for Active WhatsApp Gateway (Super Admin only) -->
@if(auth()->user()->role === 'super_admin' && $waStatus['status'] === 'connected')
<div class="glass-panel" style="padding: 20px 30px; margin-bottom: 30px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2); flex-wrap: wrap; gap: 20px;">
    <div>
        <h3 style="font-weight: 700; color: #fff; margin-bottom: 5px; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
            <i class="fa-brands fa-whatsapp" style="color: #25D366; font-size: 1.5rem;"></i> WhatsApp Gateway Aktif
        </h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Sistem terhubung dan siap mengirimkan notifikasi pelanggaran ke orang tua siswa.
        </p>
    </div>
    
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <!-- Disconnect / Change number -->
        <form action="{{ route('whatsapp.logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-secondary" style="padding: 10px 18px; font-size: 0.88rem;" onclick="return confirm('Apakah Anda yakin ingin memutuskan nomor ini dan memindai nomor lain?')">
                <i class="fa-solid fa-arrows-rotate"></i> Ganti Nomor
            </button>
        </form>

        <!-- Kill process / Turn off -->
        <form action="{{ route('whatsapp.stop') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-danger" style="padding: 10px 18px; font-size: 0.88rem;" onclick="return confirm('Apakah Anda yakin ingin mematikan WhatsApp Gateway?')">
                <i class="fa-solid fa-power-off"></i> Matikan Gateway
            </button>
        </form>
    </div>
</div>
@endif

<!-- Metrics Grid -->
<div class="dashboard-grid">
    <!-- Card 1 -->
    <div class="glass-panel metric-card">
        <div class="metric-icon" style="color: var(--accent-cyan); background: rgba(6, 182, 212, 0.1);">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
        <div class="metric-info">
            <h3>{{ $totalStudents }}</h3>
            <p>{{ $isWaliKelas ? 'Siswa Terdaftar di Kelas' : 'Total Siswa Terdaftar' }}</p>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="glass-panel metric-card">
        <div class="metric-icon" style="color: var(--accent-pink); background: rgba(236, 72, 153, 0.1);">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="metric-info">
            <h3>{{ $todayCases }}</h3>
            <p>Kasus Hari Ini</p>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="glass-panel metric-card">
        <div class="metric-icon" style="color: var(--warning); background: rgba(245, 158, 11, 0.1);">
            <i class="fa-solid fa-circle-radiation"></i>
        </div>
        <div class="metric-info">
            <h3>{{ $criticalStudents }}</h3>
            <p>Siswa Poin Kritis (>= 50)</p>
        </div>
    </div>
</div>

<!-- Charts Grid -->
@if(!$isWaliKelas)
<div class="responsive-grid-2" style="margin-bottom: 40px;">
    <!-- Chart 1: Category Ratio -->
    <div class="glass-panel" style="padding: 25px; border-radius: 20px; display: flex; flex-direction: column;">
        <h4 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
            <i class="fa-solid fa-chart-pie" style="color: var(--accent-cyan);"></i> Rasio Kategori Pelanggaran
        </h4>
        <div style="position: relative; width: 100%; height: 260px;">
            <canvas id="chart-categories"></canvas>
        </div>
    </div>

    <!-- Chart 2: Monthly Trend -->
    <div class="glass-panel" style="padding: 25px; border-radius: 20px; display: flex; flex-direction: column;">
        <h4 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
            <i class="fa-solid fa-chart-line" style="color: var(--accent-pink);"></i> Tren Kasus Bulanan
        </h4>
        <div style="position: relative; width: 100%; height: 260px;">
            <canvas id="chart-monthly"></canvas>
        </div>
    </div>

    <!-- Chart 3: Top Violations -->
    <div class="glass-panel" style="padding: 25px; border-radius: 20px; display: flex; flex-direction: column;">
        <h4 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
            <i class="fa-solid fa-chart-bar" style="color: var(--accent-purple);"></i> Top 5 Jenis Pelanggaran
        </h4>
        <div style="position: relative; width: 100%; height: 260px;">
            <canvas id="chart-violations"></canvas>
        </div>
    </div>

    <!-- Chart 4: Top Classes -->
    <div class="glass-panel" style="padding: 25px; border-radius: 20px; display: flex; flex-direction: column;">
        <h4 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
            <i class="fa-solid fa-ranking-star" style="color: var(--warning);"></i> Top 5 Kelas (Akumulasi Poin)
        </h4>
        <div style="position: relative; width: 100%; height: 260px;">
            <canvas id="chart-classes"></canvas>
        </div>
    </div>
</div>
@else
<div class="responsive-grid-2" style="margin-bottom: 40px;">
    <!-- Chart 1: Category Ratio -->
    <div class="glass-panel" style="padding: 25px; border-radius: 20px; display: flex; flex-direction: column;">
        <h4 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
            <i class="fa-solid fa-chart-pie" style="color: var(--accent-cyan);"></i> Rasio Kategori Pelanggaran Kelas
        </h4>
        <div style="position: relative; width: 100%; height: 260px;">
            <canvas id="chart-categories"></canvas>
        </div>
    </div>

    <!-- Chart 2: Monthly Trend -->
    <div class="glass-panel" style="padding: 25px; border-radius: 20px; display: flex; flex-direction: column;">
        <h4 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
            <i class="fa-solid fa-chart-line" style="color: var(--accent-pink);"></i> Tren Kasus Bulanan Kelas
        </h4>
        <div style="position: relative; width: 100%; height: 260px;">
            <canvas id="chart-monthly"></canvas>
        </div>
    </div>

    <!-- Chart 3: Top Violations -->
    <div class="glass-panel responsive-chart-col" style="padding: 25px; border-radius: 20px; display: flex; flex-direction: column;">
        <h4 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 1.05rem;">
            <i class="fa-solid fa-chart-bar" style="color: var(--accent-purple);"></i> Top 5 Jenis Pelanggaran Kelas
        </h4>
        <div style="position: relative; width: 100%; height: 260px;">
            <canvas id="chart-violations"></canvas>
        </div>
    </div>
</div>
@endif

{{-- ============================= --}}
{{-- TABEL SISWA KELAS (Wali Kelas) --}}
{{-- ============================= --}}
@if($isWaliKelas && $myClass)
<div class="glass-panel" style="padding: 30px; border-radius: 20px; margin-bottom: 30px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <h2 style="font-size: 1.4rem; font-weight: 700; display: flex; align-items: center; gap: 10px; margin: 0;">
            <i class="fa-solid fa-users" style="color: var(--accent-cyan);"></i>
            Daftar Siswa &mdash; {{ $myClass->class_name }}
            <span style="font-size: 0.82rem; font-weight: 500; background: rgba(6,182,212,0.12); color: var(--accent-cyan); padding: 3px 10px; border-radius: 999px; border: 1px solid rgba(6,182,212,0.25);">
                {{ $classStudents->count() }} siswa
            </span>
        </h2>
        <a href="{{ route('violations.create') }}" class="btn-primary" style="font-size: 0.85rem; padding: 9px 18px; height: auto; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; border-radius: 10px;">
            <i class="fa-solid fa-circle-exclamation"></i> Catat Pelanggaran
        </a>
    </div>

    {{-- Legend --}}
    <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; font-size: 0.8rem; color: var(--text-secondary);">
        <span><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981; margin-right: 5px;"></span>Aman (0–24)</span>
        <span><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; margin-right: 5px;"></span>Perhatian (25–49)</span>
        <span><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444; margin-right: 5px;"></span>Kritis (≥ 50)</span>
    </div>

    @if($classStudents->isEmpty())
        <div style="text-align: center; padding: 50px; color: var(--text-muted);">
            <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; display: block; margin-bottom: 12px;"></i>
            Belum ada siswa terdaftar di kelas ini.
        </div>
    @else
        {{-- Search --}}
        <div style="position: relative; margin-bottom: 16px; max-width: 320px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
            <input type="text" id="class-search" placeholder="Cari siswa..." class="form-control" style="padding-left: 36px; padding-top: 9px; padding-bottom: 9px; font-size: 0.88rem;">
        </div>

        <div class="table-container">
            <table class="table-glass" id="class-student-table">
                <thead>
                    <tr>
                        <th style="width: 36px;">#</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Total Poin</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classStudents as $i => $siswa)
                        @php
                            $pts = $siswa->current_points;
                            if ($pts >= 50) {
                                $riskColor = '#ef4444';
                                $riskBg    = 'rgba(239,68,68,0.08)';
                                $riskDot   = '#ef4444';
                            } elseif ($pts >= 25) {
                                $riskColor = '#f59e0b';
                                $riskBg    = 'rgba(245,158,11,0.08)';
                                $riskDot   = '#f59e0b';
                            } else {
                                $riskColor = '#10b981';
                                $riskBg    = 'transparent';
                                $riskDot   = '#10b981';
                            }
                        @endphp
                        <tr data-search="{{ strtolower($siswa->name . ' ' . $siswa->nisn) }}"
                            style="{{ $pts >= 50 ? 'background: rgba(239,68,68,0.04);' : ($pts >= 25 ? 'background: rgba(245,158,11,0.03);' : '') }}">
                            <td style="color: var(--text-muted); font-size: 0.82rem;">{{ $i + 1 }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 9px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $riskDot }}; flex-shrink: 0; box-shadow: 0 0 6px {{ $riskDot }}55;"></span>
                                    <span style="font-weight: 600; color: var(--text-primary);">{{ $siswa->name }}</span>
                                </div>
                            </td>
                            <td style="font-size: 0.88rem; color: var(--text-secondary); font-family: monospace;">{{ $siswa->nisn }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    {{-- Progress bar --}}
                                    <div style="flex: 1; height: 6px; border-radius: 999px; background: rgba(255,255,255,0.08); max-width: 80px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ min(100, $pts) }}%; background: {{ $riskColor }}; border-radius: 999px; transition: width 0.4s ease;"></div>
                                    </div>
                                    <span style="font-weight: 700; color: {{ $riskColor }}; font-size: 0.95rem; min-width: 30px;">{{ $pts }}</span>
                                </div>
                            </td>
                            <td>
                                @if($siswa->status === 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($siswa->status === 'skorsing')
                                    <span class="badge badge-warning">Skorsing</span>
                                @elseif($siswa->status === 'drop_out')
                                    <span class="badge badge-berat">Drop Out</span>
                                @else
                                    <span class="badge" style="background: rgba(156,163,175,0.15); color: var(--text-secondary);">{{ ucfirst($siswa->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                    <a href="{{ route('violations.create') }}?student_id={{ $siswa->id }}"
                                        class="btn-primary"
                                        style="padding: 5px 10px; font-size: 0.78rem; height: auto; border-radius: 7px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
                                        title="Catat pelanggaran untuk {{ $siswa->name }}">
                                        <i class="fa-solid fa-plus"></i> Catat
                                    </a>
                                    <a href="{{ route('students.show', $siswa) }}"
                                        class="btn-secondary"
                                        style="padding: 5px 10px; font-size: 0.78rem; height: auto; border-radius: 7px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
                                        title="Lihat detail {{ $siswa->name }}">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <script>
            document.getElementById('class-search').addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                document.querySelectorAll('#class-student-table tbody tr').forEach(row => {
                    row.style.display = (row.dataset.search || '').includes(q) ? '' : 'none';
                });
            });
        </script>
    @endif
</div>
@endif

{{-- Recent Violations Table --}}
<div class="glass-panel" style="padding: 30px; border-radius: 20px;">
    <h2 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-list-check" style="color: var(--accent-purple);"></i> Riwayat Pelanggaran Terbaru
    </h2>

    <div class="table-container">
        @if($recentLogs->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                <i class="fa-solid fa-clipboard-question" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Belum ada riwayat pelanggaran tercatat.</p>
            </div>
        @else
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Pelanggaran</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Status WA</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->date_occurred)->format('d/m/Y') }}</td>
                            <td style="font-weight: 600;">
                                <a href="{{ route('students.show', $log->student->id) }}" class="student-detail-link" style="color: var(--text-primary); text-decoration: none; border-bottom: 1px dashed var(--text-muted); transition: all 0.2s;" title="Lihat detail riwayat poin">
                                    {{ $log->student->name }}
                                </a>
                            </td>
                            <td>{{ $log->student->kelas->class_name }}</td>
                            <td>{{ $log->violation->violation_name }}</td>
                            <td>
                                <span class="badge badge-{{ $log->violation->category }}">
                                    {{ ucfirst($log->violation->category) }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #ff6b6b;">+{{ $log->points_added }}</td>
                            <td>
                                @php
                                    $waItem = $log->waQueues->first();
                                @endphp
                                @if(!$waItem)
                                    <span class="badge" style="background: rgba(156, 163, 175, 0.15); color: var(--text-secondary); border: 1px solid rgba(156, 163, 175, 0.25);">
                                        <i class="fa-solid fa-hourglass-start"></i> Antre
                                    </span>
                                @elseif($waItem->status === 'sent')
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);">
                                        <i class="fa-solid fa-circle-check"></i> Terkirim
                                    </span>
                                @elseif($waItem->status === 'failed')
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25);" title="{{ $waItem->error_message }}">
                                        <i class="fa-solid fa-circle-xmark"></i> Gagal
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.25);">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Memproses
                                    </span>
                                @endif
                            </td>
                            <td>{{ $log->user->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection

@section('styles')
<style>
    @media (min-width: 768px) {
        .responsive-chart-col {
            grid-column: span 2;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Theme color helper
        const getThemeColors = () => {
            const isLight = document.documentElement.getAttribute('data-theme') === 'light';
            return {
                text: isLight ? '#1e1e38' : '#ffffff',
                textSecondary: isLight ? 'rgba(30, 30, 56, 0.7)' : 'rgba(255, 255, 255, 0.7)',
                gridLine: isLight ? 'rgba(0, 0, 0, 0.05)' : 'rgba(255, 255, 255, 0.05)',
                border: isLight ? 'rgba(30, 30, 56, 0.2)' : 'rgba(255, 255, 255, 0.15)',
            };
        };

        const colors = getThemeColors();

        // 1. Rasio Kategori Pelanggaran (Doughnut Chart)
        const categoriesCtx = document.getElementById('chart-categories');
        let chartCategories = null;
        if (categoriesCtx) {
            chartCategories = new Chart(categoriesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Ringan', 'Sedang', 'Berat'],
                    datasets: [{
                        data: {!! json_encode(array_values($chartCategories)) !!},
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.75)', // emerald
                            'rgba(245, 158, 11, 0.75)', // amber
                            'rgba(239, 68, 68, 0.75)'   // rose
                        ],
                        borderColor: colors.border,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: colors.textSecondary,
                                font: { family: 'Outfit', size: 12 }
                            }
                        }
                    }
                }
            });
        }

        // 2. Tren Kasus Bulanan (Line Chart)
        const monthlyCtx = document.getElementById('chart-monthly');
        let chartMonthly = null;
        if (monthlyCtx) {
            chartMonthly = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartMonths) !!},
                    datasets: [{
                        label: 'Total Kasus',
                        data: {!! json_encode($chartMonthlyCounts) !!},
                        borderColor: '#06b6d4', // accent-cyan
                        backgroundColor: 'rgba(6, 182, 212, 0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#06b6d4',
                        pointBorderColor: colors.border,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            grid: { color: colors.gridLine },
                            ticks: { color: colors.textSecondary, font: { family: 'Outfit' } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.gridLine },
                            ticks: { 
                                stepSize: 1,
                                color: colors.textSecondary, 
                                font: { family: 'Outfit' } 
                            }
                        }
                    }
                }
            });
        }

        // 3. Top 5 Jenis Pelanggaran (Horizontal Bar Chart)
        const violationsCtx = document.getElementById('chart-violations');
        let chartViolations = null;
        if (violationsCtx) {
            const vData = {!! json_encode($topViolations->pluck('count')->toArray()) !!};
            const vLabels = {!! json_encode($topViolations->pluck('violation_name')->toArray()) !!};
            
            // Shorten label names if they are too long
            const truncatedLabels = vLabels.map(label => label.length > 28 ? label.substring(0, 25) + '...' : label);

            chartViolations = new Chart(violationsCtx, {
                type: 'bar',
                data: {
                    labels: truncatedLabels,
                    datasets: [{
                        data: vData,
                        backgroundColor: 'rgba(139, 92, 246, 0.75)', // violet
                        borderColor: colors.border,
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (tooltipItems) => {
                                    return vLabels[tooltipItems[0].dataIndex];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: colors.gridLine },
                            ticks: { 
                                stepSize: 1,
                                color: colors.textSecondary, 
                                font: { family: 'Outfit' } 
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: colors.textSecondary, font: { family: 'Outfit' } }
                        }
                    }
                }
            });
        }

        // 4. Top 5 Kelas Paling Kritis (BK/Admin only)
        const classesCtx = document.getElementById('chart-classes');
        let chartClasses = null;
        if (classesCtx) {
            chartClasses = new Chart(classesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($topClasses ? $topClasses->pluck('class_name')->toArray() : []) !!},
                    datasets: [{
                        data: {!! json_encode($topClasses ? $topClasses->pluck('total_points')->toArray() : []) !!},
                        backgroundColor: 'rgba(236, 72, 153, 0.75)', // pink
                        borderColor: colors.border,
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: colors.textSecondary, font: { family: 'Outfit' } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.gridLine },
                            ticks: { color: colors.textSecondary, font: { family: 'Outfit' } }
                        }
                    }
                }
            });
        }

        // Real-time Theme Sync logic
        const updateChartsTheme = () => {
            const newColors = getThemeColors();
            
            const allCharts = [chartCategories, chartMonthly, chartViolations, chartClasses];
            allCharts.forEach(chart => {
                if (!chart) return;
                
                // Update datasets border color
                if (chart.data.datasets && chart.data.datasets[0]) {
                    chart.data.datasets[0].borderColor = chart.config.type === 'line' ? '#06b6d4' : newColors.border;
                }
                
                // Update legend labels
                if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                    chart.options.plugins.legend.labels.color = newColors.textSecondary;
                }
                
                // Update scales
                if (chart.options.scales) {
                    if (chart.options.scales.x) {
                        chart.options.scales.x.grid.color = newColors.gridLine;
                        chart.options.scales.x.ticks.color = newColors.textSecondary;
                    }
                    if (chart.options.scales.y) {
                        chart.options.scales.y.grid.color = newColors.gridLine;
                        chart.options.scales.y.ticks.color = newColors.textSecondary;
                    }
                }
                
                chart.update();
            });
        };

        // Listen for theme switch button clicks
        document.querySelectorAll('.theme-toggle-btn, #theme-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                setTimeout(updateChartsTheme, 150);
            });
        });
    });
</script>
@endsection
