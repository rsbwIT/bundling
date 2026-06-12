@extends('..layout.layoutDashboard')

@section('konten')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="fp-wrapper">
    <div class="fp-card">
        <div class="fp-header">
            <h3>VERIFIKASI BPJS</h3>
            <span id="deviceBadge" class="badge bg-warning">Mengecek Alat...</span>
        </div>
        <div class="fp-body">
            <input type="text" id="no_kartu" class="form-control text-center" placeholder="Scan kartu di sini..." autofocus autocomplete="off">
            <div id="hasil" style="display:none; margin-top:20px;">
                <h2 id="card_number" class="text-center"></h2>
                <div id="fingerStatus" class="alert alert-info text-center">Menunggu...</div>
            </div>
        </div>
    </div>
</div>



<script>
const input = document.getElementById('no_kartu');
const badge = document.getElementById('deviceBadge');

// 1. Fungsi Cek Device dengan Timestamp (Agar tidak kena cache browser)
async function checkDevice() {
    try {
        const timestamp = new Date().getTime();
        const res = await fetch('/bpjs/fingerprint/check-device?t=' + timestamp);
        const data = await res.json();
        
        badge.className = data.success ? 'badge bg-success' : 'badge bg-danger';
        badge.innerText = data.success ? 'Alat Aktif' : 'Alat Offline';
    } catch(e) { 
        badge.className = 'badge bg-danger'; 
        badge.innerText = 'Alat Offline'; 
    }
}

// Cek lebih responsif (setiap 2 detik)
setInterval(checkDevice, 2000); 
checkDevice();

// 2. Memastikan kursor selalu fokus di input (PENTING untuk scanner)
setInterval(() => {
    if (document.activeElement !== input) input.focus();
}, 1000);

// ... (kode event listener input Anda tetap sama)
</script>
@endsection