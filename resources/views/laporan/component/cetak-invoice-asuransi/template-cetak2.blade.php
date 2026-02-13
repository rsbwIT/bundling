<table border="0px" width="1000px" class="mt-4">
    <tr>
        <th></th>
        <th></th>
        <th>Nama</th>
        <th>Jumlah Biaya</th>
        <th>No Kwit</th>
        <th>No Kartu</th>
        <th>No Klaim</th>
        <th>Rm</th>
        <th>Tanggal Rawat</th>
    </tr>

    @if ($getPasien && $getPasien->count())
        @foreach ($getPasien as $key => $item)
            <tr>
                <td width="50px"></td>
                <td width="15px">{{ $key + 1 }}.</td>
                <td>{{ $item->nm_pasien }}</td>

                {{-- TOTAL PIUTANG --}}
                <td>
                    Rp. {{ number_format($item->total_biaya ?? 0, 0, ',', '.') }}
                </td>

                {{-- NOMOR NOTA --}}
                <td>
                    @if (!empty($item->getNomorNota))
                        @foreach ($item->getNomorNota as $detail)
                            {{ substr(str_replace(':', '', $detail->nm_perawatan), -6) }}
                        @endforeach
                    @endif
                </td>

                <td>{{ $item->nomor_kartu }}</td>
                <td>{{ $item->nomor_klaim }}</td>
                <td>{{ $item->no_rkm_medis }}</td>

                {{-- TANGGAL RAWAT --}}
                <td>
                    @if (!$item->tgl_masuk && !$item->tgl_keluar)

                        {{ date('d-m-Y', strtotime($item->tgl_byr)) }}

                    @else

                        {{ date('d', strtotime($item->tgl_masuk)) }}
                        -{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_masuk))) }}
                        -
                        {{ date('d', strtotime($item->tgl_keluar)) }}
                        -{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_keluar))) }}
                        -{{ date('Y', strtotime($item->tgl_keluar)) }}

                    @endif
                </td>
            </tr>
        @endforeach

        {{-- TOTAL SEMUA --}}
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <b>
                    Rp. {{ number_format($getPasien->sum('total_biaya'), 0, ',', '.') }}
                </b>
            </td>
        </tr>
    @endif
</table>
