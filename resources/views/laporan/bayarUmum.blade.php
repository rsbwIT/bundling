@extends('..layout.layoutDashboard')

@section('title', 'Bayar Umum')

@section('konten')
<div class="card shadow-sm border-0">
    <div class="card-body">
        {{-- 🔍 Komponen Pencarian --}}
        @include('laporan.component.search-bayarUmum')

        {{-- 🔹 Header Atas --}}
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <div>Jumlah Data : {{ count($bayarUmum) }}</div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyButton">
                <i class="fas fa-copy"></i> Copy Table
            </button>
        </div>

        {{-- 🔹 Tabel Data --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered table-striped align-middle text-xs" id="tableToCopy" style="white-space: nowrap;">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Tgl. Bayar</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Bayar</th>
                        <th>Status Lanjut</th>
                        <th>No. RM</th>
                        <th>No. Rawat</th>
                        <th>No. Nota</th>
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
                        <th>Dokter</th>
                        <th>Paramedis</th>
                        <th>Dokter Paramedis</th>
                        <th>Total Paket Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bayarUmum as $key => $item)
                        @php
                            $dokter = $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya');
                            $paramedis = $item->getRalanParamedis->sum('totalbiaya') + $item->getRanapParamedis->sum('totalbiaya');
                            $drParamedis = $item->getRalanDrParamedis->sum('totalbiaya') + $item->getRanapDrParamedis->sum('totalbiaya');
                            $paket = $dokter + $paramedis + $drParamedis;

                            $total = $item->getRegistrasi->sum('totalbiaya') +
                                      $item->getObat->sum('totalbiaya') +
                                      $item->getReturObat->sum('totalbiaya') +
                                      $item->getResepPulang->sum('totalbiaya') +
                                      $paket +
                                      $item->getOprasi->sum('totalbiaya') +
                                      $item->getLaborat->sum('totalbiaya') +
                                      $item->getRadiologi->sum('totalbiaya') +
                                      $item->getTambahan->sum('totalbiaya') +
                                      $item->getKamarInap->sum('totalbiaya') +
                                      $item->getPotongan->sum('totalbiaya');
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->tgl_byr }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>{{ $item->status_lanjut }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>
                                @foreach ($item->getNomorNota as $detail)
                                    {{ str_replace(':', '', $detail->nm_perawatan) }}
                                @endforeach
                            </td>

                            {{-- Nominal uang rata kanan --}}
                            <td class="text-end">{{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getObat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getReturObat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getResepPulang->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($paket, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getOprasi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getLaborat->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getRadiologi->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getTambahan->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getKamarInap->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->getPotongan->sum('totalbiaya'), 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">{{ number_format($total, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($dokter, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($paramedis, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($drParamedis, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">{{ number_format($paket, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    {{-- 🔹 Baris Total Akhir --}}
                    @php
                        $sumDokter = $bayarUmum->sum(fn($i) => $i->getRalanDokter->sum('totalbiaya') + $i->getRanapDokter->sum('totalbiaya'));
                        $sumParamedis = $bayarUmum->sum(fn($i) => $i->getRalanParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya'));
                        $sumDrParamedis = $bayarUmum->sum(fn($i) => $i->getRalanDrParamedis->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya'));
                        $sumPaket = $sumDokter + $sumParamedis + $sumDrParamedis;

                        $sumTotal = $bayarUmum->sum(fn($i) =>
                            $i->getRegistrasi->sum('totalbiaya') +
                            $i->getObat->sum('totalbiaya') +
                            $i->getReturObat->sum('totalbiaya') +
                            $i->getResepPulang->sum('totalbiaya') +
                            $i->getOprasi->sum('totalbiaya') +
                            $i->getLaborat->sum('totalbiaya') +
                            $i->getRadiologi->sum('totalbiaya') +
                            $i->getTambahan->sum('totalbiaya') +
                            $i->getKamarInap->sum('totalbiaya') +
                            $i->getPotongan->sum('totalbiaya') +
                            ($i->getRalanDokter->sum('totalbiaya') + $i->getRanapDokter->sum('totalbiaya')) +
                            ($i->getRalanParamedis->sum('totalbiaya') + $i->getRanapParamedis->sum('totalbiaya')) +
                            ($i->getRalanDrParamedis->sum('totalbiaya') + $i->getRanapDrParamedis->sum('totalbiaya'))
                        );
                    @endphp

                    <tr class="fw-bold bg-light text-end total-row">
                        <th colspan="8" class="text-start">Total :</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getRegistrasi->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getObat->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getReturObat->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getResepPulang->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($sumPaket,0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getOprasi->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getLaborat->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getRadiologi->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getTambahan->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getKamarInap->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($bayarUmum->sum(fn($i)=>$i->getPotongan->sum('totalbiaya')),0,',','.') }}</th>
                        <th>{{ number_format($sumTotal,0,',','.') }}</th>
                        <th>{{ number_format($sumDokter,0,',','.') }}</th>
                        <th>{{ number_format($sumParamedis,0,',','.') }}</th>
                        <th>{{ number_format($sumDrParamedis,0,',','.') }}</th>
                        <th><strong>{{ number_format($sumPaket,0,',','.') }}</strong></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ✅ CSS: pastikan footer juga rata kanan --}}
<style>
    #tableToCopy td.text-end,
    #tableToCopy th.text-end,
    #tableToCopy tr.total-row th:not(:first-child) {
        text-align: right !important;
        font-variant-numeric: tabular-nums;
    }

    #tableToCopy td.fw-bold,
    #tableToCopy tr.total-row th {
        font-weight: 600;
        background-color: #fafafa;
    }

    #tableToCopy tr.total-row th:first-child {
        text-align: left !important;
    }
</style>

{{-- 🔹 Script Copy Table --}}
<script>
document.getElementById("copyButton").addEventListener("click", function () {
    const table = document.getElementById("tableToCopy");
    const rows = Array.from(table.querySelectorAll("tr"));
    const headerCols = table.querySelectorAll("thead tr th").length;
    let tsv = "";

    rows.forEach(row => {
        const cols = Array.from(row.querySelectorAll("th, td")).flatMap(col => {
            const span = parseInt(col.getAttribute("colspan") || 1);
            const text = col.innerText
                .replace(/\./g, "")
                .replace(/\t/g, " ")
                .replace(/\n/g, " ")
                .trim();
            return [text, ...Array(span - 1).fill("")];
        });
        while (cols.length < headerCols) cols.push("");
        tsv += cols.join("\t") + "\n";
    });

    navigator.clipboard.writeText(tsv)
        .then(() => alert("✅ Data berhasil disalin! Silakan paste ke Excel."))
        .catch(() => {
            const textarea = document.createElement("textarea");
            textarea.value = tsv;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
            alert("✅ Data berhasil disalin! Silakan paste ke Excel.");
        });
});
</script>
@endsection
