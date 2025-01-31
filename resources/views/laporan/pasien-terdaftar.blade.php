@extends('..layout.layoutDashboard')
@section('title', 'Pasien Rajal Terdaftar')

@section('konten')
    <div class="card">
        <div class="card-header">
            <form action="{{ url('/pasien-terdaftar') }}">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <input type="text" name="cariNomor" class="form-control form-control-sm"
                            placeholder="Cari Nama/RM/No Rawat">
                        <div class="input-group-append">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tgl1" class="form-control form-control-sm"
                            value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                        <div class="input-group-append">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tgl2" class="form-control form-control-sm"
                            value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                        <div class="input-group-append">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-md btn-primary btn-sm">
                                <i class="fa fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-default btn-sm float-right" id="copyButton">
                            <i class="fas fa-copy"></i> Copy table
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 65vh; overflow: auto;">
            <table class="table table-sm table-bordered table-hover table-head-fixed p-3 text-xs"
                style="white-space: nowrap;" id="tableToCopy">
                <thead>
                    <tr>
                        <th>No Rawat</th>
                        <th>Tanggal Registrasi</th>
                        <th>Jam Registrasi</th>
                        <th>Kode Dokter</th>
                        <th>No Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Status Lanjut</th>
                        <th>Nama Poli</th>
                        <th class="text-center">Umum</th>
                        <th class="text-center">BPJS</th>
                        <th class="text-center">Asuransi</th>
                        <th class="text-center">Piutang Pasien</th>
                        <th>Belum Cetak Bil</th>
                        <th>Sudah Cetak Bil</th>
                        <th>Batal</th>
                        <th class="text-center">Opname</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $item)
                        <tr>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->tgl_registrasi }}</td>
                            <td>{{ $item->jam_reg }}</td>
                            <td>{{ $item->kd_dokter }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->status_lanjut }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td class="text-center">
                                @if (count($item->getPasienUmum) > 0)
                                    1
                                @endif
                            </td>
                            <td class="text-center">
                                @if (count($item->getPasienBpjs) > 0)
                                    1
                                @endif
                            </td>
                            <td class="text-center">
                                @if (count($item->getPasienAsuransi) > 0)
                                    1
                                @endif
                            </td>
                            <td class="text-center">
                                @if (count($item->getPiutangPasien) > 0)
                                    1
                                @endif
                            </td>
                            <td>
                                @if (count($item->getBilling) > 0)
                                @elseif (count($item->getPasienOpname) > 0)
                                @elseif (count($item->getPasienBatal) > 0)
                                @else
                                    1
                                @endif
                            </td>
                            <td>
                                @if (count($item->getBilling) > 0)
                                    1
                                @endif
                            </td>
                            <td>
                                @if (count($item->getPasienBatal) > 0)
                                    1
                                @endif
                            </td>
                            <td>
                                @if (count($item->getPasienOpname) > 0)
                                    1
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- <tfoot>
                    <th colspan="8">Total</th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getPasienUmum);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getPasienBpjs);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getPasienAsuransi);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getPiutangPasien);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getBilling);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getBilling);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getPasienBatal);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getPasienOpname);
                        }) }}
                    </th>
                </tfoot> --}}
            </table>
        </div>
        <div class="card-footer">
            Jumlah Data : {{ count($getPasien) }}
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
    </div>
@endsection
