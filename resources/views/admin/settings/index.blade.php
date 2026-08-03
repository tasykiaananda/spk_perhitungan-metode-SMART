@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Pengaturan</span>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start; flex-wrap: wrap;">

    <!-- Left side: Account Profile & Security -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Profile Settings Card -->
        <div class="card">
            <div class="card-header">
                <h3>Profil Administrator</h3>
                <p>Ubah nama, username, dan alamat email Anda</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.profile') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
                        @error('name')
                            <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="{{ $user->username }}" required>
                        @error('username')
                            <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                        @error('email')
                            <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-full"><i class="fas fa-user-pen"></i> Simpan Perubahan Profil</button>
                </form>
            </div>
        </div>

        <!-- Password Change Card -->
        <div class="card">
            <div class="card-header">
                <h3>Ganti Password</h3>
                <p>Perbarui password akun Anda secara berkala</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.password') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="••••••••" required>
                        @error('current_password')
                            <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="••••••••" required>
                        @error('new_password')
                            <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-secondary w-full"><i class="fas fa-key"></i> Perbarui Password</button>
                </form>
            </div>
        </div>

    </div>

    <!-- Right side: Website Settings -->
    <div class="card">
        <div class="card-header">
            <h3>Pengaturan Branding Website</h3>
            <p>Konfigurasi nama aplikasi, teks footer, logo, dan favicon</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.website') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="app_name" class="form-label">Nama Aplikasi / Coffeeshop</label>
                    <input type="text" name="app_name" id="app_name" class="form-control" value="{{ $settings['app_name'] ?? 'Lacete Coffeeshop' }}" required>
                    @error('app_name')
                        <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="footer_text" class="form-label">Teks Hak Cipta Footer</label>
                    <input type="text" name="footer_text" id="footer_text" class="form-control" value="{{ $settings['footer_text'] ?? '© 2026 Lacete Coffeeshop. All rights reserved.' }}" required>
                    @error('footer_text')
                        <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Logo upload -->
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label class="form-label">Logo Website</label>
                    <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 0.75rem;">
                        <div style="width: 70px; height: 70px; border-radius: 12px; background: rgba(0,0,0,0.05); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if(isset($settings['logo_path']) && $settings['logo_path'])
                                <img src="{{ asset($settings['logo_path']) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fas fa-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Rekomendasi ukuran square, maks 2MB (.png, .jpg, .jpeg)</small>
                        </div>
                    </div>
                    @error('logo')
                        <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Favicon upload -->
                <div class="form-group" style="margin-top: 1.5rem; margin-bottom: 2rem;">
                    <label class="form-label">Favicon (.ico atau .png)</label>
                    <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(0,0,0,0.05); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if(isset($settings['favicon_path']) && $settings['favicon_path'])
                                <img src="{{ asset($settings['favicon_path']) }}" alt="Favicon" style="width: 24px; height: 24px; object-fit: contain;">
                            @else
                                <i class="fas fa-coffee" style="font-size: 1rem; color: var(--text-muted);"></i>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <input type="file" name="favicon" class="form-control" accept="image/x-icon,image/png">
                            <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Rekomendasi ukuran 16x16 atau 32x32 piksel, maks 512KB</small>
                        </div>
                    </div>
                    @error('favicon')
                        <span style="color: #f43f5e; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-full"><i class="fas fa-palette"></i> Simpan Pengaturan Website</button>
            </form>
        </div>
    </div>

</div>
@endsection
