@extends('layouts.admin')

@section('title', 'Kelola Kriteria')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Kriteria</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header flex-between">
        <div>
            <h3>Daftar Kriteria SMART</h3>
            <p>Konfigurasi bobot dan tipe kriteria untuk perhitungan supplier</p>
        </div>
        <button onclick="openAddModal()" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Kriteria</button>
    </div>
    
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Jenis Kriteria</th>
                        <th>Rating (Bobot Asli)</th>
                        <th>Bobot Normalisasi (W)</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kriterias as $k)
                        <tr>
                            <td><span class="badge" style="background: rgba(91, 134, 229, 0.15); color: var(--primary); font-weight: 800;">{{ $k->id }}</span></td>
                            <td><b>{{ $k->nama }}</b></td>
                            <td>
                                @if($k->jenis === 'Cost')
                                    <span class="badge badge-cost"><i class="fas fa-arrow-down-long"></i> Cost</span>
                                @else
                                    <span class="badge badge-benefit"><i class="fas fa-arrow-up-long"></i> Benefit</span>
                                @endif
                            </td>
                            <td><b>{{ $k->rating }}</b></td>
                            <td class="text-primary font-bold">
                                {{ number_format($k->bobot, 4) }} 
                                <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-muted);">({{ number_format($k->bobot * 100, 2) }}%)</span>
                            </td>
                            <td class="text-right" style="white-space: nowrap;">
                                <div class="d-flex justify-content-end gap-2 align-items-center">
                                    <button onclick="openEditModal('{{ $k->id }}', '{{ $k->nama }}', '{{ $k->jenis }}', '{{ $k->rating }}')" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                    <button onclick="confirmDelete('{{ $k->id }}', '{{ $k->nama }}')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                                    <div class="empty-state-title">Belum ada Kriteria</div>
                                    <div class="empty-state-description">Silakan tambah kriteria baru untuk memulai penilaian.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kriteria -->
<div id="addModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card" style="width: 100%; max-width: 480px; margin: 0; padding: 2rem;">
        <div class="flex-between mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 style="margin: 0;">Tambah Kriteria Baru</h3>
            <button onclick="closeAddModal()" class="btn-icon" style="border: none; background: transparent;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.kriteria.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="add-id" class="form-label">Kode Kriteria (Contoh: K1, K2)</label>
                <input type="text" name="id" id="add-id" class="form-control" placeholder="K6" required>
            </div>
            <div class="form-group">
                <label for="add-nama" class="form-label">Nama Kriteria</label>
                <input type="text" name="nama" id="add-nama" class="form-control" placeholder="Cita Rasa" required>
            </div>
            <div class="form-group">
                <label for="add-jenis" class="form-label">Jenis Kriteria</label>
                <select name="jenis" id="add-jenis" class="form-control" required>
                    <option value="Benefit">Benefit (Lebih tinggi lebih baik)</option>
                    <option value="Cost">Cost (Lebih rendah lebih baik)</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="add-rating" class="form-label">Rating / Bobot Asli (1 - 100)</label>
                <input type="number" name="rating" id="add-rating" class="form-control" min="1" max="100" placeholder="80" required>
            </div>
            <div class="flex-between">
                <button type="button" onclick="closeAddModal()" class="btn btn-outline" style="width: 48%;">Batal</button>
                <button type="submit" class="btn btn-primary" style="width: 48%;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kriteria -->
<div id="editModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card" style="width: 100%; max-width: 480px; margin: 0; padding: 2rem;">
        <div class="flex-between mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 style="margin: 0;">Edit Kriteria</h3>
            <button onclick="closeEditModal()" class="btn-icon" style="border: none; background: transparent;"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Kode Kriteria</label>
                <input type="text" id="edit-id" class="form-control" style="background: rgba(0,0,0,0.05);" readonly>
            </div>
            <div class="form-group">
                <label for="edit-nama" class="form-label">Nama Kriteria</label>
                <input type="text" name="nama" id="edit-nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="edit-jenis" class="form-label">Jenis Kriteria</label>
                <select name="jenis" id="edit-jenis" class="form-control" required>
                    <option value="Benefit">Benefit (Lebih tinggi lebih baik)</option>
                    <option value="Cost">Cost (Lebih rendah lebih baik)</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="edit-rating" class="form-label">Rating / Bobot Asli (1 - 100)</label>
                <input type="number" name="rating" id="edit-rating" class="form-control" min="1" max="100" required>
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
    // Modal controls
    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }
    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    function openEditModal(id, nama, jenis, rating) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-jenis').value = jenis;
        document.getElementById('edit-rating').value = rating;
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('/admin/kriteria') }}/" + id;
        
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Delete validation warning
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Kriteria "${nama}" (${id}) dan semua nilai penilaian terkait akan dihapus secara permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = "{{ url('/admin/kriteria') }}/" + id;
                document.getElementById('loader').style.display = 'flex';
                form.submit();
            }
        });
    }
</script>
@endsection
