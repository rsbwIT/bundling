<table border="1px" width="1000px" class="mt-4 text-xs">
    <tr>
        <th></th>
        <th class="text-center">Nama </th>
        <th class="text-center">Diagnosa</th>
        <th class="text-center">Jumlah Biaya</th>
        <th class="text-center">No Kwit</th>
        <th class="text-center">No Kartu</th>
        <th class="text-center">No Klaim</th>
        <th class="text-center">Rm</th>
        <th class="text-center">Tanggal Rawat</th>
    </tr>
    @if ($getPasien)
        @foreach ($getPasien as $key => $item)
            <tr>
                <td width="15px">{{ $key + 1 }}. </td>
                <td width="150px">{{ $item->nm_pasien }}</td>
                <td width="250px">
                    @foreach ($item->getDiagnosa as $detail)
                        {{ $detail->kd_penyakit }} - ( {{$detail->nm_penyakit}} )
                    @endforeach
                </td>
                {{-- <td><u>Rp. {{ number_format($item->total_biaya, 0, ',', '.') }}</u> ,</td> --}}
                <td class="text-right">Rp. {{ number_format($item->getTotalBiaya->sum('totalpiutang'), 0, ',', '.') }}</td>
                <td class="text-center">
                    @foreach ($item->getNomorNota as $detail)
                        {{ substr(str_replace(':', '', $detail->nm_perawatan), -6) }}
                    @endforeach
                </td>
                <td class="text-center">{{ $item->nomor_kartu }}</td>
                <td class="text-center">{{ $item->nomor_klaim }}</td>
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
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            {{-- <td><b>Rp. {{ number_format($getPasien->sum('total_biaya'), 0, ',', '.') }}</b></td> --}}
            <td class="text-right"><b>Rp.
                    {{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getTotalBiaya->sum('totalpiutang');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}</b>
            </td>
            </td>
        </tr>
    @endif
</table>
