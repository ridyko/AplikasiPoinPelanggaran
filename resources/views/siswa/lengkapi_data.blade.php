@extends('layouts.guest')

@section('title', 'Lengkapi Data Wali Murid')

@section('content')
<div class="guest-card" style="max-width: 500px;">
    <div class="guest-card-header" style="text-align: center; margin-bottom: 25px;">
        <h2>Lengkapi Data Wali</h2>
        <p>Hubungkan kontak WhatsApp Orang Tua/Wali ke sistem monitoring kedisiplinan SIKAT</p>
    </div>

    @if(session('error'))
        <div class="alert-glass" style="margin-bottom: 20px;">
            <i class="fa-solid fa-triangle-exclamation"></i> {!! session('error') !!}
        </div>
    @endif

    @if(session('success'))
        <div class="alert-glass-success" style="margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-glass" style="margin-bottom: 20px; text-align: left; padding: 12px 18px;">
            <ul style="margin: 0; padding-left: 20px; font-size: 0.88rem; color: #f87171;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!$student)
        <!-- STEP 1: VERIFIKASI NIS -->
        <form action="{{ route('siswa.lengkapi_data') }}" method="GET">
            <div class="form-group" style="margin-bottom: 25px;">
                <label for="nis" class="guest-label" style="text-align: left;">MASUKKAN NIS SISWA</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-id-card" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                    <input type="text" name="nis" id="nis" class="form-control" placeholder="Contoh: 990001" style="padding-left: 45px; text-align: center; letter-spacing: 2px; font-family: monospace; font-size: 1.1rem;" required autofocus value="{{ old('nis', $nis) }}">
                </div>
                <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 8px; text-align: left;">
                    *Masukkan 6 digit Nomor Induk Siswa (NIS) Anda untuk memverifikasi identitas.
                </small>
            </div>

            <button type="submit" class="btn-primary-guest">
                VERIFIKASI NIS
            </button>
        </form>
    @else
        <!-- STEP 2: ISI DATA WALI -->
        <div class="student-info-box" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 18px; margin-bottom: 25px; text-align: left;">
            <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 6px;">
                <i class="fa-solid fa-user-graduate"></i> Identitas Siswa Ditemukan
            </h4>
            <div style="display: grid; grid-template-columns: 100px 1fr; gap: 8px 15px; font-size: 0.92rem;">
                <span style="color: var(--text-secondary);">Nama Siswa</span>
                <span style="font-weight: 700; color: var(--text-primary);">: {{ $student->name }}</span>
                
                <span style="color: var(--text-secondary);">NIS</span>
                <span style="font-weight: 600; color: var(--text-primary); font-family: monospace;">: {{ $student->nis }}</span>
                
                <span style="color: var(--text-secondary);">Kelas</span>
                <span style="font-weight: 600; color: var(--text-primary);">: {{ $student->kelas->class_name }}</span>
                
                <span style="color: var(--text-secondary);">Tahun Ajaran</span>
                <span style="color: var(--text-primary);">: {{ $student->tahun_ajaran }}</span>
            </div>
        </div>

        <form action="{{ route('siswa.lengkapi_data.post') }}" method="POST">
            @csrf
            <input type="hidden" name="nis" value="{{ $student->nis }}">

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label for="parent_name" class="guest-label">NAMA ORANG TUA / WALI</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-user-tie" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                    <input type="text" name="parent_name" id="parent_name" class="form-control" placeholder="Contoh: Hartono" style="padding-left: 45px;" required value="{{ old('parent_name', $student->parent_name) }}">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 30px; text-align: left;">
                <label for="parent_phone" class="guest-label">NOMOR WHATSAPP WALI</label>
                <div style="position: relative;">
                    <i class="fa-brands fa-whatsapp" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                    <input type="text" name="parent_phone" id="parent_phone" class="form-control" placeholder="Contoh: 08123456789" style="padding-left: 45px;" required value="{{ old('parent_phone', $student->parent_phone) }}">
                </div>
                <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 6px;">
                    *Harus diawali angka 08 (misal: 08123456789) agar notifikasi kedisiplinan WhatsApp otomatis terkirim.
                </small>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn-primary-guest" style="flex: 1.5;">
                    <i class="fa-solid fa-floppy-disk"></i> SIMPAN & HUBUNGKAN
                </button>
                <a href="{{ route('siswa.lengkapi_data') }}" class="btn-secondary" style="flex: 0.7; text-align: center; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 600; text-decoration: none;">
                    BATAL
                </a>
            </div>
        </form>
    @endif
</div>
@endsection

@section('footer_links')
<a href="{{ url('/') }}" class="guest-back-link">← KEMBALI KE BERANDA</a>
@endsection
