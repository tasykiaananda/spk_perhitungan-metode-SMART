@extends('layouts.admin')

@section('title', 'Backup & Reset Database')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Backup & Reset</span>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">

    <!-- Backup Card -->
    <div class="card">
        <div class="card-header">
            <h3>Ekspor Backup Database</h3>
            <p>Unduh seluruh data sistem dalam format JSON terstruktur</p>
        </div>
        <div class="card-body">
            <div style="text-align: center; padding: 1.5rem 0;">
                <i class="fas fa-file-export" style="font-size: 3.5rem; color: var(--primary); opacity: 0.8; margin-bottom: 1.5rem; display: block;"></i>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.6;">
                    Backup ini menyimpan seluruh data kriteria, supplier, matriks penilaian, riwayat perhitungan, dan log audit sistem. Anda dapat mengunduhnya kapan saja dan menyimpannya sebagai arsip.
                </p>
                <a href="{{ route('admin.backup.download') }}" class="btn btn-primary w-full"><i class="fas fa-download"></i> Unduh File Backup (.json)</a>
            </div>
        </div>
    </div>

    <!-- Restore Card -->
    <div class="card">
        <div class="card-header">
            <h3>Impor / Pulihkan Database</h3>
            <p>Kembalikan data sistem dari file backup JSON sebelumnya</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data" id="restoreForm">
                @csrf
                <div style="padding: 1rem 0;">
                    <div style="border: 2px dashed var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; text-align: center; background: rgba(0,0,0,0.02); margin-bottom: 1rem; position: relative;">
                        <i class="fas fa-file-import" style="font-size: 2rem; color: var(--secondary); margin-bottom: 0.8rem; display: block;"></i>
                        <span id="file-label" style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">Pilih file backup JSON</span>
                        <input type="file" name="backup_file" id="backup_file" accept=".json" required style="position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;" onchange="updateFileLabel(this)">
                    </div>
                    @error('backup_file')
                        <span style="color: #f43f5e; font-size: 0.8rem; margin-bottom: 1rem; display: block;">{{ $message }}</span>
                    @enderror
                    
                    <div class="card" style="border-left: 4px solid #f43f5e; background: rgba(244, 63, 94, 0.08); padding: 0.8rem; font-size: 0.8rem; margin-bottom: 1rem; box-shadow: none;">
                        <i class="fas fa-triangle-exclamation mr-1" style="color: #f43f5e;"></i> <b>PERINGATAN:</b> Mengimpor database akan menghapus seluruh data sistem saat ini secara permanen!
                    </div>

                    <button type="submit" class="btn btn-secondary w-full"><i class="fas fa-upload"></i> Pulihkan Database</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Card -->
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header flex-between" style="border-bottom: 1px solid rgba(244, 63, 94, 0.2);">
            <div>
                <h3 class="text-danger"><i class="fas fa-radiation"></i> Reset ke Data Awal (Bawaan)</h3>
                <p>Mengembalikan database ke kondisi awal seeder asli</p>
            </div>
            <span class="badge badge-cost" style="font-weight: 800;">Danger Zone</span>
        </div>
        <div class="card-body flex-between" style="flex-wrap: wrap; gap: 1.5rem;">
            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 650px; line-height: 1.6; margin: 0;">
                Tindakan ini akan mengosongkan seluruh tabel data (supplier, kriteria, penilaian, riwayat hitung, dan log aktivitas) kemudian mengisi kembali kriteria dasar dan alternatif awal (seperti data bawaan pabrik).
            </p>
            <form action="{{ route('admin.backup.reset') }}" method="POST" id="resetForm">
                @csrf
                <button type="button" onclick="confirmReset()" class="btn btn-danger"><i class="fas fa-trash-arrow-up"></i> Reset Sekarang</button>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Update file input naming label
    function updateFileLabel(input) {
        const label = document.getElementById('file-label');
        if (input.files && input.files[0]) {
            label.innerText = input.files[0].name;
            label.style.color = 'var(--primary)';
        } else {
            label.innerText = 'Pilih file backup JSON';
            label.style.color = 'var(--text-muted)';
        }
    }

    // Confirm Restore Submit
    document.getElementById('restoreForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Tindakan ini akan menimpa seluruh data database yang ada saat ini!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#36D1DC',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Pulihkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('loader').style.display = 'flex';
                this.submit();
            }
        });
    });

    // Confirm Reset Submit
    function confirmReset() {
        Swal.fire({
            title: 'RESET DATA BAWAAN?',
            text: 'Seluruh data kustom Anda akan terhapus dan sistem dikembalikan ke kondisi awal bawaan database seeder!',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset Data!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('loader').style.display = 'flex';
                document.getElementById('resetForm').submit();
            }
        });
    }
</script>
@endsection
