@extends('..layout.layoutDashboard')
@section('title', 'Claim Bpjs')

@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card  card-primary">
                <div class="card-header">
                    <h5 class="card-title">Claim Bpjs</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <section class="content">
                        {{-- <form action="{{ url('/cariNorawat-ClaimBpjs') }}" action="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group input-group-lg">
                                            <input type="search" name="cariNorawat" class="form-control form-control-lg"
                                                placeholder="Cari berdasarkan nomor rawat">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-lg btn-default">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form> --}}
                        <form action="{{ url('/upload-berkas') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mt-1">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="no_rkm_medis">No Rekam Medis</label>
                                        <input type="text" class="form-control" name="no_rkm_medis"
                                            value="@isset($getPasien) {{ $getPasien->no_rkm_medis }} @endisset"
                                            placeholder="No Rekam Medis">
                                    </div>
                                    <div class="form-group">
                                        <label for="no_rawat">No Rawat</label>
                                        <input type="text" class="form-control" name="no_rawat"
                                            value="@isset($getPasien){{ $getPasien->no_rawat }} @endisset"
                                            placeholder="No Rawat">
                                    </div>
                                    <div class="form-group">
                                        <label for="no_sep">No SEP</label>
                                        <input type="text" class="form-control" name="no_sep"
                                            value="@isset($getPasien){{ $getPasien->no_sep }} @endisset"
                                            placeholder="No Rawat">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="nm_pasien">Nama Pasien</label>
                                        <input type="text" class="form-control" name="nama_pasein"
                                            value="@isset($getPasien) {{ $getPasien->nm_pasien }} @endisset"
                                            placeholder="Nama Pasien">
                                    </div>
                                    <div class="form-group">
                                        <label for="berkas_claim">Berkas INACBG</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file_inacbg">
                                            <label class="custom-file-label" for="berkas_claim">Berkas INACBG</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Jenis Berkas SCAN</label>
                                        <select class="form-control" name="kode_berkas">
                                            <option value="">-- Pilih Jenis Berkas --</option>
                                            @foreach(DB::table('master_berkas_digital')->orderBy('nama')->get() as $berkas)
                                                <option value="{{ $berkas->kode }}">{{ $berkas->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="berkas_claim">Berkas SCAN</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file_scan">
                                            <label class="custom-file-label" for="berkas_claim">Berkas SCAN</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </section>
                </div>
                <div class="card-footer">
                    <div class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
