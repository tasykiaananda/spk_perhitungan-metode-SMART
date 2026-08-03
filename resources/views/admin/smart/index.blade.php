@extends('layouts.admin')

@section('title', 'Proses Perhitungan SMART')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Proses SMART</span>
@endsection

@section('content')
<!-- Check if assessments are incomplete -->
@php
    $incompleteCount = 0;
    foreach($nilai_matrix as $row) {
        foreach($row['nilai'] as $kId => $val) {
            if($val == 0) {
                $incompleteCount++;
            }
        }
    }
@endphp

@if($incompleteCount > 0)
    <div class="card" style="border-left: 6px solid #f59e0b; background: rgba(245, 158, 11, 0.1); margin-bottom: 1.5rem; padding: 1.2rem;">
        <div style="display: flex; gap: 15px; align-items: center;">
            <i class="fas fa-triangle-exclamation" style="color: #f59e0b; font-size: 1.8rem;"></i>
            <div>
                <h4 style="font-weight: 700; color: var(--text-main);">Peringatan: Data Penilaian Belum Lengkap!</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.1rem;">
                    Terdapat <b>{{ $incompleteCount }}</b> nilai penilaian yang bernilai 0. Harap lengkapi nilai kriteria pada menu <a href="{{ route('admin.penilaian.index') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Nilai Penilaian</a> untuk memastikan hasil perhitungan akurat.
                </p>
            </div>
        </div>
    </div>
@endif

<!-- Calculation Progress Control -->
<div class="card mb-4" style="padding: 1.8rem;">
    <div class="flex-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="margin-bottom: 0.25rem;">Proses Algoritma SMART</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Tekan tombol untuk menghitung dan menyimpan hasil peringkat ke database</p>
        </div>
        <form action="{{ route('admin.smart.process') }}" method="POST" id="smartProcessForm">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-rotate mr-2"></i> Proses & Simpan Perhitungan</button>
        </form>
    </div>
    
    <div style="margin-top: 1.5rem;">
        <div class="flex-between" style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.4rem;">
            <span>Kemajuan Perhitungan</span>
            <span id="progress-percentage">100% Selesai</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progress-bar" style="width: 100%;"></div>
        </div>
    </div>
</div>

