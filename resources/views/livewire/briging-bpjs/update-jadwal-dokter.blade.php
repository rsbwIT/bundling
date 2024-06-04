<div>
    <div class="card">
        <div class="card-header">
            <b>Cek Jadwal Dokter</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-lg-3">
                            <select class="form-control" wire:model.lazy="poliParam">
                                <option selected>Pilih Poli</option>
                                @foreach ($getPoli as $item)
                                    <option value="{{ $item->kd_poli_bpjs }}">
                                        {{ $item->kd_poli_bpjs . ' - ' . $item->nm_poli_bpjs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <input type="date" class="form-control form-control-sidebar"
                                    wire:model.defer="tanggal">
                                <div class="input-group-append">
                                    <button class="btn btn-sidebar btn-primary" wire:click="getJadwaldr">
                                        <i class="fas fa-search fa-fw"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-sm table-bordered table-hover text-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari</th>
                                <th>Nama Sub Spesialis</th>
                                <th>Nama Dokter</th>
                                <th>Kuota</th>
                                <th>Jam Praktek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($getJadwaldr == null)
                                <tr>
                                    <td colspan="6" class="text-center"><b>Jadwal Mungkin Tidak Tersedia
                                            !!!</b></td>
                                </tr>
                            @else
                                @foreach ($getJadwaldr as $key => $data)
                                    @if (is_object($data))
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data->namahari }}</td>
                                            <td>{{ $data->kodesubspesialis . ' - ' . $data->namapoli }}</td>
                                            <td>{{ $data->namadokter }}</td>
                                            <td>{{ $data->kapasitaspasien }}</td>
                                            <td>{{ $data->jadwal }}
                                                <button data-toggle="modal" style="background: none; border: none;"
                                                wire:click="getDataTable('{{$data->kodedokter}}', '{{$data->kodepoli}}')"><i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <b>Update Jadwal dokter</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            @if ($response)
                                @foreach ($response as $item)
                                    @if (is_object($item))
                                        @if ($item->code == 200)
                                            <div class="alert alert-success alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <i class="icon fas fa-check"></i> Terkirim !: {{ $item->message }}
                                            </div>
                                        @else
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <i class="icon fas fa-ban"></i> Gagal! : {{ $item->message }}
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control" wire:model.lazy="dokter">
                                    <option selected>Pilih Pokter</option>
                                    @foreach ($getDokter as $item)
                                        <option value="{{ $item->kd_dokter_bpjs }}">
                                            {{ $item->kd_dokter . ' - ' . $item->nm_dokter_bpjs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" wire:model.lazy="poli">
                                    <option selected>Pilih Poli</option>
                                    @foreach ($getPoli as $item)
                                        <option value="{{ $item->kd_poli_bpjs }}">
                                            {{ $item->kd_poli_bpjs . ' - ' . $item->nm_poli_bpjs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <select class="form-control" wire:model.lazy="hari">
                                    <option value="0">Pilih Hari Untuk Menambahkan Waktu</option>
                                    <option value="1">1 - Senin</option>
                                    <option value="2">2 - Selasa</option>
                                    <option value="3">3 - Rabu</option>
                                    <option value="4">4 - Kamis</option>
                                    <option value="5">5 - Jum'at</option>
                                    <option value="6">6 - Sabtu</option>
                                    <option value="7">7 - Minggu</option>
                                    <option value="8" class="text-danger">8 - Untuk Hari Libur Nasional</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                @php
                                    $hidden = $hari == '0' || $hari == null ? 'hidden' : '';
                                    $disabled = count($times) > 2 ? 'disabled' : '';
                                @endphp
                                <button wire:click="addInput" class="btn btn-primary" {{ $disabled }}
                                    {{ $hidden }}><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        @foreach ($times as $index => $time)
                            <div class="col-md-5">
                                <div class="form-group">
                                    <input type="time" class="form-control"
                                        wire:model.lazy="times.{{ $index }}.buka">
                                    <span class="text-sm text-secondary">{{ $times[$index]['buka'] }} WIB</span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <input type="time" class="form-control"
                                        wire:model.lazy="times.{{ $index }}.tutup">
                                    <span class="text-sm text-secondary">{{ $times[$index]['tutup'] }} WIB</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger"
                                    wire:click="deleteInput({{ $index }})"><i class="fa fa-trash"></i></button>
                            </div>
                        @endforeach
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" wire:click='UpdateJadwal'>Ubah
                                Jadwal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
