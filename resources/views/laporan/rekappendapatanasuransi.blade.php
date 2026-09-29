@extends('..layout.layoutDashboard')

@section('title', 'Rekap Pendapatan Asuransi')

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
                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Rekap Pendapatan Asuransi
                </h5>
                <small class="text-muted">Laporan rincian pendapatan asuransi bulanan dengan penambahan ekses dan penyesuaian piutang</small>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-success mr-2" onclick="exportSemuaExcel()">
                    <i class="fas fa-file-excel mr-1"></i> Download Excel
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="copyButton" onclick="copyTable('tableToCopy')">
                    <i class="fas fa-copy mr-1"></i> Copy Table
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Form Filter Periode Tanggal --}}
        <form method="GET" action="{{ url('/rekap-pendapatan-asuransi') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Awal:</label>
                    <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Akhir:</label>
                    <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
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
                                    <a href="{{ url('/rekap-pendapatan-asuransi/detail?tgl1='.$tgl1.'&tgl2='.$tgl2.'&tgl_nota='.$item->tgl_nota.'&status_lanjut='.$item->status_lanjut) }}" target="_blank" class="text-decoration-none">
                                        {{ $item->nama_bulan }}
                                    </a>
                                </td>
                                <td>{{ number_format($item->reg, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->js, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->jm_dr, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->pr, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->kso, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->obat, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->retur, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->lab_js, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->lab_bhp, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->lab_jm_pj, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ro_js, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ro_bhp, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ro_jm_pj, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ro_petugas, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ro_perujuk, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->pot, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->tbm, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->kamar, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ok_jm_dr, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ok_jm_pr, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ok_js, 0, ',', '.') }}</td>
                                <td class="font-weight-bold table-light">{{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->dp_ekses, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->ekses, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->bayar_piutang > 0)
                                        <span class="text-info font-weight-bold">{{ number_format($item->bayar_piutang, 0, ',', '.') }}</span>
                                    @else
                                        {{ number_format($item->bayar_piutang, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>{{ number_format($item->piutang_obat, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->cob, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->tmb_bayar, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->pot_bayar, 0, ',', '.') }}</td>
                                <td class="font-weight-bold table-primary text-dark">{{ number_format($item->grand_total, 0, ',', '.') }}</td>
                            </tr>

                        @endforeach
                        {{-- Subtotal per Group --}}
                        <tr class="table-info font-weight-bold">
                            <td colspan="2" class="text-right">TOTAL {{ $groupKey }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("reg"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("js"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("bhp"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("jm_dr"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("pr"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("kso"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("obat"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("retur"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("lab_js"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("lab_bhp"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("lab_jm_pj"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ro_js"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ro_bhp"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ro_jm_pj"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ro_petugas"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ro_perujuk"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("pot"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("tbm"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("kamar"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ok_jm_dr"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ok_jm_pr"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ok_js"), 0, ",", ".") }}</td>
                            <td class="text-right table-light">{{ number_format(collect($items)->sum("total"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("dp_ekses"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("ekses"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("bayar_piutang"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("piutang_obat"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("cob"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("tmb_bayar"), 0, ",", ".") }}</td>
                            <td class="text-right">{{ number_format(collect($items)->sum("pot_bayar"), 0, ",", ".") }}</td>
                            <td class="text-right font-weight-bold table-primary text-dark">{{ number_format(collect($items)->sum("grand_total"), 0, ",", ".") }}</td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="33" class="text-center py-3 text-muted">
                                Silakan filter tanggal untuk menampilkan data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-warning font-weight-bold">
                    <tr>
                        <td class="text-center" colspan="2">TOTAL</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("reg"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("js"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("bhp"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("jm_dr"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("pr"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("kso"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("obat"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("retur"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("lab_js"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("lab_bhp"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("lab_jm_pj"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ro_js"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ro_bhp"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ro_jm_pj"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ro_petugas"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ro_perujuk"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("pot"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("tbm"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("kamar"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ok_jm_dr"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ok_jm_pr"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ok_js"), 0, ",", ".") }}</td>
                        <td class="text-right table-light">{{ number_format($dataRekap->sum("total"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("dp_ekses"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("ekses"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("bayar_piutang"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("piutang_obat"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("cob"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("tmb_bayar"), 0, ",", ".") }}</td>
                        <td class="text-right">{{ number_format($dataRekap->sum("pot_bayar"), 0, ",", ".") }}</td>
                        <td class="text-right font-weight-bold table-primary text-dark">{{ number_format($dataRekap->sum("grand_total"), 0, ",", ".") }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Tab Rincian Bayar Piutang --}}
    <div class="tab-pane fade" id="rincian" role="tabpanel" aria-labelledby="rincian-tab">
        @forelse($rincianBayarPiutangGrouped as $bulan => $rincianBayarPiutang)
        <div class="mt-4 mb-2">
            <h5 class="text-primary font-weight-bold border-bottom pb-2">
                <i class="fas fa-calendar-alt mr-2"></i> Bulan Nota: {{ $bulan }}
            </h5>
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
            <table class="table table-bordered table-hover table-sm align-middle text-nowrap" id="tableRincian">
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
                            <td class="text-right">{{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getObat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getReturObat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getResepPulang->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($tk, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getOprasi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getLaborat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getRadiologi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getTambahan->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getKamarInap->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->getPotongan->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">
                                {{ number_format(
                                    $item->getRegistrasi->sum('totalbiaya') +
                                    $item->getObat->sum('totalbiaya') +
                                    $item->getReturObat->sum('totalbiaya') +
                                    $item->getResepPulang->sum('totalbiaya') +
                                    $tk +
                                    $item->getOprasi->sum('totalbiaya') +
                                    $item->getLaborat->sum('totalbiaya') +
                                    $item->getRadiologi->sum('totalbiaya') +
                                    $item->getTambahan->sum('totalbiaya') +
                                    $item->getKamarInap->sum('totalbiaya') +
                                    $item->getPotongan->sum('totalbiaya'),
                                0, ',', '.') }}
                            </td>
                            <td class="text-right">{{ number_format($item->uangmuka, 0, ',', '.') }}</td>
                            <td class="text-right text-info font-weight-bold">{{ number_format($item->besar_cicilan, 0, ',', '.') }}</td>
                            <td class="text-right text-warning">{{ number_format($item->diskon_piutang, 0, ',', '.') }}</td>
                            <td class="text-right text-danger">{{ number_format($item->tidak_terbayar, 0, ',', '.') }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>
                                @foreach ($item->getNoSep as $sep)
                                    {{ $sep->no_sep }}<br>
                                @endforeach
                            </td>
                            <td class="text-center">{{ $item->status }}</td>
                            <td class="text-right text-primary fw-semibold">{{ number_format($td, 0, ',', '.') }}</td>
                            <td class="text-right text-success fw-semibold">{{ number_format($tp, 0, ',', '.') }}</td>
                            <td class="text-right text-secondary fw-semibold">{{ number_format($tdp, 0, ',', '.') }}</td>
                            <td class="text-right text-danger font-weight-bold">{{ number_format($tk, 0, ',', '.') }}</td>
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
                        <td class="text-right">{{ number_format($total_registrasi, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_obat, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_retur, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_resep, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_paket, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_oprasi, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_laborat, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_radiologi, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_tambahan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_kamar, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_potongan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($grand_total, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_uangmuka, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_cicilan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_diskon, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($total_tidak_terbayar, 0, ',', '.') }}</td>
                        <td colspan="4"></td>
                        <td class="text-right text-primary">{{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRalanDokter->sum('totalbiaya') + $i->getRanapDokter->sum('totalbiaya')), 0, ',', '.') }}</td>
                        <td class="text-right text-success">{{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRalanParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya')), 0, ',', '.') }}</td>
                        <td class="text-right text-secondary">{{ number_format($rincianBayarPiutang->sum(fn($i) => $i->getRalanDrParamedis->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya')), 0, ',', '.') }}</td>
                        <td class="text-right text-danger">{{ number_format($total_paket, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @empty
            <div class="alert alert-info mt-3 text-center">
                Tidak ada data pembayaran piutang pada rentang tanggal yang dipilih.
            </div>
        @endforelse
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
        html += "<h2>Rekapan Pendapatan Asuransi</h2>" + rekapTable.outerHTML + "<br><br>";
    }
    
    // Tabel Rincian
    let rincianContainer = document.getElementById('rincian');
    if (rincianContainer) {
        let monthHeaders = rincianContainer.querySelectorAll('h5');
        let rincianTables = rincianContainer.querySelectorAll('table');
        
        for (let i = 0; i < rincianTables.length; i++) {
            let title = monthHeaders[i] ? monthHeaders[i].innerText : "Rincian Bayar Piutang";
            html += "<h2>" + title + "</h2>" + rincianTables[i].outerHTML + "<br><br>";
        }
    }
    
    html += "</body></html>";
    
    let blob = new Blob([html], { type: "application/vnd.ms-excel" });
    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    let tgl1 = document.querySelector('input[name="tgl1"]').value;
    let tgl2 = document.querySelector('input[name="tgl2"]').value;
    a.download = `Laporan_Asuransi_${tgl1}_sd_${tgl2}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function copyTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;

    let range = document.createRange();
    range.selectNode(table);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);

    try {
        document.execCommand('copy');
        alert('Tabel berhasil disalin ke clipboard!');
    } catch (err) {
        alert('Gagal menyalin tabel.');
    }
    window.getSelection().removeAllRanges();
}
</script>

@endsection
