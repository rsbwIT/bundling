@extends('layout.layoutDashboard')

@section('title', 'Daftar Pasien Fisioterapi')

@section('konten')

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        {{-- 🔍 FILTER TANGGAL --}}
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

        {{-- 📋 TABEL PASIEN --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>No. Rawat</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->no_rawat }}</td>
                            <td>{{ $row->no_rkm_medis }}</td>
                            <td class="fw-bold">{{ $row->nm_pasien }}</td>
                            <td>{{ $row->nm_poli }}</td>
                            <td>{{ $row->nm_dokter }}</td>
                            <td>

                                @php
                                    // Pecah no_rawat menjadi 4 parameter route
                                    // Format: YYYY/MM/DD/xxxxx
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
                                    // Ambil lembar terakhir dari pasien
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
                            <td colspan="6" class="text-center text-muted">
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
