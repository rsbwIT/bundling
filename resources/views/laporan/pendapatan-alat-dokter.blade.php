@extends('layout.layoutDashboard')

@section('title', 'Pendapatan Alat Dokter')

@section('content')

<style>
    #tableToCopy th, .rincian-table th {
        text-align: center;
        vertical-align: middle;
        font-size: 11px;
    }
    #tableToCopy td, .rincian-table td {
        font-size: 11px;
    }
    /* Biar tabel nggak terlalu lebar dan font cukup kecil */
    .table-responsive {
        max-height: 70vh;
        overflow-y: auto;
    }
    .table-sm th, .table-sm td {
        padding: 0.4rem;
    }
</style>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark">
                    <i class="fas fa-stethoscope text-primary mr-2"></i> Pendapatan Alat Dokter
                </h5>
                <small class="text-muted">Laporan rekapitulasi dan rincian pendapatan dari penggunaan alat medis</small>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown d-inline-block mr-2">
                    <button class="btn btn-sm btn-danger dropdown-toggle" type="button" id="dropdownMenuCetakPdf" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuCetakPdf" style="max-height: 300px; overflow-y: auto;">
                        <a class="dropdown-item font-weight-bold" href="{{ url('pendapatan-alat-dokter/pdf') }}?tgl1={{ $tgl1 }}&tgl2={{ $tgl2 }}" target="_blank">
                            - Semua Dokter -
                        </a>
                        <div class="dropdown-divider"></div>
                        @foreach($dokters as $d)
                            <a class="dropdown-item" href="{{ url('pendapatan-alat-dokter/pdf') }}?tgl1={{ $tgl1 }}&tgl2={{ $tgl2 }}&kd_dokter={{ $d->kd_dokter }}" target="_blank">
                                {{ $d->nm_dokter }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-success mr-2" onclick="exportSemuaExcel()">
                    <i class="fas fa-file-excel mr-1"></i> Download Excel
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyTable('tableToCopy')">
                    <i class="fas fa-copy mr-1"></i> Copy Rekap
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ url('pendapatan-alat-dokter') }}" method="GET" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Awal:</label>
                    <input type="date" class="form-control form-control-sm" name="tgl1" id="tgl1" value="{{ $tgl1 }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Akhir:</label>
                    <input type="date" class="form-control form-control-sm" name="tgl2" id="tgl2" value="{{ $tgl2 }}">
                </div>
                <div class="col-md-2 col-sm-12 mb-2">
                    <button type="submit" class="btn btn-sm btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Tabs Navigation --}}
        <ul class="nav nav-tabs mb-3" id="rekapTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active font-weight-bold" id="rekap-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#rekap" data-bs-target="#rekap" type="button" role="tab" aria-controls="rekap" aria-selected="true">
                    <i class="fas fa-table mr-1"></i> Rekapan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold text-info" id="rincian-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#rincian" data-bs-target="#rincian" type="button" role="tab" aria-controls="rincian" aria-selected="false">
                    <i class="fas fa-list mr-1"></i> Detail Rincian Tindakan
                </button>
            </li>
        </ul>

        {{-- Tabs Content --}}
        <div class="tab-content" id="rekapTabsContent">
            {{-- Tab Rekapan --}}
            <div class="tab-pane fade show active" id="rekap" role="tabpanel" aria-labelledby="rekap-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm align-middle text-nowrap" id="tableToCopy">
                        <thead class="table-secondary" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th class="text-center" style="vertical-align: middle;">NO</th>
                                <th class="text-center" style="vertical-align: middle;">NAMA DOKTER</th>
                                <th class="text-center" style="vertical-align: middle;">UMUM</th>
                                <th class="text-center" style="vertical-align: middle;">ASS / PERUSAHAAN</th>
                                <th class="text-center" style="vertical-align: middle;">BPJS</th>
                                <th class="text-center" style="vertical-align: middle;">INHEALTH + INDEMNITY</th>
                                <th class="text-center" style="vertical-align: middle;">KEMENKES</th>
                                <th class="text-center" style="vertical-align: middle;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                $gtUmum = 0;
                                $gtAsuransi = 0;
                                $gtBpjs = 0;
                                $gtInhealth = 0;
                                $gtKemenkes = 0;
                                $gtTotal = 0;
                            @endphp
                            @foreach($data as $dokter => $row)
                                @php
                                    $gtUmum += $row['umum'];
                                    $gtAsuransi += $row['asuransi'];
                                    $gtBpjs += $row['bpjs'];
                                    $gtInhealth += $row['inhealth'];
                                    $gtKemenkes += $row['kemenkes'];
                                    $gtTotal += $row['total'];
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ $dokter }}</td>
                                    <td class="text-right">{{ $row['umum'] == 0 ? '-' : number_format($row['umum'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ $row['asuransi'] == 0 ? '-' : number_format($row['asuransi'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ $row['bpjs'] == 0 ? '-' : number_format($row['bpjs'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ $row['inhealth'] == 0 ? '-' : number_format($row['inhealth'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ $row['kemenkes'] == 0 ? '-' : number_format($row['kemenkes'], 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold table-light">{{ $row['total'] == 0 ? '-' : number_format($row['total'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-warning font-weight-bold">
                            <tr>
                                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                                <td class="text-right">{{ $gtUmum == 0 ? '-' : number_format($gtUmum, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $gtAsuransi == 0 ? '-' : number_format($gtAsuransi, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $gtBpjs == 0 ? '-' : number_format($gtBpjs, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $gtInhealth == 0 ? '-' : number_format($gtInhealth, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $gtKemenkes == 0 ? '-' : number_format($gtKemenkes, 0, ',', '.') }}</td>
                                <td class="text-right table-primary text-dark">{{ $gtTotal == 0 ? '-' : number_format($gtTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Tab Detail Rincian --}}
            <div class="tab-pane fade" id="rincian" role="tabpanel" aria-labelledby="rincian-tab">
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyTable('rincianContainer')">
                        <i class="fas fa-copy mr-1"></i> Copy Rincian
                    </button>
                </div>
                <div class="table-responsive" id="rincianContainer">
                    @php
                        $detailsCollection = collect($details);
                        // Kelompokkan berdasarkan Dokter dulu
                        $groupedByDokter = $detailsCollection->groupBy('nm_dokter');
                    @endphp

                    @forelse($groupedByDokter as $dokter => $itemsDokter)
                        @php
                            $groupedByKategori = collect($itemsDokter)->groupBy('kategori');
                            $grandTotalDokter = 0;
                        @endphp
                        
                        <div class="mt-4 mb-2">
                            <h5 class="text-primary font-weight-bold border-bottom pb-2">
                                <i class="fas fa-user-md mr-2"></i> Dokter: {{ $dokter }}
                            </h5>
                        </div>

                        @foreach($groupedByKategori as $kategori => $itemsKategori)
                            @php
                                $subTotalKategori = 0;
                            @endphp
                            <h6 class="font-weight-bold mt-3 text-secondary">
                                <i class="fas fa-caret-right mr-1"></i> Kategori: {{ $kategori }}
                            </h6>
                            <table class="table table-bordered table-striped table-hover table-sm align-middle text-nowrap mb-3 rincian-table">
                                <thead class="table-secondary" style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle; width: 5%;">NO</th>
                                        <th class="text-center" style="vertical-align: middle; width: 10%;">TGL TINDAKAN</th>
                                        <th class="text-center" style="vertical-align: middle; width: 15%;">NO RAWAT</th>
                                        <th class="text-center" style="vertical-align: middle; width: 10%;">NO.RM</th>
                                        <th class="text-center" style="vertical-align: middle; width: 25%;">NAMA PASIEN</th>
                                        <th class="text-center" style="vertical-align: middle; width: 25%;">TINDAKAN MEDIS</th>
                                        <th class="text-center" style="vertical-align: middle; width: 10%;">JASA MEDIS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itemsKategori as $index => $item)
                                        @php
                                            $subTotalKategori += $item->jasa_medis;
                                            $grandTotalDokter += $item->jasa_medis;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">{{ date('d/m/Y', strtotime($item->tgl_tindakan)) }}</td>
                                            <td class="text-center">{{ $item->no_rawat }}</td>
                                            <td class="text-center">{{ $item->no_rkm_medis }}</td>
                                            <td>{{ $item->nm_pasien }}</td>
                                            <td>{{ $item->nm_perawatan }}</td>
                                            <td class="text-right">{{ number_format($item->jasa_medis, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-warning font-weight-bold">
                                    <tr>
                                        <td colspan="6" class="text-center">Subtotal {{ $kategori }}</td>
                                        <td class="text-right">{{ number_format($subTotalKategori, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @endforeach

                        <div class="alert alert-success font-weight-bold text-right mb-4">
                            Total Pendapatan {{ $dokter }} : {{ number_format($grandTotalDokter, 0, ',', '.') }}
                        </div>
                    @empty
                        <div class="alert alert-info text-center mt-3">Tidak ada detail tindakan untuk periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportSemuaExcel() {
    let html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head><meta charset="utf-8"></head><body>`;
    
    // Tabel Rekapan
    let rekapTable = document.getElementById('tableToCopy');
    if (rekapTable) {
        html += "<h2>Rekapan Pendapatan Alat Dokter</h2>" + rekapTable.outerHTML + "<br><br>";
    }
    
    // Tabel Rincian per Kategori
    let rincianContainer = document.getElementById('rincianContainer');
    if (rincianContainer) {
        let headers = rincianContainer.querySelectorAll('h5');
        let tables = rincianContainer.querySelectorAll('.rincian-table');
        
        for (let i = 0; i < tables.length; i++) {
            let title = headers[i] ? headers[i].innerText : "Rincian";
            html += "<h2>" + title + "</h2>" + tables[i].outerHTML + "<br><br>";
        }
    }
    
    html += "</body></html>";
    
    let blob = new Blob([html], { type: "application/vnd.ms-excel" });
    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    let tgl1 = document.querySelector('input[name="tgl1"]').value;
    let tgl2 = document.querySelector('input[name="tgl2"]').value;
    a.download = `Laporan_Pendapatan_Alat_Dokter_${tgl1}_sd_${tgl2}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function copyTable(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    let range = document.createRange();
    range.selectNode(container);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);

    try {
        document.execCommand('copy');
        alert('Data berhasil disalin ke clipboard!');
    } catch (err) {
        alert('Gagal menyalin data.');
    }
    window.getSelection().removeAllRanges();
}
</script>

<style>
    .text-right { text-align: right; }
    .text-center { text-align: center; }
</style>
@endsection
