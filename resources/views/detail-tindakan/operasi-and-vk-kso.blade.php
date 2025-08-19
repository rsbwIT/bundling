@extends('..layout.layoutDashboard')
@section('title', 'Operasi & VK (KSO)')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan.component.cari-oprasi-vk-kso')
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 500px; overflow: auto;">
                <table class="table table-sm table-bordered table-striped text-xs" style="white-space: nowrap;"
                    id="tableToCopy">
                    <thead class="sticky-top bg-white">
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
                            <th>COB</th>
                            <th>Total Terbayar</th>
                            <th>Selisih</th>
                            <th>KSO</th>
                            <th>Hasil Akhir</th>
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
                            <th>Biaya Dokter Anak (15% JM Operator)</th>
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
                                <td title="Nilai Asli: {{ $total_piutang_rs }}">
                                    {{ round($total_piutang_rs) }}
                                </td>
                                <td title="Nilai Asli: {{ $item->besar_cicilan }}">{{ round($item->besar_cicilan) }}</td>
                                <td title="Nilai Asli: {{ $item->uangmuka }}">{{ round($item->uangmuka) }}</td>
                                <td title="Nilai Asli: {{ $item->cob_totalpiutang ?? 0 }}">{{ round($item->cob_totalpiutang ?? 0) }}</td>
                                @php
                                    $cob_value = $item->cob_totalpiutang ?? 0;
                                    $total_terbayar = $item->besar_cicilan + $item->uangmuka + $cob_value;
                                @endphp
                                <td title="Nilai Asli: {{ $total_terbayar }}">{{ round($total_terbayar) }}</td>
                                @php
                                    $selisih = $item->besar_cicilan - $total_piutang_rs + $item->uangmuka + $cob_value;
                                @endphp
                                <td title="Nilai Asli: {{ $selisih }}">{{ round($selisih) }}</td>
                                @php
                                    $kso_value = $item->getKSO->total_kso ?? 0;
                                @endphp
                                <td title="Nilai Asli: {{ $kso_value }}">{{ round($kso_value) }}</td>
                                @php
                                    $hasil_akhir = $total_terbayar - $kso_value;
                                @endphp
                                <td title="Nilai Asli: {{ $hasil_akhir }}">{{ round($hasil_akhir) }}</td>
                                <td>{{ $item->operator1 }}</td>
                                <td title="Nilai Asli: {{ $item->biayaoperator1 }}">{{ round($item->biayaoperator1) }}</td>
                                @php
                                    // Perhitungan INACBG untuk Operator 1 berdasarkan $hasil_akhir
                                    $operator1_inacbg_val = $hasil_akhir * 0.20;

                                    // Inisialisasi semua biaya JM ke 0
                                    $asisten_op1_inacbg_val = 0;
                                    $dokter_anestesi_inacbg_val = 0;
                                    $asisten_anestesi_inacbg_val = 0;
                                    $dokter_anak_inacbg_val = 0;

                                    // Lakukan perhitungan hanya jika ada nama personil (setelah menghapus spasi dan bukan '-')
                                    $asisten_op1_trimmed = trim($item->asisten_operator1);
                                    if (!empty($asisten_op1_trimmed) && $asisten_op1_trimmed != '-') {
                                        $asisten_op1_inacbg_val = $operator1_inacbg_val * 0.15;
                                    }

                                    $dokter_anestesi_trimmed = trim($item->dokter_anestesi);
                                    if (!empty($dokter_anestesi_trimmed) && $dokter_anestesi_trimmed != '-') {
                                        $dokter_anestesi_inacbg_val = $operator1_inacbg_val * 0.35;
                                    }

                                    $asisten_anestesi_trimmed = trim($item->asisten_anestesi);
                                    if (!empty($asisten_anestesi_trimmed) && $asisten_anestesi_trimmed != '-') {
                                        $asisten_anestesi_inacbg_val = $operator1_inacbg_val * 0.10;
                                    }

                                    $dokter_anak_trimmed = trim($item->dokter_anak);
                                    if (!empty($dokter_anak_trimmed) && $dokter_anak_trimmed != '-') {
                                        $dokter_anak_inacbg_val = $operator1_inacbg_val * 0.15;
                                    }
                                @endphp
                                <td title="Nilai Asli: {{ $operator1_inacbg_val }}">
                                    {{ round($operator1_inacbg_val) }}
                                </td>
                                <td>{{ $item->operator2 }}</td>
                                <td title="Nilai Asli: {{ $item->biayaoperator2 }}">{{ round($item->biayaoperator2) }}</td>
                                <td>{{ $item->operator3 }}</td>
                                <td title="Nilai Asli: {{ $item->biayaoperator3 }}">{{ round($item->biayaoperator3) }}</td>
                                <td>{{ $item->asisten_operator1 }}</td>
                                <td title="Nilai Asli: {{ $item->biayaasisten_operator1 }}">{{ round($item->biayaasisten_operator1) }}</td>
                                <td title="Nilai Asli: {{ $asisten_op1_inacbg_val }}">
                                    {{ $asisten_op1_inacbg_val > 0 ? round($asisten_op1_inacbg_val) : '-' }}
                                </td>
                                <td>{{ $item->asisten_operator2 }}</td>
                                <td title="Nilai Asli: {{ $item->biayaasisten_operator2 }}">{{ round($item->biayaasisten_operator2) }}</td>
                                <td>{{ $item->asisten_operator3 }}</td>
                                <td title="Nilai Asli: {{ $item->biayaasisten_operator3 }}">{{ round($item->biayaasisten_operator3) }}</td>
                                <td>{{ $item->instrumen }}</td>
                                <td title="Nilai Asli: {{ $item->biayainstrumen }}">{{ round($item->biayainstrumen) }}</td>
                                <td>{{ $item->dokter_anak }}</td>
                                <td title="Nilai Asli: {{ $item->biayadokter_anak }}">{{ round($item->biayadokter_anak) }}</td>
                                <td title="Nilai Asli: {{ $dokter_anak_inacbg_val }}">
                                    {{ $dokter_anak_inacbg_val > 0 ? round($dokter_anak_inacbg_val) : '-' }}
                                </td>
                                <td>{{ $item->perawaat_resusitas }}</td>
                                <td title="Nilai Asli: {{ $item->biayaperawaat_resusitas }}">{{ round($item->biayaperawaat_resusitas) }}</td>
                                <td>{{ $item->dokter_anestesi }}</td>
                                <td title="Nilai Asli: {{ $item->biayadokter_anestesi }}">{{ round($item->biayadokter_anestesi) }}</td>
                                <td title="Nilai Asli: {{ $dokter_anestesi_inacbg_val }}">
                                    {{ $dokter_anestesi_inacbg_val > 0 ? round($dokter_anestesi_inacbg_val) : '-' }}
                                </td>
                                <td>{{ $item->asisten_anestesi }}</td>
                                <td title="Nilai Asli: {{ $item->biayaasisten_anestesi }}">{{ round($item->biayaasisten_anestesi) }}</td>
                                <td title="Nilai Asli: {{ $asisten_anestesi_inacbg_val }}">
                                    {{ $asisten_anestesi_inacbg_val > 0 ? round($asisten_anestesi_inacbg_val) : '-' }}
                                </td>
                                <td>{{ $item->asisten_anestesi2 }}</td>
                                <td title="Nilai Asli: {{ $item->biayaasisten_anestesi2 }}">{{ round($item->biayaasisten_anestesi2) }}</td>
                                <td>{{ $item->bidan }}</td>
                                <td title="Nilai Asli: {{ $item->biayabidan }}">{{ round($item->biayabidan) }}</td>
                                <td>{{ $item->bidan2 }}</td>
                                <td title="Nilai Asli: {{ $item->biayabidan2 }}">{{ round($item->biayabidan2) }}</td>
                                <td>{{ $item->bidan3 }}</td>
                                <td title="Nilai Asli: {{ $item->biayabidan3 }}">{{ round($item->biayabidan3) }}</td>
                                <td>{{ $item->perawat_luar }}</td>
                                <td title="Nilai Asli: {{ $item->biayaperawat_luar }}">{{ round($item->biayaperawat_luar) }}</td>
                                <td>{{ $item->omloop }}</td>
                                <td title="Nilai Asli: {{ $item->biaya_omloop }}">{{ round($item->biaya_omloop) }}</td>
                                <td>{{ $item->omloop2 }}</td>
                                <td title="Nilai Asli: {{ $item->biaya_omloop2 }}">{{ round($item->biaya_omloop2) }}</td>
                                <td>{{ $item->omloop3 }}</td>
                                <td title="Nilai Asli: {{ $item->biaya_omloop3 }}">{{ round($item->biaya_omloop3) }}</td>
                                <td>{{ $item->omloop4 }}</td>
                                <td title="Nilai Asli: {{ $item->biaya_omloop4 }}">{{ round($item->biaya_omloop4) }}</td>
                                <td>{{ $item->omloop5 }}</td>
                                <td title="Nilai Asli: {{ $item->biaya_omloop5 }}">{{ round($item->biaya_omloop5) }}</td>
                                <td>{{ $item->dokter_pjanak }}</td>
                                <td title="Nilai Asli: {{ $item->biaya_dokter_pjanak }}">{{ round($item->biaya_dokter_pjanak) }}</td>
                                <td>{{ $item->dokter_umum }}</td>
                                <td title="Nilai Asli: {{ $item->biaya_dokter_umum }}">{{ round($item->biaya_dokter_umum) }}</td>
                                <td title="Nilai Asli: {{ $item->biayaalat }}">{{ round($item->biayaalat) }}</td>
                                <td title="Nilai Asli: {{ $item->biayasewaok }}">{{ round($item->biayasewaok) }}</td>
                                <td title="Nilai Asli: {{ $item->akomodasi }}">{{ round($item->akomodasi) }}</td>
                                <td title="Nilai Asli: {{ $item->bagian_rs }}">{{ round($item->bagian_rs) }}</td>
                                <td title="Nilai Asli: {{ $item->biayasarpras }}">{{ round($item->biayasarpras) }}</td>
                                <td>{{$item->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <style>
        .table-responsive {
            position: relative;
        }
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1; /* Pastikan header tabel tetap di atas konten lain saat scroll */
            background-color: inherit; /* Mewarisi warna background dari parent */
        }
        #tableToCopy thead.sticky-top th {
            background-color: white;
        }
    </style>
    <script>
        document.getElementById("copyButton").addEventListener("click", function() {
            copyTableToClipboard("tableToCopy");
        });

        function copyTableToClipboard(tableId) {
            const table = document.getElementById(tableId);
            let tableHTML = '<table>';
            tableHTML += '<thead>' + table.tHead.innerHTML + '</thead>';
            tableHTML += '<tbody>';
            const rows = table.tBodies[0].rows;
            for (let i = 0; i < rows.length; i++) {
                tableHTML += '<tr>';
                const cells = rows[i].cells;
                for (let j = 0; j < cells.length; j++) {
                    // Prioritaskan 'title' untuk nilai numerik, jika tidak ada, gunakan 'innerText'
                    // innerText secara otomatis mengabaikan tag HTML (<br>, <small>) dan menggabungkan teks
                    const originalValue = cells[j].getAttribute('title')
                        ? cells[j].getAttribute('title').replace('Nilai Asli: ', '')
                        : cells[j].innerText.replace(/\n/g, ' '); // Ganti newline character dengan spasi

                    tableHTML += '<td>' + originalValue.trim() + '</td>';
                }
                tableHTML += '</tr>';
            }
            tableHTML += '</tbody></table>';

            const listener = function(e) {
                e.preventDefault();
                e.clipboardData.setData('text/html', tableHTML);
                e.clipboardData.setData('text/plain', table.innerText);
            };

            document.addEventListener('copy', listener);
            try {
                document.execCommand('copy');
                alert("Tabel telah berhasil disalin ke clipboard.");
            } catch (err) {
                console.error("Tidak dapat menyalin tabel:", err);
                alert("Gagal menyalin tabel.");
            } finally {
                document.removeEventListener('copy', listener);
            }
        }
    </script>
@endsection
