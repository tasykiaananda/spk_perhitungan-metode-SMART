/**
 * State Management (Synchronized with Laravel API)
 */
const state = {
    isLoggedIn: false,
    kriteria: [],
    alternatif: [],
    penilaian: [],
    hasil: {
        utilitas: [],
        terbobot: [],
        ranking: []
    }
};

let rankingChartInstance = null;
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/**
 * App Logic & DOM Manipulation
 */
const app = {
    init: async function() {
        this.bindEvents();
        await this.loadAllData();
        this.renderAll();
    },

    loadAllData: async function() {
        try {
            const [resKriteria, resAlternatif, resPenilaian] = await Promise.all([
                fetch('/api/kriteria').then(r => r.json()),
                fetch('/api/alternatif').then(r => r.json()),
                fetch('/api/penilaian').then(r => r.json())
            ]);
            state.kriteria = resKriteria;
            state.alternatif = resAlternatif;
            state.penilaian = resPenilaian;
        } catch (error) {
            console.error('Gagal mengambil data dari server:', error);
            alert('Gagal mengambil data dari server, periksa koneksi database Anda.');
        }
    },

    bindEvents: function() {
        // Login
        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const user = document.getElementById('username').value;
            const pass = document.getElementById('password').value;
            if (user === 'admin' && pass === 'admin123') {
                state.isLoggedIn = true;
                document.getElementById('login-page').classList.remove('active');
                document.getElementById('login-page').classList.add('hidden');
                document.getElementById('main-app').classList.remove('hidden');
                this.navigate('dashboard');
            } else {
                alert('Username atau Password salah!');
            }
        });

        // Logout
        document.getElementById('btn-logout').addEventListener('click', (e) => {
            e.preventDefault();
            state.isLoggedIn = false;
            document.getElementById('main-app').classList.add('hidden');
            document.getElementById('login-page').classList.remove('hidden');
            document.getElementById('login-page').classList.add('active');
            document.getElementById('login-form').reset();
        });

        // Navigation
        const navLinks = document.querySelectorAll('.nav-link[data-target]');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = e.currentTarget.getAttribute('data-target');
                this.navigate(target);
                
                // Active state
                navLinks.forEach(l => l.classList.remove('active'));
                e.currentTarget.classList.add('active');

                // Close mobile sidebar if open
                document.querySelector('.sidebar').classList.remove('open');
            });
        });

        // Mobile Toggle
        document.getElementById('mobile-toggle').addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('open');
        });

        // Proses Hitung
        document.getElementById('btn-proses-hitung').addEventListener('click', () => {
            this.hitungSMART();
            document.getElementById('hasil-perhitungan').classList.remove('hidden');
            this.renderPerhitungan();
        });
    },

    navigate: function(targetId) {
        // Hide all sections
        const sections = document.querySelectorAll('.content-section');
        sections.forEach(sec => {
            sec.classList.remove('active');
            sec.classList.add('hidden');
        });

        // Show target
        const targetSection = document.getElementById(targetId);
        targetSection.classList.remove('hidden');
        // Small delay for animation
        setTimeout(() => targetSection.classList.add('active'), 10);

        // Update Page Title
        const titles = {
            'dashboard': 'Dashboard',
            'kriteria': 'Data Kriteria',
            'alternatif': 'Data Alternatif',
            'penilaian': 'Data Penilaian',
            'perhitungan': 'Proses Perhitungan',
            'hasil': 'Hasil Akhir & Ranking'
        };
        document.getElementById('page-title').innerText = titles[targetId];

        // Update sidebar active link dynamically
        document.querySelectorAll('.nav-link[data-target]').forEach(link => {
            if (link.getAttribute('data-target') === targetId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // Specific actions on page load
        if (targetId === 'hasil' && state.hasil.ranking.length > 0) {
            this.renderChart();
        }
        if (targetId === 'dashboard') {
            this.renderDashboardStats();
        }
    },

    renderAll: function() {
        this.renderKriteria();
        this.renderAlternatif();
        this.renderPenilaian();
        this.renderDashboardStats();
    },

    // Kriteria Functions
    getKriteriaBobot: function() {
        const totalRating = state.kriteria.reduce((sum, k) => sum + k.rating, 0);
        return state.kriteria.map(k => {
            // Kita round ke 2 desimal agar hasil skor konsisten dengan Tabel 4.4
            let rawBobot = totalRating > 0 ? k.rating / totalRating : 0;
            let roundedBobot = Math.round(rawBobot * 100) / 100;
            return {
                ...k,
                bobot: roundedBobot
            };
        });
    },

    renderKriteria: function() {
        const tbody = document.getElementById('table-kriteria');
        tbody.innerHTML = '';
        
        let totalRating = 0;
        let totalBobot = 0;
        
        const kriteriaDenganBobot = this.getKriteriaBobot();

        kriteriaDenganBobot.forEach((k, index) => {
            totalRating += k.rating;
            totalBobot += k.bobot;
            
            const badgeClass = k.jenis === 'Cost' ? 'badge-cost' : 'badge-benefit';
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${k.id}</td>
                <td>${k.nama}</td>
                <td><span class="badge ${badgeClass}">${k.jenis}</span></td>
                <td>
                    <input type="number" class="input-cell" value="${k.rating}" min="1" max="100" 
                           onchange="app.updateRating('${k.id}', this.value)">
                </td>
                <td>${k.bobot.toFixed(2)} (${(k.bobot * 100).toFixed(0)}%)</td>
                <td>
                    <button class="btn btn-outline" style="padding: 0.3rem 0.6rem; color: #e63946; border-color: #e63946;" onclick="app.hapusKriteria('${k.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('total-rating').innerText = totalRating;
        document.getElementById('total-bobot').innerText = totalBobot.toFixed(2) + ' (100%)';
    },

    updateRating: async function(id, value) {
        let val = parseInt(value);
        if(isNaN(val) || val <= 0) val = 1;

        try {
            const res = await fetch(`/api/kriteria/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ rating: val })
            }).then(r => r.json());

            if (res.status === 'success') {
                const kIdx = state.kriteria.findIndex(k => k.id === id);
                state.kriteria[kIdx].rating = val;
                this.renderKriteria();
                document.getElementById('hasil-perhitungan').classList.add('hidden');
            }
        } catch (error) {
            console.error(error);
            alert('Gagal mengupdate rating kriteria.');
        }
    },

    tambahKriteria: async function() {
        const id = document.getElementById('new-kriteria-id').value.trim();
        const nama = document.getElementById('new-kriteria-nama').value.trim();
        const jenis = document.getElementById('new-kriteria-jenis').value;
        let rating = parseInt(document.getElementById('new-kriteria-rating').value);

        if (!id || !nama || isNaN(rating) || rating <= 0) {
            alert('Lengkapi data kriteria dengan benar (Rating harus angka positif).');
            return;
        }

        if (state.kriteria.find(k => k.id === id)) {
            alert('Kode Kriteria sudah ada!');
            return;
        }

        try {
            const res = await fetch('/api/kriteria', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id, nama, jenis, rating })
            }).then(r => r.json());

            if (res.status === 'success') {
                state.kriteria.push(res.data);
                
                // Bersihkan input
                document.getElementById('new-kriteria-id').value = '';
                document.getElementById('new-kriteria-nama').value = '';
                document.getElementById('new-kriteria-rating').value = '';

                this.renderKriteria();
                await this.loadAllData(); // Reload all to synch penilaian structural table
                this.renderPenilaian();
                this.renderDashboardStats();
                document.getElementById('hasil-perhitungan').classList.add('hidden');
            }
        } catch (error) {
            console.error(error);
            alert('Gagal menambahkan kriteria.');
        }
    },

    hapusKriteria: async function(id) {
        if (confirm('Hapus Kriteria ' + id + '? Data penilaian terkait akan hilang.')) {
            try {
                const res = await fetch(`/api/kriteria/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                }).then(r => r.json());

                if (res.status === 'success') {
                    state.kriteria = state.kriteria.filter(k => k.id !== id);
                    
                    // Hapus penilaian kriteria ini dari state local
                    state.penilaian.forEach(p => {
                        delete p[id];
                    });

                    this.renderKriteria();
                    this.renderPenilaian();
                    this.renderDashboardStats();
                    document.getElementById('hasil-perhitungan').classList.add('hidden');
                }
            } catch (error) {
                console.error(error);
                alert('Gagal menghapus kriteria.');
            }
        }
    },

    // Alternatif Functions
    renderAlternatif: function() {
        const tbody = document.getElementById('table-alternatif');
        tbody.innerHTML = '';
        state.alternatif.forEach((a, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${a.nama}</td>
                <td>
                    <button class="btn btn-outline" style="padding: 0.3rem 0.6rem; color: #e63946; border-color: #e63946;" onclick="app.hapusAlternatif(${a.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    tambahAlternatif: async function() {
        const nama = document.getElementById('new-alt-nama').value.trim();
        if (!nama) {
            alert('Nama Supplier tidak boleh kosong!');
            return;
        }

        try {
            const res = await fetch('/api/alternatif', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ nama: nama })
            }).then(r => r.json());

            if (res.status === 'success') {
                state.alternatif.push(res.data);
                
                // Bersihkan input
                document.getElementById('new-alt-nama').value = '';

                this.renderAlternatif();
                await this.loadAllData(); // Reload all to refresh penilaian state
                this.renderPenilaian();
                this.renderDashboardStats();
                document.getElementById('hasil-perhitungan').classList.add('hidden');
            }
        } catch (error) {
            console.error(error);
            alert('Gagal menambahkan alternatif.');
        }
    },

    hapusAlternatif: async function(id) {
        if (confirm('Hapus Supplier ini?')) {
            try {
                const res = await fetch(`/api/alternatif/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                }).then(r => r.json());

                if (res.status === 'success') {
                    state.alternatif = state.alternatif.filter(a => a.id !== id);
                    state.penilaian = state.penilaian.filter(p => p.alt_id !== id);

                    this.renderAlternatif();
                    this.renderPenilaian();
                    this.renderDashboardStats();
                    document.getElementById('hasil-perhitungan').classList.add('hidden');
                }
            } catch (error) {
                console.error(error);
                alert('Gagal menghapus alternatif.');
            }
        }
    },

    // Penilaian Functions
    renderPenilaian: function() {
        const thead = document.getElementById('header-penilaian');
        const tbody = document.getElementById('table-penilaian');
        
        // Setup Header
        thead.innerHTML = '<th>No</th><th>Supplier</th>';
        state.kriteria.forEach(k => {
            thead.innerHTML += `<th>${k.id}</th>`;
        });

        // Setup Body
        tbody.innerHTML = '';
        state.alternatif.forEach((alt, index) => {
            // Find existing penilaian
            const p = state.penilaian.find(x => x.alt_id === alt.id) || { alt_id: alt.id };
            
            let tr = document.createElement('tr');
            let html = `<td>${index + 1}</td><td>${alt.nama}</td>`;
            
            state.kriteria.forEach(k => {
                const val = p[k.id] || 0;
                html += `<td>
                    <input type="number" class="input-cell" value="${val}" step="0.01"
                           onchange="app.updatePenilaian(${alt.id}, '${k.id}', this.value)">
                </td>`;
            });
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });
    },

    updatePenilaian: async function(alt_id, kriteria_id, value) {
        let val = parseFloat(value);
        if(isNaN(val)) val = 0;
        
        try {
            const res = await fetch('/api/penilaian', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    alternatif_id: alt_id,
                    kriteria_id: kriteria_id,
                    nilai: val
                })
            }).then(r => r.json());

            if (res.status === 'success') {
                let pIndex = state.penilaian.findIndex(x => x.alt_id === alt_id);
                if (pIndex > -1) {
                    state.penilaian[pIndex][kriteria_id] = val;
                } else {
                    let newP = { alt_id: alt_id };
                    newP[kriteria_id] = val;
                    state.penilaian.push(newP);
                }
                document.getElementById('hasil-perhitungan').classList.add('hidden');
            }
        } catch (error) {
            console.error(error);
            alert('Gagal mengupdate nilai penilaian.');
        }
    },

    // Dashboard Stats
    renderDashboardStats: function() {
        document.getElementById('stat-kriteria').innerText = state.kriteria.length;
        document.getElementById('stat-alternatif').innerText = state.alternatif.length;
        
        if (state.hasil.ranking.length > 0) {
            document.getElementById('stat-terbaik').innerText = state.hasil.ranking[0].nama;
        } else {
            document.getElementById('stat-terbaik').innerText = '-';
        }
    },

    /**
     * CORE SMART CALCULATION LOGIC
     */
    hitungSMART: function() {
        const kriteria = this.getKriteriaBobot();
        
        // 1. Cari Nilai Min & Max per Kriteria dinamis dari data penilaian yang ada
        const minMax = {};
        kriteria.forEach(k => {
            const values = state.penilaian.map(p => parseFloat(p[k.id]) || 0);
            minMax[k.id] = {
                min: values.length > 0 ? Math.min(...values) : 0,
                max: values.length > 0 ? Math.max(...values) : 0
            };
        });

        const utilitasData = [];
        const terbobotData = [];
        const rankingData = [];

        state.alternatif.forEach(alt => {
            const p = state.penilaian.find(x => x.alt_id === alt.id);
            if (!p) return;

            let utilitasRow = { id: alt.id, nama: alt.nama };
            let terbobotRow = { id: alt.id, nama: alt.nama };
            let totalSkor = 0;

            kriteria.forEach(k => {
                const C = parseFloat(p[k.id]) || 0;
                const Cmin = minMax[k.id].min;
                const Cmax = minMax[k.id].max;
                
                let u = 0;
                
                // Mencegah pembagian dengan nol jika min == max
                if (Cmax !== Cmin) {
                    if (k.jenis === 'Cost') {
                        // Formula Cost: ((Cmax - C) / (Cmax - Cmin)) * 100
                        u = ((Cmax - C) / (Cmax - Cmin)) * 100;
                    } else {
                        // Formula Benefit: ((C - Cmin) / (Cmax - Cmin)) * 100
                        u = ((C - Cmin) / (Cmax - Cmin)) * 100;
                    }
                } else {
                    u = 100; // jika semua nilai sama, utilitas max
                }

                utilitasRow[k.id] = u;
                
                // Kontribusi terbobot (Wi * ui)
                const terbobot = k.bobot * u;
                terbobotRow[k.id] = terbobot;
                
                totalSkor += terbobot;
            });

            utilitasData.push(utilitasRow);
            terbobotData.push(terbobotRow);
            
            rankingData.push({
                id: alt.id,
                nama: alt.nama,
                skor: totalSkor
            });
        });

        // Urutkan ranking dari skor tertinggi ke terendah
        rankingData.sort((a, b) => b.skor - a.skor);

        // Simpan ke state
        state.hasil = {
            utilitas: utilitasData,
            terbobot: terbobotData,
            ranking: rankingData
        };

        this.renderHasil();
    },

    renderPerhitungan: function() {
        // Setup Headers
        const utilHead = document.getElementById('header-utilitas');
        const terbobotHead = document.getElementById('header-terbobot');
        
        let headHtml = '<th>Supplier</th>';
        state.kriteria.forEach(k => headHtml += `<th>${k.id}</th>`);
        
        utilHead.innerHTML = headHtml;
        terbobotHead.innerHTML = headHtml + '<th>Total Skor</th>';

        // Setup Body Utilitas
        const utilBody = document.getElementById('table-utilitas');
        utilBody.innerHTML = '';
        state.hasil.utilitas.forEach(row => {
            let tr = document.createElement('tr');
            let html = `<td>${row.nama}</td>`;
            state.kriteria.forEach(k => {
                html += `<td>${row[k.id].toFixed(2)}</td>`;
            });
            tr.innerHTML = html;
            utilBody.appendChild(tr);
        });

        // Setup Body Terbobot
        const terbobotBody = document.getElementById('table-terbobot');
        terbobotBody.innerHTML = '';
        state.hasil.terbobot.forEach((row, i) => {
            let tr = document.createElement('tr');
            let html = `<td>${row.nama}</td>`;
            
            let total = 0;
            state.kriteria.forEach(k => {
                html += `<td>${row[k.id].toFixed(2)}</td>`;
                total += row[k.id];
            });
            html += `<td class="font-bold">${total.toFixed(2)}</td>`;
            
            tr.innerHTML = html;
            terbobotBody.appendChild(tr);
        });
    },

    renderHasil: function() {
        const tbody = document.getElementById('table-ranking');
        tbody.innerHTML = '';
        
        state.hasil.ranking.forEach((r, index) => {
            const tr = document.createElement('tr');
            
            let ketHtml = '';
            if (index === 0) {
                ketHtml = '<span class="badge badge-gold"><i class="fas fa-crown"></i> Rekomendasi Terbaik</span>';
            }

            tr.innerHTML = `
                <td><b>${index + 1}</b></td>
                <td>${r.nama}</td>
                <td class="font-bold">${r.skor.toFixed(2)}</td>
                <td>${ketHtml}</td>
            `;
            tbody.appendChild(tr);
        });
    },

    renderChart: function() {
        const ctx = document.getElementById('rankingChart').getContext('2d');
        
        const labels = state.hasil.ranking.map(r => r.nama);
        const data = state.hasil.ranking.map(r => parseFloat(r.skor.toFixed(2)));
        
        // Colors from theme
        const bgColors = data.map((_, i) => i === 0 ? 'rgba(246, 198, 208, 0.8)' : 'rgba(168, 208, 230, 0.7)');
        const borderColors = data.map((_, i) => i === 0 ? '#F6C6D0' : '#A8D0E6');

        if (rankingChartInstance) {
            rankingChartInstance.destroy();
        }

        rankingChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Akhir',
                    data: data,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
};

// Initialize App
document.addEventListener('DOMContentLoaded', () => {
    app.init();
});
