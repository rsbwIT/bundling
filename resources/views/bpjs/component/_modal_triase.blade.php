<!-- MODAL DATA TRIASE -->
<div class="modal fade" id="modalTriase" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#6f42c1;color:white;padding:10px 15px;">
                <h5 class="modal-title"><i class="fas fa-heartbeat"></i> Triase Pasien Gawat Darurat</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:white;"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="font-size:12px;padding:15px;">

                {{-- === HEADER INSTANSI (KOP SURAT) === --}}
                <div style="display:flex;align-items:center;border-bottom:2px solid #000;padding-bottom:8px;margin-bottom:8px;">
                    {{-- Logo kiri --}}
                    <div style="flex:0 0 80px;margin-right:15px;">
                        @if(isset($getSetting->logo) && $getSetting->logo)
                        <img src="data:image/jpeg;base64,{{ base64_encode($getSetting->logo) }}" style="width:80px;height:auto;">
                        @endif
                    </div>
                    {{-- Teks tengah --}}
                    <div style="flex:1;text-align:center;">
                        <div style="font-size:18px;font-weight:700;letter-spacing:1px;">{{ $getSetting->nama_instansi ?? '' }}</div>
                        <div style="font-size:11px;margin-top:2px;">{{ $getSetting->alamat_instansi ?? '' }} , {{ $getSetting->kabupaten ?? '' }}, {{ $getSetting->propinsi ?? '' }} {{ $getSetting->kontak ?? '' }}</div>
                        <div style="font-size:11px;">E-mail : {{ $getSetting->email ?? '' }}</div>
                    </div>
                </div>

                {{-- === JUDUL === --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
                    <tr>
                        <td colspan="4" style="background:#DAA520;color:#000;font-weight:700;text-align:center;border:1px solid #999;padding:5px;font-size:13px;letter-spacing:1px;">
                            TRIASE PASIEN GAWAT DARURAT
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:center;border:1px solid #999;padding:4px;font-style:italic;font-size:11px;">
                            Triase dilakukan segera setelah pasien datang dan sebelum pasien/ keluarga mendaftar di TPP IGD
                        </td>
                    </tr>
                </table>

                {{-- === DATA PASIEN === --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
                    <tr>
                        <td style="border:1px solid #999;padding:4px;width:20%;">Nama Pasien</td>
                        <td style="border:1px solid #999;padding:4px;width:30%;font-weight:600;">: {{ $infoPasienTriase->nm_pasien ?? $pasien->nm_pasien ?? '-' }}</td>
                        <td style="border:1px solid #999;padding:4px;width:20%;">No. Rekam Medis</td>
                        <td style="border:1px solid #999;padding:4px;width:30%;font-weight:600;">: {{ $pasien->no_rkm_medis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:4px;">Tanggal Lahir</td>
                        <td style="border:1px solid #999;padding:4px;">: {{ $infoPasienTriase->tgl_lahir ?? '-' }}</td>
                        <td style="border:1px solid #999;padding:4px;">Jenis Kelamin</td>
                        <td style="border:1px solid #999;padding:4px;">: {{ ($infoPasienTriase->jk ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:4px;">Tanggal Kunjungan</td>
                        <td style="border:1px solid #999;padding:4px;">: {{ $triase->tgl_kunjungan ? date('d-m-Y', strtotime($triase->tgl_kunjungan)) : '-' }}</td>
                        <td style="border:1px solid #999;padding:4px;">Pukul</td>
                        <td style="border:1px solid #999;padding:4px;">: {{ $triase->tgl_kunjungan ? date('H:i:s', strtotime($triase->tgl_kunjungan)) : '-' }}</td>
                    </tr>
                </table>

                {{-- === CARA DATANG & MACAM KASUS === --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
                    <tr>
                        <td style="border:1px solid #999;padding:4px;width:20%;">Cara Datang</td>
                        <td style="border:1px solid #999;padding:4px;">
                            : <input type="text" id="triase_cara_masuk" class="form-control form-control-sm d-inline-block" style="width:auto;display:inline!important;" value="{{ $triase->cara_masuk ?? '' }}">
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:4px;">Macam Kasus</td>
                        <td style="border:1px solid #999;padding:4px;">
                            : <select id="triase_kode_kasus" class="form-control form-control-sm d-inline-block" style="width:auto;display:inline!important;">
                                @foreach($masterKasus as $kasus)
                                <option value="{{ $kasus->kode_kasus }}" {{ ($triase->kode_kasus ?? '') == $kasus->kode_kasus ? 'selected' : '' }}>{{ $kasus->macam_kasus }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>

                {{-- === KETERANGAN & TRIASE SEKUNDER === --}}
                <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
                    <tr>
                        <td style="background:#DAA520;color:#000;font-weight:700;text-align:center;border:1px solid #999;padding:5px;width:30%;">KETERANGAN</td>
                        <td style="background:#DAA520;color:#000;font-weight:700;text-align:center;border:1px solid #999;padding:5px;">TRIASE SEKUNDER</td>
                    </tr>
                    @if($triaseSekunder)
                    <tr>
                        <td style="border:1px solid #999;padding:6px;vertical-align:top;font-weight:600;">ANAMNESA / KELUHAN UTAMA</td>
                        <td style="border:1px solid #999;padding:4px;">
                            <textarea id="triase_anamnesa_singkat" class="form-control form-control-sm" rows="4">{{ $triaseSekunder->anamnesa_singkat ?? '' }}</textarea>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="border:1px solid #999;padding:6px;vertical-align:top;font-weight:600;">TANDA VITAL</td>
                        <td style="border:1px solid #999;padding:4px;">
                            <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                                <span>Suhu (°C):</span><input type="number" step="0.1" id="triase_suhu" class="form-control form-control-sm" style="width:70px;" value="{{ $triase->suhu ?? '' }}">
                                <span>Nyeri:</span><input type="number" min="0" max="10" id="triase_nyeri" class="form-control form-control-sm" style="width:60px;" value="{{ $triase->nyeri ?? '' }}">
                                <span>Tensi:</span><input type="text" id="triase_tekanan_darah" class="form-control form-control-sm" style="width:90px;" value="{{ $triase->tekanan_darah ?? '' }}" placeholder="120/80">
                                <span>Nadi(/mnt):</span><input type="number" id="triase_nadi" class="form-control form-control-sm" style="width:70px;" value="{{ $triase->nadi ?? '' }}">
                                <span>Saturasi O²(%):</span><input type="number" id="triase_saturasi_o2" class="form-control form-control-sm" style="width:70px;" value="{{ $triase->saturasi_o2 ?? '' }}">
                                <span>Respirasi(/mnt):</span><input type="number" id="triase_pernapasan" class="form-control form-control-sm" style="width:70px;" value="{{ $triase->pernapasan ?? '' }}">
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- === PEMERIKSAAN / URGENSI (Triase Primer Skala) === --}}
                @if($triaseDetailSkala->count() > 0)
                <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
                    <tr>
                        <td style="background:#DAA520;color:#000;font-weight:700;text-align:center;border:1px solid #999;padding:5px;width:30%;">PEMERIKSAAN</td>
                        <td style="background:#DAA520;color:#000;font-weight:700;text-align:center;border:1px solid #999;padding:5px;">URGENSI</td>
                    </tr>
                    @foreach($triaseDetailSkala as $detail)
                    <tr>
                        <td style="border:1px solid #999;padding:5px;font-weight:600;">{{ $detail->nama_pemeriksaan }}</td>
                        <td style="border:1px solid #999;padding:5px;background:#FFFACD;">{{ $detail->urgensi }}</td>
                    </tr>
                    @endforeach
                </table>
                @endif

                {{-- === FOOTER PETUGAS === --}}
                @if($triaseSekunder)
                <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
                    <tr>
                        <td colspan="2" style="background:#FFFACD;text-align:center;border:1px solid #999;padding:4px;font-size:11px;">Petugas Triase Sekunder</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:4px;width:30%;">Tanggal &amp; Jam</td>
                        <td style="border:1px solid #999;padding:4px;">: {{ $triaseSekunder->tanggaltriase ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:4px;">Catatan</td>
                        <td style="border:1px solid #999;padding:4px;">
                            : <input type="text" id="triase_catatan" class="form-control form-control-sm d-inline-block" style="width:auto;display:inline!important;" value="{{ (isset($triaseSekunder->catatan) && $triaseSekunder->catatan != '-') ? $triaseSekunder->catatan : '' }}">
                        </td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #999;padding:4px;">Plan / Zona</td>
                        <td style="border:1px solid #999;padding:4px;">
                            : <input type="text" id="triase_plan" class="form-control form-control-sm d-inline-block" style="width:auto;display:inline!important;" value="{{ $triaseSekunder->plan ?? '' }}" placeholder="misal: Zona Kuning">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border:1px solid #999;padding:10px;text-align:right;">
                            @if(isset($petugasTriase) && $petugasTriase)
                            <div style="display:inline-block;text-align:center;">
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ \DNS2D::getBarcodePNG('Dikeluarkan di ' . ($getSetting->nama_instansi ?? '') . ', Kabupaten/Kota ' . ($getSetting->kabupaten ?? '') . ' Ditandatangani secara elektronik oleh ' . ($petugasTriase->nama ?? '') . ' ID ' . ($petugasTriase->nik ?? '') . ' ' . ($triaseSekunder->tanggaltriase ?? ''), 'QRCODE') }}" alt="barcode" width="80px" height="75px" />
                                </div>
                                <div style="margin-top:5px;font-weight:600;">{{ $petugasTriase->nama ?? '' }}</div>
                            </div>
                            @endif
                        </td>
                    </tr>
                </table>
                @endif

                {{-- Hidden inputs --}}
                <input type="hidden" id="triase_alat_transportasi" value="{{ $triase->alat_transportasi ?? '' }}">
                <input type="hidden" id="triase_alasan_kedatangan" value="{{ $triase->alasan_kedatangan ?? '' }}">
                <input type="hidden" id="triase_keterangan_kedatangan" value="{{ $triase->keterangan_kedatangan ?? '' }}">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnSimpanTriase" onclick="simpanTriase()">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function simpanTriase() {
    var btn = document.getElementById('btnSimpanTriase');
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled = true;

    var data = {
        _token: '{{ csrf_token() }}',
        no_rawat: '{{ $pasien->no_rawat }}',
        cara_masuk: document.getElementById('triase_cara_masuk').value,
        alat_transportasi: document.getElementById('triase_alat_transportasi').value,
        alasan_kedatangan: document.getElementById('triase_alasan_kedatangan').value,
        keterangan_kedatangan: document.getElementById('triase_keterangan_kedatangan').value,
        kode_kasus: document.getElementById('triase_kode_kasus').value,
        tekanan_darah: document.getElementById('triase_tekanan_darah').value,
        nadi: document.getElementById('triase_nadi').value,
        pernapasan: document.getElementById('triase_pernapasan').value,
        suhu: document.getElementById('triase_suhu').value,
        saturasi_o2: document.getElementById('triase_saturasi_o2').value,
        nyeri: document.getElementById('triase_nyeri').value,
    };

    @if($triaseSekunder)
    data.anamnesa_singkat = document.getElementById('triase_anamnesa_singkat').value;
    data.catatan = document.getElementById('triase_catatan').value;
    data.plan = document.getElementById('triase_plan').value;
    @endif

    $.ajax({
        url: '{{ route("inacbg.updateTriaseData") }}',
        type: 'POST',
        data: data,
        success: function(response) {
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (response.success) {
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
