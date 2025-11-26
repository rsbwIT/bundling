@extends('layout.layoutDashboard')

@section('title', 'Form Fisioterapi')

@section('konten')

<style>
    .card-premium {
        border-radius: 18px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    }
    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #2c3e50;
    }
    .signature-pad {
        border: 1px solid #dcdcdc;
        width: 100%;
        height: 130px;
        border-radius: 10px;
        background: #fff;
        touch-action: none;
    }
    .table-premium th {
        background: #f3f6fa;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .ttd-preview {
        max-height: 120px;
        object-fit: contain;
    }
</style>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- ===================================== --}}
{{-- IDENTITAS PASIEN --}}
{{-- ===================================== --}}
<div class="card card-premium p-4 mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="section-title mb-0">Identitas Pasien</h4>

        <div class="d-flex align-items-center">
            <strong>Lembar: </strong>
            <select id="selectLembar" class="form-select ms-2" style="width: auto;">
                @for ($l = 1; $l <= ($lembarMax ?? $lembar); $l++)
                    <option value="{{ $l }}" {{ $l == $lembar ? 'selected' : '' }}>
                        Lembar {{ $l }}
                    </option>
                @endfor
            </select>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <label class="fw-semibold">Nama Pasien</label>
            <input class="form-control" value="{{ $data->nm_pasien }}" readonly>
        </div>

        <div class="col-md-3">
            <label class="fw-semibold">No. RM</label>
            <input class="form-control" value="{{ $data->no_rkm_medis }}" readonly>
        </div>

        <div class="col-md-3">
            <label class="fw-semibold">Dokter</label>
            <input class="form-control" value="{{ $dokter }}" readonly>
        </div>
    </div>
</div>

{{-- ===================================== --}}
{{-- FORM --}}
{{-- ===================================== --}}
<form id="fisioterapiForm" method="POST"
      action="{{ route('fisioterapi.form.save', [$tahun,$bulan,$hari,$no_rawat]) }}">
@csrf

<input type="hidden" name="lembar" id="lembarInput" value="{{ $lembar }}">

<div class="card card-premium p-4 mb-5">
    <h4 class="section-title">Protokol Terapi</h4>

    <div class="mb-3">
        <label class="fw-semibold">Diagnosa</label>
        <textarea name="diagnosa" class="form-control" rows="2">{{ $form->diagnosa }}</textarea>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="fw-semibold">FT</label>
            <input name="ft" class="form-control" value="{{ $form->ft }}">
        </div>
        <div class="col-md-6">
            <label class="fw-semibold">ST</label>
            <input name="st" class="form-control" value="{{ $form->st }}">
        </div>
    </div>
</div>

