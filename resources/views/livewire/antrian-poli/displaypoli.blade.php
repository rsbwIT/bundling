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
        const pusherKey = @json($pusher_key);
        var pusher = new Pusher(pusherKey, {
            cluster: 'ap1'
        });
        var channel = pusher.subscribe('messages' + kdRuangPoli);
        channel.bind('message', function(data) {
            console.log(data['message']['no_reg']);

            // MANUAL SPEACH
            // function playAudioSequentially(audioFiles, index = 0) {
            //     if (index < audioFiles.length) {
            //         var audio = new Audio(audioFiles[index]);
            //         audio.play();
            //         audio.onended = function() {
            //             playAudioSequentially(audioFiles, index +
            //                 1);
            //         };
            //     }
            // }
            // const audioFiles = [
            //     '/sound/noreg/' + data['message']['no_reg'] + '.mp3',
            //     '/sound/dokter/' + data['message']['kd_dokter'] + '.mp3',
            // ];
            // playAudioSequentially(audioFiles);

            // TEXT TO SPEACH
            function numberToText(number) {
                const ones = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan"];
                const teens = ["sepuluh", "sebelas", "dua belas", "tiga belas", "empat belas", "lima belas",
                    "enam belas", "tujuh belas", "delapan belas", "sembilan belas"
                ];
                const tens = ["", "puluh", "dua puluh", "tiga puluh", "empat puluh", "lima puluh", "enam puluh",
                    "tujuh puluh", "delapan puluh", "sembilan puluh"
                ];
                const hundreds = ["", "seratus", "dua ratus", "tiga ratus", "empat ratus", "lima ratus",
                    "enam ratus", "tujuh ratus", "delapan ratus", "sembilan ratus"
                ];

                number = parseInt(number); // Menghilangkan awalan nol dan mengonversi ke integer

                if (number < 10) return ones[number];
                if (number < 20) return teens[number - 10];
                if (number < 100) return tens[Math.floor(number / 10)] + (number % 10 ? " " + ones[number % 10] :
                    "");
                return hundreds[Math.floor(number / 100)] + (number % 100 ? " " + numberToText(number % 100) : "");
            }

            function speakText(data) {
                if ('speechSynthesis' in window) {
                    var speech = new SpeechSynthesisUtterance(data);
                    speech.lang = 'id-ID';
                    speech.pitch = 1; // Nada suara
                    speech.rate = 1.1; // Kecepatan suara
                    speech.volume = 1; // Volume suara
                    window.speechSynthesis.speak(speech);
                } else {
                    alert("Browser Anda tidak mendukung teks ke suara.");
                }
            }
            speakText('antrian ' + numberToText(data['message']['no_reg']));
            speakText('atas nama ' + data['message']['nm_pasien']);
            speakText(data['message']['nm_poli']);
        });
    </script>
@endpush
