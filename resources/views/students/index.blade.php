@extends('layouts.app')

@section('title', 'Data Siswa')

@section('styles')
<style>
    /* Modal background overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 16, 33, 0.65);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    /* Modal Content Card */
    .modal-content-card {
        background: var(--card-bg);
        border: var(--card-border);
        backdrop-filter: blur(25px) saturate(200%);
        -webkit-backdrop-filter: blur(25px) saturate(200%);
        border-radius: 24px;
        width: 95%;
        max-width: 520px;
        padding: 35px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        position: relative;
        transform: translateY(-40px);
        transition: transform 0.3s ease;
        color: var(--text-primary);
    }

    .modal-overlay.active .modal-content-card {
        transform: translateY(0);
    }

    :root[data-theme="light"] .modal-overlay {
        background: rgba(243, 244, 246, 0.55);
    }
    
    :root[data-theme="light"] .modal-content-card {
        box-shadow: 0 25px 50px rgba(31, 38, 135, 0.08);
    }

    /* Close Button */
    .modal-close-btn {
        position: absolute;
        top: 25px;
        right: 25px;
        background: rgba(255, 255, 255, 0.06);
        border: var(--card-border);
        color: var(--text-primary);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.2s ease;
    }

    .modal-close-btn:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: scale(1.05);
    }

    :root[data-theme="light"] .modal-close-btn {
        background: rgba(0, 0, 0, 0.04);
    }
    :root[data-theme="light"] .modal-close-btn:hover {
        background: rgba(0, 0, 0, 0.08);
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">Manajemen Siswa</h1>
        <p style="color: var(--text-secondary);">Kelola data siswa, wali murid, dan pantau poin kedisiplinan per tahun ajaran secara real-time</p>
    </div>
</div>

<!-- Main Table Container: Full Width layout -->
<div class="glass-panel" style="padding: 30px; border-radius: 20px; width: 100%; margin-bottom: 40px;">
    
    <!-- Top Bar: Filters and Add Button (Guru BK & Super Admin) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 20px;">
        <!-- Filters Form -->
        <form action="{{ route('students') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; flex: 1;">
            <div style="flex: 1.5; min-width: 180px;">
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari Nama / NISN..." value="{{ $search }}" style="padding: 10px 16px;">
            </div>
            
            <div style="width: 160px;">
                <select name="class_id" id="class_id" class="form-control" style="padding: 10px 16px;">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                            {{ $c->class_name }} ({{ $c->major->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="width: 160px;">
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-control" style="padding: 10px 16px;">
                    <option value="">Semua Thn Ajaran</option>
                    @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>
                            Thn Ajaran {{ $ta }}
                        </option>
                    @endforeach
                    @if(!$tahunAjaranList->contains('2024/2025'))
                        <option value="2024/2025" {{ $tahunAjaran == '2024/2025' ? 'selected' : '' }}>Thn Ajaran 2024/2025</option>
                    @endif
                    @if(!$tahunAjaranList->contains('2025/2026'))
                        <option value="2025/2026" {{ $tahunAjaran == '2025/2026' ? 'selected' : '' }}>Thn Ajaran 2025/2026</option>
                    @endif
                </select>
            </div>

            <button type="submit" class="btn-primary" style="padding: 10px 18px; font-size: 0.95rem;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            
            @if($search || $classId || $tahunAjaran)
                <a href="{{ route('students') }}" class="btn-secondary" style="padding: 10px 18px; font-size: 0.95rem;">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>

        <!-- Action Buttons -->
        @if(auth()->user()->role === 'super_admin')
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-secondary" id="open-bulk-modal" style="padding: 10px 18px; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-graduation-cap"></i> Aksi Masal Kelulusan
                </button>
                <button type="button" class="btn-secondary" id="open-import-modal" style="padding: 10px 18px; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 6px; background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);">
                    <i class="fa-solid fa-file-excel"></i> Import Excel
                </button>
                <button type="button" class="btn-primary" id="open-add-student-modal" style="padding: 10px 22px; font-size: 0.95rem; background: var(--btn-gradient-2); box-shadow: 0 4px 15px rgba(236, 72, 153, 0.35);">
                    <i class="fa-solid fa-user-plus"></i> Tambah Siswa Baru
                </button>
            </div>
        @endif
    </div>

    <!-- Student List Table -->
    <div class="table-container">
        @if($students->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                <i class="fa-solid fa-users-slash" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Tidak ada data siswa ditemukan.</p>
            </div>
        @else
            <table class="table-glass">
                <thead>
                    <tr>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Thn Ajaran</th>
                        <th>Wali Murid</th>
                        <th>No. WhatsApp Ortu</th>
                        <th>Poin Akumulasi</th>
                        <th>Status</th>
                        @if(auth()->user()->role === 'super_admin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td style="font-family: monospace; font-size: 0.95rem;">{{ $student->nisn }}</td>
                            <td style="font-weight: 600;">
                                <a href="{{ route('students.show', $student->id) }}" class="student-detail-link" style="color: var(--text-primary); text-decoration: none; border-bottom: 1px dashed var(--text-muted); transition: all 0.2s;" title="Lihat detail riwayat poin">
                                    {{ $student->name }}
                                </a>
                            </td>
                            <td>{{ $student->kelas->class_name }}</td>
                            <td style="font-weight: 500;">{{ $student->tahun_ajaran }}</td>
                            <td>{{ $student->parent_name }}</td>
                            <td>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->parent_phone) }}" target="_blank" style="color: #25D366; text-decoration: none; font-weight: 500;">
                                    <i class="fa-brands fa-whatsapp"></i> {{ $student->parent_phone }}
                                </a>
                            </td>
                            <td>
                                <span style="font-weight: 700; font-size: 1.1rem; color: {{ $student->current_points >= 50 ? '#ff6b6b' : 'var(--text-primary)' }}">
                                    {{ $student->current_points }}
                                </span>
                            </td>
                            <td>
                                @if($student->status === 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($student->status === 'skorsing')
                                    <span class="badge badge-warning">Skorsing</span>
                                @elseif($student->status === 'lulus')
                                    <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.25);">Lulus</span>
                                @else
                                    <span class="badge badge-danger">Drop Out</span>
                                @endif
                            </td>
                            @if(auth()->user()->role === 'super_admin')
                                <td>
                                    <div style="display: flex; gap: 15px; align-items: center;">
                                        <!-- Edit button link -->
                                        <a href="{{ route('students.edit', $student->id) }}" style="color: var(--accent-cyan); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        
                                        <!-- Delete form button -->
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa {{ $student->name }}? Semua riwayat pelanggaran siswa ini juga akan ikut terhapus secara permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; padding: 0; color: var(--danger); font-family: inherit; font-weight: 600; font-size: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@if(auth()->user()->role === 'super_admin')
<!-- Modal Overlay for Add Student -->
<div class="modal-overlay" id="add-student-modal-overlay">
    <div class="modal-content-card">
        <!-- Close Button -->
        <button type="button" class="modal-close-btn" id="close-add-student-modal" title="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h3 style="font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; font-size: 1.35rem;">
            <i class="fa-solid fa-user-plus" style="color: var(--accent-pink);"></i> Tambah Siswa Baru
        </h3>

        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div class="responsive-grid-2">
                <div class="form-group">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text" name="nisn" id="nisn" class="form-control" placeholder="e.g. 0081234567" required value="{{ old('nisn') }}">
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Nama Siswa</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Aditya Pratama" required value="{{ old('name') }}">
                </div>
            </div>

            <div class="responsive-grid-2">
                <div class="form-group">
                    <label for="modal_class_id" class="form-label">Kelas</label>
                    <select name="class_id" id="modal_class_id" class="form-control" required>
                        <option value="" disabled selected>Pilih Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="modal_tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" id="modal_tahun_ajaran" class="form-control" required>
                        <option value="" disabled selected>Pilih Tahun Ajaran</option>
                        <option value="2023/2024" {{ old('tahun_ajaran') == '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                        <option value="2024/2025" {{ old('tahun_ajaran') == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                        <option value="2025/2026" {{ old('tahun_ajaran') == '2025/2026' ? 'selected' : '' }} selected>2025/2026</option>
                        <option value="2026/2027" {{ old('tahun_ajaran') == '2026/2027' ? 'selected' : '' }}>2026/2027</option>
                        <option value="2027/2028" {{ old('tahun_ajaran') == '2027/2028' ? 'selected' : '' }}>2027/2028</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="parent_name" class="form-label">Nama Orang Tua / Wali</label>
                <input type="text" name="parent_name" id="parent_name" class="form-control" placeholder="e.g. Hartono" required value="{{ old('parent_name') }}">
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="parent_phone" class="form-label">No. WhatsApp Orang Tua</label>
                <input type="text" name="parent_phone" id="parent_phone" class="form-control" placeholder="e.g. 08123456789" required value="{{ old('parent_phone') }}">
                <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 5px;">Format nomor diawali angka 08 (misal: 0812xxx)</small>
            </div>

            <div class="responsive-flex-row">
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data Siswa
                </button>
                <button type="button" class="btn-secondary" id="cancel-add-student-modal" style="width: 120px;">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Overlay for Bulk Actions -->
<div class="modal-overlay" id="bulk-modal-overlay">
    <div class="modal-content-card" style="max-width: 480px;">
        <!-- Close Button -->
        <button type="button" class="modal-close-btn" id="close-bulk-modal" title="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h3 style="font-weight: 700; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; font-size: 1.35rem;">
            <i class="fa-solid fa-graduation-cap" style="color: var(--accent-purple);"></i> Aksi Masal Kelulusan
        </h3>

        <form action="{{ route('students.bulk') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="bulk_tahun_ajaran" class="form-label">Pilih Tahun Ajaran</label>
                <select name="tahun_ajaran" id="bulk_tahun_ajaran" class="form-control" required>
                    <option value="" disabled selected>Pilih Tahun Ajaran</option>
                    @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta }}">Tahun Ajaran {{ $ta }}</option>
                    @endforeach
                    @if(!$tahunAjaranList->contains('2024/2025'))
                        <option value="2024/2025">Tahun Ajaran 2024/2025</option>
                    @endif
                    @if(!$tahunAjaranList->contains('2025/2026'))
                        <option value="2025/2026">Tahun Ajaran 2025/2026</option>
                    @endif
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="bulk_action" class="form-label">Pilih Tindakan</label>
                <select name="action" id="bulk_action" class="form-control" required>
                    <option value="" disabled selected>Pilih Tindakan Masal</option>
                    <option value="lulus">Tandai Semua Siswa Sebagai "Lulus" (Data tetap disimpan)</option>
                    <option value="hapus">Hapus Permanen Semua Siswa & Riwayat Poin (Data terhapus bersih)</option>
                </select>
            </div>

            <div class="responsive-flex-row">
                <button type="submit" class="btn-primary" style="flex: 1;" onclick="return confirm('Apakah Anda yakin ingin memproses tindakan masal ini? Semua siswa pada tahun ajaran yang dipilih akan terkena dampaknya secara langsung.')">
                    <i class="fa-solid fa-circle-check"></i> Proses Sekarang
                </button>
                <button type="button" class="btn-secondary" id="cancel-bulk-modal" style="width: 100px;">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Overlay for Import Excel -->
<div class="modal-overlay" id="import-modal-overlay">
    <div class="modal-content-card" style="max-width: 480px;">
        <!-- Close Button -->
        <button type="button" class="modal-close-btn" id="close-import-modal" title="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h3 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 1.35rem;">
            <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Import Data Siswa
        </h3>

        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px; line-height: 1.5;">
            Unggah file Excel (`.xlsx`, `.xls`) atau CSV (`.csv`) yang berisi data siswa baru atau pembaruan data siswa.
        </p>

        <div style="background: rgba(16, 185, 129, 0.08); border: 1px dashed rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 15px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-file-excel" style="font-size: 1.5rem; color: #10b981;"></i>
                <div>
                    <h5 style="font-weight: 600; margin: 0; font-size: 0.9rem; color: var(--text-primary);">Template Excel</h5>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-secondary);">Unduh format template standar</p>
                </div>
            </div>
            <a href="{{ route('students.template') }}" class="btn-primary" style="padding: 6px 14px; font-size: 0.8rem; background: #10b981; border: none; box-shadow: none; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; border-radius: 8px;">
                <i class="fa-solid fa-download"></i> Unduh
            </a>
        </div>

        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="import_file" class="form-label">Pilih File Excel / CSV</label>
                <input type="file" name="file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required style="padding: 10px 16px;">
                <small style="color: var(--text-secondary); font-size: 0.8rem; display: block; margin-top: 5px;">Maksimal ukuran file: 5 MB</small>
            </div>

            <div class="responsive-flex-row">
                <button type="submit" class="btn-primary" style="flex: 1; background: #10b981; border: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);">
                    <i class="fa-solid fa-upload"></i> Unggah & Proses
                </button>
                <button type="button" class="btn-secondary" id="cancel-import-modal" style="width: 100px;">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Add Student Modal Elements
        const openModalBtn = document.getElementById('open-add-student-modal');
        const closeModalBtn = document.getElementById('close-add-student-modal');
        const cancelModalBtn = document.getElementById('cancel-add-student-modal');
        const modalOverlay = document.getElementById('add-student-modal-overlay');

        // Bulk Modal Elements
        const openBulkBtn = document.getElementById('open-bulk-modal');
        const closeBulkBtn = document.getElementById('close-bulk-modal');
        const cancelBulkBtn = document.getElementById('cancel-bulk-modal');
        const bulkOverlay = document.getElementById('bulk-modal-overlay');

        // Import Modal Elements
        const openImportBtn = document.getElementById('open-import-modal');
        const closeImportBtn = document.getElementById('close-import-modal');
        const cancelImportBtn = document.getElementById('cancel-import-modal');
        const importOverlay = document.getElementById('import-modal-overlay');

        const setupModal = (openBtn, closeBtn, cancelBtn, overlay) => {
            if (openBtn && overlay) {
                openBtn.addEventListener('click', () => {
                    overlay.style.display = 'flex';
                    overlay.offsetHeight;
                    overlay.classList.add('active');
                });

                const closeModal = () => {
                    overlay.classList.remove('active');
                    setTimeout(() => {
                        overlay.style.display = 'none';
                    }, 300);
                };

                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        closeModal();
                    }
                });
            }
        };

        setupModal(openModalBtn, closeModalBtn, cancelModalBtn, modalOverlay);
        setupModal(openBulkBtn, closeBulkBtn, cancelBulkBtn, bulkOverlay);
        setupModal(openImportBtn, closeImportBtn, cancelImportBtn, importOverlay);
    });
</script>
@endsection
