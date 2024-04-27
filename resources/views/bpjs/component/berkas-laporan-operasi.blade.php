@if ($getLaporanOprasi)
    @foreach ($getLaporanOprasi as $item)
        <div class="card-body">
            <div class="card p-4 d-flex justify-content-center align-items-center">
                <table border="0px" width="1000px">
                    <tr>
                        <td rowspan="3"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                alt="Girl in a jacket" width="80" height="80"></td>
                        <td class="text-center">
                            <h4>{{ $getSetting->nama_instansi }} </h4>
                        </td>
                        <td class="text-center" width="100px">
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
                <hr width="1000px" class="mt-1 mb-1"
                    style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                <table border="0px" width="1000px">
                    <tr class="text-center">
                        <td>
                            <h5><b>LAPORAN OPERASI</b></h5>
                        </td>
                    </tr>
                </table>
                <hr width="1000px" class="mt-0 mb-0" style=" height:2px; border-top:1px solid black;">
                <table border="0px" width="1000px">
                    <tr style="vertical-align: top;">
                        <td width="100px">Nama Pasien</td>
                        <td width="400px">: {{ $item->nm_pasien }}</td>
                        <td width="100px">No. Rekam Medis</td>
                        <td width="200px">: {{ $item->no_rkm_medis }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="100px">Umur</td>
                        <td width="400px">: {{ $item->umurdaftar }} {{ $item->sttsumur }}</td>
                        <td width="100px">Ruangan</td>
                        <td width="200px">: {{ $item->nm_bangsal }} </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="100px">Tanggl Lahir</td>
                        <td width="400px">: {{ date('d-m-Y', strtotime($item->tgl_lahir)) }}</td>
                        <td width="100px">Jenis Kelamin</td>
                        <td width="200px">: {{ $item->jk }} </td>
                    </tr>
                </table>
                <table border="1px" width="1000px" class="mt-1">
                    <tr>
                        <td class="text-center" style="background-color: rgb(192, 192, 192)">
                            <b>PRE SURGICAL ASSESMENT</b>
                        </td>
                    </tr>
                </table>
                <table border="0px" width="1000px">
                    <tr style="vertical-align: top;">
                        <td width="100px">Tanggal</td>
                        <td width="400px">: {{ $item->pemeriksaanRanap->tgl_perawatan }}</td>
                        <td width="100px">Waktu</td>
                        <td width="200px">: {{ $item->pemeriksaanRanap->jam_rawat }} </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="100px">Dokter Bedah</td>
                        <td width="400px">: {{ $item->operator1 }}</td>
                        <td width="100px">Alergi</td>
                        <td width="200px">: {{ $item->pemeriksaanRanap->alergi }} </td>
                    </tr>
                </table>
                <hr width="1000px" class="mt-0 mb-0" style=" height:2px; border-top:1px solid black;">
                <table border="0px" width="1000px">
                    <tr style="vertical-align: top;">
                        <td width="500px" style="border-right: 1px solid black">
                            <table border="0px" width="500px">
                                <tr>
                                    <td width="500px">
                                        <b>Keluhan : </b> {{ $item->pemeriksaanRanap->keluhan }}
                                    </td>
                                </tr>
                            </table>
                            <table border="0px" width="500px">
                                <tr>
                                    <td colspan="4"><b>Pemeriksaan :</b></td>
                                </tr>
                                <tr>
                                    <td width="120px">- Suhu Tubuh (C)</td>
                                    <td>: <i>{{ $item->pemeriksaanRanap->suhu_tubuh }}</i></td>
                                    <td width="120px">- Nadi (/Mnt)</td>
                                    <td>: <i>{{ $item->pemeriksaanRanap->nadi }}</i></td>
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
                            @if ($item->riwayat_persalinan_g || $item->riwayat_persalinan_p || $item->riwayat_persalinan_a)
                                <table border="0px" width="500px">
                                    <tr>
                                        <td colspan="4"><b>Riwayat Persalinan & Nifas :</b></td>
                                    </tr>
                                    <tr>
                                        <td>- G : {{ $item->riwayat_persalinan_g }}</td>
                                        <td>- P : {{ $item->riwayat_persalinan_p }}</td>
                                        <td>- A : {{ $item->riwayat_persalinan_a }}</td>
                                        <td>- Anak yang hidup : {{ $item->riwayat_persalinan_hidup }}</td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                        <td width="500px">
                            <table border="0px" width="500px">
                                <tr>
                                    <td width="500px">
                                        <b>Penilaian : </b> {{ $item->pemeriksaanRanap->penilaian }}
                                    </td>
                                </tr>
                            </table>
                            <table border="0px" width="500px">
                                <tr>
                                    <td width="500px">
                                        <b>Tindak Lanjut : </b>{{ $item->pemeriksaanRanap->rtl }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table border="1px" width="1000px" class="mt-1">
                    <tr>
                        <td class="text-center" style="background-color: rgb(192, 192, 192)">
                            <b>POST SURGICAL REPORT</b>
                        </td>
                    </tr>
                </table>
                <table border="0px" width="1000px">
                    <tr style="vertical-align: top;">
                        <td width="700px" style="border-right: 1px solid black">
                            <table border="0px" width="700px">
                                <tr>
                                    <td width="150px">Tanggal & Jam</td>
                                    <td width="220px">: {{ $item->tgl_operasi }}</td>
                                    <td width="150px"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Dokter Bedah</td>
                                    <td>: {{ $item->operator1 }}</td>
                                    <td>Asisten Bedah</td>
                                    <td>: {{ $item->asistenoperator1 }}</td>
                                </tr>
                                <tr>
                                    <td>Dokter Bedah 2</td>
                                    <td>: {{ $item->operator2 }}</td>
                                    <td>Asisten Bedah 2</td>
                                    <td>: {{ $item->asistenoperator2 }}</td>
                                </tr>
                                <tr>
                                    <td>Perawat Resusitas</td>
                                    <td>: {{ $item->perawatresusitas }}</td>
                                    <td>Dokter Anastesi</td>
                                    <td>: {{ $item->anastesi }}</td>
                                </tr>
                                <tr>
                                    <td>Instrumen</td>
                                    <td>: {{ $item->instrumen }}</td>
                                    <td>Asisten Anastesi</td>
                                    <td>: {{ $item->asistenanastesi }}</td>
                                </tr>
                                <tr>
                                    <td>Dokter Anak</td>
                                    <td>: {{ $item->pjanak }}</td>
                                    <td>Bidan</td>
                                    <td>: {{ $item->bidan1 }}</td>
                                </tr>
                                <tr>
                                    <td>Dokter Umum</td>
                                    <td>: {{ $item->dokumum }}</td>
                                    <td>Onloop</td>
                                    <td>: {{ $item->omloop }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background-color: rgb(192, 192, 192)">Diagnosa Pre-Op /
                                        Pre Operation Diagnosis</td>
                                </tr>
                                <tr>
                                    <td colspan="4">&emsp; {{ $item->diagnosa_preop }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background-color: rgb(192, 192, 192)">Jaringan Yang
                                        di-Eksisi/-Insisi</td>
                                </tr>
                                <tr>
                                    <td colspan="4">&emsp; {{ $item->jaringan_dieksekusi }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background-color: rgb(192, 192, 192)">Diagnosa Post-Op /
                                        Post Operation Diagnosis</td>
                                </tr>
                                <tr>
                                    <td colspan="4">&emsp; {{ $item->diagnosa_postop }}</td>
                                </tr>
                            </table>
                        </td>
                        <td width="300px">
                            <table border="1px" width="298px" class="text-center">
                                <tr>
                                    <td style="background-color: rgb(192, 192, 192)">Tipe/Jenis Anastesi :</td>
                                </tr>
                                <tr>
                                    <td class="pb-4">{{ $item->jenis_anasthesi }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgb(192, 192, 192)">Dikirim ke Pemeriksaaan PA :</td>
                                </tr>
                                <tr>
                                    <td class="pb-4">{{ $item->permintaan_pa }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgb(192, 192, 192)">Tipe/Kategori Operasi :</td>
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
                <table border="1px" width="1000px" class="mt-1">
                    <tr>
                        <td class="text-center" style="background-color: rgb(192, 192, 192)">
                            <b>REPORT ( PROCEDURES, SPECIFIC FINDINGS AND COMPLICATIONS )</b>
                        </td>
                    </tr>
                </table>
                <table border="0px" width="1000px" class="mt-1">
                    <tr>
                        <td class="pb-5">&emsp; {{ $item->laporan_operasi }}
                        </td>
                    </tr>
                </table>
                <table border="0px" width="1000px" class="mt-3" class="">
                    <tr>
                        <td width="250px" class="text-center">

                        </td>
                        <td width="150px"></td>
                        <td width="250px" class="text-center">
                            {{ $getSetting->kabupaten }}, {{ date('d/m/Y', strtotime($item->tgl_operasi)) }}<br>
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
        </div>
    @endforeach
@else
    {{-- NULL --}}
@endif
