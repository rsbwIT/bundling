<div>
    <div class="row justify-content-center">
        <div class="d-flex item-center mt-3">
            <button class="p-3 mr-2 btn" style="background-color: rgb(0, 26, 109)"></button>
            <h4 class="mr-4"> : Terisi</h4>
            <button class="p-3 mr-2 btn" style="background-color: rgb(255, 255, 255); border: 1px solid"></button>
            <h4 class="mr-4"> : Kosong</h4>
        </div>
    </div>
    <div class="mt-4 container-fluid">
        <div class="row justify-content-center" wire:poll.1000ms>
            @if ($getRuangan)
                @foreach ($getRuangan as $item)
                    <div class="col-3 p-1">
                        <div class="card p-1" style="border: 1px solid; height: 100%">
                            <div class="card-header text-center p-1" style="border: 1px solid">
                                <h2 class="font-weight-bold" style="color: rgb(2, 1, 10)">{{ $item->ruangan }}</h2>
                            </div>
                            <div class="row  text-center">
                                @foreach ($item->getKamar as $kamar)
                                    <div class="col-md-6">
                                        <div class="card mt-2 mb-2" style="border: 1px solid">
                                            <h6 class="mb-"><b>{{ $kamar->kamar }} </b> ({{ $kamar->kelas }})</h6>
                                            <hr class="m-1" style="border: 1px solid">
                                            <div class="row">
                                                @php
                                                    $bed = count($kamar->getBed);
                                                    if ($bed == 1) {
                                                        $colom = '12';
                                                    } elseif ($bed == 2) {
                                                        $colom = '6';
                                                    } else {
                                                        $colom = '4';
                                                    }
                                                @endphp
                                                @foreach ($kamar->getBed as $bed)
                                                    @php
                                                        if ($bed->status == 1) {
                                                            $baground = 'rgb(0, 26, 109)';
                                                            $text = 'text-white';
                                                        } else {
                                                            $baground = 'rgb(255, 255, 255)';
                                                            $text = 'text-black';
                                                        }
                                                    @endphp
                                                    <div class="col-md-{{ $colom }}">
                                                        <div class="card m-1 justify-content-center"
                                                            style="background-color: {{ $baground }}; border:1px solid;">
                                                            <button class="btn {{ $text }}">
                                                                <b>{{ substr($bed->bad, strlen($bed->bad) - 1, 1) }}</b>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
