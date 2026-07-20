@extends('layout.layoutDashboard')
@section('title', 'Antrian Fisioterapi')

@section('konten')
<div class="container-fluid py-3">

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="m-0">Antrian Poliklinik Rehabilitasi Medik</h4>
            <small class="text-muted">Daftar antrian pasien fisioterapi</small>
        </div>
        <span class="badge badge-primary px-3 py-2" style="font-size:.85rem;">
            <i class="fas fa-calendar-alt mr-1"></i>
            {{ \Carbon\Carbon::parse($tgl_registrasi)->format('d/m/Y') }}
        </span>
    </div>

    {{-- ── Filter Card ── --}}
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ url('/antrian-fisioterapi') }}" class="form-inline flex-wrap" style="gap:10px;">
                <div class="form-group mr-2">
                    <label class="mr-2 text-muted small font-weight-bold">
                        <i class="fas fa-calendar-alt"></i> Tanggal Registrasi
                    </label>
                    <input type="date"
                           name="tgl_registrasi"
                           id="tgl_registrasi"
                           class="form-control form-control-sm"
                           value="{{ $tgl_registrasi }}">
                </div>

                <div class="form-group mr-2">
                    <div class="input-group input-group-sm" style="width:240px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text"
                               id="searchInput"
                               class="form-control"
                               placeholder="Cari nama, No.Rawat, RM...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search mr-1"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    {{-- ── Video Remote Control Card ── --}}
    <div class="card shadow-sm mb-4 border-info">
        <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between bg-light">
            <div class="d-flex align-items-center w-50">
                <!-- Mini Preview Player -->
                <div class="mr-3" style="width: 160px; height: 90px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); flex-shrink: 0; background: #000;">
                    <div id="adminYoutubePlayer"></div>
                </div>
                
                <div class="ml-2 flex-grow-1" style="max-width: 500px; position: relative;">
                    <div class="input-group" style="border-radius: 40px; overflow: hidden; border: 1px solid #ccc; box-shadow: inset 0 1px 2px #eee;">
                        <input type="text" id="youtubeInput" class="form-control border-0 shadow-none px-4" placeholder="Telusuri YouTube di sini..." style="height: 40px; border-radius: 40px 0 0 40px;" onkeypress="if(event.keyCode==13) changeYouTubeVideo()">
                        <div class="input-group-append">
                            <button class="btn border-0 shadow-none px-4" type="button" onclick="changeYouTubeVideo()" style="background-color: #f8f8f8; border-left: 1px solid #ccc !important; border-radius: 0 40px 40px 0; height: 40px;" id="searchBtn">
                                <i class="fas fa-search text-muted"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 10px; margin-top: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); z-index: 9999; max-height: 350px; overflow-y: auto;">
                        <!-- Item will be appended here -->
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="mr-3 d-flex align-items-center">
                    <label for="volumeSlider" class="m-0 mr-2"><i class="fas fa-volume-up"></i> Volume:</label>
                    <input type="range" id="volumeSlider" min="0" max="100" value="27" style="width: 150px;" onchange="sendVideoCommand('volume', this.value)">
                    <span id="volumeLabel" class="ml-2 font-weight-bold">27%</span>
                </div>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 mr-2" onclick="sendVideoCommand('play', adminPlayer ? adminPlayer.getCurrentTime() : 0)" title="Mainkan Video">
                    <i class="fas fa-play"></i>
                </button>
                <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 mr-2" onclick="sendVideoCommand('pause', adminPlayer ? adminPlayer.getCurrentTime() : 0)" title="Jeda Video">
                    <i class="fas fa-pause"></i>
                </button>
                <button type="button" class="btn btn-sm btn-info rounded-pill px-3" onclick="sendVideoCommand('skip', null)">
                    <i class="fas fa-step-forward"></i> Ulangi/Skip
                </button>
            </div>
        </div>
    </div>
    

    {{-- ── Table Card ── --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-sm table-bordered table-hover" id="tabelPasien" style="font-size: 0.9rem;">
                    <thead class="text-center">
                        <tr>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; width: 50px;">No.</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap; width: 120px;">No. Antrian</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap; width: 150px;">No. Rawat</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap; width: 120px;">No. RM</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Nama Pasien</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Poliklinik</th>
                            <th class="text-center" style="position:sticky; top:0; z-index:2; background:#f8fafc; width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antrian as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->no_reg }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm" style="font-size: 0.8rem; letter-spacing: 0.5px; transition: all 0.3s;" onclick="panggilPasien('{{ $item->no_rawat }}', '{{ addslashes($item->nm_pasien) }}', '{{ $item->no_reg }}', '{{ addslashes($item->nm_poli) }}')" onmouseover="this.classList.replace('btn-outline-primary', 'btn-primary')" onmouseout="this.classList.replace('btn-primary', 'btn-outline-primary')">
                                    <i class="fas fa-bullhorn mr-1"></i> Panggil
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Tidak ada data antrian untuk tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('tabelPasien');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            for (let i = 0; i < rows.length; i++) {
                const text = rows[i].textContent.toLowerCase();
                if (text.includes(filter)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    });

    function numberToText(number) {
        const ones = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan"];
        const teens = ["sepuluh", "sebelas", "dua belas", "tiga belas", "empat belas", "lima belas", "enam belas", "tujuh belas", "delapan belas", "sembilan belas"];
        const tens = ["", "puluh", "dua puluh", "tiga puluh", "empat puluh", "lima puluh", "enam puluh", "tujuh puluh", "delapan puluh", "sembilan puluh"];
        const hundreds = ["", "seratus", "dua ratus", "tiga ratus", "empat ratus", "lima ratus", "enam ratus", "tujuh ratus", "delapan ratus", "sembilan ratus"];

        number = parseInt(number);
        if (isNaN(number)) return "";
        if (number < 10) return ones[number];
        if (number < 20) return teens[number - 10];
        if (number < 100) return tens[Math.floor(number / 10)] + (number % 10 ? " " + ones[number % 10] : "");
        return hundreds[Math.floor(number / 100)] + (number % 100 ? " " + numberToText(number % 100) : "");
    }

    function speakText(text) {
        if ('speechSynthesis' in window) {
            var speech = new SpeechSynthesisUtterance(text);
            speech.lang = 'id-ID';
            speech.pitch = 1;
            speech.rate = 1.1;
            speech.volume = 1;
            
            let voices = window.speechSynthesis.getVoices();
            let indoVoices = voices.filter(voice => voice.lang.includes('id') || voice.lang.includes('ID'));
            
            if (indoVoices.length > 0) {
                let bestVoice = indoVoices.find(v => v.name.includes('Google') || v.name.includes('Natural') || v.name.includes('Gadis'));
                speech.voice = bestVoice || indoVoices[0];
            }
            
            window.speechSynthesis.speak(speech);
        }
    }

    function toTitleCase(str) {
        return str.toLowerCase().split(' ').map(function(word) {
            return (word.charAt(0).toUpperCase() + word.slice(1));
        }).join(' ');
    }

    function panggilSuara(noReg, nmPasien, nmPoli) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
        
        let namaPanggilan = toTitleCase(nmPasien);
        speakText('Panggilan untuk pasien dengan nomor antrian ' + numberToText(noReg));
        speakText('Atas nama ' + namaPanggilan);
        speakText('Silakan menuju meja petugas untuk melakukan proses finger print, dan rekam wajah.');
    }

    function panggilPasien(noRawat, namaPasien, noReg, nmPoli) {

        // Mainkan suara di komputer Admin seketika saat tombol ditekan
        panggilSuara(noReg, namaPasien, nmPoli);

        $.ajax({
            url: '{{ url('/antrian-fisioterapi/panggil') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                no_rawat: noRawat,
                nm_pasien: namaPasien,
                no_reg: noReg,
                nm_poli: nmPoli
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Memanggil ' + namaPasien,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal memanggil pasien!'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan pada server.'
                });
            }
        });
    }

    function extractVideoID(url) {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        var match = url.match(regExp);
        if (match && match[2].length == 11) {
            return match[2];
        }
        if (url.length === 11) return url;
        return null; // Bukan URL/ID, berarti pencarian
    }

    function playVideoById(videoId) {
        // 1. Kirim sinyal ke TV Display terlebih dahulu
        sendVideoCommand('change_video', videoId);
        
        document.getElementById('youtubeInput').value = '';
        $('#searchResults').hide();
        
        // 2. Langsung jalankan pemutar Admin tanpa delay agar gesit
        if (adminPlayer && typeof adminPlayer.loadVideoById === 'function') {
            adminPlayer.loadVideoById(videoId);
        }
        
        // 3. Paksa sinkronisasi 0.0 setelah 3 detik (saat TV sudah selesai loading)
        setTimeout(() => {
            if (adminPlayer && typeof adminPlayer.getCurrentTime === 'function') {
                let currentTime = adminPlayer.getCurrentTime();
                sendVideoCommand('sync', currentTime);
            }
        }, 3000);
    }

    function changeYouTubeVideo() {
        var input = document.getElementById('youtubeInput').value.trim();
        if (!input) return;
        
        var videoId = extractVideoID(input);
        
        if (videoId) {
            playVideoById(videoId);
        } else {
            $('#searchBtn').html('<i class="fas fa-spinner fa-spin text-muted"></i>');
            $.ajax({
                url: '{{ url('/antrian-fisioterapi/search-video') }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', q: input },
                success: function(response) {
                    $('#searchBtn').html('<i class="fas fa-search text-muted"></i>');
                    if(response && response.length > 0) {
                        let html = '';
                        response.forEach(item => {
                            html += `
                                <div class="d-flex align-items-center p-2 border-bottom" style="cursor: pointer; transition: 0.2s;" onmouseover="this.style.backgroundColor='#f1f1f1'" onmouseout="this.style.backgroundColor='transparent'" onclick="playVideoById('${item.id}')">
                                    <img src="${item.thumbnail}" style="width: 120px; border-radius: 5px; margin-right: 15px;">
                                    <div>
                                        <h6 class="m-0 font-weight-bold" style="font-size: 0.9rem; color: #333;">${item.title}</h6>
                                    </div>
                                </div>
                            `;
                        });
                        $('#searchResults').html(html).show();
                    } else {
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'error',
                            title: 'Video tidak ditemukan!', showConfirmButton: false, timer: 2000
                        });
                    }
                },
                error: function() {
                    $('#searchBtn').html('<i class="fas fa-search text-muted"></i>');
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'error',
                        title: 'Gagal mencari video.', showConfirmButton: false, timer: 2000
                    });
                }
            });
        }
    }
    
    $(document).click(function(event) {
        if (!$(event.target).closest('.input-group, #searchResults').length) {
            $('#searchResults').hide();
        }
    });

    function sendVideoCommand(action, value) {
        // Kirim ke server duluan agar TV merespons secepat mungkin
        $.ajax({
            url: '{{ url('/antrian-fisioterapi/video-command') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: action,
                value: value
            },
            success: function(response) {
                let msg = '';
                if(action === 'skip') msg = 'Video di-skip!';
                else if(action === 'change_video') msg = 'Video TV diubah!';
                else if(action === 'play') msg = 'Video dilanjutkan!';
                else if(action === 'pause') msg = 'Video dijeda!';
                
                if(msg !== '') {
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: msg, showConfirmButton: false, timer: 1500
                    });
                }
            }
        });

        // Eksekusi aksi lokal Admin secara langsung (0 delay) agar terasa sangat responsif,
        // kecuali aksi tertentu jika diperlukan (saat ini semuanya langsung dieksekusi).
        setTimeout(function() {
            if(action === 'volume') {
                document.getElementById('volumeLabel').innerText = value + '%';
                if (adminPlayer && typeof adminPlayer.setVolume === 'function') {
                    if(value > 0) adminPlayer.unMute();
                    adminPlayer.setVolume(value);
                }
            } else if (action === 'skip') {
                if (adminPlayer && typeof adminPlayer.seekTo === 'function') {
                    adminPlayer.seekTo(0);
                    adminPlayer.playVideo();
                }
            } else if (action === 'play') {
                if (adminPlayer && typeof adminPlayer.playVideo === 'function') {
                    adminPlayer.playVideo();
                }
            } else if (action === 'pause') {
                if (adminPlayer && typeof adminPlayer.pauseVideo === 'function') {
                    adminPlayer.pauseVideo();
                }
            }
        }, 0);
    }
</script>

<!-- YouTube Iframe API for Audio on Admin -->
<script src="https://www.youtube.com/iframe_api"></script>
<script>
    var adminPlayer;
    function onYouTubeIframeAPIReady() {
        adminPlayer = new YT.Player('adminYoutubePlayer', {
            height: '90',
            width: '160',
            videoId: 'l8HbfVjp844', // Default video
            playerVars: {
                'autoplay': 0,
                'mute': 0, // Suara nyala di Admin!
                'controls': 0,
                'showinfo': 0,
                'rel': 0,
                'playsinline': 1
            },
            events: {
                'onReady': function(event) {
                    event.target.setVolume(27);
                    // Biarkan pause sampai user klik Play
                }
            }
        });
    }
</script>
@endsection
