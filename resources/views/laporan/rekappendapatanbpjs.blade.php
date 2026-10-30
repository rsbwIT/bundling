@extends('..layout.layoutDashboard')

@section('title', 'Rekap Pendapatan BPJS')

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
    /* Biar tabel nggak terlalu lebar dan font cukup kecil */
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
                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Rekap Pendapatan BPJS
                </h5>
                <small class="text-muted">Laporan rincian pendapatan BPJS berdasarkan tanggal cetak nota</small>
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
        {{-- Form Filter Periode Tanggal --}}
        <form method="GET" action="{{ url('/rekap-pendapatan-bpjs') }}" class="mb-4" id="formFilterBpjs">
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
                <div class="col-md-2 col-sm-12 mb-2">
                    <button type="submit" class="btn btn-sm btn-primary btn-block" id="btnFilterBpjs">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        @if(!request()->has('filter'))
            <div class="alert alert-info border text-center py-4 mb-4">
                <i class="fas fa-filter fa-2x mb-2 text-primary"></i>
                <h6 class="font-weight-bold text-dark mb-1">Data Belum Dimuat</h6>
                <p class="mb-0 text-muted small">Silakan pilih periode tanggal cetak nota di atas kemudian klik tombol <strong>Filter</strong> untuk menampilkan data Rekap Pendapatan BPJS.</p>
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
                <button class="nav-link font-weight-bold text-info" id="rincian-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#rincian" data-bs-target="#rincian" type="button" role="tab" aria-controls="rincian" aria-selected="false">
                    <i class="fas fa-list mr-1"></i> Rincian Bayar Piutang
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
                        <th rowspan="2">TOTAL</th>
                        <th rowspan="2">(+)<br>DP EKSES</th>
                        <th rowspan="2">(-)<br>EKSES</th>
                        <th rowspan="2">BAYAR<br>PIUTANG</th>
                        <th rowspan="2">Piutang<br>Obat (+)</th>
                        <th rowspan="2">COB<br>COB (+)</th>
                        <th rowspan="2">TMB (+)</th>
                        <th rowspan="2">POT (-)</th>
                        <th rowspan="2" class="table-primary text-dark">GRAND<br>TOTAL</th>
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
                                    <a href="{{ url('/rekap-pendapatan-bpjs/detail?tgl1='.$tgl1.'&tgl2='.$tgl2.'&tgl_nota='.$item->tgl_nota.'&status_lanjut='.$item->status_lanjut) }}" target="_blank" class="text-decoration-none">
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
                                <td data-raw="{{ $item->dp_ekses }}">{{ number_format($item->dp_ekses, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->ekses }}">{{ number_format($item->ekses, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->bayar_piutang }}">
                                    @if($item->bayar_piutang > 0)
                                        <span class="text-info font-weight-bold">{{ number_format($item->bayar_piutang, 0, ',', '.') }}</span>
                                    @else
                                        {{ number_format($item->bayar_piutang, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td data-raw="{{ $item->piutang_obat }}">{{ number_format($item->piutang_obat, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->cob }}">{{ number_format($item->cob, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->tmb_bayar }}">{{ number_format($item->tmb_bayar, 0, ',', '.') }}</td>
                                <td data-raw="{{ $item->pot_bayar }}">{{ number_format($item->pot_bayar, 0, ',', '.') }}</td>
                                <td class="font-weight-bold table-primary text-dark" data-raw="{{ $item->grand_total }}">{{ number_format($item->grand_total, 0, ',', '.') }}</td>
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
                            <td class="text-right" data-raw="{{ collect($items)->sum('dp_ekses') }}">{{ number_format(collect($items)->sum("dp_ekses"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('ekses') }}">{{ number_format(collect($items)->sum("ekses"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('bayar_piutang') }}">{{ number_format(collect($items)->sum("bayar_piutang"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('piutang_obat') }}">{{ number_format(collect($items)->sum("piutang_obat"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('cob') }}">{{ number_format(collect($items)->sum("cob"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('tmb_bayar') }}">{{ number_format(collect($items)->sum("tmb_bayar"), 0, ",", ".") }}</td>
                            <td class="text-right" data-raw="{{ collect($items)->sum('pot_bayar') }}">{{ number_format(collect($items)->sum("pot_bayar"), 0, ",", ".") }}</td>
                            <td class="text-right font-weight-bold table-primary text-dark" data-raw="{{ collect($items)->sum('grand_total') }}">{{ number_format(collect($items)->sum("grand_total"), 0, ",", ".") }}</td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="33" class="text-center py-4 text-muted">
                                @if(request()->has('filter'))
                                    <i class="fas fa-info-circle mr-1"></i> Tidak ada data pendapatan BPJS pada periode ini.
                                @else
                                    <i class="fas fa-info-circle mr-1"></i> Silakan tentukan periode tanggal dan klik tombol <strong>Filter</strong> untuk memunculkan data.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($dataRekap->isNotEmpty())
                <tfoot class="table-warning font-weight-bold">
                    <tr>
                        <td class="text-center" colspan="2">TOTAL</td>
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
                        <td class="text-right" data-raw="{{ $dataRekap->sum('dp_ekses') }}">{{ number_format($dataRekap->sum("dp_ekses"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('ekses') }}">{{ number_format($dataRekap->sum("ekses"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('bayar_piutang') }}">{{ number_format($dataRekap->sum("bayar_piutang"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('piutang_obat') }}">{{ number_format($dataRekap->sum("piutang_obat"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('cob') }}">{{ number_format($dataRekap->sum("cob"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('tmb_bayar') }}">{{ number_format($dataRekap->sum("tmb_bayar"), 0, ",", ".") }}</td>
                        <td class="text-right" data-raw="{{ $dataRekap->sum('pot_bayar') }}">{{ number_format($dataRekap->sum("pot_bayar"), 0, ",", ".") }}</td>
                        <td class="text-right font-weight-bold table-primary text-dark" data-raw="{{ $dataRekap->sum('grand_total') }}">{{ number_format($dataRekap->sum("grand_total"), 0, ",", ".") }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Tab Rincian Bayar Piutang --}}
    <div class="tab-pane fade" id="rincian" role="tabpanel" aria-labelledby="rincian-tab">
        @forelse($rincianBayarPiutangGrouped as $bulan => $rincianBayarPiutang)
        @php
            $tableId = 'tableRincian_' . \Illuminate\Support\Str::slug($bulan);
        @endphp
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mt-4 mb-2">
            <h5 class="text-primary font-weight-bold mb-0">
                <i class="fas fa-calendar-alt mr-2"></i> Bulan Nota: {{ $bulan }}
            </h5>
            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyTable('{{ $tableId }}')">
                <i class="fas fa-copy mr-1"></i> Copy Tabel {{ $bulan }}
            </button>
        </div>

        @php
            $total_registrasi = $rincianBayarPiutang->sum(fn($i) => $i->getRegistrasi->sum('totalbiaya'));
            $total_obat = $rincianBayarPiutang->sum(fn($i) => $i->getObat->sum('totalbiaya'));
            $total_retur = $rincianBayarPiutang->sum(fn($i) => $i->getReturObat->sum('totalbiaya'));
            $total_resep = $rincianBayarPiutang->sum(fn($i) => $i->getResepPulang->sum('totalbiaya'));
            $total_paket = $rincianBayarPiutang->sum(fn($i) =>
                $i->getRalanDokter->sum('totalbiaya')
                + $i->getRalanParamedis->sum('totalbiaya')
                + $i->getRalanDrParamedis->sum('totalbiaya')
                + $i->getRanapDokter->sum('totalbiaya')
                + $i->getRanapDrParamedis->sum('totalbiaya')
                + $i->getRanapParamedis->sum('totalbiaya')
            );
            $total_oprasi = $rincianBayarPiutang->sum(fn($i) => $i->getOprasi->sum('totalbiaya'));
            $total_laborat = $rincianBayarPiutang->sum(fn($i) => $i->getLaborat->sum('totalbiaya'));
            $total_radiologi = $rincianBayarPiutang->sum(fn($i) => $i->getRadiologi->sum('totalbiaya'));
            $total_tambahan = $rincianBayarPiutang->sum(fn($i) => $i->getTambahan->sum('totalbiaya'));
            $total_kamar = $rincianBayarPiutang->sum(fn($i) => $i->getKamarInap->sum('totalbiaya'));
            $total_potongan = $rincianBayarPiutang->sum(fn($i) => $i->getPotongan->sum('totalbiaya'));

            $grand_total =
                $total_registrasi + $total_obat + $total_retur + $total_resep +
                $total_paket + $total_oprasi + $total_laborat + $total_radiologi +
                $total_tambahan + $total_kamar + $total_potongan;

            $total_cicilan = $rincianBayarPiutang->sum('besar_cicilan');
            $total_diskon = $rincianBayarPiutang->sum('diskon_piutang');
            $total_tidak_terbayar = $rincianBayarPiutang->sum('tidak_terbayar');
            $total_uangmuka = $rincianBayarPiutang->sum('uangmuka');
        @endphp

        <div class="table-responsive mt-2 mb-4" style="max-height: 60vh; overflow-y: auto;">
            <table class="table table-bordered table-hover table-sm align-middle text-nowrap table-rincian" id="{{ $tableId }}">
                <thead class="table-secondary text-center align-middle" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th>No</th>
                        <th>Tgl Bayar</th>
                        <th>No RM</th>
                        <th>Status Lanjut</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Bayar</th>
                        <th>No Nota</th>
                        <th>Registrasi</th>
                        <th>Obat+Emb+Tsl</th>
                        <th>Retur Obat</th>
                        <th>Resep Pulang</th>
                        <th>Paket Tindakan</th>
                        <th>Operasi</th>
                        <th>Laborat</th>
                        <th>Radiologi</th>
                        <th>Tambahan</th>
                        <th>Kamar+Service</th>
                        <th>Potongan</th>
                        <th>Total</th>
                        <th>Ekses / Uang Muka</th>
                        <th>Cicilan</th>
                        <th>Diskon Bayar</th>
                        <th>Tidak Terbayar</th>
                        <th>Catatan</th>
                        <th>No.Rawat / No.Tagihan</th>
                        <th>No.SEP</th>
                        <th>Status</th>
                        <th>Dokter</th>
                        <th>Paramedis</th>
                        <th>Dokter Paramedis</th>
                        <th>Total Paket Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse($rincianBayarPiutang as $item)
                        @php
                            $td = $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya');
                            $tp = $item->getRalanParamedis->sum('totalbiaya') + $item->getRanapParamedis->sum('totalbiaya');
                            $tdp = $item->getRalanDrParamedis->sum('totalbiaya') + $item->getRanapDrParamedis->sum('totalbiaya');
                            $tk = $td + $tp + $tdp;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="text-center">{{ $item->tgl_bayar }}</td>
                            <td class="text-center">{{ $item->no_rkm_medis }}</td>
                            <td class="text-center">{{ $item->status_lanjut }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>
                                @foreach ($item->getNomorNota as $n)
                                    {{ str_replace(':', '', $n->nm_perawatan) }}<br>
                                @endforeach
                            </td>
                            <td class="text-right" data-raw="{{ $item->getRegistrasi->sum('totalbiaya') }}">{{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getObat->sum('totalbiaya') }}">{{ number_format($item->getObat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getReturObat->sum('totalbiaya') }}">{{ number_format($item->getReturObat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getResepPulang->sum('totalbiaya') }}">{{ number_format($item->getResepPulang->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $tk }}">{{ number_format($tk, 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getOprasi->sum('totalbiaya') }}">{{ number_format($item->getOprasi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getLaborat->sum('totalbiaya') }}">{{ number_format($item->getLaborat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getRadiologi->sum('totalbiaya') }}">{{ number_format($item->getRadiologi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getTambahan->sum('totalbiaya') }}">{{ number_format($item->getTambahan->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getKamarInap->sum('totalbiaya') }}">{{ number_format($item->getKamarInap->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right" data-raw="{{ $item->getPotongan->sum('totalbiaya') }}">{{ number_format($item->getPotongan->sum('totalbiaya'), 0, ',', '.') }}</td>
                            @php
                                $subTotalRow = $item->getRegistrasi->sum('totalbiaya') +
                                    $item->getObat->sum('totalbiaya') +
                                    $item->getReturObat->sum('totalbiaya') +
                                    $item->getResepPulang->sum('totalbiaya') +
                                    $tk +
                                    $item->getOprasi->sum('totalbiaya') +
                                    $item->getLaborat->sum('totalbiaya') +
                                    $item->getRadiologi->sum('totalbiaya') +
                                    $item->getTambahan->sum('totalbiaya') +
                                    $item->getKamarInap->sum('totalbiaya') +
                                    $item->getPotongan->sum('totalbiaya');
                            @endphp
                            <td class="text-right font-weight-bold" data-raw="{{ $subTotalRow }}">
                                {{ number_format($subTotalRow, 0, ',', '.') }}
                            </td>
                            <td class="text-right" data-raw="{{ $item->uangmuka }}">{{ number_format($item->uangmuka, 0, ',', '.') }}</td>
                            <td class="text-right text-info font-weight-bold" data-raw="{{ $item->besar_cicilan }}">{{ number_format($item->besar_cicilan, 0, ',', '.') }}</td>
                            <td class="text-right text-warning" data-raw="{{ $item->diskon_piutang }}">{{ number_format($item->diskon_piutang, 0, ',', '.') }}</td>
                            <td class="text-right text-danger" data-raw="{{ $item->tidak_terbayar }}">{{ number_format($item->tidak_terbayar, 0, ',', '.') }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>
                                @foreach ($item->getNoSep as $sep)
                                    {{ $sep->no_sep }}<br>
                                @endforeach
                            </td>
                            <td class="text-center">{{ $item->status }}</td>
                            <td class="text-right text-primary fw-semibold" data-raw="{{ $td }}">{{ number_format($td, 0, ',', '.') }}</td>
                            <td class="text-right text-success fw-semibold" data-raw="{{ $tp }}">{{ number_format($tp, 0, ',', '.') }}</td>
                            <td class="text-right text-secondary fw-semibold" data-raw="{{ $tdp }}">{{ number_format($tdp, 0, ',', '.') }}</td>
                            <td class="text-right text-danger font-weight-bold" data-raw="{{ $tk }}">{{ number_format($tk, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="31" class="text-center py-3 text-muted">
                                Tidak ada data pembayaran piutang pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($rincianBayarPiutang) > 0)
                <tfoot class="table-warning font-weight-bold text-center align-middle">
                    <tr>
                        <td colspan="7">TOTAL</td>
                        <td class="text-right" data-raw="{{ $total_registrasi }}">{{ number_format($total_registrasi, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_obat }}">{{ number_format($total_obat, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_retur }}">{{ number_format($total_retur, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_resep }}">{{ number_format($total_resep, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_paket }}">{{ number_format($total_paket, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_oprasi }}">{{ number_format($total_oprasi, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_laborat }}">{{ number_format($total_laborat, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_radiologi }}">{{ number_format($total_radiologi, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_tambahan }}">{{ number_format($total_tambahan, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_kamar }}">{{ number_format($total_kamar, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_potongan }}">{{ number_format($total_potongan, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $grand_total }}">{{ number_format($grand_total, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_uangmuka }}">{{ number_format($total_uangmuka, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_cicilan }}">{{ number_format($total_cicilan, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_diskon }}">{{ number_format($total_diskon, 0, ',', '.') }}</td>
                        <td class="text-right" data-raw="{{ $total_tidak_terbayar }}">{{ number_format($total_tidak_terbayar, 0, ',', '.') }}</td>
                        <td colspan="4"></td>
                        @php
                            $total_dr = $rincianBayarPiutang->sum(fn($i) => $i->getRalanDokter->sum('totalbiaya') + $i->getRanapDokter->sum('totalbiaya'));
                            $total_pr = $rincianBayarPiutang->sum(fn($i) => $i->getRalanParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya'));
                            $total_drpr = $rincianBayarPiutang->sum(fn($i) => $i->getRalanDrParamedis->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya'));
                        @endphp
                        <td class="text-right text-primary" data-raw="{{ $total_dr }}">{{ number_format($total_dr, 0, ',', '.') }}</td>
                        <td class="text-right text-success" data-raw="{{ $total_pr }}">{{ number_format($total_pr, 0, ',', '.') }}</td>
                        <td class="text-right text-secondary" data-raw="{{ $total_drpr }}">{{ number_format($total_drpr, 0, ',', '.') }}</td>
                        <td class="text-right text-danger" data-raw="{{ $total_paket }}">{{ number_format($total_paket, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @empty
            <div class="alert alert-light border mt-3 text-center text-muted">
                @if(request()->has('filter'))
                    <i class="fas fa-info-circle mr-1"></i> Tidak ada data pembayaran piutang pada rentang tanggal yang dipilih.
                @else
                    <i class="fas fa-info-circle mr-1"></i> Silakan tentukan periode tanggal dan klik tombol <strong>Filter</strong> untuk memunculkan data rincian bayar piutang.
                @endif
            </div>
        @endforelse
    </div>
</div>
</div>
</div>

<script>
// Flag to prevent recursive copy events
window._isCustomCopying = false;

// Convert table to 2D matrix handling rowspan and colspan for clean TSV export
function tableToMatrix(table) {
    const grid = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach((tr, rowIndex) => {
        if (!grid[rowIndex]) grid[rowIndex] = [];
        let colIndex = 0;

        tr.querySelectorAll('th, td').forEach(cell => {
            while (grid[rowIndex][colIndex] !== undefined) {
                colIndex++;
            }

            const rowSpan = parseInt(cell.getAttribute('rowspan')) || 1;
            const colSpan = parseInt(cell.getAttribute('colspan')) || 1;

            let text = cell.innerText.trim().replace(/\r?\n+/g, ' ');
            let raw = cell.getAttribute('data-raw');

            if (raw !== null && raw !== '') {
                text = raw;
            } else if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(text)) {
                text = text.replace(/\./g, '').replace(',', '.');
            }

            for (let r = 0; r < rowSpan; r++) {
                const targetRow = rowIndex + r;
                if (!grid[targetRow]) grid[targetRow] = [];
                for (let c = 0; c < colSpan; c++) {
                    const targetCol = colIndex + c;
                    if (r === 0 && c === 0) {
                        grid[targetRow][targetCol] = text;
                    } else if (c === 0 && cell.tagName.toLowerCase() === 'td') {
                        // For rowspanned td (like Tahun in tbody), repeat the text so every row has its label in TSV
                        grid[targetRow][targetCol] = text;
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
        } else if (/^0\d+$/.test(text) || text.includes('/') || (cell.cellIndex === 2 && table.id.includes('tableRincian'))) {
            // Text format for No RM, No Rawat, No SEP to keep leading zeros
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

    // Method 1: Clipboard API with ClipboardItem
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
            console.warn('ClipboardItem error, falling back to execCommand:', err);
        }
    }

    // Method 2: Fallback using execCommand with copy listener
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
            th { background-color: #E2EFDA; font-weight: bold; text-align: center; }
            .num { mso-number-format: "\\#\\,\\#\\#0"; text-align: right; }
            .txt { mso-number-format: "\\@"; }
        </style>
    </head><body>`;
    
    // Tabel Rekapan
    if (rekapTable) {
        html += "<h2>Rekapan Pendapatan BPJS</h2>" + tableToCleanHtml(rekapTable) + "<br><br>";
    }
    
    // Tabel Rincian
    let rincianContainer = document.getElementById('rincian');
    if (rincianContainer) {
        let monthHeaders = rincianContainer.querySelectorAll('h5');
        let rincianTables = rincianContainer.querySelectorAll('table');
        
        for (let i = 0; i < rincianTables.length; i++) {
            let title = monthHeaders[i] ? monthHeaders[i].innerText : "Rincian Bayar Piutang";
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
    a.download = `Laporan_BPJS_${tgl1}_sd_${tgl2}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Global copy listener to intercept manual selection + Ctrl+C on table
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
document.getElementById('formFilterBpjs')?.addEventListener('submit', function() {
    let btn = document.getElementById('btnFilterBpjs');
    if (btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Memuat...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    }
});
</script>

@endsection
