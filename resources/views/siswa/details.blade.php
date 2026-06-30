@extends('layouts.app')

@section('title', 'Detail Poin Siswa')

@section('content')
<div style="margin-bottom: 30px;">
    <a href="{{ route('siswa.check') }}" class="btn-secondary" style="padding: 8px 16px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Pencarian
    </a>
</div>

<!-- Main Grid: Profile & Point Summary -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 40px; align-items: start;">
    
    <!-- Profile Card -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px;">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-purple) 0%, var(--accent-cyan) 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #fff;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <h2 style="font-weight: 700; font-size: 1.4rem;">{{ $student->name }}</h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">NIS: {{ $student->nis }}</p>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <span style="font-size: 0.85rem; color: var(--text-muted); display: block;">Kelas & Jurusan</span>
                <span style="font-weight: 600; font-size: 1.1rem; color: var(--text-primary);">{{ $student->kelas->class_name }} ({{ $student->kelas->major->name }})</span>
            </div>
            <div>
                <span style="font-size: 0.85rem; color: var(--text-muted); display: block;">Orang Tua / Wali</span>
                <span style="font-weight: 600; font-size: 1.05rem; color: var(--text-primary);">{{ $student->parent_name }}</span>
            </div>
            <div>
                <span style="font-size: 0.85rem; color: var(--text-muted); display: block;">No. WhatsApp Orang Tua</span>
                <span style="font-weight: 600; font-size: 1.05rem; color: #25D366;">
                    <i class="fa-brands fa-whatsapp"></i> {{ $student->parent_phone }}
                </span>
            </div>
            <div>
                <span style="font-size: 0.85rem; color: var(--text-muted); display: block;">Status Akademik</span>
                @if($student->status === 'aktif')
                    <span class="badge badge-success" style="font-size: 0.9rem; padding: 4px 12px;">Aktif</span>
                @elseif($student->status === 'skorsing')
                    <span class="badge badge-warning" style="font-size: 0.9rem; padding: 4px 12px;">Skorsing</span>
                @else
                    <span class="badge badge-danger" style="font-size: 0.9rem; padding: 4px 12px;">Dikeluarkan / DO</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Points Summary Card -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 320px;">
        <span style="font-size: 1rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 15px;">AKUMULASI POIN PELANGGARAN</span>
        
        <!-- Points Glow Circle -->
        @php
            $pts = $student->current_points;
            $glowColor = 'rgba(16, 185, 129, 0.4)'; // green
            $numColor = '#34d399';
            if ($pts >= 100) {
                $glowColor = 'rgba(239, 68, 68, 0.6)'; // red
                $numColor = '#ef4444';
            } elseif ($pts >= 50) {
                $glowColor = 'rgba(239, 68, 68, 0.4)';
                $numColor = '#f87171';
            } elseif ($pts >= 26) {
                $glowColor = 'rgba(245, 158, 11, 0.4)'; // orange
                $numColor = '#fbbf24';
            } elseif ($pts >= 10) {
                $glowColor = 'rgba(245, 158, 11, 0.2)'; // yellow
                $numColor = '#fcd34d';
            }
        @endphp
        
        <div style="width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,0.03); border: 4px solid {{ $numColor }}; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; font-weight: 800; color: {{ $numColor }}; box-shadow: 0 0 35px {{ $glowColor }}; margin-bottom: 20px;">
            {{ $pts }}
        </div>

        <!-- Automated Alert Status Text -->
        <div style="width: 100%; padding: 12px; border-radius: 12px; font-weight: 600; font-size: 0.95rem; 
            @if($pts >= 100)
                background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171;
            @elseif($pts >= 50)
                background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.2); color: #f87171;
            @elseif($pts >= 26)
                background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.3); color: #fbbf24;
            @elseif($pts >= 10)
                background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2); color: #fcd34d;
            @else
                background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34d399;
            @endif
        ">
            @if($pts >= 100)
                STATUS: DIKEMBALIKAN KEPADA ORANG TUA (DROP OUT)
            @elseif($pts >= 76)
                STATUS: TINGKAT CRITICAL (SP 3 & SKORSING 1 MINGGU)
            @elseif($pts >= 51)
                STATUS: TINGKAT BERAT (SP 2 & SKORSING 3 HARI)
            @elseif($pts >= 26)
                STATUS: TINGKAT SEDANG (SP 1 & PANGGIL ORANG TUA)
            @elseif($pts >= 10)
                STATUS: TEGURAN LISAN (OLEH WALI KELAS)
            @else
                STATUS: SANGAT BAIK / DISIPLIN
            @endif
        </div>
    </div>
</div>

<!-- History of Violations Table -->
<div class="glass-panel" style="padding: 30px; border-radius: 20px;">
    <h3 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 1.3rem;">
        <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-purple);"></i> Buku Catatan Pelanggaran Siswa
    </h3>

    <div class="table-container">
        @if($student->violationLogs->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                <i class="fa-solid fa-circle-check" style="font-size: 3rem; margin-bottom: 15px; color: #34d399; opacity: 0.6;"></i>
                <p style="font-size: 1.1rem; font-weight: 600; color: var(--text-primary);">Siswa ini bersih dari pelanggaran!</p>
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 5px;">Terus pertahankan disiplin dan menjadi teladan bagi siswa lain.</p>
            </div>
        @else
            <table class="table-glass">
                <thead>
                    <tr>
                        <th style="width: 120px;">Tanggal</th>
                        <th>Pelanggaran</th>
                        <th>Kategori</th>
                        <th>Poin</th>
                        <th>Keterangan Kejadian</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->violationLogs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->date_occurred)->format('d/m/Y') }}</td>
                            <td style="font-weight: 600;">{{ $log->violation->violation_name }}</td>
                            <td>
                                <span class="badge badge-{{ $log->violation->category }}">
                                    {{ ucfirst($log->violation->category) }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #ff6b6b;">+{{ $log->points_added }}</td>
                            <td style="font-size: 0.9rem; color: var(--text-secondary);">
                                {{ $log->description ?: '-' }}
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
