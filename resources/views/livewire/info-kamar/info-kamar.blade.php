<div>
    <div class="mt-1 container-fluid">
        <div class="row justify-content-center" wire:poll.1000ms>
            @if ($getRuangan)
                @foreach ($getRuangan as $item)
                    <div class="col-4 p-1 mb-5">
                        <div class="card p-1 m-2" style="border: 1px solid; height: 100%">
                            <div class="text-center p-1  d-flex justify-content-between"
                                style="border: 1px solid">
                                <button class="btn btn-xs text-white ml-5"
                                    style="background-color: rgb(0, 26, 109); width: 30px;"><b>{{ $item->getKamarIsi }}</b>
                                </button>
                                <h3 class="font-weight-bold" style="color: rgb(2, 1, 10)">{{ $item->ruangan }}</h3>
                                <button class="btn btn-xs mr-5"
                                    style="background-color: rgb(255, 255, 255);  border: 1px solid; width: 30px;"><b>{{ $item->getKamarKosong }}</b>
                                </button>
                            </div>
                            <div class="row  text-center ">
                                @foreach ($item->getKamar as $kamar)
                                    <div class="col-md-4">
                                        <div class="card mt-2 mb-1" style="border: 1px solid;">
                                            <h6 class="m-0"><b>{{ $kamar->kamar }} </b> ({{ $kamar->kelas }})</h6>
                                            <hr class="m-0" style="border: 1px solid">
                                            <div class="row">
                                                @php
                                                    $bed = count($kamar->getBed);
                                                    $colom = $bed == 1 ? '12' : ($bed == 2 ? '6' : '4');
                                                @endphp

                                                @foreach ($kamar->getBed as $bed)
                                                    @php
                                                        $baground =
                                                            $bed->status == 1
                                                                ? 'rgb(0, 26, 109)'
                                                                : 'rgb(255, 255, 255)';
                                                        $text = $bed->status == 1 ? 'text-white' : 'text-black';
                                                    @endphp
                                                    <div class="col-md-{{ $colom }}">
                                                        <div class="card m-1 justify-content-center"
                                                            style="background-color: {{ $baground }}; border:1px solid;">
                                                            <span class="badge {{ $text }} badge-xs">
                                                                <b>{{ substr($bed->bad, strlen($bed->bad) - 1, 1) }}</b>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <br>
                        <br>
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
