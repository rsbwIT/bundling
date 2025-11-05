<div style="font-family: 'Inter', Arial, sans-serif; background:#f8fafc;">

    {{-- HEADER ATAS (Logo + Judul Display + Logo BPJS) --}}
    <div style="
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:14px 24px;
        background:#ffffff;
        border-bottom:4px solid #b8d8e8;
        box-shadow:0 2px 6px rgba(0,0,0,0.05);
    ">
        <img src="{{ asset('img/bw2.png') }}" alt="Logo RS" style="height:110px;">

        <h2 style="
            margin:0 40px;
            font-size:28px;
            font-weight:700;
            color:#00796b;
            letter-spacing:0.5px;
            text-align:center;
            flex:1;
            line-height:1.4;
            text-transform: none;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        ">
            Informasi Ketersediaan Tempat Tidur
        </h2>

        <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS" style="height:40px;">
    </div>

    {{-- SUB HEADER (Nama Ruangan & Jam) --}}
    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:12px 24px;
        background:#ffffff;
        border-bottom:1px solid #e6eef8;
    ">
        <div>
            <h2 style="margin:0; font-size:26px; color:#0b3a66; font-weight:700; line-height:1.2;">
                {{ $namaRuangan ?? '— Tidak ada ruangan —' }}
            </h2>
            <div style="font-size:14px; color:#475569; margin-top:2px;">Informasi ketersediaan tempat tidur</div>
        </div>

        <div style="text-align:right;">
            <div id="clock" style="
                font-weight:700;
                font-size:20px;
                color:#0b3a66;
                padding:4px 10px;
                background:#f1f5f9;
                border-radius:6px;
                display:inline-block;
            "></div>
            <div style="font-size:12px; color:#64748b; margin-top:2px;">Auto ganti ruangan tiap 10 detik</div>
        </div>
    </div>

    {{-- DATA RUANGAN --}}
    @if(!empty($ruangan) && isset($ruangan['kamar']))
        @php
            // Hitung total maintenance
            $total_maintenance = 0;
            foreach($ruangan['kamar'] as $kamar){
                foreach($kamar['beds'] as $bed){
                    if($bed->status == 2){
                        $total_maintenance++;
                    }
                }
            }
        @endphp

        {{-- SUMMARY --}}
        <div style="display:flex; gap:12px; padding:12px 24px;">
            <div style="flex:1; background:#ffffff; border-radius:8px; padding:14px; border:1px solid #e6eef8; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.03);">
                <div style="font-size:14px; color:#64748b;">Total Bed</div>
                <div style="font-size:28px; font-weight:800; color:#0b3a66;">{{ $ruangan['total_bad'] ?? 0 }}</div>
            </div>

            <div style="flex:1; background:#0288d1; border-radius:8px; padding:14px; color:white; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                <div style="font-size:14px; opacity:0.9;">Terisi</div>
                <div style="font-size:28px; font-weight:800;">{{ $ruangan['total_isi'] ?? 0 }}</div>
            </div>

            <div style="flex:1; background:#16a34a; border-radius:8px; padding:14px; color:white; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                <div style="font-size:14px; opacity:0.9;">Kosong</div>
                <div style="font-size:28px; font-weight:800;">{{ $ruangan['total_kosong'] ?? 0 }}</div>
            </div>

            <div style="flex:1; background:#ffeb3b; border-radius:8px; padding:14px; color:#000; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                <div style="font-size:14px; opacity:0.9;">Maintenance</div>
                <div style="font-size:28px; font-weight:800;">{{ $total_maintenance }}</div>
            </div>
        </div>

        {{-- LIST KAMAR --}}
        <div style="padding:12px 24px;">
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:14px;">
                @foreach($ruangan['kamar'] as $kamarName => $kamar)
                    <div style="
                        background:#ffffff;
                        border-radius:10px;
                        border:1px solid #e6eef8;
                        padding:12px;
                        box-shadow:0 4px 10px rgba(11,58,102,0.04);
                        transition: transform 0.2s;
                    " onmouseover="this.style.transform='translateY(-4px)';" onmouseout="this.style.transform='translateY(0)';">

                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <div style="font-weight:800; color:#0b3a66;">{{ $kamarName }}</div>
                            <div style="font-size:13px; color:#64748b;">Kelas: <strong>{{ $kamar['kelas'] }}</strong></div>
                        </div>

                        <div style="font-size:13px; color:#475569; margin-bottom:10px;">
                            Terisi: <strong>{{ $kamar['jumlah_isi'] }}</strong> |
                            Kosong: <strong>{{ $kamar['jumlah_kosong'] }}</strong> |
                            Maintenance: <strong>
                                {{ collect($kamar['beds'])->where('status', 2)->count() }}
                            </strong>
                        </div>

                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            @php
                                $count = count($kamar['beds']);
                                $width = $count == 1 ? '100%' : ($count == 2 ? '48%' : '30%');
                            @endphp
                            @foreach($kamar['beds'] as $bed)
                                @php
                                    $label = $bed->no_bed ?? '-';

                                    // Warna berdasarkan status
                                    if($bed->status == 1){
                                        $bg = '#0b3a66';      // Terisi: biru
                                        $color = '#ffffff';
                                    } elseif($bed->status == 2){
                                        $bg = '#ffeb3b';      // Cadangan/Maintenance: kuning
                                        $color = '#000000';
                                    } else {
                                        $bg = '#ffffff';      // Kosong: putih
                                        $color = '#0b3a66';
                                    }
                                @endphp
                                <div style="width:{{ $width }}; min-width:60px;">
                                    <div style="
                                        padding:10px;
                                        border-radius:8px;
                                        border:1px solid #cfe2f6;
                                        text-align:center;
                                        background:{{ $bg }};
                                        color:{{ $color }};
                                        font-weight:800;
                                        transition: background 0.2s, color 0.2s;
                                    ">
                                        {{ $label }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

    @else
        <div style="padding:60px; text-align:center; color:#64748b;">
            <div style="font-size:18px; font-weight:700;">Data kamar tidak ditemukan</div>
        </div>
    @endif

</div>

{{-- SCRIPT JAM DAN NEXT ROOM --}}
<script>
function updateClock(){
    document.getElementById('clock').innerText =
        new Date().toLocaleString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:false});
}
setInterval(updateClock,1000);
updateClock();

// trigger Livewire setiap 10 detik
document.addEventListener('DOMContentLoaded',function(){
    setInterval(()=>Livewire.emit('next-room'),10000);
});
</script>
