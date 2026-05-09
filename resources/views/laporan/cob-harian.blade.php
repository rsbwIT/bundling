@extends('..layout.layoutDashboard')
@section('title', 'Pasien COB (Harian)')

@section('konten')
    <div class="card">
        <div class="card-body">

            <form action="{{ url('cob-harian') }}">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="text" name="cariNomor" class="form-control form-control-xs"
                                    placeholder="Cari Nama/RM/No Rawat">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <select class="form-control" name="stsLanjut">
                                    <option value="Ralan">Rawat Jalan</option>
                                    <option value="Ranap">Rawat Inap</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl1" class="form-control form-control-xs"
                                    value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl2" class="form-control form-control-xs"
                                    value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-md btn-primary">
                                <i class="fa fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            Jumlah Data : {{ count($getCobHarian) }}

            <div class="row no-print mb-2">
                <div class="col-12">

                    <button type="button" class="btn btn-info btn-sm" onclick="toggleNominal()">
                        <i class="fas fa-eye"></i> Hide / Show Nominal
                    </button>

                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>

                </div>
            </div>

            <table class="table table-sm table-bordered table-responsive text-xs mb-3"
                style="white-space: nowrap;" id="tableToCopy">

                <thead>
                    <tr>
                        <th>No Rawat</th>
                        <th>Nama Pasien</th>
                        <th>Nama Poli</th>
                        <th>Kode Dokter</th>
                        <th>Nama Dokter</th>
                        <th>Nomor Nota</th>

                        <th class="kolom-nominal">Registrasi</th>
                        <th class="kolom-nominal">Obat+Emb+Tsl</th>
                        <th class="kolom-nominal">Retur Oabt</th>
                        <th class="kolom-nominal">Resep Pulang</th>
                        <th class="kolom-nominal">Paket Tindakan</th>
                        <th class="kolom-nominal">Operasi</th>
                        <th class="kolom-nominal">Laborat</th>
                        <th class="kolom-nominal">Radiologi</th>
                        <th class="kolom-nominal">Tambahan</th>
                        <th class="kolom-nominal">Kamar+Service</th>
                        <th class="kolom-nominal">Potongan</th>
                        <th class="kolom-nominal">Total</th>

                        <th class="text-center" colspan="4">Penjamin</th>
                    </tr>
                </thead>

                @forelse ($getCobHarian as $item)
                    <tr>

                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->nm_poli }}</td>
                        <td>{{ $item->kd_dokter }}</td>
                        <td>{{ $item->nm_dokter }}</td>

                        <td>
                            @foreach ($item->getNomorNota as $detail)
                                {{ str_replace(':', '', $detail->nm_perawatan) }}
                            @endforeach
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getObat->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getReturObat->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getResepPulang->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">

                            {{ number_format(
                                $item->getRalanDokter->sum('totalbiaya') +
                                $item->getRalanParamedis->sum('totalbiaya') +
                                $item->getRalanDrParamedis->sum('totalbiaya') +
                                $item->getRanapDokter->sum('totalbiaya') +
                                $item->getRanapDrParamedis->sum('totalbiaya') +
                                $item->getRanapParamedis->sum('totalbiaya')
                            , 0, ',', '.') }}

                            <div class="badge-group-sm float-right">
                                <a data-toggle="dropdown" href="#">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                                    <div class="dropdown-item text-right">
                                        Dokter =
                                        {{ number_format(
                                            $item->getRalanDokter->sum('totalbiaya') +
                                            $item->getRanapDokter->sum('totalbiaya')
                                        , 0, ',', '.') }}
                                    </div>

                                    <div class="dropdown-item text-right">
                                        Paramedis =
                                        {{ number_format(
                                            $item->getRalanParamedis->sum('totalbiaya') +
                                            $item->getRanapParamedis->sum('totalbiaya')
                                        , 0, ',', '.') }}
                                    </div>

                                    <div class="dropdown-item text-right">
                                        Dokter Paramedis =
                                        {{ number_format(
                                            $item->getRalanDrParamedis->sum('totalbiaya') +
                                            $item->getRanapDrParamedis->sum('totalbiaya')
                                        , 0, ',', '.') }}
                                    </div>

                                </div>
                            </div>

                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getOprasi->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getLaborat->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getRadiologi->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getTambahan->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getKamarInap->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getPotongan->sum('totalbiaya'), 0, ',', '.') }}
                        </td>

                        <td class="text-right text-bold kolom-nominal">
                            {{ number_format(
                                $item->getRegistrasi->sum('totalbiaya') +
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
                                $item->getPotongan->sum('totalbiaya')
                            , 0, ',', '.') }}
                        </td>

                        @foreach ($item->getPenjabCOB as $penjab)

                            <td>{{ $penjab->png_jawab }}</td>

                            <td class="text-right">
                                {{ number_format($penjab->totalpiutang, 0, ',', '.') }}
                            </td>

                        @endforeach

                    </tr>
                @empty
                    <tr>
                        <td colspan="20" class="text-center">
                            Silahkan Cari Data
                        </td>
                    </tr>
                @endforelse

            </table>

        </div>
    </div>

    <script>

        function toggleNominal() {
            document.querySelectorAll('.kolom-nominal').forEach(function(el) {
                el.classList.toggle('d-none');
            });
        }

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