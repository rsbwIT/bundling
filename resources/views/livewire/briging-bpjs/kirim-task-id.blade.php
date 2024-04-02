<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cekin / Kirim TaskId</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    @if ($getCekin)
                        @foreach ($getCekin as $item)
                            @if (is_object($item))
                                @if ($item->code == 200)
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-check"></i> Terkirim ! {{ $item->code }}
                                        status : {{ $item->message }}
                                    </div>
                                @else
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-ban"></i> Gagal Kirm Task Id! {{ $item->code }}
                                        status : {{ $item->message }}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endif
                    @if ($getCekinFktl)
                        @foreach ($getCekinFktl as $item)
                            @if (is_object($item))
                                @if ($item->code == 200)
                                    <div class="alert alert-success alert-sm alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-check"></i> Terkirim ! {{ $item->code }}
                                        status : {{ $item->message }}
                                    </div>
                                @else
                                    <div class="alert alert-danger alert-sm alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <i class="icon fas fa-ban"></i> Gagal Cekin Ke Khanza! {{ $item->code }}
                                        status : {{ $item->message }}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="test" class="form-control" wire:model.lazy="kodebooking"
                            placeholder="Kode Booking">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <select class="form-control" wire:model.lazy="taskid">
                            <option selected>Pilih Task id</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="99" class="text-danger">99 (Untuk Batal)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-check-label" @if ($taskid != 3) hidden @endif>
                            <input type="checkbox" wire:model.lazy="konfirmasi_cekin">
                            Cekin Khanza ?
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="date" class="form-control" wire:model.lazy="date">
                        <span class="text-sm text-secondary">{{ $date }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="time" class="form-control"wire:model.lazy="time">
                        <span class="text-sm text-secondary">{{ $time }} WIB</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" wire:click="cekinBPJS()">
                            Submit
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                                wire:target='cekinBPJS'></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getPasienMJKN">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="input-group">
                            <input class="form-control form-control-sidebar form-control-sm" type="text"
                                aria-label="Search" placeholder="Cari Kode Booking / Rm / No.Rawat"
                                wire:model.defer="carinomor">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <input type="date" class="form-control form-control-sidebar form-control-sm"
                            wire:model.defer="tanggal1">
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group">
                            <input type="date" class="form-control form-control-sidebar form-control-sm"
                                wire:model.defer="tanggal2">
                            <div class="input-group-append">
                                <button class="btn btn-sidebar btn-primary btn-sm" wire:click="render()">
                                    <i class="fas fa-search fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <input type="date" class="form-control form-control-sidebar form-control-sm" wire:model.lazy="date">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <input type="time" class="form-control form-control-sidebar form-control-sm"wire:model.lazy="time">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <input type="checkbox" wire:model.lazy="konfirmasi_cekin">
                                    Cekin Khanza ?
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 450px;"">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nm Pasien</th>
                        <th>No Booking</th>
                        <th>No Rawat</th>
                        <th>Nomor RM</th>
                        <th>Status Cekin</th>
                        <th>Jam Praktek</th>
                        <th>Validasi <span class="text-xs">(jam Cekin)</span></th>
                        {{-- <th>Pilih TaskId</th>
                        <th class="text-center">Act</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasienMJKN as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nobooking }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->norm }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->jampraktek }}</td>
                            <td>{{ $item->validasi }}</td>
                            {{-- <td>
                                <select class="form-control form-control-sm" wire:model.lazy="taskid.{{$key}}">
                                    <option selected>Pilih Task id</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="99" class="text-danger">99 (Untuk Batal)</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary" wire:click="cekinBPJS('{{$key}}', '{{$item->nobooking}}' )">
                                    Submit
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                                        wire:target="cekinBPJS('{{$key}}', '{{$item->nobooking}}' )"></span>
                                </button>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
