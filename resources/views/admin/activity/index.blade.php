@extends('layouts.admin')

@section('title', 'Log Aktivitas Admin')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Log Aktivitas</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header flex-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3>Audit Log Aktivitas Admin</h3>
            <p>Daftar seluruh riwayat log perubahan dan operasional yang dilakukan administrator</p>
        </div>
        <div>
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" id="activitySearchInput" class="form-control form-control-sm" placeholder="Cari log..." style="padding-left: 32px; width: 250px;">
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="data-table" id="activityTable">
                <thead>
                    <tr>
                        <th style="width: 200px;">Waktu Kejadian</th>
                        <th>Pelaku (Username)</th>
                        <th>Aktivitas Sistem</th>
                        <th>Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $log)
                        <tr>
                            <td>
                                <b>{{ $log->created_at->format('d M Y') }}</b> <br>
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="far fa-clock mr-1"></i> {{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(91, 134, 229, 0.1); color: var(--primary); font-weight: 700;">
                                    <i class="fas fa-user-shield mr-1"></i> {{ $log->username }}
                                </span>
                            </td>
                            <td><b style="font-size: 0.95rem; color: var(--text-main);">{{ $log->aktivitas }}</b></td>
                            <td style="font-family: monospace;">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-4">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-shield-halved"></i></div>
                                    <div class="empty-state-title">Belum ada Log</div>
                                    <div class="empty-state-description">Aktivitas admin akan dicatat secara otomatis ketika terjadi perubahan data.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Search Filtering
    document.getElementById('activitySearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#activityTable tbody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            const usernameCell = row.cells[1];
            const activityCell = row.cells[2];
            if (usernameCell && activityCell) {
                const text = usernameCell.textContent.toLowerCase() + " " + activityCell.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
</script>
@endsection
