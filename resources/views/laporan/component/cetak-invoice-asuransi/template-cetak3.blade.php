<table border="1px" width="1000px" class="mt-4">
    <tr class="text-center">
        <th>No</th>
        <th>Nama </th>
        <th>Rincian</th>
        <th>Jumlah Biaya</th>
        <th>No Kwit</th>
        <th>Rm</th>
        <th>Tanggal Rawat</th>
    </tr>
    @if ($getPasien)
        @foreach ($getPasien as $key => $item)
            <tr>
                <td width="15px" class="text-center">{{ $key + 1 }}. </td>
                <td>{{ $item->nm_pasien }}</td>
                <td class="text-xs">
                    <table width="100%">
                        {{-- REGISTRASI --}}
                        <tr class="my-0 py-0">
                            <td width="70%">Registrasi/Adm</td>
                            <td>:
                                {{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, ',', '.') }}
                            </td>
                        </tr>
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
                        {{--  // LABORAT --}}
                        @if ($item->getLaborat->sum('totalbiaya') != 0)
                            <tr class="my-0 py-0">
                                <td width="70%">Laborat</td>
                                <td>:
                                    {{ number_format($item->getLaborat->sum('totalbiaya'), 0, ',', '.') }}
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
                        {{-- OBAT +BHP --}}
                        @if ($item->getObat->sum('totalbiaya') != 0)
                            <tr class="my-0 py-0">
                                <td width="70%">Obat</td>
                                <td>:
                                    {{ number_format($item->getObat->sum('totalbiaya') + $item->getReturObat->sum('totalbiaya'), 0, ',', '.') }}
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
                <td><u>Rp. {{ number_format($item->total_biaya, 0, ',', '.') }}</u> ,</td>
                <td class="text-center">
                    @foreach ($item->getNomorNota as $detail)
                        {{ substr(str_replace(':', '', $detail->nm_perawatan), -6) }}
                    @endforeach
                </td>
                <td class="text-center">{{ $item->no_rkm_medis }}</td>
                <td class="text-center">
                    @if ($item->tgl_masuk == null && $item->tgl_keluar == null)
                        {{ $item->tgl_byr }}
                    @else
                        {{ date('d', strtotime($item->tgl_masuk)) . ' - ' . date('d', strtotime($item->tgl_keluar)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_keluar))) }}-{{ date('Y', strtotime($item->tgl_keluar)) }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Rp. {{ number_format($getPasien->sum('total_biaya'), 0, ',', '.') }}</b></td>
        </tr>
    @endif
</table>
