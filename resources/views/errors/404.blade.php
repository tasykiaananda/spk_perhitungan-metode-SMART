@extends('layouts.auth')

@section('title', 'Halaman Tidak Ditemukan - 404')

@section('content')
<div class="text-center" style="max-width: 480px; width: 100%; padding: 20px;">
    <div class="card" style="padding: 3rem; margin-bottom: 0;">
        <div style="font-size: 5.5rem; font-weight: 800; line-height: 1; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1.5rem; filter: drop-shadow(0 4px 10px rgba(91, 134, 229, 0.2));">
            404
        </div>
        
        <h3 style="font-weight: 800; font-size: 1.4rem; margin-bottom: 0.5rem;">Halaman Tidak Ditemukan</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 2rem;">
            Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan ke alamat lain.
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary" style="padding: 0.8rem 2rem; width: auto; display: inline-flex;">
            <i class="fas fa-house mr-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
