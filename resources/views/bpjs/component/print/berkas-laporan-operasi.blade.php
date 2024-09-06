@if ($getLaporanOprasi)
            @foreach ($getLaporanOprasi as $item)
                <div class="">
                    <table width="700px">
                        <tr>
                            <td rowspan="3"> <img
                                    src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                    alt="Girl in a jacket" width="80" height="80"></td>
                            <td class="text-center">
                                <h2>{{ $getSetting->nama_instansi }} </h2>
                            </td>
                            <td width="50px"></td>
                        </tr>
                        <tr class="text-center">
                            <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                                {{ $getSetting->propinsi }}
                                {{ $getSetting->kontak }}</td>
                            <td width="50px"></td>
                        </tr>
                        <tr class="text-center">
                            <td> E-mail : {{ $getSetting->email }}</td>
                            <td width="50px"></td>
                        </tr>
                    </table>
                    <hr width="700px" class="mt-0"
                            style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                    <table width="700px" class="mt-0 mb-0">
                        <tr class="text-center">
                            <td colspan="0">
                                <b>LAPORAN OPERASI</b>
                            </td>
                        </tr>
                    </table>
                    <hr width="700px" class="mt-0 mb-1" style="border:1px solid black;">
                    <table width="700px">
                        <tr style="vertical-align: top;">
                            <td width="140px">Nama Pasien</td>
                            <td width="250px">: {{ $item->nm_pasien }}</td>
                            <td width="140px">No. Rekam Medis</td>
                            <td width="180px">: {{ $item->no_rkm_medis }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td >Umur</td>
                            <td>: {{ $item->umurdaftar }} {{ $item->sttsumur }}</td>
                            <td >Ruangan</td>
                            <td>: {{ $item->nm_bangsal }} </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td >Tanggl Lahir</td>
                            <td>: {{ date('d-m-Y', strtotime($item->tgl_lahir)) }}</td>
                            <td >Jenis Kelamin</td>
                            <td>: {{ $item->jk }} </td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td class="text-center" style="background-color: rgb(192, 192, 192)">
                                <b>PRE SURGICAL ASSESMENT</b>
                            </td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr style="vertical-align: top;">
                            <td width="140px">Tanggal</td>
                            <td width="250px">: {{ $item->pemeriksaanRanap->tgl_perawatan }}</td>
                            <td width="140px" >Waktu</td>
                            <td width="180px">: {{ $item->pemeriksaanRanap->jam_rawat }} </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td >Dokter Bedah</td>
                            <td>: {{ $item->operator1 }}</td>
                            <td >Alergi</td>
                            <td>: {{ $item->pemeriksaanRanap->alergi }} </td>
                        </tr>
                    </table>
                    <hr width="700px" class="mt-1 mb-1" style="border:1px solid black;">
                    <table width="700px">
                        <tr style="vertical-align: top;">
                            <td width="400px" style="border-right: 1px solid black">
                                <table  width="200px">
                                    <tr>
                                        <td width="200px">
                                            <b>Keluhan : </b> {{ $item->pemeriksaanRanap->keluhan }}
                                        </td>
                                    </tr>
                                </table>
                                <table width="200px">
                                    <tr>
                                        <td colspan="4"><b>Pemeriksaan :</b></td>
                                    </tr>
                                    <tr>
                                        <td width="61px">- Suhu Tubuh (C)</td>
                                        <td width="35px">: <i>{{ $item->pemeriksaanRanap->suhu_tubuh }}</i></td>
                                        <td width="61px">- Nadi (/Mnt)</td>
                                        <td width="43px">: <i>{{ $item->pemeriksaanRanap->nadi }}</i></td>
                                    </tr>
                                    <tr>
                                        <td>- Tensi </td>
                                        <td>: <i>{{ $item->pemeriksaanRanap->tensi }}</i></td>
                                        <td>- Respirasi (/Mnt)</td>
                                        <td>: <i>{{ $item->pemeriksaanRanap->respirasi }}</i></td>
                                    </tr>
                                    <tr>
                                        <td>- Tinggi</td>
                                        <td>: <i>{{ $item->pemeriksaanRanap->tinggi }}</i></td>
                                        <td>- GCS (E,V,M)</td>
                                        <td>: <i>{{ $item->pemeriksaanRanap->gcs }}</i></td>
                                    </tr>
                                    <tr>
                                        <td>- Berat (Kg)</td>
                                        <td>: <i>{{ $item->pemeriksaanRanap->berat }}</i></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="300px">
                                <table width="300px">
                                    <tr>
                                        <td>
                                            <b>Penilaian : </b> {{ $item->pemeriksaanRanap->penilaian }}
                                        </td>
                                    </tr>
                                </table>
                                <table width="300px">
                                    <tr>
                                        <td>
                                            <b>Tindak Lanjut : </b>{{ $item->pemeriksaanRanap->rtl }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table  width="700px" class="mt-1">
                        <tr>
                            <td class="text-center" style="background-color: rgb(192, 192, 192)">
                                <b>POST SURGICAL REPORT</b>
                            </td>
                        </tr>
                    </table>
                    <table  width="700px" class="mt-1">
                        <tr style="vertical-align: top;">
                            <td width="500px" style="border-right: 1px solid black">
                                <table width="500px">
                                    <tr style="vertical-align: top;">
                                        <td width="120px">Tanggal & Jam</td>
                                        <td width="130px">: {{ $item->tgl_operasi }}</td>
                                        <td width="130px"></td>
                                        <td width="115px"></td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Dokter Bedah</td>
                                        <td>: {{ $item->operator1 }}</td>
                                        <td>Asisten Bedah</td>
                                        <td>: {{ $item->asistenoperator1 }}</td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Dokter Bedah 2</td>
                                        <td>: {{ $item->operator2 }}</td>
                                        <td>Asisten Bedah 2</td>
                                        <td>: {{ $item->asistenoperator2 }}</td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Perawat Resusitas</td>
                                        <td>: {{ $item->perawatresusitas }}</td>
                                        <td>Dokter Anastesi</td>
                                        <td>: {{ $item->anastesi }}</td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Instrumen</td>
                                        <td>: {{ $item->instrumen }}</td>
                                        <td>Asisten Anastesi</td>
                                        <td>: {{ $item->asistenanastesi }}</td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Dokter Anak</td>
                                        <td>: {{ $item->pjanak }}</td>
                                        <td>Bidan</td>
                                        <td>: {{ $item->bidan1 }}</td>
                                    </tr>
                                    <tr style="vertical-align: top;">
                                        <td>Dokter Umum</td>
                                        <td>: {{ $item->dokumum }}</td>
                                        <td>Onloop</td>
                                        <td>: {{ $item->omloop }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="background-color: rgb(192, 192, 192)">Diagnosa
                                            Pre-Op / Pre Operation Diagnosis</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">&emsp; {{ $item->diagnosa_preop }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="background-color: rgb(192, 192, 192)">Jaringan
                                            Yang di-Eksisi/-Insisi</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">&emsp; {{ $item->jaringan_dieksekusi }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="background-color: rgb(192, 192, 192)">Diagnosa
                                            Post-Op / Post Operation Diagnosis</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">&emsp; {{ $item->diagnosa_postop }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="196px">
                                <table  width="194px" class="text-center">
                                    <tr>
                                        <td style="background-color: rgb(192, 192, 192)">Tipe/Jenis Anastesi :</td>
                                    </tr>
                                    <tr>
                                        <td class="pb-4">{{ $item->jenis_anasthesi }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: rgb(192, 192, 192)">Dikirim ke Pemeriksaaan PA
                                            :</td>
                                    </tr>
                                    <tr>
                                        <td class="pb-4">{{ $item->permintaan_pa }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: rgb(192, 192, 192)">Tipe/Kategori Operasi :
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pb-4">{{ $item->kategori }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: rgb(192, 192, 192)">Selesai Operasi :</td>
                                    </tr>
                                    <tr>
                                        <td class="pb-4">{{ $item->selesaioperasi }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td class="text-center" style="background-color: rgb(192, 192, 192)">
                                <b>REPORT ( PROCEDURES, SPECIFIC FINDINGS AND COMPLICATIONS )</b>
                            </td>
                        </tr>
                    </table>
                    <table  width="700px" class="mt-1">
                        <tr>
                            <td class="pb-4">&emsp; {{ $item->laporan_operasi }}
                        </tr>
                    </table>
                    <table  width="700px" class="mt-1">
                        <tr>
                            <td width="265px" class="text-center">

                            </td>
                            <td width="170px"></td>
                            <td width="265px" class="text-center">
                                {{ $getSetting->kabupaten }},
                                {{ date('d/m/Y', strtotime($item->tgl_operasi)) }}<br>
                                Dokter Bedah
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->operator1 . $item->kd_dokter1 . ' ' . $item->tgl_operasi, 'QRCODE') }}"
                                        alt="barcode" width="80px" height="75px" />
                                </div>
                                {{ $item->operator1 }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        @else
        @endif
