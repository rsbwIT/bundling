<div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #e9f7ef, #d4edda);
            color: #000;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ✅ Header Modern */
        .header-tv {
            background: linear-gradient(135deg, #198754, #157347);
            padding: 1rem;
            text-align: center;
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* ✅ Card Loket Glassmorphism */
        .loket-card {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            min-height: 280px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .loket-card:hover {
            transform: translateY(-5px);
        }

        /* ✅ Judul Loket */
        .loket-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            background: #198754;
            border-radius: 10px;
            padding: .6rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        /* ✅ Nomor Antrian */
        .antrian-no {
            font-size: 3.2rem;
            font-weight: bold;
            color: #ecfc18;
            animation: glow 1.5s infinite alternate;
        }

        /* @keyframes glow {
            from { text-shadow: 0 0 5px rgba(220,53,69,0.6); }
            to { text-shadow: 0 0 20px rgba(220,53,69,1); }
        } */

        .antrian-pasien { font-size: 1.8rem; font-weight: bold; margin-top:.5rem; }
        .antrian-dokter { font-size: 1.1rem; color: #444; margin-top: .3rem; }

        /* ✅ Highlight Pasien Dipanggil */
        .highlight {
            background: #198754;
            color: #fff;
            padding: 1rem;
            border-radius: 12px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(25,135,84, 0.6); }
            70% { box-shadow: 0 0 0 15px rgba(25,135,84, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25,135,84, 0); }
        }

        /* ✅ Footer Running Text */
        .footer-tv {
            background: #157347;
            color: #fff;
            padding: .7rem;
            font-size: 1.2rem;
            position: fixed;
            bottom: 0;
            width: 100%;
            overflow: hidden;
        }

        .footer-tv span {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
        }

        @keyframes marquee {
            0%   { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }

        /* ✅ Panel Toggle */
        .toggle-section {
            background: #fff;
            color: #000;
            padding: 10px;
            border-radius: 10px;
            margin: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .toggle-section label {
            margin-right: 15px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        /* ✅ Custom Checkbox Bulat */
        .toggle-section input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #198754;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            vertical-align: middle;
            margin-right: 6px;
            position: relative;
            transition: all 0.3s ease-in-out;
        }

        .toggle-section input[type="checkbox"]:checked {
            background-color: #198754;
            border-color: #198754;
        }

        .toggle-section input[type="checkbox"]:checked::after {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
        }
    </style>

    <!-- ✅ Header dengan logo kiri & kanan, judul center -->
    <div class="header-bar w-100 bg-white shadow-sm border-bottom fixed-top d-flex align-items-center justify-content-between px-4 py-3">
        <!-- Logo RS -->
        <div class="d-flex align-items-center">
            <img alt="Logo Rumah Sakit" src="{{ asset('img/bw2.png') }}" style="height:90px; max-height:12vh;" />
        </div>

        <!-- Judul Tengah -->
        <div class="text-center flex-grow-1">
            <h1 class="m-0 fw-bold text-dark" style="font-size:2rem;">ANTRIAN PENDAFTARAN</h1>
        </div>

        <!-- Logo BPJS -->
        <div class="d-flex align-items-center">
            <img alt="Logo BPJS" src="{{ asset('img/bpjs.png') }}" style="height:50px; max-height:12vh;" />
        </div>
    </div>

    <!-- ✅ Panel Toggle Loket -->
    <div class="container-fluid toggle-section text-center" style="margin-top:120px;">
        <label><input type="checkbox" class="toggle-loket" value="Loket 1" checked> Loket 1</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 2" checked> Loket 2</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 3" checked> Loket 3</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 4" checked> Loket 4</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 5" checked> Loket 5</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 6" checked> Loket 6</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 7" checked> Loket 7</label>
    </div>


    <div class="container-fluid mt-4">
        <div class="row g-4" id="antrian-container">
            {{-- data antrian inject di JS --}}
        </div>
    </div>

    <!-- ✅ Running Text -->
    {{-- <div class="footer-tv">
        <span>Selamat datang di Rumah Sakit Sehat Selalu | Layanan cepat, nyaman, dan profesional | Terima kasih telah menunggu dengan sabar 🙏</span>
    </div> --}}

    <audio id="bell-sound" src="{{ asset('sounds/bell.mp3') }}" preload="auto"></audio>
    <script src="https://code.responsivevoice.org/responsivevoice.js"></script>

    <script>
        let lastDipanggil = {};
        const loketFix = ["Loket 1","Loket 2","Loket 3","Loket 4","Loket 5","Loket 6","Loket 7"];

        // ambil loket yang aktif dari checkbox
        function getActiveLokets() {
            let aktif = [];
            document.querySelectorAll(".toggle-loket:checked").forEach(cb => aktif.push(cb.value));
            return aktif;
        }

        function renderAntrian(data) {
            let container = document.getElementById("antrian-container");
            container.innerHTML = "";

            // grupkan data
            let grouped = {};
            data.forEach(item => {
                if (!grouped[item.nama_loket]) grouped[item.nama_loket] = [];
                grouped[item.nama_loket].push(item);
            });

            // gabungkan dengan loket fix
            loketFix.forEach(loket => { if (!grouped[loket]) grouped[loket] = []; });

            // filter hanya yang aktif
            let lokets = Object.keys(grouped).filter(l => getActiveLokets().includes(l)).sort();

            lokets.forEach(loket => {
                let col = document.createElement("div");
                col.className = "col-md-4 col-sm-6";

                let card = document.createElement("div");
                card.className = "loket-card";

                let title = document.createElement("div");
                title.className = "loket-title";
                title.innerText = loket.toUpperCase();

                let content = document.createElement("div");

                if (grouped[loket].length === 0) {
                    content.innerHTML = `<div class="text-muted"><i class="fas fa-user-clock"></i> BELUM ADA ANTRIAN</div>`;
                } else {
                    let dipanggil = grouped[loket].find(item => item.status === "DIPANGGIL");
                    let pasien = dipanggil ? dipanggil : grouped[loket][0];

                    content.innerHTML = `
                        <div class="slide-item ${pasien.status === "DIPANGGIL" ? "highlight" : ""}">
                            <div class="antrian-no">${pasien.no_reg}</div>
                            <div class="antrian-pasien">${pasien.nm_pasien.toUpperCase()}</div>
                            <div class="antrian-dokter"><i class="fas fa-user-md"></i> ${pasien.nm_dokter.toUpperCase()}</div>
                            <div class="mt-3"><span class="badge bg-dark text-white fs-6">${pasien.status}</span></div>
                        </div>
                    `;

                    if (pasien.status === "DIPANGGIL") {
                        let pasienDipanggil = pasien.nm_pasien.toUpperCase();
                        if (lastDipanggil[loket] !== pasienDipanggil) {
                            lastDipanggil[loket] = pasienDipanggil;
                            let bell = document.getElementById("bell-sound");
                            bell.play().then(() => {
                                setTimeout(() => {
                                    const text = `Nomor antrian ${pasien.no_reg}, pasien ${pasien.nm_pasien}, menuju ${pasien.nm_dokter} di ${loket}`;
                                    responsiveVoice.speak(text, "Indonesian Female",{pitch:1,rate:0.9,volume:1});
                                }, 1000);
                            }).catch(err => console.log("Autoplay blok:", err));
                        }
                    }
                }

                card.appendChild(title);
                card.appendChild(content);
                col.appendChild(card);
                container.appendChild(col);
            });
        }

        function fetchAntrian() {
            fetch("{{ route('antrian.apiTv') }}")
                .then(res => res.json())
                .then(data => renderAntrian(data))
                .catch(err => console.error("Gagal ambil data:", err));
        }

        // reload ketika toggle berubah
        document.querySelectorAll(".toggle-loket").forEach(cb => {
            cb.addEventListener("change", fetchAntrian);
        });

        fetchAntrian();
        setInterval(fetchAntrian, 10000);
    </script>
</div>
