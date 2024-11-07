<div>
    <form wire:submit.prevent='getRiwayat()'>
        <div class="row my-2 mx-2">
            <div class="col-md-3">
                <input class="form-control form-control-sm" placeholder="Pencarian.." wire:model.lazy="carinomor">
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
                    </select>
                    <span class="input-group-append">
                        <button class="btn btn-sidebar btn-primary btn-sm">
                            <i class="fas fa-search fa-fw"></i>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                                wire:target='getRiwayat'></span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </form>
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
                                        class="btn btn-primary btn-flat"><i class="fa fa-print"
                                            aria-hidden="true"></i></a>
                                </span>
                            </div>

                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
