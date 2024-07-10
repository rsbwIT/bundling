<div>
    <div class="col-md-4 my-2">
        <form wire:submit.prevent='getPasien()'>
            <div class="input-group input-group-sm">
                <input class="form-control form-control-sm" placeholder="Silahkan Cari Nama/Rm"
                    wire:model.lazy="carinomor">
                <span class="input-group-append">
                    <button class="btn btn-sidebar btn-primary btn-sm">
                        <i class="fas fa-search fa-fw"></i>
                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" wire:loading
                            wire:target='getPasien'></span>
                    </button>
                </span>
            </div>
        </form>
    </div>
    <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
        <thead>
            <tr>
                <th>Nama Pasien/Peserta</th>
                <th>No Rekam Medis</th>
                <th>Nomor Kartu</th>
                <th>Nomor Klaim</th>
                <th>Jenis Kelamin</th>
                <th>Nama Ibu</th>
                <th>Tanggal Daftar</th>
                <th>No Telepon</th>
                <th>Act</th>
            </tr>
        </thead>
        <tbody>
            @if ($getPasien && $getPasien->isNotEmpty())
                @foreach ($getPasien as $keyInvoice => $pasien)
                    <tr>
                        <td>{{ $pasien->nm_pasien }}</td>
                        <td>{{ $pasien->no_rkm_medis }}</td>
                        <td>{{ $pasien->nomor_kartu }}</td>
                        <td>{{ $pasien->nomor_klaim }}</td>
                        <td>{{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td>{{ $pasien->nm_ibu }}</td>
                        <td>{{ $pasien->tgl_daftar }}</td>
                        <td>{{ $pasien->no_tlp }}</td>
                        <td>
                            <div class="badge-group">
                                <a data-toggle="modal" data-target="#ModalkeyInvoice{{ $keyInvoice }}"
                                    class="text-warning mx-2" href="#"><i class="fas fa-edit"></i></a>
                                @if (Session::has('sucsess' . $pasien->no_rkm_medis))
                                    <span class="text-success"><i class="fas fa-check"></i>
                                    </span>
                                @elseif (Session::has('gagal' . $pasien->no_rkm_medis))
                                    <span class="text-danger"><i class="fas fa-ban"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="modal fade" id="ModalkeyInvoice{{ $keyInvoice }}" tabindex="-1"
                                role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Nomor Kartu/Klaim : {{ $pasien->nm_pasien }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>nomor_kartu
                                                        </label>
                                                        <input type="text" class="form-control" required
                                                            placeholder="Enter ..."
                                                            wire:model.defer="getPasien.{{ $keyInvoice }}.nomor_kartu">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>nomor_klaim
                                                        </label>
                                                        <input type="text" class="form-control" required
                                                            placeholder="Enter ..."
                                                            wire:model.defer="getPasien.{{ $keyInvoice }}.nomor_klaim">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-primary"
                                                wire:click="updateInsertNomor('{{ $keyInvoice }}', '{{ $pasien->no_rkm_medis }}')"
                                                data-dismiss="modal">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">Silahkan cari data</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
