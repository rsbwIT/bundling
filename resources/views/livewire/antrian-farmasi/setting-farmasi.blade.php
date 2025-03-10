<div>
    <div id="loket">
        <div class="card-header bg-primary">
            <h4 class="card-title w-100">
                <a class="d-block w-100 text-white" data-toggle="collapse" href="#collapseLoket">
                    <i class="fas fa-plus"></i> Setting Farmasi
                </a>
            </h4>
        </div>
        <div class="card-body">
            <div id="collapseLoket" class="collapse show" data-parent="#loket">
                @if (Session::has('message'))
                    <div class="alert alert-{{ Session::get('color') }} alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fas fa-{{ Session::get('icon') }}"></i> {{ Session::get('message') }}!
                    </div>
                @endif
                <form wire:submit.prevent="addPoli">
                    <div class="row mb-3">
                        <div class="col-2">
                            <input type="text" class="form-control" placeholder="Kode Loket" wire:model.lazy="kd_ruang_farmasi">
                            @error('kd_ruang_farmasi')
                                <span class="text-danger text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control" placeholder="Nama Loket" wire:model.lazy="nama_ruang_farmasi">
                            @error('nama_ruang_farmasi')
                                <span class="text-danger text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-2">
                            <select class="form-control" wire:model.lazy="kd_display_farmasi" placeholder="Lokasi">
                                <option>Pilih Lokasi Pendaftaran</option>
                                @foreach ($getFarmasi as $item)
                                    <option value="{{ $item->kd_display_farmasi }}">{{ $item->nama_display_farmasi }}</option>
                                @endforeach
                            </select>
                            @error('kd_display_farmasi')
                                <span class="text-danger text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-2">
                            <select class="form-control" wire:model.lazy="posisi_display_farmasi" placeholder="Lokasi">
                                <option>Pilih Posisi</option>
                                    <option value="1">Kanan</option>
                                    <option value="0">Kiri</option>
                            </select>
                            @error('kd_display_farmasi')
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
                            <th>Kd Poli</th>
                            <th>Nama Poli</th>
                            <th>Posisi</th>
                            <th>Tempat</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getFarmasi as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->kd_ruang_farmasi }}</td>
                                <td>{{ $item->nama_ruang_farmasi }}</td>
                                @php
                                    if ($item->posisi_display_farmasi == '1') {
                                        $textkanan = 'text-success';
                                        $textkiri = 'text-muted';
                                    } else {
                                        $textkanan = 'text-muted';
                                        $textkiri = 'text-success';
                                    }
                                @endphp
                                <td>
                                    <i class="fas fa-backward {{ $textkiri }} "></i> <b>|</b> <i
                                        class="fas fa-forward {{ $textkanan }}"></i>
                                </td>
                                <td>{{ $item->nama_display_farmasi }}</td>

                                <td class="text-center">
                                    <div class="badge-group">
                                        <a class="mx-2" data-toggle="modal"
                                            data-target="#updateLoket{{ str_replace(' ', '', $item->kd_ruang_farmasi) }}"
                                            href=""><i class="fas fa-edit"></i></a>
                                        <a class="mx-2 text-danger" href=""
                                            wire:click.prevent="deletePoli('{{ $item->kd_ruang_farmasi }}')"><i
                                                class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                                <div class="modal fade"
                                    id="updateLoket{{ str_replace(' ', '', $item->kd_ruang_farmasi) }}" tabindex="-1"
                                    role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="updateLoket{{ $key }}">
                                                    Edit Poli {{ $item->kd_ruang_farmasi }}
                                                </h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal"aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="">Nama Poli</label>
                                                    <input type="text" class="form-control"
                                                        wire:model.defer="getFarmasi.{{ $key }}.nama_ruang_farmasi">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Display</label>
                                                    <select class="form-control"
                                                        wire:model.defer="getfarmasi.{{ $key }}.kd_display_farmasi"
                                                        placeholder="Lokasi">
                                                        @foreach ($getDisplayFarmasi as $data)
                                                            <option value="{{ $data->kd_display_Farmasi }}">
                                                                {{ $data->nama_display_farmasi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Posisi</label>
                                                    <select class="form-control"
                                                        wire:model.defer="getFarmasi.{{ $key }}.posisi_display_farmasi"
                                                        placeholder="Lokasi">
                                                        <option value="1">Kanan</option>
                                                        <option value="0">Kiri</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"
                                                    wire:click.prevent="editFarmasi('{{ $key }}', '{{ $item->kd_ruang_Farmasi }}')"
                                                    data-dismiss="modal">Ubah</button>
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
    </div>
</div>
