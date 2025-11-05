    {{-- ✅ Header Rumah Sakit (tidak ganggu Livewire) --}}
    <div class="header-bar" style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:10px 20px;
        background:#ffffff;
        border-bottom:2px solid #e0e7f1;
    ">
        <img src="{{ asset('img/bw2.png') }}" alt="Logo RS" style="height:80px;">
        
        <div class="header-title text-center">
            <h2 style="margin:0;font-size:28px;color:#0b3a66;font-weight:800;">
                Informasi Ketersediaan Tempat Tidur
            </h2>
        </div>

        <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS" style="height:45px;">
    </div>


    {{-- ✅ Header Ruangan + Jam --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;background:#ffffff;border-bottom:1px solid #e6eef8;">
        <div>
            <h2 style="margin:0;font-size:26px;color:#0b3a66;font-weight:700;">
                {{ $namaRuangan ?? '— Tidak ada ruangan —' }}
            </h2>
        </div>

        {{-- ✅ Clock -- Livewire jangan sentuh --}}
        <div style="text-align:right;" wire:ignore>
            <div id="clock" style="font-weight:700;font-size:20px;color:#0b3a66;"></div>
        </div>
    </div>


    @if(!empty($ruangan) && isset($ruangan['kamar']))

        {{-- Summary --}}
        <div class="row" style="display:flex;gap:12px;padding:12px 18px;align-items:stretch;">
            <div style="flex:1;background:#ffffff;border-radius:8px;padding:12px;border:1px solid #e6eef8;text-align:center;">
                <div style="font-size:14px;color:#64748b;">Total Bed</div>
                <div style="font-size:28px;font-weight:800;color:#0b3a66;">{{ $ruangan['total_bad'] ?? 0 }}</div>
            </div>

            <div style="flex:1;background:#0288d1;border-radius:8px;padding:12px;color:white;text-align:center;">
                <div style="font-size:14px;opacity:0.9;">Terisi</div>
                <div style="font-size:28px;font-weight:800;">{{ $ruangan['total_isi'] ?? 0 }}</div>
            </div>

            <div style="flex:1;background:#16a34a;border-radius:8px;padding:12px;color:white;text-align:center;">
                <div style="font-size:14px;opacity:0.9;">Kosong</div>
                <div style="font-size:28px;font-weight:800;">{{ $ruangan['total_kosong'] ?? 0 }}</div>
            </div>
        </div>


        {{-- Grid Kamar --}}
        <div style="padding:12px 18px;">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;">
                @foreach($ruangan['kamar'] as $kamarName => $kamar)
                    <div style="background:#ffffff;border-radius:10px;border:1px solid #e6eef8;padding:12px;box-shadow:0 4px 10px rgba(11,58,102,0.04);">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <div style="font-weight:800;color:#0b3a66;">{{ $kamarName }}</div>
                            <div style="font-size:13px;color:#64748b;">Kelas: <strong>{{ $kamar['kelas'] }}</strong></div>
                        </div>

                        <div style="font-size:13px;color:#475569;margin-bottom:10px;">
                            Terisi: <strong>{{ $kamar['jumlah_isi'] ?? 0 }}</strong> |
                            Kosong: <strong>{{ $kamar['jumlah_kosong'] ?? 0 }}</strong>
                        </div>

                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            @php
                                $countBeds = is_countable($kamar['beds']) ? count($kamar['beds']) : 0;
                                $colClass = $countBeds === 1 ? '100%' : ($countBeds === 2 ? '48%' : '30%');
                            @endphp

                            @foreach($kamar['beds'] as $bed)
                                @php
                                    $label = $bed->no_bed ?? $bed->bad ?? '';
                                    $isIsi = ($bed->status ?? 0) == 1;
                                    $bg = $isIsi ? '#0b3a66' : '#ffffff';
                                    $fg = $isIsi ? '#ffffff' : '#0b3a66';
                                @endphp

                                <div style="width:{{ $colClass }};min-width:60px;">
                                    <div style="padding:10px;border-radius:8px;border:1px solid #cfe2f6;text-align:center;background:{{ $bg }};color:{{ $fg }};font-weight:800;">
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
        <div style="padding:60px;text-align:center;color:#64748b;">
            <div style="font-size:18px;font-weight:700;">Tidak ada data kamar</div>
        </div>
    @endif

</div>


{{-- ✅ Clock + Auto Rotate --}}
<script>
function updateClock(){
    let el = document.getElementById('clock');
    if(el){
        el.innerText = new Date().toLocaleTimeString('id-ID', {hour12:false});
    }
}
setInterval(updateClock, 1000);
updateClock();

// ✅ rotate kamar tiap 10 detik
document.addEventListener('DOMContentLoaded', () => {
    setInterval(() => {
        window.Livewire?.emit('next-room');
    }, 10000);
});
</script>
