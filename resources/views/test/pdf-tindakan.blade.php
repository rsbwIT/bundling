@extends('..layout.layoutDashboard')
@section('title', 'PDF Tindakan')

@section('konten')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-file-pdf text-danger mr-2"></i> Cetak PDF Tindakan
            </h5>
        </div>
        <div class="card-body">
            {{-- Form Filter --}}
            <form action="{{ url('/pdf-tindakan') }}" method="GET" class="mb-4">
                <input type="hidden" name="filter_submitted" value="1">
                <div class="row align-items-end">
                    <div class="col-md-3 col-sm-12 mb-2">
                        <label class="font-weight-bold text-xs">Pilih Dokter / Petugas:</label>
                        <select name="kd_dokter" class="form-control form-control-sm select2" style="width: 100%;" required>
                            <option value="">-- Pilih Dokter / Petugas --</option>
                            
                            @if(count($listDokterSpesialis) > 0)
                                <optgroup label="Dokter Spesialis">
                                    @foreach ($listDokterSpesialis as $doc)
                                        <option value="{{ $doc['id_khanza'] }}" {{ $kdDokter == $doc['id_khanza'] ? 'selected' : '' }}>
                                            {{ $doc['nama'] }} ({{ $doc['id_khanza'] }}) [{{ $doc['kode'] }}]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(count($listDokterUmum) > 0)
                                <optgroup label="Dokter Umum">
                                    @foreach ($listDokterUmum as $doc)
                                        <option value="{{ $doc['id_khanza'] }}" {{ $kdDokter == $doc['id_khanza'] ? 'selected' : '' }}>
                                            {{ $doc['nama'] }} ({{ $doc['id_khanza'] }}) [{{ $doc['kode'] }}]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(count($listPetugasHD) > 0)
                                <optgroup label="Tim Hemodialisa (HD)">
                                    @foreach ($listPetugasHD as $doc)
                                        <option value="{{ $doc['id_khanza'] }}" {{ $kdDokter == $doc['id_khanza'] ? 'selected' : '' }}>
                                            {{ $doc['nama'] }} ({{ $doc['id_khanza'] }}) [{{ $doc['kode'] }}]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if(count($listPetugas) > 0)
                                <optgroup label="Petugas / Paramedis">
                                    @foreach ($listPetugas as $doc)
                                        <option value="{{ $doc['id_khanza'] }}" {{ $kdDokter == $doc['id_khanza'] ? 'selected' : '' }}>
                                            {{ $doc['nama'] }} ({{ $doc['id_khanza'] }}) [{{ $doc['kode'] }}]
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-xs">Tanggal Awal:</label>
                        <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tanggl1 }}" required>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="font-weight-bold text-xs">Tanggal Akhir:</label>
                        <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tanggl2 }}" required>
                    </div>
                    <div class="col-md-3 col-sm-12 mb-2">
                        <label class="font-weight-bold text-xs d-block">Pilihan Penjamin / Tindakan:</label>
                        <div class="d-flex align-items-center pt-1">
                            <div class="custom-control custom-checkbox mr-3">
                                <input class="custom-control-input" type="checkbox" id="checkUmum" name="jenis[]" value="umum" {{ in_array('umum', $selectedJenis) ? 'checked' : '' }}>
                                <label for="checkUmum" class="custom-control-label font-weight-bold text-xs">Umum</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input class="custom-control-input" type="checkbox" id="checkAsuransi" name="jenis[]" value="asuransi" {{ in_array('asuransi', $selectedJenis) ? 'checked' : '' }}>
                                <label for="checkAsuransi" class="custom-control-label font-weight-bold text-xs">Asuransi</label>
                            </div>
                            <div class="custom-control custom-checkbox mr-3">
                                <input class="custom-control-input" type="checkbox" id="checkInhealth" name="jenis[]" value="inhealth" {{ in_array('inhealth', $selectedJenis) ? 'checked' : '' }}>
                                <label for="checkInhealth" class="custom-control-label font-weight-bold text-xs">Inhealth</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="checkBpjs" name="jenis[]" value="bpjs" {{ in_array('bpjs', $selectedJenis) ? 'checked' : '' }}>
                                <label for="checkBpjs" class="custom-control-label font-weight-bold text-xs">BPJS</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-2 d-flex">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill mr-1">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                        <button type="button" class="btn btn-sm btn-success flex-fill mr-1" data-toggle="modal" data-target="#modalDownloadBanyakDokter">
                            <i class="fas fa-file-pdf mr-1"></i> Cetak PDF (Banyak Dokter)
                        </button>
                        @if ($kdDokter)
                            @php
                                $exportParams = [
                                    'kd_dokter' => $kdDokter,
                                    'tgl1' => $tanggl1,
                                    'tgl2' => $tanggl2,
                                    'filter_submitted' => 1,
                                    'jenis' => $selectedJenis,
                                    'export' => 'pdf'
                                ];
                                $exportUrl = url('/pdf-tindakan') . '?' . http_build_query($exportParams);
                            @endphp
                            <a href="{{ $exportUrl }}"
                               target="_blank"
                               class="btn btn-sm btn-danger flex-fill">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if ($kdDokter)
                @php
                    $exportParams = [
                        'kd_dokter' => $kdDokter,
                        'tgl1' => $tanggl1,
                        'tgl2' => $tanggl2,
                        'filter_submitted' => 1,
                        'jenis' => $selectedJenis,
                        'export' => 'pdf'
                    ];
                    $exportUrl = url('/pdf-tindakan') . '?' . http_build_query($exportParams);
                @endphp
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0 font-weight-bold text-dark">
                            Detail Tindakan: <span class="text-primary">{{ $nmDokter }}</span> ({{ $kdDokter }})
                        </h5>
                        <small class="text-muted">
                            Periode: {{ date('d-m-Y', strtotime($tanggl1)) }} s/d {{ date('d-m-Y', strtotime($tanggl2)) }} |
                            Penjamin: <strong>{{ $labelJenis }}</strong>
                        </small>
                    </div>
                    <div>
                        <a href="{{ $exportUrl }}"
                           target="_blank"
                           class="btn btn-danger shadow-sm">
                            <i class="fas fa-file-pdf mr-1"></i> Cetak Dokumen PDF
                        </a>
                    </div>
                </div>

                {{-- Ringkasan Total Box --}}
                <div class="row mb-3">
                    @if (in_array('umum', $selectedJenis))
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="info-box bg-info mb-0">
                                <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Tindakan UMUM</span>
                                    <span class="info-box-number">Rp {{ number_format($totalUmum) }}</span>
                                    <span class="progress-description">Ranap: Rp {{ number_format($totalRanapUmum) }} | Ralan: Rp {{ number_format($totalRalanUmum) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (in_array('asuransi', $selectedJenis))
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="info-box bg-warning mb-0">
                                <span class="info-box-icon text-white"><i class="fas fa-shield-alt"></i></span>
                                <div class="info-box-content text-white">
                                    <span class="info-box-text">Total Tindakan ASURANSI</span>
                                    <span class="info-box-number">Rp {{ number_format($totalAsuransi) }}</span>
                                    <span class="progress-description">Ranap: Rp {{ number_format($totalRanapAsuransi) }} | Ralan: Rp {{ number_format($totalRalanAsuransi) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (in_array('inhealth', $selectedJenis))
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="info-box bg-success mb-0">
                                <span class="info-box-icon text-white"><i class="fas fa-heartbeat"></i></span>
                                <div class="info-box-content text-white">
                                    <span class="info-box-text">Total Tindakan INHEALTH</span>
                                    <span class="info-box-number">Rp {{ number_format($totalInhealth) }}</span>
                                    <span class="progress-description">Ranap: Rp {{ number_format($totalRanapInhealth) }} | Ralan: Rp {{ number_format($totalRalanInhealth) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (in_array('bpjs', $selectedJenis))
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                            <div class="info-box bg-secondary mb-0">
                                <span class="info-box-icon text-white"><i class="fas fa-hospital-user"></i></span>
                                <div class="info-box-content text-white">
                                    <span class="info-box-text">Total Tindakan BPJS</span>
                                    <span class="info-box-number">Rp {{ number_format($totalBpjs) }}</span>
                                    <span class="progress-description">Ranap: Rp {{ number_format($totalRanapBpjs) }} | Ralan: Rp {{ number_format($totalRalanBpjs) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                        <div class="info-box bg-danger mb-0">
                            <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">GRAND TOTAL</span>
                                <span class="info-box-number">Rp {{ number_format($grandTotal) }}</span>
                                <span class="progress-description">Ranap: Rp {{ number_format($totalRanap) }} | Ralan: Rp {{ number_format($totalRalan) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================================================= --}}
                {{-- BAGIAN I: TINDAKAN UMUM (RANAP LALU RALAN)                --}}
                {{-- ========================================================= --}}
                @if (in_array('umum', $selectedJenis))
                    <div class="alert alert-info py-2 mb-3 font-weight-bold">
                        <i class="fas fa-hospital-user mr-2"></i> I. TINDAKAN UMUM
                        <span class="float-right font-weight-bold">Subtotal Umum: Rp {{ number_format($totalUmum) }}</span>
                    </div>

                    {{-- TABEL I.A: RANAP UMUM --}}
                    <div class="card card-outline card-warning mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title font-weight-bold text-dark mb-0">
                                <i class="fas fa-bed text-warning mr-1"></i> 1. Tindakan Rawat Inap (Ranap) - UMUM
                                <span class="badge badge-warning ml-2">{{ count($detailsRanapUmum) }} Data</span>
                            </h6>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-default" onclick="copyTable('tableRanapUmum')">
                                    <i class="fas fa-copy"></i> Copy Tabel Ranap Umum
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped text-xs mb-0" style="white-space: nowrap;" id="tableRanapUmum">
                                    <thead style="background-color: #fff3cd; color: #856404;">
                                        <tr>
                                            <th class="text-center" width="4%">No</th>
                                            <th>No Rawat</th>
                                            <th>Nama Pasien</th>
                                            <th>Dokter / Petugas</th>
                                            <th>Penanggung Jawab</th>
                                            <th>Nama Tindakan</th>
                                            <th>Sumber</th>
                                            <th class="text-right">Tarif (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @forelse ($detailsRanapUmum as $item)
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>{{ $item->no_rawat }}</td>
                                                <td>{{ $item->nm_pasien }}</td>
                                                <td>{{ $item->nm_dokter_petugas ?? '-' }}</td>
                                                <td>{{ $item->penjamin ?? '-' }}</td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-warning">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Inap (Ranap) Umum.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #ffeeba; font-weight: bold;">
                                        <tr>
                                            <td colspan="7" class="text-right">TOTAL RAWAT INAP (UMUM)</td>
                                            <td class="text-right text-dark font-weight-bold">
                                                Rp {{ number_format($totalRanapUmum) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL I.B: RALAN UMUM --}}
                    <div class="card card-outline card-info mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title font-weight-bold text-info mb-0">
                                <i class="fas fa-walking mr-1"></i> 2. Tindakan Rawat Jalan (Ralan) - UMUM
                                <span class="badge badge-info ml-2">{{ count($detailsRalanUmum) }} Data</span>
                            </h6>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-default" onclick="copyTable('tableRalanUmum')">
                                    <i class="fas fa-copy"></i> Copy Tabel Ralan Umum
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped text-xs mb-0" style="white-space: nowrap;" id="tableRalanUmum">
                                    <thead style="background-color: #d1ecf1; color: #0c5460;">
                                        <tr>
                                            <th class="text-center" width="4%">No</th>
                                            <th>No Rawat</th>
                                            <th>Nama Pasien</th>
                                            <th>Dokter / Petugas</th>
                                            <th>Penanggung Jawab</th>
                                            <th>Nama Tindakan</th>
                                            <th>Sumber</th>
                                            <th class="text-right">Tarif (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @forelse ($detailsRalanUmum as $item)
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>{{ $item->no_rawat }}</td>
                                                <td>{{ $item->nm_pasien }}</td>
                                                <td>{{ $item->nm_dokter_petugas ?? '-' }}</td>
                                                <td>{{ $item->penjamin ?? '-' }}</td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-info">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Jalan (Ralan) Umum.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #bee5eb; font-weight: bold;">
                                        <tr>
                                            <td colspan="7" class="text-right">TOTAL RAWAT JALAN (UMUM)</td>
                                            <td class="text-right text-info font-weight-bold">
                                                Rp {{ number_format($totalRalanUmum) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ========================================================= --}}
                {{-- BAGIAN II: TINDAKAN ASURANSI (RANAP LALU RALAN)           --}}
                {{-- ========================================================= --}}
                @if (in_array('asuransi', $selectedJenis))
                    <div class="alert alert-primary py-2 mb-3 font-weight-bold mt-4">
                        <i class="fas fa-shield-alt mr-2"></i> II. TINDAKAN ASURANSI
                        <span class="float-right font-weight-bold">Subtotal Asuransi: Rp {{ number_format($totalAsuransi) }}</span>
                    </div>

                    {{-- TABEL II.A: RANAP ASURANSI --}}
                    <div class="card card-outline card-orange mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title font-weight-bold text-dark mb-0">
                                <i class="fas fa-bed text-orange mr-1"></i> 1. Tindakan Rawat Inap (Ranap) - ASURANSI
                                <span class="badge badge-secondary ml-2">{{ count($detailsRanapAsuransi) }} Data</span>
                            </h6>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-default" onclick="copyTable('tableRanapAsuransi')">
                                    <i class="fas fa-copy"></i> Copy Tabel Ranap Asuransi
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped text-xs mb-0" style="white-space: nowrap;" id="tableRanapAsuransi">
                                    <thead style="background-color: #ffe5d0; color: #a04000;">
                                        <tr>
                                            <th class="text-center" width="4%">No</th>
                                            <th>No Rawat</th>
                                            <th>Nama Pasien</th>
                                            <th>Dokter / Petugas</th>
                                            <th>Nama Asuransi / Penjamin</th>
                                            <th>Nama Tindakan</th>
                                            <th>Sumber</th>
                                            <th class="text-right">Tarif (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @forelse ($detailsRanapAsuransi as $item)
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>{{ $item->no_rawat }}</td>
                                                <td>{{ $item->nm_pasien }}</td>
                                                <td>{{ $item->nm_dokter_petugas ?? '-' }}</td>
                                                <td><span class="badge badge-secondary">{{ $item->penjamin ?? '-' }}</span></td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-warning">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Inap (Ranap) Asuransi.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #ffd6b3; font-weight: bold;">
                                        <tr>
                                            <td colspan="7" class="text-right">TOTAL RAWAT INAP (ASURANSI)</td>
                                            <td class="text-right text-dark font-weight-bold">
                                                Rp {{ number_format($totalRanapAsuransi) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL II.B: RALAN ASURANSI --}}
                    <div class="card card-outline card-primary mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title font-weight-bold text-primary mb-0">
                                <i class="fas fa-walking mr-1"></i> 2. Tindakan Rawat Jalan (Ralan) - ASURANSI
                                <span class="badge badge-primary ml-2">{{ count($detailsRalanAsuransi) }} Data</span>
                            </h6>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-default" onclick="copyTable('tableRalanAsuransi')">
                                    <i class="fas fa-copy"></i> Copy Tabel Ralan Asuransi
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped text-xs mb-0" style="white-space: nowrap;" id="tableRalanAsuransi">
                                    <thead style="background-color: #cce5ff; color: #004085;">
                                        <tr>
                                            <th class="text-center" width="4%">No</th>
                                            <th>No Rawat</th>
                                            <th>Nama Pasien</th>
                                            <th>Dokter / Petugas</th>
                                            <th>Nama Asuransi / Penjamin</th>
                                            <th>Nama Tindakan</th>
                                            <th>Sumber</th>
                                            <th class="text-right">Tarif (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @forelse ($detailsRalanAsuransi as $item)
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>{{ $item->no_rawat }}</td>
                                                <td>{{ $item->nm_pasien }}</td>
                                                <td>{{ $item->nm_dokter_petugas ?? '-' }}</td>
                                                <td><span class="badge badge-secondary">{{ $item->penjamin ?? '-' }}</span></td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-primary">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Jalan (Ralan) Asuransi.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #b8daff; font-weight: bold;">
                                        <tr>
                                            <td colspan="7" class="text-right">TOTAL RAWAT JALAN (ASURANSI)</td>
                                            <td class="text-right text-primary font-weight-bold">
                                                Rp {{ number_format($totalRalanAsuransi) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- 3. TABEL INHEALTH --}}
                @if(in_array('inhealth', $selectedJenis))
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-heartbeat mr-2"></i>Tindakan INHEALTH ({{ count($detailsRanapInhealth) + count($detailsRalanInhealth) }} Tindakan)</h6>
                            <div>
                                <span class="badge badge-light text-success mr-2">Ranap: Rp {{ number_format($totalRanapInhealth) }}</span>
                                <span class="badge badge-light text-success">Ralan: Rp {{ number_format($totalRalanInhealth) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            {{-- 3.A. RAWAT INAP INHEALTH --}}
                            <div class="bg-light p-2 border-bottom font-weight-bold text-success text-xs">
                                A. Rawat Inap (Ranap) - Inhealth <span class="badge badge-success ml-1">{{ count($detailsRanapInhealth) }} Data</span>
                                <button type="button" class="btn btn-xs btn-outline-success float-right" onclick="copyTable('tableRanapInhealth')" style="padding: 0px 5px;"><i class="fas fa-copy"></i> Copy</button>
                            </div>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-hover table-sm text-xs mb-0" id="tableRanapInhealth">
                                    <thead class="thead-light sticky-top">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>No. Rawat</th>
                                            <th>Nama Pasien</th>
                                            <th>Dokter / Petugas</th>
                                            <th>Nama Asuransi / Penjamin</th>
                                            <th>Nama Tindakan</th>
                                            <th>Sumber</th>
                                            <th class="text-right">Tarif (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @forelse ($detailsRanapInhealth as $item)
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>{{ $item->no_rawat }}</td>
                                                <td>{{ $item->nm_pasien }}</td>
                                                <td>{{ $item->nm_dokter_petugas ?? '-' }}</td>
                                                <td><span class="badge badge-secondary">{{ $item->penjamin ?? '-' }}</span></td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-primary">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Inap (Ranap) Inhealth.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #d4edda; font-weight: bold;">
                                        <tr>
                                            <td colspan="7" class="text-right">TOTAL RAWAT INAP (INHEALTH)</td>
                                            <td class="text-right text-success font-weight-bold">
                                                Rp {{ number_format($totalRanapInhealth) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- 3.B. RAWAT JALAN INHEALTH --}}
                            <div class="bg-light p-2 border-bottom border-top font-weight-bold text-success text-xs mt-2">
                                B. Rawat Jalan (Ralan) - Inhealth <span class="badge badge-success ml-1">{{ count($detailsRalanInhealth) }} Data</span>
                                <button type="button" class="btn btn-xs btn-outline-success float-right" onclick="copyTable('tableRalanInhealth')" style="padding: 0px 5px;"><i class="fas fa-copy"></i> Copy</button>
                            </div>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-hover table-sm text-xs mb-0" id="tableRalanInhealth">
                                    <thead class="thead-light sticky-top">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>No. Rawat</th>
                                            <th>Nama Pasien</th>
                                            <th>Dokter / Petugas</th>
                                            <th>Nama Asuransi / Penjamin</th>
                                            <th>Nama Tindakan</th>
                                            <th>Sumber</th>
                                            <th class="text-right">Tarif (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @forelse ($detailsRalanInhealth as $item)
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>{{ $item->no_rawat }}</td>
                                                <td>{{ $item->nm_pasien }}</td>
                                                <td>{{ $item->nm_dokter_petugas ?? '-' }}</td>
                                                <td><span class="badge badge-secondary">{{ $item->penjamin ?? '-' }}</span></td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-primary">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Jalan (Ralan) Inhealth.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #d4edda; font-weight: bold;">
                                        <tr>
                                            <td colspan="7" class="text-right">TOTAL RAWAT JALAN (INHEALTH)</td>
                                            <td class="text-right text-success font-weight-bold">
                                                Rp {{ number_format($totalRalanInhealth) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 4. TABEL BPJS --}}
                @if(in_array('bpjs', $selectedJenis))
                    <div class="card card-secondary shadow-sm mb-4">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-hospital-user mr-2"></i>Tindakan BPJS ({{ count($detailsRanapBpjs) + count($detailsRalanBpjs) }} Tindakan)</h6>
                            <div>
                                <span class="badge badge-light text-secondary mr-2">Ranap: Rp {{ number_format($totalRanapBpjs) }}</span>
                                <span class="badge badge-light text-secondary">Ralan: Rp {{ number_format($totalRalanBpjs) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            {{-- 4.A. RAWAT INAP BPJS --}}
                            <div class="mb-4">
                                <h6 class="font-weight-bold text-dark mb-2">
                                    A. Rawat Inap (Ranap) - BPJS <span class="badge badge-secondary ml-1">{{ count($detailsRanapBpjs) }} Data</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary float-right" onclick="copyTable('tableRanapBpjs')" style="padding: 0px 5px;"><i class="fas fa-copy"></i> Copy</button>
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm text-xs mb-0" id="tableRanapBpjs">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th width="3%">No</th>
                                                <th width="15%">No. Rawat</th>
                                                <th width="20%">Nama Pasien</th>
                                                <th width="12%">Penjamin</th>
                                                <th width="25%">Nama Perawatan</th>
                                                <th width="7%">Tgl Rawat</th>
                                                <th width="6%">Jam</th>
                                                <th width="12%">Tarif (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($detailsRanapBpjs as $item)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $item->no_rawat }}</td>
                                                    <td>{{ $item->nm_pasien }}</td>
                                                    <td>{{ $item->png_jawab ?? 'BPJS' }}</td>
                                                    <td>{{ $item->nm_perawatan }} <span class="text-muted">({{ $item->sumber ?? '-' }})</span></td>
                                                    <td class="text-center">{{ isset($item->tgl_perawatan) ? date('d-m-Y', strtotime($item->tgl_perawatan)) : '-' }}</td>
                                                    <td class="text-center">{{ $item->jam_rawat ?? '-' }}</td>
                                                    <td class="text-right">{{ number_format($item->tarif) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted font-italic">
                                                        Tidak ada data tindakan Rawat Inap (Ranap) BPJS.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="7" class="text-right">TOTAL RAWAT INAP (BPJS)</td>
                                                <td class="text-right text-secondary font-weight-bold">
                                                    Rp {{ number_format($totalRanapBpjs) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            
                            {{-- 4.B. RAWAT JALAN BPJS --}}
                            <div>
                                <h6 class="font-weight-bold text-dark mb-2">
                                    B. Rawat Jalan (Ralan) - BPJS <span class="badge badge-secondary ml-1">{{ count($detailsRalanBpjs) }} Data</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary float-right" onclick="copyTable('tableRalanBpjs')" style="padding: 0px 5px;"><i class="fas fa-copy"></i> Copy</button>
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm text-xs mb-0" id="tableRalanBpjs">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th width="3%">No</th>
                                                <th width="15%">No. Rawat</th>
                                                <th width="20%">Nama Pasien</th>
                                                <th width="12%">Penjamin</th>
                                                <th width="25%">Nama Perawatan</th>
                                                <th width="7%">Tgl Rawat</th>
                                                <th width="6%">Jam</th>
                                                <th width="12%">Tarif (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($detailsRalanBpjs as $item)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $item->no_rawat }}</td>
                                                    <td>{{ $item->nm_pasien }}</td>
                                                    <td>{{ $item->png_jawab ?? 'BPJS' }}</td>
                                                    <td>{{ $item->nm_perawatan }} <span class="text-muted">({{ $item->sumber ?? '-' }})</span></td>
                                                    <td class="text-center">{{ isset($item->tgl_perawatan) ? date('d-m-Y', strtotime($item->tgl_perawatan)) : '-' }}</td>
                                                    <td class="text-center">{{ $item->jam_rawat ?? '-' }}</td>
                                                    <td class="text-right">{{ number_format($item->tarif) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted font-italic">
                                                        Tidak ada data tindakan Rawat Jalan (Ralan) BPJS.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="7" class="text-right">TOTAL RAWAT JALAN (BPJS)</td>
                                                <td class="text-right text-secondary font-weight-bold">
                                                    Rp {{ number_format($totalRalanBpjs) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-light border text-center py-5">
                    <i class="fas fa-user-md fa-3x text-secondary mb-3"></i>
                    <h6 class="font-weight-bold text-dark">Silakan Pilih Dokter / Petugas</h6>
                    <p class="text-muted text-xs mb-0">Pilih dokter/petugas, tentukan rentang tanggal, dan pilih penjamin (Umum / Asuransi) untuk melihat tabel rincian serta mencetak laporan PDF.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Download PDF Banyak Dokter --}}
    <div class="modal fade" id="modalDownloadBanyakDokter" tabindex="-1" role="dialog" aria-labelledby="modalDownloadBanyakDokterLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ url('/pdf-tindakan') }}" method="GET" target="_blank">
                    <input type="hidden" name="filter_submitted" value="1">
                    <input type="hidden" name="export" value="zip">
                    
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="modalDownloadBanyakDokterLabel">Cetak PDF Banyak Dokter</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-xs">Tanggal Awal:</label>
                                <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tanggl1 }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-xs">Tanggal Akhir:</label>
                                <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tanggl2 }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="font-weight-bold text-xs d-block">Pilihan Penjamin / Tindakan:</label>
                            <div class="d-flex align-items-center pt-1">
                                <div class="custom-control custom-checkbox mr-3">
                                    <input class="custom-control-input" type="checkbox" id="modalCheckUmum" name="jenis[]" value="umum" {{ in_array('umum', $selectedJenis) ? 'checked' : '' }}>
                                    <label for="modalCheckUmum" class="custom-control-label font-weight-bold text-xs">Umum</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3">
                                    <input class="custom-control-input" type="checkbox" id="modalCheckAsuransi" name="jenis[]" value="asuransi" {{ in_array('asuransi', $selectedJenis) ? 'checked' : '' }}>
                                    <label for="modalCheckAsuransi" class="custom-control-label font-weight-bold text-xs">Asuransi</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3">
                                    <input class="custom-control-input" type="checkbox" id="modalCheckInhealth" name="jenis[]" value="inhealth" {{ in_array('inhealth', $selectedJenis) ? 'checked' : '' }}>
                                    <label for="modalCheckInhealth" class="custom-control-label font-weight-bold text-xs">Inhealth</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="modalCheckBpjs" name="jenis[]" value="bpjs" {{ in_array('bpjs', $selectedJenis) ? 'checked' : '' }}>
                                    <label for="modalCheckBpjs" class="custom-control-label font-weight-bold text-xs">BPJS</label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <label class="font-weight-bold text-xs d-block mb-2">Pilih Dokter / Petugas (Bisa Lebih Dari Satu):</label>
                        <div class="d-flex mb-2">
                            <button type="button" class="btn btn-xs btn-outline-primary mr-2" id="btnSelectAllDokter">Pilih Semua</button>
                            <button type="button" class="btn btn-xs btn-outline-danger" id="btnDeselectAllDokter">Hapus Semua</button>
                        </div>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            {{-- DOKTER SPESIALIS --}}
                            @if(count($listDokterSpesialis) > 0)
                                <h6 class="font-weight-bold text-primary mb-2 mt-2 border-bottom pb-1">Dokter Spesialis</h6>
                                <div class="row">
                                    @foreach ($listDokterSpesialis as $doc)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input chk-dokter" type="checkbox" name="kd_dokter[]" value="{{ $doc['id_khanza'] }}" id="modaldoc_{{ $doc['id_khanza'] }}">
                                                <label class="custom-control-label text-xs" style="cursor:pointer;" for="modaldoc_{{ $doc['id_khanza'] }}">
                                                    {{ $doc['nama'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- DOKTER UMUM --}}
                            @if(count($listDokterUmum) > 0)
                                <h6 class="font-weight-bold text-success mb-2 mt-3 border-bottom pb-1">Dokter Umum</h6>
                                <div class="row">
                                    @foreach ($listDokterUmum as $doc)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input chk-dokter" type="checkbox" name="kd_dokter[]" value="{{ $doc['id_khanza'] }}" id="modaldoc_{{ $doc['id_khanza'] }}">
                                                <label class="custom-control-label text-xs" style="cursor:pointer;" for="modaldoc_{{ $doc['id_khanza'] }}">
                                                    {{ $doc['nama'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- TIM HD --}}
                            @if(count($listPetugasHD) > 0)
                                <h6 class="font-weight-bold text-primary mb-2 mt-3 border-bottom pb-1">Tim Hemodialisa (HD)</h6>
                                <div class="row">
                                    @foreach ($listPetugasHD as $doc)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input chk-dokter" type="checkbox" name="kd_dokter[]" value="{{ $doc['id_khanza'] }}" id="modaldoc_{{ $doc['id_khanza'] }}">
                                                <label class="custom-control-label text-xs" style="cursor:pointer;" for="modaldoc_{{ $doc['id_khanza'] }}">
                                                    {{ $doc['nama'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(count($listPetugas) > 0)
                                <h6 class="font-weight-bold text-info mb-2 mt-3 border-bottom pb-1">Petugas / Paramedis</h6>
                                <div class="row">
                                    @foreach ($listPetugas as $doc)
                                        <div class="col-md-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input chk-dokter" type="checkbox" name="kd_dokter[]" value="{{ $doc['id_khanza'] }}" id="modaldoc_{{ $doc['id_khanza'] }}">
                                                <label class="custom-control-label text-xs" style="cursor:pointer;" for="modaldoc_{{ $doc['id_khanza'] }}">
                                                    {{ $doc['nama'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger btn-sm" id="btnDownloadMultiple">
                            <i class="fas fa-file-pdf mr-1"></i> Download PDF (Terpisah)
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btnDownloadZip">
                            <i class="fas fa-file-archive mr-1"></i> Download ZIP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Pilih Dokter / Petugas --',
                    allowClear: true
                });
            }

            $('#btnSelectAllDokter').on('click', function() {
                $('.chk-dokter').prop('checked', true);
            });
            $('#btnDeselectAllDokter').on('click', function() {
                $('.chk-dokter').prop('checked', false);
            });

            $('#btnDownloadZip').on('click', function(e) {
                let form = $('#modalDownloadBanyakDokter form');
                let dokters = form.find('input[name="kd_dokter[]"]:checked');
                if (dokters.length === 0) {
                    alert('Silakan pilih minimal satu dokter.');
                    return;
                }
                form.find('input[name="export"]').val('zip');
                form[0].submit();
                $('#modalDownloadBanyakDokter').modal('hide');
            });

            $('#btnDownloadMultiple').on('click', function(e) {
                let form = $('#modalDownloadBanyakDokter form');
                let tgl1 = form.find('input[name="tgl1"]').val();
                let tgl2 = form.find('input[name="tgl2"]').val();
                
                let jenis = [];
                form.find('input[name="jenis[]"]:checked').each(function() {
                    jenis.push($(this).val());
                });
                
                let dokters = [];
                form.find('input[name="kd_dokter[]"]:checked').each(function() {
                    dokters.push($(this).val());
                });
                
                if (dokters.length === 0) {
                    alert('Silakan pilih minimal satu dokter.');
                    return;
                }
                
                let jenisQuery = '';
                jenis.forEach(function(j) { jenisQuery += '&jenis[]=' + j; });
                
                let delay = 0;
                dokters.forEach(function(kd_dokter) {
                    let url = "{{ url('/pdf-tindakan') }}?filter_submitted=1&export=pdf&action=download&kd_dokter=" + kd_dokter + "&tgl1=" + tgl1 + "&tgl2=" + tgl2 + jenisQuery;
                    setTimeout(function() {
                        let iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        iframe.src = url;
                        document.body.appendChild(iframe);
                    }, delay);
                    delay += 800; // sedikit diperlambat
                });
                
                $('#modalDownloadBanyakDokter').modal('hide');
            });
        });

        function copyTable(tableId) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const lines = [];
            table.querySelectorAll('thead tr').forEach(row => {
                const cells = [];
                row.querySelectorAll('th').forEach(cell => cells.push(cell.innerText.trim()));
                lines.push(cells.join('\t'));
            });
            table.querySelectorAll('tbody tr, tfoot tr').forEach(row => {
                const cells = [];
                row.querySelectorAll('td').forEach(cell => {
                    const colspan = parseInt(cell.getAttribute('colspan')) || 1;
                    cells.push(cell.innerText.trim());
                    for (let i = 1; i < colspan; i++) cells.push('');
                });
                lines.push(cells.join('\t'));
            });
            const text = lines.join('\n');
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => alert("Tabel berhasil disalin."));
            } else {
                const ta = document.createElement('textarea');
                ta.value = text; ta.style.position = 'fixed'; ta.style.left = '-9999px';
                document.body.appendChild(ta); ta.select();
                try { document.execCommand('copy'); alert("Tabel berhasil disalin."); } catch(e) {}
                document.body.removeChild(ta);
            }
        }
    </script>
    @endpush
@endsection
