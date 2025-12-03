@extends('layout.layoutDashboard')

@section('title', 'Daftar Pasien Fisioterapi')

@section('konten')

<style>
    /* =============================
       FLOATING SEARCH TOP RIGHT
    ============================== */
    .floating-search {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;

        width: 260px;

        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);

        border-radius: 16px;
        padding: 14px;

        box-shadow: 0 4px 16px rgba(0,0,0,0.18);

        transition:
            background 0.25s ease,
            box-shadow 0.25s ease,
            transform 0.25s ease,
            backdrop-filter 0.25s ease;
    }

    .floating-search.active {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow: 0 8px 26px rgba(0,0,0,0.27);
        transform: translateY(3px);
    }

    .floating-title {
        font-weight: 700;
        margin-bottom: 6px;
        font-size: 14px;
        letter-spacing: .5px;
    }

    .search-btn-group button {
        margin-right: 5px;
    }
</style>


{{-- ========================================
    FLOATING SEARCH BAR (TOP RIGHT)
=========================================== --}}
<div class="floating-search" id="floatBox">
    <div class="floating-title">Cari No. RM</div>

    <input type="text" id="searchRM" class="form-control mb-2"
           placeholder="Masukkan No. RM" onkeyup="autoCariRM()">

    <div class="d-flex search-btn-group">

        <button class="btn btn-secondary btn-sm d-flex align-items-center"
                onclick="prevResult()">
            <i class="fas fa-arrow-left me-1"></i>
            Prev
        </button>

        <button class="btn btn-primary btn-sm d-flex align-items-center"
                onclick="nextResult()">
            <i class="fas fa-arrow-right me-1"></i>
            Next
        </button>

        <button class="btn btn-danger btn-sm d-flex align-items-center"
                onclick="resetHighlight()">
            <i class="fas fa-rotate-right me-1"></i>
            Reset
        </button>

    </div>
</div>


<div class="card shadow-sm border-0 rounded-4 mt-5">
    <div class="card-body">

        {{-- ========================================
            FILTER TANGGAL
        ========================================= --}}
        <form method="GET" action="{{ route('fisioterapi.pasien') }}">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="fw-bold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control"
                        value="{{ $tanggalMulai }}">
                </div>

                <div class="col-md-3">
                    <label class="fw-bold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control"
                        value="{{ $tanggalSelesai }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>


        {{-- ========================================
            SEARCH SCRIPT
        ========================================= --}}
        <script>
            let results = [];
            let currentIndex = 0;
            let timeout = null;

            document.addEventListener("DOMContentLoaded", () => {
                const floatBox = document.getElementById("floatBox");
                window.addEventListener("scroll", () => {
                    if (window.pageYOffset > 20) floatBox.classList.add("active");
                    else floatBox.classList.remove("active");
                });
            });

            function autoCariRM() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    goCariRM();
                }, 250);
            }

            function goCariRM() {
                const keyword = document.getElementById("searchRM").value.trim();
                const rows = document.querySelectorAll("table tbody tr");

                rows.forEach(r => r.style.backgroundColor = "");

                results = [];
                if (keyword === "") return;

                rows.forEach(row => {
                    const rm = row.children[2].innerText.trim();
                    if (rm.includes(keyword)) results.push(row);
                });

                if (results.length === 0) return;

                currentIndex = 0;
                highlightAndScroll();
            }

            function highlightAndScroll() {
                document.querySelectorAll("table tbody tr")
                    .forEach(r => r.style.backgroundColor = "");

                if (results.length === 0) return;

                const row = results[currentIndex];
                row.style.backgroundColor = "#FFE07A";

                row.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            }

            function nextResult() {
                if (results.length === 0) return;
                currentIndex = (currentIndex + 1) % results.length;
                highlightAndScroll();
            }

            function prevResult() {
                if (results.length === 0) return;
                currentIndex = (currentIndex - 1 + results.length) % results.length;
                highlightAndScroll();
            }

            function resetHighlight() {
                document.querySelectorAll("table tbody tr")
                    .forEach(r => r.style.backgroundColor = "");

                document.getElementById("searchRM").value = "";
                results = [];
                currentIndex = 0;
            }
        </script>


        {{-- ========================================
            TABLE DATA PASIEN
        ========================================= --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>No. Rawat</th>
                        <th>Tgl Registrasi</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($data as $index => $row)

                        @php
                            // ambil tanggal registrasi
                            $tglReg = $row->tgl_registrasi ?? null;

                            // Cek apakah ada kunjungan untuk no RM & tanggal ini
                            $adaKunjungan = DB::table('fisioterapi_kunjungan')
                                ->where('no_rkm_medis', $row->no_rkm_medis)
                                ->where('tanggal', $tglReg)
                                ->exists();
                        @endphp

                        <tr style="{{ $adaKunjungan ? 'background-color:#ffb3b3 !important;' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->no_rawat }}</td>
                            <td>{{ $row->tgl_registrasi }}</td>
                            <td>{{ $row->no_rkm_medis }}</td>
                            <td class="fw-bold">{{ $row->nm_pasien }}</td>
                            <td>{{ $row->nm_poli }}</td>
                            <td>{{ $row->nm_dokter }}</td>

                            <td>
                                @php
                                    $parts = explode('/', $row->no_rawat);
                                    $tahun = $parts[0] ?? '';
                                    $bulan = $parts[1] ?? '';
                                    $hari  = $parts[2] ?? '';
                                    $norawat = $parts[3] ?? '';
                                @endphp

                                <a href="{{ route('fisioterapi.form', [$tahun, $bulan, $hari, $norawat]) }}"
                                    class="btn btn-info btn-sm"
                                    style="border-radius: 10px; font-weight:600;">
                                    <i class="fas fa-notes-medical"></i>
                                    <i class="fas fa-wheelchair ml-1"></i>
                                    Isi Form
                                </a>

                                @php
                                    $lembar = DB::table('fisioterapi_kunjungan')
                                        ->where('no_rkm_medis', $row->no_rkm_medis)
                                        ->max('lembar') ?? 1;
                                @endphp

                                <a href="{{ route('fisioterapi.print', [$row->no_rkm_medis, $lembar]) }}"
                                    class="btn btn-secondary btn-sm"
                                    style="border-radius:10px;font-weight:600;">
                                    <i class="fas fa-print"></i> Print
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Tidak ada data pasien pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection
