<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getListPasienRanap">
                <div class="row">
                    <div class="col-lg-3">
                        <input class="form-control form-control-sidebar form-control-sm" type="text" aria-label="Search"
                            placeholder="Cari Sep / Rm / No.Rawat" wire:model.defer="carinomor">

                    </div>
                    <div class="col-lg-2">
                        <select class="form-control form-control-sidebar form-control-sm" wire:model="status_pulang">
                            <option value="blm_pulang">Belum Pulang</option>
                            <option value="tgl_masuk">Tanggal Masuk</option>
                            <option value="tgl_keluar">Pulang</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <input type="date" class="form-control form-control-sidebar form-control-sm"
                            wire:model.defer="tanggal1">
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group">
                            <input type="date" class="form-control form-control-sidebar form-control-sm"
                                wire:model.defer="tanggal2">
                            <div class="input-group-append">
                                <button class="btn btn-sidebar btn-primary btn-sm" wire:click="render()">
                                    <i class="fas fa-search fa-fw"></i>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                        wire:loading wire:target="getListPasienRanap"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <span class="btn btn-default btn-sm float-right" id="copyButton">
                            <i class='fas fa-copy'></i>
                        </span>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
            <table class="table table-sm table-hover table-bordered  table-responsive text-xs mb-3"
                style="white-space: nowrap;" id="tableToCopy">
                <thead>
                    <tr>
                        <th>RM</th>
                        <th>No.Rawat</th>
                        <th>Pasien</th>
                        <th>Tgl.Masuk</th>
                        <th>Tgl.Keluar</th>
                        <th>CPPT Dokter</th>
                        <th>CPPT Terakhir</th>
                        <th>Tanggal Nota</th>
                        <th>Kamar</th>
                        <th>WTB (CPPT Dokter)</th>
                        <th>WTB (CPPT Terakhir)</th>
                        <th>WTB (Set Pulang)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $key => $item)
                        <tr wire:key='{{ $key }}'>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td style="background-color: rgb(211, 233, 253)">{{ $item->tgl_masuk }}
                                {{ $item->jam_masuk }} </td>
                            <td style="background-color: rgb(255, 206, 206)">{{ $item->tgl_keluar }}
                                {{ $item->jam_keluar }}</td>
                            <td style="background-color: rgb(255, 248, 206)">
                                @foreach ($item->waktu_tunggu as $waktu)
                                    {{ $waktu->tgl_perawatan }} {{ $waktu->jam_rawat }}
                                @endforeach
                            </td>
                            <td style="background-color: rgb(255, 206, 248)">
                                @foreach ($item->waktu_tunggu_cppt_terakhir as $waktu)
                                    {{ $waktu->tgl_perawatan }} {{ $waktu->jam_rawat }}
                                @endforeach
                            </td>
                            <td style="background-color: rgb(216, 255, 207)">
                                @foreach ($item->waktu_tunggu as $waktu)
                                    {{ $waktu->tanggal_nota }} {{ $waktu->jam_nota }}
                                @endforeach
                            </td>
                            <td>{{ $item->nm_bangsal }} {{ $item->kd_kamar }}</td>
                            <td>
                                @foreach ($item->waktu_tunggu as $waktu)
                                    {{ $waktu->time_difference_cppt }}
                                @endforeach
                            </td>
                            <td>
                                @foreach ($item->waktu_tunggu_cppt_terakhir as $waktu)
                                    {{ $waktu->time_difference_cppt_terakhir }}
                                @endforeach
                            </td>
                            <td>
                                @foreach ($item->waktu_tunggu as $waktu)
                                    {{ $waktu->time_difference_set_pulang }}
                                @endforeach
                            </td>
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
</div>
