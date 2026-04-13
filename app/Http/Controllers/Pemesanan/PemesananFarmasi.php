<?php

namespace App\Http\Controllers\Pemesanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class PemesananFarmasi extends Controller
{
    public function pemesanan(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | DEFAULT TANGGAL HARI INI
        |--------------------------------------------------------------------------
        */
        $today = Carbon::today()->format('Y-m-d');

        $tglPesanDari   = $request->input('tgl_pesan_dari', $today);
        $tglPesanSampai = $request->input('tgl_pesan_sampai', $today);

        $tglTempoDari   = $request->input('tgl_tempo_dari', $today);
        $tglTempoSampai = $request->input('tgl_tempo_sampai', $today);

        /*
        |--------------------------------------------------------------------------
        | QUERY UTAMA
        |--------------------------------------------------------------------------
        */
        $query = DB::table('pemesanan')
            ->select(
                'pemesanan.no_faktur',
                DB::raw("IFNULL(no_pajak.no_pajak, 'Belum Ada') AS no_pajak"),
                'datasuplier.nama_suplier',
                'pemesanan.no_order',
                'pemesanan.tgl_pesan',
                'pemesanan.tgl_faktur',
                'pemesanan.tgl_tempo',
                'pemesanan.tagihan',
                'pemesanan.status',
                'bangsal.nm_bangsal'
            )
            ->join('bangsal', 'pemesanan.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('datasuplier', 'pemesanan.kode_suplier', '=', 'datasuplier.kode_suplier')
            ->leftJoin('no_pajak', 'pemesanan.no_faktur', '=', 'no_pajak.no_faktur');

        /*
        |--------------------------------------------------------------------------
        | CEK FIRST LOAD (TANPA FILTER)
        |--------------------------------------------------------------------------
        */
        $firstLoad = !$request->hasAny([
            'filter_pesan',
            'filter_tempo',
            'status_bayar',
            'supplier',
            'bangsal'
        ]);

        if ($firstLoad) {

            // Default: tampilkan data hari ini
            $query->whereDate('pemesanan.tgl_pesan', $today);
        } else {

            /*
            |----------------------------------------------------------------------
            | FILTER TANGGAL PESAN
            |----------------------------------------------------------------------
            */
            if ($request->filled('filter_pesan')) {
                $query->whereBetween('pemesanan.tgl_pesan', [
                    $tglPesanDari,
                    $tglPesanSampai
                ]);
            }

            /*
            |----------------------------------------------------------------------
            | FILTER TANGGAL TEMPO
            |----------------------------------------------------------------------
            */
            if ($request->filled('filter_tempo')) {
                $query->whereBetween('pemesanan.tgl_tempo', [
                    $tglTempoDari,
                    $tglTempoSampai
                ]);
            }

            /*
            |----------------------------------------------------------------------
            | FILTER STATUS BAYAR
            |----------------------------------------------------------------------
            */
            if ($request->filled('status_bayar')) {
                $query->where('pemesanan.status', $request->status_bayar);
            }

            /*
            |----------------------------------------------------------------------
            | FILTER SUPPLIER
            |----------------------------------------------------------------------
            */
            if ($request->filled('supplier')) {
                $query->where('pemesanan.kode_suplier', $request->supplier);
            }

            /*
            |----------------------------------------------------------------------
            | FILTER BANGSAL
            |----------------------------------------------------------------------
            */
            if ($request->filled('bangsal')) {
                $query->where('pemesanan.kd_bangsal', $request->bangsal);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EKSEKUSI QUERY
        |--------------------------------------------------------------------------
        */
        $data = $query
            ->orderByDesc('pemesanan.tgl_faktur')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY DATA (UNTUK CARD ATAS)
        |--------------------------------------------------------------------------
        */
        $total_pasien   = $data->count();

        $sudah_dibayar  = $data->where('status', 'Sudah Dibayar')->count();

        $belum_dibayar  = $data->where('status', 'Belum Dibayar')->count();

        /*
        |--------------------------------------------------------------------------
        | MASTER DATA
        |--------------------------------------------------------------------------
        */
        $suppliers = DB::table('datasuplier')
            ->orderBy('nama_suplier')
            ->get();

        $bangsals = DB::table('bangsal')
            ->whereIn('nm_bangsal', [
                'GUDANG FARMASI RAJAL',
                'GUDANG FARMASI RANAP'
            ])
            ->orderBy('nm_bangsal')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view(
            'pemesanan.pemesananfarmasi',
            compact(
                'data',
                'suppliers',
                'bangsals',
                'total_pasien',
                'sudah_dibayar',
                'belum_dibayar'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN NOMOR PAJAK
    |--------------------------------------------------------------------------
    */
    public function simpanPajak(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required',
            'no_pajak'  => 'required|unique:no_pajak,no_pajak'
        ]);

        DB::table('no_pajak')->insert([
            'no_faktur' => $request->no_faktur,
            'no_pajak'  => $request->no_pajak
        ]);

        return redirect()
            ->back()
            ->with('success', 'Nomor Pajak berhasil disimpan');
    }

    public function export(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');

        $tglPesanDari   = $request->input('tgl_pesan_dari', $today);
        $tglPesanSampai = $request->input('tgl_pesan_sampai', $today);

        $tglTempoDari   = $request->input('tgl_tempo_dari', $today);
        $tglTempoSampai = $request->input('tgl_tempo_sampai', $today);

        $query = DB::table('pemesanan')
            ->select(
                'pemesanan.no_faktur',
                DB::raw("IFNULL(no_pajak.no_pajak, 'Belum Ada') AS no_pajak"),
                'datasuplier.nama_suplier',
                'pemesanan.no_order',
                'pemesanan.tgl_pesan',
                'pemesanan.tgl_faktur',
                'pemesanan.tgl_tempo',
                'pemesanan.tagihan',
                'pemesanan.status',
                'bangsal.nm_bangsal'
            )
            ->join('bangsal', 'pemesanan.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('datasuplier', 'pemesanan.kode_suplier', '=', 'datasuplier.kode_suplier')
            ->leftJoin('no_pajak', 'pemesanan.no_faktur', '=', 'no_pajak.no_faktur');

        if ($request->filled('filter_pesan')) {
            $query->whereBetween('pemesanan.tgl_pesan', [$tglPesanDari, $tglPesanSampai]);
        }

        if ($request->filled('filter_tempo')) {
            $query->whereBetween('pemesanan.tgl_tempo', [$tglTempoDari, $tglTempoSampai]);
        }

        if ($request->filled('status_bayar')) {
            $query->where('pemesanan.status', $request->status_bayar);
        }

        if ($request->filled('supplier')) {
            $query->where('pemesanan.kode_suplier', $request->supplier);
        }

        if ($request->filled('bangsal')) {
            $query->where('pemesanan.kd_bangsal', $request->bangsal);
        }

        $data = $query->orderByDesc('pemesanan.tgl_faktur')->get();

        $filename = "Pemesanan_Farmasi_" . date('Ymd_His') . ".xls";

        return response()->streamDownload(function () use ($data) {

            // UTF-8 BOM supaya tidak rusak format
            echo "\xEF\xBB\xBF";

            echo "<table border='1'>";
            echo "<tr>
            <th>No Faktur</th>
            <th>No Pajak</th>
            <th>Supplier</th>
            <th>No Order</th>
            <th>Bangsal</th>
            <th>Tgl Datang</th>
            <th>Tgl Faktur</th>
            <th>Tgl Tempo</th>
            <th>Total</th>
            <th>PPN (Total)</th>
            <th>Status</th>
            <th>DPP</th>
            <th>DPP Nilai Lain</th>
            <th>PPN (DPP)</th>
            <th>Selisih</th>
        </tr>";

            foreach ($data as $row) {

                $total = (int) $row->tagihan;
                $dpp = $total > 0 ? (int) round($total / 1.11, 0) : 0;
                $ppn_total = $total > 0 ? (int) round(($total / 1.11) * 0.11, 0) : 0;
                $dpp_lain = (int) round($dpp * 11 / 12, 0);
                $ppn_dpp = (int) round($dpp_lain * 0.12, 0);
                $selisih = (int) ($ppn_total - $ppn_dpp);

                echo "<tr>
                <td style='mso-number-format:\"\\@\"'>{$row->no_faktur}</td>
                <td style='mso-number-format:\"\\@\"'>{$row->no_pajak}</td>
                <td>{$row->nama_suplier}</td>
                <td>{$row->no_order}</td>
                <td>{$row->nm_bangsal}</td>
                <td>{$row->tgl_pesan}</td>
                <td>{$row->tgl_faktur}</td>
                <td>{$row->tgl_tempo}</td>
                <td style='mso-number-format:\"0\"'>{$total}</td>
                <td style='mso-number-format:\"0\"'>{$ppn_total}</td>
                <td>{$row->status}</td>
                <td style='mso-number-format:\"0\"'>{$dpp}</td>
                <td style='mso-number-format:\"0\"'>{$dpp_lain}</td>
                <td style='mso-number-format:\"0\"'>{$ppn_dpp}</td>
                <td style='mso-number-format:\"0\"'>{$selisih}</td>
            </tr>";
            }

            echo "</table>";
        }, $filename, [
            "Content-Type" => "application/vnd.ms-excel; charset=UTF-8",
        ]);
    }
}
