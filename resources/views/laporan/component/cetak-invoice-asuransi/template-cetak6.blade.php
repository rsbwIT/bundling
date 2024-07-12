<table border="1px" width="1000px" class="mt-4 text-xs">
    <tr class="text-center">
        <th>No</th>
        <th>Nama Pasien</th>
        <th>NIP</th>
        <th>No Klaim</th>
        <th>Rm</th>
        <th>Tanggal</th>
        <th>Rincian</th>
        <th>Lab/Ro</th>
        <th>Obat</th>
        <th>Adm</th>
        <th>Jumlah Biaya</th>
    </tr>
    @if ($getPasien)
        @foreach ($getPasien as $key => $item)
            <tr>
                <td width="15px" class="text-center">{{ $key + 1 }}. </td>
                <td>{{ $item->nm_pasien }}</td>
                <td>{{ $item->nomor_kartu }}</td>
                <td>{{ $item->nomor_klaim }}</td>
                <td class="text-center">{{ $item->no_rkm_medis }}</td>
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
                <td>
                    <table width="100%">
                        {{-- RALAN DOKTER / 1 Paket Tindakan --}}
                        @foreach ($item->getRalanDokter as $detail)
                            @if (!empty($detail->nm_perawatan) && $detail->nm_perawatan !== ':')
                                <tr class="my-0 py-0">
                                    <td width="70%">
                                        {{ str_replace(':', '', $detail->nm_perawatan) }}</td>
                                    <td>: {{ number_format($detail->totalbiaya, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        {{--  // RALAN DOKTER PARAMEDIS / 2 Paket Tindakan --}}
                        @foreach ($item->getRalanDrParamedis as $detail)
                            @if (!empty($detail->nm_perawatan) && $detail->nm_perawatan !== ':')
                                <tr class="my-0 py-0">
                                    <td width="70%">
                                        {{ str_replace(':', '', $detail->nm_perawatan) }}</td>
                                    <td>: {{ number_format($detail->totalbiaya, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        {{--  // RALAN PARAMEDIS / 3 Paket Tindakan --}}
                        @foreach ($item->getRalanParamedis as $detail)
                            @if (!empty($detail->nm_perawatan) && $detail->nm_perawatan !== ':')
                                <tr class="my-0 py-0">
                                    <td width="70%">
                                        {{ str_replace(':', '', $detail->nm_perawatan) }}</td>
                                    <td>: {{ number_format($detail->totalbiaya, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        {{--  // RANAP DOKTER / 4 Paket Tindakan --}}
                        @foreach ($item->getRanapDokter as $detail)
                            @if (!empty($detail->nm_perawatan) && $detail->nm_perawatan !== ':')
                                <tr class="my-0 py-0">
                                    <td width="70%">
                                        {{ str_replace(':', '', $detail->nm_perawatan) }}</td>
                                    <td>: {{ number_format($detail->totalbiaya, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        {{--   // RANAP DOKTER PARAMEDIS / 5 Paket Tindakan --}}
                        @foreach ($item->getRanapDrParamedis as $detail)
                            @if (!empty($detail->nm_perawatan) && $detail->nm_perawatan !== ':')
                                <tr class="my-0 py-0">
                                    <td width="70%">
                                        {{ str_replace(':', '', $detail->nm_perawatan) }}</td>
                                    <td>: {{ number_format($detail->totalbiaya, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        {{--   // RANAP PARAMEDIS / 6 Ranap Paramedis --}}
                        @foreach ($item->getRanapParamedis as $detail)
                            @if (!empty($detail->nm_perawatan) && $detail->nm_perawatan !== ':')
                                <tr class="my-0 py-0">
                                    <td width="70%">
                                        {{ str_replace(':', '', $detail->nm_perawatan) }}</td>
                                    <td>: {{ number_format($detail->totalbiaya, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        {{--  // OPRASI --}}
                        @if ($item->getOprasi->sum('totalbiaya') != 0)
                            <tr class="my-0 py-0">
                                <td width="70%">Operasi</td>
                                <td>:
                                    {{ number_format($item->getOprasi->sum('totalbiaya'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- // RADIOLOGI --}}
                        @if ($item->getRadiologi->sum('totalbiaya') != 0)
                            <tr class="my-0 py-0">
                                <td width="70%">Radiologi</td>
                                <td>:
                                    {{ number_format($item->getRadiologi->sum('totalbiaya'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- // KAMAR INAP --}}
                        @if ($item->getKamarInap->sum('totalbiaya') != 0)
                            <tr class="my-0 py-0">
                                <td width="70%">Kamar Inap</td>
                                <td>:
                                    {{ number_format($item->getKamarInap->sum('totalbiaya'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- // TAMBAHAN --}}
                        @if ($item->getTambahan->sum('totalbiaya') != 0)
                            <tr class="my-0 py-0">
                                <td width="70%">Tambahan</td>
                                <td>:
                                    {{ number_format($item->getTambahan->sum('totalbiaya'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </table>
                </td>
                <td class="text-right px-2">{{ number_format($item->getLaborat->sum('totalbiaya'), 0, ',', '.') }}</td>
                <td class="text-right px-2">
                    {{ number_format($item->getObat->sum('totalbiaya') + $item->getReturObat->sum('totalbiaya'), 0, ',', '.') }}
                </td>
                <td class="text-right px-2">{{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, ',', '.') }}
                </td>
                <td class="text-right px-2">Rp. {{ number_format($item->total_biaya, 0, ',', '.') }} ,</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6"></td>
            <td class="text-right px-2"><b>
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getRalanDokter->sum('totalbiaya') +
                                $item->getRalanDrParamedis->sum('totalbiaya') +
                                $item->getRalanParamedis->sum('totalbiaya') +
                                $item->getRanapDrParamedis->sum('totalbiaya') +
                                $item->getRanapParamedis->sum('totalbiaya') +
                                $item->getTambahan->sum('totalbiaya') +
                                $item->getKamarInap->sum('totalbiaya') +
                                $item->getOprasi->sum('totalbiaya');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}
                </b>
            </td>
            <td class="text-right px-2"><b>
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getLaborat->sum('totalbiaya');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}</b>
            </td>
            <td class="text-right px-2"><b>
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getObat->sum('totalbiaya') + $item->getReturObat->sum('totalbiaya');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}</b>
            </td>
            <td class="text-right px-2"><b>
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getRegistrasi->sum('totalbiaya');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}</b>
            </td>
            <td>
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
    @endif
</table>
