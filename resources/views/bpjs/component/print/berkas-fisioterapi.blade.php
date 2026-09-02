<div class="card-body">
    <style>
        .fisio-header-box { border:1px solid #000; padding:15px; margin-bottom:15px; }
        .fisio-table, .fisio-header-table { width:100%; border-collapse: collapse; margin-top:10px; table-layout: fixed; }
        .fisio-table th, .fisio-table td, .fisio-header-table td { padding:6px; border:1px solid #000; text-align:left; vertical-align:top; word-wrap: break-word; }
        .fisio-table th { background-color: #f0f0f0; }
        .ttd-img { height:50px; width:auto; display:block; margin:auto; }
        .footer-dokter { margin-top:30px; text-align:right; font-size:14px; }
        
        /* A4 Styling for Screen moved to cesmik.blade.php to avoid DOMPDF issues */
    </style>

    <div>
        {{-- KOP SURAT --}}
        <div class="kop" style="width:100%; text-align:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:20px;">
            <table style="width:100%; border:none; margin:0;">
                <tr>
                    <td style="width:120px; border:none; text-align:left; vertical-align:middle;">
                        @if(isset($getSetting) && $getSetting->logo)
                            <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" style="height:80px; width:auto; max-width:100%;">
                        @else
                            <img src="{{ public_path('img/bw2.png') }}" style="height:80px;">
                        @endif
                    </td>
                    <td style="border:none; text-align:center; vertical-align:middle;">
                        <div style="font-size:20px; font-weight:bold;">
                            {{ $getSetting->nama_instansi ?? 'NAMA INSTANSI' }}
                        </div>
                        <div style="font-size:13px; margin-top:2px;">
                            {{ $getSetting->alamat_instansi ?? '' }} <br>
                            {{ $getSetting->kabupaten ?? '' }}, {{ $getSetting->propinsi ?? '' }} <br>
                            Telp: {{ $getSetting->kontak ?? '-' }} — Email: {{ $getSetting->email ?? '-' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- DATA PASIEN --}}
        <div class="fisio-header-box">
            <table class="fisio-header-table" style="margin-top:0;">
                <tr><td style="width:20%;"><b>No. Rawat</b></td><td>{{ $getFisioData['first']->no_rawat }}</td></tr>
                <tr><td><b>No RM</b></td><td>{{ $getFisioData['first']->no_rkm_medis }}</td></tr>
                <tr><td><b>Nama Pasien</b></td><td>{{ $getFisioData['first']->nm_pasien }}</td></tr>
                <tr><td><b>Lembar</b></td><td>{{ $getFisioData['first']->lembar }}</td></tr>
            </table>
        </div>

        {{-- PROTOKOL TERAPI --}}
        <h3 style="margin: 5px 0 10px 0; font-size:16px;">Protokol Terapi</h3>
        <table style="width:100%; border-collapse: separate; border-spacing: 15px 0; margin-left:-15px; margin-bottom:20px;">
            <tr>
                <td style="border:1px solid #000; padding:10px; width:33.33%; font-size:13px; vertical-align:top;">
                    <b>Diagnosa:</b><br>{{ $getFisioData['first']->diagnosa }}
                </td>
                <td style="border:1px solid #000; padding:10px; width:33.33%; font-size:13px; vertical-align:top;">
                    <b>FT:</b><br>{{ $getFisioData['first']->ft }}
                </td>
                <td style="border:1px solid #000; padding:10px; width:33.33%; font-size:13px; vertical-align:top;">
                    <b>ST:</b><br>{{ $getFisioData['first']->st }}
                </td>
            </tr>
        </table>

        {{-- TABEL KUNJUNGAN --}}
        <h4 style="margin: 5px 0 10px 0; font-size:15px;">Data Kunjungan</h4>
        <table class="fisio-table">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:25%;">Program</th>
                    <th style="width:10%;">Tanggal</th>
                    <th style="width:20%;">TTD Pasien</th>
                    <th style="width:20%;">TTD Dokter</th>
                    <th style="width:20%;">TTD Terapis</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $namaDokter    = $getFisioData['dokterPJ']->nm_dokter ?? '-';
                    $kdDokter      = $getFisioData['dokterPJ']->kd_dokter ?? '-';
                    $tglRegistrasi = $statusLanjut->tgl_registrasi ?? now()->format('Y-m-d');
                @endphp

                @foreach ($getFisioData['data'] as $row)
                @if (trim($row->program) != '')
                <tr>
                    <td style="text-align:center;">{{ $row->kunjungan }}</td>
                    <td>{{ $row->program }}</td>
                    <td style="text-align:center;">
                        @if ($row->tanggal)
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
                        @else -
                        @endif
                    </td>

                    <td>
                        @php
                            $ttdPasienBase64 = '';
                            if ($row->ttd_pasien && file_exists(storage_path('app/public/ttd/'.$row->ttd_pasien))) {
                                $ttdPasienBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/ttd/'.$row->ttd_pasien)));
                            }
                        @endphp
                        @if ($ttdPasienBase64)
                            <img src="{{ $ttdPasienBase64 }}" class="ttd-img">
                        @else - @endif
                    </td>

                    <td>
                        @php
                            $qrText = 'Dikeluarkan di '.$getSetting->nama_instansi.
                                      ', Kabupaten/Kota '.$getSetting->kabupaten.
                                      ' Ditandatangani secara elektronik oleh '.$namaDokter.
                                      ' ID '.$kdDokter.
                                      ' '.$tglRegistrasi;

                            $qrBase64 = DNS2D::getBarcodePNG($qrText, 'QRCODE');
                        @endphp

                        @php
                            $ttdDokterBase64 = '';
                            if ($row->ttd_dokter && file_exists(storage_path('app/public/qr_dokter/'.$row->ttd_dokter))) {
                                $ttdDokterBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/qr_dokter/'.$row->ttd_dokter)));
                            }
                        @endphp
                        @if ($ttdDokterBase64)
                            <img src="{{ $ttdDokterBase64 }}" class="ttd-img">
                        @else
                            <img src="data:image/png;base64,{{ $qrBase64 }}" class="ttd-img">
                        @endif
                    </td>

                    <td>
                        @php
                            $ttdTerapisBase64 = '';
                            if ($row->ttd_terapis && file_exists(storage_path('app/public/ttd/'.$row->ttd_terapis))) {
                                $ttdTerapisBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/ttd/'.$row->ttd_terapis)));
                            }
                        @endphp
                        @if ($ttdTerapisBase64)
                            <img src="{{ $ttdTerapisBase64 }}" class="ttd-img">
                        @else - @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>

        {{-- FOOTER --}}
        <table style="width:100%; border:none; margin-top:20px;">
            <tr>
                <td style="border:none; width:50%;"></td>
                <td style="border:none; text-align:right;">
                    <p style="margin-bottom:10px;">
                        <b>Tanggal Kunjungan Pertama:</b>
                        {{ \Carbon\Carbon::parse($getFisioData['tanggalPertama'])->format('d-m-Y') }}
                    </p>

                    <img src="data:image/png;base64,{{ $qrBase64 }}" style="width:100px; height:100px; display:inline-block; margin-right:3%;">

                    <div class="footer-dokter" style="margin-top:10px;">
                        <b>{{ $namaDokter }}</b>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
