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
                            @foreach ($listDokter as $doc)
                                <option value="{{ $doc['id_khanza'] }}" {{ $kdDokter == $doc['id_khanza'] ? 'selected' : '' }}>
                                    {{ $doc['nama'] }} ({{ $doc['id_khanza'] }}) [{{ $doc['kode'] }}]
                                </option>
                            @endforeach
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
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="checkAsuransi" name="jenis[]" value="asuransi" {{ in_array('asuransi', $selectedJenis) ? 'checked' : '' }}>
                                <label for="checkAsuransi" class="custom-control-label font-weight-bold text-xs">Asuransi</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-2 d-flex">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill mr-1">
                            <i class="fas fa-search mr-1"></i> Tampilkan
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
                        <div class="{{ in_array('asuransi', $selectedJenis) ? 'col-md-4' : 'col-md-6' }} col-sm-12 mb-2">
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
                        <div class="{{ in_array('umum', $selectedJenis) ? 'col-md-4' : 'col-md-6' }} col-sm-12 mb-2">
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
                    <div class="{{ in_array('umum', $selectedJenis) && in_array('asuransi', $selectedJenis) ? 'col-md-4' : 'col-md-6' }} col-sm-12 mb-2">
                        <div class="info-box bg-success mb-0">
                            <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">GRAND TOTAL KESELURUHAN</span>
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
                                                <td>{{ $item->penjamin ?? '-' }}</td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-warning">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Inap (Ranap) Umum.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #ffeeba; font-weight: bold;">
                                        <tr>
                                            <td colspan="6" class="text-right">TOTAL RAWAT INAP (UMUM)</td>
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
                                                <td>{{ $item->penjamin ?? '-' }}</td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-info">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Jalan (Ralan) Umum.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #bee5eb; font-weight: bold;">
                                        <tr>
                                            <td colspan="6" class="text-right">TOTAL RAWAT JALAN (UMUM)</td>
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
                                                <td><span class="badge badge-secondary">{{ $item->penjamin ?? '-' }}</span></td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-warning">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Inap (Ranap) Asuransi.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #ffd6b3; font-weight: bold;">
                                        <tr>
                                            <td colspan="6" class="text-right">TOTAL RAWAT INAP (ASURANSI)</td>
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
                                                <td><span class="badge badge-secondary">{{ $item->penjamin ?? '-' }}</span></td>
                                                <td>{{ $item->nm_perawatan }}</td>
                                                <td><span class="badge badge-primary">{{ $item->sumber }}</span></td>
                                                <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted">
                                                    Tidak ada data tindakan Rawat Jalan (Ralan) Asuransi.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot style="background-color: #b8daff; font-weight: bold;">
                                        <tr>
                                            <td colspan="6" class="text-right">TOTAL RAWAT JALAN (ASURANSI)</td>
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

            @else
                <div class="alert alert-light border text-center py-5">
                    <i class="fas fa-user-md fa-3x text-secondary mb-3"></i>
                    <h6 class="font-weight-bold text-dark">Silakan Pilih Dokter / Petugas</h6>
                    <p class="text-muted text-xs mb-0">Pilih dokter/petugas, tentukan rentang tanggal, dan pilih penjamin (Umum / Asuransi) untuk melihat tabel rincian serta mencetak laporan PDF.</p>
                </div>
            @endif
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
