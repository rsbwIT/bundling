@extends('layout.layoutDashboard')

@section('title', 'Antrian Farmasi Baru')

@section('konten')
{{-- ✅ Bootstrap & Icon --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    body {
        background: #f4f4f4;
        overflow-anchor: none;
        font-family: "Poppins", sans-serif;
    }

    .card-header {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: #fff;
        font-weight: 600;
        border-bottom: none;
        padding: 1rem 1.25rem;
        border-radius: .5rem .5rem 0 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
    }

    .filter-section {
        background: #ffffff;
        padding: 1rem;
        border-radius: .75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
    }

    .table thead th {
        background: #f1f3f5;
        color: #495057;
        text-transform: uppercase;
        font-size: .85rem;
    }

    /* 🔹 Tombol elastis dan lembut */
    .btn {
        border: none;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
        padding: 0.4rem 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .25rem;
    }

    .btn i {
        font-size: .85rem;
    }

    .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 3px 6px rgba(0, 0, 0, .15);
    }

    .btn:active {
        transform: scale(0.97);
    }

    .btn-call {
        background: linear-gradient(135deg, #198754, #157347);
        color: #fff;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: #fff;
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545, #bb2d3b);
        color: #fff;
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: #fff;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268, #4e555b);
    }

    .badge {
        font-size: .8rem;
        border-radius: .4rem;
        padding: .35em .6em;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        transition: 0.2s;
    }
</style>

<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-capsules"></i> Antrian Farmasi
            </h5>
        </div>
        <div class="card-body">

            <!-- Filter -->
            <div class="filter-section">
                <form method="GET" action="{{ route('farmasi.antrian') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="keterangan" class="form-label fw-bold">Keterangan</label>
                        <select id="keterangan" name="keterangan" class="form-select">
                            <option value="">-- Semua --</option>
                            @foreach($keterangans as $k)
                                <option value="{{ $k->keterangan }}" {{ request('keterangan') == $k->keterangan ? 'selected':'' }}>
                                    {{ $k->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="dokter" class="form-label fw-bold">Dokter</label>
                        <select id="dokter" name="dokter" class="form-select">
                            <option value="">-- Semua Dokter --</option>
                            @foreach($dokters as $d)
                                <option value="{{ $d->nm_dokter }}" {{ request('dokter')==$d->nm_dokter ? 'selected':'' }}>
                                    {{ $d->nm_dokter }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('farmasi.antrian', ['tanggal' => $tanggal]) }}" class="btn btn-secondary w-100">
                            <i class="fas fa-sync"></i> Semua Antrian
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive" id="tableAntrian">
                <table class="table table-hover align-middle shadow-sm">
                    <thead class="text-center">
                        <tr>
                            <th>No Antrian</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Dokter</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antrians as $a)
                        <tr id="row-{{ $a->nomor_antrian }}">
                            <td class="fw-bold text-primary text-center">{{ $a->nomor_antrian }}</td>
                            <td>{{ $a->nama_pasien }}</td>
                            <td>{{ $a->tanggal }}</td>
                            <td class="text-center">
                                @if(strtoupper($a->keterangan) == 'RACIK')
                                    <span class="badge bg-danger">RACIK</span>
                                @elseif(strtoupper($a->keterangan) == 'NON RACIK')
                                    <span class="badge bg-success">NON RACIK</span>
                                @else
                                    <span class="badge bg-secondary">{{ $a->keterangan ?? '-' }}</span>
                                @endif
                            </td>
                            <td>{{ $a->nm_dokter }}</td>
                            <td class="text-center status-cell">
                                @if($a->status == 'MENUNGGU')
                                    <span class="badge bg-warning text-dark">MENUNGGU</span>
                                @elseif($a->status == 'SELESAI')
                                    <span class="badge bg-success">SELESAI</span>
                                @elseif($a->status == 'TIDAK ADA')
                                    <span class="badge bg-danger">TIDAK ADA</span>
                                @elseif($a->status == 'DIPANGGIL')
                                    <span class="badge bg-info text-dark">PANGGIL</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <button type="button"
                                        class="btn btn-sm btn-call btn-panggil"
                                        data-nomor="{{ $a->nomor_antrian }}"
                                        data-pasien="{{ $a->nama_pasien }}"
                                        data-dokter="{{ $a->nm_dokter }}"
                                        data-keterangan="{{ $a->keterangan }}">
                                        <i class="fas fa-volume-up"></i> Panggil
                                    </button>

                                    <button type="button" class="btn btn-sm btn-primary btn-update"
                                        data-status="SELESAI" data-nomor="{{ $a->nomor_antrian }}">
                                        <i class="fas fa-check"></i> Ada
                                    </button>

                                    <button type="button" class="btn btn-sm btn-danger btn-update"
                                        data-status="TIDAK ADA" data-nomor="{{ $a->nomor_antrian }}">
                                        <i class="fas fa-times"></i> Tidak Ada
                                    </button>

                                    <button type="button" class="btn btn-sm btn-secondary btn-print"
                                        data-nomor="{{ $a->nomor_antrian }}">
                                        <i class="fas fa-print"></i> Cetak
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada antrian hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ✅ JS --}}
<script src="https://code.responsivevoice.org/responsivevoice.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // ✅ Panggilan suara
    document.querySelectorAll(".btn-panggil").forEach(btn => {
        btn.addEventListener("click", function() {
            let nomor = this.dataset.nomor;
            let pasien = this.dataset.pasien;
            let keterangan = this.dataset.keterangan;

            let namaPasien = pasien
                .toLowerCase()
                .replace(/\s+/g, ' ')
                .split(' ')
                .map(w => w.charAt(0).toUpperCase() + w.slice(1))
                .join(' ');

            let text = `Nomor antrian, ${nomor}. Pasien ${namaPasien}. Menuju loket ${keterangan}.`;

            if (keterangan.toUpperCase() !== 'RACIK') {
                responsiveVoice.speak(text, "Indonesian Female", { pitch: 1, rate: 0.9, volume: 1 });
            }

            setTimeout(() => updateStatus(nomor, "DIPANGGIL"), 3500);
        });
    });

    // ✅ Update status
    document.querySelectorAll(".btn-update").forEach(btn => {
        btn.addEventListener("click", function() {
            updateStatus(this.dataset.nomor, this.dataset.status);
        });
    });

    function updateStatus(nomor, status) {
        fetch(`{{ url('/farmasi/antrian/update-status') }}/${nomor}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ status: status, tanggal: "{{ $tanggal }}" })
        })
        .then(res => res.ok ? res.text() : Promise.reject(res))
        .then(() => {
            const row = document.getElementById(`row-${nomor}`);
            if (!row) return;
            let cell = row.querySelector(".status-cell");
            if (status === "SELESAI") cell.innerHTML = '<span class="badge bg-success">SELESAI</span>';
            else if (status === "TIDAK ADA") cell.innerHTML = '<span class="badge bg-danger">TIDAK ADA</span>';
            else if (status === "DIPANGGIL") cell.innerHTML = '<span class="badge bg-info text-dark">PANGGIL</span>';
            else cell.innerHTML = '<span class="badge bg-warning text-dark">MENUNGGU</span>';
        })
        .catch(err => console.error(err));
    }

    // ✅ Cetak antrian tanpa IP
    document.querySelectorAll(".btn-print").forEach(btn => {
        btn.addEventListener("click", function() {
            let nomor = this.dataset.nomor;
            window.open(`/antrian-farmasi/cetak/${nomor}`, "_blank");
        });
    });
});
</script>
@endsection
