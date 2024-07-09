<div>
    <div class="col-md-4 my-2">
        <form wire:submit.prevent='getnamaAsuransi()'>
            <div class="input-group input-group-sm">
                <input class="form-control form-control-sm" placeholder="Silahkan Cari Nama/Rm"
                    wire:model.lazy="carinomor">
                <span class="input-group-append">
                    <button class="btn btn-sidebar btn-primary btn-sm">
                        <i class="fas fa-search fa-fw"></i>
                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                            wire:target='getnamaAsuransi'></span>
                    </button>
                </span>
            </div>
        </form>
    </div>
    <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
        <thead>
            <tr>
                <th class="text-center">Kode</th>
                <th>Penjamin/Asuransi</th>
                <th>Nama Perusahaan/Asuransi</th>
                <th>Alamat Asuransi</th>
                <th>Act</th>
            </tr>
        </thead>
        <tbody>
            @if ($getAsuransi && $getAsuransi->isNotEmpty())
                @foreach ($getAsuransi as $keyAsuransi => $asuransi)
                    <tr>
                        <td class="text-center">{{ $asuransi->kd_pj }}</td>
                        <td>{{ $asuransi->png_jawab }}</td>
                        <td>{{ $asuransi->nama_perusahaan }}</td>
                        <td>{{ $asuransi->alamat_asuransi }}</td>
                        <td>200533
                            <div class="badge-group">
                                <a data-toggle="modal" data-target="#ModalkeyAsuransi{{ $keyAsuransi }}"
                                    class="text-warning mx-2" href="#"><i class="fas fa-edit"></i></a>
                            </div>
                            <div class="modal fade" id="ModalkeyAsuransi{{ $keyAsuransi }}" tabindex="-1"
                                    role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Nomor Kartu/Klaim : {{$asuransi->png_jawab}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>nama_perusahaan
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter ..."
                                                                wire:model.defer="getAsuransi.{{ $keyAsuransi }}.nama_perusahaan">
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>alamat_asuransi
                                                            </label>
                                                            <input type="textarea" class="form-control"
                                                                placeholder="Enter ..."
                                                                wire:model.defer="getAsuransi.{{ $keyAsuransi }}.alamat_asuransi">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-primary"
                                                    wire:click="updateInsertNomor('{{ $keyAsuransi }}', '{{ $asuransi->kd_pj }}')"
                                                    data-dismiss="modal">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">Silahkan cari data</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
