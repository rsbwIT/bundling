<div>
    <div id="dokter">
        <div class="card-header bg-primary">
            <h4 class="card-title w-100">
                <a class="d-block w-100 text-white" data-toggle="collapse" href="#collapseDokter">
                    <i class="fas fa-plus"></i> Setting Posisi Dokter
                </a>
            </h4>
        </div>
        <div class="card-body">
            <div id="collapseDokter" class="collapse show" data-parent="#dokter">
                @if (Session::has('message'))
                    <div class="alert alert-{{ Session::get('color') }} alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-{{ Session::get('icon') }}"></i> {{ Session::get('message') }}!
                    </div>
                @endif
                <table class="table table-sm text-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kd Dokter</th>
                            <th>Nama</th>
                            <th class="text-center">Lokasi Dokter</th>
                            <th class="text-center">Foto</th>
                            <th class="text-center">Kuota Tambahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getListDokter as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->kd_dokter }}</td>
                                <td>{{ $item->nm_dokter }}</td>
                                <td class="text-center">
                                    @foreach ($getLoket as $keyLoket => $data)
                                        @php
                                            $typeBtn =
                                                $data->kd_loket == $item->kd_loket
                                                    ? 'btn-primary'
                                                    : 'btn-outline-primary';
                                        @endphp
                                        <button type="button" class="btn {{ $typeBtn }} btn-xs mx-1"
                                            wire:click.prevent="editLoketConfirm('{{ $item->kd_dokter }}', '{{ $item->nm_dokter }}', '{{ $data->kd_loket }}')">
                                            {{ $data->nama_loket }}
                                        </button>
                                    @endforeach
                                </td>
                                <td width="3%" class="text-center">
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-block btn-outline-{{ $item->foto ? 'success' : 'primary' }} btn-xs btn-flat"
                                            data-toggle="modal" wire:click="SetmodalInacbg('{{ $key }}')"
                                            data-target="#UploadFotoDokter">
                                            <i class="fas {{ $item->foto ? 'fa-check' : 'fa-upload' }}"></i>
                                        </button>
                                    </div>
                                </td>
                                <td width="15%" class="text-center">

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-block btn-outline-primary btn-xs btn-flat"
                                            data-toggle="modal" wire:click="SetmodalInacbg('{{ $key }}')"
                                            data-target="#EditKuota">
                                            <b> {{ $item->kuota_tambahan }}</b> +
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        {{-- MODAL UPLOAD --}}
                        <div class="modal fade" id="UploadFotoDokter" tabindex="-1" role="dialog" aria-hidden="true"
                            wire:ignore.self>
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Upload Foto Dokter :
                                            <u>{{ $nm_dokter }}</u>
                                        </h6>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>File Foto
                                                    </label>
                                                    <input type="file" class="form-control form-control"
                                                        wire:model="foto_dokter.{{ $keyModal }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="submit" class="btn btn-primary" data-dismiss="modal"
                                            wire:click="UploadFoto('{{ $keyModal }}','{{ $kd_dokter }}')"
                                            wire:loading.remove wire:target="foto_dokter.{{ $keyModal }}">Submit
                                        </button>
                                        <div wire:loading wire:target="foto_dokter.{{ $keyModal }}">
                                            Uploading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- MODAL UPDATE KUOTA DOKTER --}}
                        <div class="modal fade" id="EditKuota" tabindex="-1" role="dialog" aria-hidden="true"
                            wire:ignore.self>
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Update Kuota Dokter :
                                            <u>{{ $nm_dokter }}</u>
                                        </h6>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Jumlah Kuota
                                                    </label>
                                                    <input type="text" class="form-control form-control"
                                                        wire:model.defer="getListDokter.{{ $keyModal }}.kuota_tambahan">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="submit" class="btn btn-primary" data-dismiss="modal"
                                            wire:click="updateKuotaDokter('{{ $keyModal }}','{{ $kd_dokter }}')">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- MODAl --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if ($confirmingEdit)
        <div class="modal fade show" tabindex="-100" role="dialog" style="padding-right: 17px; display: block;"
            aria-modal="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Anda Yakin Ingin Menginput Atau Merubah posisi dokter?</h4>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="cancelEdit()">Tidak
                                !</button>
                            <button type="button" class="btn btn-primary" wire:click="editLoket()">Ya !</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
