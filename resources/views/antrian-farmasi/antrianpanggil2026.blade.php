@extends('layout.layoutDashboard')

@section('title', 'Panggil Antrian Farmasi')

@section('konten')

@php
    $loketList = [
        'A' => ['label' => 'BPJS – Racikan',        'bg' => 'success'],
        'B' => ['label' => 'BPJS – Non Racikan',    'bg' => 'info'],
        'C' => ['label' => 'NON BPJS – Non Racikan','bg' => 'warning'],
        'D' => ['label' => 'NON BPJS – Racikan',    'bg' => 'danger'],
    ];
@endphp

<div class="row">

@foreach ($loketList as $loket => $cfg)
@php
    $list = $antrian[$loket] ?? collect();
@endphp

<div class="col-md-3">
    <div class="card shadow-sm mb-3 h-100">

        <div class="card-header bg-{{ $cfg['bg'] }} text-white text-center">
            <strong>LOKET {{ $loket }}</strong>
            <div style="font-size:12px">{{ $cfg['label'] }}</div>
        </div>

        <div class="card-body p-0" style="max-height:420px; overflow-y:auto">
            <table class="table table-bordered table-sm mb-0">
                <thead class="sticky-top bg-light">
                    <tr class="text-center">
                        <th width="30">No</th>
                        <th>No RM</th>
                        <th>Nama</th>
                        <th width="40">🔊</th>
                        <th width="40">✅</th>
                    </tr>
                </thead>
                <tbody>

                @forelse ($list as $i => $row)
                @php
                    $status = strtoupper(trim($row->status_panggil));
                @endphp

                    <tr id="row-{{ $row->no_rawat }}">
                        <td class="text-center">{{ $row->no_antrian }}</td>
                        <td>{{ $row->no_rkm_medis }}</td>
                        <td>{{ $row->nm_pasien }}</td>

                        {{-- 🔊 PANGGIL --}}
                        <td class="text-center">
                            @if($status === 'MENUNGGU')
                                <button
                                    class="btn btn-sm btn-{{ $cfg['bg'] }}"
                                    onclick="panggil('{{ $row->no_rawat }}','{{ $row->nm_pasien }}','{{ $row->jenis_obat }}','{{ $loket }}')">
                                    🔊
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" disabled>–</button>
                            @endif
                        </td>

                        {{-- ✅ SELESAI --}}
                        <td class="text-center">
                            {{-- Tombol SELESAI muncul untuk status MENUNGGU atau DIPANGGIL --}}
                            @if($status === 'MENUNGGU' || $status === 'DIPANGGIL')
                                <button
                                    class="btn btn-sm btn-outline-success"
                                    onclick="selesai('{{ $row->no_rawat }}')">
                                    ✅
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" disabled>–</button>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
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

{{-- ================= SCRIPT ================= --}}
<script>
function panggil(no_rawat, nama, jenis, loket) {
    // Tombol panggil bisa ditekan berkali-kali, jadi tidak disable
    fetch('/antrian-farmasi/panggil/proses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ no_rawat })
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'ok') {
            // Teks suara menyertakan loket
            let teks = (jenis === 'Racikan')
                ? `Atas nama ${nama}, resep racikan silakan menuju loket ${loket}`
                : `Atas nama ${nama}, silakan menuju loket ${loket}`;

            const suara = new SpeechSynthesisUtterance(teks);
            suara.lang = 'id-ID';
            suara.rate = jenis === 'Racikan' ? 0.85 : 0.95;

            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(suara);

            // Opsi: update status di tabel menjadi DIPANGGIL agar tombol selesai aktif
            let row = document.getElementById('row-' + no_rawat);
            if(row) {
                let tdSelesai = row.querySelectorAll('td')[4]; // kolom ke 5
                tdSelesai.innerHTML = `<button class="btn btn-sm btn-outline-success" onclick="selesai('${no_rawat}')">✅</button>`;
            }
        }
    });
}

function selesai(no_rawat) {
    if(!confirm('Tandai antrian ini sebagai selesai?')) return;

    fetch('/antrian-farmasi/selesai', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ no_rawat })
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'ok') {
            let row = document.getElementById('row-' + no_rawat);
            if(row) row.remove();
        }
    });
}
</script>

@endsection
