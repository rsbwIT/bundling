@extends('..layout.layoutDashboard')
@section('title', 'Periksa Radiologi')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan.component.cari-dokter-paramedis')
            Jumlah Data : {{ count($getPeriksaRadiologi) }}
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
                <table class="table table-sm table-bordered table-striped table-responsive text-xs"
                    style="white-space: nowrap;" id="tableToCopy">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No.Rawat</th>
                            <th>No.R.M</th>
                            <th>Nama Pasien</th>
                            <th>Sts Lanjut</th>
                            <th>Kd.Prk</th>
                            <th>Pemeriksaan</th>
                            <th>Kode Dokter</th>
                            <th>Dokter P.J. Rad</th>
                            <th>NIP</th>
                            <th>Petugas Radiologi</th>
                            <th>Kd Perujuk</th>
                            <th>Dokter Perujuk</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Cara Bayar</th>
                            <th>Ruangan</th>
                            <th>Jasa Sarana</th>
                            <th>Paket BHP</th>
                            <th>JM PJ Rad</th>
                            <th>JM Petugas</th>
                            <th>JM Perujuk</th>
                            <th>KSO</th>
                            <th>Manajemen</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getPeriksaRadiologi as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td>{{ $item->status_lanjut }}</td>
                                <td>{{ $item->kd_jenis_prw }}</td>
                                <td>{{ $item->nm_perawatan }}</td>
                                <td>{{ $item->kd_dokter }}</td>
                                <td>{{ $item->nm_dokter }}</td>
                                <td>{{ $item->nip }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->dokter_perujuk }}</td>
                                <td>{{ $item->perujuk }}</td>
                                <td>{{ $item->tgl_periksa }}</td>
                                <td>{{ $item->jam }}</td>
                                <td>{{ $item->png_jawab }}</td>
                                <td>{{ $item->ruangan }}</td>
                                <td>{{ round($item->bagian_rs) }}</td>
                                <td>{{ round($item->bhp) }}</td>
                                <td>{{ round($item->tarif_tindakan_dokter) }}</td>
                                <td>{{ round($item->tarif_tindakan_petugas) }}</td>
                                <td>{{ round($item->tarif_perujuk) }}</td>
                                <td>{{ round($item->kso) }}</td>
                                <td>{{ round($item->menejemen) }}</td>
                                <td>{{ round($item->biaya) }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