{{-- ===================================== --}}
{{-- TABEL KUNJUNGAN --}}
{{-- ===================================== --}}
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
                @for ($i = 1; $i <= 8; $i++)
                    @php $row = $kunjungan[$i] ?? null; @endphp

                    <tr>
                        <td>{{ $i }}</td>

                        <td>
                            <input class="form-control" name="program[{{ $i }}]"
                                   value="{{ $row->program ?? '' }}">
                        </td>

                        <td>
                            <input type="date" class="form-control"
                                name="tanggal[{{ $i }}]"
                                value="{{ $row->tanggal ?? '' }}">
                        </td>

                        {{-- PASIEN --}}
                        <td>
                            @if($row && $row->ttd_pasien)
                                <img src="{{ asset('storage/ttd/'.$row->ttd_pasien) }}" class="ttd-preview">
                            @else
                                <canvas id="pad_pasien_{{ $i }}" class="signature-pad"
                                        data-role="pasien" data-index="{{ $i }}"></canvas>
                                <input type="hidden" name="ttd_pasien[{{ $i }}]" id="input_pasien_{{ $i }}">
                                <button class="btn btn-sm btn-outline-danger mt-2" type="button"
                                    onclick="clearPad('pasien', {{ $i }})">Hapus</button>
                            @endif
                        </td>

                        {{-- DOKTER --}}
                        <td>
                            @if($row && $row->ttd_dokter)
                                <img src="{{ asset('storage/ttd/'.$row->ttd_dokter) }}" class="ttd-preview">
                            @else
                                <canvas id="pad_dokter_{{ $i }}" class="signature-pad"
                                        data-role="dokter" data-index="{{ $i }}"></canvas>
                                <input type="hidden" name="ttd_dokter[{{ $i }}]" id="input_dokter_{{ $i }}">
                                <button class="btn btn-sm btn-outline-danger mt-2" type="button"
                                    onclick="clearPad('dokter', {{ $i }})">Hapus</button>
                            @endif
                        </td>

                        {{-- TERAPIS --}}
                        <td>
                            @if($row && $row->ttd_terapis)
                                <img src="{{ asset('storage/ttd/'.$row->ttd_terapis) }}" class="ttd-preview">
                            @else
                                <canvas id="pad_terapis_{{ $i }}" class="signature-pad"
                                        data-role="terapis" data-index="{{ $i }}"></canvas>
                                <input type="hidden" name="ttd_terapis[{{ $i }}]" id="input_terapis_{{ $i }}">
                                <button class="btn btn-sm btn-outline-danger mt-2" type="button"
                                    onclick="clearPad('terapis', {{ $i }})">Hapus</button>
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

{{-- ===================================== --}}
{{-- BUTTON --}}
{{-- ===================================== --}}
<div class="d-flex justify-content-between mb-5">
    <a href="{{ route('fisioterapi.pasien') }}" class="btn btn-outline-secondary px-4">
        Kembali
    </a>

    <div>

        <form id="newLembarForm" method="POST"
              action="{{ route('fisioterapi.lembar.new', [$tahun,$bulan,$hari,$no_rawat]) }}"
              style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-outline-primary me-2"
                onclick="return confirm('Buat lembar kosong baru?')">
                New Lembar
            </button>
        </form>

        <button class="btn btn-primary px-5 fw-bold" type="submit" form="fisioterapiForm">
            Simpan
        </button>
    </div>
</div>

</form>

{{-- ===================================== --}}
{{-- SIGNATURE PAD --}}
{{-- ===================================== --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

<script>
let pads = {};

function resizeCanvas(c) {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    c.width = c.offsetWidth * ratio;
    c.height = c.offsetHeight * ratio;
    c.getContext("2d").scale(ratio, ratio);
}

function initPads() {
    document.querySelectorAll('.signature-pad').forEach(c => {
        resizeCanvas(c);
        const role = c.dataset.role;
        const idx = c.dataset.index;
        const key = `${role}_${idx}`;

        const pad = new SignaturePad(c, { backgroundColor: "white" });
        pads[key] = pad;

        pad.addEventListener("endStroke", () => {
            document.getElementById(`input_${role}_${idx}`).value = pad.toDataURL();
        });
    });
}

function clearPad(role, i) {
    const key = `${role}_${i}`;
    if (pads[key]) pads[key].clear();
    const input = document.getElementById(`input_${role}_${i}`);
    if (input) input.value = "";
}

function syncAll() {
    Object.keys(pads).forEach(key => {
        const pad = pads[key];
        const [role, idx] = key.split('_');
        if (!pad.isEmpty()) {
            document.getElementById(`input_${role}_${idx}`).value = pad.toDataURL();
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    initPads();

    document.getElementById("fisioterapiForm").addEventListener("submit", () => {
        syncAll();
    });

    document.getElementById('selectLembar').addEventListener('change', function() {
        const selected = this.value;
        window.location.href =
            `/fisioterapi/form/{{ $tahun }}/{{ $bulan }}/{{ $hari }}/{{ $no_rawat }}?lembar=${selected}`;
    });
});
</script>

@endsection
