<table border="0px" width="1000px" class="mt-4">
    <tr>
        <th></th>
        <th></th>
        <th>Nama</th>
        <th>Jumlah Biaya</th>
        <th>No Kwit</th>
        <th>Rm</th>
        <th>Tanggal Rawat</th>
    </tr>

    @foreach ($getPasien as $key => $item)
        <tr>
            <td width="50px"></td>
            <td width="15px">{{ $key + 1 }}.</td>
            <td>{{ $item->nm_pasien }}</td>

            {{-- TOTAL --}}
            <td>
                Rp. {{ number_format($item->total_biaya ?? 0, 0, ',', '.') }}
            </td>

            {{-- NO KWIT --}}
            <td>
                @if(!empty($item->getNomorNota))
                    @foreach ($item->getNomorNota as $detail)
                        {{ str_replace(':', '', $detail->nm_perawatan) }}
                    @endforeach
                @endif
            </td>

            <td>{{ $item->no_rkm_medis }}</td>

            {{-- TANGGAL --}}
            <td>
                @if (empty($item->tgl_masuk) && empty($item->tgl_keluar))
                    {{ $item->tgl_byr }}
                @else
                    {{ $item->tgl_masuk ? date('d', strtotime($item->tgl_masuk)) : '' }}
                    -
                    {{ $item->tgl_masuk ? \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_masuk))) : '' }}
                    -
                    {{ $item->tgl_keluar ? date('d', strtotime($item->tgl_keluar)) : '' }}
                    -
                    {{ $item->tgl_keluar ? \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_keluar))) : '' }}
                    -
                    {{ $item->tgl_keluar ? date('Y', strtotime($item->tgl_keluar)) : '' }}
                @endif
            </td>
        </tr>
    @endforeach

    {{-- GRAND TOTAL --}}
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>
            <b>
                Rp. {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
            </b>
        </td>
    </tr>
</table>
