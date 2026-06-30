@extends('layouts.guest')

@section('title', 'Cek Poin Pelanggaran Siswa')

@section('content')
<div class="guest-card">
    <div class="guest-card-header" style="text-align: center;">
        <h2>Portal Monitoring</h2>
        <p>Silakan masukkan NIS untuk memeriksa poin kedisiplinan.</p>
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

    <form action="{{ route('siswa.check.post') }}" method="POST">
        @csrf
        
        <div class="form-group" style="margin-bottom: 25px;">
            <label for="nis" class="guest-label" style="text-align: left;">NIS SISWA</label>
            <div style="position: relative;">
                <i class="fa-solid fa-id-card" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                <input type="text" name="nis" id="nis" class="form-control" placeholder="Contoh: 990001" style="padding-left: 45px; text-align: center; letter-spacing: 2px; font-family: monospace; font-size: 1.1rem;" required autofocus value="{{ old('nis') }}">
            </div>
            <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 8px; text-align: left;">
                *NIS (Nomor Induk Siswa) terdiri dari 6 digit angka.
            </small>
        </div>

        <button type="submit" class="btn-primary-guest">
            PERIKSA POIN SISWA
        </button>
    </form>
</div>
@endsection

@section('footer_links')
<a href="{{ url('/') }}" class="guest-back-link">← KEMBALI KE BERANDA</a>
@endsection
