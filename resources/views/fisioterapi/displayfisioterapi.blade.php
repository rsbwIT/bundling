<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian Fisioterapi</title>
    <!-- CSS Bootstrap 4 or 5 / Tailwind -->
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="/dist/css/adminlte.min.css" />
    <script src="/plugins/jquery/jquery.min.js"></script>
    <style>
        body {
            background: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding-top: 150px; /* Space for the fixed header */
        }
        
        /* Header dari Farmasi */
        .header-bar {
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 2rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .header-title h1 {
            font-size: 3.2rem;
            font-weight: 900;
            color: #00796b;
            margin: 0;
            letter-spacing: 2px;
        }
        .header-title h4 {
            font-size: 2.5rem;
            color: #004d40;
            margin: 5px 0 0 0;
            font-weight: 700;
        }

        /* Split Screen Layout */
        .main-content {
            flex: 1;
            display: flex;
            padding: 20px;
            gap: 30px;
            height: calc(100vh - 170px);
        }

        /* Kiri: Video Profil */
        .video-container {
            flex: 6;
            background: #000;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
        }
        
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Kanan: Kotak Antrian */
        .queue-container {
            flex: 4;
            display: flex;
            flex-direction: column;
        }

        .card-display {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            width: 100%;
            height: 100%;
            text-align: center;
            padding: 40px 20px;
            border-top: 10px solid #00796b;
            transition: transform 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .pulsate {
            animation: pulse-bg 1.5s infinite;
        }
        @keyframes pulse-bg {
            0% { box-shadow: 0 0 0 0 rgba(0, 121, 107, 0.4); }
            70% { box-shadow: 0 0 0 30px rgba(0, 121, 107, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 121, 107, 0); }
        }

        .title-text {
            font-size: 2rem;
            color: #7f8c8d;
            margin-bottom: 5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .no-antrian {
            font-size: 7rem;
            font-weight: 900;
            color: #00796b;
            line-height: 1.1;
            margin: 10px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .nama-pasien {
            font-size: 3rem;
            font-weight: 800;
            color: #2c3e50;
            margin-top: 10px;
            text-transform: uppercase;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 10px;
        }

        .poli-text {
            font-size: 1.5rem;
            color: #d35400;
            font-weight: 700;
            margin-top: 20px;
            background: #fff3e0;
            display: inline-block;
            padding: 8px 25px;
            border-radius: 50px;
        }

        .waiting-text {
            font-size: 2.5rem;
            color: #95a5a6;
            font-style: italic;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            background: #004d40;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header-bar">
        <img src="{{ asset('img/bw2.png') }}" alt="Logo RS" style="height:110px;">
        <div class="header-title text-center">
            <h1>DISPLAY ANTRIAN PASIEN</h1>
        </div>
        <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS" style="height:35px;">
    </div>

    <!-- Main Content: Split Screen -->
    <div class="main-content">
        <!-- Kiri: Video -->
        <div class="video-container">
            <!-- Tempat YouTube Player -->
            <div id="youtubePlayer"></div>
        </div>

        <!-- Kanan: Kotak Antrian -->
        <div class="queue-container">
            <div class="card-display" id="displayCard">
                <div id="contentAda" style="display: none;">
                    <div class="title-text">ANTRIAN SAAT INI</div>
                    <div class="no-antrian" id="noReg">-</div>
                    <div class="nama-pasien" id="namaPasien">-</div>
                    <div class="poli-text" id="namaPoli">-</div>
                </div>
                <div id="contentKosong">
                    <div class="waiting-text">
                        <i class="fas fa-spinner fa-spin mb-3" style="font-size: 4rem; color: #00796b;"></i><br>
                        Menunggu Panggilan...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <marquee behavior="scroll" direction="left">Selamat Datang di Poliklinik Rehabilitasi Medik RS Bumi Waras. Tetap patuhi protokol kesehatan. Terima Kasih.</marquee>
    </div>

    <!-- YouTube Iframe API -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
        var player;
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtubePlayer', {
                height: '100%',
                width: '100%',
                videoId: 'l8HbfVjp844', // Default video (Pemandangan Alam yang bisa diputar)
                playerVars: {
                    'autoplay': 1, // Harus 1 agar Chrome mengizinkan kontrol lewat JS
                    'mute': 1, // TV Display selalu Mute karena suara dari Admin
                    'controls': 0, // hide controls
                    'showinfo': 0,
                    'rel': 0,
                    'playsinline': 1
                },
                events: {
                    'onReady': onPlayerReady
                }
            });
        }
        function onPlayerReady(event) {
            event.target.mute();
            event.target.playVideo();
        }

        let lastTime = 0;
        let lastVideoCmdTime = 0;

        function fetchAntrian() {
            $.ajax({
                url: '{{ url('/api/display-fisioterapi/current') }}',
                type: 'GET',
                cache: false,
                success: function(response) {
                    // Cek Perintah Video
                    if(response.video && player && typeof player.getPlayerState === 'function' && parseFloat(response.video.time) > lastVideoCmdTime) {
                        lastVideoCmdTime = parseFloat(response.video.time);
                        if(response.video.action === 'skip' && typeof player.seekTo === 'function') {
                            player.seekTo(0);
                            player.mute();
                            player.playVideo();
                        } else if(response.video.action === 'change_video' && typeof player.loadVideoById === 'function') {
                            // Tampilkan toast agar kita tahu TV benar-benar menerima perintah
                            if(typeof Swal !== 'undefined') {
                                Swal.fire({
                                    toast: true, position: 'top-end', icon: 'info',
                                    title: 'Mengganti Video TV...', showConfirmButton: false, timer: 2000
                                });
                            }
                            player.loadVideoById(response.video.value);
                            player.mute();
                            player.playVideo();
                        } else if(response.video.action === 'play' && typeof player.playVideo === 'function') {
                            if(typeof Swal !== 'undefined') {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Melanjutkan Video...', showConfirmButton: false, timer: 1500 });
                            }
                            if(response.video.value) player.seekTo(parseFloat(response.video.value));
                            player.playVideo();
                        } else if(response.video.action === 'pause' && typeof player.pauseVideo === 'function') {
                            if(typeof Swal !== 'undefined') {
                                Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Video Dijeda', showConfirmButton: false, timer: 1500 });
                            }
                            if(response.video.value) player.seekTo(parseFloat(response.video.value));
                            player.pauseVideo();
                        } else if(response.video.action === 'sync' && typeof player.seekTo === 'function') {
                            let targetTime = parseFloat(response.video.value);
                            let currentTime = player.getCurrentTime();
                            // Jika selisih waktu lebih dari 0.5 detik, paksa sinkronisasi!
                            if (Math.abs(currentTime - targetTime) > 0.5) {
                                player.seekTo(targetTime);
                            }
                        }
                    }

                    // Cek Data Antrian
                    let antrian = response.antrian;
                    if (antrian && antrian.no_reg) {
                        $('#contentKosong').hide();
                        $('#contentAda').show();
                        
                        $('#noReg').text(antrian.no_reg);
                        $('#namaPasien').text(antrian.nm_pasien);
                        $('#namaPoli').text(antrian.nm_poli);

                        if (lastTime === 0) {
                            lastTime = antrian.time;
                        } else if (antrian.time > lastTime) {
                            lastTime = antrian.time;
                            
                            $('#displayCard').addClass('pulsate');
                            setTimeout(() => {
                                $('#displayCard').removeClass('pulsate');
                            }, 5000);
                        }
                    }
                }
            });
        }

        $(document).ready(function() {
            fetchAntrian();
            setInterval(fetchAntrian, 300); // Polling sangat cepat (300ms) agar minim delay
        });
    </script>
</body>
</html>
