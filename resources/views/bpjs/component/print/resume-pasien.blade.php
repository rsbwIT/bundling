@if ($getResume && $statusLanjut)
    @if ($statusLanjut->kd_poli === 'U0061' || $statusLanjut->kd_poli === 'FIS')
        <div class="card-body">
            <div class="card p-4 d-flex justify-content-center align-items-center">
                <table width="700px">
                    <tr>
                        <td rowspan="3"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                alt="Girl in a jacket" width="80" height="80"></td>
                        <td class="text-center">
                            <span class="h3">{{ $getSetting->nama_instansi }}</span>
                        </td>
                        <td class="text-center" width="100px">
                        </td>
                    </tr>
                    <tr class="text-center mr-5">
                        <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                            {{ $getSetting->propinsi }}
                            {{ $getSetting->kontak }}</td>
                    </tr>
                    <tr class="text-center">
                        <td> E-mail : {{ $getSetting->email }}</td>
                    </tr>
                </table>
                <hr width="700px" class="mt-0"
                    style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                <table width="700px">
                    <tr class="text-center">
                        <td colspan="0">
                            <span class="h4">LEMBAR FORMULIR RAWAT JALAN <br /> LAYANAN KEDOKTERAN
                                FISIK DAN REHABILITAS</span>
                        </td>
                    </tr>
                </table>
                <div style="border:solid black 1px;">
                    <table width="985px" class="mx-1">
                        <tr>
                            <td><b>Data Pasien</b></td>
                        </tr>
                        <tr>
                            <td width="180px">No.Rawat</td>
                            <td>: {{ $getResume->no_rawat }}</td>
                        </tr>
                        <tr>
                            <td>No.RM</td>
                            <td>: {{ $getResume->no_rkm_medis }}</td>
                        </tr>
                        <tr>
                            <td>Nama Pasien</td>
                            <td>: {{ $getResume->nm_pasien }}</td>
                        </tr>
                        <tr>
                            <td>Poliklinik</td>
                            <td>: {{ $getResume->nm_poli }}</td>
                        </tr>
                    </table>
                </div>
                <div style="border:solid black 1px; margin-top: 10px">
                    <table width="685px" class="mx-1">
                        <tr>
                            <td><b>Diisi oleh Dokter</b></td>
                        </tr>
                        <tr>
                            <td width="180px">Tanggal Pelayanan</td>
                            <td>: {{ $getResume->tgl_perawatan }}</td>
                        </tr>
                        <tr>
                            <td>Anamnesa</td>
                            <td>: {{ $getResume->keluhan }}</td>
                        </tr>
                        <tr>
                            <td>Diagnosa</td>
                            <td>: {{ $getResume->penilaian }}</td>
                        </tr>
                        <tr>
                            <td>Pemeriksaan Fisik dan Uji Fungsi</td>
                            <td>: {{ $getResume->pemeriksaan }}</td>
                        </tr>
                        <tr>
                            {{-- <td>Suhu Tubuh</td> --}}
                            <td>Program Terapi Ke</td>
                            <td>: {{ $getResume->suhu_tubuh }}</td>
                        </tr>
                        <tr>
                            <td>Tensi</td>
                            <td>: {{ $getResume->tensi }}</td>
                        </tr>
                        <tr>
                            <td>Nadi</td>
                            <td>: {{ $getResume->nadi }}</td>
                        </tr>
                        <tr>
                            <td>Anjuran</td>
                            <td>: {{ $getResume->instruksi }}</td>
                        </tr>
                        <tr>
                            <td>Evaluasi</td>
                            <td>: {{ $getResume->evaluasi }}</td>
                        </tr>
                        <tr>
                            <td>Tata Laksana KFR (ICD 9 CM)</td>
                            <td>: {{ $getResume->rtl }}</td>
                        </tr>
                    </table>
                </div>
                <table width="700px" class="mt-1">
                    <tr>
                        <td width="250px" class="text-center">

                        </td>
                        <td width="150px"></td>
                        <td width="250px" class="text-center">
                            Dokter Penanggung Jawab
                            <div class="barcode mt-1">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh '. $getResume->dokter_fiso .  $getResume->kd_dokter . ' ' . $getResume->tgl_registrasi, 'QRCODE') }}"
                                    alt="barcode" width="55px" height="55px" />
                            </div>
                            {{$getResume->dokter_fiso}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @else
        @if ($statusLanjut->status_lanjut == 'Ranap')
            {{-- BERKAS RESUME RANAP --}}
            <div class="card-body">
                <div class="">
                    <table width="700px">
                        <tr>
                            <td rowspan="3" width="90"> <img
                                    src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="80"
                                    height="80"></td>
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
                        <tr class="text-center">
                            <td colspan="2">
                                <h3 class="mt-1"><b>RESUME MEDIS PASIEN</b></h3>
                            </td>
                        </tr>
                    </table>
                    <table width="700px">
                        <tr style="vertical-align: top;">
                            <td width="115px">Nama Pasien</td>
                            <td width="300px">: {{ $getResume->nm_pasien }}</td>
                            <td width="110px">No. Rekam Medis</td>
                            <td width="160px">: {{ $getResume->no_rkm_medis }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Umur</td>
                            @php
                                $tanggal_lahir_obj = date_create($getResume->tgl_lahir);
                                $today = date_create('today');
                                $umur = date_diff($tanggal_lahir_obj, $today);
                                $umur_text =
                                    $umur->y == 0 ? $umur->m . ' Bulan' : $umur->y . ' Tahun, ' . $umur->m . ' Bulan';
                            @endphp
                            <td>: {{ $umur_text }}</td>
                            <td>Ruang</td>
                            <td>:
                                @if ($getKamarInap)
                                    {{ $getKamarInap->kd_kamar }} |
                                    {{ $getKamarInap->nm_bangsal }}
                                @endif
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Tgl Lahir</td>
                            <td>:
                                {{ date('d-m-Y', strtotime($getResume->tgl_lahir)) }}
                            </td>
                            <td>Jenis Kelamin</td>
                            @php
                                $jnsKelamin = $getResume->jenis_kelamin == 'P' ? 'Perempuan' : 'Laki-laki';
                            @endphp
                            <td>: {{ $jnsKelamin }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Pekerjaan</td>
                            <td>: {{ $getResume->pekerjaan }}</td>
                            <td>Tanggal Masuk</td>
                            <td>:
                                {{-- @if ($cekPasienKmrInap > 1) --}}
                                {{-- {{ date('d-m-Y', strtotime($getResume->tgl_registrasi)) }} --}}
                                {{-- @else --}}
                                {{ date('d-m-Y', strtotime($getResume->tgl_masuk)) }}
                                {{-- @endif --}}
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Alamat</td>
                            <td>: {{ $getResume->alamat }}</td>
                            <td>Tanggak Keluar</td>
                            <td>:
                                @if ($getKamarInap)
                                    {{ date('d-m-Y', strtotime($getKamarInap->tgl_keluar)) }}
                                @endif
                            </td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="200px" style="vertical-align: top;">Diagnosa Awal Masuk
                            </td>
                            <td width="350px" style="vertical-align: top;"> :
                                {{ $getResume->diagnosa_awal }}
                            </td>
                            <td width="130px"></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Alasan Masuk Dirawat
                            </td>
                            <td style="vertical-align: top;"> : {{ $getResume->alasan }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Keluhan Utama Riwayat
                                Penyakit
                            </td>
                            <td style="vertical-align: top;"> : {{ $getResume->keluhan_utama }}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Pemeriksaan Fisik</td>
                            <td witdth="364px" style="vertical-align: top;"> :
                                {{ $getResume->pemeriksaan_fisik }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Jalannya Penyakit
                                Selama
                                Perawatan
                            </td>
                            <td style="vertical-align: top;"> :
                                {{ $getResume->jalannya_penyakit }}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Pemeriksaan Penunjang
                                Radiologi
                                Terpenting
                            </td>
                            <td witdth="364" style="vertical-align: top;"> :
                                {{ $getResume->pemeriksaan_penunjang }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Pemeriksaan Penunjang
                                Laboratorium
                                Terpenting</td>
                            <td style="vertical-align: top;"> : {{ $getResume->hasil_laborat }}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Tindakan/Operasi
                                Selama
                                Perawatan
                            </td>
                            <td witdth="364px" style="vertical-align: top;"> :
                                {{ $getResume->tindakan_dan_operasi }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Obat-obatan Selama
                                Perawatan
                            </td>
                            <td witdth="364px" style="vertical-align: top;"> : {{ $getResume->obat_di_rs }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px">Diagnosa Akhir</td>
                            <td colspan="2"></td>
                            <td width="80px" class="text-center">Kode ICD</td>
                            <td width="20px"></td>
                        </tr>
                        <tr>
                            <td width="250px">&nbsp; - Diagnosa Utama </td>
                            <td>: {{ $getResume->diagnosa_utama }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_diagnosa_utama }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td>&nbsp; - Diagnosa Sekunder </td>
                            <td>: 1. {{ $getResume->diagnosa_sekunder }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>&nbsp; 2. {{ $getResume->diagnosa_sekunder2 }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder2 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>&nbsp; 3. {{ $getResume->diagnosa_sekunder3 }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder3 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>&nbsp; 4. {{ $getResume->diagnosa_sekunder4 }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder4 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td>&nbsp; - Prosedur/Tindakan Utama </td>
                            <td>: {{ $getResume->prosedur_utama }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->kd_prosedur_utama }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td>&nbsp; - Prosedur/Tindakan Sekunder </td>
                            <td>: 1. {{ $getResume->prosedur_sekunder }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->prosedur_sekunder }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>&nbsp; 2. {{ $getResume->prosedur_sekunder2 }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->kd_prosedur_sekunder2 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>&nbsp; 3. {{ $getResume->prosedur_sekunder3 }}</td>
                            <td class="text-right">(</td>
                            <td class="text-center">
                                {{ $getResume->kd_prosedur_sekunder3 }}
                            </td>
                            <td>)</td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px">Alergi / Reaksi Obat</td>
                            <td witdth="364px">: {{ $getResume->alergi }}</td>
                            <td width="86px"></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Diet Selama Perawatan
                            </td>
                            <td style="vertical-align: top;">: {{ $getResume->diet }}</td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Hasil Lab Yang Belum
                                Selesai
                                (Pending)
                            </td>
                            <td witdth="364px" style="vertical-align: top;">: {{ $getResume->lab_belum }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Instruksi/Anjuran Dan
                                Edukasi
                                (Follow
                                Up)
                            </td>
                            <td witdth="364px" style="vertical-align: top;">: {{ $getResume->edukasi }}</td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="115px">Keadaan Pulang</td>
                            <td width="300px">: {{ $getResume->keadaan }}</td>
                            <td width="110px">Cara Keluar</td>
                            <td width="160px">: {{ $getResume->cara_keluar }}</td>
                        </tr>
                        <tr>
                            <td>Dilanjutkan</td>
                            <td>: {{ $getResume->dilanjutkan }}</td>
                            <td>Tanggal Kontrol</td>
                            <td>:
                                {{ date('d-m-Y h:i', strtotime($getResume->kontrol)) }}
                            </td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Obat-obatan waktu
                                pulang
                            </td>
                            <td witdth="364px" style="vertical-align: top;">: {{ $getResume->obat_pulang }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            @if (count($getResume->dpjp_ranap) > 0)
                            @else
                                <td width="250px" class="text-center">
                                </td>
                            @endif
                            @foreach ($getResume->dpjp_ranap as $dpjp)
                                <td width="250px" class="text-center">
                                    Dokter Penanggung Jawab2
                                    <div class="barcode mt-1">
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $dpjp->nm_dokter . ' ID ' . $dpjp->kd_dokter . ' ' . $getKamarInap->tgl_keluar, 'QRCODE') }}"
                                            alt="barcode" width="55px" height="55px"/>
                                    </div>
                                    {{ $dpjp->nm_dokter }}
                                </td>
                            @endforeach
                            <td width="250px" class="text-center">
                                Dokter Penanggung Jawab
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getResume->nm_dokter . ' ID ' . $getResume->kd_dokter . ' ' . $getResume->tgl_keluar, 'QRCODE') }}"
                                        alt="barcode" width="55px" height="55px" />
                                </div>
                                {{ $getResume->nm_dokter }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            {{-- BERKAS RESUME RALAN --}}
            <div class="card-body">
                <div class=""">
                    <table width="700px">
                        <tr>
                            <td rowspan="3" width="90"> <img
                                    src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="80"
                                    height="80"></td>
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
                            <td colspan="2">
                                <h3 class="mt-1"><b>RESUME MEDIS PASIEN</b></h3>
                            </td>
                        </tr>
                    </table>
                    <table width="700px">
                        <tr style="vertical-align: top;">
                            <td width="115px">Nama Pasien</td>
                            <td width="300px">: {{ $getResume->nm_pasien }}</td>
                            <td width="110px">No. Rekam Medis</td>
                            <td width="160px">: {{ $getResume->no_rkm_medis }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Umur</td>
                            @php
                                $tanggal_lahir_obj = date_create($getResume->tgl_lahir);
                                $today = date_create('today');
                                $umur = date_diff($tanggal_lahir_obj, $today);
                                $umur_text =
                                    $umur->y == 0 ? $umur->m . ' Bulan' : $umur->y . ' Tahun, ' . $umur->m . ' Bulan';
                            @endphp
                            <td>: {{ $umur_text }}</td>
                            <td>Ruang</td>
                            <td>: {{ $getResume->nm_poli }} </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Tgl Lahir</td>
                            <td>:
                                {{ date('d-m-Y', strtotime($getResume->tgl_lahir)) }}
                            </td>
                            <td>Jenis Kelamin</td>
                            @php
                                $jnsKelamin = $getResume->jk == 'P' ? 'Perempuan' : 'Laki-laki';
                            @endphp
                            <td>: {{ $jnsKelamin }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Pekerjaan</td>
                            <td>: {{ $getResume->pekerjaan }}</td>
                            <td>Tanggal Masuk</td>
                            <td>:
                                {{ date('d-m-Y', strtotime($getResume->tgl_registrasi)) }}
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Alamat</td>
                            <td>: {{ $getResume->almt_pj }}</td>
                            <td>Tanggak Keluar</td>
                            <td>:
                                {{ date('d-m-Y', strtotime($getResume->tgl_registrasi)) }}
                            </td>
                        </tr>
                    </table>
                    <table width="1000px" class="mt-1">

                        <tr>
                            <td width="250px" style="vertical-align: top;">Keluhan utama dari
                                riwayat
                                penyakit
                                yang positif</td>
                            <td width="350px" style="vertical-align: top;"> :
                                {{ $getResume->keluhan_utama }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="1000px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Jalannya Penyakit
                                Selama
                                Perawatan
                            </td>
                            <td width="350px" style="vertical-align: top;"> :
                                {{ $getResume->jalannya_penyakit }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="1000px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Pemeriksaan penunjang
                                yang
                                positif
                            </td>
                            <td width="350px" style="vertical-align: top;"> :
                                {{ $getResume->pemeriksaan_penunjang }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="1000px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Hasil laboratorium
                                yang
                                positif
                            </td>
                            <td width="350px" style="vertical-align: top;"> :
                                {{ $getResume->hasil_laborat }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                    </table>
                    <table width="1000px" class="mt-1">
                        <tr>
                            <td width="250px">Diagnosa Akhir</td>
                            <td colspan="2"></td>
                            <td width="80px" class="text-center">Kode ICD</td>
                            <td width="20px"></td>
                        </tr>
                        <tr>
                            <td width="250px">&nbsp; - Diagnosa Utama </td>
                            <td>: {{ $getResume->diagnosa_utama }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_diagnosa_utama }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px">&nbsp; - Diagnosa Sekunder </td>
                            <td>: 1. {{ $getResume->diagnosa_sekunder }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px"></td>
                            <td>&nbsp; 2. {{ $getResume->diagnosa_sekunder2 }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder2 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px"></td>
                            <td>&nbsp; 3. {{ $getResume->diagnosa_sekunder3 }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder3 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px"></td>
                            <td>&nbsp; 4. {{ $getResume->diagnosa_sekunder4 }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_diagnosa_sekunder4 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px">&nbsp; - Prosedur/Tindakan Utama </td>
                            <td>: {{ $getResume->prosedur_utama }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_prosedur_utama }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px">&nbsp; - Prosedur/Tindakan Sekunder </td>
                            <td>: 1. {{ $getResume->prosedur_sekunder }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->prosedur_sekunder }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px"></td>
                            <td>&nbsp; 2. {{ $getResume->prosedur_sekunder2 }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_prosedur_sekunder2 }}
                            </td>
                            <td>)</td>
                        </tr>
                        <tr>
                            <td width="250px"></td>
                            <td>&nbsp; 3. {{ $getResume->prosedur_sekunder3 }}</td>
                            <td width="20px" class="text-right">(</td>
                            <td width="80px" class="text-center">
                                {{ $getResume->kd_prosedur_sekunder3 }}
                            </td>
                            <td>)</td>
                        </tr>
                    </table>
                    <table width="1000px" class="mt-1">
                        <tr>
                            <td width="250px" style="vertical-align: top;">Kondisi pasien pulang
                            </td>
                            <td width="350px" style="vertical-align: top;"> :
                                {{ $getResume->kondisi_pulang }}
                            </td>
                            <td width="86px"></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Obat-obatan waktu
                                pulang/nasihat
                            </td>
                            <td style="vertical-align: top;"> : {{ $getResume->obat_pulang }}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="1000px" class="mt-1">
                        <tr>
                            <td width="250px" class="text-center">

                            </td>
                            <td width="150px"></td>
                            <td width="250px" class="text-center">
                                Dokter Penanggung Jawab2
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getResume->nm_dokter . ' ID ' . $getResume->kd_dokter . ' ' . $getResume->tgl_registrasi, 'QRCODE') }}"
                                        alt="barcode" width="60px" height="60px" />
                                </div>
                                {{ $getResume->nm_dokter }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    @endif
@else
    {{-- NULL --}}
@endif
