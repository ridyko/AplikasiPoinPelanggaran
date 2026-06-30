@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi')

@section('content')
<div class="dashboard-header no-print">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">Laporan Rekapitulasi</h1>
        <p style="color: var(--text-secondary);">Saring data kasus pelanggaran siswa berdasarkan periode waktu dan kelas</p>
    </div>
</div>

<!-- KOP Surat only visible during Printing -->
<div class="print-only" style="display: none;">
    <div class="kop-surat">
        <img class="kop-logo" src="{{ asset('images/jakarta-logo.png') }}" alt="Logo DKI Jakarta">
        <div class="kop-text">
            <div class="kop-provinsi">PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA</div>
            <div class="kop-sekolah">SEKOLAH MENENGAH KEJURUSAN NEGERI 2</div>
            <div class="kop-detail">Bidang Keahlian : Bisnis, Manajemen dan Teknik Informatika</div>
            <div class="kop-detail">Jalan Batu No. 3 Gambir, Telp. 3846219, 3520860 Fax 3520860</div>
            <div class="kop-detail">Website:http://www.smkn2jkt.sch.id Email:humas@smkn2jkt.sch.id</div>
            <div class="kop-kota">JAKARTA</div>
            <div class="kop-kodepos">Kode Pos : 10110</div>
        </div>
    </div>
    <div style="text-align: center; margin-bottom: 25px;">
        <h3 style="margin: 0; font-size: 14pt; font-weight: 700; text-transform: uppercase; text-decoration: underline;">
            LAPORAN REKAPITULASI PELANGGARAN SISWA
        </h3>
        <p style="margin: 5px 0 0 0; font-size: 10pt; font-style: italic;">
            Periode: {{ $startDate ? date('d-m-Y', strtotime($startDate)) : 'Awal' }} s/d {{ $endDate ? date('d-m-Y', strtotime($endDate)) : 'Sekarang' }} 
            | Kelas: {{ $classId ? $classes->firstWhere('id', $classId)->class_name ?? 'Semua' : 'Semua Kelas' }}
        </p>
    </div>
</div>

<!-- Filters Panel (Hidden on Print) -->
<div class="glass-panel no-print" style="padding: 30px; border-radius: 20px; margin-bottom: 30px;">
    <h3 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-filter" style="color: var(--accent-cyan);"></i> Filter Laporan
    </h3>

    <form action="{{ route('reports.index') }}" method="GET" id="filter-form">
        <div class="responsive-flex-row" style="align-items: flex-end; gap: 20px; flex-wrap: wrap;">
            
            <!-- Class Filter -->
            <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
                <label for="class_id" class="form-label">Kelas</label>
                <div class="searchable-select" id="searchable-class">
                    <select name="class_id" id="class_id" class="hidden-select" style="display: none;">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->class_name }}</option>
                        @endforeach
                    </select>                    
                    <div class="searchable-select-trigger form-control">
                        <span class="trigger-text">Semua Kelas</span>
                        <i class="fa-solid fa-chevron-down trigger-arrow"></i>
                    </div>
                    
                    <div class="searchable-select-dropdown">
                        <div class="dropdown-search-box">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" class="dropdown-search-input" placeholder="Cari kelas...">
                        </div>
                        <div class="dropdown-options">
                            <div class="dropdown-option-item" data-value="" data-search="semua kelas">
                                <span class="option-title">Semua Kelas</span>
                            </div>
                            @foreach($classes as $c)
                                <div class="dropdown-option-item" data-value="{{ $c->id }}" data-search="{{ strtolower($c->class_name) }}">
                                    <span class="option-title">{{ $c->class_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start Date -->
            <div class="form-group" style="flex: 1; min-width: 180px; margin-bottom: 0;">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>

            <!-- End Date -->
            <div class="form-group" style="flex: 1; min-width: 180px; margin-bottom: 0;">
                <label for="end_date" class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 10px; width: 100%; margin-top: 10px;">
                <button type="submit" class="btn-primary" style="flex: 1; min-height: 48px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Terapkan Filter
                </button>
                <a href="{{ route('reports.index') }}" class="btn-secondary" style="display: flex; align-items: center; justify-content: center; width: 50px; min-height: 48px; padding: 0;" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>

        </div>
    </form>
</div>

<!-- Preview & Export Panel -->
<div class="glass-panel" style="padding: 30px; border-radius: 20px;">
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <h3 style="font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-eye" style="color: var(--accent-pink);"></i> 
            {{ $isFiltered ? 'Hasil Filter Laporan' : 'Pelanggaran Terbaru (Preview)' }}
        </h3>
        
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('reports.export_excel', request()->query()) }}" class="btn-primary" style="background: #10b981; border-color: #10b981; border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-excel"></i> Ekspor Excel
            </a>
            <button onclick="window.print()" class="btn-secondary" style="border-radius: 12px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i> Cetak PDF / Kertas
            </button>
        </div>
    </div>

    <!-- Table content -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 130px; text-align: center;">Tanggal</th>
                    <th style="width: 120px; text-align: center;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 100px; text-align: center;">Kelas</th>
                    <th>Pelanggaran</th>
                    <th style="width: 80px; text-align: center;">Poin</th>
                    <th>Dicatat Oleh</th>
                    <th class="no-print">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ date('d-m-Y', strtotime($log->date_occurred)) }}</td>
                        <td style="text-align: center;">{{ $log->student->nis }}</td>
                        <td style="font-weight: 600;">
                            <a href="{{ route('students.show', $log->student->id) }}" class="student-detail-link no-print" style="color: var(--text-primary); text-decoration: none; border-bottom: 1px dashed var(--text-secondary); transition: all 0.2s;">
                                {{ $log->student->name }}
                            </a>
                            <span class="print-only" style="display: none;">{{ $log->student->name }}</span>
                        </td>
                        <td style="text-align: center;">{{ $log->student->kelas ? $log->student->kelas->class_name : '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $log->violation->category }} no-print" style="font-size: 0.72rem; padding: 2px 6px; margin-right: 6px;">
                                {{ ucfirst($log->violation->category) }}
                            </span>
                            {{ $log->violation->violation_name }}
                        </td>
                        <td style="text-align: center; font-weight: 700; color: #ff6b6b;">+{{ $log->points_added }}</td>
                        <td>{{ $log->user ? $log->user->name : '-' }}</td>
                        <td class="no-print" style="max-width: 200px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $log->description }}">
                            {{ $log->description ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 2rem; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            Tidak ada data pelanggaran ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="print-only" style="display: none; margin-top: 50px; page-break-inside: avoid;">
    <div style="display: flex; justify-content: space-between; font-size: 10pt; color: #000;">
        <div style="text-align: center; width: 200px;">
            <p>Mengetahui,</p>
            <p style="font-weight: 700; margin-top: 50px;">Kepala Sekolah</p>
            <p style="margin-top: 5px;">SMK Negeri 2 Jakarta</p>
        </div>
        <div style="text-align: center; width: 200px;">
            <p>Jakarta, {{ date('d F Y') }}</p>
            <p style="font-weight: 700; margin-top: 50px;">Koordinator Guru BK</p>
            <p style="margin-top: 5px;">SIKAT SMKN 2</p>
        </div>
    </div>
</div>
@endsection
