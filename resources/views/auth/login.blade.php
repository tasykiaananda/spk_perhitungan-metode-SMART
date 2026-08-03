@extends('layouts.auth')

@section('title', 'Login - Lacete Coffeeshop')

@section('content')
<div class="login-container" style="max-width: 420px; width: 100%; padding: 20px;">
    <div class="card" style="padding: 2.5rem; margin-bottom: 0;">
        <div class="text-center mb-4">
            @php
                $appName = \App\Models\WebsiteSetting::getByKey('app_name', 'Lacete Coffeeshop');
                $logo = \App\Models\WebsiteSetting::getByKey('logo_path');
            @endphp
            @if($logo)
                <img src="{{ asset($logo) }}" alt="Logo" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 2px solid var(--border-color);">
            @else
                <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(91, 134, 229, 0.3);">
                    <i class="fas fa-coffee" style="color: white; font-size: 1.8rem;"></i>
                </div>
            @endif
            <h2 style="font-weight: 800; font-size: 1.8rem; letter-spacing: -0.5px;">{{ $appName }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.2rem;">Sistem Pengambil Keputusan SMART</p>
        </div>

        <form action="{{ url('/login') }}" method="POST" id="login-form">
            @csrf
            
            <div class="form-group">
                <label for="username" class="form-label">Username atau Email</label>
                <div style="position: relative;">
                    <i class="fas fa-user" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" name="username" id="username" class="form-control" placeholder="admin" value="{{ old('username') }}" style="padding-left: 40px;" required>
                </div>
                @error('username')
                    <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password" class="form-label">Password</label>
                <div style="position: relative;">
                    <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" style="padding-left: 40px;" required>
                </div>
                @error('password')
                    <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 0.9rem;">
                Masuk <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding: 0.8rem; background: rgba(0,0,0,0.03); border-radius: 8px; font-size: 0.8rem; color: var(--text-muted);">
            Gunakan Akun Bawaan: <br>
            Username: <b style="color: var(--text-main);">admin</b> / Password: <b style="color: var(--text-main);">admin123</b>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

@if(session('warning'))
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Sesi Berakhir',
        text: "{{ session('warning') }}",
        confirmButtonColor: 'var(--primary)'
    });
</script>
@endif
@endsection
