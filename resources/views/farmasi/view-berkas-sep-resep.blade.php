@extends('..layout.layoutDashboard')
@section('title', 'Farmasi')

@section('konten')
<div class="row">

    {{-- UPLOAD BERKAS --}}
    @include('farmasi.component.upload-berkas-farmasi')

    {{-- ===================== --}}
    {{-- BUTTON GABUNG / DOWNLOAD --}}
    {{-- ===================== --}}
    <div class="col-md-12 mb-3">
        <div class="d-flex justify-content-center align-items-center">

            {{-- TOMBOL GABUNG --}}
            @if ($cekBerkas?->contains('jenis_berkas', 'FILE-SCAN-FARMASI') &&
                 $cekBerkas?->contains('jenis_berkas', 'SEP-RESEP'))
                <form action="{{ url('gabung-berkas-farmasi') }}" method="POST">
                    @csrf
                    <input name="no_rawat" value="{{ $getPasien->no_rawat ?? '' }}" hidden>
                    <button type="submit" class="btn btn-outline-primary btn-flat">
                        <i class="fas fa-arrow-up"></i> Gabungkan <i class="fas fa-arrow-down"></i>
                    </button>
                </form>
            @endif

            {{-- TOMBOL DOWNLOAD HASIL --}}
            @if ($cekBerkas?->contains('jenis_berkas', 'HASIL-FARMASI'))
                <form action="{{ url('/download-hasilgabungberks') }}" method="GET" class="ml-2">
                    @csrf
                    <input name="no_rawat" value="{{ $getPasien->no_rawat ?? '' }}" hidden>
                    <input name="tgl1" value="{{ session('tgl1') }}" hidden>
                    <input name="tgl2" value="{{ session('tgl2') }}" hidden>
                    <input name="cariNomor" value="{{ session('cariNomor') }}" hidden>
                    <button id="ButtonDownloadHASIL" class="btn btn-outline-primary btn-flat">
                        <i class="fas fa-download"></i>
                    </button>
                </form>
            @endif

        </div>
    </div>

    {{-- ===================== --}}
    {{-- BERKAS KHANZA --}}
    {{-- ===================== --}}
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Berkas Khanza</h3>
            </div>

            <div class="card-body">

                @if ($jumlahData > 0)

                    <div class="card-body">
                        <div class="btn-group float-right">

                            {{-- SAVE PDF --}}
                            <form action="{{ url('/print-sep-resep') }}" method="GET">
                                @csrf
                                <input name="cariNoRawat" value="{{ $noRawat ?? '' }}" hidden>
                                <input name="cariNoSep" value="{{ $noSep ?? '' }}" hidden>
                                <button type="submit" class="btn btn-primary btn-flat">
                                    <i class="fas fa-save"></i> Save PDF
                                </button>
                            </form>

                            {{-- DOWNLOAD SEP-RESEP --}}
                            @if ($cekBerkas?->contains('jenis_berkas', 'SEP-RESEP'))
                                <form action="{{ url('/download-sepresep-farmasi') }}" method="GET" class="ml-2">
                                    @csrf
                                    <input name="no_rawat" value="{{ $getPasien->no_rawat ?? '' }}" hidden>
                                    <input name="tgl1" value="{{ session('tgl1') }}" hidden>
                                    <input name="tgl2" value="{{ session('tgl2') }}" hidden>
                                    <input name="cariNomor" value="{{ session('cariNomor') }}" hidden>
                                    <button id="ButtonDownloadSEPRESEP" class="btn btn-outline-primary btn-flat">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>

                    {{-- ========================= --}}
                    {{-- INCLUDE BERKAS-berkas --}}
                    {{-- ========================= --}}

                    {{-- SEP --}}
                    @if (!empty($getSEP))
                        @include('farmasi.component.berkas-sep')
                    @endif

                    {{-- RESEP KRONIS
                    @if (!empty($resepKronis))
                        @include('farmasi.component.berkas-kronis')
                    @endif --}}

                    {{-- RESEP BIASA --}}
                    @if (!empty($berkasResep))
                        @include('farmasi.component.berkas-resep')
                    @endif

                    {{-- LAB --}}
                    @if (!empty($getLaborat))
                        @include('farmasi.component.berkas-lab')
                    @endif

                @else
                    <div class="card-body">
                        <div class="card p-4 d-flex justify-content-center align-items-center text-muted">
                            Tidak ada berkas ditemukan.
                        </div>
                    </div>
                @endif

                <div class="card-footer"></div>
            </div>
        </div>
    </div>
</div>


{{-- ===================== --}}
{{-- JS Redirect (aman) --}}
{{-- ===================== --}}
<script>
    function redirectList() {
        var cariNomor = encodeURIComponent('{{ session('cariNomor') }}');
        var tgl1 = encodeURIComponent('{{ session('tgl1') }}');
        var tgl2 = encodeURIComponent('{{ session('tgl2') }}');

        window.location.href = '/cari-list-pasien-farmasi?cariNomor=' + cariNomor + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    }

    document.getElementById('ButtonDownloadSEPRESEP')?.addEventListener('click', function() {
        setTimeout(redirectList, 2000);
    });

    document.getElementById('ButtonDownloadHASIL')?.addEventListener('click', function() {
        setTimeout(redirectList, 2000);
    });
</script>

@endsection
