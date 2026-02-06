@extends('layout.layoutDashboard')

@section('title', 'Panggil Antrian Farmasi')

@section('konten')

@php
$loketList = [
    'A' => ['label' => 'BPJS – Racikan',         'bg' => 'success'],
    'B' => ['label' => 'BPJS – Non Racikan',     'bg' => 'info'],
    'C' => ['label' => 'NON BPJS – Non Racikan', 'bg' => 'warning'],
    'D' => ['label' => 'NON BPJS – Racikan',     'bg' => 'danger'],
];
@endphp

<style>
.card-loket {
    height: 100%;
    border-radius: 14px;
    overflow: hidden;
}
.card-loket .card-body {
    max-height: calc(100vh - 260px);
    overflow-y: auto;
}
.loket-header {
    padding: 14px;
    text-align: center;
    color: #fff;
}
.badge-status {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
}
.btn-action {
    width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 50%;
}
@keyframes pulse {
    0% { background:#fff3cd; }
    50% { background:#ffe69c; }
    100% { background:#fff3cd; }
}
.row-panggil {
    animation: pulse 1.2s infinite;
}
</style>

<div class="row">

@foreach ($loketList as $loket => $cfg)
@php $list = $antrian[$loket] ?? collect(); @endphp

<div class="col-md-3">
    <div class="card card-loket shadow-sm">

        {{-- HEADER --}}
        <div class="card-header bg-{{ $cfg['bg'] }} loket-header">
            <div class="h5 mb-0">LOKET {{ $loket }}</div>
            <small>{{ $cfg['label'] }}</small>
        </div>

        {{-- BODY --}}
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
                <thead class="bg-light text-center">
                    <tr>
                        <th width="40">No</th>
                        <th width="70">RM</th>
                        <th>Pasien</th>
                        <th width="38">🔊</th>
                        <th width="38">🖨</th>
                        <th width="38">✅</th>
                    </tr>
                </thead>
                <tbody>

                @forelse ($list as $row)
                @php
                    $rowId = md5($row->no_rawat); // 🔐 ID AMAN
                    $status = strtoupper(trim($row->status_panggil));
                @endphp

                <tr id="row-{{ $rowId }}">
                    <td class="text-center font-weight-bold">
                        {{ $row->no_antrian }}
                    </td>

                    <td class="text-center">
                        {{ $row->no_rkm_medis }}
                    </td>

                    <td>
                        <strong>{{ $row->nm_pasien }}</strong><br>
                        <span class="badge badge-warning badge-status">
                            {{ $status }}
                        </span>
                    </td>

                    {{-- PANGGIL --}}
                    <td class="text-center">
                        <button
                            class="btn btn-{{ $cfg['bg'] }} btn-action"
                            onclick="panggil(
                                '{{ $rowId }}',
                                '{{ $row->no_rawat }}',
                                '{{ $row->nm_pasien }}',
                                '{{ $row->jenis_obat }}',
                                '{{ $loket }}'
                            )">
                            🔊
                        </button>
                    </td>

                    {{-- CETAK --}}
                    <td class="text-center">
                        <button
                            class="btn btn-outline-primary btn-action"
                            title="Cetak Antrian {{ $row->jalur }}{{ str_pad($row->no_antrian,3,'0',STR_PAD_LEFT) }}"
                            onclick="window.open(
                                '{{ route('antrianfarmasi.cetak', $row->id) }}',
                                '_blank',
                                'width=400,height=600'
                            )">
                            🖨
                        </button>
                    </td>


                    {{-- SELESAI --}}
                    <td class="text-center">
                        <button
                            class="btn btn-outline-success btn-action"
                            onclick="selesai('{{ $rowId }}', '{{ $row->no_rawat }}')">
                            ✅
                        </button>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Tidak ada antrian
                    </td>
                </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </div>
</div>

@endforeach
</div>

<script>
function panggil(rowId, no_rawat, nama, jenis, loket) {

    fetch('/antrian-farmasi/panggil/proses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ no_rawat })
    });

    const teks = (jenis === 'Racikan')
        ? `Atas nama ${nama}, resep racikan silakan ke loket ${loket}`
        : `Atas nama ${nama}, silakan ke loket ${loket}`;

    const suara = new SpeechSynthesisUtterance(teks);
    suara.lang = 'id-ID';

    speechSynthesis.cancel();
    speechSynthesis.speak(suara);

    const row = document.getElementById('row-' + rowId);
    if (row) row.classList.add('row-panggil');
}

function selesai(rowId, no_rawat) {
    if (!confirm('Tandai antrian ini sebagai selesai?')) return;

    fetch('/antrian-farmasi/selesai', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ no_rawat })
    });

    const row = document.getElementById('row-' + rowId);
    if (row) row.remove();
}
</script>
<script>
function cetakAntrian(no_rawat) {
    const url = `/antrian-farmasi/cetak/${no_rawat}`;
    window.open(url, '_blank', 'width=400,height=600');
}
</script>


@endsection
