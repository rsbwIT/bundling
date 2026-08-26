@extends('layout.layoutDashboard')

@section('title', 'Kroscek Klaim BPJS (Pre-Claim Validator)')

@section('konten')
<style>
    /* Styling khusus untuk halaman Pre-Claim Validator */
    .metric-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-3px);
    }
    .status-icon {
        font-size: 1.2rem;
    }
    .text-success-custom { color: #28a745; }
    .text-danger-custom { color: #dc3545; }
    .text-warning-custom { color: #ffc107; }
    .text-secondary-custom { color: #6c757d; }
    
    .table-hover tbody tr:hover {
        background-color: #f4f6f9;
    }
</style>

<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-database mr-2"></i> <strong>Live Database Terhubung:</strong> Data yang ditampilkan sudah berasal langsung dari Database Khanza secara Real-Time.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info metric-card">
            <div class="inner">
                <h3>{{ $metrics['total'] ?? 0 }}</h3>
                <p>Total Pasien Pulang</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success metric-card">
            <div class="inner">
                <h3>{{ $metrics['siap'] ?? 0 }}</h3>
                <p>Siap Diklaim (Lengkap)</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning metric-card">
            <div class="inner">
                <h3>{{ $metrics['pending'] ?? 0 }}</h3>
                <p>Berkas Tertunda / Incomplete</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger metric-card">
            <div class="inner">
                <h3>{{ $metrics['sep_error'] ?? 0 }}</h3>
                <p>SEP Bermasalah</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="card card-primary card-outline shadow-sm">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clipboard-check mr-1"></i> Data Kroscek Kelengkapan Klaim
        </h3>
        <div class="card-tools">
            <form action="{{ route('bpjs.pre_claim_validator') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label for="tanggal" class="mr-2">Tanggal:</label>
                    <input type="date" class="form-control form-control-sm" id="tanggal" name="tanggal" value="{{ $tanggal }}">
                </div>
                <div class="form-group mr-3">
                    <label for="jenis_rawat" class="mr-2">Jenis Rawat:</label>
                    <select class="form-control form-control-sm" id="jenis_rawat" name="jenis_rawat">
                        <option value="Semua" {{ ($jenisRawat ?? 'Semua') == 'Semua' ? 'selected' : '' }}>Semua</option>
                        <option value="Ralan" {{ ($jenisRawat ?? '') == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                        <option value="Ranap" {{ ($jenisRawat ?? '') == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Tampilkan</button>
                <a href="{{ route('bpjs.pre_claim_validator', ['tanggal' => $tanggal, 'jenis_rawat' => $jenisRawat ?? 'Semua', 'export' => 'excel']) }}" class="btn btn-success btn-sm ml-2" target="_blank"><i class="fas fa-file-excel"></i> Export</a>
            </form>
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap table-bordered table-striped align-middle">
            <thead class="thead-light text-center">
                <tr>
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle">No Rawat / RM</th>
                    <th rowspan="2" class="align-middle">Nama Pasien / Poli</th>
                    <th colspan="4">Pengecekan Kelengkapan (Pre-Claim)</th>
                    <th rowspan="2" class="align-middle">Status Klaim</th>
                    <th rowspan="2" class="align-middle">Aksi</th>
                </tr>
                <tr>
                    <th>SEP Valid</th>
                    <th>Resume Medis (TTD)</th>
                    <th>Lap. Operasi</th>
                    <th>Koding INACBG</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $row->no_rawat }}</strong><br>
                        <span class="text-muted">RM: {{ $row->no_rkm_medis }}</span>
                    </td>
                    <td>
                        {{ $row->nama_pasien }}<br>
                        <span class="badge badge-info">{{ $row->poliklinik }}</span>
                    </td>
                    
                    {{-- Indikator SEP --}}
                    <td class="text-center">
                        @if($row->status_sep == 1)
                            <i class="fas fa-check-circle text-success-custom status-icon" title="SEP Valid: {{ $row->no_sep }}"></i><br>
                            <small class="text-muted">{{ $row->no_sep }}</small>
                        @else
                            <i class="fas fa-times-circle text-danger-custom status-icon" title="SEP Belum Terbit/Bermasalah"></i>
                        @endif
                    </td>
                    
                    {{-- Indikator Resume Medis --}}
                    <td class="text-center">
                        @if($row->resume_medis == 1)
                            <i class="fas fa-check-circle text-success-custom status-icon" title="Lengkap & TTD DPJP"></i>
                        @else
                            <i class="fas fa-exclamation-triangle text-warning-custom status-icon" title="Belum TTD DPJP"></i>
                        @endif
                    </td>
                    
                    {{-- Indikator Laporan Operasi --}}
                    <td class="text-center">
                        @if($row->laporan_operasi === 1)
                            <i class="fas fa-check-circle text-success-custom status-icon" title="Laporan Operasi Lengkap"></i>
                        @elseif($row->laporan_operasi === 0)
                            <i class="fas fa-times-circle text-danger-custom status-icon" title="Tindakan Operasi Tanpa Laporan"></i>
                        @else
                            <i class="fas fa-minus text-secondary-custom status-icon" title="Tidak Ada Tindakan Operasi"></i>
                        @endif
                    </td>
                    
                    {{-- Indikator Koding --}}
                    <td class="text-center">
                        @if($row->koding_inacbg == 1)
                            <i class="fas fa-check-circle text-success-custom status-icon" title="Sudah Dikoding"></i>
                            @if($row->tarif_inacbg)
                                <br><span class="badge badge-success mt-1" style="font-size: 0.8rem;">Rp {{ number_format($row->tarif_inacbg, 0, ',', '.') }}</span>
                            @endif
                        @else
                            <i class="fas fa-times-circle text-danger-custom status-icon" title="Belum Dikoding"></i>
                        @endif
                    </td>
                    
                    {{-- Status Final Klaim --}}
                    <td class="text-center">
                        @if($row->status_klaim == 'LENGKAP')
                            <span class="badge badge-success px-3 py-2"><i class="fas fa-check mr-1"></i> SIAP KLAIM</span>
                        @else
                            <span class="badge badge-danger px-3 py-2"><i class="fas fa-clock mr-1"></i> PENDING</span>
                        @endif
                    </td>
                    
                    <td class="text-center">
                        @php
                            $btnClass = $row->saran_optimasi ? 'btn-outline-warning' : 'btn-outline-primary';
                            $btnTitle = $row->saran_optimasi ? 'Saran Optimalisasi Koding Tersedia!' : 'Lihat Detail Kekurangan';
                        @endphp
                        <button class="btn btn-sm {{ $btnClass }}" title="{{ $btnTitle }}" 
                                onclick="showDetail(
                                    '{{ $row->no_rawat }}',
                                    '{{ $row->nama_pasien }}',
                                    {{ $row->status_sep }},
                                    {{ $row->resume_medis }},
                                    {{ $row->laporan_operasi === null ? -1 : ($row->laporan_operasi === 1 ? 1 : 0) }},
                                    {{ $row->koding_inacbg }},
                                    '{!! addslashes($row->saran_optimasi ?? '') !!}',
                                    '{{ $row->code_cbg ?? '' }}',
                                    '{{ $row->tarif_inacbg ? number_format($row->tarif_inacbg, 0, ',', '.') : '' }}'
                                )">
                            @if($row->saran_optimasi)
                                <i class="fas fa-exclamation-circle text-warning"></i>
                            @else
                                <i class="fas fa-search"></i>
                            @endif
                        </button>
                        @php
                            $waText = "Yth. Dokter " . ($row->nama_dokter ?? 'DPJP') . ",\n\nMohon izin mengingatkan untuk segera melengkapi/TTD Resume Medis pasien atas nama *" . $row->nama_pasien . "* agar berkas dapat segera kami klaimkan ke BPJS hari ini.\n\nTerima kasih, Tim Casemix.";
                            $noTelp = $row->no_telp ?? '';
                            $noTelp = str_starts_with($noTelp, '0') ? '62' . substr($noTelp, 1) : $noTelp;
                            $waLink = "https://wa.me/" . $noTelp . "?text=" . urlencode($waText);
                        @endphp
                        @if($noTelp)
                            <a href="{{ $waLink }}" target="_blank" class="btn btn-sm btn-outline-success" title="WA Dokter: {{ $row->nama_dokter }}">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        @else
                            <button class="btn btn-sm btn-outline-secondary" title="No WA Dokter Tidak Tersedia" disabled>
                                <i class="fab fa-whatsapp"></i>
                            </button>
                        @endif
                        <a href="{{ url('bpjs/inacbg/' . $row->no_rawat) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Buka Menu INACBG (Kirim 2)">
                            <i class="fas fa-paper-plane"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Belum ada data pasien pulang untuk tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel"><i class="fas fa-search mr-2"></i> Detail Kelengkapan Berkas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>No. Rawat:</strong> <span id="detNoRawat"></span><br>
                    <strong>Nama Pasien:</strong> <span id="detNamaPasien"></span>
                </div>
                <hr>
                <h6><strong>Status Pengecekan:</strong></h6>
                <ul class="list-group list-group-flush" id="detList">
                    <!-- Javascript will populate this -->
                </ul>
                
                <div id="optimasiContainer" class="mt-3 d-none">
                    <div class="alert alert-warning shadow-sm">
                        <strong class="text-dark"><i class="fas fa-lightbulb text-warning mr-1"></i> Optimasi Koding</strong>
                        <p id="detOptimasi" class="mb-0 mt-1 text-dark" style="font-size: 0.9rem;"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(no_rawat, nama_pasien, status_sep, resume_medis, laporan_operasi, koding_inacbg, saran_optimasi, code_cbg, tarif_inacbg) {
        document.getElementById('detNoRawat').innerText = no_rawat;
        document.getElementById('detNamaPasien').innerText = nama_pasien;
        
        let html = '';
        
        // Cek SEP
        if (status_sep == 1) {
            html += '<li class="list-group-item text-success"><i class="fas fa-check-circle mr-2"></i> SEP Valid dan Tersedia</li>';
        } else {
            html += '<li class="list-group-item text-danger"><i class="fas fa-times-circle mr-2"></i> SEP Belum Terbit / Bermasalah</li>';
        }

        // Cek Resume
        if (resume_medis == 1) {
            html += '<li class="list-group-item text-success"><i class="fas fa-check-circle mr-2"></i> Resume Medis Sudah di-TTD DPJP</li>';
        } else {
            html += '<li class="list-group-item text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Resume Medis BELUM di-TTD DPJP</li>';
        }

        // Cek Operasi
        if (laporan_operasi == 1) {
            html += '<li class="list-group-item text-success"><i class="fas fa-check-circle mr-2"></i> Laporan Operasi Sudah Lengkap</li>';
        } else if (laporan_operasi == 0) {
            html += '<li class="list-group-item text-danger"><i class="fas fa-times-circle mr-2"></i> Pasien memiliki tagihan Bedah, tetapi Laporan Operasi Kosong</li>';
        } else {
            html += '<li class="list-group-item text-secondary"><i class="fas fa-minus mr-2"></i> Tidak ada tindakan Operasi/Bedah</li>';
        }

        // Cek Koding
        if (koding_inacbg == 1) {
            html += '<li class="list-group-item text-success"><i class="fas fa-check-circle mr-2"></i> Koding INACBG Selesai';
            if (tarif_inacbg !== '') {
                html += '<br><small class="text-muted ml-4">Kode CBG: <strong>' + code_cbg + '</strong> | Tarif: <strong>Rp ' + tarif_inacbg + '</strong></small>';
            }
            html += '</li>';
        } else {
            html += '<li class="list-group-item text-danger"><i class="fas fa-times-circle mr-2"></i> Berkas Belum Dikoding oleh Coder</li>';
        }

        document.getElementById('detList').innerHTML = html;
        
        // Optimasi Koding
        let optContainer = document.getElementById('optimasiContainer');
        if (saran_optimasi && saran_optimasi.trim() !== '') {
            document.getElementById('detOptimasi').innerHTML = saran_optimasi;
            optContainer.classList.remove('d-none');
        } else {
            optContainer.classList.add('d-none');
            document.getElementById('detOptimasi').innerHTML = '';
        }

        $('#modalDetail').modal('show');
    }
</script>

@endsection
