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
    public $filter_by;
    public function mount()
    {
        $this->tgl_cetak1 = date('Y-m-d');
        $this->tgl_cetak2 = date('Y-m-d');
        $this->status_lanjut = "Ranap";
        $this->filter_by = "tgl_cetak";
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
        $query = DB::table('bw_invoice_asuransi')
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
            );

        if ($this->filter_by === 'periode_rawat') {
            $query->where(function ($q) {
                $q->whereBetween('bw_invoice_asuransi.tanggl1', [$this->tgl_cetak1, $this->tgl_cetak2])
                  ->orWhereBetween('bw_invoice_asuransi.tanggl2', [$this->tgl_cetak1, $this->tgl_cetak2])
                  ->orWhere(function ($sub) {
                      $sub->where('bw_invoice_asuransi.tanggl1', '<=', $this->tgl_cetak1)
                          ->where('bw_invoice_asuransi.tanggl2', '>=', $this->tgl_cetak2);
                  });
            });
        } else {
            $query->whereBetween('bw_invoice_asuransi.tgl_cetak', [$this->tgl_cetak1, $this->tgl_cetak2]);
        }

        if (!empty($this->status_lanjut)) {
            $query->where('bw_invoice_asuransi.status_lanjut', $this->status_lanjut);
        }

        if (!empty($cariKode)) {
            $query->where(function ($q) use ($cariKode) {
                $q->orWhere('bw_invoice_asuransi.kode_asuransi', 'LIKE', "%$cariKode%")
                  ->orWhere('bw_invoice_asuransi.nama_asuransi', 'LIKE', "%$cariKode%")
                  ->orWhere('bw_invoice_asuransi.nomor_tagihan', 'LIKE', "%$cariKode%");
            });
        }

        $this->getListInvoice = $query->orderBy('bw_invoice_asuransi.nomor_tagihan', 'desc')->get();
    }

    public function updateRiwayatinvoice($keyRiwayat, $nomor_tagihan)
    {
        DB::table('bw_invoice_asuransi')->where('nomor_tagihan', $nomor_tagihan)
            ->update([
                'tgl_cetak' =>  $this->getListInvoice[$keyRiwayat]['tgl_cetak']
            ]);
    }

    public function hapusInvoice($nomor_tagihan)
    {
        DB::beginTransaction();
        try {
            DB::table('bw_detail_invoice_asuransi')->where('nomor_tagihan', $nomor_tagihan)->delete();
            DB::table('bw_invoice_asuransi')->where('nomor_tagihan', $nomor_tagihan)->delete();
            DB::commit();
            $this->getRiwayat();
            session()->flash('sucsessHapus', 'Invoice ' . $nomor_tagihan . ' berhasil dihapus. Pasien terkait dapat dipilih kembali untuk dibuatkan invoice.');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('gagalHapus', 'Gagal menghapus invoice: ' . $th->getMessage());
        }
    }
}
