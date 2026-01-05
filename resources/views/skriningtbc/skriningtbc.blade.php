@extends('layout.layoutDashboard')

@section('title', 'Data Skrining TBC')

@section('konten')

<style>
    /* ===== FILTER AREA ===== */
    .filter-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
    }

    .filter-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-select {
        min-width: 180px;
        border-radius: 50px;
        padding: 4px 14px;
    }

    .filter-btn {
        border-radius: 50px;
        padding: 6px 22px;
    }
</style>


<div class="card shadow border-0 rounded-4 mt-4">

    {{-- HEADER --}}
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-lungs me-2"></i> Data Skrining TBC
        </h5>
        <span class="badge bg-light text-success">
            Total: {{ count($data) }}
        </span>
    </div>

    {{-- FILTER --}}
    <div class="card-body border-bottom bg-white">
        <form method="GET" action="{{ url('skriningtbc') }}">

            <div class="filter-bar">

                <div class="filter-item">
                    <span class="text-muted small fw-semibold">
                        Status Pelayanan
                    </span>

                    <select name="status"
                        class="form-select form-select-sm filter-select">
                        <option value="">Semua</option>
                        <option value="ralan" {{ request('status')=='ralan'?'selected':'' }}>
                            Rawat Jalan
                        </option>
                        <option value="ranap" {{ request('status')=='ranap'?'selected':'' }}>
                            Rawat Inap
                        </option>
                    </select>
                </div>

                <button class="btn btn-success btn-sm filter-btn">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>

                @if(request('status'))
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
                <thead class="table-success text-center">
                    <tr>
                        <th>No</th>
                        <th>No Rawat</th>
                        <th>No RM</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Penjamin</th>
                        <th>Tanggal</th>
                        <th>IMT</th>
                        <th>Kesimpulan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($data as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $row->no_rawat }}</td>
                        <td>{{ $row->no_rkm_medis }}</td>
                        <td class="fw-bold">{{ $row->nm_pasien }}</td>
                        <td class="text-center">
                            <span class="badge 
                                {{ $row->status_lanjut=='ralan' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                {{ strtoupper($row->status_lanjut ?? '-') }}
                            </span>
                        </td>
                        <td>{{ $row->png_jawab }}</td>
                        <td>{{ $row->tanggal }}</td>
                        <td class="text-center">{{ $row->imt }}</td>
                        <td>{{ $row->kesimpulan_skrining }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDetail{{ $i }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- ================= MODAL DETAIL ================= --}}
                    <div class="modal fade" id="modalDetail{{ $i }}" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header bg-success text-white">
                                    <h6 class="modal-title fw-bold">
                                        Detail Skrining TBC - {{ $row->nm_pasien }}
                                    </h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body small">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <b>Antropometri</b><hr>
                                            BB : {{ $row->berat_badan }} kg<br>
                                            TB : {{ $row->tinggi_badan }} cm<br>
                                            IMT : {{ $row->imt }} ({{ $row->kasifikasi_imt ?? '-' }})<br>
                                            Lingkar Pinggang : {{ $row->lingkar_pinggang }} cm<br>
                                            Risiko LP : {{ $row->risiko_lingkar_pinggang }}
                                        </div>

                                        <div class="col-md-4">
                                            <b>Riwayat & Faktor Risiko</b><hr>
                                            Kontak TBC : {{ $row->riwayat_kontak_tbc }}<br>
                                            Jenis Kontak : {{ $row->jenis_kontak_tbc }}<br>
                                            Pernah TBC : {{ $row->faktor_resiko_pernah_terdiagnosa_tbc }}<br>
                                            Ket. Pernah TBC : {{ $row->keterangan_pernah_terdiagnosa }}<br>
                                            Pernah Berobat : {{ $row->faktor_resiko_pernah_berobat_tbc }}<br>
                                            Malnutrisi : {{ $row->faktor_resiko_malnutrisi }}<br>
                                            Merokok : {{ $row->faktor_resiko_merokok }}<br>
                                            DM : {{ $row->faktor_resiko_riwayat_dm }}<br>
                                            ODHIV : {{ $row->faktor_resiko_odhiv }}<br>
                                            Lansia : {{ $row->faktor_resiko_lansia }}<br>
                                            Ibu Hamil : {{ $row->faktor_resiko_ibu_hamil }}<br>
                                            WBP : {{ $row->faktor_resiko_wbp }}<br>
                                            Lingkungan Kumuh : {{ $row->faktor_resiko_tinggal_diwilayah_padat_kumuh }}
                                        </div>

                                        <div class="col-md-4">
                                            <b>Gejala & Hasil</b><hr>
                                            Abnormalitas : {{ $row->abnormalitas_tbc }}<br>
                                            Batuk : {{ $row->gejala_tbc_batuk }}<br>
                                            BB Turun : {{ $row->gejala_tbc_bb_turun }}<br>
                                            Demam : {{ $row->gejala_tbc_demam }}<br>
                                            Keringat Malam : {{ $row->gejala_tbc_berkeringat_malam_hari }}<br>
                                            Penyakit Lain : {{ $row->keterangan_gejala_penyakit_lain }}<br><br>

                                            <b>Kesimpulan</b> : {{ $row->kesimpulan_skrining }}<br>
                                            <b>Keterangan</b> : {{ $row->keterangan_hasil_skrining }}<br>
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                        Tutup
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{-- ================= END MODAL ================= --}}

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

@endsection
