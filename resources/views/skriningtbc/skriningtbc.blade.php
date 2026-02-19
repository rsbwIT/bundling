@extends('layout.layoutDashboard')

@section('title', 'Data Skrining TBC')

@section('konten')

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
.filter-bar{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:16px;
}
.filter-item{
    display:flex;
    align-items:center;
    gap:10px;
}
.filter-select{
    min-width:180px;
    border-radius:20px;
}
.filter-btn{
    border-radius:20px;
    padding:6px 20px;
}
.table td,.table th{
    vertical-align:middle;
}
</style>

<div class="card shadow-sm border-0 rounded-4 mt-3">

    {{-- HEADER --}}
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-lungs mr-2"></i> Data Skrining TBC
        </h5>
        <span class="badge badge-light text-success">
            Total : {{ count($data) }}
        </span>
    </div>

    {{-- FILTER --}}
    <div class="card-body border-bottom">
        <form method="GET" action="{{ url('skriningtbc') }}">
            <div class="filter-bar">

                {{-- STATUS --}}
                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Status Pelayanan
                    </span>
                    <select name="status" class="form-control form-control-sm filter-select">
                        <option value="">Semua</option>
                        <option value="ralan" {{ request('status')=='ralan'?'selected':'' }}>
                            Rawat Jalan
                        </option>
                        <option value="ranap" {{ request('status')=='ranap'?'selected':'' }}>
                            Rawat Inap
                        </option>
                    </select>
                </div>

                {{-- KESIMPULAN --}}
                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Kesimpulan
                    </span>
                    <select name="kesimpulan" class="form-control form-control-sm filter-select">
                        <option value="">Semua</option>
                        <option value="terduga" {{ request('kesimpulan')=='terduga'?'selected':'' }}>
                            Terduga TBC
                        </option>
                        <option value="bukan" {{ request('kesimpulan')=='bukan'?'selected':'' }}>
                            Bukan Terduga TBC
                        </option>
                    </select>
                </div>

                {{-- TANGGAL DARI --}}
                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Dari Tanggal
                    </span>
                    <input type="date"
                           name="tgl_dari"
                           value="{{ request('tgl_dari') }}"
                           class="form-control form-control-sm filter-select">
                </div>

                {{-- TANGGAL SAMPAI --}}
                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Sampai Tanggal
                    </span>
                    <input type="date"
                           name="tgl_sampai"
                           value="{{ request('tgl_sampai') }}"
                           class="form-control form-control-sm filter-select">
                </div>

                <button class="btn btn-success btn-sm filter-btn">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>

                @if(
                    request('status') ||
                    request('kesimpulan') ||
                    request('tgl_dari') ||
                    request('tgl_sampai')
                )
                    <a href="{{ url('skriningtbc') }}"
                       class="btn btn-outline-secondary btn-sm filter-btn">
                        Reset
                    </a>
                @endif

            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="bg-success text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>No Rawat</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>Status</th>
                        <th>Penjamin</th>
                        <th>Tanggal</th>
                        <th>IMT</th>
                        <th>Kesimpulan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($data as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $row->no_rawat }}</td>
                        <td>{{ $row->no_rkm_medis }}</td>
                        <td class="font-weight-bold">{{ $row->nm_pasien }}</td>

                        <td class="text-center">
                            <span class="badge {{ $row->status_lanjut=='ralan' ? 'badge-primary' : 'badge-warning' }}">
                                {{ strtoupper($row->status_lanjut) }}
                            </span>
                        </td>

                        <td>{{ $row->png_jawab }}</td>
                        <td>{{ $row->tanggal }}</td>
                        <td class="text-center">{{ $row->imt }}</td>

                        <td class="text-center">
                            @if(stripos($row->kesimpulan_skrining, 'Terduga') !== false)
                                <span class="badge badge-danger">
                                    {{ $row->kesimpulan_skrining }}
                                </span>
                            @else
                                <span class="badge badge-success">
                                    {{ $row->kesimpulan_skrining }}
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <button class="btn btn-success btn-sm"
                                data-toggle="modal"
                                data-target="#modalDetail{{ $i }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            Data skrining TBC tidak ditemukan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL --}}
@foreach($data as $i => $row)
<div class="modal fade" id="modalDetail{{ $i }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-lungs mr-2"></i>
                    Detail Skrining TBC - {{ $row->nm_pasien }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white font-weight-bold">
                                Antropometri
                            </div>
                            <div class="card-body small">
                                BB : {{ $row->berat_badan }} kg<br>
                                TB : {{ $row->tinggi_badan }} cm<br>
                                IMT : {{ $row->imt }} ({{ $row->kasifikasi_imt ?? '-' }})<br>
                                Lingkar Pinggang : {{ $row->lingkar_pinggang }} cm<br>
                                Risiko LP : {{ $row->risiko_lingkar_pinggang }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-warning font-weight-bold">
                                Riwayat & Faktor Risiko
                            </div>
                            <div class="card-body small">
                                Kontak TBC : {{ $row->riwayat_kontak_tbc }}<br>
                                Jenis Kontak : {{ $row->jenis_kontak_tbc }}<br>
                                Pernah TBC : {{ $row->faktor_resiko_pernah_terdiagnosa_tbc }}<br>
                                Pernah Berobat : {{ $row->faktor_resiko_pernah_berobat_tbc }}<br>
                                Merokok : {{ $row->faktor_resiko_merokok }}<br>
                                DM : {{ $row->faktor_resiko_riwayat_dm }}<br>
                                ODHIV : {{ $row->faktor_resiko_odhiv }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white font-weight-bold">
                                Gejala & Kesimpulan
                            </div>
                            <div class="card-body small">
                                Batuk : {{ $row->gejala_tbc_batuk }}<br>
                                BB Turun : {{ $row->gejala_tbc_bb_turun }}<br>
                                Demam : {{ $row->gejala_tbc_demam }}<br>
                                Keringat Malam : {{ $row->gejala_tbc_berkeringat_malam_hari }}<hr>
                                <strong class="text-success">
                                    {{ $row->kesimpulan_skrining }}
                                </strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection
