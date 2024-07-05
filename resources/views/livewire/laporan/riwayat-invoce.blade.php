<div>
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
            @foreach ($getListInvoice as $key => $invoice)
                <tr>
                    <td>
                        <div class="badge-group">
                            <a data-toggle="modal" data-target="#updateModal{{ $key }}" class="text-warning mx-2"
                                href="#"><i class="fas fa-edit"></i></a>
                            {{ $invoice->nomor_tagihan }}
                        </div>
                        <div class="modal fade" id="updateModal{{ $key }}" tabindex="-1" role="dialog"
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
                                                    <label>nama_perusahaan
                                                    </label>
                                                    {{-- <input type="text" class="form-control"
                                                        placeholder="Enter ..."
                                                        wire:model.defer="getAsuransi.{{ $key }}.nama_perusahaan"> --}}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>alamat_asuransi
                                                    </label>
                                                    {{-- <input type="text" class="form-control"
                                                        placeholder="Enter ..."
                                                        wire:model.defer="getAsuransi.{{ $key }}.alamat_asuransi"> --}}
                                                </div>
                                            </div>
                                        </div>
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
                            <form action="{{ url('cetak-invoice-asuransi') }}">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input hidden name="nomor_tagihan" value="{{ $invoice->nomor_tagihan }}">
                                    <select class="form-control form-control-sm" name="template" id="">
                                        <option value="template1">Template 1</option>
                                        <option value="template2">Template 2</option>
                                        <option value="template3">Template 3</option>
                                    </select>
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-print"
                                                aria-hidden="true"></i></button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
