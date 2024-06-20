<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getListPasienRalan">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input class="form-control form-control-sidebar form-control-sm" type="text"
                                aria-label="Search" placeholder="Cari Nama / Rm / No.Rawat" wire:model.defer="carinomor">
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <select class="form-control form-control-sidebar form-control-sm"
                            wire:model.defer="statusLanjut">
                            <option value="Ralan">Rawat Jalan</option>
                            <option value="Ranap">Rawat Inap</option>
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
                                        wire:loading wire:target="getListPasienRalan"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 450px;"">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr>
                        <th>RM</th>
                        <th>No.Rawat</th>
                        <th>Pasien</th>
                        <th>Poli</th>
                        <th class="text-center">D</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td>
                                @if ($statusLanjut == 'Ralan')
                                    @if ($item->ralan_diagnosa_utama > 0)
                                        OK
                                    @endif
                                @else
                                    @if ($item->ranap_diagnosa_utama > 0)
                                        OK
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            Jumalh Pasien {{ $tanggal1 }} - {{ $tanggal1 }} = <b>{{ count($getPasien) }}</b>
        </div>
    </div>
</div>
