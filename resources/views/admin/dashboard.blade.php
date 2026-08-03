@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumbs')
<span class="breadcrumb-item active">Dashboard</span>
@endsection

@section('content')
<!-- Outer Wrapper to constrain height -->
<div class="dashboard-wrapper">

    <!-- Header Row -->
    <div class="d-flex justify-content-between align-items-end" style="flex-shrink: 0; padding: 0 0.25rem;">
        <div>
            <h5 style="margin: 0; font-weight: 700; font-size: 1.15rem; color: var(--text-main);">Selamat Datang, {{ Auth::user()->name }}!</h5>
            <div style="font-size: 0.75rem; color: var(--text-muted);">Sistem Pendukung Keputusan Pemilihan Supplier (SMART)</div>
        </div>
        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted);">
            <i class="far fa-calendar-alt text-primary me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
        </div>
    </div>

    <!-- Stats Grid (Compact) -->
    <div class="row g-2" style="flex-shrink: 0;">
        <div class="col-md-3">
            <a href="{{ route('admin.supplier.index') }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                <div class="card stat-card-hover d-flex flex-row align-items-center gap-3 h-100" style="padding: 0.6rem 1rem; border-radius: 8px; border-left: 4px solid #7A6C68; background: var(--card-bg);">
                    <div style="font-size: 1.25rem; color: #7A6C68;"><i class="fas fa-truck-ramp-box"></i></div>
                    <div>
                        <div style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Supplier</div>
                        <div style="font-size: 1.15rem; font-weight: 800; line-height: 1;">{{ $totalSupplier }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.kriteria.index') }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                <div class="card stat-card-hover d-flex flex-row align-items-center gap-3 h-100" style="padding: 0.6rem 1rem; border-radius: 8px; border-left: 4px solid #8E7361; background: var(--card-bg);">
                    <div style="font-size: 1.25rem; color: #8E7361;"><i class="fas fa-list-check"></i></div>
                    <div>
                        <div style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Kriteria</div>
                        <div style="font-size: 1.15rem; font-weight: 800; line-height: 1;">{{ $totalKriteria }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.penilaian.index') }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                <div class="card stat-card-hover d-flex flex-row align-items-center gap-3 h-100" style="padding: 0.6rem 1rem; border-radius: 8px; border-left: 4px solid var(--sage-green); background: var(--card-bg);">
                    <div style="font-size: 1.25rem; color: var(--sage-green);"><i class="fas fa-pen-to-square"></i></div>
                    <div>
                        <div style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Data Penilaian</div>
                        <div style="font-size: 1.15rem; font-weight: 800; line-height: 1;">{{ $totalPenilaian }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.smart.index') }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                <div class="card stat-card-hover d-flex flex-row align-items-center gap-3 h-100" style="padding: 0.6rem 1rem; border-radius: 8px; border-left: 4px solid var(--mustard-gold); background: var(--card-bg);">
                    <div style="font-size: 1.25rem; color: var(--mustard-gold);"><i class="fas fa-medal"></i></div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Supplier Terbaik</div>
                        <div style="font-size: 0.95rem; font-weight: 800; line-height: 1.2; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" title="{{ $supplierTerbaik }}">{{ $supplierTerbaik ?: '-' }}</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-2 flex-grow-1" style="min-height: 0;">
        
        <!-- Grafik Ranking -->
        <div class="col-md-7 d-flex flex-column h-100">
            <div class="card flex-grow-1 d-flex flex-column mb-0" style="border-radius: 8px; overflow: hidden;">
                <div class="card-header" style="padding: 0.6rem 1rem; border-bottom: 1px solid var(--border-color); background: var(--table-header-bg);">
                    <h6 style="margin: 0; font-size: 0.85rem; font-weight: 700;"><i class="fas fa-chart-bar text-primary me-2"></i> Grafik Perangkingan SMART</h6>
                </div>
                <div class="card-body p-2" style="position: relative; flex-grow: 1; min-height: 0; display: flex; flex-direction: column;">
                    <div style="position: relative; flex-grow: 1; width: 100%;">
                        <canvas id="barChartRanking"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 Table -->
        <div class="col-md-5 d-flex flex-column h-100">
            <div class="card flex-grow-1 d-flex flex-column mb-0" style="border-radius: 8px; overflow: hidden;">
                <div class="card-header d-flex justify-content-between align-items-center" style="padding: 0.6rem 1rem; border-bottom: 1px solid var(--border-color); background: var(--table-header-bg);">
                    <h6 style="margin: 0; font-size: 0.85rem; font-weight: 700;"><i class="fas fa-trophy text-warning me-2"></i> Top 5 Supplier</h6>
                    <a href="{{ route('admin.smart.index') }}" class="btn btn-primary btn-sm" style="font-size: 0.65rem; padding: 0.15rem 0.5rem; border-radius: 4px;">Lihat Hasil</a>
                </div>
                <div class="card-body p-0" style="overflow-y: auto; flex-grow: 1;">
                    <table class="table table-hover mb-0" style="font-size: 0.75rem; color: var(--text-main);">
                        <thead style="position: sticky; top: 0; background: var(--card-bg); z-index: 1;">
                            <tr>
                                <th class="text-center" style="padding: 0.5rem; border-bottom: 1px solid var(--border-color);">#</th>
                                <th style="padding: 0.5rem; border-bottom: 1px solid var(--border-color);">Nama Supplier</th>
                                <th style="padding: 0.5rem; border-bottom: 1px solid var(--border-color);">Skor</th>
                                <th class="text-center" style="padding: 0.5rem; border-bottom: 1px solid var(--border-color);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(array_slice($rankings, 0, 5) as $index => $rank)
                                <tr>
                                    <td class="text-center align-middle" style="padding: 0.4rem; border-bottom: 1px solid var(--border-color);">
                                        @if($index === 0) 🥇
                                        @elseif($index === 1) 🥈
                                        @elseif($index === 2) 🥉
                                        @else <span style="font-weight: bold; color: var(--text-muted);">{{ $index + 1 }}</span> @endif
                                    </td>
                                    <td class="align-middle" style="padding: 0.4rem; font-weight: 600; border-bottom: 1px solid var(--border-color);">{{ $rank['nama'] }}</td>
                                    <td class="align-middle" style="padding: 0.4rem; font-family: monospace; font-weight: bold; color: var(--primary); border-bottom: 1px solid var(--border-color);">{{ number_format($rank['skor'], 3) }}</td>
                                    <td class="text-center align-middle" style="padding: 0.4rem; border-bottom: 1px solid var(--border-color);">
                                        @if($rank['skor'] >= 75)
                                            <span style="background: rgba(142, 115, 97, 0.15); color: var(--mustard-gold); padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.65rem;">Layak</span>
                                        @else
                                            <span style="background: rgba(122, 108, 104, 0.15); color: var(--light-gray); padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.65rem;">Kurang</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center" style="padding: 1.5rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">Belum ada data penilaian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    
    <div id="dashboard-data" data-rankings='@json($rankings)' style="display: none;"></div>
</div>

<style>
    /* Prevent body scrolling on dashboard */
    body {
        overflow: hidden !important;
    }
    
    /* Hide footer on dashboard to maximize screen space */
    footer {
        display: none !important;
    }

    .content-section {
        padding: 0.75rem !important;
        height: calc(100vh - var(--header-height));
        overflow: hidden !important;
        display: flex;
        flex-direction: column;
    }
    
    .dashboard-wrapper {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        height: 100%;
    }

    .stat-card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-hover:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        cursor: pointer;
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
    const barColor = rootStyles.getPropertyValue('--mustard-gold').trim() || '#8E7361';

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
