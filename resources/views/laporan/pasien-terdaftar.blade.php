@extends('..layout.layoutDashboard')
@section('title', 'Laporan pasien')

@section('konten')
    <div class="card">
        <div class="card-body">
            Jumlah Data : {{ count($getPasien) }}
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <table class="table table-sm table-hover table-bordered table-responsive text-xs mb-3"
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
                <tfoot>
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
                            return count($item->getPasienBatal);
                        }) }}
                    </th>
                    <th>
                        {{ $getPasien->sum(function ($item) {
                            return count($item->getPasienOpname);
                        }) }}
                    </th>
                </tfoot>
            </table>
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
