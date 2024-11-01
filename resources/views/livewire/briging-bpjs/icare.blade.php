<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getPasien">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input class="form-control form-control-sidebar form-control-sm" type="text"
                                aria-label="Search" placeholder="Cari Nama / Rm / No.Rawat" wire:model.defer="carinomor">
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
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                        wire:loading wire:target="getPasien"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 text-right">
                        @if (isset($getriwayat['metaData']) && $getriwayat['metaData']['code'] != 200)
                            Kode: {{ $getriwayat['metaData']['code'] }} Pesan: {{ $getriwayat['metaData']['message'] }}
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 450px;"">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr>
                        <th>No Rawat</th>
                        <th>Nama Pasien</th>
                        <th>Nama Dokter</th>
                        <th>Jadwal Dokter</th>
                        <th>Poli</th>
                        <th>Np Sep</th>
                        <th>Act</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td class="text-center">
                                @foreach ($item->jadwal_dokter as $jadwal)
                                <p class="m-0">{{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</p>
                                @endforeach
                            </td>
                            <td>{{ $item->nm_poli }}</td>
                            <td>{{ $item->no_sep }}</td>
                            <td width="100px">
                                @php
                                    $color = $item->status == 1 ? 'text-success' : 'text-default';
                                    $icon = $item->status == 1 ? 'fa-check' : 'fa-pen';
                                @endphp
                                <button class="btn btn-default {{$color}} btn-sm"
                                    style="background: none; border: none; padding: 0px; margin: 0px;"
                                    wire:click="riwayatIcare('{{ $item->no_ktp }}','{{ $item->kd_dokter_bpjs }}' ,'{{ $key }}')">
                                    <i class="fas {{$icon}}" data-toggle="tooltip" data-placement="top"
                                        title="Lihat riwayat"></i>
                                </button>
                                @if (Session::has('sucsessGetUrl' . $key))
                                    <a target="_blank" href="{{ Session::get('sucsessGetUrl' . $key) }}"
                                        class="btn btn-default btn-sm float-right"
                                        style="background: none; border: none; padding: 0px; margin: 0px;" wire:click="sudahDibuka('{{$item->no_rawat}}', '{{$item->kd_dokter_bpjs}}')">
                                        <i class="fas fa-eye "></i>
                                    </a>
                                @endif
                                @if (Session::has('failedGetUrl' . $key))
                                    <button class="btn btn-default btn-sm float-right"
                                        style="background: none; border: none; padding: 0px; margin: 0px;">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
