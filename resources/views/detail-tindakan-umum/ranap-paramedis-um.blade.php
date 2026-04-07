@extends('..layout.layoutDashboard')
@section('title', 'Ranap Paramedis (Umum)')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan-umum.component.cari-paramedis')
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
                        <th>No.Rawat</th>
                        <th>No. Nota</th>
                        <th>Tanggal Bayar</th>
                        <th>No.RM</th>
                        <th>Nama Pasien</th>
                        <th>Kd.Tindakan</th>
                        <th>Nama Perawatan</th>
                        <th>NIP</th>
                        <th>Paramedis Yg Menangani</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Cara Bayar</th>
                        <th>Ruang</th>
                        <th>Jasa Sarana</th>
                        <th>Paket BHP</th>
                        <th>JM Paramedis</th>
                        <th>KSO</th>
                        <th>Manajemen</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $mergedData = $getRanapParamedis->merge($RalanParamedis);
                        $sortedData = $mergedData->sortBy('no_rawat');
                        $t_mat=0; $t_bhp=0; $t_jmp=0; $t_kso=0; $t_man=0; $t_tot=0;
                    @endphp

                    @foreach ($sortedData as $item)
                        @php
                            $t_mat += $item->material;
                            $t_bhp += $item->bhp;
                            $t_jmp += $item->tarif_tindakanpr;
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
                            <td>{{ $item->nip }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->tgl_perawatan }}</td>
                            <td>{{ $item->jam_rawat }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>{{ $item->ruang ?? $item->nm_poli }}</td>
                            <!-- Tampilkan ruang jika ada, jika tidak, tampilkan nama poli -->
                            <td>{{ $item->material }}</td>
                            <td>{{ $item->bhp }}</td>
                            <td>{{ $item->tarif_tindakanpr }}</td>
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
                        <th>{{ number_format($t_jmp) }}</th>
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
