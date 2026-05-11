@extends('..layout.layoutDashboard')
@section('title', 'Pasien COB (Harian)')

@section('konten')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="card">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ url('cob-harian') }}" method="GET">
    @csrf

    <style>
        .form-control-sm,
        .btn-sm {
            height: 38px;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-check-inline {
            margin-right: 15px;
        }

        .filter-radio-wrapper {
            height: 38px;
            display: flex;
            align-items: center;
        }

        .form-check {
            margin-bottom: 0;
        }

        .form-check-input {
            margin-top: 0.1rem;
        }

        .border-top-custom {
            border-top: 1px solid #e9ecef;
        }

        /* Rapihkan input date */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: .7;
        }

        input[type="date"]::-webkit-datetime-edit {
            color: #495057;
        }
    </style>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <!-- FILTER -->
            <div class="row">

    <!-- Cari -->
    <div class="col-md-4">
        <div class="form-group">
            <label class="small font-weight-bold mb-1">
                Cari Data
            </label>

            <input type="text"
                name="cariNomor"
                class="form-control form-control-sm"
                placeholder="Nama / RM / No Rawat"
                value="{{ request('cariNomor') }}">
        </div>
    </div>

    <!-- Status -->
    <div class="col-md-2">
        <div class="form-group">
            <label class="small font-weight-bold mb-1">
                Status
            </label>

            <select class="form-control form-control-sm"
                name="stsLanjut">

                <option value="Ralan"
                    {{ request('stsLanjut') == 'Ralan' ? 'selected' : '' }}>
                    Rawat Jalan
                </option>

                <option value="Ranap"
                    {{ request('stsLanjut') == 'Ranap' ? 'selected' : '' }}>
                    Rawat Inap
                </option>

            </select>
        </div>
    </div>

    <!-- Filter Radio -->
    <div class="col-md-3">
        <div class="form-group">
            <label class="small font-weight-bold mb-1 d-block">
                Filter Tanggal
            </label>

            <div class="filter-radio-wrapper d-flex align-items-center" style="height:38px;">

                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input"
                        type="radio"
                        name="filter_type"
                        id="filter_tempo"
                        value="tempo"
                        {{ request('filter_type', 'tempo') == 'tempo' ? 'checked' : '' }}>

                    <label class="form-check-label small"
                        for="filter_tempo">
                        Tanggal
                    </label>
                </div>

                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input"
                        type="radio"
                        name="filter_type"
                        id="filter_lunas"
                        value="lunas"
                        {{ request('filter_type') == 'lunas' ? 'checked' : '' }}>

                    <label class="form-check-label small"
                        for="filter_lunas">
                        Tgl Lunas
                    </label>
                </div>

            </div>
        </div>
    </div>

    <!-- Button -->
    <div class="col-md-3">
        <div class="form-group">
            <label class="d-block mb-1">&nbsp;</label>

            <button type="submit"
                class="btn btn-primary btn-sm btn-block"
                style="height:38px;">
                <i class="fa fa-search mr-1"></i>
                Cari
            </button>
        </div>
    </div>

</div>

<!-- GARIS -->
<div class="border-top-custom my-3"></div>

<!-- FLATPICKR -->
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- FILTER TANGGAL -->
<div class="d-flex align-items-center flex-wrap">

    <div class="form-group mb-0 mr-2" style="width:230px;">
        <input type="text"
            class="form-control form-control-sm tanggal"
            placeholder="Pilih Tanggal Nota"
            value="{{ request('tgl1') }}">
    </div>

    <span class="font-weight-bold mx-2">s.d</span>

    <div class="form-group mb-0 mr-5" style="width:230px;">
        <input type="text"
            class="form-control form-control-sm tanggal"
            placeholder="Pilih Tanggal Nota"
            value="{{ request('tgl2') }}">
    </div>

    <div class="form-group mb-0 ml-4 mr-2" style="width:230px;">
        <input type="text"
            class="form-control form-control-sm tanggal"
            placeholder="Pilih Tanggal Lunas"
            value="{{ request('tgl_lunas1') }}">
    </div>

    <span class="font-weight-bold mx-2">s.d</span>

    <div class="form-group mb-0">
        <input type="text"
            name="tgl_lunas2"
            class="form-control form-control-sm tanggal"
            placeholder="Pilih Tanggal Lunas"
            value="{{ request('tgl_lunas2') }}">
    </div>

</div>

<script>
    flatpickr(".tanggal", {
        dateFormat: "Y-m-d",
        disableMobile: true
    });
