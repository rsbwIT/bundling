@extends('..layout.layoutDashboard')
@section('title', 'Jasdok - Dokter Spesialis')

@section('konten')
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="card-title font-weight-bold mb-0 text-dark">
                <i class="fas fa-file-excel text-success mr-2"></i> Rekap Jasdok (Sebelum Pajak) - Dokter Spesialis
            </h5>
            <div class="card-tools d-flex align-items-center mt-2 mt-sm-0">
                <span class="badge badge-success px-2 py-1 mr-2" style="font-size: 11px;">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ $periodeLabel }}
                </span>
            </div>
        </div>

        <div class="card-body">
            {{-- Navigasi Tab Kategori (Sesuai Tab Sheet Excel) --}}
            <div class="mb-3 d-flex flex-wrap align-items-center justify-content-between border-bottom pb-2">
                <div class="btn-group btn-group-sm mb-2" role="group">
                    <button type="button" class="btn btn-success active font-weight-bold">
                        <i class="fas fa-user-md mr-1"></i> dr. Spesialis
                    </button>
                    <button type="button" class="btn btn-outline-secondary" disabled title="Akan datang">
                        dr. Jaga
                    </button>
                    <button type="button" class="btn btn-outline-secondary" disabled title="Akan datang">
                        HD
                    </button>
                    <button type="button" class="btn btn-outline-secondary" disabled title="Akan datang">
                        OK
                    </button>
                    <button type="button" class="btn btn-outline-secondary" disabled title="Akan datang">
                        Lain-lain
                    </button>
                    <button type="button" class="btn btn-outline-secondary" disabled title="Akan datang">
                        Total Jasdok
                    </button>
                </div>

                {{-- Form Filter Periode --}}
                <form action="{{ url('/pdf-tindakan/jasdok') }}" method="GET" class="form-inline mb-2">
                    <label class="mr-2 text-xs font-weight-bold text-muted">Periode:</label>
                    <select name="bulan" class="form-control form-control-sm mr-2" style="width: 130px;">
                        @foreach ($namaBulan as $blnKey => $blnVal)
                            <option value="{{ $blnKey }}" {{ $bulan == $blnKey ? 'selected' : '' }}>
                                {{ $blnVal }}
                            </option>
                        @endforeach
                    </select>
                    <select name="tahun" class="form-control form-control-sm mr-2" style="width: 90px;">
                        @for ($thn = date('Y') + 1; $thn >= 2020; $thn--)
                            <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>
                                {{ $thn }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary mr-1">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                    <button type="button" class="btn btn-sm btn-default" onclick="copyTable('tableJasdokSpesialis')">
                        <i class="fas fa-copy mr-1"></i> Copy Tabel
                    </button>
                </form>
            </div>

            {{-- Info Baris Sheet --}}
            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                <div class="text-xs text-muted">
                    Menampilkan daftar <strong>Dokter Spesialis (SP1 - SP{{ count($listDokterSpesialis) }})</strong> | Sheet: <code>CETAK SLIP JASDOK</code>
                </div>
                <div class="text-right">
                    <span class="badge badge-light border text-xs px-2 py-1">
                        <span class="d-inline-block mr-1" style="width: 10px; height: 10px; background-color: #f7d5d8; border: 1px solid #e0a4aa; vertical-align: middle;"></span>
                        Baris Khusus / Highlight Merah Muda
                    </span>
                </div>
            </div>

            {{-- Tabel Excel Rekap Jasdok --}}
            <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                <table class="table table-bordered table-hover text-xs mb-0" id="tableJasdokSpesialis" style="white-space: nowrap;">
                    {{-- Header Table --}}
                    <thead>
                        {{-- Baris Label Periode di atas Kolom Total --}}
                        <tr style="background-color: #f8f9fa;">
                            <th colspan="8" class="border-0"></th>
                            <th colspan="3" class="text-center font-weight-bold py-1" style="background-color: #e9ecef; border: 1px solid #adb5bd; font-size: 11px; letter-spacing: 0.5px;">
                                {{ $periodeLabel }}
                            </th>
                        </tr>
                        <tr style="background-color: #7cbd5c; color: #000000;">
                            <th class="text-center align-middle" width="3%" style="border: 1px solid #5a9e47;">No</th>
                            <th class="text-center align-middle" width="5%" style="border: 1px solid #5a9e47;">Kode</th>
                            <th class="align-middle" width="22%" style="border: 1px solid #5a9e47;">Nama</th>
                            <th class="text-center align-middle" width="9%" style="border: 1px solid #5a9e47;">UMUM</th>
                            <th class="text-center align-middle" width="11%" style="border: 1px solid #5a9e47;">ASS / PERUSAHAAN</th>
                            <th class="text-center align-middle" width="9%" style="border: 1px solid #5a9e47;">BPJS</th>
                            <th class="text-center align-middle" width="12%" style="border: 1px solid #5a9e47;">INHEALTH + INDEMNITY</th>
                            <th class="text-center align-middle" width="9%" style="border: 1px solid #5a9e47;">KEMENKES</th>
                            <th class="text-center align-middle font-weight-bold" width="10%" style="border: 1px solid #5a9e47; background-color: #6fad51;">TOTAL 1</th>
                            <th class="text-center align-middle" width="10%" style="border: 1px solid #5a9e47;">POLI UMUM</th>
                            <th class="text-center align-middle font-weight-bold" width="10%" style="border: 1px solid #5a9e47; background-color: #6fad51;">TOTAL 2</th>
                        </tr>
                    </thead>

                    {{-- Body Table --}}
                    <tbody>
                        @forelse ($listDokterSpesialis as $item)
                            <tr style="{{ $item->is_highlight ? 'background-color: #f7d5d8 !important;' : '' }}">
                                <td class="text-center align-middle" style="border: 1px solid #dee2e6;">{{ $item->no }}</td>
                                <td class="text-center align-middle font-weight-bold" style="border: 1px solid #dee2e6;">
                                    {{ $item->kode }}
                                </td>
                                <td class="align-middle" style="border: 1px solid #dee2e6;">
                                    <span class="font-weight-bold text-dark">{{ $item->nama }}</span>
                                    @if ($item->id_khanza)
                                        <small class="text-muted ml-1">({{ $item->id_khanza }})</small>
                                    @endif
                                </td>
                                <td class="text-right align-middle" style="border: 1px solid #dee2e6;">
                                    {{ $item->umum ? number_format($item->umum, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right align-middle" style="border: 1px solid #dee2e6;">
                                    {{ $item->asuransi ? number_format($item->asuransi, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right align-middle" style="border: 1px solid #dee2e6;">
                                    {{ $item->bpjs ? number_format($item->bpjs, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right align-middle" style="border: 1px solid #dee2e6;">
                                    {{ $item->inhealth ? number_format($item->inhealth, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right align-middle" style="border: 1px solid #dee2e6;">
                                    {{ $item->kemenkes ? number_format($item->kemenkes, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right align-middle font-weight-bold" style="border: 1px solid #dee2e6; background-color: {{ $item->is_highlight ? '#f0c4c8' : '#f8f9fa' }};">
                                    {{ $item->total1 ? number_format($item->total1, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right align-middle" style="border: 1px solid #dee2e6;">
                                    {{ $item->poli_umum ? number_format($item->poli_umum, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right align-middle font-weight-bold" style="border: 1px solid #dee2e6; background-color: {{ $item->is_highlight ? '#f0c4c8' : '#f8f9fa' }};">
                                    {{ $item->total2 ? number_format($item->total2, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    Tidak ada data dokter spesialis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- Footer Table --}}
                    <tfoot style="background-color: #e9ecef; font-weight: bold; border-top: 2px solid #5a9e47;">
                        <tr>
                            <td colspan="3" class="text-center align-middle font-weight-bold" style="border: 1px solid #adb5bd;">
                                TOTAL KESELURUHAN
                            </td>
                            <td class="text-right align-middle" style="border: 1px solid #adb5bd;">-</td>
                            <td class="text-right align-middle" style="border: 1px solid #adb5bd;">-</td>
                            <td class="text-right align-middle" style="border: 1px solid #adb5bd;">-</td>
                            <td class="text-right align-middle" style="border: 1px solid #adb5bd;">-</td>
                            <td class="text-right align-middle" style="border: 1px solid #adb5bd;">-</td>
                            <td class="text-right align-middle font-weight-bold" style="border: 1px solid #adb5bd; background-color: #dee2e6;">-</td>
                            <td class="text-right align-middle" style="border: 1px solid #adb5bd;">-</td>
                            <td class="text-right align-middle font-weight-bold" style="border: 1px solid #adb5bd; background-color: #dee2e6;">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyTable(tableId) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const lines = [];

            // Header columns
            const headerCells = [];
            table.querySelectorAll('thead tr:last-child th').forEach(th => {
                headerCells.push(th.innerText.trim());
            });
            lines.push(headerCells.join('\t'));

            // Body rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const rowCells = [];
                tr.querySelectorAll('td').forEach(td => {
                    rowCells.push(td.innerText.trim());
                });
                if (rowCells.length > 0) {
                    lines.push(rowCells.join('\t'));
                }
            });

            // Footer row
            table.querySelectorAll('tfoot tr').forEach(tr => {
                const footerCells = [];
                tr.querySelectorAll('td').forEach(td => {
                    const colspan = parseInt(td.getAttribute('colspan')) || 1;
                    footerCells.push(td.innerText.trim());
                    for (let i = 1; i < colspan; i++) footerCells.push('');
                });
                lines.push(footerCells.join('\t'));
            });

            const text = lines.join('\n');
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Tabel berhasil disalin ke clipboard! Siap di-paste ke Excel / Spreadsheet.');
                });
            } else {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    alert('Tabel berhasil disalin ke clipboard! Siap di-paste ke Excel / Spreadsheet.');
                } catch (e) {
                    alert('Gagal menyalin tabel.');
                }
                document.body.removeChild(ta);
            }
        }
    </script>
    @endpush
@endsection
