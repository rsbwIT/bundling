<div>
    {{-- ===== INFO BOXES ===== --}}
    <div class="row">
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="nav-icon fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total List Pasien</b></span>
                    <span class="info-box-number"><h4>{{ $getPasien->count() }}</h4></span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total Sudah Bundling</b></span>
                    <span class="info-box-number">
                        <h4>
                            @php
                                $sudahBundling = $getPasien->filter(fn($i) => !is_null($i->file))->count();
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
                    <span class="info-box-text"><b>Total Belum Bundling</b></span>
                    <span class="info-box-number"><h4>{{ abs($sudahBundling - $getPasien->count()) }}</h4></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getListPasienRanap">
                <div class="row">
                    <div class="col-lg-3">
                        <input class="form-control form-control-sm" type="text"
                            placeholder="Cari SEP / RM / No.Rawat" wire:model.defer="carinomor">
                    </div>
                    <div class="col-lg-2">
                        <input type="date" class="form-control form-control-sm" wire:model.defer="tanggal1">
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group">
                            <input type="date" class="form-control form-control-sm" wire:model.defer="tanggal2">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" wire:click="render()">
                                    <i class="fas fa-search"></i>
                                    <span class="spinner-grow spinner-grow-sm" wire:loading wire:target="getListPasienRanap"></span>
                                    CARI
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 text-right">
                        @if (session()->has('successSaveINACBG'))
                            <span class="text-success"><i class="fas fa-check"></i> {{ session('successSaveINACBG') }}</span>
                        @endif
                        @if (session()->has('errorBundling'))
                            <span class="text-danger"><i class="fas fa-ban"></i> {{ session('errorBundling') }}</span>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0" style="height: 650px;">
            <style>
                .table td, .table th { vertical-align: middle !important; }
                .spill { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; font-size: 0.68rem; font-weight: 700; }
                .spill-ok { background: #dcfce7; color: #15803d; }
                .spill-no { background: #fee2e2; color: #b91c1c; }
                .spill-warn { background: #fef9c3; color: #854d0e; }
                .spill-na { background: #f1f5f9; color: #94a3b8; }
            </style>

            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-sm">
                <thead>
                    <tr class="text-center">
                        <th width="30%">Pilihan</th>
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
                                                wire:loading wire:target="UploadInacbg('{{ $key }}', '{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')"></span>
                                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                                wire:loading wire:target="UploadScan('{{ $key }}', '{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')"></span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                wire:click="SetmodalInacbg('{{ $key }}')" data-target="#UploadInacbg">
                                                <i class="fas fa-upload"></i> Berkas Inacbg
                                            </a>
                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                wire:click="SetmodalScan('{{ $key }}')" data-target="#UploadScan">
                                                <i class="fas fa-upload"></i> Berkas Scan
                                            </a>
                                        </div>
                                    </div>
                                    {{-- KHANZA --}}
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-block btn-outline-dark btn-xs btn-flat dropdown-toggle dropdown-icon"
                                            data-toggle="dropdown">
                                            Khanza
                                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                                wire:loading wire:target="SimpanKhanza('{{ $item->no_rawat }}', '{{ $item->no_sep }}')"></span>
                                        </button>
                                        <div class="dropdown-menu shadow-sm" role="menu">
                                            <button type="button" class="dropdown-item"
                                                wire:click="SimpanKhanza('{{ $item->no_rawat }}', '{{ $item->no_sep }}')">
                                                <i class="fas fa-save text-success mr-1"></i> Simpan Khanza
                                            </button>
                                            <form action="{{ url('carinorawat-casemix') }}" method="GET" target="_blank">
                                                <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                                <input name="cariNoSep" value="{{ $item->no_sep }}" hidden>
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-eye text-info mr-1"></i> Detail Khanza
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- GABUNG --}}
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-block btn-outline-success btn-xs btn-flat"
                                            wire:click="GabungBerkas('{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')">
                                            Gabung
                                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                                wire:loading wire:target="GabungBerkas('{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')"></span>
                                        </button>
                                    </div>
                                    {{-- DOWNLOAD --}}
                                    <div class="btn-group">
                                        @if ($item->file)
                                            <a href="{{ url('hasil_pdf/' . $item->file) }}" download
                                                class="btn btn-block btn-outline-success btn-xs btn-flat" role="button">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <a href="#" class="btn btn-block btn-outline-dark btn-xs btn-flat" role="button">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        @endif
                                    </div>
                                    {{-- RESUME · TRIASE · KIRIM --}}
                                    <div class="btn-group ml-1">
                                        <button type="button" class="btn btn-outline-success btn-xs btn-flat"
                                            title="Lihat Resume Medis" onclick="openResumeModal('{{ $item->no_rawat }}')">
                                            <i class="fas fa-file-medical"></i> Resume
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-xs btn-flat"
                                            title="Data Triase" onclick="openTriaseModal('{{ $item->no_rawat }}')">
                                            <i class="fas fa-heartbeat"></i> Triase
                                        </button>
                                        <a href="{{ route('bpjs.inacbg', ['norawat' => $item->no_rawat]) }}"
                                            target="_blank" class="btn btn-outline-danger btn-xs btn-flat"
                                            title="Kirim ke INACBG">
                                            <i class="fas fa-paper-plane"></i> Kirim
                                        </a>
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
                                <input type="checkbox" disabled {{ $item->sudah_resume ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" disabled {{ $item->sudah_triase ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" disabled {{ $item->sudah_pemeriksaan ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" disabled {{ $item->sudah_mati ? 'checked' : '' }}>
                            </td>
                        </tr>
                    @endforeach

                    <div class="modal fade" id="UploadInacbg" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog" role="document"><div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Upload Berkas <b>INACBG</b> : <u>{{ $nm_pasien }}</u></h6>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>File INACBG</label>
                                    <input type="file" class="form-control" wire:model="upload_file_inacbg.{{ $keyModal }}">
                                    @error('upload_file_inacbg.' . $keyModal)<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary"
                                    wire:click="UploadInacbg('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                    wire:loading.attr="disabled"
                                    @if (!isset($upload_file_inacbg[$keyModal])) disabled @endif>
                                    Submit <span wire:loading wire:target="UploadInacbg('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')">...</span>
                                </button>
                            </div>
                        </div></div>
                    </div>

                    <div class="modal fade" id="UploadScan" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog" role="document"><div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Upload Berkas <b>SCAN</b> : <u>{{ $nm_pasien }}</u></h6>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Jenis Berkas</label>
                                    <select class="form-control" wire:model="kode_berkas.{{ $keyModal }}">
                                        <option value="">-- Pilih Jenis Berkas --</option>
                                        @foreach(DB::table('master_berkas_digital')->orderBy('nama')->get() as $berkas)
                                            <option value="{{ $berkas->kode }}">{{ $berkas->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('kode_berkas.' . $keyModal)<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label>File Scan</label>
                                    <input type="file" class="form-control" wire:model="upload_file_scan.{{ $keyModal }}">
                                    @error('upload_file_scan.' . $keyModal)<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary"
                                    wire:click="UploadScan('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')"
                                    wire:loading.attr="disabled"
                                    @if (!isset($upload_file_scan[$keyModal]) || !isset($kode_berkas[$keyModal])) disabled @endif>
                                    Submit <span wire:loading wire:target="UploadScan('{{ $keyModal }}', '{{ $no_rawat }}', '{{ $no_rkm_medis }}')">...</span>
                                </button>
                            </div>
                        </div></div>
                    </div>

                    <div id="triase-modal-container"></div>
                    <div id="resume-modal-container"></div>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.addEventListener('close-modal', event => {
            let modal = $('#' + event.detail.modal);
            modal.modal('hide');
            modal.find('input[type="file"]').val('');
        });
        function openTriaseModal(norawat) {
            $.ajax({ url: '{{ route("inacbg.triaseModalHtml") }}?norawat=' + encodeURIComponent(norawat), type: 'GET',
                success: function(r) {
                    if (r.html) { $('#triase-modal-container').html(r.html); $('#modalTriase').modal('show'); }
                    else Swal.fire({ icon:'warning', title:'Belum Ada Data', text:'Data Triase belum dibuat.', confirmButtonText:'OK' });
                },
                error: function(xhr) {
                    Swal.fire({ icon: xhr.status===404?'warning':'error', title: xhr.status===404?'Belum Ada Data':'Error',
                        text: xhr.responseJSON?.error || xhr.responseJSON?.message || 'Terjadi kesalahan.', confirmButtonText:'OK' });
                }
            });
        }
        function openResumeModal(norawat) {
            $.ajax({ url: '{{ route("inacbg.resumeModalHtml") }}?norawat=' + encodeURIComponent(norawat), type: 'GET',
                success: function(r) {
                    if (r.html) { $('#resume-modal-container').html(r.html); $('#modalLihatResume').modal('show'); }
                    else Swal.fire({ icon:'warning', title:'Belum Ada Data', text:'Resume Medis belum dibuat.', confirmButtonText:'OK' });
                },
                error: function(xhr) {
                    Swal.fire({ icon: xhr.status===404?'warning':'error', title: xhr.status===404?'Belum Ada Data':'Error',
                        text: xhr.responseJSON?.error || xhr.responseJSON?.message || 'Terjadi kesalahan.', confirmButtonText:'OK' });
                }
            });
        }
    </script>
</div>