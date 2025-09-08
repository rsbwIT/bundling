<div>
    <div class="row">
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="nav-icon fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total List Pasien</b></span>
                    <span class="info-box-number">
                        <h4>{{ $getPasien->count() }}</h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total Yang Sudah Terbundling</b></span>
                    <span class="info-box-number">
                        <h4>
                            @php
                                $sudahBundling = $getPasien
                                    ->filter(function ($item) {
                                        return !is_null($item->file);
                                    })
                                    ->count();
                            @endphp
                            {{ $sudahBundling }}
                        </h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-pen-nib"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total Yang Belum Terbundling</b></span>
                    <span class="info-box-number">
                        <h4>
                            {{ abs($sudahBundling - $getPasien->count()) }}
                        </h4>
                    </span>
                </div>
            </div>
        </div>
    </div>
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
                    <div class="col-lg-5 text-right">
                        @if (session()->has('successSaveINACBG'))
                            <span class="text-success"><i class="icon fas fa-check"> </i>
                                {{ session('successSaveINACBG') }} </span>
                        @endif
                        @if (session()->has('errorBundling'))
                            <span class="text-danger"><i class="icon fas fa-ban"> </i> {{ session('errorBundling') }}
                            </span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 450px;">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr class="text-center">
                        <th width="25%">Pilihan</th>
                        <th>RM</th>
                        <th>No.Rawat</th>
                        <th>No.Sep</th>
                        <th>Pasien</th>
                        <th>Poli</th>
                        <th>Tgl.Sep</th>
                        <th>Resume</th>
                        <th>Triase</th>
                        <th>S.O.A.P</th>
                        <th>Meninggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr>
                            <td>
                                <div class="d-flex justify-content-between">
                                    {{-- UPLOAD BERKAS --}}
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-block btn-outline-primary btn-xs btn-flat dropdown-toggle dropdown-icon"
                                            data-toggle="dropdown">
                                            Upload
                                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                                wire:loading
                                                wire:target="UploadInacbg('{{ $key }}', '{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')">
                                            </span>
                                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                                wire:loading
                                                wire:target="UploadScan('{{ $key }}', '{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')">
                                            </span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                wire:click="SetmodalInacbg('{{ $key }}')"
                                                data-target="#UploadInacbg">
                                                <i class="fas fa-upload"></i> Berkas Inacbg
                                            </a>
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                wire:click="SetmodalScan('{{ $key }}')"
                                                data-target="#UploadScan">
                                                <i class="fas fa-upload"></i> Berkas Scan
                                            </a>
                                        </div>
                                    </div>
                                    {{-- SIMPAN KHANZA  --}}
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-block btn-outline-dark btn-xs btn-flat dropdown-toggle dropdown-icon"
                                            data-toggle="dropdown">
                                            Khanza <span class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true" wire:loading
                                                wire:target="SimpanKhanza('{{ $item->no_rawat }}', '{{ $item->no_sep }}')"></span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <button type="button" class="dropdown-item"
                                                wire:click="SimpanKhanza('{{ $item->no_rawat }}', '{{ $item->no_sep }}')">
                                                <i class="nav-icon fas fa-save"></i> Simpan Khanza
                                            </button>
                                            {{-- <form action="{{ url('carinorawat-casemix') }}" method=""
                                                class="">
                                                @csrf
                                                <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                                <input name="cariNoSep" value="{{ $item->no_sep }}" hidden>
                                                <button type="submit" class="dropdown-item">
                                                    <i class="nav-icon fas fa-eye"></i> Detail Khanza
                                                </button>
                                            </form> --}}
                                            <form action="{{ url('carinorawat-casemix') }}" method="GET"
                                                target="_blank" class="">
                                                <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                                <input name="cariNoSep" value="{{ $item->no_sep }}" hidden>
                                                <button type="submit" class="dropdown-item">
                                                    <i class="nav-icon fas fa-eye"></i> Detail Khanza
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- GABUNG BERKAS --}}
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-block btn-outline-success btn-xs btn-flat"
                                            wire:click="GabungBerkas('{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')">
                                            Gabung <span class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true" wire:loading
                                                wire:target="GabungBerkas('{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')"></span>
                                        </button>
                                    </div>
                                    <div class="btn-group">
                                        @if ($item->file)
                                            <a href="{{ url('hasil_pdf/' . $item->file) }}" download
                                                class="btn btn-block btn-outline-success btn-xs btn-flat"
                                                role="button">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <a href="#" class="btn btn-block btn-outline-dark btn-xs btn-flat"
                                                role="button">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_sep }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td>{{ $item->tglsep }}</td>
                            <td class="text-center">
                                <input type="checkbox" disabled {{ $item->sudah_resume ? 'checked' : '' }}
                                    class="w-4 h-4 accent-green-500 cursor-not-allowed">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" disabled {{ $item->sudah_triase ? 'checked' : '' }}
                                    class="w-4 h-4 accent-blue-500 cursor-not-allowed">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" disabled {{ $item->sudah_pemeriksaan ? 'checked' : '' }}
                                    class="w-4 h-4 accent-yellow-500 cursor-not-allowed">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" disabled {{ $item->sudah_mati ? 'checked' : '' }}
                                    class="w-4 h-4 accent-red-500 cursor-not-allowed">
                            </td>
                        </tr>
                    @endforeach
                    {{-- MODAL --}}
                    {{-- <div class="modal fade" id="UploadInacbg" tabindex="-1" role="dialog" aria-hidden="true"
                        wire:ignore.self>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Upload Berkas <b>INACBG</b> :
                                        <u>{{ $nm_pasien }}</u>
                                    </h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>File Inacbg
                                                </label>
                                                <input type="file" class="form-control form-control"
                                                    wire:model="upload_file_inacbg.{{ $keyModal }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="submit" class="btn btn-primary" data-dismiss="modal"
                                        wire:click="UploadInacbg('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                        wire:loading.remove
                                        wire:target="upload_file_inacbg.{{ $keyModal }}">Submit
                                    </button>
                                    <div wire:loading wire:target="upload_file_inacbg.{{ $keyModal }}">
                                        Uploading...</div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <div class="modal fade" id="UploadInacbg" tabindex="-1" role="dialog" aria-hidden="true"
                        wire:ignore.self>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Upload Berkas <b>INACBG</b> : <u>{{ $nm_pasien }}</u>
                                    </h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>File Inacbg</label>
                                        <input type="file" class="form-control"
                                            wire:model="upload_file_inacbg.{{ $keyModal }}">
                                        @error('upload_file_inacbg.' . $keyModal)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-primary"
                                        wire:click="UploadInacbg('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="UploadInacbg('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                        @if (!isset($upload_file_inacbg[$keyModal])) disabled @endif>
                                        Submit
                                        <span wire:loading
                                            wire:target="UploadInacbg('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')">
                                            Uploading...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @push('scripts')
                        <script>
                            window.addEventListener('close-modal', event => {
                                let modal = $('#' + event.detail.modal);
                                modal.modal('hide');
                                modal.find('input[type="file"]').val(''); // reset input file
                            });
                        </script>
                    @endpush


                    {{-- <div class="modal fade" id="UploadScan" tabindex="-1" role="dialog" aria-hidden="true"
                        wire:ignore.self>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Upload Berkas <b>SCAN</b> :
                                        <u>{{ $nm_pasien }}</u>
                                    </h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>File Inacbg
                                                </label>
                                                <input type="file" class="form-control form-control"
                                                    wire:model="upload_file_scan.{{ $keyModal }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="submit" class="btn btn-primary" data-dismiss="modal"
                                        wire:click="UploadScan('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                        wire:loading.remove wire:target="upload_file_scan.{{ $keyModal }}">Submit
                                    </button>
                                    <div wire:loading wire:target="upload_file_scan.{{ $keyModal }}">
                                        Uploading...</div>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <div class="modal fade" id="UploadScan" tabindex="-1" role="dialog" aria-hidden="true"
                        wire:ignore.self>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Upload Berkas <b>SCAN</b> : <u>{{ $nm_pasien }}</u>
                                    </h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>File Scan</label>
                                        <input type="file" class="form-control"
                                            wire:model="upload_file_scan.{{ $keyModal }}">
                                        @error('upload_file_scan.' . $keyModal)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-primary"
                                        wire:click="UploadScan('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="UploadScan('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                        @if (!isset($upload_file_scan[$keyModal])) disabled @endif>
                                        Submit
                                        <span wire:loading
                                            wire:target="UploadScan('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')">
                                            Uploading...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @push('scripts')
                        <script>
                            window.addEventListener('close-modal', event => {
                                let modal = $('#' + event.detail.modal);
                                modal.modal('hide');
                                modal.find('input[type="file"]').val(''); // reset input file
                            });
                        </script>
                    @endpush



                    {{-- // MODAL --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
