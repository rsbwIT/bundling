<table border="0px" width="1000px" class="mt-4">
    <tr>
        @if ($is_preview ?? false)
            <th width="40px" class="text-center align-middle">
                <input type="checkbox" id="check-all-pasien" checked title="Pilih Semua / Batal Semua" style="cursor: pointer; width: 18px; height: 18px;">
            </th>
        @endif
        <th width="30px">No.</th>
        <th>Nama </th>
        <th>Jumlah Biaya</th>
        <th>No Kwit</th>
        <th>No Kartu</th>
        <th>No Klaim</th>
        <th>Rm</th>
        <th>Tanggal Rawat</th>
    </tr>
    @if ($getPasien)
        @foreach ($getPasien as $key => $item)
            @php
                $biayaItem = $item->getTotalBiaya->sum('totalpiutang');
            @endphp
            <tr class="row-pasien" id="row-pasien-{{ $key }}" style="cursor: pointer;">
                @if ($is_preview ?? false)
                    <td width="40px" class="text-center align-middle">
                        <input type="checkbox" name="selected_pasien[]" value="{{ $item->no_rawat }}" class="pasien-checkbox" checked data-biaya="{{ $biayaItem }}" data-key="{{ $key }}" style="cursor: pointer; width: 18px; height: 18px;">
                    </td>
                @endif
                <td width="30px" class="nomor-urut" data-original="{{ $key + 1 }}">{{ $key + 1 }}. </td>
                <td class="cell-nama">{{ $item->nm_pasien }}</td>
                <td class="cell-biaya" data-biaya="{{ $biayaItem }}">Rp. {{ number_format($biayaItem, 0, ',', '.') }}</td>
                <td>
                    @foreach ($item->getNomorNota as $detail)
                        {{ substr(str_replace(':', '', $detail->nm_perawatan), -6) }}
                    @endforeach
                </td>
                <td>{{ $item->nomor_kartu }}</td>
                <td>{{ $item->nomor_klaim }}</td>
                <td>{{ $item->no_rkm_medis }}</td>
                <td>
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
        <tr class="font-weight-bold">
            @if ($is_preview ?? false)
                <td></td>
            @endif
            <td></td>
            <td><b>Total</b></td>
            <td><b>Rp. <span id="preview-total-biaya">{{ number_format(
                        $getPasien->sum(function ($item) {
                            return $item->getTotalBiaya->sum('totalpiutang');
                        }),
                        0,
                        ',',
                        '.',
                    ) }}</span></b>
            </td>
            <td colspan="5"></td>
        </tr>
    @endif
</table>
