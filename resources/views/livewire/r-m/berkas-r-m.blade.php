<div>
    <section class="content ">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <div class="input-group input-group-xs">
                        <input type="text" class="form-control" placeholder="Cari Nama, No.RM / No.Rawat"
                            wire:model.defer="cari_nomor">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <div class="input-group input-group-xs">
                        <select class="form-control" wire:model.defer="status_lanjut">
                            <option value="">Semua Status Lanjut</option>
                            <option value="Ranap">Rawat Inap</option>
                            <option value="Ralan">Rawat Jalan</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <div class="input-group input-group-xs">
                        <select class="form-control" wire:model.defer="jenis_berkas">
                            <option disabled value="">Pilih Berkas</option>
                            <option value="SCAN">Berkas Scan</option>
                            {{-- <option disabled value="INACBG">Berkas Inacbg</option>
                            <option disabled value="RESUMEDLL">Berkas DB Khanza</option>
                            <option disabled value="HASIL">Berkas Gabungan</option> --}}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <div class="input-group input-group-xs">
                        <input type="date" class="form-control" value="{{ now()->format('Y-m-d') }}"
                            wire:model.defer="tgl1">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <div class="input-group input-group-xs">
                        <input type="date" class="form-control" value="{{ now()->format('Y-m-d') }}"
                            wire:model.defer="tgl2">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <div class="input-group input-group-xs">
                        <div class="input-group-append">
                            <button type="submit" wire:click="render" class="btn btn-md btn-primary">
                                <span>
                                    <span wire:loading.remove wire:target='render'>
                                        <i class="fa fa-search"></i>
                                    </span>
                                    <span wire:loading wire:target='render'>
                                        <span class="spinner-grow spinner-grow-sm" role="status"
                                            aria-hidden="true"></span> Mencari...
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="card-body table-responsive p-0" style="height: 500px;">
        <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
            <thead>
                <tr>
                    <th>No. Rkm Medis</th>
                    <th>No. Rawat</th>
                    <th>Nama Pasien
                        {{ $status_lanjut == 'Ralan' ? 'Rawat Jalan' : ($status_lanjut == 'Ranap' ? 'Rawat Inap' : '') }}
                    </th>
                    <th class="text-center">Jenis Berkas</th>
                    <th class="text-center">Act</th>
                </tr>
            </thead>
            <tbody>
                @if ($getBerkasPasien->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">Data Tidak Ada / Silahkan Cari Data</td>
                    </tr>
                @else
                    @foreach ($getBerkasPasien as $key => $item)
                        <tr>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td class="text-center">{{ $item->nama_file }}</td>
                            <td class="text-center">
                                <a href="{{ url($item->folder . $item->file) }}" download class="text-success">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <span class="text-bold mt-5">jumlah data : {{count($getBerkasPasien)}}</span>
</div>
