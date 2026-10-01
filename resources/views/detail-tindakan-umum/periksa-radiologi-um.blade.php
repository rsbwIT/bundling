@extends('..layout.layoutDashboard')
@section('title', 'Periksa Radiologi (Umum)')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan-umum.component.cari-dokter-paramedis')
            Jumlah Data : {{ count($getPeriksaRadiologi) }}
            <div class="row no-print mb-2">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButtonRalan">
                        <i class="fas fa-copy"></i> Copy table Ralan
                    </button>
                    <button type="button" class="btn btn-default float-right mr-2" id="copyButtonRanap">
                        <i class="fas fa-copy"></i> Copy table Ranap
                    </button>
                </div>
            </div>

            <!-- RAWAT JALAN -->
            <h4 class="mt-4">Rawat Jalan (Ralan)</h4>
                <table class="table table-sm table-bordered table-striped table-responsive text-xs"
                    style="white-space: nowrap;" id="tableToCopyRalan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No.Rawat</th>
                            <th>No. Nota</th>
                            <th>Tgl Bayar</th>
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
                        @php
                            $tot_sarana = 0;
                            $tot_bhp = 0;
                            $tot_jm_pj = 0;
                            $tot_jm_petugas = 0;
                            $tot_jm_perujuk = 0;
                            $tot_kso = 0;
                            $tot_manajemen = 0;
                            $tot_biaya = 0;
                        @endphp
                        @foreach ($getPeriksaRadiologi->where('status_lanjut', 'Ralan') as $key => $item)
                            @php
                                $tot_sarana += $item->bagian_rs;
                                $tot_bhp += $item->bhp;
                                $tot_jm_pj += $item->tarif_tindakan_dokter;
                                $tot_jm_petugas += $item->tarif_tindakan_petugas;
                                $tot_jm_perujuk += $item->tarif_perujuk;
                                $tot_kso += $item->kso;
                                $tot_manajemen += $item->menejemen;
                                $tot_biaya += $item->biaya;
                            @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_nota }}</td>
                                <td>{{ $item->tgl_byr }}</td>
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
                                <td>{{ $item->bagian_rs }}</td>
                                <td>{{ $item->bhp }}</td>
                                <td>{{ $item->tarif_tindakan_dokter }}</td>
                                <td>{{ $item->tarif_tindakan_petugas }}</td>
                                <td>{{ $item->tarif_perujuk }}</td>
                                <td>{{ $item->kso }}</td>
                                <td>{{ $item->menejemen }}</td>
                                <td>{{ $item->biaya }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            @for($i=0; $i<18; $i++) <th></th> @endfor
                            <th class="text-right">GRAND TOTAL :</th>
                            <th>{{ number_format($tot_sarana) }}</th>
                            <th>{{ number_format($tot_bhp) }}</th>
                            <th>{{ number_format($tot_jm_pj) }}</th>
                            <th>{{ number_format($tot_jm_petugas) }}</th>
                            <th>{{ number_format($tot_jm_perujuk) }}</th>
                            <th>{{ number_format($tot_kso) }}</th>
                            <th>{{ number_format($tot_manajemen) }}</th>
                            <th>{{ number_format($tot_biaya) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- RAWAT INAP -->
            <h4 class="mt-4">Rawat Inap (Ranap)</h4>
            <div class="row no-print">
                <table class="table table-sm table-bordered table-striped table-responsive text-xs"
                    style="white-space: nowrap;" id="tableToCopyRanap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No.Rawat</th>
                            <th>No. Nota</th>
                            <th>Tgl Bayar</th>
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
                        @php
                            $tot_sarana_ranap = 0;
                            $tot_bhp_ranap = 0;
                            $tot_jm_pj_ranap = 0;
                            $tot_jm_petugas_ranap = 0;
                            $tot_jm_perujuk_ranap = 0;
                            $tot_kso_ranap = 0;
                            $tot_manajemen_ranap = 0;
                            $tot_biaya_ranap = 0;
                            $no_ranap = 1;
                        @endphp
                        @foreach ($getPeriksaRadiologi->where('status_lanjut', 'Ranap') as $key => $item)
                            @php
                                $tot_sarana_ranap += $item->bagian_rs;
                                $tot_bhp_ranap += $item->bhp;
                                $tot_jm_pj_ranap += $item->tarif_tindakan_dokter;
                                $tot_jm_petugas_ranap += $item->tarif_tindakan_petugas;
                                $tot_jm_perujuk_ranap += $item->tarif_perujuk;
                                $tot_kso_ranap += $item->kso;
                                $tot_manajemen_ranap += $item->menejemen;
                                $tot_biaya_ranap += $item->biaya;
                            @endphp
                            <tr>
                                <td>{{ $no_ranap++ }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_nota }}</td>
                                <td>{{ $item->tgl_byr }}</td>
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
                                <td>{{ $item->bagian_rs }}</td>
                                <td>{{ $item->bhp }}</td>
                                <td>{{ $item->tarif_tindakan_dokter }}</td>
                                <td>{{ $item->tarif_tindakan_petugas }}</td>
                                <td>{{ $item->tarif_perujuk }}</td>
                                <td>{{ $item->kso }}</td>
                                <td>{{ $item->menejemen }}</td>
                                <td>{{ $item->biaya }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            @for($i=0; $i<18; $i++) <th></th> @endfor
                            <th class="text-right">GRAND TOTAL :</th>
                            <th>{{ number_format($tot_sarana_ranap) }}</th>
                            <th>{{ number_format($tot_bhp_ranap) }}</th>
                            <th>{{ number_format($tot_jm_pj_ranap) }}</th>
                            <th>{{ number_format($tot_jm_petugas_ranap) }}</th>
                            <th>{{ number_format($tot_jm_perujuk_ranap) }}</th>
                            <th>{{ number_format($tot_kso_ranap) }}</th>
                            <th>{{ number_format($tot_manajemen_ranap) }}</th>
                            <th>{{ number_format($tot_biaya_ranap) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("copyButtonRalan").addEventListener("click", function() {
            copyTableToClipboard("tableToCopyRalan");
        });

        document.getElementById("copyButtonRanap").addEventListener("click", function() {
            copyTableToClipboard("tableToCopyRanap");
        });
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
