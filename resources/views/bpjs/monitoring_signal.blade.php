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
<div class="card mb-4 border-0 shadow-sm rounded">
    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
        <h6 class="fw-bold text-secondary mb-0"><i class="fas fa-chart-line me-2"></i> Real-time Latency (Average)</h6>
    </div>
    <div class="card-body">
        <canvas id="latencyChart" style="height: 250px; width: 100%;"></canvas>
    </div>
</div>

<!-- Endpoints Grid -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
    @foreach($services as $service)
    <div class="col">
        <div class="card card-endpoint h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-light p-2 rounded text-primary">
                        <i class="{{ $service['icon'] }} fa-lg"></i>
                    </div>
                    <span class="status-badge status-loading" id="badge-{{ $service['id'] }}">
                        <span class="status-dot loading" id="dot-{{ $service['id'] }}"></span>
                        <span id="text-{{ $service['id'] }}">Checking</span>
                    </span>
                </div>
                <h6 class="fw-bold mb-1">{{ $service['name'] }}</h6>
                <p class="text-muted small text-truncate mb-3" title="{{ $service['url'] }}">{{ $service['url'] }}</p>
                
                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-auto">
                    <div>
                        <small class="text-muted d-block">Response Time</small>
                        <strong class="fs-5" id="latency-{{ $service['id'] }}">- ms</strong>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="checkSingle('{{ $service['id'] }}', '{{ $service['url'] }}')">
                        <i class="fas fa-sync-alt" id="spin-{{ $service['id'] }}"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
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

    async function checkSingle(id, url) {
        updateUI(id, 'checking', 0);
        
        try {
            const response = await fetch("{{ url('/bpjs/monitoring-signal/check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ url: url })
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
            checkSingle(service.id, service.url);
        });
    }

    // Initial check
    checkAll();

    // Auto refresh ping BPJS every 30 seconds
    setInterval(checkAll, 30000);

</script>

@endsection
