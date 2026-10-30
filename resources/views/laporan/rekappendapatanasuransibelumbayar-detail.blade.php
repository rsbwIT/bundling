@extends('layout.layoutDashboard')

@section('title', 'Detail Tindakan Asuransi Belum Bayar')

@section('konten')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 text-danger font-weight-bold">
                    <i class="fas fa-list-alt mr-2"></i>Detail Tindakan Asuransi Belum Bayar
                </h5>
                <small class="text-muted">Rincian Perawatan ({{ $status_lanjut }} - Bulan Nota: {{ \Carbon\Carbon::parse($tgl_nota)->translatedFormat('F Y') }})</small>
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
                    <button class="nav-link active" id="dr-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#dr" data-bs-target="#dr" type="button" role="tab" aria-controls="dr" aria-selected="true">Tindakan DR</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pr-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#pr" data-bs-target="#pr" type="button" role="tab" aria-controls="pr" aria-selected="false">Tindakan PR</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="drpr-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#drpr" data-bs-target="#drpr" type="button" role="tab" aria-controls="drpr" aria-selected="false">Tindakan DRPR</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lab-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#lab" data-bs-target="#lab" type="button" role="tab" aria-controls="lab" aria-selected="false">Laboratorium</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ro-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#ro" data-bs-target="#ro" type="button" role="tab" aria-controls="ro" aria-selected="false">Radiologi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ok-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#ok" data-bs-target="#ok" type="button" role="tab" aria-controls="ok" aria-selected="false">Operasi (OK)</button>
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
                                    <td>{{ $item->tgl_tindakan }}</td>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td class="text-right">{{ number_format($item->js, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso, 0, ',', '.') }}</td>
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
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Petugas/Dokter</th><th>JS</th><th>BHP</th><th>PR</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailPr as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_petugas ?: $item->nm_dokter }}</td>
                                    <td class="text-right">{{ number_format($item->js, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->pr ?: $item->jm_dr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso, 0, ',', '.') }}</td>
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
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Dokter</th><th>Petugas</th><th>JS</th><th>BHP</th><th>JM DR</th><th>PR</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailDrpr as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td>{{ $item->nm_petugas }}</td>
                                    <td class="text-right">{{ number_format($item->js, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->pr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB LAB -->
                <div class="tab-pane fade p-3" id="lab" role="tabpanel" aria-labelledby="lab-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tableLab')"><i class="fas fa-copy"></i> Copy Laboratorium</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tableLab">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Dokter</th><th>Petugas</th><th>JS</th><th>BHP</th><th>JM DR</th><th>PR</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailLab as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td>{{ $item->nm_petugas }}</td>
                                    <td class="text-right">{{ number_format($item->js, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->pr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso, 0, ',', '.') }}</td>
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
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Dokter</th><th>Petugas</th><th>JS</th><th>BHP</th><th>JM DR</th><th>Petugas</th><th>Perujuk</th><th>KSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailRo as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td>{{ $item->nm_petugas }}</td>
                                    <td class="text-right">{{ number_format($item->js, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->petugas, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->perujuk, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB OK -->
                <div class="tab-pane fade p-3" id="ok" role="tabpanel" aria-labelledby="ok-tab">
                    <button class="btn btn-sm btn-outline-primary mb-3" onclick="copyTable('tableOk')"><i class="fas fa-copy"></i> Copy Operasi</button>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover align-middle" id="tableOk">
                            <thead class="table-secondary text-center">
                                <tr>
                                    <th>Tgl Tindakan</th><th>No Rawat</th><th>No RM</th><th>Nama Pasien</th><th>Nama Perawatan</th><th>Operator</th><th>JS</th><th>JM DR</th><th>JM PR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailOk as $item)
                                <tr>
                                    <td>{{ $item->tgl_tindakan }}</td>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->nm_perawatan }}</td>
                                    <td>{{ $item->operator }}</td>
                                    <td class="text-right">{{ number_format($item->js, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_dr, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->jm_pr, 0, ',', '.') }}</td>
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
    let table = document.getElementById(tableId);
    if (!table) return;
    let rows = Array.from(table.rows);
    let tsv = rows.map(r => Array.from(r.cells).map(c => {
        let text = c.innerText.trim();
        if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(text)) {
            return text.replace(/\./g, '').replace(',', '.');
        }
        return text;
    }).join('\t')).join('\n');
    
    navigator.clipboard.writeText(tsv).then(() => {
        alert('Tabel berhasil disalin ke clipboard! Format angka telah disesuaikan untuk Excel.');
    }).catch(err => {
        alert('Gagal menyalin tabel: ' + err);
    });
}

function exportToExcel() {
    let tabs = ['tableDr', 'tablePr', 'tableDrpr', 'tableLab', 'tableRo', 'tableOk'];
    let tabNames = ['Tindakan DR', 'Tindakan PR', 'Tindakan DRPR', 'Laboratorium', 'Radiologi', 'Operasi'];
    
    let html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head><meta charset="utf-8">
    <style>
        table { border-collapse: collapse; font-family: Calibri, sans-serif; font-size: 11pt; }
        th, td { border: 0.5pt solid #999; padding: 4px 8px; }
        th { background-color: #F8D7DA; font-weight: bold; text-align: center; }
        .num { mso-number-format: "\\#\\,\\#\\#0"; text-align: right; }
    </style>
    </head><body>`;
    
    tabs.forEach((id, idx) => {
        let t = document.getElementById(id);
        if (t) {
            let clone = t.cloneNode(true);
            clone.querySelectorAll('td').forEach(cell => {
                let text = cell.innerText.trim();
                if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(text)) {
                    cell.innerText = text.replace(/\./g, '').replace(',', '.');
                    cell.classList.add('num');
                }
            });
            html += `<h3>${tabNames[idx]}</h3>` + clone.outerHTML + `<br><br>`;
        }
    });
    
    html += `</body></html>`;
    let blob = new Blob([html], { type: "application/vnd.ms-excel;charset=utf-8" });
    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    a.download = `Detail_Tindakan_Asuransi_Belum_Bayar_{{ $tgl_nota }}_{{ $status_lanjut }}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>
@endsection
