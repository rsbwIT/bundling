@extends('layout.layoutDashboard')

@section('title','Form USG')

@section('konten')

<style>
/* Global */

.usg-wrapper{
    min-height:calc(100vh - 110px);
}

.usg-card{
    border-radius:22px;
    box-shadow:0 18px 45px rgba(0,0,0,.22);
    border:none;
    height:100%;
}

/* header */

.usg-header{
    font-weight:800;
    letter-spacing:.6px;
}

.usg-subtitle{
    font-size:14px;
    color:#6c757d;
}

/* pasien  */

.pasien-box{
    background:linear-gradient(135deg,#f8f9fa,#ffffff);
    border-radius:18px;
    padding:22px;
    border-left:7px solid #0d6efd;
}

.pasien-label{
    font-size:12px;
    color:#6c757d;
}

.pasien-value{
    font-weight:700;
    font-size:16px;
}

/* Form */

.usg-label{
    font-weight:700;
    font-size:15px;
}

.usg-textarea{
    border-radius:18px;
    resize:vertical;
    font-size:16px;
    padding:22px;
    min-height:420px;
    line-height:1.7;
    box-shadow:inset 0 3px 10px rgba(0,0,0,.06);
}

.usg-textarea:focus{
    border-color:#0d6efd;
    box-shadow:0 0 0 .2rem rgba(13,110,253,.15);
}

/* tombol */

.btn-back{
    border-radius:16px;
    padding:12px 26px;
    font-weight:600;
    transition:.25s ease;
}

.btn-back:hover{
    transform:translateX(-4px);
}

/* save tombol */

.btn-save{
    padding:14px 44px;
    font-weight:800;
    font-size:15px;
    letter-spacing:.6px;

    color:#fff;
    border:none;
    border-radius:18px;

    background:linear-gradient(
        to bottom,
        #6ec6ff 0%,
        #2196f3 60%,
        #1e88e5 100%
    );

    text-shadow:0 1px 2px rgba(0,0,0,.45);

    box-shadow:
        0 6px 0 #0a58ca,
        0 10px 22px rgba(0,0,0,.35);

    transition:all .15s ease-in-out;
}

.btn-save:hover{
    background:linear-gradient(
        to bottom,
        #81d4fa 0%,
        #42a5f5 60%,
        #1e88e5 100%
    );
    transform:translateY(1px);
}

.btn-save:active{
    transform:translateY(6px);
    box-shadow:
        0 2px 0 #0a58ca,
        0 5px 12px rgba(0,0,0,.35);
}

/* style notif */

.wa-toast{
    position:fixed;
    top:24px;
    right:24px;
    z-index:9999;

    display:flex;
    align-items:center;
    gap:14px;

    min-width:280px;
    max-width:360px;

    padding:14px 18px;
    border-radius:14px;

    background:#fff;
    box-shadow:0 12px 30px rgba(0,0,0,.18);

    animation:slideIn .4s ease, fadeOut .5s ease 2.7s forwards;
}

.wa-success{
    border-left:6px solid #25d366;
}

.wa-error{
    border-left:6px solid #e53935;
}

.wa-icon{
    width:38px;
    height:38px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;

    font-weight:900;
    font-size:18px;
    color:#fff;
}

.wa-success .wa-icon{
    background:#25d366;
}

.wa-error .wa-icon{
    background:#e53935;
}

.wa-title{
    font-weight:800;
    font-size:14px;
}

.wa-text{
    font-size:13px;
    color:#555;
}

/* Animations */
@keyframes slideIn{
    from{
        opacity:0;
        transform:translateX(40px);
    }
    to{
        opacity:1;
        transform:translateX(0);
    }
}

@keyframes fadeOut{
    to{
        opacity:0;
        transform:translateX(40px);
    }
}

</style>

<div class="container-fluid px-4 usg-wrapper">
<div class="row h-100">
<div class="col-12 h-100">

<div class="card usg-card">
<div class="card-body p-4 d-flex flex-column h-100">

    {{-- Notif --}}

    @if (session('success'))
        <div class="wa-toast wa-success" id="waToast">
            <div class="wa-icon">✓</div>
            <div class="wa-content">
                <div class="wa-title">Berhasil</div>
                <div class="wa-text">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="wa-toast wa-error" id="waToast">
            <div class="wa-icon">!</div>
            <div class="wa-content">
                <div class="wa-title">Gagal</div>
                <div class="wa-text">{{ session('error') }}</div>
            </div>
        </div>
    @endif



    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="usg-header mb-1">🩺 Form USG Urologi</h4>
        <div class="usg-subtitle">
            Pemeriksaan Ultrasonografi
        </div>
    </div>

    {{-- PASIEN --}}
    <div class="pasien-box mb-4">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="pasien-label">No Rawat</div>
                <div class="pasien-value">
                    {{ $pasien->no_rawat ?? '-' }}
                </div>
            </div>

            <div class="col-md-3">
                <div class="pasien-label">No Rekam Medis</div>
                <div class="pasien-value">
                    {{ $pasien->no_rkm_medis ?? '-' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="pasien-label">Nama Pasien</div>
                <div class="pasien-value">
                    {{ $pasien->nm_pasien ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form method="POST"
        action="{{ route('urologi.usg.simpan') }}"
        class="d-flex flex-column flex-grow-1">
        @csrf

        <input type="hidden" name="no_rawat"
            value="{{ $pasien->no_rawat ?? '' }}">

        <input type="hidden" name="no_rkm_medis"
            value="{{ $pasien->no_rkm_medis ?? '' }}">

        <div class="flex-grow-1 mb-4">
            <label class="usg-label mb-2">
                Hasil Pemeriksaan USG Urologi
            </label>

            <textarea name="hasil_usg"
                    class="form-control usg-textarea"
                    required
                    placeholder="Contoh:
    - Ginjal kanan: ukuran dan ekogenitas normal 
    - Ginjal kiri: tampak batu ukuran ... 
    - Vesika urinaria: dinding normal, tidak tampak massa 
    - Kesimpulan: ..."></textarea>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <a href="/urologi" class="btn btn-light btn-back">
                ← Kembali
            </a>

            <button type="submit" class="btn btn-save">
                Simpan Hasil USG
            </button>
        </div>
    </form>


</div>

<script>
setTimeout(() => {
    const toast = document.getElementById('waToast');
    if(toast){
        toast.remove();
    }
}, 3200);
</script>

</div>

</div>
</div>
</div>



@endsection
