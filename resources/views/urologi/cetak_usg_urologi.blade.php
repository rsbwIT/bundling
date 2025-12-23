<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak USG Urologi</title>

<style>
@page{
    size:A4;
    margin:25mm;
}

body{
    font-family:"Times New Roman", serif;
    font-size:14px;
    color:#000;
    /* font-weight:bold; */
}

/* =========================
   KOP SURAT
========================= */
.kop{
    width:100%;
    display:flex;
    align-items:center;
    border-bottom:2px solid #000;
    padding-bottom:8px;
    margin-bottom:18px;
}

.kop .logo{
    width:110px;
}

.kop .logo img{
    height:75px;
}

.kop .kop-text{
    flex:1;
    text-align:center;
    padding-right:35px;
}

.kop .kop-text .nama{
    font-size:18px;
    font-weight:bold;
    text-transform:uppercase;
}

.kop .kop-text .alamat{
    font-size:12px;
    line-height:1.4;
}

/* =========================
   JUDUL
========================= */
.judul{
    text-align:center;
    font-size:14px;
    font-weight:bold;
    margin:15px 0 18px;
}

/* =========================
   IDENTITAS
========================= */
table.identitas{
    width:100%;
    border-collapse:collapse;
    margin-bottom:12px;
}

table.identitas td{
    padding:3px 5px;
    vertical-align:top;
}

table.identitas td.label{width:15%;}
table.identitas td.separator{width:2%;}

/* =========================
   HASIL
========================= */
.box{
    border:1px solid #000;
    padding:12px;
    margin-bottom:22px;
}

.hasil{
    min-height:170px;
    line-height:1.6;
}

/* =========================
   TTD + QR (KANAN, QR DI ATAS NAMA)
========================= */
.ttd-wrapper{
    width:100%;
    margin-top:20px;
    display:flex;
    justify-content:flex-end;
}

.ttd{
    width:42%;
    text-align:center;
}

.ttd .tanggal{
    margin-bottom:2px;
}

.ttd img{
    width:95px;
    height:95px;
    margin:8px auto 6px;
    display:block;
}

.ttd .nama{
    font-weight:bold;
    text-decoration:underline;
    margin-top:4px;
    font-size:13px;
}

.ttd .jabatan{
    font-size:11px;
    margin-top:2px;
}
</style>

</head>
<body onload="window.print()">

<!-- =========================
     KOP
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
     HASIL PEMERIKSAAN
========================= -->
<div class="box hasil">
    <strong>Hasil Pemeriksaan :</strong><br><br>
    {!! nl2br(e($data->hasil_usg)) !!}
</div>

<!-- =========================
     TTD & QR
========================= -->
<div class="ttd-wrapper">
    <div class="ttd">
        <div class="tanggal">
            Bandar Lampung, {{ date('d-m-Y') }}
        </div>
        Dokter Pemeriksa

        <!-- QR CODE -->
        <img src="data:image/png;base64,{{ $qrBase64 }}">

        <div class="nama">
            {{ $data->nm_dokter }}
        </div>
        <div class="jabatan">
            Spesialis Urologi
        </div>
    </div>
</div>

</body>
</html>
