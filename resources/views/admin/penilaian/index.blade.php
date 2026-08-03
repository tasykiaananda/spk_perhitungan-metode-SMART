@extends('layouts.admin')

@section('title', 'Nilai Penilaian')

@section('breadcrumbs')
<span class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></span>
<span class="breadcrumb-item active">Penilaian Matrix</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Matriks Penilaian Supplier</h3>
        <p>Input dan edit nilai evaluasi untuk setiap kriteria per supplier</p>
    </div>
    
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        @foreach($kriterias as $k)
                            <th class="text-center">{{ $k->nama }} <br><span class="badge" style="font-size:0.75rem; background:rgba(0,0,0,0.05); color:var(--text-muted);">{{ $k->id }}</span></th>
                        @endforeach
                        <th class="text-right" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alternatifs as $alt)
                        <tr>
                            <td><b style="font-size: 1.05rem;">{{ $alt->nama }}</b></td>
                            @foreach($kriterias as $k)
                                @php
                                    $score = $matrix[$alt->id]['scores'][$k->id] ?? 0;
                                @endphp
                                <td class="text-center font-bold" style="{{ $score == 0 ? 'color: #f43f5e;' : '' }}">
                                    {{ $score }}
                                </td>
                            @endforeach
                            <td class="text-right">
                                <button class="btn btn-primary btn-sm edit-score-btn" 
                                        data-id="{{ $alt->id }}" 
                                        data-nama="{{ $alt->nama }}" 
                                        data-scores='@json($matrix[$alt->id]['scores'] ?? [])'>
                                    <i class="fas fa-edit"></i> Edit Nilai
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($kriterias) + 2 }}" class="text-center p-4">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-triangle-exclamation"></i></div>
                                    <div class="empty-state-title">Data Kosong</div>
                                    <div class="empty-state-description">Silakan daftarkan supplier dan kriteria terlebih dahulu.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Scores -->
<div id="editScoresModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card" style="width: 100%; max-width: 500px; margin: 0; padding: 2rem; max-height: 90vh; overflow-y: auto;">
        <div class="flex-between mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <div>
                <h3 style="margin: 0;">Input Nilai Penilaian</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;" id="modal-supplier-name"></p>
            </div>
            <button onclick="closeEditScoresModal()" class="btn-icon" style="border: none; background: transparent;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.penilaian.store') }}" method="POST" id="editScoresForm">
            @csrf
            <input type="hidden" name="alternatif_id" id="modal-supplier-id">
            
            <div id="scores-input-container">
                <!-- Inputs are generated dynamically here by JS -->
            </div>

            <div class="flex-between mt-4">
                <button type="button" onclick="closeEditScoresModal()" class="btn btn-outline" style="width: 48%;">Batal</button>
                <button type="submit" class="btn btn-primary" style="width: 48%;">Simpan Nilai</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const kriterias = @json($kriterias);

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.edit-score-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const supplierId = this.getAttribute('data-id');
                const supplierNama = this.getAttribute('data-nama');
                const scores = JSON.parse(this.getAttribute('data-scores') || '{}');
                openEditScoresModal(supplierId, supplierNama, scores);
            });
        });
    });

    function openEditScoresModal(supplierId, supplierNama, scores) {
        document.getElementById('modal-supplier-id').value = supplierId;
        document.getElementById('modal-supplier-name').innerText = "Supplier: " + supplierNama;

        const container = document.getElementById('scores-input-container');
        container.innerHTML = ''; // Clear previous inputs

        kriterias.forEach(k => {
            const currentVal = scores[k.id] !== undefined ? scores[k.id] : 0;
            
            const group = document.createElement('div');
            group.className = 'form-group';
            
            group.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                    <label class="form-label" style="margin: 0;">${k.nama} (${k.id})</label>
                    <span class="badge ${k.jenis === 'Cost' ? 'badge-cost' : 'badge-benefit'}" style="font-size: 0.75rem; padding: 0.15rem 0.5rem;">${k.jenis}</span>
                </div>
                <input type="number" step="any" name="scores[${k.id}]" class="form-control" value="${currentVal}" min="0" required placeholder="Masukkan nilai untuk kriteria ${k.nama}">
            `;
            container.appendChild(group);
        });

        document.getElementById('editScoresModal').style.display = 'flex';
    }

    function closeEditScoresModal() {
        document.getElementById('editScoresModal').style.display = 'none';
    }
</script>
@endsection
