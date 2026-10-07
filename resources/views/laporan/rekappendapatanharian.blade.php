@extends('..layout.layoutDashboard')

@section('title', 'Rekap Pendapatan Harian - ' . ($statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Semua Pelayanan' : 'Rawat Inap')) . ' (' . $penjaminLabel . ')')

@section('konten')

<style>
    .table-rekap {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 11px;
        white-space: nowrap;
    }
    .table-rekap th {
        text-align: center;
        vertical-align: middle;
        text-transform: uppercase;
        background-color: #f4f6f9 !important;
        font-weight: bold;
        color: #333;
        padding: 8px 4px !important;
    }
    .table-rekap td {
        vertical-align: middle;
        padding: 6px 4px !important;
    }
    .table-rekap tbody td:nth-child(1) {
        text-align: center;
    }
    .table-rekap tbody td:nth-child(n+2) {
        text-align: right;
    }
</style>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark">
                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Rekap Pendapatan Harian ({{ $statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Rawat Inap & Jalan' : 'Rawat Inap') }}) - <span class="text-primary">{{ $penjaminLabel }}</span>
                </h5>
                <small class="text-muted">Laporan rincian pendapatan harian {{ $statusLanjut == 'Ralan' ? 'rawat jalan' : ($statusLanjut == 'SEMUA' ? 'rawat inap dan rawat jalan' : 'rawat inap') }} dengan penjamin {{ $penjaminLabel }} <span class="badge badge-light border ml-1">{{ $kdPj == 'UMU' ? 'Berdasarkan Tanggal Nota' : ($kdPj == 'SEMUA' ? 'Umum: Tgl Nota | BPJS & Asr: Tgl Bayar Piutang' : 'Berdasarkan Tanggal Pembayaran Piutang') }}</span></small>
            </div>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadExcel()">
                    <i class="fas fa-file-excel mr-1"></i> Download Excel
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Form Filter Periode & Jenis Pelayanan --}}
        <form method="GET" action="{{ url('/rekap-pendapatan-harian') }}" class="mb-4">
            <div class="row align-items-end">
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Awal:</label>
                    <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                </div>
                <div class="col-md-2 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Tanggal Akhir:</label>
                    <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Jenis Pelayanan:</label>
                    <select name="status_lanjut" class="form-control form-control-sm">
                        <option value="Ranap" {{ $statusLanjut == 'Ranap' ? 'selected' : '' }}>Rawat Inap (Ranap)</option>
                        <option value="Ralan" {{ $statusLanjut == 'Ralan' ? 'selected' : '' }}>Rawat Jalan (Ralan)</option>
                        <option value="SEMUA" {{ $statusLanjut == 'SEMUA' ? 'selected' : '' }}>-- Semua (Ranap & Ralan) --</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-xs">Penjamin:</label>
                    <select name="kd_pj" class="form-control form-control-sm select2">
                        <option value="UMU" {{ $kdPj == 'UMU' ? 'selected' : '' }}>Umum</option>
                        <option value="BPJ" {{ $kdPj == 'BPJ' ? 'selected' : '' }}>BPJS</option>
                        <option value="ASURANSI" {{ $kdPj == 'ASURANSI' ? 'selected' : '' }}>Asuransi</option>
                        <option value="INHEALTH" {{ $kdPj == 'INHEALTH' ? 'selected' : '' }}>Mandiri Inhealth & Askes Inhealth</option>
                        <option value="SEMUA" {{ $kdPj == 'SEMUA' ? 'selected' : '' }}>-- Semua Penjamin --</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-12 mb-2">
                    <button type="submit" class="btn btn-sm btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Tabel Rekap --}}
        @if($statusLanjut == 'SEMUA')
            <h6 class="font-weight-bold text-dark mb-2">RAWAT INAP (RANAP)</h6>
            @include('laporan.partials.table_rekap', ['dataRekapLocal' => $dataRekapRanap, 'statusLanjutLocal' => 'Ranap'])
            
            <h6 class="font-weight-bold text-dark mb-2 mt-4">RAWAT JALAN (RALAN)</h6>
            @php
                $totalSemua = $dataRekapRanap->sum('grand_total') + $dataRekapRalan->sum('grand_total');
            @endphp
            @include('laporan.partials.table_rekap', [
                'dataRekapLocal' => $dataRekapRalan, 
                'statusLanjutLocal' => 'Ralan',
                'showTotalKeseluruhan' => true,
                'totalKeseluruhan' => $totalSemua
            ])
        @else
            @include('laporan.partials.table_rekap', ['dataRekapLocal' => $dataRekap, 'statusLanjutLocal' => $statusLanjut])
        @endif

        @include('laporan.partials.summary_table')
    </div>
</div>

<script>
function downloadExcel() {
    let tables = document.querySelectorAll('.table-rekap');
    if (tables.length === 0) return;

    let html = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta charset="utf-8" />
        <style>
            table { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 11pt; }
            th, td { border: 1px solid black; padding: 4px; vertical-align: middle; }
            th { background-color: #f4f6f9; font-weight: bold; }
            .no-border { border: none !important; }
            /* Bootstrap Colors for Excel */
            .table-primary, .table-primary > th, .table-primary > td { background-color: #cfe2ff !important; }
            .table-secondary, .table-secondary > th, .table-secondary > td { background-color: #e2e3e5 !important; }
            .table-success, .table-success > th, .table-success > td { background-color: #d1e7dd !important; }
            .table-warning, .table-warning > th, .table-warning > td { background-color: #fff3cd !important; }
            .table-info, .table-info > th, .table-info > td { background-color: #cff4fc !important; }
            .table-light, .table-light > th, .table-light > td { background-color: #f8f9fa !important; }
            .table-dark, .table-dark > th, .table-dark > td { background-color: #212529 !important; color: #fff !important; }
            .bg-primary { background-color: #0d6efd !important; color: #fff !important; }
            .bg-success { background-color: #198754 !important; color: #fff !important; }
            .bg-info { background-color: #0dcaf0 !important; color: #000 !important; }
            .bg-warning { background-color: #ffc107 !important; color: #000 !important; }
            .bg-danger { background-color: #dc3545 !important; color: #fff !important; }
            .bg-light { background-color: #f8f9fa !important; }
            .text-dark { color: #212529 !important; }
            .text-muted { color: #6c757d !important; }
            .text-white { color: #fff !important; }
            .text-primary { color: #0d6efd !important; }
            .font-weight-bold { font-weight: bold !important; }
        </style>
    </head>
    <body>
    `;

    tables.forEach((table) => {
        let title = '';
        let titleElement = table.previousElementSibling;
        
        while (titleElement) {
            if (titleElement.tagName === 'H4' || titleElement.tagName === 'H5') {
                title = titleElement.innerText.trim();
                break;
            }
            titleElement = titleElement.previousElementSibling;
        }

        if (!title && table.parentElement) {
            let parentPrev = table.parentElement.previousElementSibling;
            while (parentPrev) {
                if (parentPrev.tagName === 'H4' || parentPrev.tagName === 'H5') {
                    title = parentPrev.innerText.trim();
                    break;
                }
                parentPrev = parentPrev.previousElementSibling;
            }
        }

        let maxCols = 0;
        for (let i = 0; i < table.rows.length; i++) {
            let cols = 0;
            for (let j = 0; j < table.rows[i].cells.length; j++) {
                cols += table.rows[i].cells[j].colSpan;
            }
            if (cols > maxCols) maxCols = cols;
        }

        @php
            $bulanIndo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
            $bln = date('m', strtotime($tgl1));
            $namaBulan = $bulanIndo[$bln] ?? '';
            $thn = date('Y', strtotime($tgl1));
            $judulRekap = "REKAP BULAN " . strtoupper($namaBulan) . " " . $thn;
            $pelayanan = $statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Rawat Inap & Jalan' : 'Rawat Inap');
        @endphp

        let kopSurat = `
            <tr>
                <td colspan="${maxCols}" class="no-border" align="center" style="text-align: center; vertical-align: middle;">
                    <h2 style="margin: 0; font-size: 16pt;">{{ $setting->nama_instansi }}</h2>
                    <p style="margin: 2px 0 0 0; font-size: 11pt;">{{ $setting->alamat_instansi }}, {{ $setting->kabupaten }}, {{ $setting->propinsi }}</p>
                    <p style="margin: 2px 0 0 0; font-size: 11pt;">Kontak: {{ $setting->kontak }} | Email: {{ $setting->email }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="${maxCols}" class="no-border" style="border-bottom: 2px solid black !important;"></td>
            </tr>
            <tr>
                <td colspan="${maxCols}" class="no-border" style="height: 10px;"></td>
            </tr>
            <tr>
                <td colspan="${maxCols}" class="no-border" align="center" style="text-align: center;">
                    <h3 style="margin: 0; font-size: 14pt;">{{ $judulRekap }}</h3>
                    <p style="margin: 2px 0 0 0; font-size: 11pt;">Jenis Pelayanan: {{ $pelayanan }} | Penjamin: {{ $penjaminLabel }}</p>
                    <h4 style="margin: 10px 0 5px 0; font-size: 12pt;">${title}</h4>
                </td>
            </tr>
            <tr>
                <td colspan="${maxCols}" class="no-border" style="height: 10px;"></td>
            </tr>
        `;

        let tempDiv = document.createElement('div');
        tempDiv.innerHTML = table.outerHTML;

        // Force Excel to respect alignments by injecting the HTML 'align' attribute directly
        let ths = tempDiv.querySelectorAll('th');
        ths.forEach(th => {
            th.setAttribute('align', 'center');
            th.style.textAlign = 'center';
        });

        let tds = tempDiv.querySelectorAll('td');
        tds.forEach(td => {
            let align = 'right'; // Default numbers to right
            
            if (td.style.textAlign) {
                align = td.style.textAlign; // Respect existing inline styles from summary table
            } else if (td.classList.contains('text-center') || td.classList.contains('no-border')) {
                align = 'center';
            } else if (td.classList.contains('text-left')) {
                align = 'left';
            }
            
            td.setAttribute('align', align);
            td.style.textAlign = align;
        });

        let tableHtml = tempDiv.innerHTML;
        // Insert kopSurat right after <thead>
        tableHtml = tableHtml.replace(/(<thead[^>]*>)/i, '$1' + kopSurat);
        

        
        html += tableHtml;
        html += '<br><br>';
    });

    html += `</body></html>`;

    let blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    let url = URL.createObjectURL(blob);
    
    let a = document.createElement('a');
    a.href = url;
    a.download = 'Rekap_Pendapatan_Harian.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>

@endsection
