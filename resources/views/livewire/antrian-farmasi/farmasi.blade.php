@php
    $antrians = DB::table('antrian')
        ->select([
            'antrian.nomor_antrian',
            'antrian.rekam_medik',
            'antrian.nama_pasien',
            'antrian.keterangan',
            'antrian.status'
        ])
        ->whereIn('antrian.status', ['antrian', 'dipanggil'])
        ->where(function($query) {
            $query->where('antrian.keterangan', 'like', '%racikan%')
                  ->orWhere('antrian.keterangan', 'like', '%non racik%');
        })
        ->orderBy('antrian.nomor_antrian', 'asc')
        ->get();

    $antrianRacik = collect($antrians)->first(function($item) {
        return str_contains(strtolower($item->keterangan ?? ''), 'racikan');
    });

    $antrianNonRacik = collect($antrians)->first(function($item) {
        return str_contains(strtolower($item->keterangan ?? ''), 'non racik');
    });
@endphp

<div wire:poll.3000ms style="font-family: 'Segoe UI', Arial, sans-serif; background: #eaf6f6; min-height: 100vh;">
    <!-- Header -->
    <div style="background: #fff; padding: 10px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd;">
        <div><img src="{{ asset('img/bw2.png') }}" alt="RS Bumi Waras Logo" style="height: 70px;"></div>
        <div style="font-size: 1.8em; color: #2d3e50; font-weight: bold;">Antrian Farmasi</div>
        <div><img src="{{ asset('img/bpjs.png') }}" alt="BPJS Kesehatan Logo" style="height: 40px;"></div>
    </div>

    <!-- Main Content -->
    <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: stretch;">
        <!-- Box Antrian -->
        <div style="width: 32%; display: flex; flex-direction: column; gap: 24px; padding: 32px 0 32px 32px;">
            <!-- Non Racik -->
            <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #b2dfdb; padding: 24px 0; text-align: center; border-top: 8px solid #219150;">
                <div style="font-size: 1.1em; color: #219150; font-weight: bold;">ANTRIAN FARMASI NON RACIK</div>
                <div style="font-size: 3.5em; font-weight: bold; color: #219150;" id="nonRacikNomor">
                    {{ $antrianNonRacik ? $antrianNonRacik->nomor_antrian : '-' }}
                </div>
                <div style="font-size: 1.1em; color: #222;">
                    {{ $antrianNonRacik ? $antrianNonRacik->nama_pasien : 'Belum ada antrian' }}
                </div>
                <div style="font-size: 1em; color: #888;">
                    {{ $antrianNonRacik ? $antrianNonRacik->keterangan : '' }}
                </div>
                <div style="margin-top: 18px; background: #219150; color: #fff; font-weight: bold; padding: 6px 0;">LOKET 1</div>
            </div>

            <!-- Racik -->
            <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #b2dfdb; padding: 24px 0; text-align: center; border-top: 8px solid #219150;">
                <div style="font-size: 1.1em; color: #219150; font-weight: bold;">ANTRIAN FARMASI RACIK</div>
                <div style="font-size: 3.5em; font-weight: bold; color: #219150;" id="racikNomor">
                    {{ $antrianRacik ? $antrianRacik->nomor_antrian : '-' }}
                </div>
                <div style="font-size: 1.1em; color: #222;">
                    {{ $antrianRacik ? $antrianRacik->nama_pasien : 'Belum ada antrian' }}
                </div>
                <div style="font-size: 1em; color: #888;">
                    {{ $antrianRacik ? $antrianRacik->keterangan : '' }}
                </div>
                <div style="margin-top: 18px; background: #219150; color: #fff; font-weight: bold; padding: 6px 0;">LOKET 2</div>
            </div>
        </div>

        <!-- Info Panel -->
        <div style="width: 68%; display: flex; align-items: center; justify-content: center; background: #fff; border-radius: 0 0 0 60px; margin: 32px 32px 32px 0; box-shadow: 0 2px 12px #b2dfdb;">
            <div style="width: 90%; text-align: center;">
                <div style="font-size: 1.3em; color: #219150; font-weight: bold;">
                    <span style="background: #eaf6f6; padding: 6px 18px; border-radius: 20px;">DISPLAY</span>
                </div>
                <div style="font-size: 2.2em; color: #2d3e50; font-weight: bold; margin-top: 12px;">ANTRIAN FARMASI</div>
                <img src="https://i.ibb.co/6b8Qw8F/puskesmas.png" alt="Logo Farmasi" style="width: 120px; margin-top: 18px;">
            </div>
        </div>
    </div>

    <!-- Audio -->
    <script>
        const audio = new Audio("{{ asset('sound/panggil.mp3') }}");

        window.addEventListener('panggil-antrian', event => {
            const { jenis, nomor, nama } = event.detail;
            console.log("Panggil:", jenis, nomor, nama);

            // Putar audio dan nama
            audio.play().then(() => {
                const speak = new SpeechSynthesisUtterance(nama);
                speak.lang = 'id-ID';
                speak.rate = 0.9;
                window.speechSynthesis.speak(speak);
            }).catch(err => {
                console.warn("Audio gagal diputar:", err);
            });
        });
    </script>


</div>
