<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="getListPasienRalan">
                <div class="row">

                    <!-- 🔍 PENCARIAN BIASA -->
                    <div class="col-lg-3">
                        <input class="form-control form-control-sm"
                            type="text"
                            placeholder="Cari Nama / RM / No.Rawat"
                            wire:model.defer="carinomor">
                    </div>

                    <!-- 🚀 INPUT MULTI SEP -->
                    <div class="col-lg-3">
                        <input type="text"
                            class="form-control form-control-sm"
                            placeholder="Ketik SEP lalu Enter"
                            wire:keydown.enter.prevent="addSep($event.target.value)"
                            id="inputSep">

                        <!-- TAG LIST -->
                        <div class="mt-1">
                            @foreach ($sepList as $sep)
                                <span class="badge badge-primary mr-1">
                                    {{ $sep }}
                                    <a href="#"
                                        wire:click.prevent="removeSep('{{ $sep }}')"
                                        class="text-white ml-1">×</a>
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- STATUS -->
                    <div class="col-lg-2">
                        <select class="form-control form-control-sm" wire:model.defer="status_lanjut">
                            <option value="Ralan">Rawat Jalan</option>
                            <option value="Ranap">Rawat Inap</option>
                        </select>
                    </div>

                    <!-- TANGGAL -->
                    <div class="col-lg-2">
                        <input type="date" class="form-control form-control-sm"
                            wire:model.defer="tanggal1">
                    </div>

                    <div class="col-lg-2">
                        <div class="input-group">
                            <input type="date" class="form-control form-control-sm"
                                wire:model.defer="tanggal2">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- COUNT -->
                    <div class="col-lg-12 text-right mt-2">
                        {{ $getPasien->count() }} Pasien {{$status_lanjut}}
                    </div>

                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="card-body table-responsive p-0" style="height: 450px;">
            <table class="table table-sm table-bordered table-hover table-head-fixed text-sm">
                <thead>
                    <tr>
                        <th>Pasien</th>
                        <th>RM</th>
                        <th>No.Rawat</th>
                        <th>No.Sep</th>
                        <th>Poli</th>
                        <th class="text-center">Act</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getPasien as $item)
                        <tr>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_sep }}</td>
                            <td>{{ $item->nm_poli }}</td>

                            <td width="120px">
                                <div class="d-flex justify-content-between">

                                    <!-- KHANZA -->
                                    <div class="btn-group">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-xs dropdown-toggle"
                                            data-toggle="dropdown">
                                            Khanza
                                        </button>

                                        <div class="dropdown-menu">
                                            <button type="button"
                                                class="dropdown-item"
                                                wire:click="SimpanResep('{{ $item->no_rawat }}','{{ $item->no_sep }}')">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>

                                            <form action="{{ url('/view-sep-resep2') }}">
                                                @csrf
                                                <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                                <input name="cariNoSep" value="{{ $item->no_sep }}" hidden>

                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- DOWNLOAD -->
                                    <div class="btn-group">
                                        @if($item->fileFarmasi)
                                            <a href="{{ url('storage/resep_sep_farmasi/' . $item->fileFarmasi->file) }}"
                                                download
                                                class="btn btn-outline-success btn-xs">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="btn btn-outline-dark btn-xs">
                                                <i class="fas fa-ban"></i>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 🔥 CLEAR INPUT OTOMATIS -->
<script>
    window.addEventListener('clear-input', () => {
        document.getElementById('inputSep').value = '';
    });
</script>