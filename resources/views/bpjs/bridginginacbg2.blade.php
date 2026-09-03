@extends('..layout.layoutDashboard')
@section('title','Bridging INACBG')

@section('konten')

<style>
.content-wrapper{
    background:#f4f6f9;
    padding:15px;
}

.eklaim-wrap{
    width:100%;
    font-size:12px;
    display:flex;
    justify-content:center;
    padding:0 20px;
    box-sizing:border-box;
}

.eklaim-card{
    width:100%;
    max-width:none;
    background:#fff;
    border-radius:8px;
    box-shadow:0 1px 3px rgba(0,0,0,.08);
    border:1px solid #e5e7eb;
    overflow:hidden;
}

.header-title{
    background:#ffffff;
    border-bottom:1px solid #e5e7eb;
    padding:14px 18px;
}

.header-title h5{
    margin:0;
    font-size:16px;
    font-weight:700;
    color:#374151;
}

.header-title span{
    color:#6b7280;
    font-size:12px;
}

.form-area{
    padding:15px;
}

.eklaim-table,
.tarif-grid{
    width:100%;
    border-collapse:collapse;
    margin-bottom:18px;
}

.eklaim-table td,
.tarif-grid td{
    border:1px solid #e5e7eb;
    padding:8px 10px;
    vertical-align:middle;
}

.label{
    width:220px;
    background:#f9fafb;
    font-weight:600;
    color:#374151;
}

.tarif-name{
    width:280px;
    background:#f9fafb;
    font-weight:600;
    color:#374151;
}

input,
select,
textarea{
    width:100%;
    border:1px solid #d1d5db;
    border-radius:5px;
    padding:6px 8px;
    font-size:12px;
    transition:.2s;
    background:#fff;
}

input:focus,
select:focus,
textarea:focus{
    border-color:#6b7280;
    outline:none;
    box-shadow:0 0 0 2px rgba(107,114,128,.08);
}

.section{
    background:#4b5563;
    color:#fff;
    padding:9px 12px;
    font-weight:600;
    border-radius:5px 5px 0 0;
    margin-top:10px;
    margin-bottom:0;
    font-size:13px;
}

.center-title{
    text-align:center;
    font-weight:700;
    padding:10px;
    background:#f3f4f6;
    border:1px solid #e5e7eb;
    border-bottom:none;
    color:#374151;
    font-size:13px;
}

.info-box{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:10px;
    margin-bottom:15px;
}

.info-item{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:6px;
    padding:10px;
}

.info-item small{
    display:block;
    color:#6b7280;
    margin-bottom:3px;
}

.info-item strong{
    color:#111827;
    font-size:13px;
}

.btn-area{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:20px;
}

.btn-eklaim{
    border:none;
    padding:9px 16px;
    border-radius:6px;
    color:#fff;
    cursor:pointer;
    font-size:12px;
    font-weight:600;
    transition:.2s;
}

.btn-eklaim:hover{
    opacity:.92;
}

