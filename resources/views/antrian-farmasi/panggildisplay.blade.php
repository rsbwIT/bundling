<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 90%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .nomor-antrian {
            font-size: 80px;
            font-weight: bold;
            color: white;
            background: purple;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .loket {
            font-size: 30px;
            font-weight: bold;
            margin-top: 10px;
        }
        .video {
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
    <script>
        function playVoice(text) {
            let msg = new SpeechSynthesisUtterance(text);
            msg.lang = "id-ID";
            window.speechSynthesis.speak(msg);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>DISPLAY ANTRIAN</h1>

        @if ($antrian->isNotEmpty())
            @foreach ($antrian as $data)
                <div class="nomor-antrian">
                    {{ $data->nomor_antrian }}
                </div>
                <div class="loket">
                    LOKET {{ $data->nama_loket }}
                </div>
                <button onclick="playVoice('Nomor antrian {{ $data->nomor_antrian }} silakan menuju {{ $data->nama_loket }}')">
                    🔊 Panggil
                </button>
            @endforeach
        @else
            <p>Tidak ada antrian saat ini.</p>
        @endif

        <video class="video" controls>
            <source src="{{ asset('video/display.mp4') }}" type="video/mp4">
            Browser Anda tidak mendukung tag video.
        </video>

        <p>Jam buka layanan kami adalah pukul 07:00 sd 21:00. Terima kasih atas kunjungan Anda.</p>
    </div>
</body>
</html>
