@extends('layouts.admin')

@section('title', 'Detail Perhitungan Matematika')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item"><a href="{{ route('admin.supplier.index') }}">Supplier</a></span>
<span class="breadcrumb-item active">{{ $alternatif->nama }}</span>
@endsection

@section('content')
<div class="card mb-4" style="border-left: 6px solid var(--primary); padding: 1.5rem;">
    <div class="flex-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-weight: 800; font-size: 1.6rem; letter-spacing: -0.5px;">{{ $alternatif->nama }}</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.2rem;">Derivasi rumus matematika SMART untuk mengevaluasi kelayakan supplier</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Peringkat Saat Ini</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary); line-height: 1;">#{{ $ranking }}</div>
        </div>
    </div>
</div>

<!-- Detailed Table of Derivations -->
<div class="card">
    <div class="card-header">
        <h3>Langkah Substitusi Rumus per Kriteria</h3>
        <p>Proses transformasi nilai asli menjadi nilai utility terbobot</p>
    </div>
    
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Tipe</th>
                        <th>Nilai (C)</th>
                        <th>Batas [Min, Max]</th>
                        <th>Rumus Utility</th>
                        <th>Substitusi Matematika & Hasil (u)</th>
                        <th>Bobot (W)</th>
                        <th class="text-right">Terbobot (W &times; u)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steps as $step)
                        <tr>
                            <td>
                                <b>{{ $step['kriteria_nama'] }}</b> <br>
                                <span class="badge" style="background: rgba(0,0,0,0.03); color: var(--text-muted); font-size: 0.75rem; padding: 0.15rem 0.5rem; margin-top: 0.2rem;">{{ $step['kriteria_id'] }}</span>
                            </td>
                            <td>
                                @if($step['jenis'] === 'Cost')
                                    <span class="badge badge-cost">Cost</span>
                                @else
                                    <span class="badge badge-benefit">Benefit</span>
                                @endif
                            </td>
                            <td class="font-bold">{{ $step['nilai_asli'] }}</td>
                            <td style="font-family: monospace;">[{{ $step['min'] }}, {{ $step['max'] }}]</td>
                            <td style="font-size: 0.8rem; font-family: monospace; color: var(--text-muted);">
                                {{ $step['formula'] }}
                            </td>
                            <td style="font-family: monospace; font-size: 0.9rem;">
                                {{ $step['substitusi'] }} <br>
                                <span class="font-bold" style="color: var(--primary);">u = {{ number_format($step['utility'], 2) }}</span>
                            </td>
                            <td class="font-bold text-primary">{{ number_format($step['bobot'], 4) }}</td>
                            <td class="text-right font-bold text-success" style="font-size: 1.05rem;">
                                {{ number_format($step['weighted'], 4) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: rgba(91, 134, 229, 0.05); font-weight: bold; font-size: 1.1rem;">
                        <td colspan="7" class="text-right" style="padding: 1.5rem;">Akumulasi Skor Total (&Sigma; W<sub>j</sub> &times; u<sub>j</sub>):</td>
                        <td class="text-right text-success" style="padding: 1.5rem; font-size: 1.3rem;">{{ number_format($total_skor, 4) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Formula Reference Card -->
<div class="card mt-4">
    <div class="card-header">
        <h3>Referensi Model Matematika SMART</h3>
        <p>Definisi formal formula penyelesaian</p>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div style="padding: 1rem; background: rgba(0,0,0,0.02); border-radius: var(--radius-sm); border: 1px dashed var(--border-color);">
                <h5 class="font-bold text-primary mb-4" style="font-size: 1rem;"><i class="fas fa-arrow-up-long"></i> Kriteria Benefit</h5>
                <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6;">
                    Digunakan jika kriteria semakin tinggi nilainya semakin menguntungkan (seperti kualitas biji kopi, kapasitas produksi). <br>
                    <span class="font-bold text-primary" style="font-family: monospace; display: block; margin-top: 0.5rem; font-size: 1.05rem;">u(a) = ((C_asli - C_min) / (C_max - C_min)) * 100</span>
                </p>
            </div>
            
            <div style="padding: 1rem; background: rgba(0,0,0,0.02); border-radius: var(--radius-sm); border: 1px dashed var(--border-color);">
                <h5 class="font-bold text-danger mb-4" style="font-size: 1rem;"><i class="fas fa-arrow-down-long"></i> Kriteria Cost</h5>
                <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6;">
                    Digunakan jika kriteria semakin kecil nilainya semakin menguntungkan (seperti harga biji kopi, cacat pengiriman). <br>
                    <span class="font-bold text-danger" style="font-family: monospace; display: block; margin-top: 0.5rem; font-size: 1.05rem;">u(a) = ((C_max - C_asli) / (C_max - C_min)) * 100</span>
                </p>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('admin.smart.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Kembali ke Proses SMART</a>
        </div>
    </div>
</div>
@endsection
