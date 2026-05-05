@extends('..layout.layoutDashboard')

@section('title','REKAPAN BULANAN')

@section('konten')

<style>

.kategori{
background:#d9d9d9;
font-weight:bold;
}

.sub1{
padding-left:25px !important;
}

.jm{
background:#fff200;
font-weight:bold;
}

.grand{
background:#e6b8b7;
font-weight:bold;
}

#tableToCopy th{
text-align:center;
vertical-align:middle;
}

#tableToCopy td:nth-child(1){
text-align:center;
width:60px;
}

#tableToCopy td:nth-child(3),
#tableToCopy td:nth-child(4){
text-align:right;
}

</style>

<div class="card shadow-sm border-0">
<div class="card-body">

@include('laporan.component.searchpendapatanbulanan')

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
<h6 class="mb-0 fw-bold">Laporan Pendapatan Pasien Bulanan</h6>

<button type="button" class="btn btn-sm btn-outline-secondary" id="copyButton">
<i class="fas fa-copy"></i> Copy Table
</button>
</div>

<div class="table-responsive">

<table class="table table-bordered table-sm align-middle" id="tableToCopy">

<thead class="table-secondary">

<tr>
<th rowspan="2">NO</th>
<th rowspan="2" style="width:450px;">Keterangan</th>
<th colspan="2">UMUM</th>
<th colspan="2">BPJS</th>
<th colspan="2">PERUSAHAAN</th>
<th colspan="2">ASKES INHEALTH</th>
<th colspan="2">CASH BASIK</th>
<th colspan="2">PIUTANG KARYAWAN</th>
<th colspan="2">PIUTANG YANG SUDAH DILUNASI</th>
<th colspan="3">TOTAL</th>
</tr>

<tr>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">R.Inap</th>
<th style="width:200px;">R.Jalan</th>
<th style="width:200px;">Jumlah</th>
</tr>

</thead>

<tbody>

