<div>
    <div class="mt-4 container-fluid">
        <div class="row justify-content-center" wire:poll.1000ms>
            @php
                $md = count($getLoket) > 2 ? 4 : 6;
            @endphp
            @if ($getLoket)
                @foreach ($getLoket as $item)
                    <div class="col-md-{{ $md }} mb-4">
                        <div class="card">
                            <div class="card-header text-center bg-success">
                                <h2 class="my-0"><a class="link " href="">
                                        <h1 class="font-weight-bold text-white">{{ $item->nama_loket }}</h1>
                                    </a></h2>
                            </div>
                            <table class="table font-weight-bold">
                                @if ($item->getPasien->isEmpty())
                                    <div class="container d-flex justify-content-center align-items-center"
                                        style="height: 300px">
                                        <h1 class="font-weight-bold">Tidak Ada Antrian</h1>
                                    </div>
                                @else
                                    @foreach ($item->getPasien as $item)
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="text-center">
                                                    <h3 class="font-weight-bold">Nomor Registrasi</h3>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-center display-2 font-weight-bold">
                                                    {{ $item->no_reg }}
                                                </th>
                                            </tr>
                                            <tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th colspan="3" class="text-center">
                                                    <h3 class="font-weight-bold">{{ $item->nm_pasien }}</h3>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-center">
                                                    <h4 class="font-weight-bold">{{ $item->nama_dokter }}</h4>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <h3 class="font-weight-bold">Jam Mulai :
                                                        {{ date('H:i', strtotime($item->jam_mulai)) }}</h3>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="height:300px;" class="d-flex align-items-center justify-content-center">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <strong> Koneksi terputus...</strong>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
