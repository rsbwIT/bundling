@extends('..layout.layoutDashboard')

@section('title', 'Rekap Pendapatan Asuransi')

@section('konten')

<style>
    #tableToCopy th {
        text-align: center;
        vertical-align: middle;
        font-size: 11px;
    }
    #tableToCopy td {
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

        {{-- Tabel Rekap --}}
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
                        <th rowspan="2">SUDAH BAYAR</th>
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
                                <td>{{ number_format($item->sudah_bayar, 0, ',', '.') }}</td>
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
                            <td class="text-right">{{ number_format(collect($items)->sum("sudah_bayar"), 0, ",", ".") }}</td>
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
                        <td class="text-right">{{ number_format($dataRekap->sum("sudah_bayar"), 0, ",", ".") }}</td>
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
</div>

<script>
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
