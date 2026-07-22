<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kode_brng = '809'; // I'll just pick a random code or get the first one that has data in pengeluaran_obat_bhp.
$tglAwal = '2020-01-01'; // broad date
$tglAkhir = '2026-12-31'; // broad date

$kode_brng = DB::table('detail_pengeluaran_obat_bhp')->first()->kode_brng ?? null;
if (!$kode_brng) {
    $kode_brng = DB::table('detail_pemberian_obat')->first()->kode_brng ?? null;
}

echo "Testing with kode_brng: " . $kode_brng . "\n";

$pengeluaran = DB::table('pengeluaran_obat_bhp')
    ->join('detail_pengeluaran_obat_bhp', 'detail_pengeluaran_obat_bhp.no_keluar', '=', 'pengeluaran_obat_bhp.no_keluar')
    ->join('bangsal', 'pengeluaran_obat_bhp.kd_bangsal_tujuan', '=', 'bangsal.kd_bangsal')
    ->select('bangsal.nm_bangsal', DB::raw('SUM(detail_pengeluaran_obat_bhp.jumlah) as jumlah'))
    ->whereBetween('pengeluaran_obat_bhp.tanggal', [$tglAwal, $tglAkhir])
    ->where('detail_pengeluaran_obat_bhp.kode_brng', $kode_brng)
    ->groupBy('bangsal.nm_bangsal');

$pemberian = DB::table('detail_pemberian_obat')
    ->join('bangsal', 'detail_pemberian_obat.kd_bangsal', '=', 'bangsal.kd_bangsal')
    ->select('bangsal.nm_bangsal', DB::raw('SUM(detail_pemberian_obat.jml) as jumlah'))
    ->whereBetween('detail_pemberian_obat.tgl_perawatan', [$tglAwal, $tglAkhir])
    ->where('detail_pemberian_obat.kode_brng', $kode_brng)
    ->groupBy('bangsal.nm_bangsal');

$resep = DB::table('resep_pulang')
    ->join('bangsal', 'resep_pulang.kd_bangsal', '=', 'bangsal.kd_bangsal')
    ->select('bangsal.nm_bangsal', DB::raw('SUM(resep_pulang.jml_barang) as jumlah'))
    ->whereBetween('resep_pulang.tanggal', [$tglAwal, $tglAkhir])
    ->where('resep_pulang.kode_brng', $kode_brng)
    ->groupBy('bangsal.nm_bangsal');

$penjualan = DB::table('penjualan')
    ->join('detailjual', 'detailjual.nota_jual', '=', 'penjualan.nota_jual')
    ->join('bangsal', 'penjualan.kd_bangsal', '=', 'bangsal.kd_bangsal')
    ->select('bangsal.nm_bangsal', DB::raw('SUM(detailjual.jumlah) as jumlah'))
    ->whereBetween('penjualan.tgl_jual', [$tglAwal, $tglAkhir])
    ->where('detailjual.kode_brng', $kode_brng)
    ->groupBy('bangsal.nm_bangsal');

$union = $pengeluaran
    ->unionAll($pemberian)
    ->unionAll($resep)
    ->unionAll($penjualan);

try {
    $results = DB::table(DB::raw("({$union->toSql()}) as sub"))
        ->mergeBindings($union)
        ->select('nm_bangsal', DB::raw('SUM(jumlah) as total_jumlah'))
        ->groupBy('nm_bangsal')
        ->orderByDesc('total_jumlah')
        ->get();
    print_r($results);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
