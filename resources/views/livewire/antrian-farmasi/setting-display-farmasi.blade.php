<div>
    <div id="accordion">
        <div class="card-header bg-primary">
            <h4 class="card-title w-100">
                <a class="d-block w-100 text-white" data-toggle="collapse" href="#collapseThree">
                    <i class="fas fa-plus"></i> Setting Display Farmasi
                </a>
            </h4>
        </div>
        <div class="card-body">
            <div id="collapseThree" class="collapse show" data-parent="#accordion">
                @if (Session::has('message'))
                    <div class="alert alert-{{ Session::get('color') }} alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-{{ Session::get('icon') }}"></i> {{ Session::get('message') }}!
                    </div>
                @endif
                <form wire:submit.prevent="addDisplayFarmasi">
                    <div class="row mb-2">
                        <div class="col-3">
                            <input type="text" class="form-control" placeholder="Kode Display"
                                wire:model.lazy="kd_display_farmasi">
                            @error('kd_display_farmasi')
                                <span class="text-danger text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" placeholder="Nama Display"
                                wire:model.lazy="nama_display_farmasi">
                            @error('nama_display_farmasi')
                                <span class="text-danger text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </div>
                    </div>
                </form>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Display</th>
                            <th>Nama Display</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getDisplayFarmasi as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->kd_display_farmasi }}</td>
                                <td>{{ $item->nama_display_farmasi }}</td>
                                <td class="text-center">
                                    <div class="badge-group">
                                        <a class="mx-2" data-toggle="modal"
                                            data-target="#updateModal{{ $item->kd_display_farmasi }}" href=""><i
                                                class="fas fa-edit"></i></a>
                                        <a class="mx-2 text-danger" data-toggle="modal"
                                            data-target="#deleteModal{{ $item->kd_display_farmasi }}" href=""><i
                                                class="fas fa-trash"></i></a>
                                    </div>
                                    <div class="modal fade" id="updateModal{{ $item->kd_display_farmasi }}" tabindex="-1"
                                        role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateModalLabel{{ $key }}">
                                                        Edit Pendaftaran
                                                    </h5>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal"aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            wire:model.defer="getDisplayFarmasi.{{ $key }}.kd_display_farmasi">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            wire:model.defer="getDisplayFarmasi.{{ $key }}.nama_display">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary"
                                                            wire:click.prevent="editDisplayFarmasi('{{ $key }}', '{{ $item->kd_displayFarmasi }}')"
                                                            data-dismiss="modal">Ubah</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="deleteModal{{ $item->kd_display_farmasi }}" tabindex="-1"
                                        role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="delteModalLabel{{ $key }}">
                                                        Hapus Display
                                                    </h5>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal"aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <span>Anda yakin ingin menghapus diplay {{ $item->nama_display_farmasi }}</span>
                                                </div>
                                                <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger"
                                                            wire:click.prevent="deleteDisplay_Farmasi('{{ $key }}', '{{ $item->kd_display_Farmasi }}')"
                                                            data-dismiss="modal">Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
