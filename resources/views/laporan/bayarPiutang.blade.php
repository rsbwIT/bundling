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

        {{-- 🔥 TOTAL GLOBAL DI ATAS TABEL --}}
        <div class="mt-3 p-3 bg-light border rounded">

            @php
                $total_registrasi = $bayarPiutang->sum(fn($i) => $i->getRegistrasi->sum('totalbiaya'));
                $total_obat = $bayarPiutang->sum(fn($i) => $i->getObat->sum('totalbiaya'));
                $total_retur = $bayarPiutang->sum(fn($i) => $i->getReturObat->sum('totalbiaya'));
                $total_resep = $bayarPiutang->sum(fn($i) => $i->getResepPulang->sum('totalbiaya'));
                $total_paket = $bayarPiutang->sum(fn($i) =>
                    $i->getRalanDokter->sum('totalbiaya')
                    + $i->getRalanParamedis->sum('totalbiaya')
                    + $i->getRalanDrParamedis->sum('totalbiaya')
                    + $i->getRanapDokter->sum('totalbiaya')
                    + $i->getRanapDrParamedis->sum('totalbiaya')
                    + $i->getRanapParamedis->sum('totalbiaya')
                );
                $total_oprasi = $bayarPiutang->sum(fn($i) => $i->getOprasi->sum('totalbiaya'));
                $total_laborat = $bayarPiutang->sum(fn($i) => $i->getLaborat->sum('totalbiaya'));
                $total_radiologi = $bayarPiutang->sum(fn($i) => $i->getRadiologi->sum('totalbiaya'));
                $total_tambahan = $bayarPiutang->sum(fn($i) => $i->getTambahan->sum('totalbiaya'));
                $total_kamar = $bayarPiutang->sum(fn($i) => $i->getKamarInap->sum('totalbiaya'));
                $total_potongan = $bayarPiutang->sum(fn($i) => $i->getPotongan->sum('totalbiaya'));

                $grand_total =
                    $total_registrasi + $total_obat + $total_retur + $total_resep +
                    $total_paket + $total_oprasi + $total_laborat + $total_radiologi +
                    $total_tambahan + $total_kamar + $total_potongan;

                $total_cicilan = $bayarPiutang->sum('besar_cicilan');
                $total_diskon = $bayarPiutang->sum('diskon_piutang');
                $total_tidak_terbayar = $bayarPiutang->sum('tidak_terbayar');
                $total_uangmuka = $bayarPiutang->sum('uangmuka');
            @endphp

            <div class="rekap-container">

                <div class="rekap-item">
                    <div class="rekap-label">Total Biaya Seluruh</div>
                    <div class="rekap-value text-primary">
                        Rp {{ number_format($grand_total) }}
                    </div>
                </div>

                <div class="rekap-item">
                    <div class="rekap-label">Total Uang Muka</div>
                    <div class="rekap-value text-success">
                        Rp {{ number_format($total_uangmuka) }}
                    </div>
                </div>

                <div class="rekap-item">
                    <div class="rekap-label">Total Cicilan</div>
                    <div class="rekap-value text-info">
                        Rp {{ number_format($total_cicilan) }}
                    </div>
                </div>

                <div class="rekap-item">
                    <div class="rekap-label">Total Diskon</div>
                    <div class="rekap-value text-warning">
                        Rp {{ number_format($total_diskon) }}
                    </div>
                </div>

                <div class="rekap-item">
                    <div class="rekap-label">Total Tidak Terbayar</div>
                    <div class="rekap-value text-danger fw-bold">
                        Rp {{ number_format($total_tidak_terbayar) }}
                    </div>
                </div>

            </div>

        </div>

        {{-- 🔹 TABEL --}}
        <div class="table-responsive mt-3">
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
                            $td = $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya');
                            $tp = $item->getRalanParamedis->sum('totalbiaya') + $item->getRanapParamedis->sum('totalbiaya');
                            $tdp = $item->getRalanDrParamedis->sum('totalbiaya') + $item->getRanapDrParamedis->sum('totalbiaya');
                            $tk = $td + $tp + $tdp;
                        @endphp

                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->tgl_bayar }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->status_lanjut }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>
                                @foreach ($item->getNomorNota as $n)
                                    {{ str_replace(':', '', $n->nm_perawatan) }}
                                @endforeach
                            </td>

                            <td>{{ number_format($item->getRegistrasi->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getObat->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getReturObat->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getResepPulang->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($tk) }}</td>
                            <td>{{ number_format($item->getOprasi->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getLaborat->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getRadiologi->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getTambahan->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getKamarInap->sum('totalbiaya')) }}</td>
                            <td>{{ number_format($item->getPotongan->sum('totalbiaya')) }}</td>

                            <td><strong>
                                {{ number_format(
                                    $item->getRegistrasi->sum('totalbiaya') +
                                    $item->getObat->sum('totalbiaya') +
                                    $item->getReturObat->sum('totalbiaya') +
                                    $item->getResepPulang->sum('totalbiaya') +
                                    $tk +
                                    $item->getOprasi->sum('totalbiaya') +
                                    $item->getLaborat->sum('totalbiaya') +
                                    $item->getRadiologi->sum('totalbiaya') +
                                    $item->getTambahan->sum('totalbiaya') +
                                    $item->getKamarInap->sum('totalbiaya') +
                                    $item->getPotongan->sum('totalbiaya')
                                ) }}
                            </strong></td>

                            <td>{{ number_format($item->uangmuka) }}</td>
                            <td>{{ number_format($item->besar_cicilan) }}</td>
                            <td>{{ number_format($item->diskon_piutang) }}</td>
                            <td>{{ number_format($item->tidak_terbayar) }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>
                                @foreach ($item->getNoSep as $sep)
                                    {{ $sep->no_sep }}
                                @endforeach
                            </td>
                            <td>{{ $item->status }}</td>

                            <td class="text-end text-primary fw-semibold">{{ number_format($td) }}</td>
                            <td class="text-end text-success fw-semibold">{{ number_format($tp) }}</td>
                            <td class="text-end text-secondary fw-semibold">{{ number_format($tdp) }}</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($tk) }}</td>
                        </tr>
                    @endforeach

                    {{-- FOOTER TOTAL --}}
                    <tr class="table-primary fw-bold text-center align-middle">
                        <td colspan="7">Total</td>
                        <td>{{ number_format($total_registrasi) }}</td>
                        <td>{{ number_format($total_obat) }}</td>
                        <td>{{ number_format($total_retur) }}</td>
                        <td>{{ number_format($total_resep) }}</td>
                        <td>{{ number_format($total_paket) }}</td>
                        <td>{{ number_format($total_oprasi) }}</td>
                        <td>{{ number_format($total_laborat) }}</td>
                        <td>{{ number_format($total_radiologi) }}</td>
                        <td>{{ number_format($total_tambahan) }}</td>
                        <td>{{ number_format($total_kamar) }}</td>
                        <td>{{ number_format($total_potongan) }}</td>
                        <td>{{ number_format($grand_total) }}</td>
                        <td>{{ number_format($total_uangmuka) }}</td>
                        <td>{{ number_format($total_cicilan) }}</td>
                        <td>{{ number_format($total_diskon) }}</td>
                        <td>{{ number_format($total_tidak_terbayar) }}</td>

                        <td colspan="4"></td>

                        <td class="text-end text-primary">{{ number_format($bayarPiutang->sum(fn($i) => $i->getRalanDokter->sum('totalbiaya') + $i->getRanapDokter->sum('totalbiaya'))) }}</td>
                        <td class="text-end text-success">{{ number_format($bayarPiutang->sum(fn($i) => $i->getRalanParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya'))) }}</td>
                        <td class="text-end text-secondary">{{ number_format($bayarPiutang->sum(fn($i) => $i->getRalanDrParamedis->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya'))) }}</td>
                        <td class="text-end text-danger">{{ number_format($total_paket) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- JS Copy --}}
<script>
document.getElementById("copyButton").addEventListener("click", function () {
    const table = document.getElementById("tableToCopy");
    let tsv = "";

    const header = table.querySelector("thead tr");
    const totalCols = header ? header.querySelectorAll("th").length : 0;

    const rows = table.querySelectorAll("tr");
    rows.forEach(row => {
        const cols = row.querySelectorAll("th, td");
        let rowData = Array.from(cols).flatMap(col => {
            const span = parseInt(col.getAttribute("colspan") || 1);
            const text = col.innerText.replace(/\t/g, " ").replace(/\n/g, " ").trim();
            return [text, ...Array(span - 1).fill("")];
        });

        while (rowData.length < totalCols) rowData.push("");

        tsv += rowData.join("\t") + "\n";
    });

    const textarea = document.createElement("textarea");
    textarea.value = tsv;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);

    alert("✅ Data berhasil disalin, Paste langsung ke Excel");
});
</script>

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

.rekap-container {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 22px;
}

.rekap-item {
    flex: 1;
    min-width: 230px;
    background: linear-gradient(135deg, #ffffff, #f4f6fb);
    border: 1px solid #dee3f0;
    border-radius: 14px;
    padding: 16px 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.rekap-label {
    font-size: 13px;
    font-weight: 600;
    color: #6c6c6c;
}

.rekap-value {
    font-size: 20px;
    font-weight: 700;
    margin-top: 6px;
}

</style>



@endsection
