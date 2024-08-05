@extends('..layout.layoutDashboard')
@section('title', 'Ranap Dokter 2')

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
                        <th>No. Rawat</th>
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
                        <th>DPJP</th>
                        <th>Cicilan(Rp)</th>
                        <th>Total Detail %INCBG</th>
                        <th>Total Detail</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $sortedData = $ranapDokter->sortBy('no_rawat');
                        $groupedData = $sortedData->groupBy(function ($item) {
                            return $item->no_rawat . '-' . $item->kd_dokter;
                        });
                    @endphp
                    @foreach ($groupedData as $kd_dokter => $group)
                        @php
                            $totalBiayaRawat = $group->sum('biaya_rawat');
                            foreach ($group as $item) {
                                foreach ($item->dpjpRanap as $dpjp) {
                                    if (empty($item->operator1) && $item->kd_dokter == $dpjp->dpjp) {
                                        $persen = 0.05;
                                        break 2;
                                    } elseif ($item->kd_dokter != $dpjp->dpjp) {
                                        $persen = 0.03;
                                        break 2;
                                    } else {
                                        $persen = 0;
                                    }
                                }
                            }
                        @endphp
                        @foreach ($group as $index => $item)
                            <tr>
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
                                <td>{{ round($item->material) }}</td>
                                <td>{{ round($item->bhp) }}</td>
                                <td>{{ round($item->tarif_tindakandr) }}</td>
                                <td>{{ round($item->kso) }}</td>
                                <td>{{ round($item->menejemen) }}</td>
                                <td>{{ round($item->biaya_rawat) }}</td>
                                @if ($index === 0)
                                    <td rowspan="{{ $group->count() }}">
                                        @foreach ($item->dpjpRanap as $dpjp)
                                            {{ $dpjp->dpjp }}
                                        @endforeach
                                    </td>
                                    <td rowspan="{{ $group->count() }}">
                                        {{ round($item->besar_cicilan) }}
                                    </td>
                                    <td rowspan="{{ $group->count() }}">
                                        {{ round($item->besar_cicilan * $persen) }}
                                    </td>
                                    <td rowspan="{{ $group->count() }}">
                                        {{ round($totalBiayaRawat) }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach

                    {{-- RALAN DOKTER --}}
                    @php
                        $sortedData = $RalanDokter->sortBy('no_rawat');
                        $groupedData = $sortedData->groupBy(function ($item) {
                            return $item->no_rawat . '-' . $item->kd_dokter;
                        });
                    @endphp
                    @foreach ($groupedData as $kd_dokter => $group)
                        @php
                            $totalBiayaRawat = $group->sum('biaya_rawat');
                        @endphp
                        @foreach ($group as $index => $item)
                            <tr>
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
                                <td>{{ round($item->material) }}</td>
                                <td>{{ round($item->bhp) }}</td>
                                <td>{{ round($item->tarif_tindakandr) }}</td>
                                <td>{{ round($item->kso) }}</td>
                                <td>{{ round($item->menejemen) }}</td>
                                <td>{{ round($item->biaya_rawat) }}</td>
                                @if ($index === 0)
                                    <td rowspan="{{ $group->count() }}"></td>
                                    <td rowspan="{{ $group->count() }}"></td>
                                    <td rowspan="{{ $group->count() }}"></td>
                                    <td rowspan="{{ $group->count() }}">
                                        {{ round($totalBiayaRawat) }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
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
