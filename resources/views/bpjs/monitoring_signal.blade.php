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
</style>

<div class="bbm-container">


<!-- Chart Section -->
<div class="card mb-3 border-0 shadow-sm rounded">
    <div class="card-header bg-white border-bottom-0 pt-2 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-secondary mb-0"><i class="fas fa-chart-line me-2"></i> Real-time Latency (Average)</h6>
        <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="bukaModalGangguan()">
            <i class="fas fa-history me-1"></i> Riwayat
        </button>
    </div>
    <div class="card-body py-1">
        <canvas id="latencyChart" style="height: 150px; width: 100%;"></canvas>
    </div>
</div>

<!-- Endpoints Grid -->
<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 mb-3">
    @foreach($services as $service)
    <div class="col">
        <div class="card card-endpoint h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="bg-light p-1 rounded text-primary">
                        <i class="{{ $service['icon'] }}"></i>
                    </div>
                    <span class="status-badge status-loading" id="badge-{{ $service['id'] }}" style="padding: 0.15rem 0.5rem; font-size: 0.7rem;">
                        <span class="status-dot loading" id="dot-{{ $service['id'] }}"></span>
                        <span id="text-{{ $service['id'] }}">Checking</span>
                    </span>
                </div>
                <h6 class="fw-bold mb-0 text-truncate" style="font-size: 0.9rem;">{{ $service['name'] }}</h6>
                <p class="text-muted text-truncate mb-2" style="font-size: 0.7rem;" title="{{ $service['url'] }}">{{ $service['url'] }}</p>
                
                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-auto">
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">Response Time</small>
                        <strong class="fs-6" id="latency-{{ $service['id'] }}">- ms</strong>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="checkSingle('{{ $service['id'] }}', '{{ $service['name'] }}', '{{ $service['url'] }}')">
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Waktu Gangguan</th>
                                <th>Layanan</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRiwayatGangguan">
                            <tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>
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
    const latencyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Time labels
            datasets: [{
                label: 'Average Latency (ms)',
                data: [],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, suggestedMax: 500 },
                x: { display: false } // Hide x axis for cleaner look
            },
            animation: { duration: 500, easing: 'linear' },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Simulasi chart terus berjalan setiap 2 detik layaknya EKG
    function animateChart() {
        const now = new Date().toLocaleTimeString();
        if(latencyChart.data.labels.length > 50) {
            latencyChart.data.labels.shift();
            latencyChart.data.datasets[0].data.shift();
        }
        
        // Tambahkan sedikit efek fluktuasi/jitter (+- 5ms) agar grafik tampak hidup
        let jitter = Math.floor(Math.random() * 10) - 5;
        let displayLatency = currentAvgLatency > 0 ? Math.max(0, currentAvgLatency + jitter) : 0;

        latencyChart.data.labels.push(now);
        latencyChart.data.datasets[0].data.push(displayLatency);
        latencyChart.update('none'); // Update tanpa full animasi agar tidak berkedip

        // Animasi (jitter) teks Response Time pada tiap kartu
        for (const key in serviceStatus) {
            if (serviceStatus[key].status === 'online' && serviceStatus[key].latency > 0) {
                let cardJitter = Math.floor(Math.random() * 8) - 4; // Fluktuasi +-4 ms per kartu
                let newLatency = Math.max(0, serviceStatus[key].latency + cardJitter);
                document.getElementById('latency-' + key).innerHTML = `${newLatency} <small class="text-muted fs-6">ms</small>`;
            }
        }
    }

    // Jalankan animasi chart tiap 2 detik
    setInterval(animateChart, 2000);

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

        const badge = document.getElementById('badge-' + id);
        const dot = document.getElementById('dot-' + id);
        const text = document.getElementById('text-' + id);
        const latText = document.getElementById('latency-' + id);
        const spin = document.getElementById('spin-' + id);

        spin.classList.remove('fa-spin');

        if (status === 'online') {
            badge.className = 'status-badge status-online';
            dot.className = 'status-dot online';
            text.textContent = 'Connected';
            latText.innerHTML = `${latency} <small class="text-muted fs-6">ms</small>`;
        } else if (status === 'offline') {
            badge.className = 'status-badge status-offline';
            dot.className = 'status-dot offline';
            text.textContent = 'Disconnected';
            latText.textContent = 'Timeout';
        } else {
            badge.className = 'status-badge status-loading';
            dot.className = 'status-dot loading';
            text.textContent = 'Checking';
            latText.textContent = '- ms';
            spin.classList.add('fa-spin');
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

    function checkAll() {
        services.forEach(service => {
            checkSingle(service.id, service.name, service.url);
        });
    }

    function bukaModalGangguan() {
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

    async function fetchLogs() {
        const tbody = document.getElementById('tbodyRiwayatGangguan');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>';
        try {
            const response = await fetch("{{ url('/bpjs/monitoring-signal/logs') }}");
            const logs = await response.json();
            
            if(logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada catatan gangguan.</td></tr>';
                return;
            }

            let html = '';
            logs.forEach((log, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${log.waktu_gangguan}</strong></td>
                        <td>${log.service_name} <br> <small class="text-muted">${log.url}</small></td>
                        <td><span class="badge badge-danger">${log.keterangan}</span></td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data riwayat.</td></tr>';
        }
    }

    // Initial check
    checkAll();

    // Auto refresh ping BPJS every 30 seconds
    setInterval(checkAll, 30000);

</script>

@endsection
