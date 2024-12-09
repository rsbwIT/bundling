<div>
    <div class="mt-4 container-fluid">
        <div class="row justify-content-center" wire:poll.1000ms>
            @foreach ($getPoli as $item)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header text-center bg-success">
                            <h2 class="my-0"><a class="link " href="">
                                    <h1 class="font-weight-bold text-white">{{ $item->nama_ruang_poli }}</h1>
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
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const kdRuangPoli = urlParams.get('kd_display');
        var pusher = new Pusher('f8c2a21c58f812f99944', {
            cluster: 'ap1'
        });
        var channel = pusher.subscribe('messages' +kdRuangPoli);
        channel.bind('message', function(data) {
            console.log(data['message']['kd_dokter']);
            console.log(data['message']['no_reg']);

            function playAudioSequentially(audioFiles, index = 0) {
                if (index < audioFiles.length) {
                    var audio = new Audio(audioFiles[index]);
                    audio.play();
                    audio.onended = function() {
                        playAudioSequentially(audioFiles, index +
                            1);
                    };
                }
            }
            const audioFiles = [
                '/sound/noreg/'+data['message']['no_reg']+'.mp3',
                '/sound/dokter/'+data['message']['kd_dokter']+'.mp3',
            ];
            playAudioSequentially(audioFiles);
        });
    </script>
@endpush
