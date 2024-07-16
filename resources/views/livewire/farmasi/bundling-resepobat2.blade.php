<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getListPasienRalan">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input class="form-control form-control-sidebar form-control-sm" type="text"
                                aria-label="Search" placeholder="Cari Sep / Rm / No.Rawat" wire:model.defer="carinomor">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group">
                            <select class="form-control form-control-sm" wire:model.defer="status_lanjut">
                                <option value="Ralan">Rawat Jalan</option>
                                <option value="Ranap">Rawat Inap</option>
                            </select>
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
                                        wire:loading wire:target="getListPasienRalan"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 text-right">
                        {{ $getPasien->count() }} Pasien {{$status_lanjut}}
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 450px;"">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr>
                        <th>Pasien</th>
                        <th>RM</th>
                        <th>No.Rawat</th>
                        <th>No.Sep</th>
                        <th>Poli</th>
                        <th class="text-center">Act</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_sep }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td width="120px">
                                <div class="d-flex justify-content-between">
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-block btn-outline-primary btn-xs btn-flat dropdown-toggle dropdown-icon"
                                            data-toggle="dropdown">
                                            Khanza <span class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true" wire:loading
                                                wire:target="SimpanResep('{{ $item->no_rawat }}', '{{ $item->no_sep }}')"></span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <button type="button" class="dropdown-item"
                                                wire:click="SimpanResep('{{ $item->no_rawat }}', '{{ $item->no_sep }}')">
                                                <i class="nav-icon fas fa-save"></i> Simpan Khanza
                                            </button>
                                            <form action="{{ url('/view-sep-resep2') }}" method=""
                                                class="">
                                                @csrf
                                                <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                                <input name="cariNoSep" value="{{ $item->no_sep }}" hidden>
                                                <button type="submit" class="dropdown-item"">
                                                    <i class="nav-icon fas fa-eye"></i> Detail Khanza
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        @if($item->fileFarmasi)
                                            <a href="{{ url('storage/resep_sep_farmasi/' . $item->fileFarmasi->file) }}" download
                                                class="btn btn-block btn-outline-success btn-xs btn-flat" role="button">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <a href="#"
                                                class="btn btn-block btn-outline-dark btn-xs btn-flat" role="button">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        @endif
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
