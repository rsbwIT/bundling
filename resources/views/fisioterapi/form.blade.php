@extends('layout.layoutDashboard')
@section('title', 'Form Fisioterapi')
@section('konten')

<style>
    .card-premium { border-radius:18px; border:none; box-shadow:0 4px 18px rgba(0,0,0,0.08); }
    .section-title { font-weight:700; font-size:1.3rem; }

    /* ==== DATE INPUT FIX FINAL ==== */
    .date-wrap { position:relative; width:100%; }

    .date-input {
        width:100%;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid #ccc;
        font-size:14px;
        background:white;
        transition:0.25s;
    }

    /* Sembunyikan yyyy-mm-dd saat kosong */
    .date-input:not(.has-value)::-webkit-datetime-edit {
        color:transparent;
    }

    .date-input.has-value::-webkit-datetime-edit {
        color:black !important;
    }

    .date-input:focus {
        border-color:#006de9;
        box-shadow:0 0 0 2px rgba(0,109,233,0.12);
    }

    .date-label {
        position:absolute;
        left:12px; top:50%;
        transform:translateY(-50%);
        color:#777;
        font-size:14px;
        background:white;
        padding:0 4px;
        pointer-events:none;
        transition:0.25s;
    }

    .date-input.has-value + .date-label,
    .date-input:focus + .date-label {
        top:-6px;
        font-size:11px;
        color:#006de9;
    }

    /* Signature */
    .signature-pad { width:140px; height:90px; border:1px solid #aaa; border-radius:10px; }
    .ttd-preview { width:80px; height:80px; object-fit:contain; display:block; margin:auto; }

    .table-premium th { background:#f3f6fa; font-weight:700; }

    .form-select {
        width: 100%;                /* Full width */
        padding: 0.5rem 1rem;       /* Spasi dalam */
        font-size: 1rem;            /* Ukuran font */
        border-radius: 8px;         /* Sudut membulat */
        border: 1px solid #ccc;     /* Border standar */
        background-color: #fff;     
        transition: border-color 0.3s, box-shadow 0.3s;
        appearance: none;           /* Hilangkan style default browser */
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .form-select:focus {
        border-color: #4A90E2;      /* Warna border saat focus */
        box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
        outline: none;
    }

    /* Tambahan ikon dropdown di kanan */
    .form-select-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .form-select-wrapper::after {
        content: "▼";               /* Simbol dropdown */
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;       /* Supaya tidak mengganggu klik */
        color: #888;
        font-size: 0.8rem;
    }
</style>

@if(session('success')) 
    <div class="alert alert-success">{{ session('success') }}</div> 
@endif
@if(session('error')) 
    <div class="alert alert-danger">{{ session('error') }}</div> 
@endif


<!-- ===================== IDENTITAS ===================== -->
<div class="card card-premium p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="section-title mb-0">Identitas Pasien</h4>

        <div class="d-flex align-items-center">
            <strong>Lembar:</strong>
            <select id="selectLembar" class="form-select ms-2" style="width:auto;">
                @for ($l = 1; $l <= ($lembarMax ?? $lembar); $l++)
                    <option value="{{ $l }}" {{ $l == $lembar ? 'selected' : '' }}>
                        Lembar {{ $l }}
                    </option>
                @endfor
            </select>
        </div>
    </div>

    <div class="row mt-3 g-3">
        <div class="col-md-6">
            <label class="fw-semibold">Nama Pasien</label>
            <input class="form-control" readonly value="{{ $data->nm_pasien }}">
        </div>
        <div class="col-md-3">
            <label class="fw-semibold">No. RM</label>
            <input class="form-control" readonly value="{{ $data->no_rkm_medis }}">
        </div>
        <div class="col-md-3">
            <label class="fw-semibold">Dokter</label>
            <input class="form-control" readonly value="{{ $dokter ?? '' }}">
        </div>
    </div>
</div>


<!-- ===================== FORM ===================== -->
<form id="fisioterapiForm" method="POST" action="{{ route('fisioterapi.form.save', [$tahun,$bulan,$hari,$no_rawat]) }}">
@csrf
<input type="hidden" name="lembar" id="lembarInput" value="{{ $lembar }}">


<!-- ===================== PROTOKOL ===================== -->
<div class="card card-premium p-4 mb-5">
    <h4 class="section-title">Protokol Terapi</h4>

    <div class="mb-3">
        <label class="fw-semibold">Diagnosa</label>
        <textarea name="diagnosa" class="form-control" rows="2">{{ $form->diagnosa ?? '' }}</textarea>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="fw-semibold">FT</label>
            <input name="ft" class="form-control" value="{{ $form->ft ?? '' }}">
        </div>
        <div class="col-md-6">
            <label class="fw-semibold">ST</label>
            <input name="st" class="form-control" value="{{ $form->st ?? '' }}">
        </div>
    </div>
</div>


@php
$programList = [
    'Electrical Stimulation','Electrical Stimulation + Exercise','Fisioterapi Diathermy','Fisioterapi IRR',
    'Fisioterapi IRR + Exercise','Fisioterapi MWD','Fisioterapi MWD + Exercise','Fisioterapi TENS',
    'Fisioterapi TENS + Exercise','Fisioterapi TENS + IRR','Fisioterapi Traksi','Fisioterapi US',
    'Fisioterapi US + Exercise','Fisioterapi Exercise','Chest Fisioterapi','Speech Therapy / Terapi Wicara',
    'ST Latihan Menelan','REASSESMENT – Electrical Stimulation','REASSESMENT – Electrical Stimulation + Exercise',
    'REASSESMENT – Diathermy','REASSESMENT – IRR','REASSESMENT – IRR + Exercise','REASSESMENT – MWD',
    'REASSESMENT – MWD + Exercise','REASSESMENT – TENS','REASSESMENT – TENS + Exercise',
    'REASSESMENT – TENS + IRR','REASSESMENT – Traksi','REASSESMENT – US','REASSESMENT – US + Exercise',
    'REASSESMENT – Chest','EVALUASI – Electrical Stimulation','EVALUASI – Electrical Stimulation + Exercise',
    'EVALUASI – Diathermy','EVALUASI – IRR','EVALUASI – IRR + Exercise','EVALUASI – MWD',
    'EVALUASI – MWD + Exercise','EVALUASI – TENS','EVALUASI – TENS + Exercise','EVALUASI – TENS + IRR',
    'EVALUASI – Traksi','EVALUASI – US','EVALUASI – US + Exercise','EVALUASI – Chest'
];
@endphp


<!-- ===================== KUNJUNGAN ===================== -->
<div class="card card-premium p-4 mb-5">
    <h4 class="section-title">Kunjungan Fisioterapi (Lembar {{ $lembar }})</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-premium text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Program</th>
                    <th>Tanggal</th>
                    <th>TTD Pasien</th>
                    <th>TTD Dokter</th>
                    <th>TTD Terapis</th>
                </tr>
            </thead>

            <tbody>
            @for($i=1;$i<=8;$i++)
                @php $row = $kunjungan[$i] ?? null; @endphp
                <tr>
                    <td>{{ $i }}</td>

                    <td>
                        <select name="program[{{ $i }}]" class="form-select">
                            <option value=""></option>
                            @foreach($programList as $p)
                                <option value="{{ $p }}" {{ (isset($row->program) && $row->program==$p) ? 'selected' : '' }}>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <div class="date-wrap">
                            <input
                                type="date"
                                name="tanggal[{{ $i }}]"
                                value="{{ $row->tanggal ?? '' }}"
                                class="date-input {{ ($row && $row->tanggal) ? 'has-value' : '' }}"
                                oninput="this.classList.add('has-value')"
                            >
                            <label class="date-label">Pilih Tanggal</label>
                        </div>
                    </td>

                    <td>
                        @if($row && $row->ttd_pasien)
                            <img src="{{ asset('storage/ttd/'.$row->ttd_pasien) }}" class="ttd-preview" id="img_pasien_{{ $i }}">
                            <button type="button" class="btn btn-sm btn-outline-warning mt-1" onclick="editTtd('pasien', {{ $i }})">Edit</button>
                        @else
                            <canvas id="pad_pasien_{{ $i }}" data-role="pasien" data-index="{{ $i }}" class="signature-pad"></canvas>
                            <input type="hidden" name="ttd_pasien[{{ $i }}]" id="input_pasien_{{ $i }}">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="clearPad('pasien',{{ $i }})">Hapus</button>
                        @endif
                    </td>


                    <td>
                        @php
                            $settingName = $getSetting->nama_instansi ?? '';
                            $kab = $getSetting->kabupaten ?? '';
                            $dokName = $dokter ?? '';
                            $kdDoc = $kd_dokter ?? '';
                            $tglReg = $tgl_registrasi ?? '';
                            $qrText = "Dikeluarkan di {$settingName}, Kabupaten/Kota {$kab} Ditandatangani secara elektronik oleh {$dokName} ID {$kdDoc} {$tglReg}";
                            $qrBase64 = DNS2D::getBarcodePNG($qrText, 'QRCODE');
                        @endphp

                        @if($row && $row->ttd_dokter)
                            <img src="{{ asset('storage/qr/'.$row->ttd_dokter) }}" class="ttd-preview">
                        @else
                            <img src="data:image/png;base64,{{ $qrBase64 }}" class="ttd-preview">
                            <input type="hidden" name="ttd_dokter[{{ $i }}]" value="{{ base64_encode($qrBase64) }}">
                        @endif
                    </td>

                    <td>
                        @if($row && $row->ttd_terapis)
                            <img src="{{ asset('storage/ttd/'.$row->ttd_terapis) }}" class="ttd-preview" id="img_terapis_{{ $i }}">
                            <button type="button" class="btn btn-sm btn-outline-warning mt-1" onclick="editTtd('terapis', {{ $i }})">Edit</button>
                        @else
                            <canvas id="pad_terapis_{{ $i }}" data-role="terapis" data-index="{{ $i }}" class="signature-pad"></canvas>
                            <input type="hidden" name="ttd_terapis[{{ $i }}]" id="input_terapis_{{ $i }}">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="clearPad('terapis',{{ $i }})">Hapus</button>
                        @endif
                    </td>

                </tr>
            @endfor
            </tbody>
        </table>
    </div>
</div>


<!-- ===================== BUTTONS ===================== -->
<div class="d-flex justify-content-between mb-5">
    <a href="{{ route('fisioterapi.pasien') }}" class="btn btn-outline-secondary px-4">Kembali</a>

    <div>
        <form method="POST" action="{{ route('fisioterapi.lembar.new', [$tahun,$bulan,$hari,$no_rawat]) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-outline-primary me-2">New Lembar</button>
        </form>

        <button type="submit" form="fisioterapiForm" class="btn btn-primary px-5 fw-bold">Simpan</button>
    </div>
</div>

</form>


<!-- ===================== JS ===================== -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

<script>
let pads = {};

function initPads() {
    document.querySelectorAll('.signature-pad').forEach(c => {
        const pad = new SignaturePad(c, { backgroundColor: '#fff' });
        const role = c.dataset.role;
        const idx = c.dataset.index;

        pads[`${role}_${idx}`] = pad;

        pad.addEventListener('endStroke', () => {
            const input = document.getElementById(`input_${role}_${idx}`);
            input.value = pad.toDataURL();
        });
    });
}

function clearPad(role, idx) {
    const pad = pads[`${role}_${idx}`];
    if (pad) pad.clear();
    document.getElementById(`input_${role}_${idx}`).value = '';
}

document.addEventListener('DOMContentLoaded', () => {
    initPads();

    document.querySelectorAll('.date-input').forEach(el => {
        if (el.value) {
            el.classList.add('has-value');
        }
        el.addEventListener('change', function(){
            this.classList.toggle('has-value', this.value !== '');
        });
    });

    document.getElementById('selectLembar').addEventListener('change', function(){
        window.location.href = `/fisioterapi/form/{{ $tahun }}/{{ $bulan }}/{{ $hari }}/{{ $no_rawat }}?lembar=${this.value}`;
    });
});
</script>

@endsection