</script>

            Jumlah Data : {{ count($getCobHarian) }}

            <div class="row no-print mb-2">

                <div class="col-12">

                    <button type="button"
                        class="btn btn-info btn-sm"
                        onclick="toggleNominal()">

                        <i class="fas fa-eye"></i>
                        Hide / Show Nominal

                    </button>

                    <button type="button"
                        class="btn btn-default float-right"
                        id="copyButton">

                        <i class="fas fa-copy"></i>
                        Copy table

                    </button>

                </div>

            </div>

            <table class="table table-sm table-bordered table-responsive text-xs mb-3"
                style="white-space: nowrap;"
                id="tableToCopy">

                <thead>
                    <tr>
                        <th>No Rawat</th>
                        <th>Nama Pasien</th>
                        <th>Nama Poli</th>
                        <th>Kode Dokter</th>
                        <th>Nama Dokter</th>
                        <th>Input COB</th>
                        <th>Nomor Nota</th>

                        <th class="kolom-nominal">Registrasi</th>
                        <th class="kolom-nominal">Obat+Emb+Tsl</th>
                        <th class="kolom-nominal">Retur Oabt</th>
                        <th class="kolom-nominal">Resep Pulang</th>
                        <th class="kolom-nominal">Paket Tindakan</th>
                        <th class="kolom-nominal">Operasi</th>
                        <th class="kolom-nominal">Laborat</th>
                        <th class="kolom-nominal">Radiologi</th>
                        <th class="kolom-nominal">Tambahan</th>
                        <th class="kolom-nominal">Kamar+Service</th>
                        <th class="kolom-nominal">Potongan</th>
                        <th class="kolom-nominal">Total</th>

                        <th class="text-center" colspan="4">Penjamin</th>
                        <th>Dibayar Asuransi</th>
                        <th>Selisih Dibayar</th>
                        <th>Akun Bayar</th>
                        <th>Tanggal Lunas</th>
                    </tr>
                </thead>

                @forelse ($getCobHarian as $item)
                    <tr>
                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->nm_poli }}</td>
                        <td>{{ $item->kd_dokter }}</td>
                        <td>{{ $item->nm_dokter }}</td>

                        <td class="text-center">
                            <button type="button"
                                class="btn btn-success btn-xs"
                                data-toggle="modal"
                                data-target="#modalCob"
                                onclick="setCobData('{{ $item->no_rawat }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>

                        <td>
                            @foreach ($item->getNomorNota as $detail)
                                {{ str_replace(':', '', $detail->nm_perawatan) }}
                            @endforeach
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getRegistrasi->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getObat->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getReturObat->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getResepPulang->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{
                                number_format(
                                    $item->getRalanDokter->sum('totalbiaya') +
                                    $item->getRalanParamedis->sum('totalbiaya') +
                                    $item->getRalanDrParamedis->sum('totalbiaya') +
                                    $item->getRanapDokter->sum('totalbiaya') +
                                    $item->getRanapDrParamedis->sum('totalbiaya') +
                                    $item->getRanapParamedis->sum('totalbiaya'),
                                0, '.', ',')
                            }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getOprasi->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getLaborat->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getRadiologi->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getTambahan->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getKamarInap->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right kolom-nominal">
                            {{ number_format($item->getPotongan->sum('totalbiaya'), 0, '.', ',') }}
                        </td>

                        <td class="text-right text-bold kolom-nominal">
                            {{
                                number_format(
                                    $item->getRegistrasi->sum('totalbiaya') +
                                    $item->getObat->sum('totalbiaya') +
                                    $item->getReturObat->sum('totalbiaya') +
                                    $item->getResepPulang->sum('totalbiaya') +
                                    $item->getRalanDokter->sum('totalbiaya') +
                                    $item->getRalanParamedis->sum('totalbiaya') +
                                    $item->getRalanDrParamedis->sum('totalbiaya') +
                                    $item->getRanapDokter->sum('totalbiaya') +
                                    $item->getRanapDrParamedis->sum('totalbiaya') +
                                    $item->getRanapParamedis->sum('totalbiaya') +
                                    $item->getOprasi->sum('totalbiaya') +
                                    $item->getLaborat->sum('totalbiaya') +
                                    $item->getRadiologi->sum('totalbiaya') +
                                    $item->getTambahan->sum('totalbiaya') +
                                    $item->getKamarInap->sum('totalbiaya') +
                                    $item->getPotongan->sum('totalbiaya'),
                                0, '.', ',')
                            }}
                        </td>

                        @foreach ($item->getPenjabCOB as $penjab)
                            <td>{{ $penjab->png_jawab }}</td>
                            <td class="text-right">
                                {{ number_format($penjab->totalpiutang, 0, '.', ',') }}
                            </td>
                        @endforeach

                        <td class="text-right">
                            {{ number_format($item->getLunasCob->nominal_cob ?? 0, 0, '.', ',') }}
                        </td>

                        <td class="text-right">
                            @php
                                $totalPenjab = $item->getPenjabCOB->where('png_jawab', '!=', 'BPJS')->where('png_jawab', '!=', 'ASR - JAMSOSTEK')->sum('totalpiutang');
                                $dibayarCob = $item->getLunasCob->nominal_cob ?? 0;
                            @endphp
                            {{ number_format($totalPenjab - $dibayarCob, 0, '.', ',') }}
                        </td>

                        <td>
                            {{ $item->getLunasCob->akun_bayar ?? '' }}
                        </td>

                        <td>
                            {{ $item->getLunasCob->tgl_lunas ?? '' }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="20" class="text-center">
                            Silahkan Cari Data
                        </td>
                    </tr>
                @endforelse

            </table>

        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalCob">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ url('simpan-cob') }}" method="POST" id="formCob">
                    @csrf

                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">Input COB</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>No Rawat</label>
                            <input type="text"
                                name="no_rawat"
                                id="modal_no_rawat"
                                class="form-control"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Bayar Perusahaan</label>

                            <div class="input-group">
                                <input type="text"
                                    name="tgl_lunas"
                                    id="tgl_lunas"
                                    class="form-control"
                                    inputmode="numeric"
                                    required>

                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Nominal Dibayar Asuransi</label>
                            <input type="text"
                                name="nominal_cob"
                                id="nominal_cob"
                                class="form-control text-right"
                                inputmode="numeric"
                                onkeyup="formatRibuan(this)"
                                onkeypress="return hanyaAngka(event)"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="akun_bayar">Akun Bayar</label>

                            <select name="akun_bayar"
                                id="akun_bayar"
                                class="form-control"
                                required>

                                <option value="" selected disabled hidden>
                                    Pilih Akun Bayar
                                </option>

                                @if(isset($akunBayar))
                                    @foreach($akunBayar as $akun)
                                        <option value="{{ $akun->nama_bayar }}">
                                            {{ $akun->nama_bayar }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Tutup
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>

        // Fungsi untuk hanya menerima angka
        function hanyaAngka(e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9]/.test(char)) {
                e.preventDefault();
                return false;
            }
        }

        // Fungsi format angka dengan pemisah ribuan
        function formatRibuan(input) {
            let value = input.value.replace(/\D/g, '');
            input.value = new Intl.NumberFormat('id-ID').format(value);
        }

        // Fungsi hapus format sebelum submit
        function removeFormat(input) {
            return input.value.replace(/\D/g, '');
        }

        // Initialize Flatpickr untuk date picker
        flatpickr("#tgl_lunas", {
            dateFormat: "d/m/Y",
            altFormat: "d/m/Y",
            altInput: false,
            locale: "id"
        });

        // Validasi format tanggal dd/mm/yyyy
        document.getElementById('tgl_lunas').addEventListener('blur', function(){
            const value = this.value.trim();
            const regex = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/(19|20)\d{2}$/;
            
            if(value && !regex.test(value)){
                alert('Format tanggal tidak valid. Gunakan format dd/mm/yyyy');
                this.focus();
                this.value = '';
            }
        });

        // Hapus format nominal sebelum submit
        document.getElementById('formCob').addEventListener('submit', function(e) {
            const nominalInput = document.getElementById('nominal_cob');
            nominalInput.value = removeFormat(nominalInput);
        });

        function setCobData(no_rawat){
            document.getElementById('modal_no_rawat').value = no_rawat;
            document.getElementById('nominal_cob').value = '';
            document.getElementById('tgl_lunas').value = '';
        }

        function toggleNominal(){
            document.querySelectorAll('.kolom-nominal').forEach(function(el){
                el.classList.toggle('d-none');
            });
        }

        document.getElementById("copyButton").addEventListener("click", function(){
            copyTableToClipboard("tableToCopy");
        });

        function copyTableToClipboard(tableId){
            const table = document.getElementById(tableId);
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);

            try{
                document.execCommand("copy");
                window.getSelection().removeAllRanges();
                alert("Tabel telah berhasil disalin ke clipboard.");
            }catch(err){
                console.error("Tidak dapat menyalin tabel:", err);
            }
        }

        function updateFilterState() {
            const filterType = document.querySelector('input[name="filter_type"]:checked').value;
            const tgl1 = document.querySelector('input[name="tgl1"]');
            const tgl2 = document.querySelector('input[name="tgl2"]');
            const tglLunas1 = document.querySelector('input[name="tgl_lunas1"]');
            const tglLunas2 = document.querySelector('input[name="tgl_lunas2"]');

            if (filterType === 'tempo') {
                tgl1.disabled = false;
                tgl2.disabled = false;
                tglLunas1.disabled = true;
                tglLunas2.disabled = true;
            } else {
                tgl1.disabled = true;
                tgl2.disabled = true;
                tglLunas1.disabled = false;
                tglLunas2.disabled = false;
            }
        }

        document.querySelectorAll('input[name="filter_type"], input[name="tgl1"], input[name="tgl2"], input[name="tgl_lunas1"], input[name="tgl_lunas2"]').forEach(function(input) {
            input.addEventListener('change', updateFilterState);
        });

        updateFilterState();
    </script>

@endsection