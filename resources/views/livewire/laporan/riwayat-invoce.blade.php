<div>
    <form wire:submit.prevent='getRiwayat()'>
        <div class="row my-2 mx-2">
            <div class="col-md-2">
                <input class="form-control form-control-sm" placeholder="Pencarian.." wire:model.lazy="carinomor">
            </div>
            <div class="col-md-2">
                <select class="form-control form-control-sm" wire:model.lazy="filter_by">
                    <option value="tgl_cetak">Berdasarkan Tgl Cetak</option>
                    <option value="periode_rawat">Berdasarkan Periode Rawat</option>
                </select>
            </div>
            <div class="col-md-2">
                <input class="form-control form-control-sm" type="date" wire:model.lazy="tgl_cetak1">
            </div>
            <div class="col-md-2">
                <input class="form-control form-control-sm" type="date" wire:model.lazy="tgl_cetak2">
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <select class="form-control" name="" id="" wire:model.lazy="status_lanjut">
                        <option value="Ranap">Ranap</option>
                        <option value="Ralan">Ralan</option>
                        <option value="">Semua Status</option>
                    </select>
                    <span class="input-group-append">
                        <button class="btn btn-sidebar btn-primary btn-sm" title="Cari Data">
                            <i class="fas fa-search fa-fw"></i>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                                wire:target='getRiwayat'></span>
                        </button>
                    </span>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <a href="{{ url('export-rekap-invoice-asuransi') }}?tgl_cetak1={{ $tgl_cetak1 }}&tgl_cetak2={{ $tgl_cetak2 }}&status_lanjut={{ $status_lanjut }}&carinomor={{ $carinomor }}&filter_by={{ $filter_by }}" target="_blank" class="btn btn-success btn-sm font-weight-bold shadow-sm" title="Export Rekap & Rincian ke Excel">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>
    </form>
    <div class="d-flex justify-content-between align-items-center mx-2 mb-2 px-2 py-1 bg-light border rounded">
        <div>
            <span class="badge badge-primary px-2 py-1 font-weight-normal" style="font-size: 12px;">
                <i class="fas fa-file-invoice mr-1"></i> Total: <b>{{ count($getListInvoice) }}</b> Invoice
            </span>
            <span class="badge badge-info px-2 py-1 ml-1 font-weight-normal" style="font-size: 12px;">
                <i class="fas fa-calendar-alt mr-1"></i> {{ $filter_by === 'periode_rawat' ? 'Periode Rawat' : 'Tgl Cetak' }}: <b>{{ date('d/m/Y', strtotime($tgl_cetak1)) }} - {{ date('d/m/Y', strtotime($tgl_cetak2)) }}</b>
            </span>
            <span class="badge badge-secondary px-2 py-1 ml-1 font-weight-normal" style="font-size: 12px;">
                Pelayanan: <b>{{ $status_lanjut ?: 'Semua (Ranap & Ralan)' }}</b>
            </span>
        </div>
        <div>
            <a href="{{ url('export-rekap-invoice-asuransi') }}?tgl_cetak1={{ $tgl_cetak1 }}&tgl_cetak2={{ $tgl_cetak2 }}&status_lanjut={{ $status_lanjut }}&carinomor={{ $carinomor }}&filter_by={{ $filter_by }}" target="_blank" class="btn btn-outline-success btn-xs font-weight-bold">
                <i class="fas fa-download mr-1"></i> Download Rekap (.xls)
            </a>
        </div>
    </div>
    @if (session('sucsessHapus'))
        <div class="alert alert-success alert-dismissible fade show mx-2" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('sucsessHapus') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('gagalHapus'))
        <div class="alert alert-danger alert-dismissible fade show mx-2" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('gagalHapus') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
        <thead>
            <tr>
                <th colspan="6" class="text-center"><b>Riwayat Tagihan</b></th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>Nomor Tagihan</th>
                <th>Nama Asuransi</th>
                <th>Tanggal Cetak</th>
                <th>Status Lanjut</th>
                <th>Mode Piutang</th>
                <th>Lamiran</th>
                <th>Act</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getListInvoice as $keyRiwayat => $invoice)
                <tr>
                    <td>
                        <div class="badge-group">
                            <a data-toggle="modal" data-target="#ModalkeyRiwayat{{ $keyRiwayat }}"
                                class="text-warning mx-2" href="#"><i class="fas fa-edit"></i></a>
                            {{ $invoice->nomor_tagihan }}
                        </div>
                        <div class="modal fade" id="ModalkeyRiwayat{{ $keyRiwayat }}" tabindex="-1" role="dialog"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Ubah data : {{ $invoice->nomor_tagihan }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Tanggal Cetak
                                                    </label>
                                                    <input type="date" class="form-control" placeholder="Enter ..."
                                                        wire:model.defer="getListInvoice.{{ $keyRiwayat }}.tgl_cetak">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-primary"
                                            wire:click="updateRiwayatinvoice('{{ $keyRiwayat }}', '{{ $invoice->nomor_tagihan }}')"
                                            data-dismiss="modal">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $invoice->nama_asuransi }}</td>
                    <td>{{ $invoice->tgl_cetak }}</td>
                    <td>{{ $invoice->status_lanjut }}</td>
                    <td>
                        @if (($invoice->mode_cob ?? 'non_cob') == 'cob')
                            <span class="badge badge-warning"><i class="fas fa-layer-group mr-1"></i> COB</span>
                        @else
                            <span class="badge badge-info">Non-COB</span>
                        @endif
                    </td>
                    <td>{{ $invoice->lamiran }}</td>
                    <td>
                        <div>
                            <div class="input-group input-group-sm">
                                <select class="form-control form-control-sm" wire:model.lazy="template">
                                    <option value="template1">Template 1</option>
                                    <option value="template2">Template 2</option>
                                    <option value="template3">Template 3</option>
                                    <option value="template4">Template 4</option>
                                    <option value="template5">Template 5</option>
                                    <option value="template6">Template 6</option>
                                    <option value="template7">Template 7</option>
                                </select>
                                <span class="input-group-append">
                                    <a target="_blank"
                                        href="{{ url('cetak-invoice-asuransi', ['nomor_tagihan' => urlencode($invoice->nomor_tagihan), 'template' => $template]) }}"
                                        class="btn btn-primary btn-flat" title="Cetak"><i class="fa fa-print"
                                            aria-hidden="true"></i></a>
                                    <a target="_blank"
                                        href="{{ url('export-excel-single-invoice') }}?nomor_tagihan={{ urlencode($invoice->nomor_tagihan) }}"
                                        class="btn btn-success btn-flat ml-1" title="Export Excel Rincian Pasien (No, Nama, Jumlah Biaya, No Kwit, dll)"><i class="fas fa-file-excel"></i></a>
                                    <button type="button" class="btn btn-danger btn-flat ml-1" title="Hapus Invoice"
                                        wire:click="hapusInvoice('{{ $invoice->nomor_tagihan }}')"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus invoice {{ $invoice->nomor_tagihan }}? Pasien pada invoice ini dapat dibuatkan invoice kembali.') || event.stopImmediatePropagation()"><i class="fa fa-trash"></i></button>
                                </span>
                            </div>

                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
