@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="dashboard-header">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">
            <i class="fa-solid fa-users-gear" style="color: var(--accent-purple);"></i> Manajemen User
        </h1>
        <p style="color: var(--text-secondary); margin-top: 4px;">Kelola akun staff sekolah (Super Admin, Guru BK, dan Wali Kelas)</p>
    </div>
    <button class="btn-primary" id="btn-add-user" onclick="openAddModal()">
        <i class="fa-solid fa-user-plus"></i> Tambah User
    </button>
</div>

{{-- Stats Row --}}
<div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px;">
    @php
        $superAdmins    = $users->where('role', 'super_admin')->count();
        $guruBk         = $users->where('role', 'guru_bk')->count();
        $wakilKesiswaan = $users->where('role', 'wakil_kesiswaan')->count();
        $waliKelas      = $users->where('role', 'wali_kelas')->count();
        $guru           = $users->where('role', 'guru')->count();
        $mapped         = $users->where('role', 'wali_kelas')->filter(fn($u) => $u->classes->isNotEmpty())->count();
    @endphp
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-purple);">
        <div class="metric-icon" style="background: rgba(139,92,246,0.15); color: var(--accent-purple);">
            <i class="fa-solid fa-crown"></i>
        </div>
        <div>
            <div class="metric-value">{{ $superAdmins }}</div>
            <div class="metric-label">Super Admin</div>
        </div>
    </div>
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-cyan);">
        <div class="metric-icon" style="background: rgba(6,182,212,0.15); color: var(--accent-cyan);">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <div>
            <div class="metric-value">{{ $guruBk }}</div>
            <div class="metric-label">Guru BK</div>
        </div>
    </div>
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-pink);">
        <div class="metric-icon" style="background: rgba(236,72,153,0.15); color: var(--accent-pink);">
            <i class="fa-solid fa-briefcase"></i>
        </div>
        <div>
            <div class="metric-value">{{ $wakilKesiswaan }}</div>
            <div class="metric-label">Wakil Kesiswaan</div>
        </div>
    </div>
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-green);">
        <div class="metric-icon" style="background: rgba(16,185,129,0.15); color: var(--accent-green);">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <div>
            <div class="metric-value">{{ $mapped }}<span style="font-size: 0.9rem; font-weight: 500;">/{{ $waliKelas }}</span></div>
            <div class="metric-label">Wali Kelas Terpetakan</div>
        </div>
    </div>
    <div class="glass-panel metric-card" style="border-left: 3px solid var(--accent-orange);">
        <div class="metric-icon" style="background: rgba(245,158,11,0.15); color: var(--accent-orange);">
            <i class="fa-solid fa-chalkboard"></i>
        </div>
        <div>
            <div class="metric-value">{{ $guru }}</div>
            <div class="metric-label">Guru</div>
        </div>
    </div>
</div>

