@extends('..layout.layoutDashboard')

@section('title', 'Rekap Pendapatan Asuransi Belum Bayar')

@section('konten')

<style>
    #tableToCopy th, #tableRincian th {
        text-align: center;
        vertical-align: middle;
        font-size: 11px;
    }
    #tableToCopy td, #tableRincian td {
        font-size: 11px;
    }
    #tableToCopy td:nth-child(1) {
        text-align: center;
        font-weight: bold;
    }
    #tableToCopy td:nth-child(n+2) {
        text-align: right;
    }
    .table-responsive {
        max-height: 70vh;
    }
    .table-sm th, .table-sm td {
        padding: 0.3rem;
    }
</style>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark">
                    <i class="fas fa-file-invoice text-danger mr-2"></i> Rekap Pendapatan Asuransi Belum Bayar
                </h5>
                <small class="text-muted">Laporan rincian pendapatan asuransi berdasarkan tanggal cetak nota yang belum dibayar / belum lunas</small>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-success mr-2" onclick="exportSemuaExcel()">
                    <i class="fas fa-file-excel mr-1"></i> Download Excel
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="copyButton" onclick="copyActiveTable()">
                    <i class="fas fa-copy mr-1"></i> Copy Table
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Form Filter Periode Tanggal & Asuransi --}}
        <form method="GET" action="{{ url('/rekap-pendapatan-asuransi-belum-bayar') }}" class="mb-4" id="formFilterAsuransi">
            <input type="hidden" name="filter" value="1">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Awal Nota:</label>
                    <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Akhir Nota:</label>
                    <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Penjamin / Asuransi:</label>
                    <select name="kd_pj" class="form-control form-control-sm select2">
                        <option value="SEMUA" {{ $kd_pj == 'SEMUA' ? 'selected' : '' }}>-- SEMUA ASURANSI --</option>
                        @foreach($penjaminList as $pj)
                            <option value="{{ $pj->kd_pj }}" {{ $kd_pj == $pj->kd_pj ? 'selected' : '' }}>
                                {{ $pj->png_jawab }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-12 mb-2">
                    <button type="submit" class="btn btn-sm btn-primary btn-block" id="btnFilterAsuransi">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        @if(!request()->has('filter'))
            <div class="alert alert-info border text-center py-4 mb-4">
                <i class="fas fa-filter fa-2x mb-2 text-primary"></i>
                <h6 class="font-weight-bold text-dark mb-1">Data Belum Dimuat</h6>
                <p class="mb-0 text-muted small">Silakan pilih periode tanggal cetak nota & penjamin asuransi di atas, lalu klik <strong>Filter</strong> untuk memunculkan data.</p>
            </div>
        @endif

        {{-- Tabs Navigation --}}
        <ul class="nav nav-tabs mb-3" id="rekapTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active font-weight-bold" id="rekap-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#rekap" data-bs-target="#rekap" type="button" role="tab" aria-controls="rekap" aria-selected="true">
                    <i class="fas fa-table mr-1"></i> Rekapan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link font-weight-bold text-danger" id="rincian-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#rincian" data-bs-target="#rincian" type="button" role="tab" aria-controls="rincian" aria-selected="false">
                    <i class="fas fa-list mr-1"></i> Rincian Nota Belum Bayar
                </button>
            </li>
        </ul>

        {{-- Tabs Content --}}
        <div class="tab-content" id="rekapTabsContent">
            {{-- Tab Rekapan --}}
            <div class="tab-pane fade show active" id="rekap" role="tabpanel" aria-labelledby="rekap-tab">
                <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm align-middle text-nowrap" id="tableToCopy">
                <thead class="table-secondary">
                    <tr>
                        <th rowspan="2" style="width: 70px;">Tahun</th>
                        <th rowspan="2" style="width: 70px;">Bulan</th>
                        <th rowspan="2">REG</th>
                        <th colspan="5">TINDAKAN</th>
                        <th rowspan="2">OBAT<br>EMB+TUSLAH</th>
                        <th rowspan="2">RETUR<br>OBAT</th>
                        <th colspan="3">LAB</th>
                        <th colspan="5">RO</th>
                        <th rowspan="2">POT</th>
                        <th rowspan="2">TBM</th>
                        <th rowspan="2">KAMAR<br>SERVICE</th>
                        <th colspan="3">O.K</th>
                        <th rowspan="2">TOTAL<br>BILLING</th>
                        <th rowspan="2">TOTAL<br>PIUTANG</th>
                        <th rowspan="2">CICILAN<br>DIBAYAR</th>
                        <th rowspan="2" class="table-danger text-dark font-weight-bold">SISA PIUTANG<br>(BELUM BAYAR)</th>
                    </tr>
                    <tr>
                        <th>JS</th>
                        <th>BHP</th>
                        <th>JM DR</th>
                        <th>PR</th>
                        <th>KSO</th>

                        <th>JS</th>
                        <th>BHP</th>
                        <th>JM PJ</th>

                        <th>JS</th>
                        <th>BHP</th>
                        <th>JM PJ</th>
                        <th>PETUGAS</th>
                        <th>JM PR</th>

                        <th>JM DR</th>
                        <th>JM PR</th>
                        <th>JS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedData = $dataRekap->groupBy('group_key');
                    @endphp

                    @forelse ($groupedData as $groupKey => $items)
                        @foreach ($items as $index => $item)
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ count($items) }}" style="vertical-align: middle; font-weight: bold; text-align: center;">{{ $groupKey }}</td>
                                @endif
                                <td class="text-center font-weight-bold">
                                    <a href="{{ url('/rekap-pendapatan-asuransi-belum-bayar/detail?tgl1='.$tgl1.'&tgl2='.$tgl2.'&tgl_nota='.$item->tgl_nota.'&status_lanjut='.$item->status_lanjut.'&kd_pj='.$kd_pj) }}" target="_blank" class="text-decoration-none">
                                        {{ $item->nama_bulan }}
                                    </a>
                                </td>
                                <td data-raw="{{ $item->reg }}">{{ number_format($item->reg, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->js }}">{{ number_format($item->js, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->bhp }}">{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->jm_dr }}">{{ number_format($item->jm_dr, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->pr }}">{{ number_format($item->pr, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->kso }}">{{ number_format($item->kso, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->obat }}">{{ number_format($item->obat, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->retur }}">{{ number_format($item->retur, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->lab_js }}">{{ number_format($item->lab_js, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->lab_bhp }}">{{ number_format($item->lab_bhp, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->lab_jm_pj }}">{{ number_format($item->lab_jm_pj, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ro_js }}">{{ number_format($item->ro_js, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ro_bhp }}">{{ number_format($item->ro_bhp, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ro_jm_pj }}">{{ number_format($item->ro_jm_pj, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ro_petugas }}">{{ number_format($item->ro_petugas, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ro_perujuk }}">{{ number_format($item->ro_perujuk, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->pot }}">{{ number_format($item->pot, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->tbm }}">{{ number_format($item->tbm, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->kamar }}">{{ number_format($item->kamar, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ok_jm_dr }}">{{ number_format($item->ok_jm_dr, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ok_jm_pr }}">{{ number_format($item->ok_jm_pr, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ok_js }}">{{ number_format($item->ok_js, 0, ',', '.') }}</td>
                                <td class="font-weight-bold table-light" data-raw="{{ $item->total }}">{{ number_format($item->total, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->total_piutang }}">{{ number_format($item->total_piutang, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->bayar_piutang }}">{{ number_format($item->bayar_piutang, 0, ',', '.') }}</td>
                                <td class="font-weight-bold table-danger text-danger" data-raw="{{ $item->sisa_piutang }}">{{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach

                        {{-- Subtotal per Group --}}
                        <tr class="table-info font-weight-bold">
                            <td colspan="2" class="text-right">TOTAL {{ $groupKey }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('reg') }}">{{ number_format(collect($items)->sum("reg"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('js') }}">{{ number_format(collect($items)->sum("js"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('bhp') }}">{{ number_format(collect($items)->sum("bhp"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('jm_dr') }}">{{ number_format(collect($items)->sum("jm_dr"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('pr') }}">{{ number_format(collect($items)->sum("pr"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('kso') }}">{{ number_format(collect($items)->sum("kso"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('obat') }}">{{ number_format(collect($items)->sum("obat"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('retur') }}">{{ number_format(collect($items)->sum("retur"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('lab_js') }}">{{ number_format(collect($items)->sum("lab_js"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('lab_bhp') }}">{{ number_format(collect($items)->sum("lab_bhp"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('lab_jm_pj') }}">{{ number_format(collect($items)->sum("lab_jm_pj"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ro_js') }}">{{ number_format(collect($items)->sum("ro_js"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ro_bhp') }}">{{ number_format(collect($items)->sum("ro_bhp"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ro_jm_pj') }}">{{ number_format(collect($items)->sum("ro_jm_pj"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ro_petugas') }}">{{ number_format(collect($items)->sum("ro_petugas"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ro_perujuk') }}">{{ number_format(collect($items)->sum("ro_perujuk"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('pot') }}">{{ number_format(collect($items)->sum("pot"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('tbm') }}">{{ number_format(collect($items)->sum("tbm"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('kamar') }}">{{ number_format(collect($items)->sum("kamar"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ok_jm_dr') }}">{{ number_format(collect($items)->sum("ok_jm_dr"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ok_jm_pr') }}">{{ number_format(collect($items)->sum("ok_jm_pr"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ok_js') }}">{{ number_format(collect($items)->sum("ok_js"), 0, ",", ".") }}</td>
                            <td class="text-right table-light" data-raw="{{ collect($items)->sum('total') }}">{{ number_format(collect($items)->sum("total"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('total_piutang') }}">{{ number_format(collect($items)->sum("total_piutang"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('bayar_piutang') }}">{{ number_format(collect($items)->sum("bayar_piutang"), 0, ",", ".") }}</td>
                            <td class="text-right font-weight-bold table-danger text-danger" data-raw="{{ collect($items)->sum('sisa_piutang') }}">{{ number_format(collect($items)->sum("sisa_piutang"), 0, ",", ".") }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="28" class="text-center py-4 text-muted">
                                @if(request()->has('filter'))
                                    <i class="fas fa-info-circle mr-1"></i> Tidak ada data nota asuransi belum bayar pada periode ini.
                                @else
                                    <i class="fas fa-info-circle mr-1"></i> Silakan tentukan periode tanggal cetak nota dan klik tombol <strong>Filter</strong> untuk memunculkan data.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($dataRekap->isNotEmpty())
                <tfoot class="table-warning font-weight-bold">
                    <tr>
                        <td class="text-center" colspan="2">TOTAL KESELURUHAN</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('reg') }}">{{ number_format($dataRekap->sum("reg"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('js') }}">{{ number_format($dataRekap->sum("js"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('bhp') }}">{{ number_format($dataRekap->sum("bhp"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('jm_dr') }}">{{ number_format($dataRekap->sum("jm_dr"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('pr') }}">{{ number_format($dataRekap->sum("pr"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('kso') }}">{{ number_format($dataRekap->sum("kso"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('obat') }}">{{ number_format($dataRekap->sum("obat"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('retur') }}">{{ number_format($dataRekap->sum("retur"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('lab_js') }}">{{ number_format($dataRekap->sum("lab_js"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('lab_bhp') }}">{{ number_format($dataRekap->sum("lab_bhp"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('lab_jm_pj') }}">{{ number_format($dataRekap->sum("lab_jm_pj"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ro_js') }}">{{ number_format($dataRekap->sum("ro_js"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ro_bhp') }}">{{ number_format($dataRekap->sum("ro_bhp"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ro_jm_pj') }}">{{ number_format($dataRekap->sum("ro_jm_pj"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ro_petugas') }}">{{ number_format($dataRekap->sum("ro_petugas"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ro_perujuk') }}">{{ number_format($dataRekap->sum("ro_perujuk"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('pot') }}">{{ number_format($dataRekap->sum("pot"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('tbm') }}">{{ number_format($dataRekap->sum("tbm"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('kamar') }}">{{ number_format($dataRekap->sum("kamar"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ok_jm_dr') }}">{{ number_format($dataRekap->sum("ok_jm_dr"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ok_jm_pr') }}">{{ number_format($dataRekap->sum("ok_jm_pr"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ok_js') }}">{{ number_format($dataRekap->sum("ok_js"), 0, ",", ".") }}</td>
                        <td class="text-right table-light" data-raw="{{ $dataRekap->sum('total') }}">{{ number_format($dataRekap->sum("total"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('total_piutang') }}">{{ number_format($dataRekap->sum("total_piutang"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('bayar_piutang') }}">{{ number_format($dataRekap->sum("bayar_piutang"), 0, ",", ".") }}</td>
                        <td class="text-right font-weight-bold table-danger text-danger" data-raw="{{ $dataRekap->sum('sisa_piutang') }}">{{ number_format($dataRekap->sum("sisa_piutang"), 0, ",", ".") }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Tab Rincian Nota Belum Bayar --}}
    <div class="tab-pane fade" id="rincian" role="tabpanel" aria-labelledby="rincian-tab">
        @forelse($rincianBayarPiutangGrouped as $bulan => $rincianBayarPiutang)
        @php
            $tableId = 'tableRincian_' . \Illuminate\Support\Str::slug($bulan);
        @endphp
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mt-4 mb-2">
            <h5 class="text-danger font-weight-bold mb-0">
                <i class="fas fa-calendar-alt mr-2"></i> Bulan Nota: {{ $bulan }}
            </h5>
            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyTable('{{ $tableId }}')">
                <i class="fas fa-copy mr-1"></i> Copy Tabel {{ $bulan }}
            </button>
        </div>

        <div class="table-responsive mt-2 mb-4" style="max-height: 60vh; overflow-y: auto;">
            <table class="table table-bordered table-hover table-sm align-middle text-nowrap table-rincian" id="{{ $tableId }}">
                <thead class="table-secondary text-center align-middle" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>No</th>
                        <th>No RM</th>
                        <th>Status Lanjut</th>
                        <th>Nama Pasien</th>
                        <th>Asuransi / Penjamin</th>
                        <th>No Nota</th>
                        <th>Registrasi</th>
                        <th>Obat+Emb+Tsl</th>
                        <th>Retur Obat</th>
                        <th>Resep Pulang</th>
                        <th>1 Paket Tindakan</th>
                        <th>2 Paket Tindakan</th>
                        <th>3 Paket Tindakan</th>
                        <th>4 Paket Tindakan</th>
                        <th>5 Paket Tindakan</th>
                        <th>6 Ranap Paramedis</th>
                        <th>Operasi</th>
                        <th>Laborat</th>
                        <th>Radiologi</th>
                        <th>Tambahan</th>
                        <th>Potongan</th>
                        <th>Kamar</th>
                        <th>Total Billing</th>
                        <th>Total Piutang</th>
                        <th>Sudah Dibayar</th>
                        <th>Sisa Piutang (Belum Bayar)</th>
                        <th>Status Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($rincianBayarPiutang as $rincian)
                    @php
                        $reg = $rincian->getRegistrasi->sum('totalbiaya');
                        $obat = $rincian->getObat->sum('totalbiaya');
                        $retur = $rincian->getReturObat->sum('totalbiaya');
                        $resep = $rincian->getResepPulang->sum('totalbiaya');
                        $ralanDr = $rincian->getRalanDokter->sum('totalbiaya');
                        $ralanDrPr = $rincian->getRalanDrParamedis->sum('totalbiaya');
                        $ralanPr = $rincian->getRalanParamedis->sum('totalbiaya');
                        $ranapDr = $rincian->getRanapDokter->sum('totalbiaya');
                        $ranapDrPr = $rincian->getRanapDrParamedis->sum('totalbiaya');
                        $ranapPr = $rincian->getRanapParamedis->sum('totalbiaya');
                        $oprasi = $rincian->getOprasi->sum('totalbiaya');
                        $lab = $rincian->getLaborat->sum('totalbiaya');
                        $rad = $rincian->getRadiologi->sum('totalbiaya');
                        $tmb = $rincian->getTambahan->sum('totalbiaya');
                        $pot = $rincian->getPotongan->sum('totalbiaya');
                        $kmr = $rincian->getKamarInap->sum('totalbiaya');

                        $rowTotalBilling = $reg + $obat + $retur + $resep + $ralanDr + $ralanDrPr + $ralanPr + $ranapDr + $ranapDrPr + $ranapPr + $oprasi + $lab + $rad + $tmb + $pot + $kmr;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center font-weight-bold" data-raw="{{ $rincian->no_rkm_medis }}">{{ $rincian->no_rkm_medis }}</td>
                        <td class="text-center">
                            <span class="badge {{ $rincian->status_lanjut == 'Ranap' ? 'badge-primary' : 'badge-info' }}">
                                {{ $rincian->status_lanjut }}
                            </span>
                        </td>
                        <td>{{ $rincian->nm_pasien }}</td>
                        <td><small class="font-weight-bold text-dark">{{ $rincian->png_jawab }}</small></td>
                        <td class="text-center" data-raw="{{ $rincian->getNomorNota->first()->nm_perawatan ?? '-' }}">
                            {{ $rincian->getNomorNota->first()->nm_perawatan ?? '-' }}
                        </td>
                        <td data-raw="{{ $reg }}">{{ number_format($reg, 0, ',', '.') }}</td>
                        <td data-raw="{{ $obat }}">{{ number_format($obat, 0, ',', '.') }}</td>
                        <td data-raw="{{ $retur }}">{{ number_format($retur, 0, ',', '.') }}</td>
                        <td data-raw="{{ $resep }}">{{ number_format($resep, 0, ',', '.') }}</td>
                        <td data-raw="{{ $ralanDr }}">{{ number_format($ralanDr, 0, ',', '.') }}</td>
                        <td data-raw="{{ $ralanDrPr }}">{{ number_format($ralanDrPr, 0, ',', '.') }}</td>
                        <td data-raw="{{ $ralanPr }}">{{ number_format($ralanPr, 0, ',', '.') }}</td>
                        <td data-raw="{{ $ranapDr }}">{{ number_format($ranapDr, 0, ',', '.') }}</td>
                        <td data-raw="{{ $ranapDrPr }}">{{ number_format($ranapDrPr, 0, ',', '.') }}</td>
                        <td data-raw="{{ $ranapPr }}">{{ number_format($ranapPr, 0, ',', '.') }}</td>
                        <td data-raw="{{ $oprasi }}">{{ number_format($oprasi, 0, ',', '.') }}</td>
                        <td data-raw="{{ $lab }}">{{ number_format($lab, 0, ',', '.') }}</td>
                        <td data-raw="{{ $rad }}">{{ number_format($rad, 0, ',', '.') }}</td>
                        <td data-raw="{{ $tmb }}">{{ number_format($tmb, 0, ',', '.') }}</td>
                        <td data-raw="{{ $pot }}">{{ number_format($pot, 0, ',', '.') }}</td>
                        <td data-raw="{{ $kmr }}">{{ number_format($kmr, 0, ',', '.') }}</td>
                        <td class="font-weight-bold table-light" data-raw="{{ $rowTotalBilling }}">{{ number_format($rowTotalBilling, 0, ',', '.') }}</td>
                        <td data-raw="{{ $rincian->totalpiutang }}">{{ number_format($rincian->totalpiutang, 0, ',', '.') }}</td>
                        <td data-raw="{{ $rincian->besar_cicilan }}">{{ number_format($rincian->besar_cicilan, 0, ',', '.') }}</td>
                        <td class="font-weight-bold text-danger table-danger" data-raw="{{ $rincian->sisapiutang }}">
                            {{ number_format($rincian->sisapiutang, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger">{{ $rincian->status_piutang }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-warning font-weight-bold text-right">
                    <tr>
                        <td colspan="6" class="text-center">SUBTOTAL {{ $bulan }}</td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRegistrasi->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRegistrasi->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getObat->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getObat->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getReturObat->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getReturObat->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getResepPulang->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getResepPulang->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRalanDokter->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRalanDokter->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRalanDrParamedis->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRalanDrParamedis->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRalanParamedis->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRalanParamedis->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRanapDokter->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRanapDokter->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRanapDrParamedis->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRanapDrParamedis->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRanapParamedis->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRanapParamedis->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getOprasi->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getOprasi->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getLaborat->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getLaborat->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getRadiologi->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRadiologi->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getTambahan->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getTambahan->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getPotongan->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getPotongan->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => $i->getKamarInap->sum('totalbiaya')) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getKamarInap->sum('totalbiaya')), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum(fn($i) => 
                            $i->getRegistrasi->sum('totalbiaya') + $i->getObat->sum('totalbiaya') + $i->getReturObat->sum('totalbiaya') + $i->getResepPulang->sum('totalbiaya')
                            + $i->getRalanDokter->sum('totalbiaya') + $i->getRalanDrParamedis->sum('totalbiaya') + $i->getRalanParamedis->sum('totalbiaya')
                            + $i->getRanapDokter->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya')
                            + $i->getOprasi->sum('totalbiaya') + $i->getLaborat->sum('totalbiaya') + $i->getRadiologi->sum('totalbiaya')
                            + $i->getTambahan->sum('totalbiaya') + $i->getPotongan->sum('totalbiaya') + $i->getKamarInap->sum('totalbiaya')
                        ) }}">
                            {{ number_format($rincianBayarPiutang->sum(fn($i) => 
                                $i->getRegistrasi->sum('totalbiaya') + $i->getObat->sum('totalbiaya') + $i->getReturObat->sum('totalbiaya') + $i->getResepPulang->sum('totalbiaya')
                                + $i->getRalanDokter->sum('totalbiaya') + $i->getRalanDrParamedis->sum('totalbiaya') + $i->getRalanParamedis->sum('totalbiaya')
                                + $i->getRanapDokter->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya')
                                + $i->getOprasi->sum('totalbiaya') + $i->getLaborat->sum('totalbiaya') + $i->getRadiologi->sum('totalbiaya')
                                + $i->getTambahan->sum('totalbiaya') + $i->getPotongan->sum('totalbiaya') + $i->getKamarInap->sum('totalbiaya')
                            ), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum('totalpiutang') }}">
                            {{ number_format($rincianBayarPiutang->sum('totalpiutang'), 0, ',', '.') }}
                        </td>
                        <td data-raw="{{ $rincianBayarPiutang->sum('besar_cicilan') }}">
                            {{ number_format($rincianBayarPiutang->sum('besar_cicilan'), 0, ',', '.') }}
                        </td>
                        <td class="text-danger table-danger" data-raw="{{ $rincianBayarPiutang->sum('sisapiutang') }}">
                            {{ number_format($rincianBayarPiutang->sum('sisapiutang'), 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @empty
            <div class="alert alert-light text-center py-4 border">
                <i class="fas fa-info-circle mr-1"></i> Tidak ada rincian data piutang belum bayar.
            </div>
        @endforelse
    </div>
</div>

<script>
// Helper to extract table text to matrix preserving colspan and rowspan
function tableToMatrix(table) {
    let rows = Array.from(table.querySelectorAll('tr'));
    let grid = [];

    rows.forEach((tr, rowIndex) => {
        if (!grid[rowIndex]) grid[rowIndex] = [];
        let colIndex = 0;

        Array.from(tr.children).forEach(cell => {
            while (grid[rowIndex][colIndex] !== undefined) {
                colIndex++;
            }

            let rowSpan = parseInt(cell.getAttribute('rowspan')) || 1;
            let colSpan = parseInt(cell.getAttribute('colspan')) || 1;

            let val = cell.getAttribute('data-raw');
            if (val === null || val === '') {
                val = cell.innerText.trim();
                if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(val)) {
                    val = val.replace(/\./g, '').replace(',', '.');
                }
            }

            for (let r = 0; r < rowSpan; r++) {
                let targetRow = rowIndex + r;
                if (!grid[targetRow]) grid[targetRow] = [];
                for (let c = 0; c < colSpan; c++) {
                    let targetCol = colIndex + c;
                    if (r === 0 && c === 0) {
                        grid[targetRow][targetCol] = val;
                    } else {
                        grid[targetRow][targetCol] = '';
                    }
                }
            }
            colIndex += colSpan;
        });
    });

    return grid;
}

// Clone table and sanitize numbers & styles for Excel HTML
function tableToCleanHtml(table) {
    let clone = table.cloneNode(true);
    clone.querySelectorAll('th, td').forEach(cell => {
        let text = cell.innerText.trim();
        let raw = cell.getAttribute('data-raw');

        if (raw !== null && raw !== '') {
            cell.innerText = raw;
            cell.setAttribute('style', (cell.getAttribute('style') || '') + '; mso-number-format:"\\#\\,\\#\\#0"; text-align: right;');
            cell.classList.add('num');
        } else if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(text)) {
            let clean = text.replace(/\./g, '').replace(',', '.');
            cell.innerText = clean;
            cell.setAttribute('style', (cell.getAttribute('style') || '') + '; mso-number-format:"\\#\\,\\#\\#0"; text-align: right;');
            cell.classList.add('num');
        } else if (/^0\d+$/.test(text) || text.includes('/') || (cell.cellIndex === 1 && table.id.includes('tableRincian'))) {
            cell.setAttribute('style', (cell.getAttribute('style') || '') + '; mso-number-format:"\\@";');
            cell.classList.add('txt');
        } else if (cell.querySelector('a')) {
            cell.innerText = text;
        }
    });
    return clone.outerHTML;
}

// Copy table to clipboard with proper numeric formatting
async function copyTable(tableIdOrElement) {
    let target = typeof tableIdOrElement === 'string' ? document.getElementById(tableIdOrElement) : tableIdOrElement;
    if (!target) return;

    let tables = [];
    if (target.tagName && target.tagName.toLowerCase() === 'table') {
        tables = [target];
    } else {
        tables = Array.from(target.querySelectorAll('table'));
    }

    if (tables.length === 0) return;

    let allTsv = [];
    let allHtmlTables = [];

    tables.forEach(table => {
        let matrix = tableToMatrix(table);
        allTsv.push(matrix.map(row => row.join('\t')).join('\r\n'));
        allHtmlTables.push(tableToCleanHtml(table));
    });

    let fullTsv = allTsv.join('\r\n\r\n');
    let fullHtml = `<html><head><meta charset="utf-8"><style>
        table { border-collapse: collapse; font-family: Calibri, sans-serif; font-size: 11pt; }
        th, td { border: 0.5pt solid #999; padding: 4px 8px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .num { mso-number-format: "\\#\\,\\#\\#0"; text-align: right; }
        .txt { mso-number-format: "\\@"; }
    </style></head><body>${allHtmlTables.join('<br><br>')}</body></html>`;

    let success = false;

    if (navigator.clipboard && window.ClipboardItem && window.isSecureContext) {
        try {
            const blobHtml = new Blob([fullHtml], { type: 'text/html' });
            const blobText = new Blob([fullTsv], { type: 'text/plain' });
            await navigator.clipboard.write([
                new ClipboardItem({
                    'text/html': blobHtml,
                    'text/plain': blobText
                })
            ]);
            success = true;
        } catch (err) {
            console.warn('ClipboardItem error, fallback to execCommand:', err);
        }
    }

    if (!success) {
        try {
            window._isCustomCopying = true;
            function onCopy(e) {
                e.clipboardData.setData('text/html', fullHtml);
                e.clipboardData.setData('text/plain', fullTsv);
                e.preventDefault();
            }
            document.addEventListener('copy', onCopy, { once: true });
            let dummy = document.createElement('textarea');
            dummy.value = fullTsv;
            dummy.style.position = 'fixed';
            dummy.style.left = '-9999px';
            dummy.style.top = '0';
            document.body.appendChild(dummy);
            dummy.focus();
            dummy.select();
            success = document.execCommand('copy');
            document.body.removeChild(dummy);
            window._isCustomCopying = false;
        } catch (e) {
            window._isCustomCopying = false;
            console.error('Fallback copy failed:', e);
        }
    }

    if (success) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Tabel Berhasil Disalin!',
                text: 'Format angka telah disesuaikan agar tidak menjadi desimal di Excel. Silakan paste (Ctrl+V) langsung di Excel.',
                timer: 2500,
                showConfirmButton: false
            });
        } else {
            alert('Tabel berhasil disalin ke clipboard! Format angka telah disesuaikan untuk Excel.');
        }
    } else {
        alert('Gagal menyalin tabel. Silakan gunakan tombol Download Excel.');
    }
}

// Copy active tab table
function copyActiveTable() {
    let rekapTable = document.getElementById('tableToCopy');
    if (!rekapTable || rekapTable.querySelector('tbody tr td[colspan]')) {
        alert('Tidak ada data untuk disalin. Silakan filter data terlebih dahulu.');
        return;
    }
    let rincianPane = document.getElementById('rincian');
    if (rincianPane && rincianPane.classList.contains('active')) {
        copyTable('rincian');
    } else {
        copyTable('tableToCopy');
    }
}

// Export clean Excel file
function exportSemuaExcel() {
    let rekapTable = document.getElementById('tableToCopy');
    if (!rekapTable || rekapTable.querySelector('tbody tr td[colspan]')) {
        alert('Tidak ada data untuk diekspor. Silakan filter data terlebih dahulu.');
        return;
    }
    let html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta charset="utf-8">
        <style>
            table { border-collapse: collapse; font-family: Calibri, sans-serif; font-size: 11pt; }
            th, td { border: 0.5pt solid #999; padding: 4px 8px; }
            th { background-color: #F8D7DA; font-weight: bold; text-align: center; }
            .num { mso-number-format: "\\#\\,\\#\\#0"; text-align: right; }
            .txt { mso-number-format: "\\@"; }
        </style>
    </head><body>`;
    
    // Tabel Rekapan
    if (rekapTable) {
        html += "<h2>Rekap Pendapatan Asuransi Belum Bayar</h2>" + tableToCleanHtml(rekapTable) + "<br><br>";
    }
    
    // Tabel Rincian
    let rincianContainer = document.getElementById('rincian');
    if (rincianContainer) {
        let monthHeaders = rincianContainer.querySelectorAll('h5');
        let rincianTables = rincianContainer.querySelectorAll('table');
        
        for (let i = 0; i < rincianTables.length; i++) {
            let title = monthHeaders[i] ? monthHeaders[i].innerText : "Rincian Nota Belum Bayar";
            html += "<h2>" + title + "</h2>" + tableToCleanHtml(rincianTables[i]) + "<br><br>";
        }
    }
    
    html += "</body></html>";
    
    let blob = new Blob([html], { type: "application/vnd.ms-excel;charset=utf-8" });
    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    let tgl1 = document.querySelector('input[name="tgl1"]').value;
    let tgl2 = document.querySelector('input[name="tgl2"]').value;
    a.download = `Asuransi_Belum_Bayar_${tgl1}_sd_${tgl2}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Global copy listener
document.addEventListener('copy', function(e) {
    if (window._isCustomCopying) return;

    let selection = window.getSelection();
    if (!selection || !selection.rangeCount || selection.isCollapsed) return;

    let range = selection.getRangeAt(0);
    let container = range.commonAncestorContainer;
    let table = (container.nodeType === 1 ? container : container.parentElement)?.closest('table');

    if (!table || (!table.id.includes('tableToCopy') && !table.classList.contains('table-rincian') && !table.closest('#rekapTabsContent'))) {
        return;
    }

    let clonedFrag = range.cloneContents();
    let div = document.createElement('div');
    div.appendChild(clonedFrag);

    let cells = div.querySelectorAll('th, td');
    if (cells.length > 0) {
        cells.forEach(cell => {
            let text = cell.innerText.trim();
            let raw = cell.getAttribute('data-raw');
            if (raw !== null && raw !== '') {
                cell.innerText = raw;
                cell.setAttribute('style', (cell.getAttribute('style') || '') + '; mso-number-format:"\\#\\,\\#\\#0"; text-align: right;');
            } else if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(text)) {
                let cleanNum = text.replace(/\./g, '').replace(',', '.');
                cell.innerText = cleanNum;
                cell.setAttribute('style', (cell.getAttribute('style') || '') + '; mso-number-format:"\\#\\,\\#\\#0"; text-align: right;');
            }
        });

        let plainText = div.innerText.replace(/(\b\d{1,3}(?:\.\d{3})+(?:,\d+)?\b)/g, function(match) {
            return match.replace(/\./g, '').replace(',', '.');
        });

        e.clipboardData.setData('text/html', div.innerHTML);
        e.clipboardData.setData('text/plain', plainText);
        e.preventDefault();
    }
});

// Loading state on filter submission
document.getElementById('formFilterAsuransi')?.addEventListener('submit', function() {
    let btn = document.getElementById('btnFilterAsuransi');
    if (btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Memuat...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    }
});
</script>

@endsection
