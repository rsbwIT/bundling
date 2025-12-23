@extends('layout.layoutDashboard')

@section('title', 'Daftar Pasien Urologi')

@section('konten')

<style>
    .floating-search{
        position: fixed;
        top:20px;
        right:20px;
        z-index:9999;
        width:260px;
        background:rgba(255,255,255,.45);
        backdrop-filter:blur(12px);
        border-radius:16px;
        padding:14px;
        box-shadow:0 4px 16px rgba(0,0,0,.18);
        transition:.25s ease;
    }
    .floating-search.active{
        background:rgba(255,255,255,.85);
        backdrop-filter:blur(18px);
        box-shadow:0 8px 26px rgba(0,0,0,.27);
        transform:translateY(3px);
    }
    .highlight{background:#FFE07A!important}

    /* style tombol form  */
    .btn-usg-3d{
        padding:10px 18px;
        font-weight:800;
        font-size:13px;
        letter-spacing:.4px;

        color:#fff;
        text-decoration:none;

        border:none;
        border-radius:14px;

        background:linear-gradient(
            to bottom,
            #43d17a 0%,
            #28a745 60%,
            #1e7e34 100%
        );

        text-shadow:0 1px 2px rgba(0,0,0,.4);

        box-shadow:
            0 5px 0 #1c7430,
            0 8px 18px rgba(0,0,0,.3);

        transition:all .15s ease-in-out;
        display:inline-block;
    }

    .btn-usg-3d:hover{
        background:linear-gradient(
            to bottom,
            #5fe08f 0%,
            #34ce57 60%,
            #1e7e34 100%
        );
        transform:translateY(1px);
        color:#fff;
    }

    .btn-usg-3d:active{
        transform:translateY(5px);
        box-shadow:
            0 2px 0 #1c7430,
            0 5px 10px rgba(0,0,0,.3);
    }

    /* print  */

    .btn-print-3d{
        padding:10px 16px;
        font-weight:800;
        font-size:13px;
        letter-spacing:.4px;

        color:#fff;
        text-decoration:none;

        border:none;
        border-radius:14px;

        background:linear-gradient(
            to bottom,
            #ffb74d 0%,
            #ff9800 60%,
            #fb8c00 100%
        );

        text-shadow:0 1px 2px rgba(0,0,0,.45);

        box-shadow:
            0 5px 0 #ef6c00,
            0 8px 18px rgba(0,0,0,.35);

        transition:all .15s ease-in-out;
        display:inline-block;
    }

    .btn-print-3d:hover{
        background:linear-gradient(
            to bottom,
            #ffcc80 0%,
            #ffa726 60%,
            #fb8c00 100%
        );
        transform:translateY(1px);
        color:#fff;
    }

    .btn-print-3d:active{
        transform:translateY(5px);
        box-shadow:
            0 2px 0 #ef6c00,
            0 5px 12px rgba(0,0,0,.35);
    }
</style>

{{-- FLOATING SEARCH --}}
<div class="floating-search" id="floatBox">
    <b>Cari No RM / Nama</b>
    <input type="text" id="searchRM" class="form-control my-2"
           placeholder="No RM / Nama" onkeyup="autoCariRM()">
    <div class="d-flex gap-1">
        <button class="btn btn-secondary btn-sm" onclick="prevResult()">Prev</button>
        <button class="btn btn-primary btn-sm" onclick="nextResult()">Next</button>
        <button class="btn btn-danger btn-sm" onclick="resetHighlight()">Reset</button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mt-5">
<div class="card-body">
{{-- FILTER TANGGAL --}}
<form method="GET" action="{{ url('/urologi') }}">
    <div class="row g-3 mb-3 align-items-end">

        <div class="col-md-3">
            <label class="form-label fw-semibold">Tanggal Mulai</label>
            <input type="date"
                   name="tanggal_mulai"
                   class="form-control"
                   value="{{ $tanggalMulai ?? date('Y-m-d') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Tanggal Selesai</label>
            <input type="date"
                   name="tanggal_selesai"
                   class="form-control"
                   value="{{ $tanggalSelesai ?? date('Y-m-d') }}">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                Filter
            </button>
        </div>

    </div>
</form>


{{-- TABLE --}}
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="table-primary">
<tr>
    <th>No</th>
    <th>No Rawat</th>
    <th>Tgl Registrasi</th>
    <th>No RM</th>
    <th>Nama Pasien</th>
    <th>Poli</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
@forelse($data as $i => $row)
<tr>
    <td>{{ $i+1 }}</td>
    <td>{{ $row->no_rawat }}</td>
    <td>{{ $row->tgl_registrasi }}</td>
    <td>{{ $row->no_rkm_medis }}</td>
    <td class="fw-bold">{{ $row->nm_pasien }}</td>
    <td>{{ $row->nm_poli }}</td>
    <td>
        <a href="/form_usg?no_rawat={{ $row->no_rawat }}"
            class="btn btn-usg-3d btn-sm">
                🩺 Isi Form USG
        </a>

        <a href="/urologi/usg/cetak/{{ $row->no_rawat }}"
            class="btn btn-print-3d btn-sm"
                target="_blank">
                 🖨️ Cetak
        </a>

    </td>


</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">
        Tidak ada data pasien
    </td>
</tr>
@endforelse
</tbody>
</table>
</div>

</div>
</div>

{{-- SEARCH SCRIPT --}}
<script>
let results=[],currentIndex=0,timeout=null;

function autoCariRM(){
    clearTimeout(timeout);
    timeout=setTimeout(goCariRM,250);
}

function goCariRM(){
    const key=document.getElementById("searchRM").value.toLowerCase();
    const rows=document.querySelectorAll("tbody tr");
    results=[];
    rows.forEach(r=>{
        r.classList.remove("highlight");
        if(!key) return;
        const rm=r.children[3].innerText.toLowerCase();
        const nm=r.children[4].innerText.toLowerCase();
        if(rm.includes(key)||nm.includes(key)) results.push(r);
    });
    currentIndex=0;
    highlight();
}

function highlight(){
    document.querySelectorAll("tbody tr").forEach(r=>r.classList.remove("highlight"));
    if(!results.length) return;
    results[currentIndex].classList.add("highlight");
    results[currentIndex].scrollIntoView({behavior:"smooth",block:"center"});
}

function nextResult(){if(results.length){currentIndex=(currentIndex+1)%results.length;highlight();}}
function prevResult(){if(results.length){currentIndex=(currentIndex-1+results.length)%results.length;highlight();}}
function resetHighlight(){
    document.getElementById("searchRM").value="";
    document.querySelectorAll("tbody tr").forEach(r=>r.classList.remove("highlight"));
    results=[];
}
</script>

@endsection
