@php
    $thn = date('Y', strtotime($tgl1));
    $bln = date('m', strtotime($tgl1));
    $bulanIndo = array(
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    );
    $namaBulan = $bulanIndo[$bln] ?? '';

    $sum_ranap = isset($dataRekapRanap) ? $dataRekapRanap->sum('total') : ($statusLanjut == 'Ranap' ? $dataRekap->sum('total') : 0);
    $sum_ralan = isset($dataRekapRalan) ? $dataRekapRalan->sum('total') : ($statusLanjut == 'Ralan' ? $dataRekap->sum('total') : 0);
    $sum_pj = isset($dataRekapRalan) ? $dataRekapRalan->sum('pj') : ($statusLanjut != 'Ranap' ? $dataRekap->sum('pj') : 0);
    $sum_ekses_ri = isset($dataRekapRanap) ? $dataRekapRanap->sum('ekses') : ($statusLanjut == 'Ranap' ? $dataRekap->sum('ekses') : 0);
    $sum_ekses_rj = isset($dataRekapRalan) ? $dataRekapRalan->sum('ekses') : ($statusLanjut == 'Ralan' ? $dataRekap->sum('ekses') : 0);
    
    $total_pendapatan = $sum_ranap + $sum_ralan + $sum_pj + $sum_ekses_ri + $sum_ekses_rj;
@endphp

<table class="table table-bordered table-sm table-rekap" style="width: 450px; margin-top: 20px; font-size: 11px;">
    <thead style="background-color: #dbe2e8;">
        <tr>
            <th colspan="2" class="text-center font-weight-bold" style="font-size: 13px; padding: 10px; border: 1px solid #000; color: #000;">Pendapatan {{ $namaBulan }} {{ $thn }} ({{ $penjaminLabel }})</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 60%; text-align: left; border: 1px solid #000; padding: 4px 6px;">Ranap</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">{{ $sum_ranap == 0 ? '-' : number_format($sum_ranap, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="text-align: left; border: 1px solid #000; padding: 4px 6px;">Ralan</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">{{ $sum_ralan == 0 ? '-' : number_format($sum_ralan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="text-align: left; border: 1px solid #000; padding: 4px 6px;">Pend. Lain</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">-</td>
        </tr>
        <tr>
            <td style="text-align: left; border: 1px solid #000; padding: 4px 6px;">PJ</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">{{ $sum_pj == 0 ? '-' : number_format($sum_pj, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="text-align: left; border: 1px solid #000; padding: 4px 6px;">Ekses RI</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">{{ $sum_ekses_ri == 0 ? '-' : number_format($sum_ekses_ri, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="text-align: left; border: 1px solid #000; padding: 4px 6px;">Ekses RJ</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">{{ $sum_ekses_rj == 0 ? '-' : number_format($sum_ekses_rj, 0, ',', '.') }}</td>
        </tr>
        <tr style="background-color: #ffc107;">
            <td style="text-align: left; font-weight: bold; color: #000; border: 1px solid #000; padding: 4px 6px;">Total Pendapatan {{ $namaBulan }} {{ $thn }} ({{ $penjaminLabel }})</td>
            <td style="text-align: right; font-weight: bold; color: #000; border: 1px solid #000; padding: 4px 6px;">{{ number_format($total_pendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="text-align: left; border: 1px solid #000; padding: 4px 6px;">Tambahan (+)</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">-</td>
        </tr>
        <tr>
            <td style="text-align: left; border: 1px solid #000; padding: 4px 6px;">(-)</td>
            <td style="text-align: right; border: 1px solid #000; padding: 4px 6px;">-</td>
        </tr>
        <tr style="background-color: #9ec5fe;">
            <td style="text-align: left; font-weight: bold; color: #000; border: 1px solid #000; padding: 4px 6px;">Rekening Bank</td>
            <td style="text-align: right; font-weight: bold; color: #000; border: 1px solid #000; padding: 4px 6px;">{{ number_format($total_pendapatan, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
