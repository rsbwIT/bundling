@extends('layout.layoutDashboard')
@section('title', 'Jadwal Operasi')

@section('konten')
<div class="container-fluid px-4 py-3">

    {{-- 🔹 Header + Tombol Tambah --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary m-0">
            📅 Jadwal Operasi Pasien
        </h4>
        <button class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
        </button>
    </div>

    {{-- 🔹 Modal Tambah Jadwal --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-success text-white">
                    <h6 class="modal-title">Input Jadwal Operasi</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/jadwal-operasi/store') }}" method="POST" class="row g-3 p-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label mb-1">Pasien (Dirawat & Belum Pulang)</label>
                        <select name="no_rawat" id="no_rawat" class="form-select form-select-sm rounded-pill" required>
                            <option value="">-- Pilih Pasien --</option>
                            @forelse ($pasien_dirawat ?? [] as $p)
                                <option value="{{ $p->no_rawat }}">
                                    {{ $p->no_rawat }} - {{ $p->nm_pasien }} ({{ $p->nm_bangsal }})
                                </option>
                            @empty
                                <option disabled>Data pasien belum tersedia</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Dokter Operator</label>
                        <select name="kd_dokter" id="kd_dokter" class="form-select form-select-sm rounded-pill" disabled required>
                            <option value="">-- Pilih Dokter --</option>
                            @foreach ($dokter ?? [] as $d)
                                <option value="{{ $d->kd_dokter }}">{{ $d->nm_dokter }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Ruang OK</label>
                        <select name="kd_ruang_ok" id="kd_ruang_ok" class="form-select form-select-sm rounded-pill" disabled required>
                            <option value="">-- Pilih Ruang --</option>
                            @foreach ($ruang_ok ?? [] as $r)
                                <option value="{{ $r->kd_ruang_ok }}">{{ $r->nm_ruang_ok }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Jenis / Paket Operasi</label>
                        <select name="kode_paket" id="kode_paket" class="form-select form-select-sm rounded-pill" disabled required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach ($paket_operasi ?? [] as $p)
                                <option value="{{ $p->kode_paket }}">{{ $p->nm_perawatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm rounded-pill"
                            value="{{ date('Y-m-d') }}" required disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control form-control-sm rounded-pill" required disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control form-control-sm rounded-pill" required disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm rounded-pill" required disabled>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div class="col-md-12 text-end mt-2">
                        <button type="submit" id="btnSimpan" class="btn btn-success btn-sm rounded-pill px-3" disabled>
                            <i class="bi bi-check-circle me-1"></i> Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 🔹 Tabel Jadwal Operasi --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>No. Rawat</th>
                            <th>Nama Pasien</th>
                            <th>Dokter</th>
                            <th>Ruang OK</th>
                            <th>Paket Operasi</th>
                            <th>Tanggal</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwal_operasi ?? [] as $item)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td class="text-start">{{ $item->nm_pasien }}</td>
                                <td>{{ $item->nm_dokter }}</td>
                                <td>{{ $item->nm_ruang_ok }}</td>
                                <td>{{ $item->nm_perawatan }}</td>
                                <td>{{ $item->tanggal }}</td>
                                <td>{{ $item->jam_mulai }}</td>
                                <td>{{ $item->jam_selesai }}</td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($item->status == 'Menunggu') bg-warning text-dark
                                        @elseif($item->status == 'Selesai') bg-success
                                        @else bg-secondary @endif">
                                        {{ strtoupper($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $item->no_rawat }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ url('/jadwal-operasi/delete/'.$item->no_rawat) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    Tidak ada jadwal operasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 🔹 Script aktifkan form setelah pilih pasien --}}
<script>
    document.getElementById('no_rawat').addEventListener('change', function() {
        const isSelected = this.value !== '';
        document.querySelectorAll('#kd_dokter, #kd_ruang_ok, #kode_paket, input[name="tanggal"], input[name="jam_mulai"], input[name="jam_selesai"], select[name="status"], #btnSimpan')
            .forEach(el => el.disabled = !isSelected);
    });
</script>
@endsection