<!-- STEP-BY-STEP FLOW -->
<div style="display: flex; flex-direction: column; gap: 2rem;">

    <!-- LANGKAH 1: Normalisasi Bobot Kriteria -->
    <div class="card">
        <div class="card-header">
            <h3>Langkah 1: Menentukan Bobot & Normalisasi Kriteria</h3>
            <p>Rumus Normalisasi Bobot: <b>W<sub>j</sub> = Rating<sub>j</sub> / &Sigma; Rating</b></p>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Tipe</th>
                        <th>Rating (Bobot Asli)</th>
                        <th>Proses Normalisasi</th>
                        <th>Bobot Ternormalisasi (W)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kriterias as $k)
                        <tr>
                            <td><span class="badge" style="background: rgba(91, 134, 229, 0.15); color: var(--primary); font-weight: 800;">{{ $k->id }}</span></td>
                            <td><b>{{ $k->nama }}</b></td>
                            <td>
                                @if($k->jenis === 'Cost')
                                    <span class="badge badge-cost">Cost</span>
                                @else
                                    <span class="badge badge-benefit">Benefit</span>
                                @endif
                            </td>
                            <td>{{ $k->rating }}</td>
                            <td style="font-family: monospace;">{{ $k->rating }} / {{ $total_rating }}</td>
                            <td class="font-bold text-primary">{{ number_format($k->bobot, 4) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background: rgba(255, 255, 255, 0.05); font-weight: bold;">
                        <td colspan="3" class="text-right">Total Rating:</td>
                        <td>{{ $total_rating }}</td>
                        <td>-</td>
                        <td class="text-primary">1.0000 (100%)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- LANGKAH 2: Matriks Penilaian Asli -->
    <div class="card">
        <div class="card-header">
            <h3>Langkah 2: Matriks Nilai Evaluasi Asli (C)</h3>
            <p>Skor mentah hasil penilaian evaluasi supplier</p>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        @foreach($kriterias as $k)
                            <th class="text-center">{{ $k->id }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($nilai_matrix as $row)
                        <tr>
                            <td><b>{{ $row['nama'] }}</b></td>
                            @foreach($kriterias as $k)
                                <td class="text-center">{{ $row['nilai'][$k->id] }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- LANGKAH 3: Penentuan Nilai Min & Max -->
    <div class="card">
        <div class="card-header">
            <h3>Langkah 3: Menentukan Nilai Minimal & Maksimal per Kriteria</h3>
            <p>Batas atas (C<sub>max</sub>) dan batas bawah (C<sub>min</sub>) untuk normalisasi utility</p>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        @foreach($kriterias as $k)
                            <th class="text-center">{{ $k->id }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Nilai Maksimal (C<sub>max</sub>)</b></td>
                        @foreach($kriterias as $k)
                            <td class="text-center font-bold text-success">{{ $min_max[$k->id]['max'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td><b>Nilai Minimal (C<sub>min</sub>)</b></td>
                        @foreach($kriterias as $k)
                            <td class="text-center font-bold text-danger">{{ $min_max[$k->id]['min'] }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- LANGKAH 4: Matriks Nilai Utility (u) -->
    <div class="card">
        <div class="card-header">
            <h3>Langkah 4: Matriks Nilai Utility (u)</h3>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                Rumus Benefit: <b>u(a) = ((C - C<sub>min</sub>) / (C<sub>max</sub> - C<sub>min</sub>)) * 100</b> <br>
                Rumus Cost: <b>u(a) = ((C<sub>max</sub> - C) / (C<sub>max</sub> - C<sub>min</sub>)) * 100</b>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        @foreach($kriterias as $k)
                            <th class="text-center">{{ $k->id }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($utility_matrix as $row)
                        <tr>
                            <td><b>{{ $row['nama'] }}</b></td>
                            @foreach($kriterias as $k)
                                <td class="text-center font-bold" style="color: var(--primary);">
                                    {{ number_format($row['values'][$k->id], 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- LANGKAH 5: Matriks Nilai Terbobot (W * u) -->
    <div class="card">
        <div class="card-header">
            <h3>Langkah 5: Matriks Nilai Utility Terbobot (W &times; u)</h3>
            <p>Hasil kali bobot normalisasi kriteria dengan nilai utility</p>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        @foreach($kriterias as $k)
                            <th class="text-center">{{ $k->id }} (W: {{ number_format($k->bobot, 2) }})</th>
                        @endforeach
                        <th class="text-right">Skor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weighted_matrix as $row)
                        <tr>
                            <td><b>{{ $row['nama'] }}</b></td>
                            @foreach($kriterias as $k)
                                <td class="text-center">{{ number_format($row['values'][$k->id], 4) }}</td>
                            @endforeach
                            <td class="text-right font-bold text-success" style="font-size: 1.05rem;">{{ number_format($row['total'], 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- LANGKAH 6: Perangkingan Supplier -->
    <div class="card" style="border: 2px solid var(--primary); box-shadow: var(--shadow-hover);">
        <div class="card-header flex-between" style="background: linear-gradient(135deg, rgba(91, 134, 229, 0.05) 0%, rgba(54, 209, 220, 0.05) 100%); flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="color: var(--primary);"><i class="fas fa-crown"></i> Langkah 6: Hasil Rekomendasi Peringkat</h3>
                <p>Urutan supplier terbaik berdasarkan skor total SMART tertinggi</p>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('admin.smart.report') }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Cetak PDF</a>
                <a href="{{ route('admin.smart.excel') }}" class="btn btn-outline btn-sm" style="border-color: #4a6e5b; color: #4a6e5b;"><i class="fas fa-file-excel"></i> Ekspor Excel</a>
                <span class="badge badge-gold" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;"><i class="fas fa-check-double mr-1"></i> Terhitung Akurat</span>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Peringkat</th>
                        <th>Nama Supplier</th>
                        <th>Skor Total</th>
                        <th>Kategori Kelayakan</th>
                        <th class="text-right" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankings as $index => $rank)
                        @php
                            $rankColor = 'var(--text-main)';
                            if ($index === 0) $rankColor = '#f59e0b'; // Gold
                            elseif ($index === 1) $rankColor = '#94a3b8'; // Silver
                            elseif ($index === 2) $rankColor = '#b45309'; // Bronze
                        @endphp
                        <tr>
                            <td>
                                <div style="width: 35px; height: 35px; border-radius: 50%; background: {{ $index < 3 ? 'rgba(0,0,0,0.03)' : 'transparent' }}; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.15rem; color: {{ $rankColor }};">
                                    {{ $rank['ranking'] }}
                                </div>
                            </td>
                            <td>
                                <b style="font-size: 1.1rem;">{{ $rank['nama'] }}</b>
                                @if($index === 0)
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; font-size: 0.75rem; margin-left: 0.5rem; font-weight: 700;"><i class="fas fa-thumbs-up"></i> Rekomendasi Utama</span>
                                @endif
                            </td>
                            <td><b style="font-size: 1.1rem;" class="text-success">{{ number_format($rank['skor'], 4) }}</b></td>
                            <td>
                                @if($rank['skor'] >= 75)
                                    <span class="badge badge-benefit" style="font-weight: 800;"><i class="fas fa-circle-check"></i> Sangat Layak</span>
                                @elseif($rank['skor'] >= 50)
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); font-weight: 800;"><i class="fas fa-circle-exclamation"></i> Layak</span>
                                @else
                                    <span class="badge badge-cost" style="font-weight: 800;"><i class="fas fa-circle-xmark"></i> Kurang Layak</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.supplier.detail', $rank['id']) }}" class="btn btn-outline btn-sm" style="border-width: 1px; text-decoration: none;"><i class="fas fa-calculator"></i> Detail Rumus</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Animation for calculation processing
    document.getElementById('smartProcessForm').addEventListener('submit', function() {
        document.getElementById('loader').style.display = 'flex';
    });
</script>
@endsection
