@extends('..layout.layoutDashboard')

@section('title', 'Rekap Pendapatan Harian - ' . ($statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Semua Pelayanan' : 'Rawat Inap')) . ' (' . $penjaminLabel . ')')

@section('konten')

<style>
    #tableToCopy th {
        text-align: center;
        vertical-align: middle;
    }
    #tableToCopy td:nth-child(1) {
        text-align: center;
    }
    #tableToCopy td:nth-child(n+2) {
        text-align: right;
    }
</style>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark">
                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Rekap Pendapatan Harian ({{ $statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Rawat Inap & Jalan' : 'Rawat Inap') }}) - <span class="text-primary">{{ $penjaminLabel }}</span>
                </h5>
                <small class="text-muted">Laporan rincian pendapatan harian {{ $statusLanjut == 'Ralan' ? 'rawat jalan' : ($statusLanjut == 'SEMUA' ? 'rawat inap dan rawat jalan' : 'rawat inap') }} dengan penjamin {{ $penjaminLabel }} <span class="badge badge-light border ml-1">{{ $kdPj == 'UMU' ? 'Berdasarkan Tanggal Nota' : ($kdPj == 'SEMUA' ? 'Umum: Tgl Nota | BPJS & Asr: Tgl Bayar Piutang' : 'Berdasarkan Tanggal Pembayaran Piutang') }}</span></small>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="copyButton" onclick="copyTable('tableToCopy')">
                    <i class="fas fa-copy mr-1"></i> Copy Table
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Form Filter Periode & Jenis Pelayanan --}}
        <form method="GET" action="{{ url('/rekap-pendapatan-harian') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Awal:</label>
                    <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Akhir:</label>
                    <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Jenis Pelayanan:</label>
                    <select name="status_lanjut" class="form-control form-control-sm">
                        <option value="Ranap" {{ $statusLanjut == 'Ranap' ? 'selected' : '' }}>Rawat Inap (Ranap)</option>
                        <option value="Ralan" {{ $statusLanjut == 'Ralan' ? 'selected' : '' }}>Rawat Jalan (Ralan)</option>
                        <option value="SEMUA" {{ $statusLanjut == 'SEMUA' ? 'selected' : '' }}>-- Semua (Ranap & Ralan) --</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Penjamin:</label>
                    <select name="kd_pj" class="form-control form-control-sm select2">
                        <option value="UMU" {{ $kdPj == 'UMU' ? 'selected' : '' }}>Umum</option>
                        <option value="BPJ" {{ $kdPj == 'BPJ' ? 'selected' : '' }}>BPJS</option>
                        <option value="ASURANSI" {{ $kdPj == 'ASURANSI' ? 'selected' : '' }}>Asuransi</option>
                        <option value="INHEALTH" {{ $kdPj == 'INHEALTH' ? 'selected' : '' }}>Mandiri Inhealth & Askes Inhealth</option>
                        <option value="SEMUA" {{ $kdPj == 'SEMUA' ? 'selected' : '' }}>-- Semua Penjamin --</option>
                    </select>
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
            <table class="table table-bordered table-hover table-sm align-middle" id="tableToCopy">
                <thead class="table-secondary">
                    <tr>
                        <th rowspan="2" style="width: 110px;">Tanggal</th>
                        <th rowspan="2" style="width: 120px;">REG</th>
                        <th colspan="5">Paket Tindakan {{ $statusLanjut == 'Ralan' ? 'Ralan' : ($statusLanjut == 'SEMUA' ? '' : 'Ranap') }}</th>
                        <th rowspan="2" style="width: 130px;">OBAT<br>EMB+TUSLAH</th>
                        <th rowspan="2" style="width: 120px;">RETUR<br>OBAT</th>
                        <th colspan="2">LAB</th>
                        <th colspan="5">RO</th>
                        <th rowspan="2" style="width: 120px;">POT</th>
                        <th rowspan="2" style="width: 120px;">TBM</th>
                        <th rowspan="2" style="width: 140px;">Kamar + Service</th>
                        <th colspan="3">OK</th>
                        <th rowspan="2" style="width: 140px;" class="table-primary text-dark">TOTAL</th>
                    </tr>
                    <tr>
                        <th style="width: 120px;">JS</th>
                        <th style="width: 120px;">BHP</th>
                        <th style="width: 120px;">JM DR</th>
                        <th style="width: 120px;">PR</th>
                        <th style="width: 120px;">KSO</th>

                        <th style="width: 120px;">JS</th>
                        <th style="width: 120px;">BHP</th>

                        <th style="width: 120px;">JS</th>
                        <th style="width: 120px;">BHP</th>
                        <th style="width: 120px;">JM PJ</th>
                        <th style="width: 120px;">PETUGAS</th>
                        <th style="width: 140px;">JM PR (PERUJUK)</th>

                        <th style="width: 120px;">JM DR</th>
                        <th style="width: 120px;">JM PR</th>
                        <th style="width: 120px;">JS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totReg = 0;
                        $totJs = 0;
                        $totBhp = 0;
                        $totJmDr = 0;
                        $totPr = 0;
                        $totKso = 0;
                        $totObat = 0;
                        $totRetur = 0;
                        $totLabJs = 0;
                        $totLabBhp = 0;
                        $totRoJs = 0;
                        $totRoBhp = 0;
                        $totRoJmPj = 0;
                        $totRoPetugas = 0;
                        $totRoPerujuk = 0;
                        $totPot = 0;
                        $totTbm = 0;
                        $totKmr = 0;
                        $totOkJmDr = 0;
                        $totOkJmPr = 0;
                        $totOkJs = 0;
                        $totGrandTotal = 0;
                    @endphp

                    @forelse ($dataRekap as $item)
                        @php
                            $totReg += $item->reg ?? 0;
                            $totJs += $item->js ?? 0;
                            $totBhp += $item->bhp ?? 0;
                            $totJmDr += $item->jm_dr ?? 0;
                            $totPr += $item->pr ?? 0;
                            $totKso += $item->kso ?? 0;
                            $totObat += $item->obat ?? 0;
                            $totRetur += $item->retur ?? 0;
                            $totLabJs += $item->lab_js ?? 0;
                            $totLabBhp += $item->lab_bhp ?? 0;
                            $totRoJs += $item->ro_js ?? 0;
                            $totRoBhp += $item->ro_bhp ?? 0;
                            $totRoJmPj += $item->ro_jm_pj ?? 0;
                            $totRoPetugas += $item->ro_petugas ?? 0;
                            $totRoPerujuk += $item->ro_perujuk ?? 0;
                            $totPot += $item->pot ?? 0;
                            $totTbm += $item->tbm ?? 0;
                            $totKmr += $item->kamar ?? 0;
                            $totOkJmDr += $item->ok_jm_dr ?? 0;
                            $totOkJmPr += $item->ok_jm_pr ?? 0;
                            $totOkJs += $item->ok_js ?? 0;
                            $totGrandTotal += $item->total ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ number_format($item->reg ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->js ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->bhp ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->jm_dr ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->pr ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->kso ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->obat ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->retur ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->lab_js ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->lab_bhp ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ro_js ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ro_bhp ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ro_jm_pj ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ro_petugas ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ro_perujuk ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->pot ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->tbm ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->kamar ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ok_jm_dr ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ok_jm_pr ?? 0, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->ok_js ?? 0, 0, ',', '.') }}</td>
                            <td class="font-weight-bold table-light">{{ number_format($item->total ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="23" class="text-center py-3 text-muted">
                                Tidak ada data pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-warning font-weight-bold">
                    <tr>
                        <td class="text-center">TOTAL</td>
                        <td class="text-right">{{ number_format($totReg, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totJs, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totBhp, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totJmDr, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totPr, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totKso, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totObat, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totRetur, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totLabJs, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totLabBhp, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totRoJs, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totRoBhp, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totRoJmPj, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totRoPetugas, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totRoPerujuk, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totPot, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totTbm, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totKmr, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totOkJmDr, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totOkJmPr, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totOkJs, 0, ',', '.') }}</td>
                        <td class="text-right font-weight-bold table-primary text-dark">{{ number_format($totGrandTotal, 0, ',', '.') }}</td>
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
