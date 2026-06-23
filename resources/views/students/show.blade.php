@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $student->name)

@section('content')
<div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <a href="{{ route('students') }}" class="btn-secondary" style="padding: 10px 18px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Siswa
    </a>

    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        @if($student->status === 'aktif' || $student->status === 'skorsing')
            <a href="{{ route('violations.create', ['student_id' => $student->id]) }}" class="btn-primary" style="padding: 10px 18px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; background: var(--btn-gradient-2); box-shadow: 0 4px 15px rgba(236, 72, 153, 0.25);">
                <i class="fa-solid fa-circle-exclamation"></i> Catat Pelanggaran Baru
            </a>
        @endif

        <a href="{{ route('reports.print_card', $student->id) }}" target="_blank" class="btn-secondary" style="padding: 10px 18px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fa-solid fa-print"></i> Cetak Kartu Poin
        </a>

        @if($student->current_points >= 50)
            <div class="dropdown-print-sp" style="position: relative; display: inline-block;">
                <button id="btn-sp-dropdown" class="btn-secondary" style="padding: 10px 18px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; background: rgba(245, 158, 11, 0.12); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2);">
                    <i class="fa-solid fa-envelope-open-text"></i> Cetak Surat SP <i class="fa-solid fa-chevron-down" style="font-size: 0.8rem;"></i>
                </button>
                <div id="sp-dropdown-menu" class="glass-panel" style="display: none; position: absolute; right: 0; top: calc(100% + 5px); width: 200px; z-index: 100; padding: 6px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: var(--card-border); background: #15162d;">
                    <a href="{{ route('reports.print_sp', [$student->id, 'sp' => 1]) }}" target="_blank" style="display: block; padding: 10px 14px; color: var(--text-primary); text-decoration: none; border-radius: 8px; font-size: 0.88rem; transition: background 0.2s;" onmouseover="this.style.background='var(--card-bg-hover)'" onmouseout="this.style.background='transparent'">
                        Cetak Surat SP 1
                    </a>
                    <a href="{{ route('reports.print_sp', [$student->id, 'sp' => 2]) }}" target="_blank" style="display: block; padding: 10px 14px; color: var(--text-primary); text-decoration: none; border-radius: 8px; font-size: 0.88rem; transition: background 0.2s;" onmouseover="this.style.background='var(--card-bg-hover)'" onmouseout="this.style.background='transparent'">
                        Cetak Surat SP 2
                    </a>
                    <a href="{{ route('reports.print_sp', [$student->id, 'sp' => 3]) }}" target="_blank" style="display: block; padding: 10px 14px; color: var(--text-primary); text-decoration: none; border-radius: 8px; font-size: 0.88rem; transition: background 0.2s;" onmouseover="this.style.background='var(--card-bg-hover)'" onmouseout="this.style.background='transparent'">
                        Cetak Surat SP 3 (DO)
                    </a>
                </div>
            </div>
        @endif

        @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('students.edit', $student->id) }}" class="btn-secondary" style="padding: 10px 18px; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; background: rgba(6, 182, 212, 0.12); color: #06b6d4; border: 1px solid rgba(6, 182, 212, 0.2);">
                <i class="fa-solid fa-user-gear"></i> Edit Data Siswa
            </a>
        @endif
    </div>
</div>

<!-- Main Grid: Profile & Point Summary -->
<div class="responsive-two-col" style="margin-bottom: 40px;">
    
    <!-- Profile Card -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px;">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-purple) 0%, var(--accent-cyan) 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #fff;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <h2 style="font-weight: 700; font-size: 1.4rem;">{{ $student->name }}</h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">NISN: {{ $student->nisn }}</p>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div class="responsive-grid-2" style="gap: 15px; margin-bottom: 0;">
                <div>
                    <span style="font-size: 0.82rem; color: var(--text-muted); display: block; margin-bottom: 2px;">Kelas</span>
                    <span style="font-weight: 600; font-size: 1.05rem; color: var(--text-primary);">{{ $student->kelas->class_name }}</span>
                </div>
                <div>
                    <span style="font-size: 0.82rem; color: var(--text-muted); display: block; margin-bottom: 2px;">Jurusan</span>
                    <span style="font-weight: 600; font-size: 1.05rem; color: var(--text-primary);">{{ $student->kelas->major->name }}</span>
                </div>
            </div>
            
            <div class="responsive-grid-2" style="gap: 15px; margin-bottom: 0;">
                <div>
                    <span style="font-size: 0.82rem; color: var(--text-muted); display: block; margin-bottom: 2px;">Orang Tua / Wali</span>
                    <span style="font-weight: 600; font-size: 1.05rem; color: var(--text-primary);">{{ $student->parent_name }}</span>
                </div>
                <div>
                    <span style="font-size: 0.82rem; color: var(--text-muted); display: block; margin-bottom: 2px;">No. WhatsApp Orang Tua</span>
                    <span style="font-weight: 600; font-size: 1.05rem;">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->parent_phone) }}" target="_blank" style="color: #25D366; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-brands fa-whatsapp"></i> {{ $student->parent_phone }}
                        </a>
                    </span>
                </div>
            </div>

            <div class="responsive-grid-2" style="gap: 15px; margin-bottom: 0;">
                <div>
                    <span style="font-size: 0.82rem; color: var(--text-muted); display: block; margin-bottom: 2px;">Tahun Ajaran</span>
                    <span style="font-weight: 600; font-size: 1.05rem; color: var(--text-primary);">{{ $student->tahun_ajaran }}</span>
                </div>
                <div>
                    <span style="font-size: 0.82rem; color: var(--text-muted); display: block; margin-bottom: 2px;">Status Keaktifan</span>
                    @if($student->status === 'aktif')
                        <span class="badge badge-success" style="font-size: 0.8rem; padding: 3px 10px;">Aktif</span>
                    @elseif($student->status === 'skorsing')
                        <span class="badge badge-warning" style="font-size: 0.8rem; padding: 3px 10px;">Skorsing</span>
                    @elseif($student->status === 'lulus')
                        <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.25); font-size: 0.8rem; padding: 3px 10px;">Lulus</span>
                    @else
                        <span class="badge badge-danger" style="font-size: 0.8rem; padding: 3px 10px;">Drop Out</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Points Summary Card -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 250px;">
        <span style="font-size: 0.9rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 12px; letter-spacing: 0.5px;">AKUMULASI POIN KEDISIPLINAN</span>
        
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
        
        <div style="width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,0.03); border: 4px solid {{ $numColor }}; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 800; color: {{ $numColor }}; box-shadow: 0 0 25px {{ $glowColor }}; margin-bottom: 15px;">
            {{ $pts }}
        </div>

        <!-- Automated Alert Status Text -->
        <div style="width: 100%; padding: 10px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; 
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
                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 5px;">Siswa belum pernah tercatat melakukan tindakan pelanggaran tata tertib.</p>
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
                        <th>Dicatat Oleh (Nama Guru)</th>
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
                            <td style="font-weight: 500;">
                                <i class="fa-solid fa-user-tie" style="opacity: 0.7; margin-right: 4px;"></i> {{ $log->user->name }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropdownBtn = document.getElementById('btn-sp-dropdown');
    const dropdownMenu = document.getElementById('sp-dropdown-menu');
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdownMenu.style.display === 'block';
            dropdownMenu.style.display = isOpen ? 'none' : 'block';
        });
        document.addEventListener('click', () => {
            dropdownMenu.style.display = 'none';
        });
    }
});
</script>
@endsection