.btn-primary{ background:#2563eb; }
.btn-success{ background:#059669; }
.btn-dark{ background:#374151; }

.readonly{
    background:#f9fafb;
}

.alert-eklaim{
    padding:12px 15px;
    border-radius:6px;
    margin-bottom:15px;
    font-size:12px;
    border:1px solid transparent;
}

.alert-success{
    background:#ecfdf5;
    color:#065f46;
    border-color:#a7f3d0;
}

.alert-danger{
    background:#fef2f2;
    color:#991b1b;
    border-color:#fecaca;
}

/* DESKTOP */
@media(min-width:992px){

    .eklaim-wrap{
        width:100%;
        max-width:100%;
    }

    .form-area{
        width:100%;
    }

    .eklaim-table{
        table-layout:fixed;
    }

    .eklaim-table td{
        word-wrap:break-word;
    }
}

/* TABLET */
@media(max-width:991px){

    .eklaim-table{
        display:block;
        overflow-x:auto;
        white-space:nowrap;
    }

    .tarif-grid{
        display:block;
        overflow-x:auto;
    }

    .info-box{
        grid-template-columns:repeat(2,1fr);
    }
}

/* MOBILE */
@media(max-width:768px){

    .content-wrapper{
        padding:10px;
    }

    .eklaim-wrap{
        padding:0;
    }

    .form-area{
        padding:10px;
    }

    .info-box{
        grid-template-columns:1fr;
    }

    /* JANGAN UBAH TD MENJADI BLOCK */
    .eklaim-table,
    .tarif-grid{
        display:block;
        overflow-x:auto;
        white-space:nowrap;
    }

    .eklaim-table td,
    .tarif-grid td{
        display:table-cell;
        min-width:150px;
    }

    .label{
        min-width:180px;
    }

    .btn-area{
        flex-direction:column;
    }

    .btn-eklaim{
        width:100%;
        text-align:center;
    }
}
</style>

{{-- <div class="content-wrapper"> --}}

    <div class="eklaim-wrap">
    <div class="eklaim-card">
        <div class="form-area">

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-eklaim">
            {{ session('success') }}
        </div>
    @endif

    {{-- ALERT ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger alert-eklaim">
            {{ session('error') }}
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-eklaim">
            <strong>Terjadi kesalahan :</strong>
            <ul style="margin:8px 0 0 18px;padding:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

            <div class="info-box">
                <div class="info-item">
                    <small>No Rawat</small>
                    <strong>{{ $pasien->no_rawat }}</strong>
                </div>

                <div class="info-item">
                    <small>Pasien</small>
                    <strong>{{ $pasien->nm_pasien }}</strong>
                </div>

                <div class="info-item">
                    <small>No RM</small>
                    <strong>{{ $pasien->no_rkm_medis }}</strong>
                </div>

                <div class="info-item">
                    <small>Poliklinik</small>
                    <strong>{{ $pasien->nm_poli }}</strong>
                </div>
            </div>

            <form method="POST" action="{{ route('bpjs.inacbg.simpan') }}">
                @csrf

                <input type="hidden" name="no_rawat" value="{{ $pasien->no_rawat }}">
                <input type="hidden" name="nokartu" value="{{ $pasien->no_peserta }}">
                <table class="eklaim-table">
                    <tr>
                        <td class="label">Coder INACBG</td>
                        <td>
                            <input type="text"
                                class="readonly"
                                value="{{ $coder->no_ik }} - {{ $coder->nama }}"
                                readonly>

                            <input type="hidden"
                                name="coder_nik"
                                value="{{ $coder->no_ik }}">
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="tgl_masuk" value="{{ $pasien->tgl_masuk ?? date('Y-m-d') }}">
                <input type="hidden" name="tgl_keluar" value="{{ $pasien->tgl_keluar ?? $pasien->tgl_registrasi }}">
                <input type="hidden" name="nama_dokter" value="{{ $pasien->nm_dokter }}">
                <!-- ================= TABLE PERTAMA ================= -->
                <table class="eklaim-table">
                    <tr>
                        <td class="label">Jaminan / Cara Bayar</td>
                        <td>{{ $pasien->png_jawab }}</td>

                        <td class="label">No Peserta</td>
                        <td>
                            <input type="text" class="readonly" value="{{ $pasien->no_peserta }}" readonly>
                        </td>

                        <td class="label">No SEP</td>
                        <td>
                            <input type="text" name="nosep" value="{{ old('nosep',$nosep) }}">
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Jenis Rawat</td>
                        <td>
                            {{ $pasien->status_lanjut == 'Ranap' ? 'Rawat Inap' : 'Jalan / Kelas Reguler' }}
                        </td>

                        <td class="label">Kelas Hak</td>
                        <td>{{ $pasien->kelas ?? '-' }}</td>

                        <td class="label">Umur</td>
                        <td>
                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->age }} Tahun
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Tanggal Rawat</td>
                        <td>
                            Masuk : {{ $pasien->tgl_masuk ?? $pasien->tgl_registrasi }}<br>
                            Pulang : {{ $pasien->tgl_keluar ?? $pasien->tgl_registrasi }}
                        </td>

                        <td class="label">Cara Masuk</td>
                        <td>Rujukan FKTP</td>

                        <td class="label">Cara Pulang</td>
                        <td>Atas Persetujuan Dokter</td>
                    </tr>

                    <tr>
                        <td class="label">DPJP</td>
                        <td>{{ $pasien->nm_dokter }}</td>

                        <td class="label">LOS</td>
                        <td>
                            @if($pasien->status_lanjut == 'Ranap')
                                {{
                                    \Carbon\Carbon::parse($pasien->tgl_masuk)
                                    ->diffInDays(\Carbon\Carbon::parse($pasien->tgl_keluar ?? now())) + 1
                                }} Hari
                            @else
                                1 Hari
                            @endif
                        </td>

                        <td class="label">Jenis Tarif</td>
                        <td>TARIF RS</td>
                    </tr>

                </table>

                <!-- ================= DATA TAMBAHAN ================= -->
                <div class="section">Data Tambahan INACBG</div>

                <table class="eklaim-table">
                    <tr><td class="label">Lama Hari Naik Kelas</td><td><input type="number" name="upgrade_class_los" value="0"></td></tr>
                    <tr><td class="label">Biaya Tambahan</td><td><input type="number" name="add_payment_pct" value="0"></td></tr>
                    <tr><td class="label">Berat Saat Lahir</td><td><input type="number" name="birth_weight" value="0"></td></tr>
                    <tr>
                        <td class="label">Sistole</td>
                        <td>
                            <input type="number"
                                name="sistole"
                                value="{{ old('sistole', $sistole ?? 120) }}"
                                required>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="label">Pasien TB</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <label style="margin: 0; display: flex; align-items: center; gap: 5px; cursor: pointer;">
                                    <input type="checkbox" id="is_pasien_tb" onchange="toggleSITB()" style="width: auto;"> Ya
                                </label>
                                
                                <div id="container_sitb" style="display: none; align-items: center; gap: 10px; flex: 1;">
                                    <input type="text" id="input_sitb" name="nomor_register_sitb" value="{{ old('nomor_register_sitb', $nomor_register_sitb ?? '') }}" style="width: 200px;" placeholder="Nomor Register">
                                    <button type="button" id="btn_validasi_sitb" onclick="tampilkanModalValidasiSITB()" style="padding: 4px 10px; border: 1px solid #ccc; background: #eee; border-radius: 4px; cursor: pointer;">Validasi</button>
                                    <button type="button" id="btn_batal_validasi_sitb" onclick="batalValidasiSITB()" style="padding: 4px 10px; border: 1px solid #dc3545; background: #dc3545; color: white; border-radius: 4px; cursor: pointer; display: none;">Batal Validasi</button>
                                    <span id="sitb_status" style="font-size: 13px;"></span>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Diastole</td>
                        <td>
                            <input type="number"
                                name="diastole"
                                value="{{ old('diastole', $diastole ?? 90) }}"
                                required>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Status Pulang</td>
                        <td>
                            <select name="discharge_status" required>
                                <option value="1" selected>Atas Persetujuan Dokter</option>
                                <option value="2">Dirujuk</option>
                                <option value="3">APS</option>
                                <option value="4">Meninggal</option>
                                <option value="5">Lain-lain</option>
                            </select>
                        </td>
                    </tr>

                    @if(
                        str_contains(strtolower($pasien->nm_poli), 'hemodialisa')
                        || str_contains(strtolower($pasien->nm_poli), 'hd')
                    )
                    <tr>
                        <td class="label">Penggunaan Dializer</td>
                        <td>
                            <label style="margin-right:20px;">
                                <input type="radio"
                                    name="dializer_single_use"
                                    value="0"
                                    checked
                                    style="width:auto;">
                                Multiple Use (reuse)
                            </label>

                            <label>
                                <input type="radio"
                                    name="dializer_single_use"
                                    value="1"
                                    style="width:auto;">
                                Single Use
                            </label>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td class="label">Diagnosa</td>
                        <td>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <textarea name="diagnosa" rows="3" style="flex: 1;">{{ $diagnosa }}</textarea>
                                <button type="button" class="btn-eklaim btn-dark" data-toggle="modal" data-target="#modalEditDiagnosa" onclick="$('#modalEditDiagnosa .nav-tabs a[href=\'#tabDiagnosa\']').tab('show')" style="white-space: nowrap;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Prosedur</td>
                        <td>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <textarea name="procedure" rows="3" style="flex: 1;">{{ $procedure }}</textarea>
                                <button type="button" class="btn-eklaim btn-dark" data-toggle="modal" data-target="#modalEditDiagnosa" onclick="$('#modalEditDiagnosa .nav-tabs a[href=\'#tabProsedur\']').tab('show')" style="white-space: nowrap;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                    </tr>

                    <tr>
                        <td class="label">Diagnosa INACBG</td>
                        <td>
                            <textarea name="diagnosainacbg" rows="3">{{ $diagnosainacbg }}</textarea>
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Prosedur INACBG</td>
                        <td>
                            <textarea name="procedureinacbg" rows="3">{{ $procedureinacbg }}</textarea>
                        </td>
                    </tr>
                </table>

                <!-- ================= TARIF ================= -->
                <div class="center-title">Tarif Rumah Sakit</div>

                <table class="tarif-grid">
                    <tr><td class="tarif-name">Biaya Prosedur Non Bedah</td><td><input type="number" name="prosedur_non_bedah" value="{{ $prosedur_non_bedah }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Prosedur Bedah</td><td><input type="number" name="prosedur_bedah" value="{{ $prosedur_bedah }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Konsultasi</td><td><input type="number" name="konsultasi" value="{{ $konsultasi }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Tenaga Ahli</td><td><input type="number" name="tenaga_ahli" value="{{ $tenaga_ahli }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Keperawatan</td><td><input type="number" name="keperawatan" value="{{ $keperawatan }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Penunjang</td><td><input type="number" name="penunjang" value="{{ $penunjang }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Radiologi</td><td><input type="number" name="radiologi" value="{{ $radiologi }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Laboratorium</td><td><input type="number" name="laboratorium" value="{{ $laboratorium }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Pelayanan Darah</td><td><input type="number" name="pelayanan_darah" value="{{ $pelayanan_darah }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Rehabilitasi</td><td><input type="number" name="rehabilitasi" value="{{ $rehabilitasi }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Kamar</td><td><input type="number" name="kamar" value="{{ $kamar }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Rawat Intensif</td><td><input type="number" name="rawat_intensif" value="{{ $rawat_intensif }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Obat</td><td><input type="number" name="obat" value="{{ $obat }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Obat Kronis</td><td><input type="number" name="obat_kronis" value="{{ $obat_kronis }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Obat Kemoterapi</td><td><input type="number" name="obat_kemoterapi" value="{{ $obat_kemoterapi }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Alkes</td><td><input type="number" name="alkes" value="{{ $alkes }}"></td></tr>
                    <tr><td class="tarif-name">Biaya BMHP</td><td><input type="number" name="bmhp" value="{{ $bmhp }}"></td></tr>
                    <tr><td class="tarif-name">Biaya Sewa Alat</td><td><input type="number" name="sewa_alat" value="{{ $sewa_alat }}"></td></tr>
                </table>

                <div class="btn-area d-flex flex-wrap" style="gap: 10px;">

                    <!-- Tombol Lihat Resume -->
                    @if($resume)
                    <button type="button" class="btn-eklaim btn-info" data-toggle="modal" data-target="#modalLihatResume" style="color:white;">
                        <i class="fas fa-file-medical"></i> Lihat Resume
                    </button>
                    @endif

                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn-eklaim btn-success">
                        <i class="fas fa-save"></i> Simpan & Final Klaim
                    </button>

                    <!-- Tombol Print Klaim -->
                    @if($status_kirim)
                        <a href="{{ url('/inacbg/print/'.$nosep) }}" target="_blank" class="btn-eklaim btn-danger d-flex align-items-center">
                            <i class="fas fa-file-pdf"></i>
                            <span style="margin:0 6px;">|</span>
                            <i class="fas fa-print"></i>
                            Print Klaim
                        </a>
                    @endif

                </div>


            </form>

        </div>
    </div>
</div>

<!-- MODAL LIHAT RESUME -->
@if($resume)
<div class="modal fade" id="modalLihatResume" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-medical"></i> Resume Medis Pasien
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow-y: auto; background: #fff; color: #000; font-family: Arial, sans-serif; font-size: 11px;">
                
                <!-- KOP SURAT -->
                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; position: relative;">
                    <!-- Logo -->
                    <div style="position: absolute; left: 10px; top: 0;">
                        @if(isset($getSetting) && $getSetting->logo)
                            <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="70" height="70">
                        @else
                            <i class="fas fa-hospital-alt fa-3x" style="color: #28a745;"></i>
                        @endif
                    </div>
                    <h4 style="margin: 0; font-weight: bold; font-size: 16px;">{{ $getSetting->nama_instansi ?? 'RS. BUMI WARAS' }}</h4>
                    <p style="margin: 0; font-size: 11px;">{{ $getSetting->alamat_instansi ?? 'Jln. Wolter Monginsidi No. 235' }}, {{ $getSetting->kabupaten ?? 'Bandar Lampung' }}, {{ $getSetting->propinsi ?? 'Lampung' }}</p>
                    <p style="margin: 0; font-size: 11px;">{{ $getSetting->kontak ?? '(0721) 254589' }}</p>
                    <p style="margin: 0; font-size: 11px;">E-mail : {{ $getSetting->email ?? 'www.rsbumiwaras.co.id' }}</p>
                </div>

                <div style="text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 15px;">RESUME MEDIS PASIEN</div>
                <hr style="border-top: 1px solid #000; margin: 5px 0;">

                <!-- DATA PASIEN -->
                <table style="width: 100%; font-size: 11px; margin-bottom: 10px;">
                    <tr>
                        <td width="15%">Nama Pasien</td><td width="2%">:</td><td width="33%">{{ $pasien->nm_pasien }}</td>
                        <td width="15%">No. Rekam Medis</td><td width="2%">:</td><td width="33%">{{ $pasien->no_rkm_medis }}</td>
                    </tr>
                    <tr>
                        <td>Umur</td><td>:</td><td>{{ $pasien->umurdaftar ?? '-' }} {{ $pasien->sttsumur ?? '-' }}</td>
                        <td>Ruang</td><td>:</td><td>{{ $pasien->status_lanjut == 'Ranap' ? ($pasien->kelas ?? '-') : ($pasien->nm_poli ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td>Tgl Lahir</td><td>:</td><td>{{ date('d-m-Y', strtotime($pasien->tgl_lahir)) }}</td>
                        <td>Jenis Kelamin</td><td>:</td><td>{{ $pasien->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td><td>:</td><td>{{ $pasien->pekerjaan ?? '-' }}</td>
                        <td>Tanggal Masuk</td><td>:</td><td>{{ date('d-m-Y', strtotime($pasien->tgl_registrasi)) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Alamat</td><td style="vertical-align: top;">:</td><td style="vertical-align: top;">{{ $pasien->alamat ?? '-' }}</td>
                        <td style="vertical-align: top;">Tanggal Keluar</td><td style="vertical-align: top;">:</td><td style="vertical-align: top;">{{ $pasien->tgl_keluar ? date('d-m-Y', strtotime($pasien->tgl_keluar)) : date('d-m-Y') }}</td>
                    </tr>
                </table>
                <hr style="border-top: 1px solid #000; margin: 5px 0 15px 0;">

                <!-- CLINICAL INFO -->
                <div style="margin-bottom: 10px;">
                    <div>Keluhan utama dari riwayat penyakit yang positif :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_keluhan_utama">{!! nl2br(e($resume->keluhan_utama ?? '-')) !!}</div>
                </div>

                @if(isset($resume->pemeriksaan_fisik))
                <div style="margin-bottom: 10px;">
                    <div>Pemeriksaan Fisik :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_pemeriksaan_fisik">{!! nl2br(e($resume->pemeriksaan_fisik ?? '-')) !!}</div>
                </div>
                @endif

                <div style="margin-bottom: 10px;">
                    <div>Jalannya penyakit selama perawatan :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_jalannya_penyakit">{!! nl2br(e($resume->jalannya_penyakit ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Pemeriksaan penunjang yang positif :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_pemeriksaan_penunjang">{!! nl2br(e($resume->pemeriksaan_penunjang ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Hasil laboratorium yang positif :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_hasil_laborat">{!! nl2br(e($resume->hasil_laborat ?? '-')) !!}</div>
                </div>

                @if(isset($resume->tindakan_dan_operasi))
                <div style="margin-bottom: 10px;">
                    <div>Tindakan dan operasi :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_tindakan_dan_operasi">{!! nl2br(e($resume->tindakan_dan_operasi ?? '-')) !!}</div>
                </div>
                @endif

                <!-- DIAGNOSA & PROSEDUR -->
                <table style="width: 100%; font-size: 11px; margin-top: 20px;">
                    <tr>
                        <td colspan="2" style="width: 85%;">Diagnosa Akhir :</td>
                        <td style="width: 15%; text-align: center;">Kode ICD</td>
                    </tr>
                    <tr>
                        <td style="width: 25%; padding-left: 10px;">- Diagnosa Utama</td>
                        <td style="width: 60%;"><span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_diagnosa_utama">{{ $resume->diagnosa_utama ?? '' }}</span></td>
                        <td style="width: 15%; white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_diagnosa_utama">{{ $resume->kd_diagnosa_utama ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">- Diagnosa Sekunder</td>
                        <td>1. <span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_diagnosa_sekunder">{{ $resume->diagnosa_sekunder ?? '' }}</span></td>
                        <td style="white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_diagnosa_sekunder">{{ $resume->kd_diagnosa_sekunder ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2. <span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_diagnosa_sekunder2">{{ $resume->diagnosa_sekunder2 ?? '' }}</span></td>
                        <td style="white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_diagnosa_sekunder2">{{ $resume->kd_diagnosa_sekunder2 ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>3. <span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_diagnosa_sekunder3">{{ $resume->diagnosa_sekunder3 ?? '' }}</span></td>
                        <td style="white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_diagnosa_sekunder3">{{ $resume->kd_diagnosa_sekunder3 ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>4. <span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_diagnosa_sekunder4">{{ $resume->diagnosa_sekunder4 ?? '' }}</span></td>
                        <td style="white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_diagnosa_sekunder4">{{ $resume->kd_diagnosa_sekunder4 ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px; padding-top: 10px;">- Prosedur/Tindakan Utama</td>
                        <td style="padding-top: 10px;"><span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_prosedur_utama">{{ $resume->prosedur_utama ?? '' }}</span></td>
                        <td style="padding-top: 10px; white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_prosedur_utama">{{ $resume->kd_prosedur_utama ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">- Prosedur/Tindakan Sekunder</td>
                        <td>1. <span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_prosedur_sekunder">{{ $resume->prosedur_sekunder ?? '' }}</span></td>
                        <td style="white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_prosedur_sekunder">{{ $resume->kd_prosedur_sekunder ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2. <span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_prosedur_sekunder2">{{ $resume->prosedur_sekunder2 ?? '' }}</span></td>
                        <td style="white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_prosedur_sekunder2">{{ $resume->kd_prosedur_sekunder2 ?? '' }}</span> )</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>3. <span style="display:inline-block; border-bottom: 1px dotted #ccc; outline: none; min-width: 90%;" contenteditable="true" id="edit_prosedur_sekunder3">{{ $resume->prosedur_sekunder3 ?? '' }}</span></td>
                        <td style="white-space: nowrap;">( <span style="display: inline-block; width: 45px; text-align: center; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kd_prosedur_sekunder3">{{ $resume->kd_prosedur_sekunder3 ?? '' }}</span> )</td>
                    </tr>
                </table>

                <!-- OTHERS -->
                <div style="margin-top: 15px;">
                    Kondisi pasien pulang : {{ $resume->kondisi_pulang ?? '-' }}
                </div>
                <div style="margin-top: 5px;">
                    Obat-obatan waktu pulang/nasihat :
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_obat_pulang">{!! nl2br(e($resume->obat_pulang ?? '-')) !!}</div>
                </div>

                <!-- TTD -->
                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <div style="text-align: center; width: 250px;">
                        <div>Dokter Penanggung Jawab</div>
                        <!-- Barcode QR Code -->
                        <div style="height: 65px; margin: 5px 0;">
                            @if(isset($getSetting) && isset($pasien))
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $pasien->nm_dokter . ' ID ' . $pasien->kd_dokter . ' ' . date('Y-m-d'), 'QRCODE') }}"
                                    alt="barcode" width="60px" height="60px" />
                            @else
                                <i class="fas fa-qrcode fa-3x" style="opacity: 0.2;"></i>
                            @endif
                        </div>
                        <div style="text-decoration: underline;">{{ $pasien->nm_dokter ?? 'Nama Dokter' }}</div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="simpanResume()">
                    <i class="fas fa-save"></i> Simpan Resume
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function simpanResume() {
    var btn = event.currentTarget;
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled = true;

    var data = {
        _token: '{{ csrf_token() }}',
        no_rawat: '{{ $pasien->no_rawat }}',
        keluhan_utama: document.getElementById('edit_keluhan_utama') ? document.getElementById('edit_keluhan_utama').innerText.trim() : '',
        jalannya_penyakit: document.getElementById('edit_jalannya_penyakit') ? document.getElementById('edit_jalannya_penyakit').innerText.trim() : '',
        pemeriksaan_penunjang: document.getElementById('edit_pemeriksaan_penunjang') ? document.getElementById('edit_pemeriksaan_penunjang').innerText.trim() : '',
        hasil_laborat: document.getElementById('edit_hasil_laborat') ? document.getElementById('edit_hasil_laborat').innerText.trim() : '',
        obat_pulang: document.getElementById('edit_obat_pulang') ? document.getElementById('edit_obat_pulang').innerText.trim() : '',
        
        diagnosa_utama: document.getElementById('edit_diagnosa_utama') ? document.getElementById('edit_diagnosa_utama').innerText.trim() : '',
        kd_diagnosa_utama: document.getElementById('edit_kd_diagnosa_utama') ? document.getElementById('edit_kd_diagnosa_utama').innerText.trim() : '',
        diagnosa_sekunder: document.getElementById('edit_diagnosa_sekunder') ? document.getElementById('edit_diagnosa_sekunder').innerText.trim() : '',
        kd_diagnosa_sekunder: document.getElementById('edit_kd_diagnosa_sekunder') ? document.getElementById('edit_kd_diagnosa_sekunder').innerText.trim() : '',
        diagnosa_sekunder2: document.getElementById('edit_diagnosa_sekunder2') ? document.getElementById('edit_diagnosa_sekunder2').innerText.trim() : '',
        kd_diagnosa_sekunder2: document.getElementById('edit_kd_diagnosa_sekunder2') ? document.getElementById('edit_kd_diagnosa_sekunder2').innerText.trim() : '',
        diagnosa_sekunder3: document.getElementById('edit_diagnosa_sekunder3') ? document.getElementById('edit_diagnosa_sekunder3').innerText.trim() : '',
        kd_diagnosa_sekunder3: document.getElementById('edit_kd_diagnosa_sekunder3') ? document.getElementById('edit_kd_diagnosa_sekunder3').innerText.trim() : '',
        diagnosa_sekunder4: document.getElementById('edit_diagnosa_sekunder4') ? document.getElementById('edit_diagnosa_sekunder4').innerText.trim() : '',
        kd_diagnosa_sekunder4: document.getElementById('edit_kd_diagnosa_sekunder4') ? document.getElementById('edit_kd_diagnosa_sekunder4').innerText.trim() : '',
        
        prosedur_utama: document.getElementById('edit_prosedur_utama') ? document.getElementById('edit_prosedur_utama').innerText.trim() : '',
        kd_prosedur_utama: document.getElementById('edit_kd_prosedur_utama') ? document.getElementById('edit_kd_prosedur_utama').innerText.trim() : '',
        prosedur_sekunder: document.getElementById('edit_prosedur_sekunder') ? document.getElementById('edit_prosedur_sekunder').innerText.trim() : '',
        kd_prosedur_sekunder: document.getElementById('edit_kd_prosedur_sekunder') ? document.getElementById('edit_kd_prosedur_sekunder').innerText.trim() : '',
        prosedur_sekunder2: document.getElementById('edit_prosedur_sekunder2') ? document.getElementById('edit_prosedur_sekunder2').innerText.trim() : '',
        kd_prosedur_sekunder2: document.getElementById('edit_kd_prosedur_sekunder2') ? document.getElementById('edit_kd_prosedur_sekunder2').innerText.trim() : '',
        prosedur_sekunder3: document.getElementById('edit_prosedur_sekunder3') ? document.getElementById('edit_prosedur_sekunder3').innerText.trim() : '',
        kd_prosedur_sekunder3: document.getElementById('edit_kd_prosedur_sekunder3') ? document.getElementById('edit_kd_prosedur_sekunder3').innerText.trim() : ''
    };

    if (document.getElementById('edit_pemeriksaan_fisik')) {
        data.pemeriksaan_fisik = document.getElementById('edit_pemeriksaan_fisik').innerText.trim();
    }
    if (document.getElementById('edit_tindakan_dan_operasi')) {
        data.tindakan_dan_operasi = document.getElementById('edit_tindakan_dan_operasi').innerText.trim();
    }

    $.ajax({
        url: '{{ route("inacbg.updateResumeData") }}',
        type: 'POST',
        data: data,
        success: function(response) {
            btn.innerHTML = originalText;
            btn.disabled = false;
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Gagal', response.message, 'error');
            }
        },
        error: function(xhr) {
            btn.innerHTML = originalText;
            btn.disabled = false;
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
        }
    });
}
</script>
@endif

<div class="modal fade" id="modalEditDiagnosa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('bpjs.updateDiagnosa') }}" method="POST">
                @csrf
                <input type="hidden" name="no_rawat" value="{{ $pasien->no_rawat }}">
                
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Diagnosa & Prosedur</h5>
                </div>
                
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabDiagnosa">ICD-10 (Diagnosa)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabProsedur">ICD-9 (Prosedur)</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="tabDiagnosa">
                            <label>Kode Diagnosa:</label>
                            <textarea id="diag_input" name="diagnosa" class="form-control mb-2" rows="2">{{ implode('#', DB::table('diagnosa_pasien')->where('no_rawat', $pasien->no_rawat)->pluck('kd_penyakit')->toArray()) }}</textarea>
                            <input type="text" id="cariPenyakit" class="form-control mb-2" placeholder=" Cari nama penyakit ">
                            <div style="height: 250px; overflow-y: scroll; border: 1px solid #ddd;">
                                <table class="table table-sm table-bordered">
                                    <thead style="background:#f4f6f9; position:sticky; top:0;"><tr><th style="width:50px;">Pilih</th><th style="width:80px;">Kode</th><th>Nama Penyakit</th></tr></thead>
                                    <tbody id="bodyPenyakit"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tabProsedur">
                            <label>Kode Prosedur:</label>
                            <textarea id="proc_input" name="prosedur" class="form-control mb-2" rows="2">{{ implode('#', DB::table('prosedur_pasien')->where('no_rawat', $pasien->no_rawat)->pluck('kode')->toArray()) }}</textarea>
                            <input type="text" id="cariProsedur" class="form-control mb-2" placeholder=" Cari nama prosedur ">
                            <div style="height: 250px; overflow-y: scroll; border: 1px solid #ddd;">
                                <table class="table table-sm table-bordered">
                                    <thead style="background:#f4f6f9; position:sticky; top:0;"><tr><th style="width:50px;">Pilih</th><th style="width:80px;">Kode</th><th>Nama Prosedur</th></tr></thead>
                                    <tbody id="bodyProsedur"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Validasi SITB -->
<div class="modal fade" id="modalKonfirmasiSITB" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #333; color: white;">
                <h5 class="modal-title">Konfirmasi Validasi Register SITB</h5>
            </div>
            <div class="modal-body" style="padding: 0;">
                <table class="table table-bordered" style="margin: 0;">
                    <tr>
                        <td align="right" style="width: 35%; color: #666;">Nama</td>
                        <td id="sitb_modal_nama"></td>
                    </tr>
                    <tr>
                        <td align="right" style="color: #666;">NIK</td>
                        <td id="sitb_modal_nik"></td>
                    </tr>
                    <tr>
                        <td align="right" style="color: #666;">Jenis Kelamin</td>
                        <td id="sitb_modal_jk"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" style="justify-content: center; background: #f8f9fa;">
                <button type="button" class="btn btn-light" style="border: 1px solid #ccc; width: 120px;" onclick="validasiSITB()">Ya (Benar)</button>
                <button type="button" class="btn btn-light" style="border: 1px solid #ccc; width: 120px;" data-dismiss="modal">Tidak (Batal)</button>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Data Source (Langsung dari PHP ke JS)
    let dataPenyakit = [];
    let dataProsedur = [];

    // Load data secara asinkron di belakang layar agar tidak memberatkan HTML (Wussh!)
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/penyakit')
            .then(res => res.json())
            .then(data => dataPenyakit = data)
            .catch(e => console.error('Gagal memuat penyakit:', e));

        fetch('/api/icd9')
            .then(res => res.json())
            .then(data => dataProsedur = data)
            .catch(e => console.error('Gagal memuat prosedur:', e));
    });

    // 2. Fungsi Render Ringan (Limit 30 item saat tidak dicari)
    function render(data, bodyId, inputId, filter = "") {
        let selected = document.getElementById(inputId).value.split('#');
        let html = "";
        let count = 0;
        for (let item of data) {
            if (item.kd.toLowerCase().includes(filter) || item.nm.toLowerCase().includes(filter)) {
                html += `<tr>
                    <td class="text-center"><input type="checkbox" class="pilih" value="${item.kd}" ${selected.includes(item.kd) ? 'checked' : ''}></td>
                    <td>${item.kd}</td>
                    <td>${item.nm}</td>
                </tr>`;
                if (++count >= 30 && filter === "") break;
            }
        }
        document.getElementById(bodyId).innerHTML = html;
    }

    // 3. Inisialisasi
    render(dataPenyakit, 'bodyPenyakit', 'diag_input');
    render(dataProsedur, 'bodyProsedur', 'proc_input');


    // 4. Event Pencarian
    document.getElementById('cariPenyakit').addEventListener('keyup', e => render(dataPenyakit, 'bodyPenyakit', 'diag_input', e.target.value.toLowerCase()));
    document.getElementById('cariProsedur').addEventListener('keyup', e => render(dataProsedur, 'bodyProsedur', 'proc_input', e.target.value.toLowerCase()));

    // 5. Update Otomatis saat Checkbox diklik
    document.addEventListener('change', function(e) {
        if(e.target.classList.contains('pilih')) {
            let container = e.target.closest('.tab-pane');
            let input = container.querySelector('textarea');
            
            let val = e.target.value;
            let currentSelected = input.value ? input.value.split('#').filter(Boolean) : [];
            
            if (e.target.checked) {
                if (!currentSelected.includes(val)) {
                    currentSelected.push(val);
                }
            } else {
                currentSelected = currentSelected.filter(item => item !== val);
            }
            
            input.value = currentSelected.join('#');
        }
    });

    // Toggle SITB Input
    function toggleSITB() {
        let isTB = document.getElementById('is_pasien_tb').checked;
        let containerSITB = document.getElementById('container_sitb');
        let inputSITB = document.getElementById('input_sitb');

        if (isTB) {
            containerSITB.style.display = 'flex';
            inputSITB.required = true;
        } else {
            containerSITB.style.display = 'none';
            inputSITB.required = false;
            inputSITB.value = '';
            document.getElementById('sitb_status').innerHTML = '';
        }
    }

    // 5b. Tampilkan Modal Konfirmasi sebelum Validasi
    function tampilkanModalValidasiSITB() {
        // Ambil dari variabel Blade
        let nama = "{{ $pasien->nm_pasien ?? '-' }}";
        let nik = "{{ $pasien->no_ktp ?? '-' }}";
        let jk = "{{ isset($pasien->jk) ? ($pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}";

        document.getElementById('sitb_modal_nama').innerText = nama;
        document.getElementById('sitb_modal_nik').innerText = nik;
        document.getElementById('sitb_modal_jk').innerText = jk;

        $('#modalKonfirmasiSITB').modal('show');
    }

    // 6. Validasi SITB (Eksekusi setelah Ya Benar ditekan)
    function validasiSITB() {
        let sep = document.querySelector('input[name="nosep"]').value;
        let sitb = document.getElementById('input_sitb').value;
        let no_rawat = document.querySelector('input[name="no_rawat"]').value;
        let statusSpan = document.getElementById('sitb_status');
        let btn = document.getElementById('btn_validasi_sitb');
        let originalText = btn.innerHTML;

        if (!sep) {
            alert('Nomor SEP tidak ditemukan, pastikan SEP sudah tersimpan.');
            return;
        }
        if (!sitb) {
            alert('Nomor Register SITB harus diisi!');
            return;
        }

        $('#modalKonfirmasiSITB').modal('hide'); // Tutup modal saat proses
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        statusSpan.innerHTML = 'Memproses...';
        statusSpan.style.color = 'black';

        fetch('{{ route("bpjs.inacbg.sitbValidate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nomor_sep: sep,
                nomor_register_sitb: sitb,
                no_rawat: no_rawat
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (data.success) {
                console.log("Response Validasi SITB:", data);
                statusSpan.style.color = 'green';
                statusSpan.innerHTML = data.message;
                document.getElementById('btn_batal_validasi_sitb').style.display = 'inline-block';
            } else {
                statusSpan.style.color = 'red';
                statusSpan.innerHTML = data.message;
            }
        })
        .catch(err => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            statusSpan.style.color = 'red';
            statusSpan.innerHTML = 'Terjadi kesalahan server';
            console.error(err);
        });
    }

    // 7. Batal Validasi SITB
    function batalValidasiSITB() {
        let sep = document.querySelector('input[name="nosep"]').value;
        let statusSpan = document.getElementById('sitb_status');

        if (!sep) {
            alert('Nomor SEP harus diisi!');
            return;
        }

        if (!confirm('Apakah Anda yakin ingin membatalkan validasi SITB?')) {
            return;
        }

        let btn = document.getElementById('btn_batal_validasi_sitb');
        let originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch('{{ route("bpjs.inacbg.sitbInvalidate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nomor_sep: sep
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (data.success) {
                statusSpan.style.color = 'green';
                statusSpan.innerHTML = data.message;
                btn.style.display = 'none';
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(err => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Terjadi kesalahan pada server');
            console.error(err);
        });
    }

    // Panggil saat halaman diload untuk menyesuaikan state
    document.addEventListener("DOMContentLoaded", function() {
        if (document.getElementById('input_sitb').value !== '') {
            document.getElementById('is_pasien_tb').checked = true;
        }
        toggleSITB();
    });
</script>

@endsection