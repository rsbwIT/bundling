@extends('layout.layoutDashboard')
@section('title', 'Laporan Antrian Farmasi')

@section('konten')
<style>
    .filter-input { border-radius: 0.25rem; }
    .btn-round { border-radius: 0.25rem; }
    .chart-legend { margin-top: 10px; font-size: 0.9rem; }
    .chart-legend .circle { display:inline-block; width:12px; height:12px; border-radius:50%; margin-right:5px; }
</style>

<div class="container-fluid mt-2">

    {{-- FILTER --}}
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <label class="fw-bold small">Tanggal 1</label>
            <input type="date" class="form-control filter-input" wire:model.defer="tgl1">
        </div>

        <div class="col-md-3">
            <label class="fw-bold small">Tanggal 2</label>
            <input type="date" class="form-control filter-input" wire:model.defer="tgl2">
        </div>

        <div class="col-md-3">
            <label class="fw-bold small">Pencarian</label>
            <input type="text" class="form-control filter-input" placeholder="Cari No Rawat / RM / Nama" wire:model.debounce.500ms="search">
        </div>

        <div class="col-md-3 d-flex align-items-end gap-2">
            <button class="btn btn-primary btn-round w-50" wire:click="loadData">
                <i class="fa fa-filter"></i> Filter
            </button>
            <button class="btn btn-secondary btn-round w-50" wire:click="resetFilter">
                <i class="fa fa-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    {{-- CHART & TABLE --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-2">
                <div class="card-header bg-light fw-bold">
                    <i class="fa fa-chart-pie text-primary"></i> Statistik Status
                </div>
                <div class="card-body text-center" style="height:300px;">
                    <canvas id="statusChart"></canvas>
                    <div id="chartLegend" class="chart-legend"></div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-2">
                <div class="card-header bg-light fw-bold">
                    <i class="fa fa-list text-success"></i> Data Pasien
                </div>
                <div class="card-body p-0 table-responsive" style="max-height:450px;">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No Rawat</th>
                                <th>No RM</th>
                                <th>Nama</th>
                                <th>Masuk</th>
                                <th>Selesai</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listData as $item)
                                @php
                                    $badge = match(strtoupper($item->status)) {
                                        'SELESAI' => 'success',
                                        'DIPROSES','MENUNGGU' => 'warning',
                                        'DIPANGGIL' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->masuk)->format('d-m-Y H:i') }}</td>
                                    <td>{{ $item->selesai ? \Carbon\Carbon::parse($item->selesai)->format('d-m-Y H:i') : '-' }}</td>
                                    <td>{{ $item->durasi_menit ? $item->durasi_menit.' mnt' : '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $badge }}">{{ strtoupper($item->status) }}</span>
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-2">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('livewire:load', () => {
    let chart;

    function renderChart(labels, values) {
        const ctx = document.getElementById('statusChart').getContext('2d');
        if(chart) chart.destroy();

        const colors = ["#007bff","#ffc107","#28a745","#dc3545","#6f42c1","#17a2b8"];

        chart = new Chart(ctx, {
            type:'pie',
            data: { labels, datasets: [{ data: values, backgroundColor: colors }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        let legendHtml = "";
        labels.forEach((label, i) => {
            legendHtml += `<div><span class="circle" style="background:${colors[i]}"></span>${label}: <b>${values[i]}</b></div>`;
        });
        document.getElementById('chartLegend').innerHTML = legendHtml;
    }

    renderChart(@json($labels), @json($values));

    Livewire.on('refreshChart', data => renderChart(data.labels, data.values));
});
</script>
@endsection
