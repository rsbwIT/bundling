@extends('layout.layoutDashboard')

@section('title', 'Detail Tindakan Asuransi')

@section('konten')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 text-primary font-weight-bold">
                    <i class="fas fa-list-alt mr-2"></i>Detail Tindakan Asuransi
                </h5>
                <small class="text-muted">Rincian Perawatan ({{ $status_lanjut }} - Nota: {{ \Carbon\Carbon::parse($tgl_nota)->format('F Y') }})</small>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-success mr-2" onclick="exportToExcel()">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel Semua Tab
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.close()">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- TABS NAVIGATION -->
            <ul class="nav nav-tabs px-3 pt-3" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="dr-tab" data-bs-toggle="tab" data-bs-target="#dr" type="button" role="tab" aria-controls="dr" aria-selected="true">Tindakan DR</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pr-tab" data-bs-toggle="tab" data-bs-target="#pr" type="button" role="tab" aria-controls="pr" aria-selected="false">Tindakan PR</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="drpr-tab" data-bs-toggle="tab" data-bs-target="#drpr" type="button" role="tab" aria-controls="drpr" aria-selected="false">Tindakan DRPR</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lab-tab" data-bs-toggle="tab" data-bs-target="#lab" type="button" role="tab" aria-controls="lab" aria-selected="false">Laboratorium</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ro-tab" data-bs-toggle="tab" data-bs-target="#ro" type="button" role="tab" aria-controls="ro" aria-selected="false">Radiologi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ok-tab" data-bs-toggle="tab" data-bs-target="#ok" type="button" role="tab" aria-controls="ok" aria-selected="false">Operasi (OK)</button>
                </li>
            </ul>
            
            <div class="tab-content" id="myTabContent">
                <!-- TAB DR -->
                <div class="tab-pane fade show active p-3" id="dr" role="tabpanel" aria-labelledby="dr-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tableDr')"><i class="fas fa-copy"></i> Copy Tindakan DR</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tableDr">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Petugas/Dokter</th><th>JS</th><th>BHP</th><th>JM DR</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailDr as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td><td>{{ $item->no_rawat }}</td><td>{{ $item->no_rkm_medis }}</td><td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->operator }}</td>
                                    <td class="text-right">{{ number_format($item->js,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB PR -->
                <div class="tab-pane fade p-3" id="pr" role="tabpanel" aria-labelledby="pr-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tablePr')"><i class="fas fa-copy"></i> Copy Tindakan PR</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tablePr">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Petugas/Dokter</th><th>JS</th><th>BHP</th><th>JM PR</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailPr as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td><td>{{ $item->no_rawat }}</td><td>{{ $item->no_rkm_medis }}</td><td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->operator }}</td>
                                    <td class="text-right">{{ number_format($item->js,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->pr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB DRPR -->
                <div class="tab-pane fade p-3" id="drpr" role="tabpanel" aria-labelledby="drpr-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tableDrpr')"><i class="fas fa-copy"></i> Copy Tindakan DRPR</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tableDrpr">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Dokter</th><th>Petugas</th><th>JS</th><th>BHP</th><th>JM DR</th><th>JM PR</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailDrpr as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td><td>{{ $item->no_rawat }}</td><td>{{ $item->no_rkm_medis }}</td><td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td>{{ $item->nm_petugas }}</td>
                                    <td class="text-right">{{ number_format($item->js,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->pr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB LAB -->
                <div class="tab-pane fade p-3" id="lab" role="tabpanel" aria-labelledby="lab-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tableLab')"><i class="fas fa-copy"></i> Copy LAB</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tableLab">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Pemeriksaan</th><th>Dokter</th><th>Petugas</th><th>JS</th><th>BHP</th><th>JM DR</th><th>JM PR</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailLab as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td><td>{{ $item->no_rawat }}</td><td>{{ $item->no_rkm_medis }}</td><td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td>{{ $item->nm_petugas }}</td>
                                    <td class="text-right">{{ number_format($item->js,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->pr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB RO -->
                <div class="tab-pane fade p-3" id="ro" role="tabpanel" aria-labelledby="ro-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tableRo')"><i class="fas fa-copy"></i> Copy Radiologi</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tableRo">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Pemeriksaan</th><th>Dokter</th><th>Petugas</th><th>JS</th><th>BHP</th><th>JM DR</th><th>JM Petugas</th><th>Perujuk</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailRo as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td><td>{{ $item->no_rawat }}</td><td>{{ $item->no_rkm_medis }}</td><td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td>{{ $item->nm_petugas }}</td>
                                    <td class="text-right">{{ number_format($item->js,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->petugas,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->perujuk,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB OK -->
                <div class="tab-pane fade p-3" id="ok" role="tabpanel" aria-labelledby="ok-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tableOk')"><i class="fas fa-copy"></i> Copy Operasi (OK)</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tableOk">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Operasi</th><th>Petugas/Dokter</th><th>JS</th><th>JM DR</th><th>JM PR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailOk as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td><td>{{ $item->no_rawat }}</td><td>{{ $item->no_rkm_medis }}</td><td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->operator }}</td>
                                    <td class="text-right">{{ number_format($item->js,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr,0,',','.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_pr,0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function copyTable(tableId) {
        var table = document.getElementById(tableId);
        var rows = table.rows;
        var tsv = "";
        
        for (var i = 0; i < rows.length; i++) {
            var rowArray = [];
            for (var j = 0; j < rows[i].cells.length; j++) {
                var cellText = rows[i].cells[j].innerText.trim();
                cellText = cellText.replace(/\n/g, " ");
                
                // Jika format angka dengan titik ribuan (misal 1.500.000)
                if (/^-?\d{1,3}(\.\d{3})*(,\d+)?$/.test(cellText)) {
                    cellText = cellText.replace(/\./g, "").replace(",", ".");
                }
                rowArray.push(cellText);
            }
            tsv += rowArray.join("\t") + "\n";
        }

        var tempInput = document.createElement("textarea");
        tempInput.style.position = "absolute";
        tempInput.style.left = "-9999px";
        tempInput.value = tsv;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);

        alert("Tabel berhasil disalin ke clipboard! Silakan paste (Ctrl+V) di Excel.");
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function exportToExcel() {
        var wb = XLSX.utils.book_new();
        var sheets = [
            { id: 'tableDr', name: 'Tindakan DR' },
            { id: 'tablePr', name: 'Tindakan PR' },
            { id: 'tableDrpr', name: 'Tindakan DRPR' },
            { id: 'tableLab', name: 'LAB' },
            { id: 'tableRo', name: 'Radiologi' },
            { id: 'tableOk', name: 'Operasi (OK)' }
        ];

        sheets.forEach(function(s) {
            var table = document.getElementById(s.id);
            if (!table) return;
            var rows = table.rows;
            var data = [];
            for (var i = 0; i < rows.length; i++) {
                var rowData = [];
                for (var j = 0; j < rows[i].cells.length; j++) {
                    var cellText = rows[i].cells[j].innerText.trim();
                    cellText = cellText.replace(/\n/g, " ");
                    
                    // bersihkan format angka
                    if (/^-?\d{1,3}(\.\d{3})*(,\d+)?$/.test(cellText)) {
                        var numericVal = parseFloat(cellText.replace(/\./g, "").replace(",", "."));
                        rowData.push(numericVal);
                    } else {
                        rowData.push(cellText);
                    }
                }
                data.push(rowData);
            }
            var ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, s.name);
        });

        var date = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(wb, "Detail_Tindakan_Asuransi_" + date + ".xlsx");
    }
</script>
<!-- Tambahkan Bootstrap 5 JS Bundle jika diperlukan untuk nav-tabs -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
