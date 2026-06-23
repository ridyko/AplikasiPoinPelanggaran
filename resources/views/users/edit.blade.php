@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="dashboard-header">
    <div>
        <a href="{{ route('users') }}" style="color: var(--text-muted); font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Kelola User
        </a>
        <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;">
            <i class="fa-solid fa-pen" style="color: var(--accent-cyan);"></i> Edit User
        </h1>
        <p style="color: var(--text-secondary); margin-top: 4px;">Mengubah data akun: <strong>{{ $user->name }}</strong></p>
    </div>
</div>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="glass-panel" style="padding: 36px; border-radius: 24px;">

        @if($errors->any())
            <div class="alert-glass" style="padding: 12px 16px; font-size: 0.88rem; margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
            </div>

            <div class="form-group">
                <label class="form-label">
                    Password Baru
                    <span style="font-weight: 400; color: var(--text-muted); font-size: 0.82rem;">(kosongkan jika tidak ingin mengubah)</span>
                </label>
                <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter">
            </div>

            <div class="form-group">
                <label class="form-label">Role Akses</label>
                <select name="role" id="edit-role" class="form-control" required onchange="toggleClassSelect('edit-class-group', this.value)">
                   <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                   <option value="guru_bk"     {{ old('role', $user->role) === 'guru_bk'     ? 'selected' : '' }}>Guru BK</option>
                   <option value="wakil_kesiswaan" {{ old('role', $user->role) === 'wakil_kesiswaan' ? 'selected' : '' }}>Wakil Kesiswaan</option>
                   <option value="wali_kelas"  {{ old('role', $user->role) === 'wali_kelas'  ? 'selected' : '' }}>Wali Kelas</option>
                   <option value="guru"        {{ old('role', $user->role) === 'guru'        ? 'selected' : '' }}>Guru</option>
                </select>
            </div>

            {{-- Class assignment --}}
            @php $currentClass = $user->classes->first(); @endphp
            <div class="form-group" id="edit-class-group" style="{{ old('role', $user->role) === 'wali_kelas' ? '' : 'display:none;' }}">
                <label class="form-label">
                    <i class="fa-solid fa-school" style="color: var(--accent-cyan);"></i> Pemetaan Kelas
                    <span style="color: var(--accent-orange); font-size: 0.8rem;"> *wajib untuk Wali Kelas</span>
                </label>
                <select name="class_id" id="edit-class-id" class="form-control">
                    <option value="">— Pilih Kelas —</option>
                    @foreach($classes as $kelas)
                        <option value="{{ $kelas->id }}"
                            {{ old('class_id', $currentClass?->id) == $kelas->id ? 'selected' : '' }}
                            style="{{ $kelas->homeroom_teacher_id && $kelas->homeroom_teacher_id !== $user->id ? 'color: var(--text-muted);' : '' }}">
                            {{ $kelas->class_name }}
                            @if($kelas->homeroom_teacher_id && $kelas->homeroom_teacher_id !== $user->id)
                                (Sudah: {{ $kelas->homeroomTeacher?->name }})
                            @elseif($kelas->homeroom_teacher_id === $user->id)
                                (Kelas Saat Ini)
                            @else
                                — Kosong
                            @endif
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 5px;">
                    <i class="fa-solid fa-circle-info"></i> Pemetaan kelas lama akan dilepas secara otomatis.
                </small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('users') }}" class="btn-secondary" style="flex: 1; text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-ban"></i> Batal
                </a>
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleClassSelect(groupId, role) {
        const group = document.getElementById(groupId);
        if (!group) return;
        if (role === 'wali_kelas') {
            group.style.display = 'block';
            group.style.animation = 'fadeInDown 0.25s ease';
        } else {
            group.style.display = 'none';
            const sel = group.querySelector('select');
            if (sel) sel.value = '';
        }
    }
</script>
<style>
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
