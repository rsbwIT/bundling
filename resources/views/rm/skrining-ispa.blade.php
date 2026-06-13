@extends('..layout.layoutDashboard')
@section('title', 'Skrining ISPA')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Skrining ISPA - Rawat Inap</h5>
                    <p class="text-muted text-xs">Data pasien rawat inap dengan diagnosa utama ISPA (J06.9 / J18.9)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            {{-- FORM FILTER --}}
            <form action="{{ url('/skrining-ispa') }}" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-xs">Tanggal Awal</label>
                            <input type="date" name="tgl_awal" class="form-control form-control-xs"
                                value="{{ request('tgl_awal', $tgl_awal) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-xs">Tanggal Akhir</label>
                            <input type="date" name="tgl_akhir" class="form-control form-control-xs"
                                value="{{ request('tgl_akhir', $tgl_akhir) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-xs">Bangsal</label>
                            <input type="text" name="bangsal" class="form-control form-control-xs"
                                placeholder="Semua Bangsal" value="{{ request('bangsal') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-xs">Penjamin</label>
                            <input type="text" name="penjamin" class="form-control form-control-xs"
                                placeholder="Semua Penjamin" value="{{ request('penjamin') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-xs">Cari</label>
                            <input type="text" name="keyword" class="form-control form-control-xs"
                                placeholder="Nama / RM / Dokter" value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label class="text-xs">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- SUMMARY TABEL KELOMPOK UMUR --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <table class="table table-sm table-bordered text-xs text-center">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">Kelompok Umur</th>
                                <th colspan="3">J06.9 - ISPA Atas</th>
                                <th colspan="3">J18.9 - Pneumonia</th>
                                <th rowspan="2" class="align-middle">Total</th>
                                <th rowspan="2" class="align-middle">Meninggal</th>
                            </tr>
                            <tr>
                                <th>L</th>
                                <th>P</th>
                                <th>Sub</th>
                                <th>L</th>
                                <th>P</th>
                                <th>Sub</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($summaryUmur as $kat)
                                <tr>
                                    <td class="text-left">{{ $kat['nama'] }}</td>
                                    <td>{{ $kat['j069_l'] }}</td>
                                    <td>{{ $kat['j069_p'] }}</td>
                                    <td><strong>{{ $kat['j069_total'] }}</strong></td>
                                    <td>{{ $kat['j189_l'] }}</td>
                                    <td>{{ $kat['j189_p'] }}</td>
                                    <td><strong>{{ $kat['j189_total'] }}</strong></td>
                                    <td><strong>{{ $kat['total'] }}</strong></td>
                                    <td>{{ $kat['meninggal'] }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="text-left"><strong>Total</strong></td>
                                <td><strong>{{ collect($summaryUmur)->sum('j069_l') }}</strong></td>
                                <td><strong>{{ collect($summaryUmur)->sum('j069_p') }}</strong></td>
                                <td><strong>{{ $totalJ069 }}</strong></td>
                                <td><strong>{{ collect($summaryUmur)->sum('j189_l') }}</strong></td>
                                <td><strong>{{ collect($summaryUmur)->sum('j189_p') }}</strong></td>
                                <td><strong>{{ $totalJ189 }}</strong></td>
                                <td><strong>{{ $totalPasien }}</strong></td>
                                <td><strong>{{ $meninggalTotal }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TOMBOL COPY --}}
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <table class="table table-sm table-bordered table-striped table-responsive text-xs"
                style="white-space: nowrap;" id="tableToCopy">
                <tbody>
                    <tr>
                        <th>No</th>
                        <th>Tgl Registrasi</th>
                        <th>No Rawat</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>JK</th>
                        <th>Umur</th>
                        <th>Alamat Lengkap</th>
                        <th>Bangsal</th>
                        <th>Kamar</th>
                        <th>Tgl Masuk</th>
                        <th>Status Pulang</th>
                        <th>Dokter</th>
                        <th>Kode Diagnosa</th>
                        <th>Nama Diagnosa</th>
                    </tr>
                    @foreach ($results as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->tgl_registrasi }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->jk }}</td>
                            <td>{{ $item->umur }}</td>
                            <td>{{ $item->almt_pj }}</td>
                            <td>{{ $item->nm_bangsal }}</td>
                            <td>{{ $item->kd_kamar }}</td>
                            <td>{{ $item->tgl_masuk }}</td>
                            <td>{{ $item->stts_pulang }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td>{{ $item->kd_penyakit }}</td>
                            <td>{{ $item->nm_penyakit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById("copyButton").addEventListener("click", function() {
            copyTableToClipboard("tableToCopy");
        });

        function copyTableToClipboard(tableId) {
            const table = document.getElementById(tableId);
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand("copy");
                window.getSelection().removeAllRanges();
                alert("Tabel telah berhasil disalin ke clipboard.");
            } catch (err) {
                console.error("Tidak dapat menyalin tabel:", err);
            }
        }
    </script>
@endsection
@push('scripts')
    @livewireScripts
@endpush
