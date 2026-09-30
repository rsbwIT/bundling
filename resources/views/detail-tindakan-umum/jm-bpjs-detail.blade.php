@extends('..layout.layoutDashboard')
@section('title', 'Detail JM BPJS - ' . $nmDokter)

@section('konten')
    <div class="card">
        <div class="card-header bg-purple text-white">
            <h5 class="mb-0">
                <i class="fas fa-search-dollar mr-2"></i>
                Detail Tindakan JM BPJS: <strong>{{ $nmDokter }}</strong> ({{ $kdDokter }})
            </h5>
            <small>Periode: {{ $tanggl1 }} s/d {{ $tanggl2 }}</small>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="javascript:history.back()" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-sm btn-default" id="copyButton">
                        <i class="fas fa-copy"></i> Copy Table
                    </button>
                </div>
                <div>
                    <form action="" method="GET" class="form-inline">
                        <input type="hidden" name="tgl1" value="{{ request('tgl1') }}">
                        <input type="hidden" name="tgl2" value="{{ request('tgl2') }}">
                        <input type="hidden" name="kd_dokter" value="{{ request('kd_dokter') }}">
                        <input type="hidden" name="nm_dokter" value="{{ request('nm_dokter') }}">
                        <input type="hidden" name="penjamin" value="{{ request('penjamin', 'all') }}">
                        <label for="filter_status" class="mr-2">Filter Status:</label>
                        <select name="filter_status" id="filter_status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="all" {{ request('filter_status', 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="Ralan" {{ request('filter_status') == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                            <option value="Ranap" {{ request('filter_status') == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped text-xs" style="white-space: nowrap;" id="tableToCopy">
                    <thead style="background-color: #b4a7d6; color: black;">
                        <tr>
                            <th>No</th>
                            <th>No Rawat</th>
                            <th>Nama Pasien</th>
                            <th>Penanggung Jawab</th>
                            <th>Nama Tindakan</th>
                            <th>Sumber</th>
                            <th>Status</th>
                            <th class="text-right">Tarif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse ($details as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td>{{ $item->penjamin }}</td>
                                <td>{{ $item->nm_perawatan }}</td>
                                <td>
                                    <span class="badge {{ str_contains($item->sumber, 'Ralan') ? 'badge-info' : (str_contains($item->sumber, 'Operasi') ? 'badge-warning' : (str_contains($item->sumber, 'Radiologi') ? 'badge-secondary' : (str_contains($item->sumber, 'Lab') ? 'badge-dark' : 'badge-success'))) }}">
                                        {{ $item->sumber }}
                                    </span>
                                </td>
                                <td>{{ $item->status }}</td>
                                <td class="text-right">{{ number_format($item->tarif) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data tindakan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background-color: #e9ecef; font-weight: bold;">
                        <tr>
                            <td colspan="7" class="text-right">TOTAL</td>
                            <td class="text-right">{{ number_format($details->sum('tarif')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("copyButton").addEventListener("click", function() {
            const table = document.getElementById("tableToCopy");
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
        });
    </script>
@endsection
