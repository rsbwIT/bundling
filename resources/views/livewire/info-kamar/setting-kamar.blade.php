<div>
    <div class="card ">
        <div class="card-header">
            <div class="row">
                <div class="col-4">
                    <select class="form-control form-control-sidebar form-control" wire:model="select_kamar">
                        @foreach ($getRuang as $item)
                            <option value="{{ $item->ruangan }}">{{ $item->ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-1">
                    <button class="form-control btn btn-xs text-white"
                        style="background-color: rgb(0, 26, 109);"><b>Terisi</b>
                    </button>
                </div>
                <div class="col-1">
                    <button class="form-control btn btn-xs"
                        style="background-color: rgb(255, 255, 255);  border: 1px solid;"><b>Kosong</b>
                    </button>
                </div>
                <div class="col-6">
                    @if ($respone)
                        @if ($respone['code'] == 1)
                            <span class="float-right mx-2 text-success">
                                <i class="fas fa-check mr-1"></i> Data Kamar Berhasil Update Ke JKN
                            </span>
                        @else
                            <span class="float-right mx-2 text-danger">
                                <i class="fas fa-ban mr-1"></i> Data Kamar Gagal Update Ke JKN
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- TAB INFO KAMAR --}}
            <div class="container-fluid">
                <div class="row">
                    @if ($getRuangan)
                        @foreach ($getRuangan as $item)
                            <div class="col-12">
                                <div class="card p-1" style="border: 1px solid; height: 100%">
                                    <div class="card-header text-center p-1" style="border: 1px solid">
                                        <h2 class="font-weight-bold" style="color: rgb(2, 1, 10)">
                                            {{ $item->ruangan }}
                                        </h2>
                                    </div>
                                    <div class="row  text-center">
                                        @foreach ($item->getKamar as $kamar)
                                            <div class="col-md-3">
                                                <div class="card mt-2 mb-2" style="border: 1px solid">
                                                    <h6 class="mb-"><b>{{ $kamar->kamar }} </b>
                                                        ({{ $kamar->kelas }})
                                                        {{ $kamar->nm_ruangan_bpjs }}
                                                    </h6>
                                                    <hr class="m-1" style="border: 1px solid">
                                                    <div class="row">
                                                        @php
                                                            $bed = count($kamar->getBed);
                                                            $colom = $bed == 1 ? '12' : ($bed == 2 ? '6' : '4');
                                                        @endphp

                                                        @foreach ($kamar->getBed as $bed)
                                                            @php
                                                                $baground =
                                                                    $bed->status == 1
                                                                        ? 'rgb(0, 26, 109)'
                                                                        : 'rgb(255, 255, 255)';
                                                                $text = $bed->status == 1 ? 'text-white' : 'text-black';
                                                            @endphp
                                                            <div class="col-md-{{ $colom }}">
                                                                <div class="card m-1 justify-content-center"
                                                                    style="background-color: {{ $baground }}; border:1px solid;">
                                                                    <button class="btn {{ $text }}"
                                                                        wire:click="actionIsi('{{ $bed->status }}','{{ $bed->id }}', '{{ $bed->kd_kelas_bpjs }}', '{{ $bed->nm_ruangan_bpjs }}')">
                                                                        <b>{{ substr($bed->bad, strlen($bed->bad) - 1, 1) }}</b>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <span class="text-header"><b>Tambah Bed/Kamar</b></span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="input-group">
                                <select class="form-control" wire:model.lazy="select_kamar">
                                    @foreach ($getRuang as $item)
                                        <option value="{{ $item->ruangan }}">{{ $item->ruangan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model.lazy="input_kamar"
                                    placeholder="G-XXX">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <select class="form-control" wire:model.lazy="input_kelas">
                                    <option value="Non Kelas">Non Kelas</option>
                                    <option value="Kelas 1">Kelas 1</option>
                                    <option value="Kelas 2">Kelas 2</option>
                                    <option value="Kelas 3">Kelas 3</option>
                                    <option value="Senior">Senior</option>
                                    <option value="Yunior">Yunior</option>
                                    <option value="Utama">Utama</option>
                                    <option value="Deluxe">Deluxe</option>
                                    <option value="VIP">VIP</option>
                                    <option value="VVIP">VVIP</option>
                                    <option value="Executive">Executive</option>
                                </select>
                            </div>
                        </div>
                        @foreach ($input_bed as $index => $time)
                            <input type="text" wire:model.lazy="input_bed.{{ $index }}"
                                value="{{ $index }}" hidden>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-default form-control"
                                    wire:click="deleteInput({{ $index }})"><b>Bed
                                        {{ \App\Services\BulanRomawi::angkaToAbjad($input_bed[$index]) }}</b>
                                    <i class="fa fa-trash"></i></button>
                            </div>
                        @endforeach
                        @php
                            $disabled = count($input_bed) > 5 ? 'disabled' : '';
                        @endphp
                        <div class="col-md-1">
                            <div class="d-flex justify-content-between">
                                <button wire:click="addInput" class="btn btn-default" {{ $disabled }}><i
                                        class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" wire:click='tambahBed'>Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
