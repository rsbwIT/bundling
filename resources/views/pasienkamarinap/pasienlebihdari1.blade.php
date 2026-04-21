@extends('layout.layoutDashboard')

@section('title', 'Pasien Lebih Dari 1x Berobat')

@section('konten')

<style>
/* ===== MODAL MODERN ===== */
.modal-content{
    border-radius:14px;
    border:none;
    box-shadow:0 10px 35px rgba(0,0,0,.2);
    animation:fadeInUp .25s ease;
}

@keyframes fadeInUp{
    from{transform:translateY(20px);opacity:0;}
    to{transform:translateY(0);opacity:1;}
}

/* icon circle */
.icon-circle{
    width:42px;
    height:42px;
    border-radius:50%;
    background:rgba(255,255,255,0.25);
    display:flex;
    align-items:center;
    justify-content:center;
}

/* hover isi modal */
#detailBody tr:hover{
    background:#eef4ff;
}

/* badge jumlah */
.badge-jml{
    font-size:12px;
    padding:6px 10px;
    border-radius:20px;
}
</style>

<div class="container-fluid">

    {{-- ================= SUMMARY ================= --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Pasien</h6>
                    <h4>{{ $total_pasien }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Kunjungan</h6>
                    <h4>{{ $total_kunjungan }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= FILTER ================= --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label>Dari</label>
                        <input type="date" name="dari" class="form-control" value="{{ $dari }}">
                    </div>

                    <div class="col-md-4">
                        <label>Sampai</label>
                        <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card">
        <div class="card-body p-0">

            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>Bulan</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($data as $key => $row)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $row->no_rkm_medis }}</td>
                        <td>{{ $row->nm_pasien }}</td>
                        <td>{{ $row->bulan }}</td>
                        <td>
                            <span class="badge bg-success badge-jml">
                                {{ $row->jumlah_kunjungan }}x
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-detail"
                                data-norm="{{ $row->no_rkm_medis }}"
                                data-bulan="{{ $row->bulan }}">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>


{{-- ================= MODAL ================= --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header text-white border-0"
                 style="background:linear-gradient(135deg,#0d6efd,#20c997);">

                <div class="d-flex align-items-center gap-2">
                    <div class="icon-circle">
                        <i class="fas fa-user-injured"></i>
                    </div>

                    <div>
                        <h6 class="mb-0">Detail Kunjungan Pasien</h6>
                        <small style="opacity:.8">Riwayat dalam 1 bulan</small>
                    </div>
                </div>

                <button type="button" class="close text-white" data-dismiss="modal">
                    &times;
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-alt me-1"></i> Tanggal</th>
                            <th><i class="fas fa-clinic-medical me-1"></i> Poli</th>
                            <th><i class="fas fa-user-md me-1"></i> Dokter</th>
                        </tr>
                    </thead>
                    <tbody id="detailBody"></tbody>
                </table>

            </div>

        </div>
    </div>
</div>


{{-- ================= SCRIPT ================= --}}
<script>
$(document).on('click', '.btn-detail', function(){

    let no_rm = $(this).data('norm');
    let bulan = $(this).data('bulan');

    $.get("{{ url('/pasien-lebih-dari-1/detail') }}", {
        no_rkm_medis: no_rm,
        bulan: bulan
    }, function(res){

        let html = '';

        res.forEach(row => {
            html += `
                <tr>
                    <td>${row.tgl_registrasi}</td>
                    <td>${row.nm_poli}</td>
                    <td>${row.nm_dokter}</td>
                </tr>
            `;
        });

        $('#detailBody').html(html);
        $('#modalDetail').modal('show');

    });

});
</script>

@endsection