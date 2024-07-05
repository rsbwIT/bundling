<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatInvoce extends Component
{
    public $status_lanjut;
    public $kdPenjamin;
    public function mount(Request $request)
    {
        $this->getRiwayat();
        $this->status_lanjut = $request->status_lanjut;
        $this->kdPenjamin = $request->input('kdPenjamin') ? explode(',', $request->input('kdPenjamin')) : [];
    }

    public function render()
    {
        $this->getRiwayat();
        return view('livewire.laporan.riwayat-invoce');
    }

    public $getListInvoice;
    public $tgl_cetak;
    public $nomor_tagihan;
    public function getRiwayat()
    {
        $this->getListInvoice = DB::table('bw_invoice_asuransi')
            ->select(
                'bw_invoice_asuransi.nomor_tagihan',
                'bw_invoice_asuransi.kode_asuransi',
                'bw_invoice_asuransi.nama_asuransi',
                'bw_invoice_asuransi.alamat_asuransi',
                'bw_invoice_asuransi.tanggl1',
                'bw_invoice_asuransi.tanggl2',
                'bw_invoice_asuransi.tgl_cetak',
                'bw_invoice_asuransi.status_lanjut',
                'bw_invoice_asuransi.lamiran'
            )
            ->where('bw_invoice_asuransi.kode_asuransi', $this->kdPenjamin)
            ->where('bw_invoice_asuransi.status_lanjut', $this->status_lanjut)
            ->orderBy('bw_invoice_asuransi.nomor_tagihan', 'desc')
            ->get();
    }

    public function updateRiwayatinvoice($key, $nomor_tagihan)
    {
        DB::table('bw_invoice_asuransi')->where('nomor_tagihan', $nomor_tagihan)
            ->update([
                'tgl_cetak' =>  $this->getListInvoice[$key]['tgl_cetak']
            ]);
    }
}
