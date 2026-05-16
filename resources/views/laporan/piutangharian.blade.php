@extends('..layout.layoutDashboard')
@section('title', 'Pasien Piutang (Harian)')

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

    .filter-radio-wrapper{
        height:38px;
        display:flex;
        align-items:center;
    }

    .border-top-custom{
        border-top:1px solid #e9ecef;
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



        <form action="{{ url('piutang-harian') }}"
            method="GET">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    <div class="row">

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



                        <div class="col-md-2">

                            <div class="form-group">

                                <label class="small font-weight-bold mb-1">
                                    Status
                                </label>

                                <select name="stsLanjut"
                                    class="form-control form-control-sm">

                                    <option value="Ralan"
                                        {{ request('stsLanjut')=='Ralan' ? 'selected':'' }}>
                                        Rawat Jalan
                                    </option>

                                    <option value="Ranap"
                                        {{ request('stsLanjut')=='Ranap' ? 'selected':'' }}>
                                        Rawat Inap
                                    </option>

                                </select>

                            </div>

                        </div>



                        <div class="col-md-3">

                            <label class="small font-weight-bold d-block mb-1">
                                Filter Tanggal
                            </label>

                            <div class="filter-radio-wrapper">

                                <div class="form-check form-check-inline">

                                    <input type="radio"
                                        class="form-check-input"
                                        name="filter_type"
                                        value="tempo"
                                        {{ request('filter_type','tempo')=='tempo' ? 'checked':'' }}>

                                    <label class="form-check-label small">
                                        Tanggal Nota
                                    </label>

                                </div>


                                <div class="form-check form-check-inline">

                                    <input type="radio"
                                        class="form-check-input"
                                        name="filter_type"
                                        value="lunas"
                                        {{ request('filter_type')=='lunas' ? 'checked':'' }}>

                                    <label class="form-check-label small">
                                        Tgl Lunas
                                    </label>

                                </div>

                            </div>

                        </div>



                        <div class="col-md-3">

                            <label>&nbsp;</label>

                            <button type="submit"
                                class="btn btn-primary btn-sm btn-block">

                                <i class="fa fa-search mr-1"></i>
                                Cari

                            </button>

                        </div>

                    </div>



                    <div class="border-top-custom my-3"></div>



                    <div class="row">

                        <div class="col-md-6">

                            <label class="small font-weight-bold mb-1">
                                Tanggal Nota
                            </label>

                            <div class="d-flex">

                                <input type="text"
                                    id="tgl1"
                                    name="tgl1"
                                    class="form-control form-control-sm tanggal"
                                    value="{{ request('tgl1') }}">

                                <span class="mx-2 mt-2">
                                    s.d
                                </span>

                                <input type="text"
                                    id="tgl2"
                                    name="tgl2"
                                    class="form-control form-control-sm tanggal"
                                    value="{{ request('tgl2') }}">

                            </div>

                        </div>



                        <div class="col-md-6">

                            <label class="small font-weight-bold mb-1">
                                Tanggal Lunas
                            </label>

                            <div class="d-flex">

                                <input type="text"
                                    id="tgl_lunas1"
                                    name="tgl_lunas1"
                                    class="form-control form-control-sm tanggal"
                                    value="{{ request('tgl_lunas1') }}">

                                <span class="mx-2 mt-2">
                                    s.d
                                </span>

                                <input type="text"
                                    id="tgl_lunas2"
                                    name="tgl_lunas2"
                                    class="form-control form-control-sm tanggal"
                                    value="{{ request('tgl_lunas2') }}">

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </form>



        <br>

        Jumlah Data : {{ count($getPiutangHarian) }}



        <div class="row mb-2">

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



        {{-- table tetap pakai punyamu --}}

        <table
            class="table table-sm table-bordered table-responsive text-xs mb-3"
            style="white-space: nowrap;"
            id="tableToCopy">

            <thead>
                <tr>
                    <th>No Rawat</th>
                    <th>Nama Pasien</th>
                    <th>Nama Poli</th>
                    <th>Kode Dokter</th>
                    <th>Nama Dokter</th>
                    <th>Input Nominal Piutang</th>
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

                    <th colspan="2" class="text-center">
                        Penjamin
                    </th>

                    <th>Dibayar</th>
                    <th>Selisih</th>
                    <th>Akun Bayar</th>
                    <th>Tanggal Lunas</th>
                </tr>
            </thead>

            @forelse($getPiutangHarian as $item)

                @php

                    $paket =
                        $item->getRalanDokter->sum('totalbiaya') +
                        $item->getRalanParamedis->sum('totalbiaya') +
                        $item->getRalanDrParamedis->sum('totalbiaya') +
                        $item->getRanapDokter->sum('totalbiaya') +
                        $item->getRanapDrParamedis->sum('totalbiaya') +
                        $item->getRanapParamedis->sum('totalbiaya');

                    $detailPiutang =
                        $item->getPenjab->detail_piutang ?? '';

                    $rows =
                        $detailPiutang
                            ? explode(',', $detailPiutang)
                            : [];

                    $nominalDetail =
                        $item->getPenjab->total_detail ?? 0;

                    $nominalCob =
                        $item->getLunasCob->nominal_cob ?? 0;

                    $selisih =
                        $nominalDetail - $nominalCob;

                @endphp

                <tr>

                    <td>{{ $item->no_rawat }}</td>
                    <td>{{ $item->nm_pasien }}</td>
                    <td>{{ $item->nm_poli }}</td>
                    <td>{{ $item->kd_dokter }}</td>
                    <td>{{ $item->nm_dokter }}</td>

                    <td class="text-center">

                        <button
                            type="button"
                            class="btn btn-success btn-xs"
                            data-toggle="modal"
                            data-target="#modalCob"
                            onclick="setPiutangData('{{ $item->no_rawat }}')">

                            <i class="fas fa-edit"></i>

                        </button>

                    </td>

                    <td>{{ $item->nomor_nota ?? '-' }}</td>

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
                        {{ number_format($paket,0,'.',',') }}
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

                    <td class="text-right font-weight-bold kolom-nominal">

                        {{
                            number_format(
                                $item->getRegistrasi->sum('totalbiaya') +
                                $item->getObat->sum('totalbiaya') +
                                $item->getReturObat->sum('totalbiaya') +
                                $item->getResepPulang->sum('totalbiaya') +
                                $paket +
                                $item->getOprasi->sum('totalbiaya') +
                                $item->getLaborat->sum('totalbiaya') +
                                $item->getRadiologi->sum('totalbiaya') +
                                $item->getTambahan->sum('totalbiaya') +
                                $item->getKamarInap->sum('totalbiaya') +
                                $item->getPotongan->sum('totalbiaya'),
                                0,
                                '.',
                                ','
                            )
                        }}

                    </td>


                    {{-- penjamin nama --}}
                    <td style="min-width:220px; padding:0; vertical-align:top;">

                        @forelse($rows as $row)

                            @php

                                $row = trim($row);

                                preg_match(
                                    '/^(.*)\s+(\d+)$/',
                                    $row,
                                    $hasil
                                );

                                $nama =
                                    preg_replace(
                                        '/^\d+\.\s*/',
                                        '',
                                        trim($hasil[1] ?? '-')
                                    );

                            @endphp

                            <div style="
                                padding:8px 12px;
                                border-bottom:1px solid #dee2e6;
                                min-height:38px;
                                display:flex;
                                align-items:center;">

                                {{ $nama }}

                            </div>

                        @empty

                            <div class="text-center p-2">-</div>

                        @endforelse

                    </td>


                    {{-- penjamin nominal --}}
                    <td style="width:120px; padding:0; vertical-align:top;">

                        @forelse($rows as $row)

                            @php

                                $row = trim($row);

                                preg_match(
                                    '/^(.*)\s+(\d+)$/',
                                    $row,
                                    $hasil
                                );

                                $nominal =
                                    $hasil[2] ?? 0;

                            @endphp

                            <div style="
                                padding:8px 12px;
                                border-left:1px solid #dee2e6;
                                border-bottom:1px solid #dee2e6;
                                min-height:38px;
                                display:flex;
                                justify-content:flex-end;
                                align-items:center;
                                font-weight:600;">

                                {{ number_format($nominal,0,',','.') }}

                            </div>

                        @empty

                            <div class="text-center p-2">-</div>

                        @endforelse

                    </td>


                    <td class="text-right">
                        {{ number_format($nominalCob,0,'.',',') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($selisih,0,'.',',') }}
                    </td>

                    <td>
                        {{ $item->getLunas->akun_bayar ?? '' }}
                    </td>

                    <td>
                        {{ $item->getLunas->tgl_lunas ?? '' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="24" class="text-center">
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

            <form action="{{ url('simpan-cob') }}"
                method="POST"
                id="formCob">

                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        Input Piutang
                    </h5>

                    <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                        <span>&times;</span>
                    </button>
                </div>


                <div class="modal-body">

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

    flatpickr(".tanggal",{
        dateFormat:"Y-m-d",
        allowInput:true,
        disableMobile:true
    });

    flatpickr("#tgl_lunas",{
        dateFormat:"Y-m-d",
        allowInput:true,
        disableMobile:true
    });


    function hanyaAngka(e){

        const char =
            String.fromCharCode(e.which);

        if(!/[0-9]/.test(char)){

            e.preventDefault();

            return false;
        }
    }


    function formatRibuan(input){

        let value =
            input.value.replace(/\D/g,'');

        input.value =
            new Intl.NumberFormat('id-ID')
            .format(value);
    }


    function removeFormat(input){

        return input.value.replace(/\D/g,'');
    }


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


    function setPiutangData(no){

        document.getElementById(
            'modal_no_rawat'
        ).value = no;

        document.getElementById(
            'nominal_cob'
        ).value = '';

        document.getElementById(
            'tgl_lunas'
        ).value = '';
    }


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



    // COPY TABLE RAPI
    document.getElementById(
        "copyButton"
    ).addEventListener(
        "click",
        function(){

            copyTableFormatted(
                "tableToCopy"
            );

        }
    );


    function copyTableFormatted(tableId){

    const table =
        document.getElementById(
            tableId
        );


    let html =
        '<html>' +
        '<body>' +
        '<table border="1" style="border-collapse:collapse;">' +
        table.innerHTML +
        '</table>' +
        '</body>' +
        '</html>';


    const container =
        document.createElement(
            "div"
        );

    container.style.position =
        "fixed";

    container.style.left =
        "-99999px";

    container.contentEditable =
        true;

    container.innerHTML =
        html;


    document.body.appendChild(
        container
    );


    const range =
        document.createRange();

    range.selectNodeContents(
        container
    );


    const selection =
        window.getSelection();

    selection.removeAllRanges();

    selection.addRange(
        range
    );


    try{

        document.execCommand(
            "copy"
        );

        alert(
            "Tabel berhasil disalin."
        );

    }catch(err){

        alert(
            "Copy gagal."
        );

        console.log(
            err
        );

    }


    selection.removeAllRanges();


    document.body.removeChild(
        container
    );

}



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


        if(filterType=='tempo'){

            tgl1.disabled=false;
            tgl2.disabled=false;

            tglLunas1.disabled=true;
            tglLunas2.disabled=true;

            tglLunas1.value='';
            tglLunas2.value='';

        }else{

            tgl1.disabled=true;
            tgl2.disabled=true;

            tglLunas1.disabled=false;
            tglLunas2.disabled=false;

            tgl1.value='';
            tgl2.value='';
        }

    }


    document.querySelectorAll(
        'input[name="filter_type"]'
    ).forEach(function(el){

        el.addEventListener(
            'change',
            updateFilterState
        );

    });


    document.addEventListener(
        'DOMContentLoaded',
        updateFilterState
    );

</script>

@endsection