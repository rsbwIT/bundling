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
                        <select class="form-control form-control-sidebar form-control-sm" wire:model.defer="penjamin">
                            <option value="Semua">Semua Pasien</option>
                            <option value="Bpjs">Pasien BPJS</option>
                            <option value="NonBpjs">Pasien Non BPJS</option>
                        </select>
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
                        <th>Penjamin</th>
                        <th>Pasien</th>
                        <th>Poli</th>
                        <th class="text-center">Resume</th>
                        <th class="text-center">Diagnosa</th>
                        <th class="text-center">Act</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td class="text-center">
                                @if ($statusLanjut == 'Ralan')
                                    @if ($item->ralan_diagnosa_utama > 0)
                                        <i class="nav-icon fas fa-check"></i>
                                    @endif
                                @else
                                    @if ($item->ranap_diagnosa_utama > 0)
                                        <i class="nav-icon fas fa-check"></i>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                @if (count($item->getDiagnosa) > 0)
                                    <i class="nav-icon fas fa-check"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if (count($item->getCoding) > 0)
                                <button class="btn btn-xs btn-success">
                                    <i class="fas fa-check"></i>
                                </button>
                                @else
                                <button class="btn btn-xs btn-primary"
                                    wire:click="simpanPekerjaan('{{ $item->no_rawat }}', '{{ date('d-m-Y') }}')" >
                                    <i class="fas fa-clock" wire:loading.remove wire:target="simpanPekerjaan('{{ $item->no_rawat }}', '{{ date('d-m-Y') }}')"></i>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                        wire:loading wire:target="simpanPekerjaan('{{ $item->no_rawat }}', '{{ date('d-m-Y') }}')"></span>
                                </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            @php
                $text = $penjamin == 'Bpjs' ? 'Pasien BPJS' : 'Semua Pasien';
            @endphp
            <b>Jumlah {{ $text }} {{ $statusLanjut }} {{ date('d-m-Y', strtotime($tanggal1)) }} -
                {{ date('d-m-Y', strtotime($tanggal2)) }} = {{ count($getPasien) }}</b>
        </div>
    </div>
</div>
