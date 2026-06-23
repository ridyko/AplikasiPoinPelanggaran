@extends('layouts.app')

@section('title', 'Kelola Kelas')

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">
            <i class="fa-solid fa-school" style="color: var(--accent-cyan);"></i> Manajemen Kelas
        </h1>
        <p style="color: var(--text-secondary); margin-top: 4px;">Kelola data kelas, jurusan, dan penugasan wali kelas</p>
    </div>
    <button class="btn-primary" id="btn-add-kelas" onclick="openAddModal()">
        <i class="fa-solid fa-plus"></i> Tambah Kelas
    </button>
</div>

{{-- Stats Row --}}
<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
    @php
        $totalKelas = $kelas->count();
        $totalJurusan = $majors->count();
        $tanpaWali = $kelas->where('homeroom_teacher_id', null)->count();
        $totalSiswa = $kelas->sum('students_count');
    @endphp
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-cyan);">
        <div class="metric-icon" style="background: rgba(6,182,212,0.15); color: var(--accent-cyan);">
            <i class="fa-solid fa-school"></i>
        </div>
        <div>
            <div class="metric-value">{{ $totalKelas }}</div>
            <div class="metric-label">Total Kelas</div>
        </div>
    </div>
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-purple);">
        <div class="metric-icon" style="background: rgba(139,92,246,0.15); color: var(--accent-purple);">
            <i class="fa-solid fa-shapes"></i>
        </div>
        <div>
            <div class="metric-value">{{ $totalJurusan }}</div>
            <div class="metric-label">Total Jurusan</div>
        </div>
    </div>
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-orange);">
        <div class="metric-icon" style="background: rgba(245,158,11,0.15); color: var(--accent-orange);">
            <i class="fa-solid fa-user-slash"></i>
        </div>
        <div>
            <div class="metric-value">{{ $tanpaWali }}</div>
            <div class="metric-label">Kelas Tanpa Wali</div>
        </div>
    </div>
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-green);">
        <div class="metric-icon" style="background: rgba(16,185,129,0.15); color: var(--accent-green);">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div>
            <div class="metric-value">{{ $totalSiswa }}</div>
            <div class="metric-label">Total Siswa Terploting</div>
        </div>
    </div>
</div>

