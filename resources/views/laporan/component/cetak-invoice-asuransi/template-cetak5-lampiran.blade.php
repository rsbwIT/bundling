<table border="0px" width="1000px" class="mt-4">
    <tr class="text-center">
        <td>DAFTAR NAMA PENSIUNAN DAN KELUARGA</td>
    </tr>
    <tr class="text-center">
        <td>
            @if ($getDetailAsuransi->nama_perusahaan == '' || $getDetailAsuransi->nama_perusahaan == '-')
                <b>Input dimenu maping asuransi untuk melengkapi nama asuransi</b>
            @else
                {{ $getDetailAsuransi->nama_perusahaan }}
            @endif
        </td>
    </tr>
</table>
<table border="1px" width="1000px" class="mt-4">
    <tr class="text-center">
        <th></th>
        <th>Nama Karyawan</th>
        <th>No Kartu</th>
        <th>Rm</th>
        <th>Nama Pasien</th>
        <th>Tanggal Rawat</th>
        <th>Biaya Perawatan</th>
        <th>Biaya Dokter</th>
        <th>Jumlah Tagihan</th>
    </tr>
    @if ($getPasien)
        @foreach ($getPasien as $key => $item)
            <tr>
                <td width="15px">{{ $key + 1 }}. </td>
                <td>{{ $item->nm_pasien }}</td>
                <td class="text-center">{{ $item->nomor_kartu }}</td>
                <td class="text-center">{{ $item->no_rkm_medis }}</td>
                <td>{{ $item->nm_pasien }}</td>
                <td class="text-center">
                    @foreach ($item->getTglKeluar as $detail)
                        @php
                            $tgl_keluar = $detail->tgl_keluar;
                        @endphp
                    @endforeach
                    @if ($item->tgl_masuk == null && $item->tgl_keluar == null)
                        {{ $item->tgl_byr }}
                    @else
                        {{ date('d', strtotime($item->tgl_masuk)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_masuk))) }}
                        -
                        {{ date('d', strtotime($tgl_keluar)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($tgl_keluar))) }}-{{ date('Y', strtotime($tgl_keluar)) }}
                    @endif
                </td>
                <td class="text-right">
                    Rp.
                    {{ number_format(
                        $item->getRegistrasi->sum('totalbiaya') +
                            $item->getRalanDrParamedis->sum('totalbiaya') +
                            $item->getRalanParamedis->sum('totalbiaya') +
                            $item->getRanapDrParamedis->sum('totalbiaya') +
                            $item->getRanapParamedis->sum('totalbiaya') +
                            $item->getOprasi->sum('totalbiaya') +
                            $item->getLaborat->sum('totalbiaya') +
                            $item->getRadiologi->sum('totalbiaya') +
                            $item->getKamarInap->sum('totalbiaya') +
                            $item->getObat->sum('totalbiaya') +
                            $item->getReturObat->sum('totalbiaya') +
                            $item->getTambahan->sum('totalbiaya'),
                        0,
                        ',',
                        '.',
                    ) }}
                </td>
                <td class="text-right">
                    Rp.
                    {{ number_format(
                        $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya'),
                        0,
                        ',',
                        '.',
                    ) }}
                </td>
                <td class="text-right">
                    Rp.
                    {{ number_format(
                        $item->getRegistrasi->sum('totalbiaya') +
                            $item->getRalanDrParamedis->sum('totalbiaya') +
                            $item->getRalanParamedis->sum('totalbiaya') +
                            $item->getRanapDrParamedis->sum('totalbiaya') +
                            $item->getRanapParamedis->sum('totalbiaya') +
                            $item->getOprasi->sum('totalbiaya') +
                            $item->getLaborat->sum('totalbiaya') +
                            $item->getRadiologi->sum('totalbiaya') +
                            $item->getKamarInap->sum('totalbiaya') +
                            $item->getObat->sum('totalbiaya') +
                            $item->getReturObat->sum('totalbiaya') +
                            $item->getTambahan->sum('totalbiaya') +
                            $item->getRalanDokter->sum('totalbiaya') +
                            $item->getRanapDokter->sum('totalbiaya'),
                        0,
                        ',',
                        '.',
                    ) }}
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6"></td>
            <td class="text-right">
                <b>Rp.
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getRegistrasi->sum('totalbiaya') +
                                $item->getRalanDrParamedis->sum('totalbiaya') +
                                $item->getRalanParamedis->sum('totalbiaya') +
                                $item->getRanapDrParamedis->sum('totalbiaya') +
                                $item->getRanapParamedis->sum('totalbiaya') +
                                $item->getOprasi->sum('totalbiaya') +
                                $item->getLaborat->sum('totalbiaya') +
                                $item->getRadiologi->sum('totalbiaya') +
                                $item->getKamarInap->sum('totalbiaya') +
                                $item->getObat->sum('totalbiaya') +
                                $item->getReturObat->sum('totalbiaya') +
                                $item->getTambahan->sum('totalbiaya');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}
                </b>
            </td>
            <td class="text-right">
                <b>
                    Rp.
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}
                </b>
            </td>
            <td class="text-right">
                <b>
                    Rp.
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getTotalBiaya->sum('totalpiutang');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}
                </b>
            </td>
        </tr>
        <tr>
            <td colspan="9">
                <b>Terbilang : </b>
                @if ($getPasien)
                    {{ \App\Services\Keuangan\NomorInvoice::Terbilang(
                        $getPasien->sum(function ($item) {
                            return $item->getTotalBiaya->sum('totalpiutang');
                        }),
                    ) }}
                @endif rupiah
            </td>
        </tr>
    @endif
</table>
<br>
<br>
<table border="0px" width="1000px" class="mt-4">
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
                Lampung,{{ date('d', strtotime($getListInvoice->tgl_cetak)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($getListInvoice->tgl_cetak))) }}-{{ date('Y', strtotime($getListInvoice->tgl_cetak)) }}<br />
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
