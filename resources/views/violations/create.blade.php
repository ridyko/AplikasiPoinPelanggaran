@extends('layouts.app')

@section('title', 'Catat Pelanggaran')

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">Catat Pelanggaran</h1>
        <p style="color: var(--text-secondary);">Rekam kejadian pelanggaran siswa dan kirim notifikasi WhatsApp instan ke orang tua</p>
    </div>
</div>

<div class="responsive-two-col">
    
    <!-- Left Column: Form -->
    <div class="glass-panel" style="padding: 40px; border-radius: 20px; grid-column: span 2;">
        <h3 style="font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-plus" style="color: var(--accent-pink);"></i> Formulir Laporan Pelanggaran
        </h3>

        <form action="{{ route('violations.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="student_id" class="form-label">Siswa Pelanggar</label>
                <div class="searchable-select" id="searchable-student">
                    <select name="student_id" id="student_id" class="hidden-select" required style="display: none;">
                        <option value="" disabled selected>Pilih Siswa</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ old('student_id', $selectedStudentId) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} (NIS: {{ $s->nis }}) - Kelas: {{ $s->kelas->class_name }}
                            </option>
                        @endforeach
                    </select>                    
                    <div class="searchable-select-trigger form-control">
                        <span class="trigger-text">Pilih Siswa</span>
                        <i class="fa-solid fa-chevron-down trigger-arrow"></i>
                    </div>
                    
                    <div class="searchable-select-dropdown">
                        <div class="dropdown-search-box">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" class="dropdown-search-input" placeholder="Cari nama, NIS, atau kelas...">
                        </div>
                        <div class="dropdown-options">
                            @foreach($students as $s)
                                <div class="dropdown-option-item" data-value="{{ $s->id }}" data-search="{{ strtolower($s->name . ' ' . $s->nis . ' ' . $s->kelas->class_name) }}">
                                    <span class="option-title">{{ $s->name }}</span>
                                    <span class="option-subtitle">NIS: {{ $s->nis }} • Kelas: {{ $s->kelas->class_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="violation_id" class="form-label">Jenis Pelanggaran</label>
                <div class="searchable-select" id="searchable-violation">
                    <select name="violation_id" id="violation_id" class="hidden-select" required style="display: none;">
                        <option value="" disabled selected>Pilih Pelanggaran</option>
                        @foreach($violations as $v)
                            <option value="{{ $v->id }}" {{ old('violation_id') == $v->id ? 'selected' : '' }}>
                                [{{ ucfirst($v->category) }}] {{ $v->violation_name }} (+{{ $v->points }} Poin)
                            </option>
                        @endforeach
                    </select>
                    
                    <div class="searchable-select-trigger form-control">
                        <span class="trigger-text">Pilih Pelanggaran</span>
                        <i class="fa-solid fa-chevron-down trigger-arrow"></i>
                    </div>
                    
                    <div class="searchable-select-dropdown">
                        <div class="dropdown-search-box">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" class="dropdown-search-input" placeholder="Cari jenis pelanggaran...">
                        </div>
                        <div class="dropdown-options">
                            @foreach($violations as $v)
                                <div class="dropdown-option-item" data-value="{{ $v->id }}" data-search="{{ strtolower($v->violation_name . ' ' . $v->category) }}">
                                    <span class="option-title">
                                        <span class="badge badge-{{ $v->category }}" style="font-size: 0.72rem; padding: 2px 6px; margin-right: 6px;">
                                            {{ ucfirst($v->category) }}
                                        </span>
                                        {{ $v->violation_name }}
                                    </span>
                                    <span class="option-subtitle" style="color: #ff6b6b; font-weight: 600;">+{{ $v->points }} Poin</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="date_occurred" class="form-label">Tanggal Kejadian</label>
                <input type="date" name="date_occurred" id="date_occurred" class="form-control" value="{{ old('date_occurred', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="description" class="form-label">Keterangan Tambahan / Detail Kejadian</label>
                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Tuliskan kronologi singkat kejadian (opsional)..." style="resize: vertical;">{{ old('description') }}</textarea>
            </div>

            <div class="responsive-flex-row">
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-paper-plane"></i> Simpan & Kirim WhatsApp
                </button>
                <a href="{{ route('dashboard') }}" class="btn-secondary" style="width: 150px;">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Right Column: Violations Reference Guide -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px;">
        <h3 style="font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-size: 1.15rem;">
            <i class="fa-solid fa-circle-info" style="color: var(--warning);"></i> Info Ambang Batas Poin
        </h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 15px;">
            Sanksi dan tindakan lanjut bagi siswa SMKN 2 Jakarta yang melakukan pelanggaran berdasarkan akumulasi poin:
        </p>

        <ul style="list-style: none; padding-left: 0; display: flex; flex-direction: column; gap: 12px; font-size: 0.88rem;">
            <li style="padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span class="badge badge-ringan" style="font-size: 0.75rem; padding: 2px 8px;">10 - 25 Poin</span>
                <p style="margin-top: 5px; color: var(--text-secondary);">Peringatan Lisan oleh Wali Kelas.</p>
            </li>
            <li style="padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span class="badge badge-sedang" style="font-size: 0.75rem; padding: 2px 8px;">26 - 50 Poin</span>
                <p style="margin-top: 5px; color: var(--text-secondary);">Peringatan Tertulis 1 (SP 1) & Pemanggilan Orang Tua.</p>
            </li>
            <li style="padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span class="badge badge-berat" style="font-size: 0.75rem; padding: 2px 8px;">51 - 75 Poin</span>
                <p style="margin-top: 5px; color: var(--text-secondary);">SP 2 & Skorsing 3 Hari.</p>
            </li>
            <li style="padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span class="badge badge-berat" style="font-size: 0.75rem; padding: 2px 8px;">76 - 99 Poin</span>
                <p style="margin-top: 5px; color: var(--text-secondary);">SP 3 & Skorsing 1 Minggu.</p>
            </li>
            <li>
                <span class="badge badge-berat" style="font-size: 0.75rem; padding: 2px 8px; background: rgba(239,68,68,0.4);">≥ 100 Poin</span>
                <p style="margin-top: 5px; color: #ff6b6b; font-weight: 600;">Siswa dikembalikan kepada orang tua (Drop Out).</p>
            </li>
        </ul>
    </div>
</div>
@endsection