{{-- Kelas Table --}}
<div class="glass-panel" style="padding: 30px; border-radius: 20px;">
    <h3 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-list" style="color: var(--accent-cyan);"></i> Daftar Kelas Aktif
        <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-secondary); margin-left: auto;">Total: {{ $totalKelas }} kelas</span>
    </h3>

    {{-- Search --}}
    <div style="position: relative; margin-bottom: 20px; max-width: 360px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        <input type="text" id="search-kelas" placeholder="Cari kelas, jurusan, wali kelas..." class="form-control" style="padding-left: 40px;">
    </div>

    <div class="table-container">
        <table class="table-glass" id="kelas-table">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Nama Kelas</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th style="text-align: center; width: 150px;">Jumlah Siswa</th>
                    <th style="text-align: center; width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $i => $item)
                    @php
                        $searchData = strtolower(implode(' ', [
                            $item->class_name,
                            $item->major ? ($item->major->code . ' ' . $item->major->name) : '',
                            $item->homeroomTeacher ? $item->homeroomTeacher->name : 'belum ditentukan'
                        ]));
                    @endphp
                    <tr data-search="{{ $searchData }}">
                        <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $i + 1 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(6,182,212,0.15); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; color: var(--accent-cyan); flex-shrink: 0;">
                                    <i class="fa-solid fa-school"></i>
                                </div>
                                <span style="font-weight: 700; color: var(--text-primary);">{{ $item->class_name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($item->major)
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: var(--text-primary);">{{ $item->major->code }}</span>
                                    <span style="font-size: 0.78rem; color: var(--text-secondary);">{{ $item->major->name }}</span>
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">Tidak ada jurusan</span>
                            @endif
                        </td>
                        <td>
                            @if($item->homeroomTeacher)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: var(--accent-green);">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <span style="font-weight: 500; color: var(--text-primary);">{{ $item->homeroomTeacher->name }}</span>
                                </div>
                            @else
                                <span class="badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); font-size: 0.78rem; padding: 2px 8px;">
                                    <i class="fa-solid fa-circle-question"></i> Belum ditentukan
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge" style="background: rgba(139,92,246,0.15); color: #a78bfa; border: 1px solid rgba(139,92,246,0.3); font-weight: 700; min-width: 40px; display: inline-block;">
                                {{ $item->students_count }} siswa
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                {{-- Edit Button --}}
                                <button class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; height: auto; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px;"
                                    onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->class_name) }}', {{ $item->major_id }}, {{ $item->homeroom_teacher_id ?? 'null' }})">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                {{-- Delete Button --}}
                                <button class="btn-danger" style="padding: 6px 12px; font-size: 0.8rem; height: auto; border-radius: 8px;"
                                    onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->class_name) }}', {{ $item->students_count }})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-school-circle-exclamation" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            Belum ada data kelas terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: Add New Kelas          --}}
{{-- ============================= --}}
<div id="modal-add" class="modal-overlay" style="display: none;" onclick="if(event.target===this) closeAddModal()">
    <div class="glass-panel" style="max-width: 500px; width: 95%; padding: 36px; border-radius: 24px; position: relative;">
        <button onclick="closeAddModal()" style="position: absolute; top: 16px; right: 18px; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.3rem; line-height: 1;">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 6px;">
            <i class="fa-solid fa-plus-circle" style="color: var(--accent-cyan);"></i> Tambah Kelas Baru
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.88rem; margin-bottom: 24px;">Buat kelas baru dan tentukan wali kelas serta jurusannya.</p>

        @if($errors->any())
            <div class="alert-glass" style="padding: 12px 16px; font-size: 0.85rem; margin-bottom: 18px;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('kelas.store') }}" method="POST" id="form-add-kelas">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kelas</label>
                <input type="text" name="class_name" class="form-control" placeholder="Contoh: XII RPL 1" required value="{{ old('class_name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Jurusan</label>
                <select name="major_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Jurusan --</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}" {{ old('major_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->code }} — {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Wali Kelas <span style="color: var(--text-secondary); font-weight: normal; font-size: 0.8rem;">(Opsional)</span></label>
                <select name="homeroom_teacher_id" class="form-control">
                    <option value="">-- Tanpa Wali Kelas --</option>
                    @foreach($waliKandidates as $w)
                        <option value="{{ $w->id }}" {{ old('homeroom_teacher_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Kelas
            </button>
        </form>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: Edit Kelas             --}}
{{-- ============================= --}}
<div id="modal-edit" class="modal-overlay" style="display: none;" onclick="if(event.target===this) closeEditModal()">
    <div class="glass-panel" style="max-width: 500px; width: 95%; padding: 36px; border-radius: 24px; position: relative;">
        <button onclick="closeEditModal()" style="position: absolute; top: 16px; right: 18px; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.3rem; line-height: 1;">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 6px;">
            <i class="fa-solid fa-pen-to-square" style="color: var(--accent-purple);"></i> Edit Data Kelas
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.88rem; margin-bottom: 24px;">Perbarui nama kelas, jurusan, atau wali kelas.</p>

        <form id="form-edit" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Kelas</label>
                <input type="text" name="class_name" id="edit-class-name" class="form-control" placeholder="Contoh: XII RPL 1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jurusan</label>
                <select name="major_id" id="edit-major-id" class="form-control" required>
                    <option value="" disabled>-- Pilih Jurusan --</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->code }} — {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Wali Kelas <span style="color: var(--text-secondary); font-weight: normal; font-size: 0.8rem;">(Opsional)</span></label>
                <select name="homeroom_teacher_id" id="edit-teacher-id" class="form-control">
                    <option value="">-- Tanpa Wali Kelas --</option>
                    @foreach($waliKandidates as $w)
                        <option value="{{ $w->id }}">
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="closeEditModal()" class="btn-secondary" style="flex: 1;">
                    <i class="fa-solid fa-ban"></i> Batal
                </button>
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: Delete Confirm         --}}
{{-- ============================= --}}
<div id="modal-delete" class="modal-overlay" style="display: none;" onclick="if(event.target===this) closeDeleteModal()">
    <div class="glass-panel" style="max-width: 440px; width: 95%; padding: 36px; border-radius: 24px; position: relative; text-align: center;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <i class="fa-solid fa-trash-can" style="font-size: 1.8rem; color: #ef4444;"></i>
        </div>
        <h2 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 8px;">Hapus Kelas?</h2>
        
        <div id="delete-warning-has-students" style="display: none; margin-bottom: 20px;">
            <div class="alert-glass" style="padding: 12px 16px; font-size: 0.85rem; text-align: left;">
                <i class="fa-solid fa-triangle-exclamation" style="color: var(--accent-orange);"></i> 
                <strong>Peringatan!</strong> Kelas <span id="delete-class-name-warn" style="font-weight: 700;"></span> masih memiliki <strong id="delete-student-count"></strong> siswa terdaftar. 
                Sesuai aturan keamanan data, Anda <strong>tidak diperbolehkan</strong> menghapus kelas yang berisi siswa.
            </div>
        </div>

        <div id="delete-confirm-allow" style="display: block; margin-bottom: 20px;">
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Anda yakin ingin menghapus kelas: <strong id="delete-class-name" style="color: var(--text-primary);"></strong>?<br>
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeDeleteModal()" class="btn-secondary" style="flex: 1; max-width: 150px;">
                <i class="fa-solid fa-ban"></i> Batal
            </button>
            <form id="form-delete" method="POST" style="flex: 1; max-width: 150px;">
                @csrf
                @method('DELETE')
                <button type="submit" id="btn-confirm-delete" class="btn-danger" style="width: 100%;">
                    <i class="fa-solid fa-trash-can"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // -------- Modal Add Kelas --------
    function openAddModal() {
        document.getElementById('modal-add').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeAddModal() {
        document.getElementById('modal-add').style.display = 'none';
        document.body.style.overflow = '';
    }

    // -------- Modal Edit Kelas --------
    function openEditModal(kelasId, className, majorId, teacherId) {
        document.getElementById('edit-class-name').value = className;
        document.getElementById('edit-major-id').value = majorId;
        document.getElementById('edit-teacher-id').value = teacherId !== null ? teacherId : '';
        
        document.getElementById('form-edit').action = `/kelas/${kelasId}`;
        
        document.getElementById('modal-edit').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() {
        document.getElementById('modal-edit').style.display = 'none';
        document.body.style.overflow = '';
    }

    // -------- Modal Delete Kelas --------
    function openDeleteModal(kelasId, className, studentCount) {
        const warnSection = document.getElementById('delete-warning-has-students');
        const confirmSection = document.getElementById('delete-confirm-allow');
        const deleteButton = document.getElementById('btn-confirm-delete');

        if (studentCount > 0) {
            // Block deletion
            warnSection.style.display = 'block';
            confirmSection.style.display = 'none';
            deleteButton.disabled = true;
            deleteButton.style.opacity = '0.5';
            deleteButton.style.cursor = 'not-allowed';
            document.getElementById('delete-class-name-warn').textContent = className;
            document.getElementById('delete-student-count').textContent = studentCount;
        } else {
            // Allow deletion
            warnSection.style.display = 'none';
            confirmSection.style.display = 'block';
            deleteButton.disabled = false;
            deleteButton.style.opacity = '';
            deleteButton.style.cursor = '';
            document.getElementById('delete-class-name').textContent = className;
            document.getElementById('form-delete').action = `/kelas/${kelasId}`;
        }

        document.getElementById('modal-delete').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() {
        document.getElementById('modal-delete').style.display = 'none';
        document.body.style.overflow = '';
    }

    // -------- Search Filter --------
    document.getElementById('search-kelas').addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('#kelas-table tbody tr').forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            row.style.display = searchData.includes(q) ? '' : 'none';
        });
    });

    // -------- Auto-open add modal if validation errors exist --------
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => openAddModal());
    @endif

    // Keyboard escape closes modals
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDeleteModal();
        }
    });
</script>

<style>
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.2s ease;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
</style>
@endsection
