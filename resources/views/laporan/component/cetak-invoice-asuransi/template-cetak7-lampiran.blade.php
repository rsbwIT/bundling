<table border="0px" width="1200px" class="mt-4">
    <tr class="text-center">
        <td>Lampiran</td>
    </tr>
</table>
<table border="1px" width="1200px" class="mt-4 text-xs">
    <tr class="text-center">
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama Pasien</th>
        <th>Umur</th>
        <th>Nomor Kartu</th>
        <th>Diagnosa</th>
        <th>Jasa Tindakan & Medis</th>
        <th>Penunjang Diagnostik</th>
        <th>Obat</th>
        <th>Jasa Prasarana</th>
        <th>Jumlah</th>
        <th>Detail</th>
        <th>No Kwit</th>
        <th>RM</th>
    </tr>
    @if ($getPasien)
        @php
            $jenisBayarTotals = [];
        @endphp
        @foreach ($getPasien as $key => $item)
            <tr>
                <td width="15px" class="text-center">{{ $key + 1 }}. </td>
                <td class="text-center">
                    @if ($item->tgl_masuk == null && $item->tgl_keluar == null)
                        {{ $item->tgl_byr }}
                    @else
                        {{ date('d', strtotime($item->tgl_masuk)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_masuk))) }}
                        -
                        {{ date('d', strtotime($tgl_keluar)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($tgl_keluar))) }}-{{ date('Y', strtotime($tgl_keluar)) }}
                    @endif
                </td>
                <td>{{ $item->nm_pasien }}</td>
                <td class="text-center">{{ $item->umurdaftar }} {{ $item->sttsumur }}</td>
                <td class="text-right">{{ $item->nomor_kartu }}</td>
                <td>
                    @foreach ($item->getDiagnosa as $detail)
                        {{ $detail->kd_penyakit }} - ( {{ $detail->nm_penyakit }} )
                    @endforeach
                </td>
                <td class="text-right">
                    {{ number_format(
                        $item->getRalanDrParamedis->sum('totalbiaya') +
                            $item->getRalanParamedis->sum('totalbiaya') +
                            $item->getRanapDrParamedis->sum('totalbiaya') +
                            $item->getRanapParamedis->sum('totalbiaya') +
                            $item->getOprasi->sum('totalbiaya') +
                            $item->getKamarInap->sum('totalbiaya') +
                            $item->getRalanDokter->sum('totalbiaya') +
                            $item->getTambahan->sum('totalbiaya') +
                            $item->getRanapDokter->sum('totalbiaya'),
                        0,
                        ',',
                        '.',
                    ) }}
                </td>
                <td class="text-center">
                    {{ number_format($item->getLaborat->sum('totalbiaya') + $item->getRadiologi->sum('totalbiaya'), 0, ',', '.') }}
                </td>
                <td>
                    {{ number_format($item->getObat->sum('totalbiaya') + $item->getReturObat->sum('totalbiaya'), 0, ',', '.') }}
                </td>
                <td>
                    {{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, ',', '.') }}
                </td>
                <td>
                    {{ number_format($item->total_biaya, 0, ',', '.') }}
                </td>
                <td width="16%">
                    <table width="100%">
                        @foreach ($item->getTotalBiaya2 as $totalbiaya2)
                            <tr>
                                <td>{{ $totalbiaya2->nama_perusahaan }} </td>
                                <td class="text-right"> : {{ number_format($totalbiaya2->totalpiutang, 0, ',', '.') }}
                                </td>
                            </tr>
                            @php
                                $jenisBayar = $totalbiaya2->nama_perusahaan;
                                $jenisBayarTotals[$jenisBayar] =
                                    ($jenisBayarTotals[$jenisBayar] ?? 0) + $totalbiaya2->totalpiutang;
                            @endphp
                        @endforeach
                    </table>
                </td>
                <td>
                    @foreach ($item->getNomorNota as $detail)
                        {{ substr(str_replace(':', '', $detail->nm_perawatan), -6) }}
                    @endforeach
                </td>
                <td>{{ $item->no_rkm_medis }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6" class="text-right text-bold">Total : </td>
            <td class="text-right text-bold">
                {{ number_format(
                    $getPasien->sum(function ($item) {
                        return $item->getRalanDrParamedis->sum('totalbiaya') +
                            $item->getRalanParamedis->sum('totalbiaya') +
                            $item->getRanapDrParamedis->sum('totalbiaya') +
                            $item->getRanapParamedis->sum('totalbiaya') +
                            $item->getOprasi->sum('totalbiaya') +
                            $item->getKamarInap->sum('totalbiaya') +
                            $item->getRalanDokter->sum('totalbiaya') +
                            $item->getTambahan->sum('totalbiaya') +
                            $item->getRanapDokter->sum('totalbiaya');
                    }),
                    0,
                    ',',
                    '.',
                ) }}</b>
            </td>
            <td class="text-right text-bold">
                {{ number_format(
                    $getPasien->sum(function ($item) {
                        return $item->getLaborat->sum('totalbiaya') + $item->getRadiologi->sum('totalbiaya');
                    }),
                    0,
                    ',',
                    '.',
                ) }}</b>
            </td>
            <td class="text-right text-bold">
                {{ number_format(
                    $getPasien->sum(function ($item) {
                        return $item->getObat->sum('totalbiaya') + $item->getReturObat->sum('totalbiaya');
                    }),
                    0,
                    ',',
                    '.',
                ) }}</b>
            </td>
            <td class="text-right text-bold">
                {{ number_format(
                    $getPasien->sum(function ($item) {
                        return $item->getRegistrasi->sum('totalbiaya');
                    }),
                    0,
                    ',',
                    '.',
                ) }}</b>
            </td>
            <td class="text-right text-bold">
                {{ number_format(
                    $getPasien->sum(function ($item) {
                        return $item->total_biaya;
                    }),
                    0,
                    ',',
                    '.',
                ) }}</b>
            </td>
            <td>
                <table width="100%">
                    @foreach ($jenisBayarTotals as $jenisBayar => $grandTotal)
                        <tr class="text-bold">
                            <td>{{ $jenisBayar }}</td>
                            <td class="text-right">: {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td colspan="2"></td>
        </tr>
    @endif
</table>
<br>
<br>
<table border="0px" width="1200px" class="mt-4">
    <tr>
        <td width="8%">
        </td>
        <td width="40%">
            <b>Mengetahui <br />
                Direktur Utama
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                dr. Arief Yulizar, MARS, FISQua.CHAE</b>
        </td>
        <td width="22%"></td>
        <td width="30%">
            <b>
                Bandar
                Lampung, {{ date('d', strtotime($getListInvoice->tgl_cetak)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($getListInvoice->tgl_cetak))) }}-{{ date('Y', strtotime($getListInvoice->tgl_cetak)) }}<br />
                Koord. Tagihan Perusahaan
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                Ita apriyanti S.Kep NS
            </b>
        </td>
    </tr>
</table>
