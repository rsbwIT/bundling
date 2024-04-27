<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .text-xs {
            font-size: 8px;
            /* Adjust font size for paragraphs */
        }

        .h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .h4 {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            /* Adjust font size for tables */
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 {
            margin-top: 10px;
        }

        .mt-0 {
            margin-top: 0px;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .mx-1 {
            margin: 5px 8px;
        }

        .card-body {
            page-break-after: always;
        }

        .pb-4{
            padding-bottom: 30px;
        }

        .card-body:last-child {
            page-break-after: auto;
        }
    </style>

<body>
    @if ($jumlahData > 0)
        {{-- BERKAS SEP ============================================================= --}}
        @if ($getSEP)
            <div class="card-body">
                <div class="card p-4 d-flex justify-content-center align-items-center">
                    <table width="700px">
                        <thead>
                            <tr>
                                <th rowspan="2" width="150px"><img src="{{ public_path('img/bpjs.png') }}"
                                        width="150px" class="" alt="">
                                </th>
                                <th class="text-center">
                                    <span class="h3">SURAT ELEGIBILITAS PESERTA</span>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">
                                    <span class="h3">{{ $getSetting->nama_instansi }}</span>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($getSEP->no_sep, 'C39+') }}"
                                        alt="barcode" width="200px" height="30px" />
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <table width="700px">
                        <tr>
                            <td width="144px">No. SEP</td>
                            <td width="250px">: {{ $getSEP->no_sep }}</td>
                            <td width="144px">No. Rawat</td>
                            <td width="150px">: {{ $getSEP->no_rawat }}</td>
                        </tr>
                        <tr>
                            <td>Tgl. SEP</td>
                            <td>: {{ date('d/m/Y', strtotime($getSEP->tglsep)) }}</td>
                            <td>No. Reg</td>
                            <td>: {{ $getSEP->no_reg }}</td>
                        </tr>
                        <tr>
                            <td>No. Kartu</td>
                            <td>: {{ $getSEP->no_kartu }} (MR: {{ $getSEP->nomr }})</td>
                            <td>Peserta</td>
                            <td>: {{ $getSEP->peserta }}</td>
                        </tr>
                        <tr>
                            <td>Nama Peserta</td>
                            <td>: {{ $getSEP->nama_pasien }}</td>
                            <td>Jns Rawat</td>
                            @php
                                $jnsRawat = $getSEP->jnspelayanan == '1' ? 'Rawat Inap' : 'Rawat Jalan';
                            @endphp
                            <td>: {{ $jnsRawat }}
                            </td>
                        </tr>
                        <tr>
                            @php
                                $jnsKunjungan =
                                    $getSEP->tujuankunjungan == 0
                                        ? '-Konsultasi dokter(Pertama)'
                                        : 'Kunjungan Kontrol(ulangan)';
                            @endphp
                            <td>Tgl. Lahir</td>
                            <td>: {{ date('d/m/Y', strtotime($getSEP->tanggal_lahir)) }}
                            </td>
                            <td>Jns. Kunjungan</td>
                            <td class="text-xs">: {{ $jnsKunjungan }}</td>
                        </tr>
                        <tr>
                            @php
                                $Prosedur =
                                    $getSEP->flagprosedur == 0
                                        ? '-Prosedur Tidak Berkelanjutan'
                                        : ($getSEP->flagprosedur == 1
                                            ? '- Prosedur dan Terapi Tidak Berkelanjutan'
                                            : '');
                            @endphp
                            <td style="vertical-align: top;">No. Telpon</td>
                            <td style="vertical-align: top;">: {{ $getSEP->notelep }}</td>
                            <td></td>
                            <td class="text-xs">{{ $Prosedur }}</td>
                        </tr>
                        <tr>
                            <td>Sub/Spesialis</td>
                            <td>: {{ $getSEP->nmpolitujuan }}</td>
                            <td>Poli Perujuk</td>
                            <td>: -</td>
                        </tr>
                        <tr>
                            <td>Dokter</td>
                            <td>: {{ $getSEP->nmdpdjp }}</td>
                            <td>Kls. Hak</td>
                            <td>: Kelas {{ $getSEP->klsrawat }}</td>
                        </tr>
                        <tr>
                            <td>Fasker Perujuk</td>
                            <td>: {{ $getSEP->nmppkrujukan }}</td>
                            <td>Kls. Rawat</td>
                            <td>: {{ $getSEP->klsrawat }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Diagnosa Awal</td>
                            <td>: {{ $getSEP->nmdiagnosaawal }}</td>
                            <td style="vertical-align: top;">Penjamin</td>
                            <td style="vertical-align: top;">: BPJS Kesehatan</td>
                        </tr>
                        <tr>
                            <td>Catatan</td>
                            <td>: {{ $getSEP->catatan }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px">
                        <tr>
                            <td width="473px" class="text-xs">
                                *Saya Menyetujui BPJS Kesehatan Menggunakan Informasi Medis Pasien jika
                                diperlukan.
                                <br>
                                *SEP bukan sebagai bukti penjamin peserta <br>
                                Catatan Ke 1 {{ date('Y-m-d H:i:s') }}

                            </td>
                            <td class="text-center" width="220px">
                                <p>Pasien/Keluarga Pasien </p>
                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSEP->nmppkpelayanan . ',' . ' Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSEP->nama_pasien . ' ID ' . $getSEP->no_kartu . ' ' . $getSEP->tglsep, 'QRCODE') }}"
                                        alt="barcode" width="55px" height="55px" />
                                </div>
                                <p><b>{{ $getSEP->nama_pasien }}</b></p>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        @else
            {{-- NULL --}}
        @endif

        {{-- RESUME PASIEN ============================================================= --}}
        @if ($getResume && $statusLanjut)
            @if ($statusLanjut->kd_poli === 'U0061' || $statusLanjut->kd_poli === 'FIS')
                <div class="card-body">
                    <div class="card p-4 d-flex justify-content-center align-items-center">
                        <table width="700px">
                            <tr>
                                <td rowspan="3"> <img
                                        src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
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
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh dr. Sanjoto Santibudi, Sp.KFR ID ' . $getResume->kd_dokter . ' ' . $getResume->tgl_registrasi, 'QRCODE') }}"
                                            alt="barcode" width="55px" height="55px" />
                                    </div>
                                    dr. Sanjoto Santibudi, Sp.KFR
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
                                            src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                            width="80" height="80"></td>
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
                                            $umur->y == 0
                                                ? $umur->m . ' Bulan'
                                                : $umur->y . ' Tahun, ' . $umur->m . ' Bulan';
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
                                    <td width="250px" class="text-center">
                                        Dokter Penanggung Jawab
                                        <div class="barcode mt-1">
                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getResume->nm_dokter . ' ID ' . $getResume->kd_dokter . ' ' . $getResume->tgl_keluar, 'QRCODE') }}"
                                                alt="barcode" width="55px" height="55px" />
                                        </div>
                                        {{ $getResume->nm_dokter }}
                                    </td>
                                    <td width="150px"></td>
                                    <td width="250px" class="text-center">
                                        Dokter Penanggung Jawab2
                                        <div class="barcode mt-1">
                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getResume->nm_dokter . ' ID ' . $getResume->kd_dokter . ' ' . $getResume->tgl_keluar, 'QRCODE') }}"
                                                alt="barcode" width="60px" height="60px" />
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
                                            src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                            width="80" height="80"></td>
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
                                            $umur->y == 0
                                                ? $umur->m . ' Bulan'
                                                : $umur->y . ' Tahun, ' . $umur->m . ' Bulan';
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

        {{-- RIANCIAN BIAYA ============================================================= --}}
        @if ($bilingRalan)
            <div class="card-body">
                <div class="">
                    <table width="700px">
                        <tr>
                            <td rowspan="4"> <img
                                    src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                    width="80" height="80"></td>
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
                            @php
                                $jnsRawatNota = $statusLanjut->status_lanjut == 'Ranap' ? 'RAWAT INAP' : 'RAWAT JALAN';
                            @endphp
                            <td> RIANCIAN BIAYA {{ $jnsRawatNota }}</td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        @php
                            $totalBiaya = 0;
                        @endphp
                        @foreach ($bilingRalan as $item)
                            <tr>
                                <td width="150px">{{ $item->no }}</td>
                                <td width="300px">{{ $item->nm_perawatan }}</td>
                                <td width="70px">
                                    @if ($item->biaya == 0)
                                        {{-- Display an empty cell --}}
                                    @else
                                        {{ number_format($item->biaya, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td width="50px">
                                    @if ($item->jumlah == 0)
                                        {{-- Display an empty cell --}}
                                    @else
                                        {{ $item->jumlah }}
                                    @endif
                                </td>
                                <td width="70px">
                                    @if ($item->totalbiaya == 0)
                                        {{-- Display an empty cell --}}
                                    @else
                                        {{ number_format($item->totalbiaya, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                            @php
                                $totalBiaya += $item->totalbiaya;
                            @endphp
                        @endforeach
                        <tr>
                            <td>TOTAL TAGIHAN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>{{ number_format($totalBiaya, 0, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td>PPN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>TAGIHAN + PPN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>{{ number_format($totalBiaya, 0, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td>DEPOSIT</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>EKSES</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>SISA PIUTANG</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>{{ number_format($totalBiaya, 0, ',', '.') }}</b></td>
                        </tr>

                    </table>
                </div>
            </div>
        @else
            {{-- NULL --}}
        @endif

        {{-- BERKAS LABORAT =============================================================  --}}
        @if ($getLaborat)
            @foreach ($getLaborat as $periksa)
                <div class="card-body">
                    <div class="">
                        <table width="700px">
                            <tr>
                                <td rowspan="4"> <img
                                        src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                        width="80" height="80">
                                </td>
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
                                <td> {{ $getSetting->email }}</td>
                            </tr>
                            <tr class="text-center">
                                <td colspan="">
                                    <h3 class="mt-2">HASIL PEMERIKSAAN LABORATORIUM</h3>
                                </td>
                            </tr>
                        </table>
                        <table width="700px">
                            <tr style="vertical-align: top;">
                                <td width="115px">No.RM</td>
                                <td width="300px">: {{ $periksa->no_rkm_medis }}</td>
                                <td width="110px">No.Rawat </td>
                                <td width="160px">: {{ $periksa->no_rawat }}</td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>Nama Pasien</td>
                                <td>: {{ $periksa->nm_pasien }}</td>

                                <td>Tgl. Periksa </td>
                                <td>:
                                    {{ date('d-m-Y', strtotime($periksa->tgl_periksa)) }}
                                </td>
                            </tr>
                            <tr style="vertical-align: top;">
                                <td>JK/Umur </td>
                                <td>: {{ $periksa->jk }} / {{ $periksa->umur }}
                                </td>

                                <td>Jam Periksa </td>
                                <td>: {{ $periksa->jam }}</td>
                                </td>
                            </tr>

                            <tr style="vertical-align: top;">
                                <td>Alamat </td>
                                <td>: {{ $periksa->alamat }}</td>
                                @if ($statusLanjut->status_lanjut == 'Ranap')
                                    <td width="130px">Kamar </td>
                                    <td width="200px">: {{ $periksa->nm_bangsal }}</td>
                                @else
                                    <td width="130px">Poli </td>
                                    <td width="200px">: {{ $periksa->nm_poli }}</td>
                                @endif
                            </tr>
                            <tr style="vertical-align: top;">
                                <td> Dokter Pengirim </td>
                                <td>: {{ $periksa->nm_dokter_pj }} </td>
                                <td></td>
                                <td> </td>
                            </tr>
                        </table>
                        <table border="1px" width="700px" class="mt-1">
                            <tr>
                                <td width="152px" class="text-center">Pemeriksaan</td>
                                <td width="152px" class="text-center">Hasil</td>
                                <td width="100px" class="text-center">Satuan</td>
                                <td width="142px" class="text-center">Nilai Rujukan</td>
                                <td width="140px" class="text-center">Keterangan</td>
                            </tr>
                            @foreach ($periksa->getPeriksaLab as $perawatan)
                                <tr>
                                    <td colspan="5">- {{ $perawatan->nm_perawatan }}</td>
                                </tr>
                                @foreach ($perawatan->getDetailLab as $detail)
                                    <tr>
                                        <td> &emsp;{{ $detail->Pemeriksaan }}</td>
                                        <td class="text-center">
                                            {{ $detail->nilai }}
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->satuan }}
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->nilai_rujukan }}
                                        </td>
                                        <td>{{ $detail->keterangan }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>

                        <table width="700px" class="mt-1">
                            <tr>
                                <td class="text-xs"><b>Catatan :</b> Jika ada keragu-raguan
                                    pemeriksaan,
                                    diharapkan
                                    segera menghubungi laboratorium</td>
                            </tr>
                        </table>

                        <table width="700px">
                            <tr>
                                <td width="250px" class="text-center">
                                    Penanggung Jawab
                                    <div class="barcode mt-1">
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $periksa->nm_dokter . ' ID ' . $periksa->kd_dokter . ' ' . $periksa->tgl_periksa, 'QRCODE') }}"
                                            alt="barcode" width="60px" height="60px" />
                                    </div>
                                    {{ $periksa->nm_dokter }}
                                </td>
                                <td width="150px"></td>
                                <td width="250px" class="text-center">
                                    Hasil : {{ date('d-m-Y', strtotime($periksa->tgl_periksa)) }}
                                    <br>
                                    Petugas Laboratorium
                                    <div class="barcode mt-1">
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $periksa->nama_petugas . ' ID ' . $periksa->nip . ' ' . $periksa->tgl_periksa, 'QRCODE') }}"
                                            alt="barcode" width="60px" height="60px" />
                                    </div>
                                    {{ $periksa->nama_petugas }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            {{-- NULL --}}
        @endif

        {{-- BERKSA RADIOLOGI =============================================================  --}}
        @if ($getRadiologi)
            @foreach ($getRadiologi as $item)
                <div class="card-body">
                    <table width="700px">
                        <tr>
                            <td rowspan="4"> <img
                                    src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
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
                                <h3 class="mt-2">HASIL PEMERIKSAAN RADIOLOGI</h3>
                            </td>
                        </tr>
                    </table>
                    <table width="700px">
                        <tr style="vertical-align: top;">
                            <td width="115px">No.RM</td>
                            <td width="300px">: {{ $item->no_rkm_medis }}</td>
                            <td width="110px">Penanggung Jawab</td>
                            <td width="160px">: {{ $item->nm_dokter_pj }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Nama Pasien</td>
                            <td>: {{ $item->nm_pasien }}</td>
                            <td>Dokter Pengirim</td>
                            <td>: {{ $item->nm_dokter }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>JK/Umur </td>
                            <td>: {{ $item->jk }} | {{ $item->umur }}</td>
                            <td>Tgl.Pemeriksaan</td>
                            <td>:
                                {{ date('d-m-Y', strtotime($item->tgl_periksa)) }}
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Alamat </td>
                            <td>: {{ $item->alamat }}</td>
                            <td>Jam Pemeriksaan</td>
                            <td>: {{ $item->jam }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>No.Periksa </td>
                            <td>: {{ $item->no_rawat }}</td>
                            @if ($statusLanjut->status_lanjut == 'Ranap')
                                <td width="130px">Kamar </td>
                                <td width="200px">: {{ $item->kd_kamar }} |
                                    {{ $item->nm_bangsal }}
                                </td>
                            @else
                                <td width="130px">Poli </td>
                                <td width="200px">: {{ $item->nm_poli }}
                                </td>
                            @endif
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Pemeriksaan </td>
                            <td>: {{ $item->nm_perawatan }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="500px">
                                {!! nl2br(e($item->hasil)) !!}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px" class="mt-1">
                        <tr>
                            <td width="250px" class="text-center">
                                Penanggung Jawab
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->nm_dokter_pj . ' ID ' . $item->kd_dokter_pj . ' ' . $item->tgl_periksa, 'QRCODE') }}"
                                        alt="barcode" width="60px" height="60px" />
                                </div>
                                {{ $item->nm_dokter_pj }}
                            </td>
                            <td width="150px"></td>
                            <td width="250px" class="text-center">
                                Periksa : {{ date('d-m-Y', strtotime($item->tgl_periksa)) }} <br>
                                Petugas Laboratorium
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->nama_pegawai . ' ID ' . $item->nip . ' ' . $item->tgl_periksa, 'QRCODE') }}"
                                        alt="barcode" width="60px" height="60px" />
                                </div>
                                {{ $item->nama_pegawai }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        @else
            {{-- NULL --}}
        @endif

        {{-- AWAL MEDIS ============================================================= --}}
        @if ($awalMedis)
            <div class="card-body">
                <table width="700px">
                    <tr>
                        <td rowspan="3"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                alt="Girl in a jacket" width="80" height="80"></td>
                        <td class="text-center">
                            <h3>{{ $getSetting->nama_instansi }} </h3>
                        </td>
                        <td width="150px"></td>
                    </tr>
                    <tr class="text-center">
                        <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                            {{ $getSetting->propinsi }}
                            {{ $getSetting->kontak }}</td>
                        <td width="150px"></td>
                    </tr>
                    <tr class="text-center">
                        <td> E-mail : {{ $getSetting->email }}</td>
                        <td width="150px"></td>
                    </tr>
                </table>
                <div style="border: 1px solid #000;">
                    <table width="700px">
                        <tr>
                            <td class="text-center" style="background-color: rgb(236, 230, 197)">
                                <h3>PENILAIAN AWAL MEDIS RAWAT INAP</h3>
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td width="101px"> No. RM</td>
                            <td width="174px">: {{ $awalMedis->no_rkm_medis }}</td>
                            <td width="89px"> Jenis Kelamin</td>
                            @php
                                $jenisKelamin = $awalMedis->jk == 'P' ? 'Perempuan' : 'Laki-laki';
                            @endphp
                            <td width="126px">: {{ $jenisKelamin }}</td>
                            <td width="78px"> Tanggal</td>
                            <td width="120px">: {{ date('d/m/Y h:i:s', strtotime($awalMedis->tanggal)) }}</td>
                        </tr>
                        <tr>
                            <td>Nama Pasien</td>
                            <td>: {{ $awalMedis->nm_pasien }}</td>
                            <td>Tanggal Lahir</td>
                            <td>: {{ date('d/m/Y', strtotime($awalMedis->tgl_lahir)) }}</td>
                            <td>Anamnesis</td>
                            <td>: {{ $awalMedis->anamnesis }}</td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td width="340px">
                                I. RIWAYAT KESEHATAN
                                <p>Keluhan Utama : {{ $awalMedis->keluhan_utama }}</p>
                            </td>
                            <td width="350px"></td>
                        </tr>
                    </table>
                    <table width="700px" class="" style="border-top: 1px solid #000;">
                        <tr>
                            <td width="340px" height="60px" style="vertical-align: top;">
                                Riwayat Penyakit Sekarang : {{ $awalMedis->rps }}
                            </td>
                            <td width="350px"></td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td width="340px" height="50px" style="vertical-align: top;">
                                Riwayat Penyakit Dahulu : {{ $awalMedis->rpd }}
                            </td>
                            <td width="350px" height="50px" style="vertical-align: top;">
                                Riwayat Penyakit dalam Keluarga : {{ $awalMedis->rpk }}
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td width="340px" height="50px" style="vertical-align: top;">
                                Riwayat Pengobatan : {{ $awalMedis->rpo }}
                            </td>
                            <td width="350px" height="50px" style="vertical-align: top;">
                                Riwayat Alergi : {{ $awalMedis->alergi }}
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td>II. PEMERIKSAAN FISIK</td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td width="101px">Keadaan Umum</td>
                            <td width="174px">: {{ $awalMedis->keadaan }}</td>
                            <td width="89px">Kesadaraan </td>
                            <td width="126px">: {{ $awalMedis->kesadaran }}</td>
                            <td width="78px">GCS(E,V,M)</td>
                            <td width="120px">: {{ $awalMedis->gcs }}</td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td class="text-center">
                                Tanda Vital :
                                TD : {{ $awalMedis->td }}mmHg
                                N :{{ $awalMedis->nadi }} x/m
                                R : {{ $awalMedis->rr }} x/m
                                S : {{ $awalMedis->suhu }}
                                SPO2 : {{ $awalMedis->spo }} %
                                BB : {{ $awalMedis->bb }} Kg
                                TB : {{ $awalMedis->tb }}cm
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td width="82px">Kepala</td>
                            <td width="93px">: {{ $awalMedis->kepala }}</td>
                            <td width="82px">Thoraks</td>
                            <td width="93px">: {{ $awalMedis->thoraks }}</td>
                            <td width="340px"></td>
                        </tr>
                        <tr>
                            <td>Mata</td>
                            <td>: {{ $awalMedis->mata }}</td>
                            <td>Abdomen</td>
                            <td>: {{ $awalMedis->abdomen }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Gigi & Mulut</td>
                            <td>: {{ $awalMedis->gigi }}</td>
                            <td>Genital & Anus</td>
                            <td>: {{ $awalMedis->genital }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Leher</td>
                            <td>: {{ $awalMedis->leher }}</td>
                            <td>Ekstremitas</td>
                            <td>: {{ $awalMedis->ekstremitas }}</td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td>III. STATUS LOKALIS</td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td height="170px" style="vertical-align: top;">{{ $awalMedis->ket_lokalis }}</td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td height="60px" style="vertical-align: top;">Keterangan : {{ $awalMedis->ket_fisik }}
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td>IV. PEMERIKSAAN PENUNJANG</td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr height="50px">
                            <td width="230px" style="vertical-align: top;">EKG : {{ $awalMedis->ekg }}</td>
                            <td width="230px" style="vertical-align: top;">Radiologi : {{ $awalMedis->rad }}</td>
                            <td width="230px" style="vertical-align: top;">Laboratorium : {{ $awalMedis->lab }}
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td>
                                V. DIAGNOSIS
                                <p>{{ $awalMedis->diagnosis }}</p>
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr>
                            <td>
                                VI. TATALAKSANA
                                <p>{{ $awalMedis->tata }}</p>
                            </td>
                        </tr>
                    </table>
                    <table width="700px" style="border-top: 1px solid #000;">
                        <tr class="text-center">
                            <td width="345px">Tanggal dan Jam </td>
                            <td width="345px">Nama Dokter dan Tanda Tangan </td>
                        </tr>
                        <tr class="text-center">
                            <td width="345px">{{ date('d/m/Y h:i:s', strtotime($awalMedis->tanggal)) }} WIB</td>
                            <td width="345px">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $awalMedis->nm_dokter . ' ID ' . $awalMedis->kd_dokter . ' ', 'QRCODE') }}"
                                    alt="barcode" width="70px" height="60px" />
                                <br>
                                {{ $awalMedis->nm_dokter }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            {{-- NULL --}}
        @endif

        {{-- SURAT KEMATIAN ===================================================== --}}
        @if ($getSudartKematian)
            <div class="">
                <table width="700px">
                    <tr>
                        <td rowspan="3"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
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
                <table width="700px" class="mt-0">
                    <tr class="text-center">
                        <td colspan="0">
                            <h3>SURAT KEMATIAN</h3>
                        </td>
                    </tr>
                </table>
                <table width="700px">
                    <tr>
                        <td width="60px">No.RM</td>
                        <td width="120px">: {{ $getSudartKematian->no_rkm_medis }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Lamp.</td>
                        <td>: </td>
                        <td></td>
                    </tr>
                </table>
                <table width="700px" class="mt-1">
                    <tr>
                        <td>Yang bertanda tangan di bawah ini menerangkan bahwa :</td>
                    </tr>
                </table>
                <table width="700px" class="mt-1">
                    <tr>
                        <td width="30px"></td>
                        <td width="80px">Nama </td>
                        <td>: {{ $getSudartKematian->nm_pasien }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Umur </td>
                        <td>: {{ $getSudartKematian->umur }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Alamat </td>
                        <td>: {{ $getSudartKematian->alamat }}</td>
                    </tr>
                </table>
                <table width="700px" class="mt-1">
                    <tr>
                        <td>Telah meninggal dunia pada &emsp;
                            <u>{{ date('d-m-Y', strtotime($getSudartKematian->tanggal)) }}</u> &emsp; Jam
                            &emsp; <u>{{ $getSudartKematian->jam }}</u> di {{ $getSetting->nama_instansi }}
                            dikarenakan
                            {{ $getSudartKematian->keterangan }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Demikian surat keterangan ini dibuat agar menjadikan maklum dan dapat sebagaimana mestinya
                        </td>
                    </tr>
                </table>
                <table width="700px" class="mt-1">
                    <tr>
                        <td width="250px" class="text-center">

                        </td>
                        <td width="150px"></td>
                        <td class="text-center" width="250px">
                            {{ $getSetting->kabupaten }},
                            {{ date('d-m-Y', strtotime($getSudartKematian->tanggal)) }}<br>
                            Dokter Pemeriksa <br>
                            <div class="barcode mt-1">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSudartKematian->nm_dokter . ' ID ' . $getSudartKematian->kd_dokter . ' ' . $getSudartKematian->tanggal, 'QRCODE') }}"
                                    alt="barcode" width="60px" height="60px" />
                            </div>
                            {{ $getSudartKematian->nm_dokter }}
                        </td>
                    </tr>
                </table>
            </div>
        @else
            {{-- NULL --}}
        @endif

        {{-- LAPORAN OPERASI ============================================================================== --}}
        {{-- @if ($getLaporanOprasi)
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
        @else --}}
            {{-- NULL --}}
        {{-- @endif --}}
        {{-- ERROR HANDLING ============================================================= --}}
    @else
        <div class="card-body">
            <div class="card p-4 d-flex justify-content-center align-items-center">

            </div>
        </div>
    @endif
</body>

</html>
