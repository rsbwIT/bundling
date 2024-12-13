<div>
    <div class="mt-1 ">
        @php
            $namakelas = [
                'NON' => '-',
                'VVP' => 'VVIP',
                'VIP' => 'VIP',
                'UTM' => 'UTAMA',
                'KL1' => 'KELAS I',
                'KL2' => 'KELAS II',
                'KL3' => 'KELAS III',
                'ICU' => 'ICU',
                'ICC' => 'ICCU',
                'NIC' => 'NICU',
                'PIC' => 'PICU',
                'IGD' => 'IGD',
                'UGD' => 'UGD',
                'SAL' => 'RUANG BERSALIN',
                'HCU' => 'HCU',
                'ISO' => 'RUANG ISOLASI',
            ];
        @endphp
        <div class="row mb-2">
            <div class="col-12 d-flex align-items-center justify-content-center ">
                @foreach ($getKelas as $item)
                @php
                    $active = in_array($item->kd_kelas_bpjs, $kd_kelas_bpjs) ? 'active' : '';
                    $all = empty($kd_kelas_bpjs) ? 'active' : '';
                @endphp
                    <button type="button" class="btn btn-outline-secondary mx-1 {{$active}}" wire:click="pilih('{{ $item->kd_kelas_bpjs }}')">
                        {{ $item->kd_kelas_bpjs }}
                    </button>
                    @endforeach
                    <button type="button" class="btn btn-outline-secondary mx-1 {{$all}}" wire:click="resetPaginate()">
                        All
                    </button>
            </div>

        </div>
        <div class="row" wire:poll.1000ms>
            {{-- <div class="col-4">
            </div> --}}
            {{-- <div class="col-4"> --}}
            @if ($getRuangan)
                @foreach ($getRuangan as $item)
                    {{-- <div class="row"> --}}
                    <div class="col-4">
                        <div class="shadow p-1 card rounded-md" style="border: 1px solid">
                            <h4>{{ $namakelas[$item->kd_kelas_bpjs] }}</h4>
                            @foreach ($item->getkamar as $kamar)
                                <div class="card m-2">
                                    <div class="row ">
                                        <div class="col-4 d-flex align-items-center justify-content-center"
                                            style="background-color: rgb(101, 194, 82)">
                                            <div class="m-0">
                                                <span class="text-bold">{{ $namakelas[$item->kd_kelas_bpjs] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div>
                                                <table border="0" class="my-2" width='100%'>
                                                    <tr class="p-2">
                                                        <td class="text-center text-bold " colspan="3">
                                                            {{ $kamar->nm_ruangan_bpjs }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width='10%'>
                                                            <i class="fas fa-hospital"></i>
                                                        </td>
                                                        <td width='70%'>Total Tempat Tidur</td>
                                                        <td width='20%'>{{ $kamar->kapasitas }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width='10%'>
                                                            <i class="fas fa-heartbeat"></i>
                                                        </td>
                                                        <td width='70%'>Tersedia</td>
                                                        <td width='20%'>{{ $kamar->tersedia }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width='10%'>
                                                            <i class="fas fa-mars"></i>
                                                        </td>
                                                        <td width='70%'>Pria</td>
                                                        <td width='20%'>{{ $kamar->tersedia_pria }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width='10%'>
                                                            <i class="fas fa-venus"></i>
                                                        </td>
                                                        <td width='70%'>Wanita</td>
                                                        <td width='20%'>{{ $kamar->tersedia_wanita }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width='10%'>
                                                            <i class="fas fa-venus"></i>
                                                        </td>
                                                        <td width='70%'>Pria & Wanita</td>
                                                        <td width='20%'>{{ $kamar->tersedia_pria_wanita }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- </div> --}}
                @endforeach
            @else
                <div style="height:300px;" class="d-flex align-items-center justify-content-center">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <strong> Koneksi terputus...</strong>
                    </div>
                </div>
            @endif
            {{-- </div> --}}
            {{-- <div class="col-4">
            </div> --}}
        </div>
    </div>
</div>
