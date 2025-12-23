<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak USG Urologi</title>

<style>
@page{
    size: A4;
    margin: 25mm;
}

body{
    font-family: "Times New Roman", serif;
    font-size: 13px;
    color: #000;
}

/* =========================
   KOP SURAT
========================= */
.kop{
    width:100%;
    display:flex;
    align-items:center;
    border-bottom:2px solid #000;
    padding-bottom:10px;
    margin-bottom:20px;
}

.kop .logo{
    width:120px;
    text-align:left;
}

.kop .logo img{
    height:80px;
}

.kop .kop-text{
    flex:1;
    text-align:center;
}

.kop .kop-text .nama{
    font-size:20px;
    font-weight:bold;
    text-transform:uppercase;
}

.kop .kop-text .alamat{
    font-size:13px;
    margin-top:2px;
    line-height:1.4;
}

/* =========================
   JUDUL
========================= */
.judul{
    text-align:center;
    font-size:15px;
    font-weight:bold;
    margin:15px 0 20px 0;
}

/* =========================
   IDENTITAS PASIEN
========================= */
table.identitas{
    width:100%;
    border-collapse:collapse;
    margin-bottom:12px;
}

table.identitas td{
    padding:4px 6px;
    vertical-align:top;
}

table.identitas td.label{
    width:15%;
}

table.identitas td.separator{
    width:2%;
}

/* =========================
   HASIL USG
========================= */
.box{
    border:1px solid #000;
    padding:12px;
    margin-bottom:18px;
}

.hasil{
    min-height:180px;
    line-height:1.6;
}

/* =========================
   TTD + QR (QR DI ATAS NAMA)
========================= */
.ttd-wrapper{
    width:100%;
    margin-top:45px;
}

.ttd-kiri{
    width:50%;
    text-align:left;
}

.ttd-kiri img{
    width:100px;
    height:100px;
    margin:10px 0;
}

.ttd-kiri .nama{
    font-weight:bold;
}
</style>

</head>
<body onload="window.print()">

<!-- =========================
     KOP SURAT
========================= -->
<div class="kop">
    <div class="logo">
        <img src="{{ asset('img/bw2.png') }}">
    </div>
    <div class="kop-text">
        <div class="nama">{{ $data->nama_instansi }}</div>
        <div class="alamat">
            {{ $data->alamat_instansi }}<br>
            {{ $data->kabupaten }}, {{ $data->propinsi }}<br>
            Telp. {{ $data->kontak }} — Email: {{ $data->email }}
        </div>
    </div>
</div>

<!-- =========================
     JUDUL
========================= -->
<div class="judul">
    HASIL PEMERIKSAAN<br>
    USG UROLOGI
</div>

<!-- =========================
     IDENTITAS PASIEN
========================= -->
<table class="identitas">
<tr>
    <td class="label">No. RM</td><td class="separator">:</td>
    <td>{{ $data->no_rkm_medis }}</td>

    <td class="label">Poli</td><td class="separator">:</td>
    <td>{{ $data->nm_poli }}</td>
</tr>

<tr>
    <td class="label">Nama Pasien</td><td class="separator">:</td>
    <td>{{ $data->nm_pasien }}</td>

    <td class="label">Tanggal</td><td class="separator">:</td>
    <td>{{ date('d-m-Y', strtotime($data->created_at)) }}</td>
</tr>

<tr>
    <td class="label">JK / Umur</td><td class="separator">:</td>
    <td>{{ $data->jk }} / {{ $data->umurdaftar }}</td>

    <td class="label">Jam</td><td class="separator">:</td>
    <td>{{ date('H:i:s', strtotime($data->created_at)) }}</td>
</tr>

<tr>
    <td class="label">No. Rawat</td><td class="separator">:</td>
    <td colspan="4">{{ $data->no_rawat }}</td>
</tr>
</table>

<!-- =========================
     HASIL USG
========================= -->
<div class="box hasil">
    <strong>Hasil Pemeriksaan :</strong><br><br>
    {!! nl2br(e($data->hasil_usg)) !!}
</div>

<!-- =========================
     TTD + QR
========================= -->
<div class="ttd-wrapper">
    <div class="ttd-kiri">
        Bandar Lampung, {{ date('d-m-Y') }}<br>
        Dokter Pemeriksa<br><br>

        <!-- QR CODE -->
        <img src="data:image/png;base64,{{ $qrBase64 }}"
            style="margin-left:20px;">

        <!-- NAMA DOKTER -->
        <div class="nama">
            {{ $data->nm_dokter }}
        </div>
    </div>
</div>

</body>
</html>
