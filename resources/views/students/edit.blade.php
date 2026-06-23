@extends('layouts.app')

@section('title', 'Edit Siswa - ' . $student->name)

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">Edit Data Siswa</h1>
        <p style="color: var(--text-secondary);">Perbarui data diri siswa, wali murid, nomor kontak WhatsApp, dan status kedisiplinan</p>
    </div>
</div>

<div class="responsive-two-col">
    
    <!-- Left Column: Edit Form -->
    <div class="glass-panel" style="padding: 40px; border-radius: 20px; grid-column: span 2;">
        <h3 style="font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-user-pen" style="color: var(--accent-cyan);"></i> Formulir Edit Siswa
        </h3>

        @if($errors->any())
            <div class="alert-glass" style="padding: 15px; font-size: 0.9rem; margin-bottom: 25px;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('students.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="responsive-grid-2">
                <div class="form-group">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text" name="nisn" id="nisn" class="form-control" placeholder="e.g. 0081234567" required value="{{ old('nisn', $student->nisn) }}">
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Siswa</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Aditya Pratama" required value="{{ old('name', $student->name) }}">
                </div>
            </div>

            <div class="responsive-grid-3">
                <div class="form-group">
                    <label for="class_id" class="form-label">Kelas</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="" disabled>Pilih Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->class_name }} ({{ $c->major->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Status Siswa</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="aktif" {{ old('status', $student->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="skorsing" {{ old('status', $student->status) === 'skorsing' ? 'selected' : '' }}>Skorsing</option>
                        <option value="drop_out" {{ old('status', $student->status) === 'drop_out' ? 'selected' : '' }}>Drop Out</option>
                        <option value="lulus" {{ old('status', $student->status) === 'lulus' ? 'selected' : '' }}>Lulus</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-control" required>
                        @php
                            $years = ['2023/2024', '2024/2025', '2025/2026', '2026/2027', '2027/2028'];
                            $currentYear = old('tahun_ajaran', $student->tahun_ajaran);
                            if (!in_array($currentYear, $years)) {
                                $years[] = $currentYear;
                            }
                        @endphp
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ $currentYear === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="responsive-grid-2" style="margin-bottom: 25px;">
                <div class="form-group">
                    <label for="parent_name" class="form-label">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" id="parent_name" class="form-control" placeholder="e.g. Hartono" required value="{{ old('parent_name', $student->parent_name) }}">
                </div>

                <div class="form-group">
                    <label for="parent_phone" class="form-label">No. WhatsApp Orang Tua</label>
                    <input type="text" name="parent_phone" id="parent_phone" class="form-control" placeholder="e.g. 08123456789" required value="{{ old('parent_phone', $student->parent_phone) }}">
                    <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 5px;">Format nomor diawali angka 08 (misal: 0812xxx)</small>
                </div>
            </div>

            <div class="responsive-flex-row">
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('students') }}" class="btn-secondary" style="width: 150px;">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Right Column: Student Summary Panel -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px; display: flex; flex-direction: column; gap: 25px;">
        <div style="text-align: center; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-blue) 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 2.2rem; color: #fff; box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <h3 style="font-weight: 700; font-size: 1.25rem; color: var(--text-primary);">{{ $student->name }}</h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px;">NISN: {{ $student->nisn }}</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 0.9rem;">Status Kedisiplinan:</span>
                @if($student->status === 'aktif')
                    <span class="badge badge-success">Aktif</span>
                @elseif($student->status === 'skorsing')
                    <span class="badge badge-warning">Skorsing</span>
                @elseif($student->status === 'lulus')
                    <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.25);">Lulus</span>
                @else
                    <span class="badge badge-danger">Drop Out</span>
                @endif
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 0.9rem;">Tahun Ajaran:</span>
                <span style="font-weight: 600; color: var(--text-primary);">{{ $student->tahun_ajaran }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 0.9rem;">Total Poin Pelanggaran:</span>
                <span style="font-weight: 800; font-size: 1.3rem; color: {{ $student->current_points >= 50 ? '#ff6b6b' : 'var(--text-primary)' }}">
                    {{ $student->current_points }}
                </span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 0.9rem;">Kelas Saat Ini:</span>
                <span style="font-weight: 600; color: var(--text-primary);">{{ $student->kelas->class_name }}</span>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.03); border: var(--card-border); border-radius: 12px; padding: 15px; font-size: 0.88rem; color: var(--text-secondary);">
            <h4 style="font-weight: 700; color: var(--text-primary); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-circle-exclamation" style="color: var(--warning);"></i> Perhatian
            </h4>
            Mengubah status siswa menjadi <strong>Skorsing</strong> atau <strong>Drop Out</strong> akan memengaruhi riwayat kedisiplinan dan dapat memicu notifikasi pembaruan ke pihak wali kelas maupun orang tua/wali murid.
        </div>
    </div>
</div>
@endsection