<tr class="kategori">
<td>1</td>
<td>REGISTER</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Register</td>
<td>{{ number_format($registerRanap ?? 0) }}</td>
<td>{{ number_format($registerRalan ?? 0) }}</td>
<td>{{ number_format($registerRanapBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($registerRalanBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr class="kategori">
<td>2</td>
<td>JASA TINDAKAN</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr class="jasasarana">
<td></td>
<td class="sub1">Jasa Sarana</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($TotalJasaSarana ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="paketbhp">
<td></td>
<td class="sub1">Paket BHP</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($TotalBHP ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="jmdokter">
<td></td>
<td class="sub1">JM Dokter</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($TotalJMDokter ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="jmparamedis">
<td></td>
<td class="sub1">Paramedis</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($totalParamedis ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="kategori">
<td>3</td>
<td>KSO</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr class="alatbw">
<td></td>
<td class="sub1">Alat BW</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format((float) ($ksoPR->total_kso_pr ?? 0), 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="alatdokter">
<td></td>
<td class="sub1">Alat Dokter</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format((float) ($totalKsoDR ?? 0), 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="ambulancebw">
<td></td>
<td class="sub1">Ambulance BW</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format((float) ($totalAmbulanceValue ?? 0), 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="ambulancesap">
<td></td>
<td class="sub1">Ambulance SAP</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr class="kategori">
<td>3</td>
<td>PEND. OBAT & ALAT KESEHATAN</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Obat + Embalase + Tuslah</td>
<td>{{ number_format($obatRanap ?? 0) }}</td>
<td>{{ number_format($obatRalan ?? 0) }}</td>
<td>{{ number_format($RanapObatBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanObatBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">Retur Obat</td>
<td>{{ number_format($returRanap ?? 0) }}</td>
<td>{{ number_format($returRalan ?? 0) }}</td>
<td>{{ number_format($RanapReturObatBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanReturObatBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">Resep Pulang</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format((float) $totalResepPulangValue, 0, ',', '.') }}</td>
<td>{{ number_format(0) }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">Obat PJ</td>
<td>0</td>
<td>{{ number_format($totalPJ ?? 0) }}</td>
<td>0</td>
<td>0</td>
</tr>

<tr class="jm">
<td></td>
<td class="sub1">JM Dokter</td>
<td>{{ number_format($jmDokterRanap ?? 0) }}</td>
<td>{{ number_format($jmDokterRalan ?? 0) }}</td>
<td>0</td>
<td>0</td>
</tr>

<tr>
<td></td>
<td class="sub1">Paramedis</td>
<td>{{ number_format($paramedisRanap ?? 0) }}</td>
<td>{{ number_format($paramedisRalan ?? 0) }}</td>
<td>0</td>
<td>0</td>
</tr>

<tr>
<td></td>
<td class="sub1">Dr Paramedis</td>
<td>{{ number_format($drparamedisRanap ?? 0) }}</td>
<td>{{ number_format($drparamedisRalan ?? 0) }}</td>
<td>0</td>
<td>0</td>
</tr>



<tr class="kategori">
<td>4</td>
<td>HEMODIALISA</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Alkes HD</td>
<td>{{ number_format($totalHDRanap ?? 0) }}</td>
<td>{{ number_format($totalHDRalan ?? 0) }}</td>
<td>{{ number_format($totalHDRanapBpjs ?? 0) }}</td>
<td>{{ number_format($totalHDRalanBpjs ?? 0) }}</td>
</tr>

<tr class="kategori">
<td>4</td>
<td>PENDAPATAN LABORATORIUM</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Laboratorium</td>
<td>{{ number_format($labRanap ?? 0) }}</td>
<td>{{ number_format($labRalan ?? 0) }}</td>
<td>{{ number_format($RanapLaboratBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanLaboratBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr class="kategori">
<td>5</td>
<td>RADIOLOGI</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Jasa Sarana</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($hasilAkhir ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanRadiologiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">Paket BHP</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($RanapRadiologiBpjsBHP1 ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanRadiologiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">JM Dokter PJ</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($RanapRadiologiJmDokterPj ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanRadiologiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">Petugas</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($RanapRadiologiJmPetugas ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanRadiologiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">JM Perujuk</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format(0) }}</td>
<td>{{ number_format($RanapRadiologiPerujuk ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanRadiologiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr class="kategori">
<td>6</td>
<td>Rad tes</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Radiologi</td>
<td>{{ number_format($radiologiRanap ?? 0) }}</td>
<td>{{ number_format($radiologiRalan ?? 0) }}</td>
<td>{{ number_format($RanapRadiologiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RanapRadiologiBpjs1 ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanRadiologiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr class="kategori">
<td>6</td>
<td>KAMAR INAP</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Kamar</td>
<td>{{ number_format($kamarRanap ?? 0) }}</td>
<td>{{ number_format($kamarRalan ?? 0) }}</td>
<td>{{ number_format($RanapKamarBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanKamarBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr class="kategori">
<td>7</td>
<td>OPERASI</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
<td></td>
<td class="sub1">Operasi</td>
<td>{{ number_format($operasiRanap ?? 0) }}</td>
<td>{{ number_format($operasiRalan ?? 0) }}</td>
<td>{{ number_format($RanapOperasiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanOperasiBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr class="kategori">
<td>8</td>
<td>TAMBAHAN / EXSES</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

{{-- <tr>
<td></td>
<td class="sub1">Tambahan Non Umum</td>
<td>{{ number_format($tambahanNonUmumRanap ?? 0) }}</td>
<td>{{ number_format($tambahanNonUmumRalan ?? 0) }}</td>
</tr> --}}

<tr>
<td></td>
<td class="sub1">Exses</td>
<td>{{ number_format($totalEksesRanap ?? 0) }}</td>
<td>{{ number_format($totalEksesRalan ?? 0) }}</td>
<td>{{ number_format($ExsesBPJS ?? 0, 0, ',', '.') }}</td>
<td>0</td>
</tr>

<tr>
<td></td>
<td class="sub1">Potongan</td>
<td>{{ number_format($potonganRanap ?? 0) }}</td>
<td>{{ number_format($potonganRalan ?? 0) }}</td>
<td>{{ number_format($RanapPotonganBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($RalanPotonganBpjs->sum('totalbiaya') ?? 0, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">COB</td>
<td>0</td>
<td>0</td>
<td>{{ number_format($cobranapbpjs ?? 0, 0, ',', '.') }}</td>
<td>{{ number_format($totalCicilan, 0, ',', '.') }}</td>
</tr>

<tr>
<td></td>
<td class="sub1">Adm Transfer</td>
<td>0</td>
<td>0</td>
<td>0</td>
<td>0</td>
</tr>

<tr class="grand">
<td></td>
<td>GRAND TOTAL</td>
<td>{{ number_format($grandRanap ?? 0) }}</td>
<td>{{ number_format($grandRalan ?? 0) }}</td>
</tr>

</tbody>

</table>

</div>
</div>
</div>


@endsection
