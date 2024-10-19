@extends('..layout.layoutDashboard')
@section('title', 'Operasi & VK')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan.component.cari-oprasi-vk')
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
                        <th>No. </th>
                        <th>No. Rawat</th>
                        <th>No. Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Kode Paket</th>
                        <th>Nama Perawatan</th>
                        <th>Tanggal Operasi</th>
                        <th>Penanggung Jawab</th>
                        <th>Ruangan</th>
                        <th>Rincian Piutang (Total)</th>
                        <th>Cicilan(Rp)</th>
                        <th>Ekses / Uang Muka</th>
                        <th>Total Terbayar</th>
                        <th>Selisih</th>
                        <th>Operator 1</th>
                        <th>Biaya Operator 1</th>
                        <th>Operator 1 (20% Koding INACBG)</th>
                        <th>Operator 2</th>
                        <th>Biaya Operator 2</th>
                        <th>Operator 3</th>
                        <th>Biaya Operator 3</th>
                        <th>Asisten Operator 1</th>
                        <th>Biaya Asisten Operator 1</th>
                        <th>JM Asisten Operator 1 (15% JM Operator)</th>
                        <th>Asisten Operator 2</th>
                        <th>Biaya Asisten Operator 2</th>
                        <th>Asisten Operator 3</th>
                        <th>Biaya Asisten Operator 3</th>
                        <th>Instrumen</th>
                        <th>Biaya Instrumen</th>
                        <th>Dokter Anak</th>
                        <th>Biaya Dokter Anak</th>
                        <th>Perawat Resusitas</th>
                        <th>Biaya Perawat Resusitas</th>
                        <th>Dokter Anestesi</th>
                        <th>Biaya Dokter Anestesi</th>
                        <th>Biaya Dokter Anestesi (35% JM Operator)</th>
                        <th>Asisten Anestesi</th>
                        <th>Biaya Asisten Anestesi</th>
                        <th>Biaya Asisten Anestesi (10% JM Operator)</th>
                        <th>Asisten Anestesi 2</th>
                        <th>Biaya Asisten Anestesi 2</th>
                        <th>Bidan</th>
                        <th>Biaya Bidan</th>
                        <th>Bidan 2</th>
                        <th>Biaya Bidan 2</th>
                        <th>Bidan 3</th>
                        <th>Biaya Bidan 3</th>
                        <th>Perawat Luar</th>
                        <th>Biaya Perawat Luar</th>
                        <th>Omloop</th>
                        <th>Biaya Omloop</th>
                        <th>Omloop 2</th>
                        <th>Biaya Omloop 2</th>
                        <th>Omloop 3</th>
                        <th>Biaya Omloop 3</th>
                        <th>Omloop 4</th>
                        <th>Biaya Omloop 4</th>
                        <th>Omloop 5</th>
                        <th>Biaya Omloop 5</th>
                        <th>Dokter Pjanak</th>
                        <th>Biaya Dokter Pjanak</th>
                        <th>Dokter Umum</th>
                        <th>Biaya Dokter Umum</th>
                        <th>Biaya Alat</th>
                        <th>Biaya Sewa OK</th>
                        <th>Akomodasi</th>
                        <th>Bagian RS</th>
                        <th>Biaya Sarpras</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($OperasiAndVK as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->kode_paket }}</td>
                            <td>{{ $item->nm_perawatan }}</td>
                            <td>{{ $item->tgl_operasi }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>{{ $item->ruangan }}</td>
                            @php
                                $total_piutang_rs =
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
                                    $item->getPotongan->sum('totalbiaya') -
                                    $item->uangmuka;
                            @endphp
                            <td>
                                {{ $total_piutang_rs }}
                            </td>
                            <td>{{ $item->besar_cicilan }}</td>
                            <td>{{ $item->uangmuka }}</td>
                            @php
                                $total_terbayar = $item->besar_cicilan + $item->uangmuka;
                            @endphp
                            <td>{{ $total_terbayar }}</td>
                            <td>{{ $item->besar_cicilan - $total_piutang_rs + $item->uangmuka }}</td>
                            <td>{{ $item->operator1 }}</td>
                            <td>{{ round($item->biayaoperator1) }}</td>
                            @php
                                $oprator1INACBG = $total_terbayar * 0.2;
                            @endphp
                            <td>
                                @if ($item->kd_pj == 'BPJ')
                                    {{ round($oprator1INACBG) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->operator2 }}</td>
                            <td>{{ round($item->biayaoperator2) }}</td>
                            <td>{{ $item->operator3 }}</td>
                            <td>{{ round($item->biayaoperator3) }}</td>
                            <td>{{ $item->asisten_operator1 }}</td>
                            <td>{{ round($item->biayaasisten_operator1) }}</td>
                            <td>
                                @if ($item->kd_pj == 'BPJ')
                                    {{ round($oprator1INACBG * 0.15) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->asisten_operator2 }}</td>
                            <td>{{ round($item->biayaasisten_operator2) }}</td>
                            <td>{{ $item->asisten_operator3 }}</td>
                            <td>{{ round($item->biayaasisten_operator3) }}</td>
                            <td>{{ $item->instrumen }}</td>
                            <td>{{ round($item->biayainstrumen) }}</td>
                            <td>{{ $item->dokter_anak }}</td>
                            <td>{{ round($item->biayadokter_anak) }}</td>
                            <td>{{ $item->perawaat_resusitas }}</td>
                            <td>{{ round($item->biayaperawaat_resusitas) }}</td>
                            <td>{{ $item->dokter_anestesi }}</td>
                            <td>{{ round($item->biayadokter_anestesi) }}</td>
                            <td>
                                @if ($item->kd_pj == 'BPJ')
                                    {{ round($oprator1INACBG * 0.35) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->asisten_anestesi }}</td>
                            <td>{{ round($item->biayaasisten_anestesi) }}</td>
                            <td>
                                @if ($item->kd_pj == 'BPJ')
                                    {{ round($oprator1INACBG * 0.1) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->asisten_anestesi2 }}</td>
                            <td>{{ round($item->biayaasisten_anestesi2) }}</td>
                            <td>{{ $item->bidan }}</td>
                            <td>{{ round($item->biayabidan) }}</td>
                            <td>{{ $item->bidan2 }}</td>
                            <td>{{ round($item->biayabidan2) }}</td>
                            <td>{{ $item->bidan3 }}</td>
                            <td>{{ round($item->biayabidan3) }}</td>
                            <td>{{ $item->perawat_luar }}</td>
                            <td>{{ round($item->biayaperawat_luar) }}</td>
                            <td>{{ $item->omloop }}</td>
                            <td>{{ round($item->biaya_omloop) }}</td>
                            <td>{{ $item->omloop2 }}</td>
                            <td>{{ round($item->biaya_omloop2) }}</td>
                            <td>{{ $item->omloop3 }}</td>
                            <td>{{ round($item->biaya_omloop3) }}</td>
                            <td>{{ $item->omloop4 }}</td>
                            <td>{{ round($item->biaya_omloop4) }}</td>
                            <td>{{ $item->omloop5 }}</td>
                            <td>{{ round($item->biaya_omloop5) }}</td>
                            <td>{{ $item->dokter_pjanak }}</td>
                            <td>{{ round($item->biaya_dokter_pjanak) }}</td>
                            <td>{{ $item->dokter_umum }}</td>
                            <td>{{ round($item->biaya_dokter_umum) }}</td>
                            <td>{{ round($item->biayaalat) }}</td>
                            <td>{{ round($item->biayasewaok) }}</td>
                            <td>{{ round($item->akomodasi) }}</td>
                            <td>{{ round($item->bagian_rs) }}</td>
                            <td>{{ round($item->biayasarpras) }}</td>
                            <td>{{$item->status }}</td>
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
