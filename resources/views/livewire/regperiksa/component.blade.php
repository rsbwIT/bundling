<div class="row">
    <div class="col-lg-12">
        <div style="height: 100vh">
            <div class="d-flex h-100 justify-content-center align-items-center">
                @if (!$showInputRm)
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="text-center display-4">Cari Nomor</h2>
                            <div class="input-group">
                                <input type="search" class="form-control form-control-lg "
                                    placeholder="No.Rm / No.Ktp / No.Telp" wire:model.lazy='cariKode' id="inputField">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-lg btn-default" wire:click='getpasien'>
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('1')">1</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('2')">2</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('3')">3</button>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('4')">4</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('5')">5</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('6')">6</button>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('7')">7</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('8')">8</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('9')">9</button>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('0')">0</button>
                                    <button type="button" class="btn btn-outline-danger btn-lg px-4"
                                        onclick="clearInput()">C</button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4"
                                        onclick="deleteLast()">←</button>
                                </div>
                            </div>
                        </div>
                        <script>
                            function addToInput(value) {
                                document.getElementById('inputField').value += value;
                                @this.set('cariKode', document.getElementById('inputField').value); // Sync with Livewire model
                            }

                            function clearInput() {
                                document.getElementById('inputField').value = '';
                                @this.set('cariKode', ''); // Clear Livewire model
                            }

                            function deleteLast() {
                                let currentValue = document.getElementById('inputField').value;
                                document.getElementById('inputField').value = currentValue.slice(0, -1);
                                @this.set('cariKode', document.getElementById('inputField').value); // Update Livewire model
                            }
                        </script>
                    </div>
                @endif
            </div>
        </div>
        {{-- 341527 --}}
        <div class="row">
            <div class="col-md-12">
                @if ($showInputPoli && !$getDokter)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Poliklinik/Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getPoli as $item)
                                <tr>
                                    <td><b>{{ $item->nm_poli }}</b></td>
                                    <td>
                                        <button type="button" class="btn btn-default" data-toggle="button"
                                            aria-pressed="false" autocomplete="off"
                                            wire:click='getDokter("{{ $item->kd_poli }}")'>
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        @if ($getDokter && !$showInputdokter)
            <table class="table">
                <thead>
                    <tr>
                        <th>Pilih Dokter</th>
                        <th><a href="{{ url('/anjungan-mandiri') }}">reset</a></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getDokter as $item)
                        <tr>
                            <td><b>{{ $item->nm_dokter }}</b></td>
                            <td>
                                <button type="button" class="btn btn-default" data-toggle="button" aria-pressed="false"
                                    autocomplete="off"
                                    wire:click='registrasiAnjungan("{{ $item->kd_dokter }}","{{ $item->kd_poli }}")'>
                                    Daftar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
