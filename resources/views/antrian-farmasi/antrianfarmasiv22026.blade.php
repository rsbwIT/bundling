<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>ANTRIAN FARMASI</title>
<meta http-equiv="refresh" content="8">

<style>
/* ================= GLOBAL ================= */
html, body {
    height: 100%;
    overflow: hidden;
}

body {
    margin: 0;
    background: #000;
    color: #e5e7eb;
    font-family: Arial, Helvetica, sans-serif;
    display: flex;
    flex-direction: column;
}

/* ================= HEADER ================= */
.header {
    background: #000;
    color: #facc15;
    padding: 16px 30px;
    font-size: 36px;
    font-weight: 900;
    letter-spacing: 3px;
    border-bottom: 4px solid #facc15;
    display: flex;
    align-items: center;
    gap: 22px;
    flex-shrink: 0;
}

.logo-wrap {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid #facc15;
    padding: 8px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow:
        0 0 0 4px #000,
        0 0 0 6px #facc15,
        0 0 18px rgba(250,204,21,.6);
}

.logo-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.header-text { line-height: 1.15; }

.sub {
    font-size: 18px;
    color: #9ca3af;
    margin-top: 6px;
    letter-spacing: 2px;
}

/* ================= FILTER ================= */
.lane-filter {
    padding: 10px 20px;
    background: #020617;
    border-bottom: 2px solid #facc15;
    flex-shrink: 0;
}

.lane-filter a {
    margin-right: 8px;
    padding: 8px 14px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 4px;
    background: #111827;
    color: #9ca3af;
}

.lane-filter a.active {
    background: #facc15;
    color: #000;
}

/* ================= TABLE WRAPPER ================= */
.table-wrap {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}

/* ================= TABLE ================= */
table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed; /* 🔑 WAJIB */
    font-size: 22px;
}

/* ===== KUNCI LEBAR KOLOM ===== */
th:nth-child(1), td:nth-child(1) { width: 7%;  text-align:center; }
th:nth-child(2), td:nth-child(2) { width: 6%;  text-align:center; }
th:nth-child(3), td:nth-child(3) { width: 10%; text-align:center; }
th:nth-child(4), td:nth-child(4) { width: 32%; }
th:nth-child(5), td:nth-child(5) { width: 8%;  text-align:center; }
th:nth-child(6), td:nth-child(6) { width: 17%; text-align:center; }
th:nth-child(7), td:nth-child(7) { width: 10%; text-align:center; }

/* ===== HEADER KOLOM ===== */
thead th {
    position: sticky;
    top: 0;
    background: #000;
    color: #9ca3af;
    font-weight: 700;
    padding: 14px 16px;
    border-bottom: 2px solid #facc15;
    z-index: 5;
}

/* ===== BODY ===== */
td {
    padding: 14px 16px;
    border-bottom: 1px solid #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

tr:nth-child(even) {
    background: #020617;
}

/* ================= STATUS ================= */
.MENUNGGU {
    color: #facc15;
    font-weight: bold;
}

.DIPANGGIL {
    color: #22c55e;
    font-weight: 900;
    animation: blink 1s infinite;
}

@keyframes blink {
    50% { opacity: .3; }
}

/* ================= JALUR ================= */
.jalur {
    font-weight: bold;
    padding: 6px 12px;
    border-radius: 6px;
    display: inline-block;
    min-width: 38px;
}

.A { background:#16a34a; }
.B { background:#0ea5e9; }
.C { background:#facc15; color:#000; }
.D { background:#ef4444; }

/* ================= FOOTER ================= */
.footer {
    height: 46px;
    background: #000;
    border-top: 3px solid #facc15;
    display: flex;
    align-items: center;
    overflow: hidden;
    flex-shrink: 0;
}

.marquee {
    width: 100%;
    white-space: nowrap;
}

.marquee span {
    display: inline-block;
    padding-left: 100%;
    font-size: 20px;
    letter-spacing: 3px;
    color: #facc15;
    animation: marquee 18s linear infinite;
}

@keyframes marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}
</style>
</head>

<body>

<!-- ================= HEADER ================= -->
<div class="header">
    <div class="logo-wrap">
        <img src="{{ asset('img/bw2.png') }}" alt="Logo Rumah Sakit Bumi Waras">
    </div>
    <div class="header-text">
        ANTRIAN FARMASI
        <div class="sub">RUMAH SAKIT BUMI WARAS</div>
    </div>
</div>

<!-- ================= FILTER ================= -->
<div class="lane-filter">
@foreach (['ALL','A','B','C','D'] as $l)
    <a href="?lane={{ $l }}" class="{{ ($lane ?? 'ALL') == $l ? 'active' : '' }}">
        {{ $l }}
    </a>
@endforeach
</div>

<!-- ================= TABLE ================= -->
<div class="table-wrap">
<table>
<thead>
<tr>
    <th>TIME</th>
    <th>NO</th>
    <th>RM</th>
    <th>NAMA PASIEN</th>
    <th>LANE</th>
    <th>TYPE</th>
    <th>STATUS</th>
</tr>
</thead>
<tbody>
@forelse ($antrian as $row)
<tr>
    <td>{{ $row->waktu_panggil ? \Carbon\Carbon::parse($row->waktu_panggil)->format('H:i') : '--:--' }}</td>
    <td>{{ $row->no_antrian }}</td>
    <td>{{ $row->no_rkm_medis }}</td>
    <td>{{ strtoupper($row->nm_pasien) }}</td>
    <td><span class="jalur {{ $row->jalur }}">{{ $row->jalur }}</span></td>
    <td>{{ strtoupper($row->jenis_obat) }}</td>
    <td class="{{ $row->status_panggil }}">{{ $row->status_panggil }}</td>
</tr>
@empty
<tr>
    <td colspan="7" style="text-align:center;color:#6b7280">
        NO DEPARTURE INFORMATION
    </td>
</tr>
@endforelse
</tbody>
</table>
</div>

<!-- ================= FOOTER ================= -->
<div class="footer">
    <div class="marquee">
        <span>MOHON PERHATIKAN PAPAN INFORMASI • TERIMA KASIH ATAS KESABARAN ANDA</span>
    </div>
</div>

</body>
</html>
