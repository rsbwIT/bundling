@extends('layout.layoutDashboard')

@section('title', 'Croscek Pasien Pulang')

@section('konten')

<div class="container-fluid">

<div class="card shadow-sm border-0 rounded-4 mt-4">
<div class="card-body">

{{-- ================= FILTER TANGGAL ================= --}}
<form method="GET" action="{{ route('bpjs.croscekpasienpulang') }}">
    <div class="row g-3 mb-4 align-items-end">

        <div class="col-md-3">
            <label class="form-label fw-semibold">Tanggal Dari</label>
            <input type="date"
                   name="tanggal_dari"
                   class="form-control"
                   value="{{ $tanggalDari }}">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Tanggal Sampai</label>
            <input type="date"
                   name="tanggal_sampai"
                   class="form-control"
                   value="{{ $tanggalSampai }}">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                Filter
            </button>
        </div>

    </div>
</form>

{{-- ================= TABLE ================= --}}
<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
<thead class="table-primary text-center">
<tr>
    <th>No</th>
    <th>No Rawat</th>
    <th>No RM</th>
    <th>Nama Pasien</th>
    <th>Bangsal / Kamar</th>
    <th>Tgl Masuk</th>
    <th>Tgl Keluar</th>
    <th>Lama</th>
    <th>Status Pulang</th>
    <th width="8%">Aksi</th>
</tr>
</thead>

<tbody>
@forelse($data as $i => $row)
<tr>
    <td class="text-center">{{ $i + 1 }}</td>
    <td>{{ $row->no_rawat }}</td>
    <td>{{ $row->no_rkm_medis }}</td>
    <td class="fw-bold">{{ $row->nm_pasien }}</td>
    <td>
        <span class="badge bg-info text-dark">
            {{ $row->bangsal_kamar }}
        </span>
    </td>
    <td class="text-center">{{ $row->tgl_masuk }}</td>
    <td class="text-center">{{ $row->tgl_keluar }}</td>
    <td class="text-center">{{ $row->lama }} hari</td>
    <td class="text-center">
        @if($row->stts_pulang == 'Sehat')
            <span class="badge bg-success">Sehat</span>
        @elseif($row->stts_pulang == 'Rujuk')
            <span class="badge bg-warning text-dark">Rujuk</span>
        @elseif($row->stts_pulang == 'Meninggal')
            <span class="badge bg-danger">Meninggal</span>
        @else
            <span class="badge bg-secondary">{{ $row->stts_pulang }}</span>
        @endif
    </td>
    <td class="text-center">
        <button
            class="btn btn-sm btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalDetailPasien"
            data-norawat="{{ $row->no_rawat }}"
            data-norm="{{ $row->no_rkm_medis }}"
            data-nama="{{ $row->nm_pasien }}"
            data-bangsal="{{ $row->bangsal_kamar }}"
            data-masuk="{{ $row->tgl_masuk }}"
            data-keluar="{{ $row->tgl_keluar }}"
            data-lama="{{ $row->lama }}"
            data-status="{{ $row->stts_pulang }}"
        >
            Detail
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center text-muted">
        Tidak ada data pasien pulang
    </td>
</tr>
@endforelse
</tbody>
</table>
</div>

</div>
</div>
</div>

{{-- ================= MODAL DETAIL ================= --}}
<div class="modal fade" id="modalDetailPasien" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Detail Pasien Pulang</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <table class="table table-sm table-borderless">
            <tr><th width="35%">No Rawat</th><td id="m_no_rawat"></td></tr>
            <tr><th>No RM</th><td id="m_no_rm"></td></tr>
            <tr><th>Nama Pasien</th><td id="m_nama"></td></tr>
            <tr><th>Bangsal / Kamar</th><td id="m_bangsal"></td></tr>
            <tr><th>Tanggal Masuk</th><td id="m_masuk"></td></tr>
            <tr><th>Tanggal Keluar</th><td id="m_keluar"></td></tr>
            <tr><th>Lama Rawat</th><td id="m_lama"></td></tr>
            <tr><th>Status Pulang</th><td id="m_status"></td></tr>
        </table>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

{{-- ================= SCRIPT MODAL ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let modal = document.getElementById('modalDetailPasien');

    modal.addEventListener('show.bs.modal', function (event) {
        let b = event.relatedTarget;

        document.getElementById('m_no_rawat').innerText = b.dataset.norawat;
        document.getElementById('m_no_rm').innerText    = b.dataset.norm;
        document.getElementById('m_nama').innerText     = b.dataset.nama;
        document.getElementById('m_bangsal').innerText  = b.dataset.bangsal;
        document.getElementById('m_masuk').innerText    = b.dataset.masuk;
        document.getElementById('m_keluar').innerText   = b.dataset.keluar;
        document.getElementById('m_lama').innerText     = b.dataset.lama + ' hari';
        document.getElementById('m_status').innerText   = b.dataset.status;
    });
});
</script>

{{-- ================= BOOTSTRAP JS (WAJIB) ================= --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@endsection
