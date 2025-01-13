<div>
    {{-- 1. CARI RM --}}
    @if ($showItem == 1)
        <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-12">
                <div class="row justify-content-center">
                    <div class="col-3">
                        <h2 class="text-center display-5">Cari Nomor</h2>
                        <form wire:submit.prevent="setPasien">
                            <div class="input-group">
                                <input type="search"
                                    class="form-control form-control-lg {{ session()->has('message') || session()->has('sudahada') || session()->has('pasienBelum') ? 'is-invalid' : '' }}"
                                    placeholder="No.Rm / No.Ktp / No.Telp" wire:model.lazy='cariKode' id="inputField">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-lg btn-default">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                @if (session()->has('sudahada'))
                                    <div class="invalid-feedback">
                                        {{ session('sudahada') }}
                                    </div>
                                @endif
                                @if (session()->has('pasienBelum'))
                                    <div class="invalid-feedback">
                                        {{ session('pasienBelum') }}
                                    </div>
                                @endif
                                @if (session()->has('message'))
                                    <div class="invalid-feedback">
                                        {{ session('message') }}
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row justify-content-center">
                        <div class="col-3">
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
                                    <button type="button" class="btn btn-outline-danger btn-lg px-4"
                                        onclick="clearInput()">C</button>
                                    <button type="button" class="btn btn-outline-primary btn-lg px-5"
                                        onclick="addToInput('0')">0</button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4"
                                        onclick="deleteLast()">←</button>
                                </div>
                            </div>
                        </div>
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

    {{-- 2.PILIH POLI --}}
    @if ($showItem == 2)
        <div class="container">
            <div style="position: sticky; top: 0;padding: 20px;  z-index: 100;">
                <button style="position: fixed; top: 20px; right: 20px; z-index: 1000;" type="button"
                    class="btn btn-danger" wire:click='ResertShow("{{ 1 }}")'>X</button>
            </div>
            <div class="row justify-content-center">
                <div class="col-8">
                    <h4 class="text-center display-5">Pilih Poliklinik</h4>
                    <div class="row">
                        <div class="col-8">
                            @if ($getpasien)
                                <table>
                                    @foreach ($getpasien as $item)
                                        <tr>
                                            <td>Nama</td>
                                            <td> : {{ $item->nm_pasien }}</td>
                                        </tr>
                                        <tr>
                                            <td>No.Rm</td>
                                            <td> : {{ $item->no_rkm_medis }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($getPoli as $item)
                            <div class="col-3 m-0" style="cursor: pointer;"
                                wire:click='setDokter("{{ $item->kd_poli }}","{{ $item->nm_poli }}")'>
                                <div class="card card-primary card-outline m-0">
                                    <div class="card-body box-profile">
                                        <h6 class="text-center">{{ $item->nm_poli }}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 3. PILIH DOKTER  --}}
    @if ($showItem == 3)
        @if ($getDokter)
            <div class="container">
                <div style="position: sticky; top: 0;padding: 20px;  z-index: 100;">
                    <button style="position: fixed; top: 20px; right: 20px; z-index: 1000;" type="button"
                        class="btn btn-danger" wire:click='ResertShow("{{ 2 }}")'>X</button>
                </div>
                {{-- <div class="col-12"> --}}
                    <div class="row justify-content-center">
                        <div class="col-6">
                            <h4 class="text-center display-5">Pilih Dokter</h4>
                            @php
                                $center = count($getDokter) > 1 ? '' : 'justify-content-center';
                            @endphp
                            <div class="row">
                                <div class="col-12">
                                    @if ($getpasien)
                                        <table>
                                            @foreach ($getpasien as $item)
                                                <tr>
                                                    <td>Nama</td>
                                                    <td> : {{ $item->nm_pasien }}</td>
                                                </tr>
                                                <tr>
                                                    <td>No.Rm</td>
                                                    <td> : {{ $item->no_rkm_medis }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endif
                                </div>
                            </div>
                            <div class="row {{ $center }}">
                                @foreach ($getDokter as $dokter)
                                    <div class="col-6 m-0">
                                        <div class="card card-primary card-outline m-0">
                                            @if ($dokter->terdaftar >= $dokter->total_kuota)
                                                <div class="ribbon-wrapper">
                                                    <div class="ribbon bg-warning">
                                                        Penuh
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="card-body box-profile">
                                                <div class="text-center">
                                                    <img class="img-circle elevation-1 mb-2"
                                                        src="{{ $dokter->foto ? url('/storage/foto_dokter/' . $dokter->foto) : '/img/user.jpg' }}"
                                                        alt="User profile picture"
                                                        style="width: 90px; height: 90px; object-fit: cover;">

                                                </div>
                                                <div class="text-center mb-1 font-weight-bold">
                                                    {{ $dokter->nm_dokter }}
                                                </div>
                                                <div class="text-center">
                                                    {{ date('H:i', strtotime($dokter->jam_mulai)) }} -
                                                    {{ date('H:i', strtotime($dokter->jam_selesai)) }}
                                                </div>
                                                @php
                                                    if ($dokter->terdaftar >= $dokter->total_kuota) {
                                                        $disable = 'disabled';
                                                        $hidden = 'hidden';
                                                    } else {
                                                        $disable = '';
                                                        $hidden = '';
                                                    }
                                                @endphp
                                                Kuota <a class="float-right">{{ $dokter->kuota }} + {{ $dokter->kuota_tambahan }}</a>
                                                <hr class="my-1">
                                                <span {{ $hidden }}> Terdaftar<a
                                                        class="float-right">{{ $dokter->terdaftar }}</a>
                                                    <hr class="my-1">
                                                </span>
                                                <button class="btn btn-block btn-xs  btn-outline-primary mt-4"
                                                    wire:click='pilihDokter("{{ $dokter->kd_dokter }}","{{ $dokter->kd_poli }}","{{ $dokter->nm_dokter }}","{{ $dokter->nm_poli }}")'{{ $disable }}>
                                                    <b>Daftar</b>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                {{-- </div> --}}
            </div>
        @endif
    @endif

    {{-- 4. DAFTAR --}}
    @if ($showItem == 4)
        <div class="container">
            <div style="position: sticky; top: 0;padding: 20px;  z-index: 100;">
                <a href="{{ url('/anjungan-mandiri') }}"
                    style="position: fixed; top: 20px; left: 20px; z-index: 1000;" type="button"
                    class="btn btn-danger">
                    <i class='fas fa-home'></i>
                </a>
            </div>
            <div style="position: sticky; top: 0;padding: 20px;  z-index: 100;">
                <button style="position: fixed; top: 20px; right: 20px; z-index: 1000;" type="button"
                    class="btn btn-danger" wire:click='ResertShow("{{ 3 }}")'>X</button>
            </div>
            <h4 class="text-center display-5 mt-0">Form Daftar</h4>
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row justify-content-center">
                        <div class="col-5">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="img-circle elevation-1 mb-2" src="/img/user.jpg"
                                            alt="User profile picture" width="60px" height="60px">
                                    </div>
                                    <div class="text-center">{{ $getpasien[0]['nm_pasien'] }} /
                                        {{ $getpasien[0]['no_rkm_medis'] }}</div>
                                    <div>
                                        <hr class="mb-1 mt-3">
                                        No. Reg <a class="float-right">{{ $registrasi['no_reg'] }}</a>
                                        <hr class="my-1">
                                        No. Rawat <a class="float-right">{{ $registrasi['no_rawat'] }}</a>
                                        <hr class="my-1">
                                        Tgl. Reg <a class="float-right">{{ $registrasi['tgl_registrasi'] }}
                                            {{ $registrasi['jam_reg'] }}</a>
                                        <hr class="my-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="card card-primary card-outline" >
                                @if (session()->has('messageRegistrasi'))
                                    <div class="ribbon-wrapper">
                                        <div class="ribbon bg-success">
                                            {{ session('messageRegistrasi') }}
                                        </div>
                                    </div>
                                @endif
                                @if (session()->has('gagalRegistrasi'))
                                    <div class="ribbon-wrapper">
                                        <div class="ribbon bg-danger">
                                            {{ session('gagalRegistrasi') }}
                                        </div>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <form class="form-horizontal mt-0">
                                        <div class="form-group row" style="margin: 5px">
                                            <span for="inputName" class="col-4 "> Dituju</span>
                                            <div class="col-8">
                                                : {{ $registrasi['nm_dokter'] }}
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin: 5px">
                                            <span for="inputName" class="col-4">Unit/Poliklinik</span>
                                            <div class="col-8">
                                                : {{ $registrasi['nm_poli'] }}
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin: 5px">
                                            <span for="inputName2" class="col-4">Penanggung Jawab</span>
                                            <div class="col-8">
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model='namakeluarga' placeholder="Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin: 5px">
                                            <span for="inputSkills" class="col-4">Hubungan PJ</span>
                                            <div class="col-8">
                                                <select class="form-control form-control-sm" wire:model='keluarga'>
                                                    @foreach ($getKeluarga as $item)
                                                        <option value="{{ $item->keluarga }}">
                                                            {{ $item->keluarga }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin: 5px">
                                            <span for="inputName2" class="col-4">Alamat
                                            </span>
                                            <div class="col-8">
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model='alamat' placeholder="Name">
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin: 5px">
                                            <span for="inputSkills" class="col-4">Status
                                                Pasien</span>
                                            <div class="col-8">
                                                <select class="form-control form-control-sm" wire:model='stts_daftar'>
                                                    <option value="Baru">Baru</option>
                                                    <option value="Lama">Lama</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row" style="margin: 5px">
                                            <span for="inputSkills" class="col-4">Penjamin</span>
                                            <div class="col-8">
                                                <select class="form-control form-control-sm" wire:model='penjab'>
                                                    @foreach ($getPenjab as $item)
                                                        <option value="{{ $item->kd_pj }}">
                                                            {{ $item->png_jawab }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <button type="button" class="form-control btn btn-block btn-primary"
                                wire:click='Registrasi'><i class="fas fa-save"></i> Daftar</button>
                        </div>
                        <div class="col-6">
                            @php
                                if ($getRegistrasi) {
                                    $dislabelPrint = '';
                                } else {
                                    $dislabelPrint = 'disabled';
                                }
                            @endphp
                            <button type="button" class="form-control btn btn-block btn-warning" id="printButton"
                                {{ $dislabelPrint }}>
                                <i class="fas fa-print"></i> Cetak
                            </button>
                        </div>
                        @if ($getRegistrasi)
                            <script>
                                document.getElementById('printButton').addEventListener('click', function() {
                                    const noRawat = @json($getRegistrasi);
                                    const encodedNoRawat = encodeURIComponent(noRawat);
                                    const url = `/anjungan-mandiri-print/${encodedNoRawat}`;
                                    window.open(url, '_blank', 'width=800,height=600');
                                });
                            </script>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
