@extends('layouts.app')

@section('title', 'Login Staff')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="glass-panel" style="width: 100%; max-width: 450px; padding: 40px; border-radius: 20px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fa-solid fa-shield-halved" style="font-size: 3rem; color: var(--accent-cyan); margin-bottom: 15px;"></i>
            <h2 style="font-weight: 700; margin-bottom: 5px;">Login Staff</h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">SIKAT - Sistem Informasi Ketertiban & Aturan Sekolah</p>
        </div>

        @if($errors->any())
            <div class="alert-glass">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email Sekolah</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nama@smkn2jkt.sch.id" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px;">
            <a href="{{ route('siswa.check') }}" class="nav-link" style="font-size: 0.9rem;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Cek Poin Siswa
            </a>
        </div>
    </div>
</div>
@endsection
