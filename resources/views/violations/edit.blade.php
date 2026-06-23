@extends('layouts.app')

@section('title', 'Edit Jenis Pelanggaran')

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">Edit Jenis Pelanggaran</h1>
        <p style="color: var(--text-secondary);">Perbarui nama, kategori, atau bobot poin jenis pelanggaran ini</p>
    </div>
</div>

<div class="responsive-two-col">
    
    <!-- Edit Form Panel -->
    <div class="glass-panel" style="padding: 40px; border-radius: 20px; grid-column: span 2;">
        <h3 style="font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-pen-to-square" style="color: var(--accent-cyan);"></i> Formulir Edit Pelanggaran
        </h3>

        @if($errors->any())
            <div class="alert-glass" style="padding: 15px; font-size: 0.9rem; margin-bottom: 25px;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('violations.update', $violation->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="violation_name" class="form-label">Nama Pelanggaran</label>
                <input type="text" name="violation_name" id="violation_name" class="form-control" placeholder="e.g. Terlambat masuk sekolah" required value="{{ old('violation_name', $violation->violation_name) }}">
            </div>

            <div class="responsive-grid-2" style="margin-bottom: 25px;">
                <div class="form-group">
                    <label for="category" class="form-label">Kategori</label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="ringan" {{ old('category', $violation->category) === 'ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="sedang" {{ old('category', $violation->category) === 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="berat" {{ old('category', $violation->category) === 'berat' ? 'selected' : '' }}>Berat</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="points" class="form-label">Jumlah Poin</label>
                    <input type="number" name="points" id="points" class="form-control" placeholder="e.g. 5" min="1" required value="{{ old('points', $violation->points) }}">
                </div>
            </div>

            <div class="responsive-flex-row">
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('violations.index') }}" class="btn-secondary" style="width: 150px;">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Box -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px; display: flex; flex-direction: column; gap: 20px;">
        <h4 style="font-weight: 700; color: var(--text-primary); border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px;">
            <i class="fa-solid fa-circle-info" style="color: var(--accent-purple);"></i> Info Pelanggaran
        </h4>
        
        <div style="display: flex; flex-direction: column; gap: 15px; font-size: 0.95rem;">
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-secondary);">Kategori Awal:</span>
                <span style="font-weight: 600;">{{ ucfirst($violation->category) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-secondary);">Poin Awal:</span>
                <span style="font-weight: 600;">{{ $violation->points }}</span>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.03); border: var(--card-border); border-radius: 12px; padding: 15px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
            <i class="fa-solid fa-triangle-exclamation" style="color: var(--warning); margin-right: 4px;"></i>
            Perubahan poin pada jenis pelanggaran ini <strong>tidak akan</strong> mengubah poin pelanggaran siswa yang sudah tercatat sebelumnya. Poin baru hanya berlaku untuk pelanggaran baru yang dicatat setelah perubahan ini disimpan.
        </div>
    </div>
</div>
@endsection
