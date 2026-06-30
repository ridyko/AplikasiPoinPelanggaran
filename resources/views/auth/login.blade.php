@extends('layouts.guest')

@section('title', 'Login Staff')

@section('content')
<div class="guest-card">
    <div class="guest-card-header">
        <h2>Selamat Datang</h2>
        <p>Silakan masuk untuk melanjutkan monitoring.</p>
    </div>

    @if($errors->any())
        <div class="alert-glass" style="margin-bottom: 20px;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="email" class="guest-label">EMAIL</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group" style="margin-bottom: 30px;">
            <label for="password" class="guest-label">PASSWORD</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-primary-guest">
            MASUK SEKARANG
        </button>
    </form>
</div>
@endsection

@section('footer_links')
<a href="{{ url('/') }}" class="guest-back-link">← KEMBALI KE BERANDA</a>
@endsection
