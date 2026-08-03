@extends('layouts.admin')

@section('title', 'Kelola Supplier')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Supplier</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header flex-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3>Daftar Supplier Biji Kopi</h3>
            <p>Kelola data alternatif supplier kopi untuk proses pemilihan</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" id="supplierSearchInput" class="form-control form-control-sm" placeholder="Cari supplier..." style="padding-left: 32px; width: 220px;">
            </div>
            <button onclick="openAddModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Supplier</button>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="data-table" id="supplierTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nama Supplier</th>
                        <th>Tanggal Terdaftar</th>
                        <th class="text-right" style="width: 320px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alternatifs as $alt)
                        <tr>
                            <td><span class="badge" style="background: rgba(91, 134, 229, 0.15); color: var(--primary); font-weight: 800;">A-{{ $alt->id }}</span></td>
                            <td><b style="font-size: 1.05rem;">{{ $alt->nama }}</b></td>
                            <td>{{ $alt->created_at->format('d M Y') }}</td>
                            <td class="text-right" style="white-space: nowrap;">
                                <div class="d-flex justify-content-end gap-2 align-items-center">
                                    <a href="{{ route('admin.supplier.detail', $alt->id) }}" class="btn btn-outline btn-sm" style="border-width: 1px; padding: 0.35rem 0.8rem; text-decoration: none;"><i class="fas fa-square-root-variable"></i> Detail Hitung</a>
                                    <button onclick="openEditModal('{{ $alt->id }}', '{{ $alt->nama }}')" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                    <button onclick="confirmDelete('{{ $alt->id }}', '{{ $alt->nama }}')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-4">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-users-slash"></i></div>
                                    <div class="empty-state-title">Belum ada Supplier</div>
                                    <div class="empty-state-description">Silakan tambah supplier baru untuk memulai analisis.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Supplier -->
<div id="addModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card" style="width: 100%; max-width: 450px; margin: 0; padding: 2rem;">
        <div class="flex-between mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 style="margin: 0;">Tambah Supplier Baru</h3>
            <button onclick="closeAddModal()" class="btn-icon" style="border: none; background: transparent;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.supplier.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="add-nama" class="form-label">Nama Supplier</label>
                <input type="text" name="nama" id="add-nama" class="form-control" placeholder="PT. Biji Kopi Gayo" required>
            </div>
            <div class="flex-between">
                <button type="button" onclick="closeAddModal()" class="btn btn-outline" style="width: 48%;">Batal</button>
                <button type="submit" class="btn btn-primary" style="width: 48%;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Supplier -->
<div id="editModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card" style="width: 100%; max-width: 450px; margin: 0; padding: 2rem;">
        <div class="flex-between mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 style="margin: 0;">Edit Nama Supplier</h3>
            <button onclick="closeEditModal()" class="btn-icon" style="border: none; background: transparent;"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="edit-nama" class="form-label">Nama Supplier</label>
                <input type="text" name="nama" id="edit-nama" class="form-control" required>
            </div>
            <div class="flex-between">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline" style="width: 48%;">Batal</button>
                <button type="submit" class="btn btn-primary" style="width: 48%;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    // Search Filtering
    document.getElementById('supplierSearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#supplierTable tbody tr');
        
        rows.forEach(row => {
            const nameCell = row.cells[1];
            if (nameCell) {
                const name = nameCell.textContent.toLowerCase();
                if (name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });

    // Modal controls
    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }
    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    function openEditModal(id, nama) {
        document.getElementById('edit-nama').value = nama;
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('/admin/supplier') }}/" + id;
        
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Delete validation warning
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Supplier?',
            text: `Supplier "${nama}" beserta semua nilai penilaiannya akan terhapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = "{{ url('/admin/supplier') }}/" + id;
                document.getElementById('loader').style.display = 'flex';
                form.submit();
            }
        });
    }
</script>
@endsection
