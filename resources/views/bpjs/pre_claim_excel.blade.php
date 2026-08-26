<table border="1">
    <thead>
        <tr>
            <th>No. Rawat</th>
            <th>No. RM</th>
            <th>Nama Pasien</th>
            <th>Poliklinik</th>
            <th>DPJP</th>
            <th>Status SEP</th>
            <th>Resume Medis</th>
            <th>Lap. Operasi</th>
            <th>Koding INACBG</th>
            <th>Tarif INACBG (Rp)</th>
            <th>Saran Optimasi</th>
            <th>Status Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->no_rawat }}</td>
            <td>{{ $row->no_rkm_medis }}</td>
            <td>{{ $row->nama_pasien }}</td>
            <td>{{ $row->poliklinik }}</td>
            <td>{{ $row->nama_dokter }}</td>
            <td>{{ $row->status_sep == 1 ? 'Ada (' . $row->no_sep . ')' : 'Tidak Ada' }}</td>
            <td>{{ $row->resume_medis == 1 ? 'Lengkap' : 'Belum TTD' }}</td>
            <td>
                @if($row->laporan_operasi === 1) Lengkap
                @elseif($row->laporan_operasi === 0) Kosong
                @else -
                @endif
            </td>
            <td>{{ $row->koding_inacbg == 1 ? 'Selesai (' . $row->code_cbg . ')' : 'Belum' }}</td>
            <td>{{ $row->tarif_inacbg }}</td>
            <td>{{ strip_tags(str_replace(['<br>', '<hr class=\'my-2\'>'], "\n", $row->saran_optimasi)) }}</td>
            <td>{{ $row->status_klaim }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
