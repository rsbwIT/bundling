@if ($getSoapie)
    @foreach ($getSoapie as $item)
        <div class="card-body">
            <table width="700px">
                <tr>
                    <td rowspan="4"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                            width="80" height="80">
                    </td>
                    <td class="text-center">
                        <h2>{{ $getSetting->nama_instansi }}</h2>
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
                <tr class="text-center">
                    <td colspan="">
                        <h3 class="mt-2">S.O.A.P.I.E PASIEN</h3>
                    </td>
                </tr>
            </table>
            <hr width="700px" class="mt-0"
                style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
            <table width="700px">
                <tr style="vertical-align: top;">
                    <td width="115px">Nama Pasien</td>
                    <td width="300px">: {{ $item->nm_pasien }}</td>
                    <td width="110px">No.RM</td>
                    <td width="160px">: {{ $item->no_rkm_medis }}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>Alamat</td>
                    <td>: {{ $item->alamat }}</td>
                    <td>Jenis Kelamin</td>
                    <td>: {{ $item->jk }}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>Tempat & Tanggal Lahir</td>
                    <td>: {{ $item->tmp_lahir }} - {{ $item->tgl_lahir }}</td>
                    <td>Ibu Kandung</td>
                    @php
                        $nm_ibu = $item->nm_ibu;
                        $nm_ibu_masked =
                            strlen($nm_ibu) > 4
                                ? substr($nm_ibu, 0, 2) . str_repeat('*', strlen($nm_ibu) - 4) . substr($nm_ibu, -2)
                                : $nm_ibu;
                    @endphp
                    <td>: {{ $nm_ibu_masked }}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>Dokter</td>
                    <td>: {{ $item->nm_dokter }}</td>
                    @php
                        $cacat_fisik = $item->cacat_fisik;
                        switch ($cacat_fisik) {
                            case 1:
                                $cacat_fisik_text = '-';
                                break;
                            case 3:
                                $cacat_fisik_text = 'Bisu';
                                break;
                            case 2:
                                $cacat_fisik_text = 'Buta';
                                break;
                            case 8:
                                $cacat_fisik_text = 'Lumpuh';
                                break;
                            case 5:
                                $cacat_fisik_text = 'TIDAK ADA';
                                break;
                            case 4:
                                $cacat_fisik_text = 'Tidak Punya Tangan Kanan';
                                break;
                            default:
                                $cacat_fisik_text = 'Tidak Diketahui';
                        }
                    @endphp
                    <td>Cacat Fisik</td>
                    <td>: {{ $cacat_fisik_text }}</td>
                </tr>
            </table>
            <table border="1px" width="700px" class="mt-3">
                <tr>
                    <td class="text-center"><b>Diagnosa</b></td>
                    <td class="text-center"><b>Prosedur</b></td>
                </tr>
                <tr>
                    <td height="50px" style="vertical-align: top;">
                        <ul class="m-0">
                            @foreach ($item->getDiagnosa as $diagnosa)
                                <li>{{ $diagnosa->prioritas }}. {{ $diagnosa->kd_penyakit }} -
                                    {{ $diagnosa->nm_penyakit }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td height="50px" style="vertical-align: top;">
                        <ul class="m-0">
                            @foreach ($item->getProcedure as $diagnosa)
                                <li>{{ $diagnosa->prioritas }}. {{ $diagnosa->kode }} -
                                    {{ $diagnosa->deskripsi_pendek }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" width="50%"><b>Subjek</b></td>
                    <td class="text-center" width="50%"><b>Objek</b></td>
                </tr>
                <tr>
                    <td height="100px" style="vertical-align: top;">{{ $item->keluhan }}</td>
                    <td height="100px" style="vertical-align: top;">
                        {{ $item->pemeriksaan }}
                        <table>
                            @if (!empty($item->alergi))
                                <tr>
                                    <td>Alergi</td>
                                    <td>: {{ $item->alergi }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->suhu_tubuh))
                                <tr>
                                    <td>Suhu(C)</td>
                                    <td>: {{ $item->suhu_tubuh }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->tensi))
                                <tr>
                                    <td>Tensi</td>
                                    <td>: {{ $item->tensi }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->nadi))
                                <tr>
                                    <td>Nadi(/menit)</td>
                                    <td>: {{ $item->nadi }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->respirasi))
                                <tr>
                                    <td>Respirasi(/menit)</td>
                                    <td>: {{ $item->respirasi }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->tinggi))
                                <tr>
                                    <td>Tinggi(Cm)</td>
                                    <td>: {{ $item->tinggi }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->berat))
                                <tr>
                                    <td>Berat(Kg)</td>
                                    <td>: {{ $item->berat }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->spo2))
                                <tr>
                                    <td>SpO2(%)</td>
                                    <td>: {{ $item->spo2 }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->gcs))
                                <tr>
                                    <td>GCS(E,V,M)</td>
                                    <td>: {{ $item->gcs }}</td>
                                </tr>
                            @endif
                            @if (!empty($item->kesadaran))
                                <tr>
                                    <td>Kesadaran</td>
                                    <td>: {{ $item->kesadaran }}</td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="text-center"><b>Asesmen</b></td>
                    <td class="text-center"><b>Plan</b></td>
                </tr>
                <tr>
                    <td height="100px" style="vertical-align: top;">{{ $item->penilaian }}</td>
                    <td height="100px" style="vertical-align: top;">{{ $item->rtl }}</td>
                </tr>
                <tr>
                    <td class="text-center"><b>Instruksi</b></td>
                    <td class="text-center"><b>Evaluasi</b></td>
                </tr>
                <tr>
                    <td height="100px" style="vertical-align: top;">{{ $item->instruksi }}</td>
                    <td height="100px" style="vertical-align: top;">{{ $item->evaluasi }}</td>
                </tr>
            </table>

            <table width="700px" class="mt-3">
                <tr>
                    <td width="250px" class="text-center">

                    </td>
                    <td width="150px"></td>
                    <td width="250px" class="text-center">
                        Penanggung Jawab
                        <div class="barcode mt-1">
                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->nm_dokter . ' ID ' . $item->kd_dokter . ' ' . $item->tgl_perawatan, 'QRCODE') }}"
                                alt="barcode" width="80px" height="75px" />
                        </div>
                        {{ $item->nm_dokter }}
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
@else
    {{-- NULL --}}
@endif
