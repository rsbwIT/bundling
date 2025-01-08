@extends('..layout.layoutDashboard')
@section('title', 'COB Bayar Piutang')

@section('konten')
    <div class="card">
        <div class="card-body">
            <form action="{{ url($url) }}">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="text" name="cariNomor" class="form-control form-control-xs"
                                    placeholder="Cari Nama/RM/No Rawat">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl1" class="form-control form-control-xs"
                                    value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl2" class="form-control form-control-xs"
                                    value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <i class="fa fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            Jumlah Data : {{ count($getCob) }}
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <table class="table table-sm table-bordered table-responsive text-xs mb-3" style="white-space: nowrap;"
                id="tableToCopy">
                <thead>
                    <th>No</th>
                    <th>Tgl. Bayar</th>
                    <th>No. RM</th>
                    <th>No. Rawat</th>
                    <th>Nama Pasien</th>
                    <th>Status Lanjut</th>
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
                    <th>Uang Muka</th>
                    <th>Total</th>
                    <th colspan="2">Penjamin</th>
                </thead>
                <tbody>
                    @foreach ($getCob as $key => $item)
                        <tr>
                            <td style="vertical-align: middle;">{{ $key + 1 }}</td>
                            <td style="vertical-align: middle;">{{ $item->tgl_bayar }}</td>
                            <td style="vertical-align: middle;">{{ $item->no_rkm_medis }}</td>
                            <td style="vertical-align: middle;">{{ $item->no_rawat }}</td>
                            <td style="vertical-align: middle;">{{ $item->nm_pasien }}</td>
                            <td style="vertical-align: middle;">{{ $item->status_lanjut }}
                            </td>
                            <td style="vertical-align: middle;">
                                @foreach ($item->getNomorNota as $detail)
                                    {{ str_replace(':', '', $detail->nm_perawatan) }}
                                @endforeach
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getRegistrasi->sum('totalbiaya') }}</td>
                            <td style="vertical-align: middle;">
                                {{ $item->getObat->sum('totalbiaya') }}</td>
                            <td style="vertical-align: middle;">
                                {{ $item->getReturObat->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getResepPulang->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getRalanDokter->sum('totalbiaya') +
                                    $item->getRalanParamedis->sum('totalbiaya') +
                                    $item->getRalanDrParamedis->sum('totalbiaya') +
                                    $item->getRanapDokter->sum('totalbiaya') +
                                    $item->getRanapDrParamedis->sum('totalbiaya') +
                                    $item->getRanapParamedis->sum('totalbiaya') }}
                                <div class="badge-group-sm float-right">
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
                            <td style="vertical-align: middle;">
                                {{ $item->getOprasi->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getLaborat->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getRadiologi->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getTambahan->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getKamarInap->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->getPotongan->sum('totalbiaya') }}
                            </td>
                            <td style="vertical-align: middle;">
                                {{ $item->uangmuka }}
                            </td>
                            <td style="vertical-align: middle;">
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
                            </td>
                            @foreach ($item->getDetailCob as $penjab)
                                <td>
                                    <span class="mx-1">{{ $penjab->png_jawab }} (Rp. {{ $penjab->totalpiutang }})</span>
                                </td>
                            @endforeach
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
