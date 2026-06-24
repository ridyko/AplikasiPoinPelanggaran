@extends('layouts.app')

@section('title', 'Cek Poin Pelanggaran Siswa')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="glass-panel" style="width: 100%; max-width: 500px; padding: 40px; border-radius: 20px; text-align: center;">
        <div style="margin-bottom: 30px;">
            <i class="fa-solid fa-graduation-cap" style="font-size: 3.5rem; color: var(--accent-cyan); margin-bottom: 15px;"></i>
            <h2 style="font-weight: 800; margin-bottom: 5px; font-size: 1.8rem; background: linear-gradient(135deg, #fff 0%, var(--accent-cyan) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Portal Monitoring Siswa
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 5px;">
                Cek Akumulasi Poin Kedisiplinan & Pelanggaran - SIKAT SMKN 2 Jakarta
            </p>
        </div>

        <form action="{{ route('siswa.check.post') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 25px;">
                <label for="nisn" class="form-label" style="text-align: left;">Masukkan NISN Siswa</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-id-card" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                    <input type="text" name="nisn" id="nisn" class="form-control" placeholder="Contoh: 0081234567" style="padding-left: 45px; text-align: center; letter-spacing: 2px; font-family: monospace; font-size: 1.1rem;" required autofocus value="{{ old('nisn') }}">
                </div>
                <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 8px; text-align: left;">
                    *NISN (Nomor Induk Siswa Nasional) terdiri dari 10 digit angka.
                </small>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; font-size: 1rem; padding: 14px;">
                <i class="fa-solid fa-magnifying-glass"></i> Periksa Poin Kedisiplinan
            </button>
        </form>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.08); font-size: 0.85rem; color: var(--text-muted);">
            Informasi poin terintegrasi langsung dengan notifikasi WhatsApp Orang Tua/Wali Murid.
        </div>
    </div>
</div>
@endsection
