<div style="background:#ffffff; min-height:100vh;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  <div class="container-fluid py-4 px-4">
    <div class="row justify-content-center g-4" wire:poll.1000ms>
      @php
        $xl = 4; $lg = 6; $md = 12; $sm = 12;
      @endphp

      @foreach ($getLoket as $loket)
      <div class="col-sm-{{ $sm }} col-md-{{ $md }} col-lg-{{ $lg }} col-xl-{{ $xl }}">
        <div class="loket-box shadow-lg rounded-4 border-0 h-100">

          <!-- Header Loket -->
          <div class="loket-header text-white text-center py-3 rounded-top-4">
            <h4 class="fw-bold mb-0">{{ $loket->nama_loket }}</h4>
          </div>

          <!-- Body Loket -->
          <div class="p-3 p-md-4 rounded-bottom-4">
            @if ($loket->getPasien->isEmpty())
              <div class="text-center text-muted py-5">
                <i class="bi bi-person-x display-2 mb-3"></i>
                <p class="fs-5 fw-semibold">Tidak Ada Antrian</p>
              </div>
            @else
              @foreach ($loket->getPasien as $index => $pasien)
              @php $isActive = $index === 0; @endphp
              <div class="antrian-card {{ $isActive ? 'active' : '' }}">
                <div class="no-antrian">
                  {{ str_pad($pasien->no_reg, 3, '0', STR_PAD_LEFT) }}
                </div>
                <div class="info">
                  <div class="nama">{{ strtoupper($pasien->nm_pasien) }}</div>
                  <div class="dokter">{{ $pasien->nama_dokter }}</div>
                  <div class="jam">Jam Mulai:
                    {{ \Carbon\Carbon::parse($pasien->jam_mulai)->format('H:i') }}
                  </div>
                </div>
              </div>
              @endforeach
            @endif
          </div>

        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<style>
body{background:#ffffff;margin:0;padding:0;}
.container-fluid{max-width:98%;}
.loket-header{
  background:linear-gradient(135deg,#2e7d32,#43a047);
  box-shadow:inset 0 -2px 6px rgba(0,0,0,0.15);
}
.loket-box{
  max-width:400px;
  margin:0 auto;
}
.antrian-card{
  background:#e8f5e9;
  border-radius:12px;
  padding:16px;
  display:flex;
  align-items:center;
  gap:16px;
  margin-bottom:15px;
  box-shadow:0 4px 12px rgba(76,175,80,0.25);
}
.antrian-card.active{
  background:#c8e6c9;
  box-shadow:0 0 24px 4px #2e7d32;
}
.no-antrian{
  background:#2e7d32;
  color:#fff;
  font-weight:700;
  font-size:1.8rem;
  width:70px;
  height:70px;
  border-radius:50%;
  display:flex;
  justify-content:center;
  align-items:center;
  flex-shrink:0;
}
.info{flex:1;}
.nama{
  font-size:1.2rem;
  font-weight:700;
  margin-bottom:6px;
}
.dokter{
  background:#208496;
  color:#fff;
  font-weight:600;
  padding:4px 10px;
  border-radius:8px;
  display:inline-block;
  margin-bottom:6px;
}
.jam{
  font-weight:600;
  color:#333;
}
@media (max-width:768px){
  .no-antrian{width:60px;height:60px;font-size:1.5rem;}
  .nama{font-size:1.05rem;}
}
</style>
