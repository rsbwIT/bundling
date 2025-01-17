<div>
    @php
        $nik = session('auth')['id_user'];
        $user = session('user')->nama;
    @endphp
    @push('styles')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
    <div class="card-header">
        <form wire:submit.prevent="getDataKhanza">
            <div class="row">
                <div class="col-lg-3">
                    <div class="input-group">
                        <input class="form-control form-control-sidebar form-control-sm" type="text" aria-label="Search"
                            placeholder="Cari Sep / Rm / No.Rawat" wire:model.defer="carinomor">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" class="form-control form-control-sidebar form-control-sm"
                        wire:model.defer="tanggal1">
                </div>
                <div class="col-lg-2">
                    <input type="date" class="form-control form-control-sidebar form-control-sm"
                        wire:model.defer="tanggal2">
                </div>
                <div class="col-lg-2">
                    <div class="input-group">
                        <select class="form-control form-control-sidebar form-control-sm"
                            wire:model.lazy="status_lanjut">
                            <option value="Ranap">Ranap</option>
                            <option value="Ralan">Ralan</option>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-sidebar btn-primary btn-sm" wire:click="render()">
                                <i class="fas fa-search fa-fw"></i>
                                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                    wire:loading wire:target="getDataKhanza"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 text-right">
                    @if (session()->has('response200'))
                        <span class="text-success"><i class="icon fas fa-check"> </i>
                            {{ session('response200') }} </span>
                    @endif
                    @if (session()->has('response500'))
                        <span class="text-danger"><i class="icon fas fa-ban"> </i> {{ session('response500') }}
                        </span>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <div class="card-body table-responsive p-0" style="height: 450px;">
        <table class="table text-nowrap table-sm table-bordered table-hover table-head-fixed p-3 text-sm"
            style="white-space: nowrap;">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>No. Rawat</th>
                    <th>Pasien</th>
                    <th>Tanggal Lahir</th>
                    <th>S.Lanjut</th>
                    <th>Penjab</th>
                    <th>Dokter</th>
                    <th>dr_perujuk</th>
                    <th>Poli</th>
                    <th>Tgl Permintaan</th>
                    <th>Act</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($getDatakhanza as $key => $data)
                    <tr>
                        <td>
                            <div class="d-flex justify-content-between">
                                {{ $data->noorder }} &nbsp;
                                <div class="badge-group-sm float-right">
                                    <a data-toggle="dropdown" href="#"><i class="fas fa-eye"></i></a>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        @foreach ($data->Permintaan as $item)
                                            <div class="dropdown-item">
                                                {{ $item->nm_perawatan }} - ( {{ $item->kd_jenis_prw }})
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $data->no_rawat }}</td>
                        <td>{{ $data->nm_pasien }} - {{ $data->no_rkm_medis }} - ({{ $data->jk }})</td>
                        <td>{{ $data->tgl_lahir }}</td>
                        <td>{{ $data->status_lanjut }}</td>
                        <td>{{ $data->png_jawab }}</td>
                        <td>{{ $data->nm_dokter }}</td>
                        <td>{{ $data->dr_perujuk }}</td>
                        <td>{{ $data->nm_poli }}</td>
                        <td>{{ $data->tgl_permintaan }}</td>
                        <td>
                            <button id="dropdownSubMenu1{{ $key }}" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"
                                class="btn btn-default btn-sm dropdown-toggle dropdown dropdown-hover py-0"></button>
                            <div>
                                <ul aria-labelledby="dropdownSubMenu1{{ $key }}"
                                    class="dropdown-menu border-0 shadow">
                                    <li><button class="dropdown-item" data-toggle="modal"
                                            data-target="#KirimDataLIS{{ $key }}">Kirim ke SOFTMEDIX</a>
                                    </li>
                                    <li><button class="dropdown-item"
                                            wire:click="getDataLIS('{{ $data->noorder }}', '{{ $data->kd_dokter }}', '{{ $data->nm_dokter }}', '{{ $nik }}', '{{ $user }}')"
                                            data-toggle="modal" data-target="#DetailDataLIS{{ $key }}">Tarik
                                            Data Sotfmedix</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @foreach ($getDatakhanza as $key => $item)
            {{-- MODAL KIRIM --}}
            <div class="modal fade" id="KirimDataLIS{{ $key }}" tabindex="-1" role="dialog"
                aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title">Order Data SOFTMEDIX LIS
                                <b>{{ $data->nm_pasien }}</b>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Action
                                        </label>
                                        <select class="form-control"
                                            wire:model.defer="getDatakhanza.{{ $key }}.order_control">
                                            <option value="D">Delete</option>
                                            <option value="U">Update</option>
                                            <option value="N">Baru</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Cito
                                        </label>
                                        <select class="form-control"
                                            wire:model.defer="getDatakhanza.{{ $key }}.cito">
                                            <option value="Y">Cito</option>
                                            <option value="N">Tidak Cito</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Sembunyikan Nama
                                        </label>
                                        <select class="form-control"
                                            wire:model.defer="getDatakhanza.{{ $key }}.med_legal">
                                            <option value="Y">Ya</option>
                                            <option value="N">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Catan 1
                                        </label>
                                        <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve1"></textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Catan 2
                                        </label>
                                        <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve2"></textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Catan 3
                                        </label>
                                        <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve3"></textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Catan 4
                                        </label>
                                        <textarea type="text" class="form-control" wire:model.defer="getDatakhanza.{{ $key }}.reserve4"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-primary"
                                wire:click="sendDataToLIS('{{ $key }}')" data-dismiss="modal">Kirim</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- MODAL DETAIL --}}
            <div class="modal fade" id="DetailDataLIS{{ $key }}" tabindex="-1" role="dialog"
                aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title">Detail Data LIS
                            </h6>
                        </div>
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="card py-3  d-flex justify-content-center align-items-center">
                                    <span class="spinner-grow spinner-grow" role="status" aria-hidden="true"
                                        wire:loading></span>
                                    @if ($detailDataLis)
                                        @if ($detailDataLis['response']['code'] == '200')
                                            <table border="0px" width="1000px">
                                                <tr>
                                                    <td rowspan="4"> <img
                                                            src="data:image/png;base64,{{ base64_encode($Setting['logo']) }}"
                                                            alt="Girl in a jacket" width="80" height="80">
                                                    </td>
                                                    <td class="text-center">
                                                        <h4>{{ $Setting['nama_instansi'] }} </h4>
                                                    </td>
                                                    <td rowspan="4" class="px-4">
                                                    </td>
                                                </tr>
                                                <tr class="text-center">
                                                    <td>{{ $Setting['alamat_instansi'] }} ,
                                                        {{ $Setting['kabupaten'] }},
                                                        {{ $Setting['propinsi'] }}
                                                        {{ $Setting['kontak'] }}</td>
                                                </tr>
                                                <tr class="text-center">
                                                    <td> E-mail : {{ $Setting['email'] }}</td>
                                                </tr>
                                                <tr class="text-center">
                                                    <td colspan="">
                                                        <h5 class="mt-2">HASIL PEMERIKSAAN LABORATORIUM </h5>
                                                    </td>
                                                </tr>
                                            </table>
                                            <table border="1px" width="1000px">
                                                <tr style="vertical-align: top;">
                                                    <td width="130px">No.RM</td>
                                                    <td width="300px">:
                                                        {{ $detailDataLis['response']['sampel']['pid'] }}</td>
                                                    <td width="130px">No.Rawat </td>
                                                    <td width="200px">: </td>
                                                </tr>
                                                <tr style="vertical-align: top;">
                                                    <td width="130px">Nama Pasien</td>
                                                    <td width="300px">:
                                                        {{ $detailDataLis['response']['sampel']['pname'] }}</td>

                                                    <td width="130px">Tgl. Periksa </td>
                                                    <td width="200px">:
                                                        {{ date('d-m-Y', strtotime($detailDataLis['response']['sampel']['acc_date'])) }}
                                                    </td>
                                                </tr>
                                                <tr style="vertical-align: top;">
                                                    <td width="130px">JK/Umur </td>
                                                    <td width="300px">:
                                                        {{ $detailDataLis['response']['sampel']['sex'] }} /
                                                        {{ $detailDataLis['response']['sampel']['birth_dt'] }}
                                                    </td>

                                                    <td width="130px">Jam Periksa </td>
                                                    <td width="200px">: </td>
                                                    </td>
                                                </tr>

                                                <tr style="vertical-align: top;">
                                                    <td width="130px">Alamat </td>
                                                    <td width="300px">:</td>
                                                    <td width="130px">Kamar/Poli </td>
                                                    <td width="200px">:
                                                        {{ $detailDataLis['response']['sampel']['bangsal_name'] }}</td>
                                                </tr>
                                                <tr style="vertical-align: top;">
                                                    <td width="130px"> Dokter Pengirim </td>
                                                    <td width="300px">:
                                                        {{ $detailDataLis['response']['sampel']['clinician_name'] }}
                                                    </td>
                                                    <td width="130px">Penerima </td>
                                                    <td width="200px">
                                                        <div class="form-group dropdown m-0 p-0"
                                                            x-data="{ open: false }">
                                                            <button
                                                                class="btn btn-default btn-block btn-sm dropdown dropdown-hover"
                                                                data-bs-auto-close="outside"
                                                                id="dokterMenu{{ $key }}"
                                                                aria-expanded="true"
                                                                @click="open = ! open; $nextTick(() => $refs.cariDokter.focus());">
                                                                <span
                                                                    class="float-left">{{ $set_dokter_penerima }}</span>
                                                                <span class="float-right">
                                                                    <i class="fas fa-angle-down"></i>
                                                                </span>
                                                            </button>
                                                            <div x-show="open" x-transition>
                                                                <ul aria-labelledby="dokterMenu{{ $key }}"
                                                                    style="width: 100%;background-color: rgb(230, 230, 230);"
                                                                    class="dropdown-menu border-0 shadow p-2 show">
                                                                    <li>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            x-ref="cariDokter"
                                                                            wire:model='cariDokter'>
                                                                    </li>
                                                                    @if ($getDokter)
                                                                        @if ($getDokter->IsEmpty())
                                                                            <li wire:loading.remove
                                                                                wire:target="cariDokter">
                                                                                Tidak Tersedia
                                                                            </li>
                                                                        @else
                                                                            <li wire:loading.remove
                                                                                style="margin-top: 10px; max-height: 200px; overflow-y: auto; padding: 5px; border: 1px solid #7ab8fb; border-radius: 5px;">
                                                                                @foreach ($getDokter as $item)
                                                                                    <div @click="open = ! open">
                                                                                        <button class="dropdown-item"
                                                                                            wire:click='setDokterPenerima("{{ $item->kd_dokter }}", "{{ $item->nm_dokter }}")'>{{ $item->nm_dokter }}</button>
                                                                                    </div>
                                                                                @endforeach
                                                                            </li>
                                                                        @endif
                                                                    @endif
                                                                    <li wire:loading wire:target="cariDokter">
                                                                        <button class="dropdown-item">
                                                                            Processing Payment...
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr style="vertical-align: top;">
                                                    <td width="130px">Petugas </td>
                                                    <td width="300px">
                                                        <div class="form-group dropdown m-0 p-0 col-8"
                                                            x-data="{ open: false }">
                                                            <button
                                                                class="btn btn-default btn-block btn-sm dropdown dropdown-hover"
                                                                data-bs-auto-close="outside"
                                                                id="CariPetugas{{ $key }}"
                                                                aria-expanded="true"
                                                                @click="open = ! open; $nextTick(() => $refs.cariPetugas.focus());">
                                                                <span
                                                                    class="float-left">{{ $set_nama_petugas }}</span>
                                                                <span class="float-right">
                                                                    <i class="fas fa-angle-down"></i>
                                                                </span>
                                                            </button>
                                                            <div x-show="open" x-transition>
                                                                <ul aria-labelledby="CariPetugas{{ $key }}"
                                                                    style="width: 100%;background-color: rgb(230, 230, 230);"
                                                                    class="dropdown-menu border-0 shadow p-2 show">
                                                                    <li>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            x-ref="cariPetugas"
                                                                            wire:model='cariPetugas'>
                                                                    </li>
                                                                    @if ($getPetugas)
                                                                        @if ($getPetugas->IsEmpty())
                                                                            <li wire:loading.remove
                                                                                wire:target="cariPetugas">
                                                                                Tidak Tersedia
                                                                            </li>
                                                                        @else
                                                                            <li wire:loading.remove
                                                                                style="margin-top: 10px; max-height: 200px; overflow-y: auto; padding: 5px; border: 1px solid #7ab8fb; border-radius: 5px;">
                                                                                @foreach ($getPetugas as $item)
                                                                                    <div @click="open = ! open">
                                                                                        <button class="dropdown-item"
                                                                                            wire:click='setPetugasPenerima("{{ $item->nip }}", "{{ $item->nama }}")'>{{ $item->nama }}</button>
                                                                                    </div>
                                                                                @endforeach
                                                                            </li>
                                                                        @endif
                                                                    @endif
                                                                    <li wire:loading wire:target="cariPetugas">
                                                                        <button class="dropdown-item">
                                                                            Processing Payment...
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td width="130px"></td>
                                                    <td width="200px"></td>
                                                </tr>
                                            </table>
                                            <table border="1px" width="1000px" class="mt-2">
                                                <tr>
                                                    <th>test_id</th>
                                                    <th>nama_test</th>
                                                    <th>id_template(BW)</th>
                                                    {{-- <th>nama_test(Bw)</th> --}}
                                                    <th>jenis_hasil</th>
                                                    <th>hasil</th>
                                                    <th>satuan</th>
                                                    <th>nilai_normal</th>
                                                    <th>flag</th>
                                                    <th>kode_paket</th>
                                                    <th>reserve4</th>
                                                </tr>
                                                @php
                                                    $uniqueTests = [];
                                                @endphp

                                                @foreach ($detailDataLis['response']['sampel']['result_test'] as $check => $item)
                                                    @if (!in_array($item['kode_paket'], $uniqueTests))
                                                        <tr>
                                                            <td>{{ $item['test_id'] }}</td>
                                                            <td>{{ $item['nama_test'] }}</td>
                                                            <td>{{ $item['id_template'] }}</td>
                                                            {{-- <td>{{ $item['Pemeriksaan'] }}</td> --}}
                                                            <td>{{ $item['jenis_hasil'] }}</td>
                                                            <td>{{ $item['hasil'] }}</td>
                                                            <td>{{ $item['satuan'] }}</td>
                                                            <td>{{ $item['nilai_normal'] }}</td>
                                                            <td>{{ $item['flag'] }}</td>
                                                            <td>{{ $item['kode_paket'] }}</td>
                                                            <td>{{ $item['reserve4'] }}</td>
                                                        </tr>
                                                        @php
                                                            $uniqueTests[] = $item['kode_paket'];
                                                        @endphp
                                                    @endif
                                                @endforeach

                                            </table>
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="button"
                                                        class="btn btn-primary btn-lg btn-block mt-2"
                                                        wire:click='getTestLAB("{{ $key }}")'>getTestLab
                                                        <span class="spinner-grow spinner-grow-sm" role="status"
                                                            aria-hidden="true" wire:loading
                                                            wire:target='getTestLAB("{{ $key }}")'></span>
                                                    </button>
                                                </div>
                                            </div>
                                            @if ($testok == 'ok')
                                                <br>
                                                <h3 class="text-success">Tersimpan</h3>
                                            @elseif($testok == 'gagal')
                                                <br>
                                                <h3 class="text-danger">Gagall</h3>
                                            @endif
                                            <table border="0px" width="1000px" class="mt-2">
                                                <tr>
                                                    <td class="text-xs"><b>Catatan :</b> Jika ada keragu-raguan
                                                        pemeriksaan,
                                                        diharapkan
                                                        segera menghubungi laboratorium</td>
                                                </tr>
                                            </table>
                                            <table border="0px" width="1000px">
                                                <tr>
                                                    <td width="250px" class="text-center">
                                                        Penanggung Jawab
                                                        <div class="barcode mt-1">
                                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $Setting['nama_instansi'] . ', Kabupaten/Kota ' . $Setting['kabupaten'] . ' Ditandatangani secara elektronik oleh ' . $detailDataLis['response']['sampel']['clinician_name'] . ' ID ' . 'Kode Dokter' . ' ' . $detailDataLis['response']['sampel']['order_lab'], 'QRCODE') }}"
                                                                alt="barcode" width="80px" height="75px" />
                                                        </div>
                                                        {{-- {{ $periksa->nm_dokter }} --}}
                                                    </td>
                                                    <td width="150px"></td>
                                                    <td width="250px" class="text-center">
                                                        Hasil :
                                                        {{ date('d-m-Y', strtotime($detailDataLis['response']['sampel']['order_lab'])) }}
                                                        <br>
                                                        Petugas Laboratorium
                                                        <div class="barcode mt-1">
                                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $Setting['nama_instansi'] . ', Kabupaten/Kota ' . $Setting['kabupaten'] . ' Ditandatangani secara elektronik oleh ' . $detailDataLis['response']['sampel']['acc_by'] . ' ID ' . 'PETUGAS' . ' ' . $detailDataLis['response']['sampel']['order_lab'], 'QRCODE') }}"
                                                                alt="barcode" width="80px" height="75px" />
                                                        </div>
                                                        {{ $detailDataLis['response']['sampel']['acc_by'] }}
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
    </div>
</div>
