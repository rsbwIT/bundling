@extends('..layout.layoutDashboard')
@section('title', 'Ranap Dokter (Umum)')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan-umum.component.cari-dokter')
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <table class="table table-sm table-bordered table-striped table-responsive text-xs" style="white-space: nowrap;"
                id="tableToCopy">
                <thead>
                    <tr>
                        <th>No. Rawat</th>
                        <th>No. Nota</th>
                        <th>Tanggal Bayar</th>
                        <th>No. Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Kode Jenis Perawatan</th>
                        <th>Nama Perawatan</th>
                        <th>Kode Dokter</th>
                        <th>Dokter Yg Menangani</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Cara Bayar</th>
                        <th>Ruang</th>
                        <th>Jasa Sarana</th>
                        <th>Paket BHP</th>
                        <th>JM Dokter</th>
                        <th>KSO</th>
                        <th>Manajemen</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $mergedData = $ranapDokterUmum->merge($RalanDokterUmum);
                        $sortedData = $mergedData->sortBy('no_rawat');
                        $t_mat=0; $t_bhp=0; $t_jmd=0; $t_kso=0; $t_man=0; $t_tot=0;
                    @endphp
                    @foreach ($sortedData as $item)
                        @php
                            $t_mat += $item->material;
                            $t_bhp += $item->bhp;
                            $t_jmd += $item->tarif_tindakandr;
                            $t_kso += $item->kso;
                            $t_man += $item->menejemen;
                            $t_tot += $item->biaya_rawat;
                        @endphp
                        <tr>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_nota }}</td>
                            <td>{{ $item->tgl_byr }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->kd_jenis_prw }}</td>
                            <td>{{ $item->nm_perawatan }}</td>
                            <td>{{ $item->kd_dokter }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td>{{ $item->tgl_perawatan }}</td>
                            <td>{{ $item->jam_rawat }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>{{ $item->ruang ?? $item->nm_poli }}</td>
                            <!-- Tampilkan ruang jika ada, jika tidak, tampilkan nama poli -->
                            <td>{{ $item->material }}</td>
                            <td>{{ $item->bhp }}</td>
                            <td>{{ $item->tarif_tindakandr }}</td>
                            <td>{{ $item->kso }}</td>
                            <td>{{ $item->menejemen }}</td>
                            <td>{{ $item->biaya_rawat }}</td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot>
                    <tr>
                        @for($i=0; $i<12; $i++) <th></th> @endfor
                        <th class="text-right">GRAND TOTAL :</th>
                        <th>{{ number_format($t_mat) }}</th>
                        <th>{{ number_format($t_bhp) }}</th>
                        <th>{{ number_format($t_jmd) }}</th>
                        <th>{{ number_format($t_kso) }}</th>
                        <th>{{ number_format($t_man) }}</th>
                        <th>{{ number_format($t_tot) }}</th>
                    </tr>
                </tfoot>
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
