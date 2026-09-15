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

                @if($pasien->status_lanjut == 'Ranap')
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

                @if($pasien->status_lanjut == 'Ranap')
                <div style="margin-bottom: 10px;">
                    <div>Tindakan dan operasi :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_tindakan_dan_operasi">{!! nl2br(e($resume->tindakan_dan_operasi ?? '-')) !!}</div>
                </div>
                
                <div style="margin-bottom: 10px;">
                    <div>Diagnosa Awal :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_diagnosa_awal">{!! nl2br(e($resume->diagnosa_awal ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Alasan Rawat :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_alasan">{!! nl2br(e($resume->alasan ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Obat di RS :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_obat_di_rs">{!! nl2br(e($resume->obat_di_rs ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Alergi :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_alergi">{!! nl2br(e($resume->alergi ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Diet :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_diet">{!! nl2br(e($resume->diet ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Laboratorium yang belum :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_lab_belum">{!! nl2br(e($resume->lab_belum ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Edukasi :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_edukasi">{!! nl2br(e($resume->edukasi ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Cara Keluar :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_cara_keluar">{!! nl2br(e($resume->cara_keluar ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Keterangan Keluar :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_ket_keluar">{!! nl2br(e($resume->ket_keluar ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Keadaan Pulang :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_keadaan">{!! nl2br(e($resume->keadaan ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Keterangan Keadaan :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_ket_keadaan">{!! nl2br(e($resume->ket_keadaan ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Perawatan Dilanjutkan :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_dilanjutkan">{!! nl2br(e($resume->dilanjutkan ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Keterangan Dilanjutkan :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_ket_dilanjutkan">{!! nl2br(e($resume->ket_dilanjutkan ?? '-')) !!}</div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div>Kontrol :</div>
                    <div style="padding-left: 15px; border-bottom: 1px dotted #ccc; outline: none;" contenteditable="true" id="edit_kontrol">{!! nl2br(e($resume->kontrol ?? '-')) !!}</div>
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
// Saat modal resume dibuka, update kode ICD dan isi nama jika belum ada
$('#modalLihatResume').on('show.bs.modal', function () {
    // Data diagnosa aktual dari DB (dirender saat page load, selalu fresh karena edit diagnosa = page reload)
    @php
        $diagnosaData = DB::table('diagnosa_pasien')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $pasien->no_rawat)
            ->orderBy('diagnosa_pasien.prioritas')
            ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
            ->get();
            
        $prosedurData = DB::table('prosedur_pasien')
            ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
            ->where('prosedur_pasien.no_rawat', $pasien->no_rawat)
            ->orderBy('prosedur_pasien.prioritas')
            ->select('prosedur_pasien.kode', 'icd9.deskripsi_panjang as deskripsi')
            ->get();
    @endphp
    var diagnosaData = {!! json_encode($diagnosaData) !!};
    var prosedurData = {!! json_encode($prosedurData) !!};

    var nameFields = [
        'edit_diagnosa_utama',
        'edit_diagnosa_sekunder',
        'edit_diagnosa_sekunder2',
        'edit_diagnosa_sekunder3',
        'edit_diagnosa_sekunder4'
    ];
    var kdFields = [
        'edit_kd_diagnosa_utama',
        'edit_kd_diagnosa_sekunder',
        'edit_kd_diagnosa_sekunder2',
        'edit_kd_diagnosa_sekunder3',
        'edit_kd_diagnosa_sekunder4'
    ];

    kdFields.forEach(function(kdId, idx) {
        var kdEl   = document.getElementById(kdId);
        var nameEl = document.getElementById(nameFields[idx]);
        var row    = diagnosaData[idx] || null;

        if (kdEl) {
            kdEl.innerText = row ? row.kd_penyakit : '';
        }
    });

    var procNameFields = [
        'edit_prosedur_utama',
        'edit_prosedur_sekunder',
        'edit_prosedur_sekunder2',
        'edit_prosedur_sekunder3'
    ];
    var procKdFields = [
        'edit_kd_prosedur_utama',
        'edit_kd_prosedur_sekunder',
        'edit_kd_prosedur_sekunder2',
        'edit_kd_prosedur_sekunder3'
    ];

    procKdFields.forEach(function(kdId, idx) {
        var kdEl   = document.getElementById(kdId);
        var nameEl = document.getElementById(procNameFields[idx]);
        var row    = prosedurData[idx] || null;

        if (kdEl) {
            kdEl.innerText = row ? row.kode : '';
        }
    });
});


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
    
    var ranapFields = [
        'diagnosa_awal', 'alasan', 'obat_di_rs', 'alergi', 'diet', 'lab_belum', 
        'edukasi', 'cara_keluar', 'ket_keluar', 'keadaan', 'ket_keadaan', 
        'dilanjutkan', 'ket_dilanjutkan', 'kontrol'
    ];
    
    ranapFields.forEach(function(field) {
        if (document.getElementById('edit_' + field)) {
            data[field] = document.getElementById('edit_' + field).innerText.trim();
        }
    });

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
            var errMsg = 'Terjadi kesalahan sistem';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            } else if (xhr.status == 419) {
                errMsg = 'Sesi Anda telah habis (419). Silakan refresh halaman dan coba lagi.';
            }
            Swal.fire('Error', errMsg, 'error');
        }
    });
}
</script>
@endif
