@extends('..layout.layoutDashboard')
@section('title', 'Detail JM Umum - ' . $nmDokter)

@section('konten')
    @php
        $detailsRalan = $detailsRalan ?? $details->filter(fn($i) => stripos($i->status, 'Ralan') !== false)->values();
        $detailsRanap = $detailsRanap ?? $details->filter(fn($i) => stripos($i->status, 'Ranap') !== false)->values();
    @endphp

    <div class="card">
        <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 font-weight-bold">
                    <i class="fas fa-search-dollar mr-2"></i>
                    Detail Tindakan JM Umum: <strong>{{ $nmDokter }}</strong> ({{ $kdDokter }})
                </h5>
                <small>Periode: {{ date('d-m-Y', strtotime($tanggl1)) }} s/d {{ date('d-m-Y', strtotime($tanggl2)) }}</small>
            </div>
            <div>
                <a href="{{ url('/pdf-tindakan') }}?kd_dokter={{ $kdDokter }}&tgl1={{ $tanggl1 }}&tgl2={{ $tanggl2 }}&export=pdf" target="_blank" class="btn btn-sm btn-danger shadow-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <a href="javascript:history.back()" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Ringkasan Total Box --}}
            <div class="row mb-3">
                <div class="col-md-4 col-sm-12 mb-2">
                    <div class="info-box bg-info mb-0">
                        <span class="info-box-icon"><i class="fas fa-walking"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Rawat Jalan (Ralan)</span>
                            <span class="info-box-number">Rp {{ number_format($detailsRalan->sum('tarif')) }}</span>
                            <span class="progress-description">{{ count($detailsRalan) }} Tindakan</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 mb-2">
                    <div class="info-box bg-warning mb-0">
                        <span class="info-box-icon text-white"><i class="fas fa-bed"></i></span>
                        <div class="info-box-content text-white">
                            <span class="info-box-text">Rawat Inap (Ranap)</span>
                            <span class="info-box-number">Rp {{ number_format($detailsRanap->sum('tarif')) }}</span>
                            <span class="progress-description">{{ count($detailsRanap) }} Tindakan</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 mb-2">
                    <div class="info-box bg-success mb-0">
                        <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Grand Total Keseluruhan</span>
                            <span class="info-box-number">Rp {{ number_format($details->sum('tarif')) }}</span>
                            <span class="progress-description">{{ count($details) }} Total Tindakan</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 1. TABEL RAWAT JALAN (RALAN) --}}
            <div class="card card-outline card-info mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title font-weight-bold text-info mb-0">
                        <i class="fas fa-walking mr-1"></i> 1. Tindakan Rawat Jalan (Ralan)
                        <span class="badge badge-info ml-2">{{ count($detailsRalan) }} Data</span>
                    </h6>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-default" onclick="copyTable('tableRalan')">
                            <i class="fas fa-copy"></i> Copy Tabel Ralan
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped text-xs mb-0" style="white-space: nowrap;" id="tableRalan">
                            <thead style="background-color: #d1ecf1; color: #0c5460;">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>No Rawat</th>
                                    <th>Nama Pasien</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Nama Tindakan</th>
                                    <th>Sumber</th>
                                    <th class="text-right">Tarif (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $noRalan = 1; @endphp
                                @forelse ($detailsRalan as $item)
                                    <tr>
                                        <td class="text-center">{{ $noRalan++ }}</td>
                                        <td>{{ $item->no_rawat }}</td>
                                        <td>{{ $item->nm_pasien }}</td>
                                        <td>{{ $item->penjamin ?? '-' }}</td>
                                        <td>{{ $item->nm_perawatan }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $item->sumber }}</span>
                                        </td>
                                        <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3 text-muted">
                                            Tidak ada data tindakan Rawat Jalan (Ralan).
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot style="background-color: #bee5eb; font-weight: bold;">
                                <tr>
                                    <td colspan="6" class="text-right">SUBTOTAL RAWAT JALAN (RALAN)</td>
                                    <td class="text-right text-info font-weight-bold">
                                        Rp {{ number_format($detailsRalan->sum('tarif')) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 2. TABEL RAWAT INAP (RANAP) --}}
            <div class="card card-outline card-warning mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title font-weight-bold text-warning mb-0">
                        <i class="fas fa-bed mr-1"></i> 2. Tindakan Rawat Inap (Ranap)
                        <span class="badge badge-warning ml-2">{{ count($detailsRanap) }} Data</span>
                    </h6>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-default" onclick="copyTable('tableRanap')">
                            <i class="fas fa-copy"></i> Copy Tabel Ranap
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped text-xs mb-0" style="white-space: nowrap;" id="tableRanap">
                            <thead style="background-color: #fff3cd; color: #856404;">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>No Rawat</th>
                                    <th>Nama Pasien</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Nama Tindakan</th>
                                    <th>Sumber</th>
                                    <th class="text-right">Tarif (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $noRanap = 1; @endphp
                                @forelse ($detailsRanap as $item)
                                    <tr>
                                        <td class="text-center">{{ $noRanap++ }}</td>
                                        <td>{{ $item->no_rawat }}</td>
                                        <td>{{ $item->nm_pasien }}</td>
                                        <td>{{ $item->penjamin ?? '-' }}</td>
                                        <td>{{ $item->nm_perawatan }}</td>
                                        <td>
                                            <span class="badge badge-warning">{{ $item->sumber }}</span>
                                        </td>
                                        <td class="text-right font-weight-bold">{{ number_format($item->tarif) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3 text-muted">
                                            Tidak ada data tindakan Rawat Inap (Ranap).
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot style="background-color: #ffeeba; font-weight: bold;">
                                <tr>
                                    <td colspan="6" class="text-right">SUBTOTAL RAWAT INAP (RANAP)</td>
                                    <td class="text-right text-dark font-weight-bold">
                                        Rp {{ number_format($detailsRanap->sum('tarif')) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
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
