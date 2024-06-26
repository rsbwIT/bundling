<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Log Book Keperawatan RSBW
                {{ session()->has('auth') ? session('auth')['id_user'] : '' }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <form wire:submit.prevent="cariPasien">
                        <div class="input-group">
                            <input class="form-control form-control-sidebar" type="text" placeholder="Cari No.RM"
                                wire:model.lazy="cari_nama_rm" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-sidebar btn-primary">
                                    <i class="fas fa-search fa-fw" wire:loading.remove wire:target='cariPasien'></i>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                        wire:loading wire:target='cariPasien'></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr />
            <div class="row mt-3">
                <div class="container-fluid">
                    @if (!$getPasien->isEmpty())
                        @foreach ($getPasien as $key => $item)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card card-primary ">
                                        <div class="card-body box-profile">
                                            <div class="text-center">
                                                <img class="img-circle" height="80px" width="80px"
                                                    src="/img/user.jpg" alt="User profile picture">
                                            </div>
                                            <h3 class="profile-username text-center">{{ $item->nm_pasien }}</h3>
                                            <p class="text-muted text-center">{{ $item->no_rkm_medis }}</p>
                                            <ul class="list-group list-group-unbordered">
                                                <li class="list-group-item">
                                                    <b>Tanggal Lahir</b> <span
                                                        class="float-right">{{ $item->tgl_lahir }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Nomor Telpon</b> <span
                                                        class="float-right">{{ $item->no_tlp }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Jenis Kelamin</b> <span
                                                        class="float-right">{{ $item->jk }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <form wire:submit.prevent="cariNamaKegiatan">
                                                        <div class="input-group">
                                                            <input
                                                                class="form-control form-control-sidebar form-control-sm"
                                                                type="text" wire:model.lazy="cari_kode_kegiatan"
                                                                aria-label="Search"
                                                                placeholder="Cari Kode Kegiatan / Nama Kegiatan">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-sidebar btn-default btn-sm">
                                                                    <i class="fas fa-search fa-fw" wire:loading.remove
                                                                        wire:target='cariNamaKegiatan'></i>
                                                                    <span class="spinner-grow spinner-grow-sm"
                                                                        role="status" aria-hidden="true" wire:loading
                                                                        wire:target='cariNamaKegiatan'></span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="input-group">
                                                        <select class="form-control form-control-sm"
                                                            wire:model="kodejnslb">
                                                            <option>Pilih Jenis Log Book</option>
                                                            @foreach ($getLookBook as $lokbok)
                                                                <option value="{{ $lokbok->kd_jesni_lb }}">
                                                                    {{ $lokbok->nama_jenis_lb }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                </div>
                                                {{-- tanggal --}}
                                                <div class="col-lg-2 order-lg-last">
                                                    <div class="card-tools">
                                                        <input type="date"
                                                            class="form-control form-control-sidebar form-control-sm"
                                                            wire:model="tanggal" value="{{ now()->format('Y-m-d') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body table-responsive p-0" style="height: 290px;">
                                            @if (!$getKegiatan->isEmpty())
                                                <table class="table table-sm table-bordered table-head-fixed p-3">
                                                    <thead>
                                                        <tr>
                                                            <th>Kd.Kegiatan</th>
                                                            <th width="50%">Nama Kegiatan</th>
                                                            <th class="text-center">Mandiri</th>
                                                            <th class="text-center">Dibawah Supervisi</th>
                                                            <th class="text-center">Act</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($getKegiatan as $key => $data)
                                                            <tr>
                                                                <td>{{ $data->kd_kegiatan }}</td>
                                                                <td class="text-sm">{{ $data->nama_kegiatan }}</td>
                                                                <td class="text-center">
                                                                    <input type="checkbox"
                                                                        wire:model.defer ="mandiri.{{ $key }}">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox"
                                                                        wire:model.defer ="dibawahsupervisi.{{ $key }}">
                                                                </td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $user = session()->has('auth')
                                                                            ? session('auth')['id_user']
                                                                            : '';
                                                                    @endphp
                                                                    @if (Session::has('sucsess' . $key))
                                                                        <span class="text-success"><i
                                                                                class="fas fa-check"></i>
                                                                        </span>
                                                                    @else
                                                                        <button class="btn btn-xs btn-primary"
                                                                            wire:click="simpanKegiatan('{{ $key }}', '{{ $data->kd_kegiatan }}', '{{ $user }}', '{{ $item->no_rkm_medis }}')">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <h6 class="text-center mt-3">Silahkan Cari Data Pada Jenis Log Book !!!
                                                </h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <form wire:submit.prevent="cariKewenanganKhusus">
                                                        <div class="input-group">
                                                            <input
                                                                class="form-control form-control-sidebar form-control-sm"
                                                                type="text"
                                                                wire:model.lazy="cari_kode_kewenagankhusus"
                                                                aria-label="Search"
                                                                placeholder="Cari Kode / Nama Kewenangan Khusus">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-sidebar btn-default btn-sm">
                                                                    <i class="fas fa-search fa-fw" wire:loading.remove
                                                                        wire:target='cariKewenanganKhusus'></i>
                                                                    <span class="spinner-grow spinner-grow-sm"
                                                                        role="status" aria-hidden="true" wire:loading
                                                                        wire:target='cariKewenanganKhusus'></span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="col-lg-6">
                                                </div>
                                                {{-- tanggal --}}
                                            </div>
                                        </div>
                                        <div class="card-body table-responsive p-0" style="height: 290px;">
                                            @if (!$getKegiatan->isEmpty())
                                                <table class="table table-sm table-bordered table-head-fixed p-3">
                                                    <thead>
                                                        <tr>
                                                            <th>Kode</th>
                                                            <th width="50%">Kewenangan Khusus</th>
                                                            <th class="text-center">Mandiri</th>
                                                            <th class="text-center">Dibawah Supervisi</th>
                                                            <th class="text-center">Act</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($getKewenanganKhusus as $key => $data)
                                                            <tr>
                                                                <td>{{ $data->kd_kewenangan }}</td>
                                                                <td class="text-sm">{{ $data->nama_kewenangan }}</td>
                                                                <td class="text-center">
                                                                    <input type="checkbox"
                                                                        wire:model.defer ="kw_mandiri.{{ $key }}">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox"
                                                                        wire:model.defer ="kw_dibawahsupervisi.{{ $key }}">
                                                                </td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $user = session()->has('auth')
                                                                            ? session('auth')['id_user']
                                                                            : '';
                                                                    @endphp
                                                                    @if (Session::has('sucsess2' . $key))
                                                                        <span class="text-success"><i
                                                                                class="fas fa-check"></i>
                                                                        </span>
                                                                    @else
                                                                        <button class="btn btn-xs btn-primary"
                                                                            wire:click="simpanKewenangan('{{ $key }}', '{{ $data->kd_kewenangan }}', '{{ $user }}', '{{ $item->no_rkm_medis }}')">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <h6 class="text-center mt-3">Silahkan Cari Data Pada Jenis Log Book !!!
                                                </h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="card-body table-responsive p-0" style="height: 350px;"">
                            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="3%">No.</th>
                                        <th>Kegiatan/Tindakan Yang Sudah Dilakukan Terhadap Pasien Pada
                                            {{ $tanggal }}</th>
                                        <th width="10%" class="text-center">Mandiri</th>
                                        <th width="10%" class="text-center">Supervisi</th>
                                        <th width="10%" class="text-center">Tanggal</th>
                                        <th>Act</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($getLogbookPasien as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->nama_kegiatan }}</td>
                                            <td class="text-center">
                                                @if ($item->mandiri == 1)
                                                    <i class="fas fa-check"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($item->supervisi == 1)
                                                    <i class="fas fa-check"></i>
                                                @endif
                                            </td>
                                            <td>{{ $item->tanggal }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger"
                                                    wire:click="HapusKegiatan('{{ $item->id_logbook }}')"
                                                    data-dismiss="modal">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach ($getLogbookPasienKHusus as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->nama_kewenangan }}</td>
                                            <td class="text-center">
                                                @if ($item->mandiri == 1)
                                                    <i class="fas fa-check"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($item->supervisi == 1)
                                                    <i class="fas fa-check"></i>
                                                @endif
                                            </td>
                                            <td>{{ $item->tanggal }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger"
                                                    wire:click="HapusKegiatanKhusus('{{ $item->id_kewenangankhusus}}')"
                                                    data-dismiss="modal">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
