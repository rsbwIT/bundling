@extends('..layout.layoutDashboard')
@section('title', 'Bayar Piutang')

@section('konten')
<div class="card shadow-sm border-0">
    <div class="card-body">

        {{-- 🔍 Komponen Pencarian --}}
        @include('laporan.component.search-bayarPiutang')

        {{-- 🔹 Header Atas --}}
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <div><strong>Jumlah Data :</strong> {{ count($bayarPiutang) }}</div>
            <button type="button" class="btn btn-sm btn-primary" id="copyButton">
                <i class="fas fa-copy"></i> Copy Table
            </button>
        </div>

        {{-- 🔹 Pagination --}}
        <nav aria-label="Page navigation">
            {{ $bayarPiutang->appends(request()->input())->links('pagination::bootstrap-4') }}
        </nav>

        {{-- 🔹 Tabel Data --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm align-middle text-sm" id="tableToCopy" style="white-space: nowrap;">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th>No</th>
                        <th>Tgl Bayar</th>
                        <th>No RM</th>
                        <th>Status Lanjut</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Bayar</th>
                        <th>No Nota</th>
                        <th>Registrasi</th>
                        <th>Obat+Emb+Tsl</th>
                        <th>Retur Obat</th>
                        <th>Resep Pulang</th>
                        <th>Paket Tindakan</th>
                        <th>Operasi</th>
                        <th>Laborat</th>
                        <th>Radiologi</th>
                        <th>Tambahan</th>
                        <th>Kamar+Service</th>
                        <th>Potongan</th>
                        <th>Total</th>
                        <th>Ekses / Uang Muka</th>
                        <th>Cicilan</th>
                        <th>Diskon Bayar</th>
                        <th>Tidak Terbayar</th>
                        <th>Catatan</th>
                        <th>No.Rawat / No.Tagihan</th>
                        <th>No.SEP</th>
                        <th>Status</th>
                        <th>Dokter</th>
                        <th>Paramedis</th>
                        <th>Dokter Paramedis</th>
                        <th>Total Paket Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($bayarPiutang as $item)
                        @php
                            $totalDokter = $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya');
                            $totalParamedis = $item->getRalanParamedis->sum('totalbiaya') + $item->getRanapParamedis->sum('totalbiaya');
                            $totalDrParamedis = $item->getRalanDrParamedis->sum('totalbiaya') + $item->getRanapDrParamedis->sum('totalbiaya');
                            $totalPaket = $totalDokter + $totalParamedis + $totalDrParamedis;
                        @endphp
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->tgl_bayar }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->status_lanjut }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>
                                @foreach ($item->getNomorNota as $detail)
                                    {{ str_replace(':', '', $detail->nm_perawatan) }}
                                @endforeach
                            </td>

                            {{-- ✅ Nilai-nilai utama --}}
                            <td>{{ number_format($item->getRegistrasi->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getObat->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getReturObat->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getResepPulang->sum('totalbiaya')) }}</td>
                            <td>{{ number_format(
                                $item->getRalanDokter->sum('totalbiaya')
                                + $item->getRalanParamedis->sum('totalbiaya')
                                + $item->getRalanDrParamedis->sum('totalbiaya')
                                + $item->getRanapDokter->sum('totalbiaya')
                                + $item->getRanapDrParamedis->sum('totalbiaya')
                                + $item->getRanapParamedis->sum('totalbiaya')
                            ) }}</td>
                            <td>{{ number_format($item->getOprasi->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getLaborat->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getRadiologi->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getTambahan->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getKamarInap->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getPotongan->sum('totalbiaya')) }}</td>
                            <td><strong>
                                {{ number_format(
                                    $item->getRegistrasi->sum('totalbiaya')
                                    + $item->getObat->sum('totalbiaya')
                                    + $item->getReturObat->sum('totalbiaya')
                                    + $item->getResepPulang->sum('totalbiaya')
                                    + $item->getRalanDokter->sum('totalbiaya')
                                    + $item->getRalanParamedis->sum('totalbiaya')
                                    + $item->getRalanDrParamedis->sum('totalbiaya')
                                    + $item->getRanapDokter->sum('totalbiaya')
                                    + $item->getRanapDrParamedis->sum('totalbiaya')
                                    + $item->getRanapParamedis->sum('totalbiaya')
                                    + $item->getOprasi->sum('totalbiaya')
                                    + $item->getLaborat->sum('totalbiaya')
                                    + $item->getRadiologi->sum('totalbiaya')
                                    + $item->getTambahan->sum('totalbiaya')
                                    + $item->getKamarInap->sum('totalbiaya')
                                    + $item->getPotongan->sum('totalbiaya')
                                ) }}
                            </strong></td>
                            <td>{{ number_format($item->uangmuka) }}</td>
                            <td>{{ number_format($item->besar_cicilan) }}</td>
                            <td>{{ number_format($item->diskon_piutang) }}</td>
                            <td>{{ number_format($item->tidak_terbayar) }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>
                                @foreach ($item->getNoSep as $detail)
                                    {{ $detail->no_sep }}
                                @endforeach
                            </td>
                            <td>{{ $item->status }}</td>

                            {{-- 🔹 Dokter & Paramedis terpisah --}}
                            <td class="text-end text-primary fw-semibold">{{ number_format($totalDokter) }}</td>
                            <td class="text-end text-success fw-semibold">{{ number_format($totalParamedis) }}</td>
                            <td class="text-end text-secondary fw-semibold">{{ number_format($totalDrParamedis) }}</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($totalPaket) }}</td>
                        </tr>
                    @endforeach

                    {{-- 🔹 Total Footer --}}
                    <tr class="table-primary fw-bold text-center align-middle">
                        <td colspan="7">Total</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getRegistrasi->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getObat->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getReturObat->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getResepPulang->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) =>
                            $i->getRalanDokter->sum('totalbiaya')
                            + $i->getRalanParamedis->sum('totalbiaya')
                            + $i->getRalanDrParamedis->sum('totalbiaya')
                            + $i->getRanapDokter->sum('totalbiaya')
                            + $i->getRanapDrParamedis->sum('totalbiaya')
                            + $i->getRanapParamedis->sum('totalbiaya')
                        )) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getOprasi->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getLaborat->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getRadiologi->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getTambahan->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getKamarInap->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->getPotongan->sum('totalbiaya'))) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) =>
                            $i->getRegistrasi->sum('totalbiaya')
                            + $i->getObat->sum('totalbiaya')
                            + $i->getReturObat->sum('totalbiaya')
                            + $i->getResepPulang->sum('totalbiaya')
                            + $i->getRalanDokter->sum('totalbiaya')
                            + $i->getRalanParamedis->sum('totalbiaya')
                            + $i->getRalanDrParamedis->sum('totalbiaya')
                            + $i->getRanapDokter->sum('totalbiaya')
                            + $i->getRanapDrParamedis->sum('totalbiaya')
                            + $i->getRanapParamedis->sum('totalbiaya')
                            + $i->getOprasi->sum('totalbiaya')
                            + $i->getLaborat->sum('totalbiaya')
                            + $i->getRadiologi->sum('totalbiaya')
                            + $i->getTambahan->sum('totalbiaya')
                            + $i->getKamarInap->sum('totalbiaya')
                            + $i->getPotongan->sum('totalbiaya')
                        )) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->uangmuka)) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->besar_cicilan)) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->diskon_piutang)) }}</td>
                        <td>{{ number_format($bayarPiutang->sum(fn($i) => $i->tidak_terbayar)) }}</td>
                        <td colspan="4"></td>
                        {{-- ✅ Total Dokter & Paramedis --}}
                        <td class="text-end text-primary">{{ number_format($bayarPiutang->sum(fn($i) => $i->getRalanDokter->sum('totalbiaya') + $i->getRanapDokter->sum('totalbiaya'))) }}</td>
                        <td class="text-end text-success">{{ number_format($bayarPiutang->sum(fn($i) => $i->getRalanParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya'))) }}</td>
                        <td class="text-end text-secondary">{{ number_format($bayarPiutang->sum(fn($i) => $i->getRalanDrParamedis->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya'))) }}</td>
                        <td class="text-end text-danger">{{ number_format($bayarPiutang->sum(fn($i) =>
                            ($i->getRalanDokter->sum('totalbiaya') + $i->getRanapDokter->sum('totalbiaya'))
                            + ($i->getRalanParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya'))
                            + ($i->getRalanDrParamedis->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya'))
                        )) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById("copyButton").addEventListener("click", function () {
    const table = document.getElementById("tableToCopy");
    let tsv = "";

    // Hitung jumlah kolom di header
    const header = table.querySelector("thead tr");
    const totalCols = header ? header.querySelectorAll("th").length : 0;

    // Ambil semua baris tabel
    const rows = table.querySelectorAll("tr");
    rows.forEach(row => {
        const cols = row.querySelectorAll("th, td");
        let rowData = Array.from(cols).flatMap(col => {
            const span = parseInt(col.getAttribute("colspan") || 1);
            const text = col.innerText.replace(/\t/g, " ").replace(/\n/g, " ").trim();
            // kalau colspan > 1, isi dengan text pertama lalu kosong sisanya
            return [text, ...Array(span - 1).fill("")];
        });

        // Jika jumlah kolom baris < header, tambahkan kolom kosong
        while (rowData.length < totalCols) rowData.push("");

        tsv += rowData.join("\t") + "\n";
    });

    // Salin ke clipboard
    const textarea = document.createElement("textarea");
    textarea.value = tsv;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);

    alert("✅ Data berhasil disalin, Paste langsung ke Excel");
});
</script>

{{-- ✅ Style --}}
<style>
.table th, .table td {
    vertical-align: middle !important;
    text-align: center;
    padding: 6px 10px !important;
}
.table th {
    background-color: #f8f9fa !important;
    font-weight: 600;
}
.table td.text-end {
    text-align: right !important;
}
</style>
@endsection
