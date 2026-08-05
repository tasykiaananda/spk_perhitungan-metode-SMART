@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumbs')
<span class="breadcrumb-item active">Dashboard</span>
@endsection

@section('content')
<!-- Outer Wrapper to constrain height -->
<div class="dashboard-wrapper">

    <!-- Header Row -->
    <div class="welcome-card card p-4 mb-0" style="background: var(--card-bg); border: none; border-radius: var(--radius-lg); box-shadow: var(--shadow);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="animated-coffee-wrapper">
                    <i class="fas fa-mug-hot animated-coffee"></i>
                    <i class="fas fa-wind coffee-steam steam-1"></i>
                    <i class="fas fa-wind coffee-steam steam-2"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-weight: 800; font-size: 1.5rem; color: var(--text-main); margin-bottom: 0.2rem;">Selamat Datang, {{ Auth::user()->name }}!</h2>
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Sistem Pendukung Keputusan Pemilihan Supplier (SMART)</div>
                </div>
            </div>
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--primary); background: var(--primary-light); padding: 0.5rem 1rem; border-radius: 100px;">
                <i class="far fa-calendar-alt me-2"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
    </div>

    <!-- Stats Grid (Modern Pastel) -->
    <div class="stats-grid mb-0">
        <a href="{{ route('admin.supplier.index') }}" style="text-decoration: none; color: inherit;">
            <div class="stat-card stat-card-hover" style="background: var(--card-bg);">
                <div class="stat-icon" style="background: #F1C5C5; color: #8F4C4C;">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label text-uppercase">Total Supplier</div>
                    <div class="stat-value">{{ $totalSupplier }}</div>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.kriteria.index') }}" style="text-decoration: none; color: inherit;">
            <div class="stat-card stat-card-hover" style="background: var(--card-bg);">
                <div class="stat-icon" style="background: #E6CDAA; color: #755B37;">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label text-uppercase">Total Kriteria</div>
                    <div class="stat-value">{{ $totalKriteria }}</div>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.penilaian.index') }}" style="text-decoration: none; color: inherit;">
            <div class="stat-card stat-card-hover" style="background: var(--card-bg);">
                <div class="stat-icon" style="background: #DBCBE5; color: #5F456F;">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label text-uppercase">Data Penilaian</div>
                    <div class="stat-value">{{ $totalPenilaian }}</div>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.smart.index') }}" style="text-decoration: none; color: inherit;">
            <div class="stat-card stat-card-hover" style="background: var(--card-bg);">
                <div class="stat-icon" style="background: #E2C7C4; color: #7C615C;">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label text-uppercase">Supplier Terbaik</div>
                    <div class="stat-value" style="font-size: 1.15rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" title="{{ $supplierTerbaik }}">{{ $supplierTerbaik ?: '-' }}</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mt-2">
        
        <!-- Grafik Ranking -->
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="m-0"><i class="fas fa-chart-bar text-primary me-2"></i> Grafik Perangkingan SMART</h3>
                        <p class="m-0 mt-1">Distribusi skor utilitas akhir untuk seluruh supplier</p>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="barChartRanking"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 Table -->
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="m-0"><i class="fas fa-trophy text-warning me-2"></i> Top 5 Supplier</h3>
                        <p class="m-0 mt-1">Supplier dengan skor tertinggi</p>
                    </div>
                    <a href="{{ route('admin.smart.index') }}" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: rgba(155, 130, 126, 0.05);">
                                <tr>
                                    <th class="text-center px-4 py-3" style="width: 50px;">#</th>
                                    <th class="py-3">Nama Supplier</th>
                                    <th class="py-3">Skor</th>
                                    <th class="text-center px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(array_slice($rankings, 0, 5) as $index => $rank)
                                    <tr>
                                        <td class="text-center align-middle px-4 py-3" style="font-size: 1.1rem;">
                                            @if($index === 0) 🥇
                                            @elseif($index === 1) 🥈
                                            @elseif($index === 2) 🥉
                                            @else <span style="font-weight: 700; color: var(--text-muted);">{{ $index + 1 }}</span> @endif
                                        </td>
                                        <td class="align-middle py-3">
                                            <div style="font-weight: 700; color: var(--text-main);">{{ $rank['nama'] }}</div>
                                        </td>
                                        <td class="align-middle py-3">
                                            <div style="font-family: monospace; font-weight: 800; font-size: 1.1rem; color: var(--primary);">{{ number_format($rank['skor'], 3) }}</div>
                                        </td>
                                        <td class="text-center align-middle px-4 py-3">
                                            @if($rank['skor'] >= 75)
                                                <span class="badge-benefit">Layak</span>
                                            @else
                                                <span class="badge-cost" style="background: rgba(122, 108, 104, 0.15); color: var(--light-gray);">Kurang</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="empty-state">
                                                <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                                                <h4>Belum Ada Data</h4>
                                                <p>Belum ada data penilaian supplier yang dapat dirangking.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="dashboard-data" data-rankings='@json($rankings)' style="display: none;"></div>
</div>

<style>
    /* Clean, unconstrained layout styles */
    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-card-hover {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        border: none !important;
        box-shadow: var(--shadow) !important;
    }
    .stat-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover) !important;
        cursor: pointer;
    }
    
    /* Make sure cards have same height */
    .stats-grid > a {
        display: block;
        height: 100%;
    }
    .stats-grid .stat-card {
        height: 100%;
    }

    .table {
        color: var(--text-main) !important;
    }
    .table td {
        background: transparent !important;
    }
    .table-hover tbody tr:hover td {
        background: var(--table-hover-bg) !important;
    }
</style>
@endsection

@section('scripts')
<script>
    const dataEl = document.getElementById('dashboard-data');
    const rankings = JSON.parse(dataEl.dataset.rankings || '[]');

    function getChartThemeColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            grid: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(75, 57, 53, 0.08)',
            text: isDark ? '#B8A89F' : '#7A6C68'
        };
    }
    let colors = getChartThemeColors();

    const barCtx = document.getElementById('barChartRanking').getContext('2d');
    const barLabels = rankings.map(r => r.nama);
    const barScores = rankings.map(r => parseFloat(r.skor.toFixed(2)));

    // Extract root color for primary bar color
    const rootStyles = getComputedStyle(document.documentElement);
    const barColor = rootStyles.getPropertyValue('--primary').trim() || '#9B827E';

    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Skor SMART',
                data: barScores,
                backgroundColor: barColor,
                borderRadius: 4,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(44, 33, 31, 0.9)',
                    titleFont: { family: 'Inter', size: 11 },
                    bodyFont: { family: 'Inter', size: 11 },
                    padding: 8,
                    cornerRadius: 6
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: colors.grid, drawBorder: false },
                    ticks: { color: colors.text, font: { family: 'Inter', size: 10 }, stepSize: 20 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: colors.text, font: { family: 'Inter', size: 10 } }
                }
            }
        }
    });

    window.addEventListener('theme-changed', () => {
        const updatedColors = getChartThemeColors();
        barChart.options.scales.y.grid.color = updatedColors.grid;
        barChart.options.scales.y.ticks.color = updatedColors.text;
        barChart.options.scales.x.ticks.color = updatedColors.text;
        barChart.update();
    });
</script>
@endsection
