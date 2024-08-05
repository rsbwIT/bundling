@extends('..layout.layoutDashboard')
@section('title', 'Ranap Dokter 3')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan.component.cari-dokter')
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
                        <th>No</th>
                        <th>No. Rawat</th>
                        <th>No. Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Kode Jenis Perawatan</th>
                        <th>Nama Perawatan</th>
                        <th>Kode Dokter</th>
                        <th>Dokter Yg Menangani</th>
                        <th>Spesialis</th>
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
                        <th>DPJP</th>
                        <th>Cicilan(Rp)</th>
                        <th>uangmuka</th>
                        <th>Bayar Inacbg</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ranapDokter as $key => $item)
                        @php
                            $currentNoRawat = $item->no_rawat;
                            $tempCollection = $ranapDokter->filter(function ($i) use ($currentNoRawat) {
                                return $i->no_rawat === $currentNoRawat;
                            });
                            $umum = $tempCollection->where('nm_sps', 'Umum')->unique('kd_dokter')->count();
                            $spesialis = $tempCollection->where('nm_sps', '!=', 'Umum')->unique('kd_dokter')->count();
                            foreach ($item->dpjpRanap as $dpjp) {
                                if (empty($item->operator1) && $item->kd_dokter == $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis < 2) {
                                    $persen = 0.09;
                                } elseif (empty($item->operator1) && $item->kd_dokter == $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis > 1) {
                                    $persen = 0.05;
                                } elseif (empty($item->operator1) && $item->kd_dokter != $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis > 1) {
                                    $persen = 0.04 / ($spesialis);
                                } elseif (empty($item->operator1) && $item->kd_dokter != $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis < 2) {
                                    $persen = 0.04;
                                } elseif (!empty($item->operator1) && $item->kd_dokter == $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis < 2) {
                                    $persen = 0.05;
                                } elseif (!empty($item->operator1) && $item->kd_dokter == $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis > 1) {
                                    $persen = 0.05;
                                } elseif (!empty($item->operator1) && $item->kd_dokter != $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis < 2) {
                                    $persen = 0.04 / $spesialis;
                                } elseif (!empty($item->operator1) && $item->kd_dokter != $dpjp->dpjp && $item->nm_sps != 'Umum' && $spesialis > 1) {
                                    $persen = 0.04 / $spesialis;
                                } elseif (empty($item->operator1) && $item->nm_sps == 'Umum' && $umum < 2) {
                                    $persen = 0.01 / $umum;
                                } elseif (empty($item->operator1) && $item->nm_sps == 'Umum' && $umum > 1) {
                                    $persen = 0.01 / $umum;
                                } elseif (!empty($item->operator1) && $item->nm_sps == 'Umum' && $umum < 2) {
                                    $persen = 0.01 / $umum;
                                } elseif (!empty($item->operator1) && $item->nm_sps == 'Umum' && $umum > 1) {
                                    $persen = 0.01 / $umum;
                                } else {
                                    $persen = 0;
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->kd_jenis_prw }}</td>
                            <td>{{ $item->nm_perawatan }}</td>
                            <td>{{ $item->kd_dokter }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td>{{ $item->nm_sps }}</td>
                            <td>{{ $item->tgl_perawatan }}</td>
                            <td>{{ $item->jam_rawat }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>{{ $item->ruang ?? $item->nm_poli }}</td>
                            <td>{{ round($item->total_material) }}</td>
                            <td>{{ round($item->total_bhp) }}</td>
                            <td>{{ round($item->total_tarif_tindakandr) }}</td>
                            <td>{{ round($item->total_kso) }}</td>
                            <td>{{ round($item->total_menejemen) }}</td>
                            <td>{{ round($item->total_biaya_rawat) }}</td>
                            <td class="bg-danger">
                                @foreach ($item->dpjpRanap as $dpjp)
                                    {{ $dpjp->dpjp }}
                                @endforeach
                            </td>
                            <td class="bg-success">{{ round($item->besar_cicilan) }}</td>
                            <td class="bg-success">{{ round($item->uangmuka) }}</td>
                            <td class="bg-success">{{ round(($item->uangmuka + $item->besar_cicilan) * $persen) }}
                                ||{{ number_format($persen, 3) }}</td>
                        </tr>
                    @endforeach
                    {{-- @foreach ($RalanDokter as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->no_rawat }}</td>
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
                            <td>{{ round($item->total_material) }}</td>
                            <td>{{ round($item->total_bhp) }}</td>
                            <td>{{ round($item->total_tarif_tindakandr) }}</td>
                            <td>{{ round($item->total_kso) }}</td>
                            <td>{{ round($item->total_menejemen) }}</td>
                            <td>{{ round($item->total_biaya_rawat) }}</td>
                            <td>
                                @foreach ($item->dpjpRanap as $dpjp)
                                    {{ $dpjp->dpjp }}
                                @endforeach
                            </td>
                            <td>{{ round($item->besar_cicilan) }}</td>
                            <td>{{ round($item->uangmuka) }}</td>
                            <td></td>
                        </tr>
                    @endforeach --}}
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
