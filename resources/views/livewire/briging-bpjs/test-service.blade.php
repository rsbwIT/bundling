<div>
    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="date" class="form-control" wire:model.lazy="date">
                                <span class="text-sm text-secondary">{{ $date }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="time" class="form-control"wire:model.lazy="time">
                                <span class="text-sm text-secondary">{{ $time }} WIB</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" wire:model.lazy="menit">
                                    <option selected>Jarak Pelayanan Pasien (Menit)</option>
                                    @for ($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <select class="form-control" wire:model.lazy="taskid">
                                    <option selected>Pilih Task id</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="99" class="text-danger">99 (Untuk Batal)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for=""></label>
                                <textarea class="form-control" name="" id="" rows="3" wire:model.lazy='kode_booking'
                                    placeholder="Masukan List kode booking pasien dengan dokter yang sama Contoh : 20240507000001,20240507000002,20240507000003"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        @if ($getCekin)
                            @foreach ($getCekin as $response)
                                @foreach ($response as $item)
                                    @if (is_object($item))
                                        @if ($item->code == 200)
                                            <div class="alert alert-success alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <i class="icon fas fa-check"></i> <b>{{ $item->kodebooking }}</b>
                                                Terkirim !
                                                {{ $item->code }}
                                                status : {{ $item->message }}
                                            </div>
                                        @else
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <i class="icon fas fa-ban"></i> <b>{{ $item->kodebooking }}</b> Gagal
                                                Kirm
                                                Task Id! {{ $item->code }}
                                                status : {{ $item->message }}
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>
                <button type="button" class="btn btn-primary" wire:click='Cekin'>Cekin
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                        wire:target='Cekin'></span>
                </button>
            </div>
        </div>


    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <button type="button" class="btn btn-primary" wire:click='TestKoneksi'>CEK Koneksi Briging V-Claim
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                        wire:target='Cekin'></span>
                </button>
            </div>
        </div>
    </div>
</div>
