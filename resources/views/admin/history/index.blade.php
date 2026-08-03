@extends('layouts.admin')

@section('title', 'Riwayat Perhitungan')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Riwayat Perhitungan</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header flex-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3>Daftar Riwayat Perhitungan SMART</h3>
            <p>Jurnal catatan hasil perhitungan perangkingan yang telah diproses</p>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" id="historySearchInput" class="form-control form-control-sm" placeholder="Cari riwayat..." style="padding-left: 32px; width: 220px;">
            </div>
            @if(count($histories) > 0)
                <button onclick="confirmClearHistory()" class="btn btn-danger btn-sm"><i class="fas fa-trash-can"></i> Kosongkan Riwayat</button>
            @endif
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="data-table" id="historyTable">
                <thead>
                    <tr>
                        <th>Waktu Proses</th>
                        <th>Jumlah Supplier</th>
                        <th>Jumlah Kriteria</th>
                        <th>Rekomendasi Utama (Terbaik)</th>
                        <th>Skor Tertinggi</th>
                        <th class="text-right" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $hist)
                        <tr>
                            <td>
                                <b>{{ date('d M Y', strtotime($hist->tanggal)) }}</b> <br>
                                <span class="badge" style="background: rgba(0,0,0,0.03); color: var(--text-muted); font-size: 0.75rem; padding: 0.15rem 0.5rem; margin-top: 0.2rem;"><i class="fas fa-clock mr-1"></i> {{ substr($hist->waktu, 0, 5) }} WIB</span>
                            </td>
                            <td>{{ $hist->jumlah_supplier }} Supplier</td>
                            <td>{{ $hist->jumlah_kriteria }} Kriteria</td>
                            <td><b style="font-size: 1.05rem;"><i class="fas fa-crown text-warning mr-1"></i> {{ $hist->supplier_terbaik }}</b></td>
                            <td><b class="text-success" style="font-size: 1.05rem;">{{ number_format($hist->skor_tertinggi, 4) }}</b></td>
                            <td class="text-right">
                                <button onclick="confirmDeleteHistory('{{ $hist->id }}')" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-clock-rotate-left"></i></div>
                                    <div class="empty-state-title">Belum ada Riwayat</div>
                                    <div class="empty-state-description">Lakukan proses perhitungan pada menu SMART untuk mencatat riwayat pertama.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Forms for Action -->
<form id="deleteHistoryForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="clearHistoryForm" action="{{ route('admin.history.clear') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection

@section('scripts')
<script>
    // Search Filtering
    document.getElementById('historySearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#historyTable tbody tr');
        
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            const supplierCell = row.cells[3];
            const dateCell = row.cells[0];
            if (supplierCell && dateCell) {
                const text = supplierCell.textContent.toLowerCase() + " " + dateCell.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });

    // Delete single history
    function confirmDeleteHistory(id) {
        Swal.fire({
            title: 'Hapus Riwayat?',
            text: 'Catatan perhitungan ini akan dihapus secara permanen dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteHistoryForm');
                form.action = "{{ url('/admin/history') }}/" + id;
                document.getElementById('loader').style.display = 'flex';
                form.submit();
            }
        });
    }

    // Clear all history
    function confirmClearHistory() {
        Swal.fire({
            title: 'Kosongkan Semua Riwayat?',
            text: 'Tindakan ini akan menghapus seluruh daftar catatan perhitungan SMART secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Kosongkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('loader').style.display = 'flex';
                document.getElementById('clearHistoryForm').submit();
            }
        });
    }
</script>
@endsection
