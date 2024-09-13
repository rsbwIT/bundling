@if ($getTriaseIGD)
    @php
        switch ($getTriaseIGD->plan) {
            case 'Zona Hijau':
                $bgstyle = 'rgb(57, 202, 0)';
                break;
            case 'Zona Kuning':
                $bgstyle = 'rgb(241, 217, 0)';
                break;
            default:
                $bgstyle = 'rgb(204, 204, 204)';
                break;
        }
    @endphp
    <div class="card-body">
        <table width="700px">
            <tr>
                <td rowspan="4"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="80"
                        height="80"></td>
            </tr>
            <tr>
                <td class="text-center">
                    <h2>{{ $getSetting->nama_instansi }} </h2>
                </td>

            </tr>
            <tr class="text-center">
                <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                    {{ $getSetting->propinsi }}
                    {{ $getSetting->kontak }}</td>
            </tr>
            <tr class="text-center">
                <td> E-mail : {{ $getSetting->email }}</td>
            </tr>
        </table>
        <table border="1px" width="700px" class="mt-1">
            <tr class="text-center">
                <td style="background-color:{{ $bgstyle }};">
                    <h4 class="mt-0 mb-0"><b>TRIASE PASIEN GAWAT DARURAT</b></h4>
                </td>
            </tr>
            <tr class="text-center">
                <td>Triase dilakukan segera setelah pasien datang dan sebelum pasien/ keluarga mendaftar di TPP IGD
                </td>
            </tr>
        </table>
        <table width="700px" class="mt-1">
            <tr style="vertical-align: top;">
                <td width="115px">Nama Pasien</td>
                <td width="300px">: {{ $getTriaseIGD->nm_pasien }}</td>
                <td width="110px">No. Rekam Medis</td>
                <td width="160px">: {{ $getTriaseIGD->no_rkm_medis }}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>Tanggal Lahir</td>
                <td>: {{ $getTriaseIGD->tgl_lahir }}</td>
                <td>Jenis Kelamin</td>
                <td>: {{ $getTriaseIGD->jk == 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>Tanggal Kunjungan</td>
                <td>: {{ date('d-m-Y', strtotime($getTriaseIGD->tgl_kunjungan)) }}</td>
                <td>Pukul</td>
                <td>: {{ date('H:i:s', strtotime($getTriaseIGD->tgl_kunjungan)) }}</td>
            </tr>
        </table>
        <table border="1px" width="700px" class="mt-1">
            <tr style="vertical-align: top;">
                <td width="150px">Cara Datang</td>
                <td width="350px">: {{ $getTriaseIGD->cara_masuk }}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>Macam Kasus</td>
                <td>: {{ $getTriaseIGD->macam_kasus }}</td>
            </tr>
        </table>
        <table border="1px" width="700px" class="mt-1">
            <tr style="vertical-align: top;">
                <td width="250px" class="text-center" style="background-color: rgb(255, 246, 163)">KETERANGAN</td>
                <td class="text-center" style="background-color: rgb(255, 246, 163)">TRIASE SEKUNDER</td>
            </tr>
            <tr style="vertical-align: top;">
                <td height="100px">ANAMNESA SINGKAT</td>
                <td height="100px">{{ $getTriaseIGD->anamnesa_singkat }}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>TANDA VITAL</td>
                <td>
                    Suhu (C) : {{ $getTriaseIGD->suhu }},
                    Nyeri : {{ $getTriaseIGD->nyeri }},
                    Tensi : {{ $getTriaseIGD->tekanan_darah }},
                    Nadi(/menit) : {{ $getTriaseIGD->nadi }},
                    Saturasi O²(%) : {{ $getTriaseIGD->saturasi_o2 }},
                    Respirasi(/menit) : {{ $getTriaseIGD->pernapasan }}
                </td>
            </tr>
        </table>
        <table border="1px" width="700px" class="mt-1">
            <tr style="vertical-align: top;">
                <td width="250px" class="text-center" style="background-color: rgb(255, 246, 163)">PEMERIKSAAN</td>
                <td class="text-center" style="background-color: {{$bgstyle}}">URGENSI</td>
            </tr>
            @foreach ($getTriaseIGD->masterTriase as $item)
                <tr style="vertical-align: top;">
                    <td>{{ $item->nama_pemeriksaan }}</td>
                    <td style="background-color: {{$bgstyle}}">
                        @foreach ($item->skala as $skala)
                            {{ $skala->pengkajian_skala3 }}
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </table>
        <table width="700px" class="mt-1">
            <tr style="vertical-align: top;">
                <td class="text-center" colspan="2" style="background-color: rgb(255, 246, 163)"><b>Petugas Triase Sekunder</b></td>
            </tr>
            <tr style="vertical-align: top;">
                <td width="125px">Tanggal & Jam</td>
                <td>: {{ date('d-m-Y', strtotime($getTriaseIGD->tgl_kunjungan)) }}
                    {{ date('H:i:s', strtotime($getTriaseIGD->tgl_kunjungan)) }}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>Catatan</td>
                <td>: {{ $getTriaseIGD->catatan }}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td>Dokter / Petugas Jaga IGD</td>
                <td>: {{ $getTriaseIGD->nama }}</td>
            </tr>
        </table>
        <table width="700px" class="mt-1">
            <tr>
                <td width="250px" class="text-center">

                </td>
                <td width="150px"></td>
                <td width="250px" class="text-center">
                    Dokter / Petugas Jaga
                    <div class="barcode mt-1">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getTriaseIGD->nama . ' ID ' . $getTriaseIGD->nik . ' ' . $getTriaseIGD->tgl_kunjungan, 'QRCODE') }}"
                            alt="barcode" width="80px" height="75px" />
                    </div>
                    {{ $getTriaseIGD->nama }}
                </td>
            </tr>
        </table>
    </div>
@endif