{{-- User Table --}}
<div class="glass-panel" style="padding: 30px; border-radius: 20px;">
    <h3 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-list" style="color: var(--accent-purple);"></i> Daftar Pengguna Terdaftar
        <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-secondary); margin-left: auto;">Total: {{ $users->count() }} user</span>
    </h3>

    {{-- Search --}}
    <div style="position: relative; margin-bottom: 20px; max-width: 360px;">
        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        <input type="text" id="search-user" placeholder="Cari nama atau email..." class="form-control" style="padding-left: 40px;">
    </div>

    <div class="table-container">
        <table class="table-glass" id="user-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role & Kelas</th>
                    <th>Terdaftar</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                    <tr data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                        <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $i + 1 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background:
                                    @if($user->role === 'super_admin') rgba(139,92,246,0.2)
                                    @elseif($user->role === 'guru_bk') rgba(6,182,212,0.2)
                                    @elseif($user->role === 'wakil_kesiswaan') rgba(236,72,153,0.2)
                                    @elseif($user->role === 'guru') rgba(245,158,11,0.2)
                                    @else rgba(16,185,129,0.2) @endif;
                                    display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                                    @if($user->role === 'super_admin')
                                        <i class="fa-solid fa-crown" style="color: var(--accent-purple);"></i>
                                    @elseif($user->role === 'guru_bk')
                                        <i class="fa-solid fa-user-shield" style="color: var(--accent-cyan);"></i>
                                    @elseif($user->role === 'wakil_kesiswaan')
                                        <i class="fa-solid fa-briefcase" style="color: var(--accent-pink);"></i>
                                    @elseif($user->role === 'guru')
                                        <i class="fa-solid fa-chalkboard" style="color: var(--accent-orange);"></i>
                                    @else
                                        <i class="fa-solid fa-chalkboard-user" style="color: var(--accent-green);"></i>
                                    @endif
                                </div>
                                <span style="font-weight: 600;">{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span style="font-size: 0.7rem; background: rgba(139,92,246,0.2); color: var(--accent-purple); padding: 2px 7px; border-radius: 999px; font-weight: 600; border: 1px solid rgba(139,92,246,0.3);">Anda</span>
                                @endif
                            </div>
                        </td>
                        <td style="color: var(--text-secondary); font-size: 0.9rem;">{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'super_admin')
                                <span class="badge badge-berat" style="background: rgba(139,92,246,0.2); color: #a78bfa; border: 1px solid rgba(139,92,246,0.3);">Super Admin</span>
                            @elseif($user->role === 'guru_bk')
                                <span class="badge badge-success">Guru BK</span>
                            @elseif($user->role === 'wakil_kesiswaan')
                                <span class="badge" style="background: rgba(236,72,153,0.15); color: #f472b6; border: 1px solid rgba(236,72,153,0.3);">Wakil Kesiswaan</span>
                            @elseif($user->role === 'guru')
                                <span class="badge" style="background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3);">Guru</span>
                            @else
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span class="badge badge-warning" style="align-self: flex-start;">Wali Kelas</span>
                                    @if($user->classes->isNotEmpty())
                                        <span style="font-size: 0.82rem; font-weight: 600; color: var(--accent-cyan);">
                                            <i class="fa-solid fa-school"></i> {{ $user->classes->pluck('class_name')->implode(', ') }}
                                        </span>
                                    @else
                                        <span style="font-size: 0.78rem; color: #f97316; font-style: italic; font-weight: 500;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Belum dipetakan
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary); font-size: 0.85rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap;">
                                {{-- Remap button for Wali Kelas --}}
                                @if($user->role === 'wali_kelas')
                                    <button class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; height: auto; border-radius: 8px;"
                                        onclick="openRemapModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->classes->first()?->id ?? 'null' }})">
                                        <i class="fa-solid fa-arrows-rotate"></i> Petakan Ulang
                                    </button>
                                @endif
                                {{-- Edit button --}}
                                <a href="{{ route('users.edit', $user) }}" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; height: auto; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                {{-- Delete button --}}
                                @if($user->id !== auth()->id())
                                    <button class="btn-danger" style="padding: 6px 12px; font-size: 0.8rem; height: auto; border-radius: 8px;"
                                        onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-users-slash" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            Belum ada user terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: Add New User           --}}
{{-- ============================= --}}
<div id="modal-add" class="modal-overlay" style="display: none;" onclick="if(event.target===this) closeAddModal()">
    <div class="glass-panel" style="max-width: 520px; width: 95%; padding: 36px; border-radius: 24px; position: relative; max-height: 90vh; overflow-y: auto;">
        <button onclick="closeAddModal()" style="position: absolute; top: 16px; right: 18px; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.3rem; line-height: 1;">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 6px;">
            <i class="fa-solid fa-user-plus" style="color: var(--accent-cyan);"></i> Daftarkan User Baru
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.88rem; margin-bottom: 24px;">Isi data di bawah untuk membuat akun baru.</p>

        @if($errors->any())
            <div class="alert-glass" style="padding: 12px 16px; font-size: 0.85rem; margin-bottom: 18px;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" id="form-add-user">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Ahmad Suherman, S.Pd." required value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="e.g. ahmad@smkn2jkt.sch.id" required value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role Akses</label>
                <select name="role" id="add-role" class="form-control" required onchange="toggleClassSelect('add-class-group', this.value)">
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="guru_bk" {{ old('role') === 'guru_bk' ? 'selected' : '' }}>Guru BK</option>
                    <option value="wakil_kesiswaan" {{ old('role') === 'wakil_kesiswaan' ? 'selected' : '' }}>Wakil Kesiswaan</option>
                    <option value="wali_kelas" {{ old('role') === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                    <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>

            {{-- Class selection (only shown for wali_kelas) --}}
            <div class="form-group" id="add-class-group" style="{{ old('role') === 'wali_kelas' ? '' : 'display: none;' }}">
                <label class="form-label">
                    <i class="fa-solid fa-school" style="color: var(--accent-cyan);"></i> Pemetaan Kelas
                    <span style="color: var(--accent-orange); font-size: 0.8rem;"> *wajib untuk Wali Kelas</span>
                </label>
                <select name="class_id" id="add-class-id" class="form-control">
                    <option value="">— Pilih Kelas —</option>
                    @foreach($classes as $kelas)
                        <option value="{{ $kelas->id }}"
                            {{ old('class_id') == $kelas->id ? 'selected' : '' }}
                            style="{{ $kelas->homeroom_teacher_id ? 'color: var(--text-muted);' : '' }}">
                            {{ $kelas->class_name }}
                            @if($kelas->homeroomTeacher)
                                (Sudah: {{ $kelas->homeroomTeacher->name }})
                            @else
                                — Kosong
                            @endif
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 5px;">
                    <i class="fa-solid fa-circle-info"></i> Kelas yang sudah diisi wali kelas lain bisa tetap dipilih — pemetaan lama akan digantikan.
                </small>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan User
            </button>
        </form>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: Remap Wali Kelas       --}}
{{-- ============================= --}}
<div id="modal-remap" class="modal-overlay" style="display: none;" onclick="if(event.target===this) closeRemapModal()">
    <div class="glass-panel" style="max-width: 460px; width: 95%; padding: 36px; border-radius: 24px; position: relative;">
        <button onclick="closeRemapModal()" style="position: absolute; top: 16px; right: 18px; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.3rem; line-height: 1;">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 6px;">
            <i class="fa-solid fa-arrows-rotate" style="color: var(--accent-cyan);"></i> Petakan Ulang Kelas
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.88rem; margin-bottom: 24px;">
            Mengubah pemetaan kelas untuk: <strong id="remap-teacher-name" style="color: var(--text-primary);"></strong>
        </p>

        <form id="form-remap" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label">
                    <i class="fa-solid fa-school" style="color: var(--accent-cyan);"></i> Kelas Baru
                </label>
                <select name="class_id" id="remap-class-id" class="form-control" required>
                    <option value="">— Pilih Kelas —</option>
                    @foreach($classes as $kelas)
                        <option value="{{ $kelas->id }}"
                            style="{{ $kelas->homeroom_teacher_id ? 'color: var(--text-muted);' : '' }}">
                            {{ $kelas->class_name }}
                            @if($kelas->homeroomTeacher)
                                ({{ $kelas->homeroomTeacher->name }})
                            @else
                                — Kosong
                            @endif
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 5px;">
                    <i class="fa-solid fa-circle-info"></i> Pemetaan kelas lama akan otomatis dilepas.
                </small>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeRemapModal()" class="btn-secondary" style="flex: 1;">
                    <i class="fa-solid fa-ban"></i> Batal
                </button>
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-arrows-rotate"></i> Simpan Pemetaan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL: Delete Confirm         --}}
{{-- ============================= --}}
<div id="modal-delete" class="modal-overlay" style="display: none;" onclick="if(event.target===this) closeDeleteModal()">
    <div class="glass-panel" style="max-width: 420px; width: 95%; padding: 36px; border-radius: 24px; position: relative; text-align: center;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <i class="fa-solid fa-trash-can" style="font-size: 1.8rem; color: #ef4444;"></i>
        </div>
        <h2 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 8px;">Hapus User?</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 24px;">
            Anda akan menghapus akun: <strong id="delete-user-name" style="color: var(--text-primary);"></strong>.<br>
            Tindakan ini tidak dapat dibatalkan.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeDeleteModal()" class="btn-secondary" style="flex: 1; max-width: 150px;">
                <i class="fa-solid fa-ban"></i> Batal
            </button>
            <form id="form-delete" method="POST" style="flex: 1; max-width: 150px;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="width: 100%;">
                    <i class="fa-solid fa-trash-can"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // -------- Modal Add User --------
    function openAddModal() {
        document.getElementById('modal-add').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeAddModal() {
        document.getElementById('modal-add').style.display = 'none';
        document.body.style.overflow = '';
    }

    // -------- Toggle Class Select --------
    function toggleClassSelect(groupId, role) {
        const group = document.getElementById(groupId);
        if (!group) return;
        if (role === 'wali_kelas') {
            group.style.display = 'block';
            group.style.animation = 'fadeInDown 0.25s ease';
        } else {
            group.style.display = 'none';
            // Clear selection
            const sel = group.querySelector('select');
            if (sel) sel.value = '';
        }
    }

    // -------- Modal Remap --------
    function openRemapModal(userId, userName, currentClassId) {
        document.getElementById('remap-teacher-name').textContent = userName;
        document.getElementById('form-remap').action = `/users/${userId}/remap`;

        // Set current class if any
        const sel = document.getElementById('remap-class-id');
        if (currentClassId) sel.value = currentClassId;

        document.getElementById('modal-remap').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeRemapModal() {
        document.getElementById('modal-remap').style.display = 'none';
        document.body.style.overflow = '';
    }

    // -------- Modal Delete --------
    function openDeleteModal(userId, userName) {
        document.getElementById('delete-user-name').textContent = userName;
        document.getElementById('form-delete').action = `/users/${userId}`;
        document.getElementById('modal-delete').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() {
        document.getElementById('modal-delete').style.display = 'none';
        document.body.style.overflow = '';
    }

    // -------- Search Filter --------
    document.getElementById('search-user').addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('#user-table tbody tr').forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            row.style.display = searchData.includes(q) ? '' : 'none';
        });
    });

    // -------- Auto-open add modal if errors (old input had role) --------
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => openAddModal());
    @endif

    // Restore role-based class select on error
    const oldRole = document.getElementById('add-role')?.value;
    if (oldRole === 'wali_kelas') {
        toggleClassSelect('add-class-group', oldRole);
    }

    // Keyboard escape closes modals
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeAddModal();
            closeRemapModal();
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
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
</style>
@endsection
