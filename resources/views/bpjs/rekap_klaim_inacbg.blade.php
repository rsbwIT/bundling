@extends('layout.layoutDashboard')
@section('title', 'Rekap Klaim E-Klaim Terkirim')

@section('konten')
<div class="container-fluid px-3 py-4">

    {{-- HEADER & TITLE --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Rekap Klaim E-Klaim INA-CBG Terkirim
            </h4>
            <p class="text-muted small mb-0">
                Data sinkronisasi valid langsung dari Server E-Klaim (<span class="font-monospace text-primary">192.168.1.153</span>) &amp; Status Pengiriman DC Kemenkes/BPJS.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold shadow-sm" id="btnSyncSemua">
                <i class="fas fa-sync-alt mr-1"></i> Sinkronkan E-Klaim (Semua Data Tampil)
            </button>
            <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm" onclick="exportToExcel()">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </button>
        </div>
    </div>

    {{-- KARTU STATISTIK RINGKAS --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-light-primary text-primary mr-3" style="background: #e0f2fe;">
                        <i class="fas fa-paper-plane fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Total Pengiriman</div>
                        <div class="h4 font-weight-bold text-dark mb-0">{{ number_format($totalKlaim, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-light-success text-success mr-3" style="background: #dcfce7;">
                        <i class="fas fa-check-double fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Valid Sent (Kemenkes)</div>
                        <div class="h4 font-weight-bold text-success mb-0">{{ number_format($totalTerkirimKemenkes, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-light-info text-info mr-3" style="background: #e0e7ff;">
                        <i class="fas fa-coins fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Total Tarif INA-CBG</div>
                        <div class="h4 font-weight-bold text-primary mb-0">Rp {{ number_format($totalNominalInacbg, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm rounded-lg h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-light-warning text-warning mr-3" style="background: #fef3c7;">
                        <i class="fas fa-hospital-alt fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase">Total Tarif Rumah Sakit</div>
                        <div class="h4 font-weight-bold text-dark mb-0">Rp {{ number_format($totalNominalRs, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM FILTER PENCARIAN --}}
    <div class="card border-0 shadow-sm rounded-lg mb-4">
        <div class="card-body p-3">
            <form action="{{ route('bpjs.rekap-klaim-inacbg') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small font-weight-bold text-muted">Berdasarkan</label>
                    <select name="tipe_tanggal" class="form-control form-control-sm">
                        <option value="tgl_kirim" {{ ($tipeTanggal ?? 'tgl_kirim') === 'tgl_kirim' ? 'selected' : '' }}>Tanggal Kirim Klaim</option>
                        <option value="tgl_registrasi" {{ ($tipeTanggal ?? '') === 'tgl_registrasi' ? 'selected' : '' }}>Tanggal Rawat / Berobat</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small font-weight-bold text-muted">Tanggal Mulai</label>
                    <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small font-weight-bold text-muted">Sampai Tanggal</label>
                    <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small font-weight-bold text-muted">Jenis Pelayanan</label>
                    <select name="status_lanjut" class="form-control form-control-sm">
                        <option value="Semua" {{ $statusLanjut === 'Semua' ? 'selected' : '' }}>Semua (Ralan &amp; Ranap)</option>
                        <option value="Ralan" {{ $statusLanjut === 'Ralan' ? 'selected' : '' }}>Rawat Jalan (Ralan)</option>
                        <option value="Ranap" {{ $statusLanjut === 'Ranap' ? 'selected' : '' }}>Rawat Inap (Ranap)</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small font-weight-bold text-muted">Status Server E-Klaim</label>
                    <select name="status_kirim" class="form-control form-control-sm">
                        <option value="Semua" {{ $statusKirim === 'Semua' ? 'selected' : '' }}>Semua Data</option>
                        <option value="Terkirim" {{ $statusKirim === 'Terkirim' ? 'selected' : '' }}>Terkirim Valid (Sent)</option>
                        <option value="Belum_Sinkron" {{ $statusKirim === 'Belum_Sinkron' ? 'selected' : '' }}>Belum Disinkron</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-8">
                    <label class="form-label small font-weight-bold text-muted">Cari (SEP / Rawat / Pasien / Coder)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="keyword" class="form-control" placeholder="Ketik kata kunci..." value="{{ $keyword }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 col-sm-4">
                    <a href="{{ route('bpjs.rekap-klaim-inacbg') }}" class="btn btn-secondary btn-sm btn-block" title="Reset Filter">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA REKAP --}}
    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-list text-primary mr-1"></i> Daftar Klaim Terkirim ({{ count($listKlaim) }} Pasien)
            </h6>
            <small class="text-muted">Klik tombol <span class="badge badge-info">Cek E-Klaim</span> untuk menarik live status terbaru dari server INACBG</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm" id="tableRekapKlaim">
                    <thead class="bg-light">
                        <tr>
                            <th width="40px" class="text-center">No</th>
                            <th>No. SEP &amp; Rawat</th>
                            <th>Pasien</th>
                            <th>Status Pelayanan</th>
                            <th>Kode &amp; Deskripsi INA-CBG</th>
                            <th class="text-right">Tarif INA-CBG</th>
                            <th class="text-right">Tarif RS</th>
                            <th class="text-center">Status E-Klaim</th>
                            <th>Coder &amp; Waktu Kirim</th>
                            <th class="text-center" width="140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($listKlaim as $index => $row)
                            <tr id="row-{{ $row->no_sep }}" data-sep="{{ $row->no_sep }}">
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold text-primary font-monospace">{{ $row->no_sep }}</div>
                                    <small class="text-muted font-monospace">{{ $row->no_rawat }}</small>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $row->nm_pasien }}</div>
                                    <small class="text-muted">RM: {{ $row->no_rkm_medis }} &bull; {{ $row->nm_poli ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $row->status_lanjut === 'Ranap' ? 'badge-info' : 'badge-success' }} px-2 py-1">
                                        {{ $row->status_lanjut }}
                                    </span>
                                </td>
                                <td>
                                    @if (!empty($row->cbg_code))
                                        <span class="badge badge-primary font-monospace">{{ $row->cbg_code }}</span>
                                        <div class="small text-dark mt-1" style="max-width: 250px; line-height: 1.3;">{{ $row->cbg_desc }}</div>
                                    @else
                                        <span class="text-muted small font-italic">Belum disinkron</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-primary font-monospace">
                                    {{ $row->tarif_inacbg > 0 ? 'Rp ' . number_format($row->tarif_inacbg, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right font-monospace text-muted">
                                    {{ $row->tarif_rs > 0 ? 'Rp ' . number_format($row->tarif_rs, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if ($row->kemenkes_dc_status === 'sent')
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle mr-1"></i> SENT
                                        </span>
                                        <div class="text-muted" style="font-size: 10px;">{{ $row->kemenkes_dc_sent_dttm ? substr($row->kemenkes_dc_sent_dttm, 0, 16) : '' }}</div>
                                    @elseif(!empty($row->kemenkes_dc_status))
                                        <span class="badge badge-warning text-dark px-2 py-1">
                                            {{ strtoupper($row->kemenkes_dc_status) }}
                                        </span>
                                    @else
                                        <span class="badge badge-light border text-muted px-2 py-1">
                                            Belum Cek
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark">{{ $row->nama_coder ?? '-' }}</div>
                                    <small class="text-muted"><i class="far fa-clock mr-1"></i>{{ $row->tgl_kirim }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ url('/inacbg/print/' . $row->no_sep) }}" target="_blank" class="btn btn-outline-danger" title="Cetak / Unduh PDF Klaim">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ url('/bpjs/inacbg/' . str_replace('/', '', $row->no_rawat)) }}" target="_blank" class="btn btn-outline-secondary" title="Buka Detail INACBG Pasien">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-primary btn-sync-single" data-sep="{{ $row->no_sep }}" title="Tarik Status Valid dari Server E-Klaim">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                    <div>Tidak ada data pengiriman klaim pada filter tanggal ini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL PROSES BATCH SINKRONISASI --}}
<div class="modal fade" id="modalBatchSync" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5 class="font-weight-bold mb-2">Menghubungkan ke Server E-Klaim...</h5>
                <p class="text-muted small mb-3" id="batchSyncStatus">Sedang mengambil validasi klaim dari E-Klaim...</p>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="batchProgressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT --}}
<script>
$(document).ready(function() {

    // 1. SINKRONISASI 1 SEP
    $(document).on('click', '.btn-sync-single', function() {
        let btn = $(this);
        let sep = btn.data('sep');
        let icon = btn.find('i');

        icon.addClass('fa-spin');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('bpjs.rekap-klaim-inacbg.sync-single') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                no_sep: sep
            },
            success: function(res) {
                icon.removeClass('fa-spin');
                btn.prop('disabled', false);

                if (res.status) {
                    alert('Berhasil! Status E-Klaim: ' + res.data.kemenkes_status + ' | CBG: ' + (res.data.cbg_code || '-') + ' | Tarif: Rp ' + Number(res.data.tarif_inacbg).toLocaleString('id-ID'));
                    location.reload();
                } else {
                    alert('Gagal: ' + res.message);
                }
            },
            error: function(xhr) {
                icon.removeClass('fa-spin');
                btn.prop('disabled', false);
                alert('Terjadi kesalahan jaringan ke server E-Klaim');
            }
        });
    });

    // 2. SINKRONISASI BATCH (SEMUA YANG TAMPIL)
    $('#btnSyncSemua').click(async function() {
        let seps = [];
        $('#tableRekapKlaim tbody tr[data-sep]').each(function() {
            let s = $(this).data('sep');
            if (s) seps.push(s);
        });

        if (seps.length === 0) {
            alert('Tidak ada data klaim yang tampil untuk disinkronkan.');
            return;
        }

        if (!confirm('Apakah Anda ingin memverifikasi ' + seps.length + ' klaim langsung ke Server E-Klaim? Proses ini memerlukan beberapa detik.')) {
            return;
        }

        $('#modalBatchSync').modal('show');
        let total = seps.length;
        let completed = 0;

        // Bagi menjadi chunk per 5 SEP agar server tidak timeout
        const chunkSize = 5;
        for (let i = 0; i < seps.length; i += chunkSize) {
            let chunk = seps.slice(i, i + chunkSize);
            $('#batchSyncStatus').text('Memproses ' + (i + 1) + ' - ' + Math.min(i + chunkSize, total) + ' dari ' + total + ' klaim...');
            
            try {
                await $.ajax({
                    url: "{{ route('bpjs.rekap-klaim-inacbg.sync-batch') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        seps: chunk
                    }
                });
            } catch (err) {
                console.error(err);
            }

            completed += chunk.length;
            let pct = Math.round((completed / total) * 100);
            $('#batchProgressBar').css('width', pct + '%');
        }

        $('#batchSyncStatus').text('Selesai! Memuat ulang data...');
        setTimeout(function() {
            location.reload();
        }, 800);
    });

});

// 3. EXPORT EXCEL TABLE
function exportToExcel() {
    let htmlTable = document.getElementById('tableRekapKlaim').outerHTML;
    let url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(htmlTable);
    let downloadLink = document.createElement("a");
    downloadLink.href = url;
    downloadLink.download = "Rekap_Klaim_Inacbg_{{ $tgl1 }}_sd_{{ $tgl2 }}.xls";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
@endsection
