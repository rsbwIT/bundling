@extends('layout.layoutDashboard')

@section('title', 'Antrian Pendaftaran Baru')

@section('konten')
{{-- ✅ Bootstrap & Icon --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    body { background:#f4f4f4; }
    .card-header {
        background: linear-gradient(135deg,#0d6efd,#0a58ca);
        color:#fff;font-weight:600;border-bottom:none;
        padding:1rem 1.25rem;border-radius:.5rem .5rem 0 0;
    }
    .filter-section {
        background:#f8f9fa;padding:1rem;border-radius:.5rem;
        margin-bottom:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.08);
    }
    .table thead th {
        background:#f1f3f5;color:#495057;text-transform:uppercase;
        font-size:.85rem;
    }
    .btn-call {
        background: linear-gradient(135deg,#198754,#157347);
        color:#fff;border:none;
    }
    .btn-call:hover { background: #157347; }
</style>

<div class="container-fluid my-4">
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <i class="fa fa-users"></i> Antrian Pendaftaran Baru
        </div>

        <div class="card-body">
            {{-- 🔍 Filter Dokter --}}
            <div class="filter-section">
                <form action="{{ route('antrian.index') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-user-md"></i> Pilih Dokter
                        </label>
                        <select name="dokter" class="form-select">
                            <option value="">-- Semua Dokter --</option>
                            @foreach ($dokters ?? [] as $d)
                                <option value="{{ $d->nm_dokter }}" {{ request('dokter')==$d->nm_dokter?'selected':'' }}>
                                    {{ $d->nm_dokter }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-grid gap-2 d-md-flex justify-content-md-start mt-4">
                        <button type="submit" class="btn btn-primary me-md-2">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        <a href="{{ route('antrian.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- 📋 Tabel Antrian --}}
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr class="text-center">
                            <th>No. Reg</th>
                            <th>Nama Pasien</th>
                            <th>Dokter</th>
                            <th>Jam Mulai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($antrian ?? [] as $item)
                            @php
                                $status = $item->status ?? 'MENUNGGU';
                                $badgeClass = match($status) {
                                    'DIPANGGIL' => 'bg-success',
                                    'SELESAI'   => 'bg-secondary',
                                    default     => 'bg-warning text-dark'
                                };
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $item->no_reg }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td>{{ $item->nm_dokter }}</td>
                                <td class="text-center">{{ $item->jam_mulai }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                </td>
                                <td class="text-center">
                                    {{-- 🔔 Tombol panggil --}}
                                    <div class="btn-group mb-1">
                                        <button class="btn btn-call btn-sm dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-bullhorn"></i> Panggil
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @for ($i = 1; $i <= 7; $i++)
                                                <li>
                                                    <form action="{{ route('antrian.update-status') }}"
                                                          method="POST" class="px-3 py-1 call-form">
                                                        @csrf
                                                        <input type="hidden" name="no_reg" value="{{ $item->no_reg }}">
                                                        <input type="hidden" name="nama_loket" value="Loket {{ $i }}">
                                                        <input type="hidden" name="nm_pasien" value="{{ $item->nm_pasien }}">
                                                        <input type="hidden" name="nm_dokter" value="{{ $item->nm_dokter }}">
                                                        <button type="submit" class="dropdown-item">
                                                            Loket {{ $i }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endfor
                                        </ul>
                                    </div>

                                    {{-- ✅ Tombol selesai --}}
                                    <form action="{{ route('antrian.selesai') }}" method="POST"
                                          onsubmit="return confirm('Tandai antrian ini sudah selesai?');"
                                          style="display:inline-block;">
                                        @csrf
                                        <input type="hidden" name="no_reg" value="{{ $item->no_reg }}">
                                        <button type="submit"
                                            class="btn btn-outline-success btn-sm {{ $status === 'SELESAI' ? 'disabled' : '' }}">
                                            <i class="fas fa-check-circle"></i> Selesai
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Tidak ada antrian untuk dokter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- 🔊 Suara panggilan --}}
<script src="https://code.responsivevoice.org/responsivevoice.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    function formatNama(pasien) {
        pasien = pasien.trim();

        // Nama khusus -> langsung return pelafalan yang benar
        const khusus = {
            "SUHERNIYATI , SH": "Suher Niyati SH",
            "SATUI": "Satui",
            "NIRLAWATY": "Nirla Waty",
            "NG TJONG YONG ALIAS SUJANTO": "Ng Tjong Yong alias Sujanto"
        };
        if (khusus[pasien.toUpperCase()]) {
            return khusus[pasien.toUpperCase()];
        }

        // 🧠 Perbaiki nama umum agar tidak dieja:
        // - jadi huruf kecil semua
        // - kapital di awal kata
        // - hapus koma dan titik
        pasien = pasien
            .toLowerCase()
            .replace(/[,\.]/g, '')
            .split(' ')
            .filter(Boolean)
            .map(w => w.charAt(0).toUpperCase() + w.slice(1))
            .join(' ');

        return pasien;
    }

    document.querySelectorAll('.call-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            let noReg = this.querySelector('input[name="no_reg"]').value.trim();
            let loket = this.querySelector('input[name="nama_loket"]').value.trim();
            let pasien = this.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
            let dokter = this.closest('tr').querySelector('td:nth-child(3)').textContent.trim();

            // ✅ Format nama supaya tidak dieja
            pasien = formatNama(pasien);
            dokter = dokter.replace(/\s+/g, ' ').replace(/[,\.]/g, '');

            const messages = [
                `Nomor antrian ${noReg}`,
                `Pasien ${pasien}`,
                `Menuju ${dokter} di ${loket}`
            ];

            function speakParts(index = 0) {
                if (index >= messages.length) {
                    form.submit();
                    return;
                }

                let textToSpeak = messages[index] + ' .';
                responsiveVoice.speak(textToSpeak, "Indonesian Female", {
                    pitch: 1,
                    rate: 1,
                    volume: 1,
                    wordgap: 0,
                    onend: function() {
                        speakParts(index + 1);
                    }
                });
            }

            speakParts();
        });
    });
});
</script>

@endsection
