@extends('..layout.layoutDashboard')
@section('title', 'Pasien COB (Harian)')

@section('konten')

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>

    .form-control-sm,
    .btn-sm{
        height:38px;
        font-size:13px;
    }

    .form-group{
        margin-bottom:0;
    }

    .form-check-inline{
        margin-right:15px;
    }

    .filter-radio-wrapper{
        height:38px;
        display:flex;
        align-items:center;
    }

    .form-check{
        margin-bottom:0;
    }

    .form-check-input{
        margin-top:0.1rem;
    }

    .border-top-custom{
        border-top:1px solid #e9ecef;
    }

    .table-responsive{
        overflow:auto;
    }

    table td,
    table th{
        white-space:nowrap;
        vertical-align:middle !important;
    }

</style>

<div class="card">

    <div class="card-body">

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button type="button"
                    class="close"
                    data-dismiss="alert">

                    <span>&times;</span>

                </button>

            </div>

        @endif

        @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show">

                {{ session('error') }}

                <button type="button"
                    class="close"
                    data-dismiss="alert">

                    <span>&times;</span>

                </button>

            </div>

        @endif

        <form action="{{ url('cob-harian') }}"
            method="GET">

            @csrf

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="row">

                        {{-- CARI --}}
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

                        {{-- STATUS --}}
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

                        {{-- FILTER --}}
                        <div class="col-md-3">

                            <div class="form-group">

                                <label class="small font-weight-bold mb-1 d-block">
                                    Filter Tanggal
                                </label>

                                <div class="filter-radio-wrapper">

                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input"
                                            type="radio"
                                            name="filter_type"
                                            id="filter_tempo"
                                            value="tempo"
                                            {{ request('filter_type','tempo') == 'tempo' ? 'checked' : '' }}>

                                        <label class="form-check-label small"
                                            for="filter_tempo">

                                            Tanggal Nota

                                        </label>

                                    </div>

                                    <div class="form-check form-check-inline">

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

                        {{-- BUTTON --}}
                        <div class="col-md-3">

                            <div class="form-group">

                                <label class="d-block mb-1">&nbsp;</label>

                                <button type="submit"
                                    class="btn btn-primary btn-sm btn-block">

                                    <i class="fa fa-search mr-1"></i>
                                    Cari

                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="border-top-custom my-3"></div>

                    {{-- FILTER TANGGAL --}}
                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="small font-weight-bold mb-1">
                                    Tanggal Nota
                                </label>

                                <div class="d-flex align-items-center">

                                    <input type="text"
                                        name="tgl1"
                                        id="tgl1"
                                        class="form-control form-control-sm tanggal"
                                        placeholder="Pilih Tanggal"
                                        value="{{ request('tgl1') }}">

                                    <span class="mx-2 font-weight-bold">
                                        s.d
                                    </span>

                                    <input type="text"
                                        name="tgl2"
                                        id="tgl2"
                                        class="form-control form-control-sm tanggal"
                                        placeholder="Pilih Tanggal"
                                        value="{{ request('tgl2') }}">

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label class="small font-weight-bold mb-1">
                                    Tanggal Lunas
                                </label>

                                <div class="d-flex align-items-center">

                                    <input type="text"
                                        name="tgl_lunas1"
                                        id="tgl_lunas1"
                                        class="form-control form-control-sm tanggal"
                                        placeholder="Pilih Tanggal"
                                        value="{{ request('tgl_lunas1') }}">

                                    <span class="mx-2 font-weight-bold">
                                        s.d
                                    </span>

                                    <input type="text"
                                        name="tgl_lunas2"
                                        id="tgl_lunas2"
                                        class="form-control form-control-sm tanggal"
                                        placeholder="Pilih Tanggal"
                                        value="{{ request('tgl_lunas2') }}">

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

        <div class="mt-3 mb-2">

            <strong>
                Jumlah Data :
            </strong>

            {{ count($getCobHarian) }}

        </div>

        <div class="row no-print mb-2">

            <div class="col-12">

                <button type="button"
                    class="btn btn-info btn-sm"
                    onclick="toggleNominal()">

                    <i class="fas fa-eye"></i>
                    Hide / Show Nominal

                </button>

                <button type="button"
                    class="btn btn-default btn-sm float-right"
                    id="copyButton">

                    <i class="fas fa-copy"></i>
                    Copy table

                </button>

            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-sm table-bordered text-xs mb-3"
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
                        <th class="kolom-nominal">Retur Obat</th>
                        <th class="kolom-nominal">Resep Pulang</th>
                        <th class="kolom-nominal">Paket Tindakan</th>
                        <th class="kolom-nominal">Operasi</th>
                        <th class="kolom-nominal">Laborat</th>
                        <th class="kolom-nominal">Radiologi</th>
                        <th class="kolom-nominal">Tambahan</th>
                        <th class="kolom-nominal">Kamar+Service</th>
                        <th class="kolom-nominal">Potongan</th>
                        <th class="kolom-nominal">Total</th>

                        <th>Penjamin</th>
                        <th>Piutang</th>

                        <th>Dibayar Asuransi</th>
                        <th>Selisih Dibayar</th>
                        <th>Akun Bayar</th>
                        <th>Tanggal Lunas</th>

                    </tr>

                </thead>

                <tbody>

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
                                {{ number_format($item->getRegistrasi->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getObat->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getReturObat->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getResepPulang->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">

                                {{
                                    number_format(

                                        $item->getRalanDokter->sum('totalbiaya') +
                                        $item->getRalanParamedis->sum('totalbiaya') +
                                        $item->getRalanDrParamedis->sum('totalbiaya') +
                                        $item->getRanapDokter->sum('totalbiaya') +
                                        $item->getRanapDrParamedis->sum('totalbiaya') +
                                        $item->getRanapParamedis->sum('totalbiaya')

                                    ,0,'.',',')
                                }}

                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getOprasi->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getLaborat->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getRadiologi->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getTambahan->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getKamarInap->sum('totalbiaya'),0,'.',',') }}
                            </td>

                            <td class="text-right kolom-nominal">
                                {{ number_format($item->getPotongan->sum('totalbiaya'),0,'.',',') }}
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
                                        $item->getPotongan->sum('totalbiaya')

                                    ,0,'.',',')
                                }}

                            </td>

                            @php

                                $penjabNama =
                                    $item->getPenjabCOB
                                    ->pluck('png_jawab')
                                    ->implode(', ');

                                $penjabTotal =
                                    $item->getPenjabCOB
                                    ->sum('totalpiutang');

                                $totalPenjab =
                                    $item->getPenjabCOB
                                    ->where('png_jawab','!=','BPJS')
                                    ->where('png_jawab','!=','ASR - JAMSOSTEK')
                                    ->sum('totalpiutang');

                                $dibayarCob =
                                    $item->getLunasCob->nominal_cob ?? 0;

                            @endphp

                            <td>
                                {{ $penjabNama }}
                            </td>

                            <td class="text-right">
                                {{ number_format($penjabTotal,0,'.',',') }}
                            </td>

                            <td class="text-right">
                                {{ number_format($dibayarCob,0,'.',',') }}
                            </td>

                            <td class="text-right">
                                {{ number_format($totalPenjab - $dibayarCob,0,'.',',') }}
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

                            <td colspan="25"
                                class="text-center">

                                Silahkan Cari Data

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- MODAL --}}
<div class="modal fade" id="modalCob">

    <div class="modal-dialog">

        <div class="modal-content">

            <form action="{{ url('simpan-cob') }}"
                method="POST"
                id="formCob">

                @csrf

                <div class="modal-header bg-primary text-white">

                    <h5 class="modal-title">
                        Input COB
                    </h5>

                    <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    @if ($errors->any())

                        <div class="alert alert-danger">

                            <ul class="mb-0">

                                @foreach ($errors->all() as $error)

                                    <li>{{ $error }}</li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                    <div class="form-group mb-3">

                        <label>No Rawat</label>

                        <input type="text"
                            name="no_rawat"
                            id="modal_no_rawat"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="form-group mb-3">

                        <label>
                            Tanggal Bayar Perusahaan
                        </label>

                        <div class="input-group">

                            <input type="text"
                                name="tgl_lunas"
                                id="tgl_lunas"
                                class="form-control"
                                autocomplete="off"
                                required>

                            <div class="input-group-append">

                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="form-group mb-3">

                        <label>
                            Nominal Dibayar Asuransi
                        </label>

                        <input type="text"
                            name="nominal_cob"
                            id="nominal_cob"
                            class="form-control text-right"
                            inputmode="numeric"
                            onkeyup="formatRibuan(this)"
                            onkeypress="return hanyaAngka(event)"
                            required>

                    </div>

                    <div class="form-group mb-3">

                        <label>
                            Akun Bayar
                        </label>

                        <select name="akun_bayar"
                            id="akun_bayar"
                            class="form-control"
                            required>

                            <option value="">
                                Pilih Akun Bayar
                            </option>

                            @foreach($akunBayar as $akun)

                                <option value="{{ $akun->nama_bayar }}">
                                    {{ $akun->nama_bayar }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Tutup

                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

    // FILTER TANGGAL
    flatpickr(".tanggal",{
        dateFormat:"Y-m-d",
        allowInput:true,
        disableMobile:true
    });

    // TANGGAL LUNAS
    flatpickr("#tgl_lunas",{
        dateFormat:"Y-m-d",
        allowInput:true,
        disableMobile:true
    });

    // HANYA ANGKA
    function hanyaAngka(e){

        const char =
            String.fromCharCode(e.which);

        if(!/[0-9]/.test(char)){

            e.preventDefault();

            return false;
        }
    }

    // FORMAT RIBUAN
    function formatRibuan(input){

        let value =
            input.value.replace(/\D/g,'');

        input.value =
            new Intl.NumberFormat('id-ID')
            .format(value);
    }

    // HAPUS FORMAT
    function removeFormat(input){

        return input.value.replace(/\D/g,'');
    }

    // SUBMIT FORM
    document.getElementById(
        'formCob'
    ).addEventListener(
        'submit',
        function(){

            const nominalInput =
                document.getElementById(
                    'nominal_cob'
                );

            nominalInput.value =
                removeFormat(
                    nominalInput
                );

        }
    );

    // SET DATA MODAL
    function setCobData(no){

        document.getElementById(
            'modal_no_rawat'
        ).value = no;

        document.getElementById(
            'nominal_cob'
        ).value = '';

        document.getElementById(
            'tgl_lunas'
        ).value = '';

        document.getElementById(
            'akun_bayar'
        ).selectedIndex = 0;
    }

    // HIDE SHOW NOMINAL
    function toggleNominal(){

        document
            .querySelectorAll(
                '.kolom-nominal'
            )
            .forEach(function(el){

                el.classList.toggle(
                    'd-none'
                );

            });

    }

    // COPY TABLE
    document.getElementById(
        "copyButton"
    ).addEventListener(
        "click",
        function(){

            copyTableToClipboard(
                "tableToCopy"
            );

        }
    );

    function copyTableToClipboard(tableId){

        const table =
            document.getElementById(
                tableId
            );

        let text = "";

        for(let row of table.rows){

            let rowData = [];

            for(let cell of row.cells){

                if(
                    !cell.classList.contains(
                        'd-none'
                    )
                ){

                    rowData.push(
                        cell.innerText
                            .replace(/\n/g,' ')
                            .trim()
                    );

                }
            }

            text +=
                rowData.join('\t') + '\n';
        }

        navigator.clipboard.writeText(text)
            .then(function(){

                alert(
                    'Tabel berhasil di copy'
                );

            })
            .catch(function(err){

                console.error(err);

                alert(
                    'Gagal copy table'
                );

            });
    }

    // FILTER TANGGAL
    function updateFilterState(){

        const filterType =
            document.querySelector(
                'input[name="filter_type"]:checked'
            ).value;

        const tgl1 =
            document.getElementById(
                'tgl1'
            );

        const tgl2 =
            document.getElementById(
                'tgl2'
            );

        const tglLunas1 =
            document.getElementById(
                'tgl_lunas1'
            );

        const tglLunas2 =
            document.getElementById(
                'tgl_lunas2'
            );

        if(filterType === 'tempo'){

            tgl1.disabled = false;
            tgl2.disabled = false;

            tglLunas1.disabled = true;
            tglLunas2.disabled = true;

            tglLunas1.value = '';
            tglLunas2.value = '';

        }else{

            tgl1.disabled = true;
            tgl2.disabled = true;

            tglLunas1.disabled = false;
            tglLunas2.disabled = false;

            tgl1.value = '';
            tgl2.value = '';
        }
    }

    // TRIGGER RADIO
    document.querySelectorAll(
        'input[name="filter_type"]'
    ).forEach(function(el){

        el.addEventListener(
            'change',
            updateFilterState
        );

    });

    // LOAD AWAL
    document.addEventListener(
        'DOMContentLoaded',
        updateFilterState
    );

</script>

@endsection