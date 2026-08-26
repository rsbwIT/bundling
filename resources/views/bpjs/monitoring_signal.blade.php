@extends('layout.layoutDashboard')
@section('title', 'BPJS API Monitoring Dashboard')

@section('konten')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Tambahkan Font dan Chart.js -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Styling khusus meniru basoro/bbm dengan Bootstrap */
    .bbm-container {
        font-family: 'Inter', sans-serif;
    }
    .bbm-container .shadow-elegant {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .card-endpoint {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background-color: #ffffff;
    }
    .card-endpoint:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-online {
        background-color: #dcfce7;
        color: #166534;
    }
    .status-offline {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .status-loading {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .status-dot.online { background-color: #22c55e; }
    .status-dot.offline { background-color: #ef4444; }
    .status-dot.loading { background-color: #f59e0b; animation: pulse 1.5s infinite; }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
    
    /* Tambahan efek UI yang lebih modern */
    .card-endpoint {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background-color: #ffffff;
        overflow: hidden;
    }
    .card-endpoint:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }
    .border-online { border-bottom: 4px solid #22c55e !important; }
    .border-offline { border-bottom: 4px solid #ef4444 !important; }
    .border-loading { border-bottom: 4px solid #f59e0b !important; }

    /* Latency Bar Visualizer */
    .latency-bar-bg {
        height: 4px;
        background: #f1f5f9;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 6px;
    }
    .latency-bar-fill {
        height: 100%;
        transition: width 0.3s ease, background-color 0.3s ease;
        width: 0%;
        background: #22c55e;
    }

    /* Chart Style */
    .chart-card {
        background: #ffffff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e2e8f0;
    }
    .chart-title { color: #475569 !important; }
</style>

<div class="bbm-container">


<!-- Chart Section -->
<div class="card mb-3 border-0 chart-card">
    <div class="card-header pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold chart-title mb-0"><i class="fas fa-chart-line me-2 text-primary"></i> Real-time Network Telemetry</h6>
        <button class="btn btn-sm btn-outline-danger py-1 px-3" style="border-radius: 6px;" onclick="bukaModalGangguan()">
            <i class="fas fa-history me-1"></i> Riwayat Downtime
        </button>
    </div>
    <div class="card-body py-2">
        <canvas id="latencyChart" style="height: 250px; width: 100%;"></canvas>
    </div>
</div>

<!-- Endpoints Grid -->
<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 mb-3">
    @foreach($services as $service)
    <div class="col">
        <div class="card card-endpoint border-loading h-100" id="card-{{ $service['id'] }}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="bg-light p-2 rounded text-primary shadow-sm" style="background: linear-gradient(145deg, #ffffff, #f0f0f0);">
                        <i class="{{ $service['icon'] }}"></i>
                    </div>
                    <span class="status-badge status-loading" id="badge-{{ $service['id'] }}" style="padding: 0.2rem 0.6rem; font-size: 0.7rem;">
                        <span class="status-dot loading" id="dot-{{ $service['id'] }}"></span>
                        <span id="text-{{ $service['id'] }}">Checking</span>
                    </span>
                </div>
                <h6 class="fw-bold mb-0 text-truncate mt-1" style="font-size: 0.95rem;">{{ $service['name'] }}</h6>
                <p class="text-muted text-truncate mb-3" style="font-size: 0.75rem;" title="{{ $service['url'] }}">{{ $service['url'] }}</p>
                
                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-auto">
                    <div class="w-100 me-3">
                        <div class="d-flex justify-content-between align-items-end">
                            <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">Response Time</small>
                            <strong class="fs-6" id="latency-{{ $service['id'] }}">- ms</strong>
                        </div>
                        <div class="latency-bar-bg">
                            <div class="latency-bar-fill" id="bar-{{ $service['id'] }}"></div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-light text-secondary rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; background: #f8fafc; border: 1px solid #e2e8f0;" onclick="checkSingle('{{ $service['id'] }}', '{{ $service['name'] }}', '{{ $service['url'] }}')">
                        <i class="fas fa-sync-alt small" id="spin-{{ $service['id'] }}"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Riwayat Gangguan (Bootstrap 4 compatible) -->
<div class="modal fade" id="modalRiwayatGangguan" tabindex="-1" role="dialog" aria-labelledby="modalRiwayatGangguanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRiwayatGangguanLabel"><i class="fas fa-history mr-2"></i> Riwayat Gangguan (Downtime)</h5>
                <button type="button" class="close text-white" onclick="tutupModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-white border-bottom shadow-sm" style="position: sticky; top: 0; z-index: 10;">
                    <form class="d-flex flex-wrap justify-content-between align-items-center w-100 m-0" onsubmit="event.preventDefault(); fetchLogs();">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <label for="filterTanggalAwal" class="mb-0 fw-bold text-secondary" style="font-size: 0.85rem; margin-right: 8px;"><i class="far fa-calendar-alt mr-1"></i> Periode:</label>
                            <input type="date" id="filterTanggalAwal" class="form-control form-control-sm shadow-none" style="width: auto; margin-right: 5px;">
                            <span class="text-muted" style="margin-right: 5px;">s/d</span>
                            <input type="date" id="filterTanggalAkhir" class="form-control form-control-sm shadow-none" style="width: auto; margin-right: 10px;">
                            <button type="submit" class="btn btn-sm btn-primary shadow-sm px-3" style="border-radius: 20px;"><i class="fas fa-search mr-1"></i> Cari</button>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="javascript:void(0)" onclick="cetakPdf()" class="btn btn-sm btn-danger shadow-sm px-3 mr-2" style="border-radius: 20px;">
                                <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                            </a>
                            <button type="button" class="btn btn-sm btn-light border shadow-sm px-3" style="border-radius: 20px;" onclick="document.getElementById('filterTanggalAwal').value=''; document.getElementById('filterTanggalAkhir').value=''; fetchLogs();">
                                <i class="fas fa-undo-alt mr-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-hover table-striped mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light text-secondary" style="position: sticky; top: 0; z-index: 1; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                            <tr>
                                <th class="border-top-0 py-3 text-center" width="5%">No</th>
                                <th class="border-top-0 py-3">Waktu Gangguan</th>
                                <th class="border-top-0 py-3">Waktu Normal</th>
                                <th class="border-top-0 py-3">Layanan</th>
                                <th class="border-top-0 py-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRiwayatGangguan">
                            <tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="tutupModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    const services = @json($services);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Status Data
    let serviceStatus = {};
    let currentAvgLatency = 0; // Menyimpan rata-rata latensi terakhir

    services.forEach(s => {
        serviceStatus[s.id] = { status: 'checking', latency: 0 };
    });

    // Initialize Chart.js
    const ctx = document.getElementById('latencyChart').getContext('2d');
    
    // Warna untuk setiap service
    const chartColors = [
        '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', 
        '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16'
    ];
    
    const datasets = services.map((s, index) => {
        return {
            label: s.name,
            data: [],
            borderColor: chartColors[index % chartColors.length],
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 0
        };
    });

    const latencyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Time labels
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true, 
                    suggestedMax: 200,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: { color: '#64748b', font: { size: 10 } }
                },
                x: { display: false } // Hide x axis for cleaner look
            },
            animation: { duration: 500, easing: 'linear' },
            plugins: {
                legend: { 
                    display: true, 
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 10, family: 'Inter' }, color: '#475569', padding: 12 }
                }
            }
        }
    });

    // Update Chart with Real Data
    function updateChartRealData() {
        const now = new Date().toLocaleTimeString();
        if(latencyChart.data.labels.length > 20) {
            latencyChart.data.labels.shift();
            latencyChart.data.datasets.forEach(ds => ds.data.shift());
        }
        
        latencyChart.data.labels.push(now);

        services.forEach((s, index) => {
            let lat = serviceStatus[s.id].latency;
            let status = serviceStatus[s.id].status;
            
            // Gunakan latency asli (real)
            let displayLatency = (status === 'online' && lat > 0) ? lat : 0;
            
            latencyChart.data.datasets[index].data.push(displayLatency);
        });

        latencyChart.update();
    }

    // Auto refresh ping BPJS every 10 seconds
    setInterval(checkAll, 10000);

    function updateGlobalStats() {
        let connectedCount = 0;
        let totalLatency = 0;
        let validLatencyCount = 0;

        for (const key in serviceStatus) {
            if (serviceStatus[key].status === 'online') {
                connectedCount++;
                if (serviceStatus[key].latency > 0) {
                    totalLatency += serviceStatus[key].latency;
                    validLatencyCount++;
                }
            }
        }

        const avg = validLatencyCount > 0 ? Math.round(totalLatency / validLatencyCount) : 0;
        currentAvgLatency = avg; // Simpan untuk animasi chart
        
        const countEl = document.getElementById('connectedCount');
        if (countEl) countEl.textContent = connectedCount;
        
        const avgEl = document.getElementById('avgLatency');
        if (avgEl) avgEl.textContent = avg;
    }

    function updateUI(id, status, latency) {
        serviceStatus[id] = { status, latency };

        const card = document.getElementById('card-' + id);
        const bar = document.getElementById('bar-' + id);
        const badge = document.getElementById('badge-' + id);
        const dot = document.getElementById('dot-' + id);
        const text = document.getElementById('text-' + id);
        const latText = document.getElementById('latency-' + id);
        const spin = document.getElementById('spin-' + id);

        spin.classList.remove('fa-spin');

        if (status === 'online') {
            card.className = 'card card-endpoint border-online h-100';
            badge.className = 'status-badge status-online';
            dot.className = 'status-dot online';
            text.textContent = 'Connected';
            latText.innerHTML = `${latency} <small class="text-muted fs-6">ms</small>`;
            
            if (bar) {
                let pct = Math.min(100, (latency / 500) * 100);
                bar.style.width = pct + '%';
                if (latency < 100) bar.style.background = '#22c55e';
                else if (latency < 300) bar.style.background = '#eab308';
                else bar.style.background = '#ef4444';
            }
        } else if (status === 'offline') {
            card.className = 'card card-endpoint border-offline h-100';
            badge.className = 'status-badge status-offline';
            dot.className = 'status-dot offline';
            text.textContent = 'Disconnected';
            latText.textContent = 'Timeout';
            
            if (bar) {
                bar.style.width = '100%';
                bar.style.background = '#ef4444';
            }
        } else {
            card.className = 'card card-endpoint border-loading h-100';
            badge.className = 'status-badge status-loading';
            dot.className = 'status-dot loading';
            text.textContent = 'Checking';
            latText.textContent = '- ms';
            spin.classList.add('fa-spin');
            
            if (bar) {
                bar.style.width = '0%';
            }
        }

        updateGlobalStats();
    }

    async function checkSingle(id, name, url) {
        updateUI(id, 'checking', 0);
        
        try {
            const response = await fetch("{{ url('/bpjs/monitoring-signal/check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id: id, name: name, url: url })
            });
            const data = await response.json();
            
            if (data.status === 'online') {
                updateUI(id, 'online', data.latency);
            } else {
                updateUI(id, 'offline', 0);
            }
        } catch (error) {
            updateUI(id, 'offline', 0);
        }
    }

    async function checkAll() {
        for (let i = 0; i < services.length; i++) {
            checkSingle(services[i].id, services[i].name, services[i].url);
            // Jeda 500ms antar request agar Firewall BPJS tidak mengira ini serangan DDoS
            await new Promise(r => setTimeout(r, 500));
        }
    }

    // Update grafik setiap 2 detik dengan status/latensi terakhir yang diketahui (tanpa fake jitter)
    setInterval(updateChartRealData, 2000);

    function bukaModalGangguan() {
        const filterInputAwal = document.getElementById('filterTanggalAwal');
        if (!filterInputAwal.value) {
            const today = new Date().toISOString().split('T')[0];
            filterInputAwal.value = today;
            document.getElementById('filterTanggalAkhir').value = today;
        }
        
        fetchLogs();
        // Coba Bootstrap 4 dulu, fallback ke manual
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#modalRiwayatGangguan').modal('show');
        } else {
            const modal = document.getElementById('modalRiwayatGangguan');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            // Tambahkan backdrop
            let backdrop = document.getElementById('modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'modal-backdrop';
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    }

    function tutupModal() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#modalRiwayatGangguan').modal('hide');
        } else {
            const modal = document.getElementById('modalRiwayatGangguan');
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.getElementById('modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }

    function cetakPdf() {
        const tglAwal = document.getElementById('filterTanggalAwal').value;
        const tglAkhir = document.getElementById('filterTanggalAkhir').value;
        let url = "{{ url('/bpjs/monitoring-signal/pdf') }}";
        
        let params = new URLSearchParams();
        if (tglAwal) params.append('tanggal_awal', tglAwal);
        if (tglAkhir) params.append('tanggal_akhir', tglAkhir);
        
        if(params.toString()) {
            url += '?' + params.toString();
        }
        
        // Buka PDF di tab baru
        window.open(url, '_blank');
    }

    async function fetchLogs() {
        const tbody = document.getElementById('tbodyRiwayatGangguan');
        const filterTanggalAwal = document.getElementById('filterTanggalAwal').value;
        const filterTanggalAkhir = document.getElementById('filterTanggalAkhir').value;
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>';
        
        try {
            let url = "{{ url('/bpjs/monitoring-signal/logs') }}";
            let params = new URLSearchParams();
            if (filterTanggalAwal) params.append('tanggal_awal', filterTanggalAwal);
            if (filterTanggalAkhir) params.append('tanggal_akhir', filterTanggalAkhir);
            
            if(params.toString()) {
                url += '?' + params.toString();
            }
            
            const response = await fetch(url);
            const logs = await response.json();
            
            if(logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada catatan gangguan.</td></tr>';
                return;
            }

            let html = '';
            logs.forEach((log, index) => {
                let waktuNormal = log.waktu_normal ? `<strong>${log.waktu_normal}</strong>` : '<span class="text-warning">Belum Normal</span>';
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong class="text-danger">${log.waktu_gangguan}</strong></td>
                        <td>${waktuNormal}</td>
                        <td>${log.service_name} <br> <small class="text-muted">${log.url}</small></td>
                        <td><span class="badge badge-danger">${log.keterangan}</span></td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data riwayat.</td></tr>';
        }
    }

    // Initial check
    checkAll();

</script>

@endsection
