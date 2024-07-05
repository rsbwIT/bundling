<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatInvoce extends Component
{
    public $tgl_cetak1;
    public $tgl_cetak2;
    public $carinomor;
    public $status_lanjut;
    public $template;
    public function mount()
    {
        $this->tgl_cetak1 = date('Y-m-d');
        $this->tgl_cetak2 = date('Y-m-d');
        $this->status_lanjut = "Ranap";
        $this->template = "template2";
        $this->getRiwayat();
    }

    public function render()
    {
        $this->getRiwayat();
        return view('livewire.laporan.riwayat-invoce');
    }

    public $getListInvoice;
    public $tgl_cetak;
    public function getRiwayat()
    {
        $cariKode = $this->carinomor;
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
            ->whereBetween('bw_invoice_asuransi.tgl_cetak', [$this->tgl_cetak1, $this->tgl_cetak2])
            ->where('bw_invoice_asuransi.status_lanjut', $this->status_lanjut)
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('bw_invoice_asuransi.kode_asuransi', 'LIKE', "%$cariKode%")
                    ->orwhere('bw_invoice_asuransi.nama_asuransi', 'LIKE', "%$cariKode%");
            })
            ->orderBy('bw_invoice_asuransi.nomor_tagihan', 'desc')
            ->get();
    }

    public function updateRiwayatinvoice($keyRiwayat, $nomor_tagihan)
    {
        DB::table('bw_invoice_asuransi')->where('nomor_tagihan', $nomor_tagihan)
            ->update([
                'tgl_cetak' =>  $this->getListInvoice[$keyRiwayat]['tgl_cetak']
            ]);
    }
}
