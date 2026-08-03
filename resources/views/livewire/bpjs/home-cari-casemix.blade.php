<div>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-3">
                    <div class="input-group input-group-xs">
                        <input type="search" wire:model.lazy="cariNorawat" class="form-control form-control-xs"
                            placeholder="Cari RM, No.Rawat, No.SEP">
                        <div class="input-group-append">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="input-group input-group-xs">
                        <div class="input-group-append">
                            <button wire:click="getPasien()" class="btn btn-md btn-primary">
                                <span>
                                    <span wire:loading.remove>
                                        <i class="fa fa-search"></i>
                                    </span>
                                    <span wire:loading>
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
        <!-- /.card-header -->
        <div class="card-body">
            @if (Session::has('errorBundling'))
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-ban"></i> {{ Session::get('errorBundling') }}!
                </div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">RM</th>
                        <th>Nama</th>
                        <th>No_Rawat</th>
                        <th>No_SEP</th>
                        <th>Sts Lanjut</th>
                        <th>Tanggl</th>
                        <th style="width: 40px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($getPasien == null)
                        <tr>
                            <td colspan="7" class="text-center">Data tidak tersedia</td>
                        </tr>
                    @else
                        @foreach ($getPasien as $item)
                            <tr>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_sep }}</td>
                                <td>{{ $item->jnspelayanan == '1' ? 'Rawat Inap' : ($item->jnspelayanan == '2' ? 'Rawat Jalan' : '-') }}</td>
                                <td>{{ $item->tgl_registrasi }}</td>
                                <td>
                                    <div class="btn-group">
                                        <div class="btn-group">
                                            <button type="button" class="badge bg-primary border-0 dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                Upload
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalUploadInacbg-{{ str_replace('/', '', $item->no_rawat) }}">
                                                    <i class="fas fa-upload"></i> Berkas Inacbg
                                                </a>
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalUploadScan-{{ str_replace('/', '', $item->no_rawat) }}">
                                                    <i class="fas fa-upload"></i> Berkas Scan
                                                </a>
                                            </div>
                                        </div>
                                        <form action="{{ url('carinorawat-casemix') }}" method="" class="">
                                            @csrf
                                            <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                            <input name="cariNoSep" value="{{ $item->no_sep }}" hidden>
                                            <button class="badge bg-primary mx-2">
                                                <i class="fas fa-save"> Khanza</i>
                                            </button>
                                        </form>

                                        <form action="{{ url('gabung-berkas-casemix') }}" method="">
                                            @csrf
                                            <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                            <input name="no_rkm_medis" value="{{ $item->no_rkm_medis }}" hidden>
                                            <input name="nm_pasien" value="{{ $item->nm_pasien }}" hidden>
                                            <input name="tgl1" value="{{ session('tgl1') }}" hidden>
                                            <input name="tgl2" value="{{ session('tgl2') }}" hidden>
                                            {{-- <input name="page" value="{{ session('page') }}" hidden> --}}
                                            <input name="statusLanjut" value="{{ session('statusLanjut') }}" hidden>
                                            <button class="badge bg-primary">
                                                <i class="fas fa-save"> Gabung</i>
                                            </button>
                                        </form>
                                        {{-- @if ($downloadFile)
                                                <a href="{{ url('hasil_pdf/' . $downloadFile->file) }}" download
                                                    class="ml-2 text-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif --}}
                                    </div>

                                    <!-- The Modal Upload INACBG -->
                                    <div class="modal fade" id="modalUploadInacbg-{{ str_replace('/', '', $item->no_rawat) }}" tabindex="-1" role="dialog" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">Upload Berkas <b>INACBG</b> : <u>{{ $item->nm_pasien }}</u></h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ url('/upload-berkas') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="no_rkm_medis" value="{{ $item->no_rkm_medis }}">
                                                    <input type="hidden" name="no_rawat" value="{{ $item->no_rawat }}">
                                                    <input type="hidden" name="no_sep" value="{{ $item->no_sep }}">
                                                    <input type="hidden" name="nama_pasein" value="{{ $item->nm_pasien }}">
                                                    
                                                    <div class="modal-body text-left">
                                                        <div class="form-group">
                                                            <label style="font-weight: bold;">File INACBG</label>
                                                            <input type="file" class="form-control" name="file_inacbg" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal INACBG -->

                                    <!-- The Modal Upload SCAN -->
                                    <div class="modal fade" id="modalUploadScan-{{ str_replace('/', '', $item->no_rawat) }}" tabindex="-1" role="dialog" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">Upload Berkas <b>SCAN</b> : <u>{{ $item->nm_pasien }}</u></h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ url('/upload-berkas') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="no_rkm_medis" value="{{ $item->no_rkm_medis }}">
                                                    <input type="hidden" name="no_rawat" value="{{ $item->no_rawat }}">
                                                    <input type="hidden" name="no_sep" value="{{ $item->no_sep }}">
                                                    <input type="hidden" name="nama_pasein" value="{{ $item->nm_pasien }}">
                                                    
                                                    <div class="modal-body text-left">
                                                        <div class="form-group">
                                                            <label style="font-weight: bold;">Jenis Berkas</label>
                                                            <select class="form-control" name="kode_berkas" required>
                                                                <option value="">-- Pilih Jenis Berkas --</option>
                                                                @foreach(DB::table('master_berkas_digital')->orderBy('nama')->get() as $berkas)
                                                                    <option value="{{ $berkas->kode }}">{{ $berkas->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <label style="font-weight: bold;">File Scan</label>
                                                            <input type="file" class="form-control" name="file_scan" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal -->
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
