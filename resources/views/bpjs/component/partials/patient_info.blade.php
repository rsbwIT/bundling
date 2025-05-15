<table border="0px" width="1000px">
    <tr style="vertical-align: top;">
        <td width="100px">Nama Pasien</td>
        <td width="400px">: {{ $getResume->nm_pasien }}</td>
        <td width="100px">No. Rekam Medis</td>
        <td width="200px">: {{ $getResume->no_rkm_medis }}</td>
    </tr>
    <tr style="vertical-align: top;">
        <td width="100px">Umur</td>
        @php
            $tanggal_lahir_obj = date_create($getResume->tgl_lahir);
            $today = date_create('today');
            $umur = date_diff($tanggal_lahir_obj, $today);
        @endphp
        <td width="400px">: {{ $umur->y . ' Tahun, ' . $umur->m . ' Bulan' }}</td>
        <td width="100px">
            @if ($statusLanjut->status_lanjut == 'Ranap')
                Ruang
            @else
                Poliklinik
            @endif
        </td>
        <td width="200px">:
            @if ($statusLanjut->status_lanjut == 'Ranap')
                @if ($getKamarInap)
                    {{ $getKamarInap->kd_kamar }} |
                    {{ $getKamarInap->nm_bangsal }}
                @endif
            @else
                {{ $getResume->nm_poli }}
            @endif
        </td>
    </tr>
    <tr style="vertical-align: top;">
        <td width="100px">Tgl Lahir</td>
        <td width="400px">: {{ date('d-m-Y', strtotime($getResume->tgl_lahir)) }}</td>
        <td width="100px">Jenis Kelamin</td>
        @php
            $jnsKelamin = $getResume->jenis_kelamin == 'P' ? 'Perempuan' : 'Laki-laki';
        @endphp
        <td width="200px">: {{ $jnsKelamin }}</td>
    </tr>
    <tr style="vertical-align: top;">
        <td width="100px">Pekerjaan</td>
        <td width="400px">: {{ $getResume->pekerjaan }}</td>
        <td width="100px">
            @if ($statusLanjut->status_lanjut == 'Ranap')
                Tanggal Masuk
            @else
                Tanggal Periksa
            @endif
        </td>
        <td width="200px">:
            @if ($statusLanjut->status_lanjut == 'Ranap')
                {{ date('d-m-Y', strtotime($getResume->tgl_masuk)) }}
            @else
                {{ date('d-m-Y', strtotime($getResume->tgl_perawatan)) }}
            @endif
        </td>
    </tr>
    <tr style="vertical-align: top;">
        <td width="100px">Alamat</td>
        <td width="400px">: {{ $getResume->alamat }}</td>
        <td width="100px">
            @if ($statusLanjut->status_lanjut == 'Ranap')
                Tanggal Keluar
            @endif
        </td>
        <td width="200px">
            @if ($statusLanjut->status_lanjut == 'Ranap')
                @if ($getKamarInap)
                    : {{ date('d-m-Y', strtotime($getKamarInap->tgl_keluar)) }}
                @endif
            @endif
        </td>
    </tr>
</table>
