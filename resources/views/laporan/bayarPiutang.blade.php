@extends('..layout.layoutDashboard')
@section('title', 'Bayar Piutang')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('laporan.component.search-bayarPiutang')
            Jumlah Data : {{ count($bayarPiutang) }}
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <nav aria-label="Page navigation example">
                {{ $bayarPiutang->appends(request()->input())->links('pagination::bootstrap-4') }}
            </nav>
            <table class="table table-sm table-bordered table-striped table-responsive text-xs mb-3"
                style="white-space: nowrap;" id="tableToCopy">
                <tbody>
                    <tr>
                        <th>No</th>
                        <th>Tgl.Bayar</th>
                        <th>No.RM</th>
                        <th>status_lanjut</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Bayar</th>
                        <th>No. Nota</th>
                        <th>Registrasi</th>
                        <th>Obat+Emb+Tsl</th>
                        <th>Retur Oabt</th>
                        <th>Resep Pulang</th>
                        <th>Paket Tindakan</th>
                        <th>Operasi</th>
                        <th>Laborat</th>
                        <th>Radiologi</th>
                        <th>Tambahan</th>
                        <th>Kamar+Service</th>
                        <th>Potongan</th>
                        <th>Total</th>
                        <th>Ekses / Uang Muka</th>
                        <th>Cicilan(Rp)</th>
                        <th>Diskon Bayar(Rp)</th>
                        <th>Tidak Terbayar(Rp)</th>
                        <th>Catatan</th>
                        <th>No.Rawat/No.Tagihan</th>
                        <th>No.Sep</th>
                        <th>Status</th>
                        <th><i class="fas fa-eye"></i></th>
                    </tr>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($bayarPiutang as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->tgl_bayar }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->status_lanjut }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>
                                {{ $item->png_jawab }}
                            </td>
                            <td>
                                @foreach ($item->getNomorNota as $detail)
                                    {{ str_replace(':', '', $detail->nm_perawatan) }}
                                @endforeach
                            </td>

                            <td>{{ $item->getRegistrasi->sum('totalbiaya') }}</td>
                            <td>{{ $item->getObat->sum('totalbiaya') }}</td>
                            <td>{{ $item->getReturObat->sum('totalbiaya') }}</td>
                            <td>{{ $item->getResepPulang->sum('totalbiaya') }}</td>

                            <td>
                                {{ $item->getRalanDokter->sum('totalbiaya') +
                                    $item->getRalanParamedis->sum('totalbiaya') +
                                    $item->getRalanDrParamedis->sum('totalbiaya') +
                                    $item->getRanapDokter->sum('totalbiaya') +
                                    $item->getRanapDrParamedis->sum('totalbiaya') +
                                    $item->getRanapParamedis->sum('totalbiaya') }}
                            </td>

                            <td>{{ $item->getOprasi->sum('totalbiaya') }}</td>
                            <td>{{ $item->getLaborat->sum('totalbiaya') }}</td>
                            <td>{{ $item->getRadiologi->sum('totalbiaya') }}</td>
                            <td>{{ $item->getTambahan->sum('totalbiaya') }}</td>
                            <td>{{ $item->getKamarInap->sum('totalbiaya') }}</td>
                            <td>{{ $item->getPotongan->sum('totalbiaya') }}</td>

                            <td>
                                <b>
                                    {{ $item->getRegistrasi->sum('totalbiaya') +
                                        $item->getObat->sum('totalbiaya') +
                                        $item->getReturObat->sum('totalbiaya') +
                                        $item->getResepPulang->sum('totalbiaya') +
                                        $item->getRalanDokter->sum('totalbiaya') +
                                        $item->getRalanParamedis->sum('totalbiaya') +
                                        $item->getRalanDrParamedis->sum('totalbiaya') +
                                        $item->getRanapDokter->sum('totalbiaya') +
                                        $item->getRanapDrParamedis->sum('totalbiaya') +
                                        $item->getRanapParamedis->sum('totalbiaya') +
                                        $item->getOprasi->sum('totalbiaya') +
                                        $item->getLaborat->sum('totalbiaya') +
                                        $item->getRadiologi->sum('totalbiaya') +
                                        $item->getTambahan->sum('totalbiaya') +
                                        $item->getKamarInap->sum('totalbiaya') +
                                        $item->getPotongan->sum('totalbiaya') }}
                                </b>
                            </td>

                            <td>{{ $item->uangmuka }}</td>
                            <td>{{ $item->besar_cicilan }}</td>
                            <td>{{ $item->diskon_piutang }}</td>
                            <td>{{ $item->tidak_terbayar }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>
                                @foreach ($item->getNoSep as $detail)
                                    {{ $detail->no_sep }}
                                @endforeach
                            </td>
                            <td>{{ $item->status }}</td>

                            {{-- 🔹 Mata dipindah ke sini --}}
                            <td>
                                <div class="badge-group-sm">
                                    <a data-toggle="dropdown" href="#"><i class="fas fa-eye"></i></a>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <div class="dropdown-item">
                                            Dokter =
                                            {{ $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya') }}
                                        </div>
                                        <div class="dropdown-item">
                                            Paramedis =
                                            {{ $item->getRalanParamedis->sum('totalbiaya') + $item->getRanapParamedis->sum('totalbiaya') }}
                                        </div>
                                        <div class="dropdown-item">
                                            Dokter Paramedis =
                                            {{ $item->getRalanDrParamedis->sum('totalbiaya') + $item->getRanapDrParamedis->sum('totalbiaya') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    {{-- 🔸 Total --}}
                    <tr>
                        <th colspan="7">Total</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getRegistrasi->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getObat->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getReturObat->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getResepPulang->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) =>
                            $i->getRalanDokter->sum('totalbiaya') +
                            $i->getRalanParamedis->sum('totalbiaya') +
                            $i->getRalanDrParamedis->sum('totalbiaya') +
                            $i->getRanapDokter->sum('totalbiaya') +
                            $i->getRanapDrParamedis->sum('totalbiaya') +
                            $i->getRanapParamedis->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getOprasi->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getLaborat->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getRadiologi->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getTambahan->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getKamarInap->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->getPotongan->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) =>
                            $i->getRegistrasi->sum('totalbiaya') +
                            $i->getObat->sum('totalbiaya') +
                            $i->getReturObat->sum('totalbiaya') +
                            $i->getResepPulang->sum('totalbiaya') +
                            $i->getRalanDokter->sum('totalbiaya') +
                            $i->getRalanParamedis->sum('totalbiaya') +
                            $i->getRalanDrParamedis->sum('totalbiaya') +
                            $i->getRanapDokter->sum('totalbiaya') +
                            $i->getRanapDrParamedis->sum('totalbiaya') +
                            $i->getRanapParamedis->sum('totalbiaya') +
                            $i->getOprasi->sum('totalbiaya') +
                            $i->getLaborat->sum('totalbiaya') +
                            $i->getRadiologi->sum('totalbiaya') +
                            $i->getTambahan->sum('totalbiaya') +
                            $i->getKamarInap->sum('totalbiaya') +
                            $i->getPotongan->sum('totalbiaya')) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->uangmuka) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->besar_cicilan) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->diskon_piutang) }}</th>
                        <th>{{ $bayarPiutang->sum(fn($i) => $i->tidak_terbayar) }}</th>
                        <th colspan="5"></th>
                    </tr>
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
