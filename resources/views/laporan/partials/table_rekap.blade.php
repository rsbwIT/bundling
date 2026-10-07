<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle table-rekap" id="tableToCopy_{{ $statusLanjutLocal }}">
        <thead class="table-secondary">
            <tr style="height: 35px;">
                <th rowspan="2" style="width: 110px;">Tanggal</th>
                <th rowspan="2" style="width: 120px;">REG</th>
                <th colspan="5">Paket Tindakan {{ $statusLanjutLocal == 'Ralan' ? 'Ralan' : 'Ranap' }}</th>
                <th rowspan="2" style="width: 130px;">OBAT<br>EMB+TUSLAH</th>
                <th rowspan="2" style="width: 120px;">RETUR<br>OBAT</th>
                <th colspan="3">LAB</th>
                <th colspan="5">RO</th>
                <th rowspan="2" style="width: 120px;">POT</th>
                <th rowspan="2" style="width: 120px;">TBM</th>
                <th rowspan="2" style="width: 140px;">Kamar + Service</th>
                <th colspan="3">OK</th>
                <th rowspan="2" style="width: 140px;" class="table-primary text-dark">TOTAL</th>
                @if($statusLanjutLocal != 'Ranap')
                <th rowspan="2" style="width: 120px;">PJ</th>
                @endif
                <th rowspan="2" style="width: 120px;">EKSES</th>
                <th rowspan="2" style="width: 140px;" class="table-primary text-dark">GRAND TOTAL</th>
            </tr>
            <tr>
                <th style="width: 120px;">JS</th>
                <th style="width: 120px;">BHP</th>
                <th style="width: 120px;">JM DR</th>
                <th style="width: 120px;">PR</th>
                <th style="width: 120px;">KSO</th>

                <th style="width: 120px;">JS</th>
                <th style="width: 120px;">BHP</th>
                <th style="width: 120px;">JM LAB</th>

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
                if (!isset($formatRp)) {
                    $formatRp = function($val) {
                        return ($val == 0) ? '-' : number_format($val, 0, ',', '.');
                    };
                }

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
                $totLabJm = 0;
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
                $totSubTindakan = 0;
                $totSubLab = 0;
                $totSubRo = 0;
                $totSubOk = 0;
                $totTotal = 0;
                $totPj = 0;
                $totEkses = 0;
                $totGrandTotal = 0;
            @endphp

            @forelse ($dataRekapLocal as $item)
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
                    $totLabJm += $item->lab_jm ?? 0;
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
                    
                    $subTotalTindakan = ($item->js ?? 0) + ($item->bhp ?? 0) + ($item->jm_dr ?? 0) + ($item->pr ?? 0) + ($item->kso ?? 0);
                    $subTotalLab = ($item->lab_js ?? 0) + ($item->lab_bhp ?? 0) + ($item->lab_jm ?? 0);
                    $subTotalRo = ($item->ro_js ?? 0) + ($item->ro_bhp ?? 0) + ($item->ro_jm_pj ?? 0) + ($item->ro_petugas ?? 0) + ($item->ro_perujuk ?? 0);
                    $subTotalOk = ($item->ok_jm_dr ?? 0) + ($item->ok_jm_pr ?? 0) + ($item->ok_js ?? 0);

                    $totSubTindakan += $subTotalTindakan;
                    $totSubLab += $subTotalLab;
                    $totSubRo += $subTotalRo;
                    $totSubOk += $subTotalOk;
                    $totTotal += $item->total ?? 0;
                    $totPj += $item->pj ?? 0;
                    $totEkses += $item->ekses ?? 0;
                    $totGrandTotal += $item->grand_total ?? 0;
                @endphp
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $formatRp($item->reg ?? 0) }}</td>
                    <td>{{ $formatRp($item->js ?? 0) }}</td>
                    <td>{{ $formatRp($item->bhp ?? 0) }}</td>
                    <td>{{ $formatRp($item->jm_dr ?? 0) }}</td>
                    <td>{{ $formatRp($item->pr ?? 0) }}</td>
                    <td>{{ $formatRp($item->kso ?? 0) }}</td>
                    <td>{{ $formatRp($item->obat ?? 0) }}</td>
                    <td>{{ $formatRp($item->retur ?? 0) }}</td>
                    <td>{{ $formatRp($item->lab_js ?? 0) }}</td>
                    <td>{{ $formatRp($item->lab_bhp ?? 0) }}</td>
                    <td>{{ $formatRp($item->lab_jm ?? 0) }}</td>
                    <td>{{ $formatRp($item->ro_js ?? 0) }}</td>
                    <td>{{ $formatRp($item->ro_bhp ?? 0) }}</td>
                    <td>{{ $formatRp($item->ro_jm_pj ?? 0) }}</td>
                    <td>{{ $formatRp($item->ro_petugas ?? 0) }}</td>
                    <td>{{ $formatRp($item->ro_perujuk ?? 0) }}</td>
                    <td>{{ $formatRp($item->pot ?? 0) }}</td>
                    <td>{{ $formatRp($item->tbm ?? 0) }}</td>
                    <td>{{ $formatRp($item->kamar ?? 0) }}</td>
                    <td>{{ $formatRp($item->ok_jm_dr ?? 0) }}</td>
                    <td>{{ $formatRp($item->ok_jm_pr ?? 0) }}</td>
                    <td>{{ $formatRp($item->ok_js ?? 0) }}</td>
                    <td class="font-weight-bold table-light">{{ $formatRp($item->total ?? 0) }}</td>
                    @if($statusLanjutLocal != 'Ranap')
                    <td>{{ $formatRp($item->pj ?? 0) }}</td>
                    @endif
                    <td>{{ $formatRp($item->ekses ?? 0) }}</td>
                    <td class="font-weight-bold table-primary text-dark">{{ $formatRp($item->grand_total ?? 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $statusLanjutLocal == 'Ranap' ? 26 : 27 }}" class="text-center py-3 text-muted">
                        Tidak ada data pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-warning font-weight-bold">
            <tr>
                <td class="text-center">TOTAL</td>
                <td class="text-right">{{ $formatRp($totReg) }}</td>
                <td class="text-right">{{ $formatRp($totJs) }}</td>
                <td class="text-right">{{ $formatRp($totBhp) }}</td>
                <td class="text-right">{{ $formatRp($totJmDr) }}</td>
                <td class="text-right">{{ $formatRp($totPr) }}</td>
                <td class="text-right">{{ $formatRp($totKso) }}</td>
                <td class="text-right">{{ $formatRp($totObat) }}</td>
                <td class="text-right">{{ $formatRp($totRetur) }}</td>
                <td class="text-right">{{ $formatRp($totLabJs) }}</td>
                <td class="text-right">{{ $formatRp($totLabBhp) }}</td>
                <td class="text-right">{{ $formatRp($totLabJm) }}</td>
                <td class="text-right">{{ $formatRp($totRoJs) }}</td>
                <td class="text-right">{{ $formatRp($totRoBhp) }}</td>
                <td class="text-right">{{ $formatRp($totRoJmPj) }}</td>
                <td class="text-right">{{ $formatRp($totRoPetugas) }}</td>
                <td class="text-right">{{ $formatRp($totRoPerujuk) }}</td>
                <td class="text-right">{{ $formatRp($totPot) }}</td>
                <td class="text-right">{{ $formatRp($totTbm) }}</td>
                <td class="text-right">{{ $formatRp($totKmr) }}</td>
                <td class="text-right">{{ $formatRp($totOkJmDr) }}</td>
                <td class="text-right">{{ $formatRp($totOkJmPr) }}</td>
                <td class="text-right">{{ $formatRp($totOkJs) }}</td>
                <td class="text-right font-weight-bold table-light">{{ $formatRp($totTotal) }}</td>
                @if($statusLanjutLocal != 'Ranap')
                <td class="text-right">{{ $formatRp($totPj) }}</td>
                @endif
                <td class="text-right">{{ $formatRp($totEkses) }}</td>
                <td class="text-right font-weight-bold table-primary text-dark">{{ $formatRp($totGrandTotal) }}</td>
            </tr>
            <tr class="table-info font-weight-bold">
                <td class="text-center">GRANDTOTAL</td>
                <td class="text-right">{{ $formatRp($totReg) }}</td>
                <td class="text-center" colspan="5">{{ $formatRp($totSubTindakan) }}</td>
                <td class="text-right">{{ $formatRp($totObat) }}</td>
                <td class="text-right">{{ $formatRp($totRetur) }}</td>
                <td class="text-center" colspan="3">{{ $formatRp($totSubLab) }}</td>
                <td class="text-center" colspan="5">{{ $formatRp($totSubRo) }}</td>
                <td class="text-right">{{ $formatRp($totPot) }}</td>
                <td class="text-right">{{ $formatRp($totTbm) }}</td>
                <td class="text-right">{{ $formatRp($totKmr) }}</td>
                <td class="text-center" colspan="3">{{ $formatRp($totSubOk) }}</td>
                <td class="text-right font-weight-bold table-light">{{ $formatRp($totTotal) }}</td>
                @if($statusLanjutLocal != 'Ranap')
                <td class="text-right">{{ $formatRp($totPj) }}</td>
                @endif
                <td class="text-right">{{ $formatRp($totEkses) }}</td>
                <td class="text-right font-weight-bold table-primary text-dark">{{ $formatRp($totGrandTotal) }}</td>
            </tr>
            @if(isset($showTotalKeseluruhan) && $showTotalKeseluruhan)
            <tr style="background-color: #d1e7dd; font-weight: bold;">
                <td class="text-right pr-3" colspan="{{ $statusLanjutLocal == 'Ranap' ? 25 : 26 }}">TOTAL KESELURUHAN (RI + RJ)</td>
                <td class="text-right font-weight-bold text-dark table-primary">{{ $formatRp($totalKeseluruhan) }}</td>
            </tr>
            @endif
        </tfoot>
    </table>
</div>
