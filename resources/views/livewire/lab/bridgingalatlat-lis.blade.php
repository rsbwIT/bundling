<div>
    <div class="card-header">
        <form wire:submit.prevent="getDataKhanza">
            <div class="row">
                <div class="col-lg-3">
                    <div class="input-group">
                        <input class="form-control form-control-sidebar form-control-sm" type="text" aria-label="Search"
                            placeholder="Cari Sep / Rm / No.Rawat" wire:model.defer="carinomor">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" class="form-control form-control-sidebar form-control-sm"
                        wire:model.defer="tanggal1">
                </div>
                <div class="col-lg-2">
                    <input type="date" class="form-control form-control-sidebar form-control-sm"
                        wire:model.defer="tanggal2">
                </div>
                <div class="col-lg-2">
                    <div class="input-group">
                        <select class="form-control form-control-sidebar form-control-sm"
                            wire:model.lazy="status_lanjut">
                            <option value="Ranap">Ranap</option>
                            <option value="Ralan">Ralan</option>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-sidebar btn-primary btn-sm" wire:click="render()">
                                <i class="fas fa-search fa-fw"></i>
                                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                    wire:loading wire:target="getDataKhanza"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body table-responsive p-0" style="height: 450px;">
        <table class="table text-nowrap table-sm table-bordered table-hover table-head-fixed p-3 text-sm"
            style="white-space: nowrap;">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>No. Rawat</th>
                    <th>Pasien</th>
                    <th>Tanggal Lahir</th>
                    <th>NIP</th>
                    <th>Penjab</th>
                    <th>Dokter</th>
                    <th>dr_perujuk</th>
                    <th>Poli</th>
                    <th>Tgl Permintaan</th>
                    <th>Act</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($getDatakhanza as $key => $data)
                    <tr>
                        <td>
                            <div class="d-flex justify-content-between">
                                {{ $data->noorder }} &nbsp;
                                <div class="badge-group-sm float-right">
                                    <a data-toggle="dropdown" href="#"><i class="fas fa-eye"></i></a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        @foreach ($data->Permintaan as $item)
                                            <div class="dropdown-item">
                                                {{ $item->nm_perawatan }} - ( {{ $item->kd_jenis_prw }})
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </td>
                        <td>{{ $data->no_rawat }}</td>
                        <td>{{ $data->nm_pasien }} - {{ $data->no_rkm_medis }} - ({{ $data->jk }})</td>
                        <td>{{ $data->tgl_lahir }}</td>
                        <td>{{ $data->nip }}</td>
                        <td>{{ $data->png_jawab }}</td>
                        <td>{{ $data->nm_dokter }}</td>
                        <td>{{ $data->dr_perujuk }}</td>
                        <td>{{ $data->nm_poli }}</td>
                        <td>{{ $data->tgl_permintaan }}</td>
                        <td>
                            <button id="dropdownSubMenu1{{ $key }}" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"
                                class="btn btn-default btn-sm dropdown-toggle dropdown dropdown-hover py-0"></button>
                            <div>
                                <ul aria-labelledby="dropdownSubMenu1{{ $key }}"
                                    class="dropdown-menu border-0 shadow">
                                    <li><button class="dropdown-item" data-toggle="modal"
                                            data-target="#KirimDataLIS{{ $key }}">Kirim ke SOFTMEDIX</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        {{-- MODAL KIRIM --}}
                        <div class="modal fade" id="KirimDataLIS{{ $key }}" tabindex="-1" role="dialog"
                            aria-hidden="true" wire:ignore.self>
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Order Data SOFTMEDIX LIS
                                            <b>{{ $data->nm_pasien }}</b>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Cito
                                                    </label>
                                                    <select class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.cito">
                                                        <option value="Y">Cito</option>
                                                        <option value="N">Tidak Cito</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Sembunyikan Nama
                                                    </label>
                                                    <select class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.med_legal">
                                                        <option value="Y">Ya</option>
                                                        <option value="N">Tidak</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Catan 1
                                                    </label>
                                                    <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve1"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Catan 2
                                                    </label>
                                                    <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve2"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Catan 3
                                                    </label>
                                                    <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Catan 4
                                                    </label>
                                                    <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve4"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-primary"
                                            wire:click="sendDataToLIS('{{ $key }}')"
                                            data-dismiss="modal">Kirim</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
