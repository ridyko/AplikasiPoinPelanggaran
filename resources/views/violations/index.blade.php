@extends('layouts.app')

@section('title', 'Kelola Jenis Pelanggaran')

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">Jenis Pelanggaran</h1>
        <p style="color: var(--text-secondary);">Kelola daftar jenis pelanggaran, kategori bobot (ringan/sedang/berat), dan poin pelanggaran siswa</p>
    </div>
</div>

<div class="responsive-two-col">
    
    <!-- Left Column: Violation List -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px; grid-column: span 2;">
        <h3 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-list-check" style="color: var(--accent-purple);"></i> Daftar Jenis Pelanggaran
        </h3>

        <div class="table-container">
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Nama Pelanggaran</th>
                        <th>Poin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($violations as $violation)
                        <tr>
                            <td>
                                @if($violation->category === 'ringan')
                                    <span class="badge badge-success">Ringan</span>
                                @elseif($violation->category === 'sedang')
                                    <span class="badge badge-warning">Sedang</span>
                                @else
                                    <span class="badge badge-danger">Berat</span>
                                @endif
                            </td>
                            <td style="font-weight: 600;">{{ $violation->violation_name }}</td>
                            <td>
                                <span style="font-weight: 700; font-size: 1.1rem;">
                                    {{ $violation->points }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 15px; align-items: center;">
                                    <a href="{{ route('violations.edit', $violation->id) }}" style="color: var(--accent-cyan); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    
                                    <form action="{{ route('violations.destroy', $violation->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis pelanggaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; padding: 0; color: var(--danger); font-family: inherit; font-weight: 600; font-size: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Add Violation Form -->
    <div class="glass-panel" style="padding: 30px; border-radius: 20px;">
        <h3 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-plus" style="color: var(--accent-cyan);"></i> Tambah Jenis Pelanggaran
        </h3>

        @if($errors->any())
            <div class="alert-glass" style="padding: 10px; font-size: 0.85rem; margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('violations.store_type') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="violation_name" class="form-label">Nama Pelanggaran</label>
                <input type="text" name="violation_name" id="violation_name" class="form-control" placeholder="e.g. Terlambat masuk sekolah" required value="{{ old('violation_name') }}">
            </div>

            <div class="form-group">
                <label for="category" class="form-label">Kategori</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="ringan" {{ old('category') === 'ringan' ? 'selected' : '' }}>Ringan</option>
                    <option value="sedang" {{ old('category') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="berat" {{ old('category') === 'berat' ? 'selected' : '' }}>Berat</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="points" class="form-label">Jumlah Poin</label>
                <input type="number" name="points" id="points" class="form-control" placeholder="e.g. 5" min="1" required value="{{ old('points') }}">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pelanggaran
            </button>
        </form>
    </div>
</div>
@endsection
