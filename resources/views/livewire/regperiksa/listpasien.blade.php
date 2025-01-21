<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getListPasien">
                <div class="row">
                    <div class="col-lg-3">
                        <input class="form-control form-control-sidebar form-control-sm" type="text" aria-label="Search"
                            placeholder="Cari Sep / Rm / No.Rawat" wire:model.defer="carinomor">

                    </div>
                    <div class="col-lg-2">
                        <select class="form-control form-control-sidebar form-control-sm" wire:model="status_lanjut">
                            <option value="Ralan">Rawat Jalan</option>
                            <option value="Ranap">Rawat Inap</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-control form-control-sidebar form-control-sm" wire:model="status_pulang"
                            @if ($status_lanjut == 'Ralan') disabled @endif>
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
                                        wire:loading wire:target="getListPasien"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 65vh; overflow: auto;">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>RM</th>
                        <th>No.Rawat</th>
                        <th>No.Sep</th>
                        <th>Pasien</th>
                        @if ($status_lanjut == 'Ranap')
                            <th>Tgl.Masuk</th>
                            <th>Tgl.Keluar</th>
                        @else
                            <th>Tgl.Registrasi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr wire:key='{{ $key }}'>
                            <td>
                                <button id="dropdownSubMenu1{{ $key }}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false"
                                    class="btn btn-default btn-sm dropdown-toggle dropdown dropdown-hover py-0"></button>
                                @include('regperiksa.component.menu')
                            </td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_sep }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            @if ($status_lanjut == 'Ranap')
                                <td>{{ $item->tgl_masuk }}</td>
                                <td>{{ $item->tgl_keluar }}</td>
                            @else
                                <td>{{ $item->tgl_registrasi }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- <div class="modal fade" id="BillingPasien" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            @if ($getBillingPasien)
                <div class="modal-content modal-lg">
                    <div class="modal-header">

                        <h6 class="modal-title">Billing Pasien :
                            <u>
                                {{ $getBillingPasien[0]->nm_pasien }}
                            </u>
                        </h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                {{ $getBillingPasien}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div> --}}
</div>
