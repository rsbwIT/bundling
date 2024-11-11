<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getListPasienRanap">
                <div class="row">
                    <div class="col-lg-3">
                        <input class="form-control form-control-sidebar form-control-sm" type="text" aria-label="Search"
                            placeholder="Cari Sep / Rm / No.Rawat" wire:model.defer="carinomor">

                    </div>
                    <div class="col-lg-2">
                        <select class="form-control form-control-sidebar form-control-sm" wire:model="status_pulang">
                            <option value="blm_pulang">Belum Pulang</option>
                            <option value="tgl_masuk">Tanggal Masuk</option>
                            <option value="tgl_keluar">Pulang</option>
                        </select>
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
                                        wire:loading wire:target="getListPasienRanap"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr>
                        <th>RM</th>
                        <th>No.Rawat</th>
                        <th>Pasien</th>
                        <th>Tgl.Masuk</th>
                        <th>Tgl.Keluar</th>
                        <th>Tanggal Nota</th>
                        <th>Jam Nota</th>
                        <th>Kamar</th>
                        <th>Waktu Tunggu Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr wire:key='{{ $key }}'>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->tgl_masuk }}</td>
                            <td>{{ $item->tgl_keluar }}</td>
                            <td>
                                @foreach ($item->waktu_tunggu as $waktu)
                                    {{ $waktu->tanggal_nota }}
                                @endforeach
                            </td>
                            <td>
                                @foreach ($item->waktu_tunggu as $waktu)
                                    {{ $waktu->jam_nota }}
                                @endforeach
                            </td>
                            <td>{{ $item->nm_bangsal }} {{ $item->kd_kamar }}</td>
                            <td>
                                @foreach ($item->waktu_tunggu as $waktu)
                                    {{ $waktu->time_difference }}
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
