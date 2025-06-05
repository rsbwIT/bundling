<div class="antrian-container">
    <div class="antrian-card">
        <h2 class="antrian-title">Panggil Antrian Farmasi</h2>
        <div class="antrian-legend">
            <span class="legend panggil"></span> : Panggil
            <span class="legend ada"></span> : Ada
            <span class="legend tidak-ada"></span> : Tidak Ada
            <span class="legend ulang"></span> : Belum Terpanggil / Ulang
        </div>
        <div style="overflow-x: auto;">
            <table class="antrian-table">
                <thead>
                    <tr>
                        <th>No. Antrian</th>
                        <th>No. Rekam Medik</th>
                        <th>Nama Pasien</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>No. Rawat</th>
                        <th>Racik/Non Racik</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($antrians as $antrian)
                        @php
                            $rowClass = 'row-menunggu';
                            if ($antrian->status === 'dipanggil') {
                                $rowClass = 'row-dipanggil';
                            } elseif ($antrian->status === 'selesai') {
                                $rowClass = 'row-selesai';
                            } elseif ($antrian->status === 'tidak ada') {
                                $rowClass = 'row-tidak-ada';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ data_get($antrian, 'nomor_antrian', '-') }}</td>
                            <td>{{ data_get($antrian, 'rekam_medik', '-') }}</td>
                            <td>{{ data_get($antrian, 'nama_pasien', '-') }}</td>
                            <td>{{ data_get($antrian, 'keterangan', '-') }}</td>
                            <td>{{ data_get($antrian, 'tanggal', '-') }}</td>
                            <td>{{ data_get($antrian, 'no_rawat', '-') }}</td>
                            <td>{{ data_get($antrian, 'racik_non_racik', '-') }}</td>
                            <td>{{ data_get($antrian, 'status', '-') }}</td>
                            <td style="text-align: center;">
                                @if (!empty(data_get($antrian, 'no_rawat')))
                                    <button wire:click="panggil('{{ data_get($antrian, 'no_rawat') }}')" class="btn-action btn-panggil"><i class="fas fa-bullhorn"></i> Panggil</button>
                                    <button wire:click="markAda('{{ data_get($antrian, 'no_rawat') }}')" class="btn-action btn-ada"><i class="fas fa-check"></i> Ada</button>
                                    <button wire:click="markTidakAda('{{ data_get($antrian, 'no_rawat') }}')" class="btn-action btn-tidak-ada"><i class="fas fa-times"></i> Tidak Ada</button>
                                    <button wire:click="ulangiPanggil('{{ data_get($antrian, 'nomor_antrian') }}')" class="btn-action btn-ulangi"><i class="fas fa-redo"></i> Ulang</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center;">Tidak ada antrian farmasi saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .antrian-container {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 20px;
            background: #f4f7f6;
        }
        .antrian-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px #0001;
            padding: 20px;
        }
        .antrian-title {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.4em;
        }
        .antrian-legend {
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        .legend {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 3px;
            margin-right: 4px;
            vertical-align: middle;
        }
        .legend.panggil { background: #3498db; }
        .legend.ada { background: #2ecc71; }
        .legend.tidak-ada { background: #e74c3c; }
        .legend.ulang { background: #fff; border: 1px solid #ccc; }
        .antrian-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .antrian-table th, .antrian-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
        .antrian-table thead tr {
            background: #ecf0f1;
        }
        .antrian-table tr.row-dipanggil { background: #e3f0fc; }
        .antrian-table tr.row-selesai { background: #eafbe7; color: #333; }
        .antrian-table tr.row-tidak-ada { background: #fdeaea; }
        .antrian-table tr.row-menunggu { background: #fff; }
        .antrian-table tr:hover { background: #f0f8ff; }
        .btn-action {
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            margin: 2px 1px;
            font-size: 0.95em;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-panggil { background: #3498db; color: #fff; }
        .btn-panggil:hover { background: #217dbb; }
        .btn-ada { background: #2ecc71; color: #fff; }
        .btn-ada:hover { background: #27ae60; color: #fff; }
        .btn-tidak-ada { background: #e74c3c; color: #fff; }
        .btn-tidak-ada:hover { background: #c0392b; }
        .btn-ulangi { background: #7f8c8d; color: #fff; }
        .btn-ulangi:hover { background: #95a5a6; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if ('speechSynthesis' in window) {
                window.addEventListener('speakQueue', event => {
                    const nomorAntrian = event.detail.nomorAntrian;
                    const namaPasien = event.detail.namaPasien;
                    let text = '';
                    if (nomorAntrian && namaPasien) {
                        text = `Nomor antrian ${nomorAntrian}, atas nama ${namaPasien}, silakan ke farmasi`;
                    } else if (nomorAntrian) {
                        text = `Nomor antrian ${nomorAntrian}, silakan ke farmasi`;
                    }
                    if (text) {
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'id-ID';
                        speechSynthesis.speak(utterance);
                    }
                });
            }
        });
    </script>

    <button wire:click="setFilterRacik(null)">Semua</button>
    <button wire:click="setFilterRacik('racik')">Racik</button>
    <button wire:click="setFilterRacik('non_racik')">Non Racik</button>
</div>