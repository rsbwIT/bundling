@php
    if (!isset($formatRp)) {
        $formatRp = function($val) {
            return ($val == 0) ? '-' : number_format($val, 0, ',', '.');
        };
    }

    $isRanap = ($statusLanjutLocal == 'Ranap');

    $totReg = 0; $totJs = 0; $totBhp = 0; $totJmDr = 0; $totPr = 0; $totKso = 0;
    $totObat = 0; $totRetur = 0;
    $totLabJs = 0; $totLabBhp = 0; $totLabJm = 0;
    $totRoJs = 0; $totRoBhp = 0; $totRoJmPj = 0; $totRoPetugas = 0; $totRoPerujuk = 0;
    $totPot = 0; $totTbm = 0; $totKmr = 0;
    $totOkJmDr = 0; $totOkJmPr = 0; $totOkJs = 0;
    $totSubTindakan = 0; $totSubLab = 0; $totSubRo = 0; $totSubOk = 0;
    $totTotal = 0; $totPj = 0; $totEkses = 0; $totGrandTotal = 0;
@endphp

<table class="rekap-pdf-table">
    <thead>
        <tr>
            <th rowspan="2" style="width: {{ $isRanap ? '5.5%' : '5.5%' }};">TANGGAL</th>
            <th rowspan="2" style="width: {{ $isRanap ? '4.0%' : '4.0%' }};">REG</th>
            <th colspan="5">PAKET TINDAKAN {{ $isRanap ? 'RANAP' : 'RALAN' }}</th>
            <th rowspan="2" style="width: {{ $isRanap ? '4.2%' : '4.0%' }};">OBAT+<br/>TUSLAH</th>
            <th rowspan="2" style="width: {{ $isRanap ? '3.5%' : '3.3%' }};">RETUR<br/>OBAT</th>
            <th colspan="3">LABORATORIUM</th>
            <th colspan="5">RADIOLOGI</th>
            <th rowspan="2" style="width: {{ $isRanap ? '3.2%' : '3.0%' }};">POT</th>
            <th rowspan="2" style="width: {{ $isRanap ? '3.2%' : '3.0%' }};">TBM</th>
            <th rowspan="2" style="width: {{ $isRanap ? '4.2%' : '3.8%' }};">KAMAR<br/>+SRV</th>
            <th colspan="3">OPERASI (OK)</th>
            <th rowspan="2" style="width: {{ $isRanap ? '5.5%' : '5.2%' }};" class="col-th-total">TOTAL</th>
            @if(!$isRanap)
            <th rowspan="2" style="width: 3.6%;">PJ</th>
            @endif
            <th rowspan="2" style="width: {{ $isRanap ? '4.4%' : '4.4%' }};">EKSES</th>
            <th rowspan="2" style="width: {{ $isRanap ? '6.0%' : '5.6%' }};" class="col-th-grand">GRAND<br/>TOTAL</th>
        </tr>
        <tr>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JS</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">BHP</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JM DR</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">PR</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">KSO</th>

            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JS</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">BHP</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JM LAB</th>

            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JS</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">BHP</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JM PJ</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">PETUGAS</th>
            <th style="width: {{ $isRanap ? '3.8%' : '3.6%' }};">PERUJUK</th>

            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JM DR</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JM PR</th>
            <th style="width: {{ $isRanap ? '3.5%' : '3.4%' }};">JS</th>
        </tr>
    </thead>
    <tbody>
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

                $totSubTindakan += (($item->js ?? 0) + ($item->bhp ?? 0) + ($item->jm_dr ?? 0) + ($item->pr ?? 0) + ($item->kso ?? 0));
                $totSubLab += (($item->lab_js ?? 0) + ($item->lab_bhp ?? 0) + ($item->lab_jm ?? 0));
                $totSubRo += (($item->ro_js ?? 0) + ($item->ro_bhp ?? 0) + ($item->ro_jm_pj ?? 0) + ($item->ro_petugas ?? 0) + ($item->ro_perujuk ?? 0));
                $totSubOk += (($item->ok_jm_dr ?? 0) + ($item->ok_jm_pr ?? 0) + ($item->ok_js ?? 0));

                $totTotal += $item->total ?? 0;
                $totPj += $item->pj ?? 0;
                $totEkses += $item->ekses ?? 0;
                $totGrandTotal += $item->grand_total ?? 0;
            @endphp
            <tr>
                <td class="text-center">{{ $item->tanggal }}</td>
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
                <td class="col-total">{{ $formatRp($item->total ?? 0) }}</td>
                @if(!$isRanap)
                <td>{{ $formatRp($item->pj ?? 0) }}</td>
                @endif
                <td>{{ $formatRp($item->ekses ?? 0) }}</td>
                <td class="col-grand">{{ $formatRp($item->grand_total ?? 0) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $isRanap ? 26 : 27 }}" class="text-center" style="padding: 10px; color: #888;">
                    Tidak ada data pada periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="row-footer-total">
            <td class="text-center">TOTAL</td>
            <td>{{ $formatRp($totReg) }}</td>
            <td>{{ $formatRp($totJs) }}</td>
            <td>{{ $formatRp($totBhp) }}</td>
            <td>{{ $formatRp($totJmDr) }}</td>
            <td>{{ $formatRp($totPr) }}</td>
            <td>{{ $formatRp($totKso) }}</td>
            <td>{{ $formatRp($totObat) }}</td>
            <td>{{ $formatRp($totRetur) }}</td>
            <td>{{ $formatRp($totLabJs) }}</td>
            <td>{{ $formatRp($totLabBhp) }}</td>
            <td>{{ $formatRp($totLabJm) }}</td>
            <td>{{ $formatRp($totRoJs) }}</td>
            <td>{{ $formatRp($totRoBhp) }}</td>
            <td>{{ $formatRp($totRoJmPj) }}</td>
            <td>{{ $formatRp($totRoPetugas) }}</td>
            <td>{{ $formatRp($totRoPerujuk) }}</td>
            <td>{{ $formatRp($totPot) }}</td>
            <td>{{ $formatRp($totTbm) }}</td>
            <td>{{ $formatRp($totKmr) }}</td>
            <td>{{ $formatRp($totOkJmDr) }}</td>
            <td>{{ $formatRp($totOkJmPr) }}</td>
            <td>{{ $formatRp($totOkJs) }}</td>
            <td class="col-total">{{ $formatRp($totTotal) }}</td>
            @if(!$isRanap)
            <td>{{ $formatRp($totPj) }}</td>
            @endif
            <td>{{ $formatRp($totEkses) }}</td>
            <td class="col-grand">{{ $formatRp($totGrandTotal) }}</td>
        </tr>
        <tr class="row-footer-grand">
            <td class="text-center">GRANDTOTAL</td>
            <td>{{ $formatRp($totReg) }}</td>
            <td class="text-center" colspan="5">{{ $formatRp($totSubTindakan) }}</td>
            <td>{{ $formatRp($totObat) }}</td>
            <td>{{ $formatRp($totRetur) }}</td>
            <td class="text-center" colspan="3">{{ $formatRp($totSubLab) }}</td>
            <td class="text-center" colspan="5">{{ $formatRp($totSubRo) }}</td>
            <td>{{ $formatRp($totPot) }}</td>
            <td>{{ $formatRp($totTbm) }}</td>
            <td>{{ $formatRp($totKmr) }}</td>
            <td class="text-center" colspan="3">{{ $formatRp($totSubOk) }}</td>
            <td class="col-total">{{ $formatRp($totTotal) }}</td>
            @if(!$isRanap)
            <td>{{ $formatRp($totPj) }}</td>
            @endif
            <td>{{ $formatRp($totEkses) }}</td>
            <td class="col-grand">{{ $formatRp($totGrandTotal) }}</td>
        </tr>
        @if(isset($showTotalKeseluruhan) && $showTotalKeseluruhan)
        <tr class="row-footer-all">
            <td class="text-right" colspan="{{ $isRanap ? 25 : 26 }}" style="padding-right: 8px;">
                TOTAL KESELURUHAN (RAWAT INAP + RAWAT JALAN)
            </td>
            <td class="col-grand-all">{{ $formatRp($totalKeseluruhan) }}</td>
        </tr>
        @endif
    </tfoot>
</table>
