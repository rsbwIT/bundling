<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PantauUgdExportController extends Controller
{
    /**
     * Export data pasien UGD ke Excel (SpreadsheetML / .xls)
     * dengan sheet terpisah: Ralan & Ranap.
     * Parameter: tgl_awal, tgl_akhir, kd_pj (opsional), search (opsional)
     */
    public function export(Request $request)
    {
        $tglAwal  = $request->get('tgl_awal') ?: date('Y-m-d');
        $tglAkhir = $request->get('tgl_akhir') ?: date('Y-m-d');
        $kdPj     = $request->get('kd_pj');
        $search   = $request->get('search');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->select(
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'penjab.png_jawab as jenis_bayar',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut'
            )
            ->whereBetween('reg_periksa.tgl_registrasi', [$tglAwal, $tglAkhir])
            ->where(function ($q) {
                $q->where('poliklinik.nm_poli', 'like', '%IGD%')
                  ->orWhere('poliklinik.nm_poli', 'like', '%UGD%');
            });

        if (!empty($kdPj)) {
            $query->where('reg_periksa.kd_pj', $kdPj);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pasien.nm_pasien', 'like', '%' . $search . '%')
                  ->orWhere('reg_periksa.no_rawat', 'like', '%' . $search . '%')
                  ->orWhere('pasien.no_rkm_medis', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->orderBy('reg_periksa.tgl_registrasi')
                      ->orderBy('reg_periksa.jam_reg')
                      ->get();

        $perPj = $rows->groupBy('jenis_bayar')->sortKeys();

        $namaPj = 'SEMUA';
        if (!empty($kdPj)) {
            $namaPj = DB::table('penjab')->where('kd_pj', $kdPj)->value('png_jawab') ?: $kdPj;
        }

        $periode = date('d-m-Y', strtotime($tglAwal)) . ' s/d ' . date('d-m-Y', strtotime($tglAkhir));

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
              . 'xmlns:o="urn:schemas-microsoft-com:office:office" '
              . 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
              . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles>
  <Style ss:ID="title"><Font ss:Bold="1" ss:Size="14"/></Style>
  <Style ss:ID="sub"><Font ss:Italic="1" ss:Color="#555555"/></Style>
  <Style ss:ID="head">
    <Font ss:Bold="1" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#3D5A80" ss:Pattern="Solid"/>
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
    </Borders>
  </Style>
  <Style ss:ID="cell">
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
    </Borders>
  </Style>
  <Style ss:ID="total"><Font ss:Bold="1"/><Interior ss:Color="#E8EEF5" ss:Pattern="Solid"/></Style>
  <Style ss:ID="headRalan" ss:Parent="head"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#5B8DB8" ss:Pattern="Solid"/></Style>
  <Style ss:ID="headRanap" ss:Parent="head"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#C06C6C" ss:Pattern="Solid"/></Style>
  <Style ss:ID="totalRalan"><Font ss:Bold="1" ss:Color="#3D5A80"/><Interior ss:Color="#EAF2F9" ss:Pattern="Solid"/></Style>
  <Style ss:ID="totalRanap"><Font ss:Bold="1" ss:Color="#9E4F4F"/><Interior ss:Color="#F8ECEC" ss:Pattern="Solid"/></Style>
</Styles>' . "\n";

        $xml .= $this->rekapSheet($perPj, $namaPj, $periode);

        $usedNames = ['rekap' => true];
        foreach ($perPj as $pj => $list) {
            foreach (['Ralan', 'Ranap'] as $status) {
                $data = $list->where('status_lanjut', $status)->values();
                $sheetName = $this->sheetName($pj, ' - ' . $status, $usedNames);
                $xml .= $this->sheet($sheetName, $data, $pj, $periode, $status);
            }
        }
        $xml .= '</Workbook>';

        $safePj   = preg_replace('/[^A-Za-z0-9_-]+/', '_', $namaPj);
        $filename = 'Pasien_UGD_' . $safePj . '_' . $tglAwal . '_sd_' . $tglAkhir . '.xls';

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function e($v)
    {
        return htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /** Nama sheet Excel: maks 31 karakter, tanpa : \ / ? * [ ], dan harus unik. */
    private function sheetName($pj, $suffix, array &$used)
    {
        $clean = trim(preg_replace('/[:\\\\\/\?\*\[\]]+/', ' ', $pj));
        $n = 1;
        do {
            $extra = $n > 1 ? ' ' . $n : '';
            $final = rtrim(mb_substr($clean, 0, 31 - mb_strlen($suffix . $extra))) . $extra . $suffix;
            $n++;
        } while (isset($used[mb_strtolower($final)]));
        $used[mb_strtolower($final)] = true;
        return $final;
    }

    private function rekapSheet($perPj, $namaPj, $periode)
    {
        $x  = '<Worksheet ss:Name="Rekap"><Table>' . "\n";
        $x .= '<Column ss:Width="35"/><Column ss:Width="200"/><Column ss:Width="70"/><Column ss:Width="70"/><Column ss:Width="70"/>' . "\n";
        $x .= '<Row><Cell ss:StyleID="title"><Data ss:Type="String">REKAP PASIEN UGD PER JENIS BAYAR</Data></Cell></Row>' . "\n";
        $x .= '<Row><Cell ss:StyleID="sub"><Data ss:Type="String">' . $this->e('Jenis Bayar: ' . $namaPj . '  |  Periode: ' . $periode) . '</Data></Cell></Row>' . "\n";
        $x .= '<Row></Row>' . "\n";
        $x .= '<Row>' . $this->cell('No', 'head') . $this->cell('Jenis Bayar', 'head') . $this->cell('Ralan', 'head') . $this->cell('Ranap', 'head') . $this->cell('Total', 'head') . '</Row>' . "\n";

        $no = 1; $tRalan = 0; $tRanap = 0; $tAll = 0;
        foreach ($perPj as $pj => $list) {
            $ralan = $list->where('status_lanjut', 'Ralan')->count();
            $ranap = $list->where('status_lanjut', 'Ranap')->count();
            $total = $list->count();
            $tRalan += $ralan; $tRanap += $ranap; $tAll += $total;
            $x .= '<Row>'
                . $this->cell($no++, 'cell', 'Number')
                . $this->cell($pj)
                . $this->cell($ralan, 'cell', 'Number')
                . $this->cell($ranap, 'cell', 'Number')
                . $this->cell($total, 'cell', 'Number')
                . '</Row>' . "\n";
        }
        $x .= '<Row>'
            . $this->cell('', 'total')
            . $this->cell('TOTAL', 'total')
            . $this->cell($tRalan, 'total', 'Number')
            . $this->cell($tRanap, 'total', 'Number')
            . $this->cell($tAll, 'total', 'Number')
            . '</Row>' . "\n";
        $x .= '</Table></Worksheet>' . "\n";
        return $x;
    }

    private function cell($v, $style = 'cell', $type = 'String')
    {
        return '<Cell ss:StyleID="' . $style . '"><Data ss:Type="' . $type . '">' . $this->e($v) . '</Data></Cell>';
    }

    private function sheet($name, $rows, $namaPj, $periode, $status = null)
    {
        $headers = ['No', 'No. Rawat', 'No. RM', 'Nama Pasien', 'JK', 'Tgl Registrasi', 'Jam', 'Poliklinik', 'Dokter', 'Jenis Bayar', 'Status', 'Status Lanjut'];
        $widths  = [35, 130, 70, 180, 30, 85, 60, 110, 160, 120, 70, 80];

        $x  = '<Worksheet ss:Name="' . $this->e($name) . '"><Table>' . "\n";
        foreach ($widths as $w) {
            $x .= '<Column ss:Width="' . $w . '"/>';
        }
        $x .= "\n";
        $x .= '<Row><Cell ss:StyleID="title"><Data ss:Type="String">' . $this->e('DATA PASIEN UGD - ' . strtoupper($namaPj) . ' - ' . strtoupper($status ?: $name)) . '</Data></Cell></Row>' . "\n";
        $x .= '<Row><Cell ss:StyleID="sub"><Data ss:Type="String">' . $this->e('Jenis Bayar: ' . $namaPj . '  |  Periode: ' . $periode) . '</Data></Cell></Row>' . "\n";
        $x .= '<Row></Row>' . "\n";

        $headStyle  = $status === 'Ranap' ? 'headRanap' : ($status === 'Ralan' ? 'headRalan' : 'head');
        $totalStyle = $status === 'Ranap' ? 'totalRanap' : ($status === 'Ralan' ? 'totalRalan' : 'total');

        $x .= '<Row>';
        foreach ($headers as $h) {
            $x .= $this->cell($h, $headStyle);
        }
        $x .= '</Row>' . "\n";

        foreach ($rows as $i => $r) {
            $x .= '<Row>'
                . $this->cell($i + 1, 'cell', 'Number')
                . $this->cell($r->no_rawat)
                . $this->cell($r->no_rkm_medis)
                . $this->cell($r->nm_pasien)
                . $this->cell($r->jk)
                . $this->cell(date('d-m-Y', strtotime($r->tgl_registrasi)))
                . $this->cell($r->jam_reg)
                . $this->cell($r->nm_poli)
                . $this->cell($r->nm_dokter)
                . $this->cell($r->jenis_bayar)
                . $this->cell($r->stts)
                . $this->cell($r->status_lanjut)
                . '</Row>' . "\n";
        }

        $x .= '<Row><Cell ss:StyleID="' . $totalStyle . '" ss:MergeAcross="2"><Data ss:Type="String">TOTAL ' . $this->e(strtoupper($status ?: $name)) . ': ' . count($rows) . ' pasien</Data></Cell></Row>' . "\n";
        $x .= '</Table></Worksheet>' . "\n";

        return $x;
    }
}
