@extends('layout.layoutDashboard')

@section('title', 'Pemesanan Farmasi')

@section('konten')
<style>
/* ================= CARD ================= */
.card-modern{
    border-radius:12px;
    border:none;
    box-shadow:0 6px 20px rgba(0,0,0,.08);
}
.card-modern .card-header{
    background:linear-gradient(135deg,#0d6efd,#20c997);
    color:#fff;
    padding:12px 18px;
}

/* ================= FILTER ================= */
.filter-group{
    background:#f8f9fa;
    border-radius:10px;
    padding:12px;
}
.filter-label{
    font-size:12px;
    font-weight:600;
}

/* ================= TABLE ================= */
.table-modern{
    font-size:12.5px;
    white-space:nowrap;
}
.table-modern thead th{
    background:#f1f3f5;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.4px;
    text-align:center;
}
.table-modern tbody td{
    padding:8px 10px;
    vertical-align:middle;
}
.table-modern tbody tr:hover{
    background:#eef4ff;
}

/* ================= MONEY ================= */
.text-money{
    font-family:monospace;
    font-weight:700;
    color:#198754;
    text-align:right;
}

/* ================= BADGE ================= */
.badge-soft{
    padding:5px 12px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
}
.badge-soft-info{background:#e7f1ff;color:#084298;}
.badge-soft-success{background:#d1e7dd;color:#0f5132;}

/* ================= BUTTON ================= */
.btn-sm{
    font-size:11px;
    padding:5px 14px;
}

/* ================= MODAL ================= */
.modal-modern{
    border-radius:14px;
    border:none;
    box-shadow:0 10px 40px rgba(0,0,0,.25);
}
.modal-header-modern{
    background:linear-gradient(135deg,#fd7e14,#ffc107);
    color:#fff;
    border:none;
    padding:16px 20px;
    border-radius:14px 14px 0 0;
}
.icon-circle{
    width:38px;
    height:38px;
    background:rgba(255,255,255,.25);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* tombol modal  */
.btn-modern-cancel{
    min-width:120px;
    border-radius:10px;
    font-weight:500;
    transition:all .2s ease;
}

.btn-modern-cancel:hover{
    background:#f8f9fa;
    transform:translateY(-1px);
}

.btn-modern-save{
    min-width:150px;
    border-radius:10px;
    font-weight:600;
    box-shadow:0 4px 10px rgba(25,135,84,.25);
    transition:all .2s ease;
}

.btn-modern-save:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 14px rgba(25,135,84,.35);
}

/* ================= COMPACT MODE ================= */

/* ===== ALIGNMENT FIX ===== */
.filter-box,
.filter-action{
    background:#f8f9fa;
    border-radius:14px;
    padding:10px 14px;
    height:100%;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

/* label konsisten */
.filter-label{
    font-size:11.5px;
    font-weight:600;
    margin-bottom:6px;
    display:flex;
    align-items:center;
    gap:6px;
}

/* s.d sejajar */
.filter-sd{
    font-size:11px;
    color:#6c757d;
    display:flex;
    align-items:center;
}

/* input compact */
.filter-box .form-control,
.filter-box .form-select{
    height:36px;
    font-size:12px;
    border-radius:9px;
}

/* action khusus */
.filter-action{
    gap:8px;
}

.btn-filter,
.btn-reset{
    height:36px;
    border-radius:9px;
    font-size:12px;
}

/* rapatkan jarak vertikal */
.row.g-3{
    --bs-gutter-y:0.6rem;
}

</style>

<style>
    /* ================= SUMMARY CARD ================= */

.summary-card{
    border-radius:16px;
    border:none;
    box-shadow:0 6px 18px rgba(0,0,0,.06);
    transition:all .25s ease;
    background:#fff;
}

.summary-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 28px rgba(0,0,0,.10);
}

.summary-body{
    padding:18px 20px;
    display:flex;
    align-items:center;
    gap:16px;
}

/* Icon */
.summary-icon{
    width:52px;
    height:52px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    color:#fff;
    flex-shrink:0;
}

.summary-icon.bg-info{
    background:linear-gradient(135deg,#0dcaf0,#0d6efd);
}
.summary-icon.bg-success{
    background:linear-gradient(135deg,#20c997,#198754);
}
.summary-icon.bg-warning{
    background:linear-gradient(135deg,#ffc107,#fd7e14);
}

/* Text */
.summary-title{
    font-size:12px;
    font-weight:600;
    color:#6c757d;
    margin-bottom:4px;
    letter-spacing:.3px;
}

.summary-value{
    font-size:22px;
    font-weight:700;
    color:#212529;
    line-height:1;
}

/* Mobile biar tetap enak */
@media (max-width: 576px){
    .summary-value{
        font-size:20px;
    }
}

/* ================= TABLE SCROLL ONLY ================= */
.table-scroll{
    max-height:460px;           /* tinggi scroll */
    overflow-y:auto;
    overflow-x:auto;
}

/* header tetap */
.table-scroll thead th{
    position:sticky;
    top:0;
    z-index:5;
    background:#f1f3f5;
}

/* scrollbar halus */
.table-scroll::-webkit-scrollbar{
    width:8px;
    height:8px;
}
.table-scroll::-webkit-scrollbar-thumb{
    background:#cfd4da;
    border-radius:8px;
}
.table-scroll::-webkit-scrollbar-track{
    background:#f8f9fa;
}

</style>

<div class="container-fluid">


{{-- total sudah bayar dan belum bayar  --}}
<div class="row g-3 mb-3">

    {{-- TOTAL SUPPLIER --}}
    <div class="col-xl-4 col-md-6">
        <div class="card summary-card h-100">
            <div class="summary-body">
                <div class="summary-icon bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="summary-title">Total Supplier</div>
                    <div class="summary-value">{{ $total_pasien ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUDAH DIBAYAR --}}
    <div class="col-xl-4 col-md-6">
        <div class="card summary-card h-100">
            <div class="summary-body">
                <div class="summary-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="summary-title">Sudah Dibayar</div>
                    <div class="summary-value">{{ $sudah_dibayar ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- BELUM DIBAYAR --}}
    <div class="col-xl-4 col-md-6">
        <div class="card summary-card h-100">
            <div class="summary-body">
                <div class="summary-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="summary-title">Belum Dibayar</div>
                    <div class="summary-value">{{ $belum_dibayar ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ================= FILTER ================= --}}
<div class="card card-modern mb-3">
    <div class="card-body">

        <form method="GET">

            {{-- ===== ROW 1 ===== --}}
            <div class="row g-3">

                {{-- TANGGAL DATANG --}}
                <div class="col-xl-4 col-lg-6">
                    <div class="filter-box">
                        <label class="filter-label">
                            <input type="checkbox" name="filter_pesan"
                                   {{ request('filter_pesan') ? 'checked' : '' }}>
                            Tanggal Datang
                        </label>

                        <div class="row g-2">
                            <div class="col">
                                <input type="date" name="tgl_pesan_dari"
                                       class="form-control"
                                       value="{{ request('tgl_pesan_dari') ?? now()->format('Y-m-d') }}"
                                       {{ request('filter_pesan') ? '' : 'disabled' }}>
                            </div>
                            <div class="col-auto filter-sd">s.d</div>
                            <div class="col">
                                <input type="date" name="tgl_pesan_sampai"
                                       class="form-control"
                                       value="{{ request('tgl_pesan_sampai') ?? now()->format('Y-m-d') }}"
                                       {{ request('filter_pesan') ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TANGGAL TEMPO --}}
                <div class="col-xl-4 col-lg-6">
                    <div class="filter-box">
                        <label class="filter-label">
                            <input type="checkbox" name="filter_tempo"
                                   {{ request('filter_tempo') ? 'checked' : '' }}>
                            Tanggal Tempo
                        </label>

                        <div class="row g-2">
                            <div class="col">
                                <input type="date" name="tgl_tempo_dari"
                                       class="form-control"
                                       value="{{ request('tgl_tempo_dari') ?? now()->format('Y-m-d') }}"
                                       {{ request('filter_tempo') ? '' : 'disabled' }}>
                            </div>
                            <div class="col-auto filter-sd">s.d</div>
                            <div class="col">
                                <input type="date" name="tgl_tempo_sampai"
                                       class="form-control"
                                       value="{{ request('tgl_tempo_sampai') ?? now()->format('Y-m-d') }}"
                                       {{ request('filter_tempo') ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STATUS --}}
                <div class="col-xl-4 col-lg-6">
                    <div class="filter-box">
                        <label class="filter-label">Status Pembayaran</label>
                        <select name="status_bayar" class="form-select">
                            <option value="">Semua</option>
                            <option value="Belum Dibayar" {{ request('status_bayar')=='Belum Dibayar'?'selected':'' }}>
                                Belum Dibayar
                            </option>
                            <option value="Sudah Dibayar" {{ request('status_bayar')=='Sudah Dibayar'?'selected':'' }}>
                                Sudah Dibayar
                            </option>
                        </select>
                    </div>
                </div>

            </div>

            {{-- ===== ROW 2 ===== --}}
            <div class="row g-3 mt-1">

                {{-- SUPPLIER --}}
                <div class="col-xl-4 col-lg-6">
                    <div class="filter-box">
                        <label class="filter-label">Supplier</label>
                        <select name="supplier" class="form-select select2">
                            <option value="">Semua Supplier</option>
                            @foreach($suppliers as $sp)
                                <option value="{{ $sp->kode_suplier }}"
                                    {{ request('supplier')==$sp->kode_suplier?'selected':'' }}>
                                    {{ $sp->nama_suplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- BANGSAL --}}
                <div class="col-xl-4 col-lg-6">
                    <div class="filter-box">
                        <label class="filter-label">Bangsal</label>
                        <select name="bangsal" class="form-select">
                            <option value="">Semua Bangsal</option>
                            @foreach($bangsals as $bg)
                                <option value="{{ $bg->kd_bangsal }}"
                                    {{ request('bangsal')==$bg->kd_bangsal?'selected':'' }}>
                                    {{ $bg->nm_bangsal }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ACTION --}}
                <div class="col-xl-4 col-lg-6">
                    <div class="filter-action">
                        <button class="btn btn-primary btn-filter w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ url()->current() }}"
                           class="btn btn-reset border w-100">
                            Reset
                        </a>
                    </div>
                </div>

            </div>

        </form>

    </div>
</div>

{{-- ================= TABLE ================= --}}
<div class="card card-modern">
    {{-- <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-file-invoice mr-2"></i> Pemesanan Farmasi
        </h6>
    </div> --}}

    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-modern table-hover mb-0">
                <thead>
                    <tr>
                        <th>No Faktur</th>
                        <th>No Pajak</th>
                        <th class="text-start">Supplier</th>
                        <th>No Order</th>
                        <th class="text-start">Bangsal</th>
                        <th>Tgl Datang</th>
                        <th>Tgl Faktur</th>
                        <th>Tgl Tempo</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">PPN (Total)</th>
                        <th>Status</th>
                        <th class="text-end">DPP</th>
                        <th class="text-end">DPP Nilai Lain</th>
                        <th class="text-end">PPN (DPP)</th>
                        <th class="text-end">Selisih</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($data as $row)

                @php
                    $total = $row->tagihan ?? 0;

                    // 1️⃣ DPP
                    $dpp = $total > 0 ? round($total / 1.11, 0) : 0;

                    // 2️⃣ PPN dari Total
                    $ppn_total = $total > 0 ? round(($total / 1.11) * 0.11, 0) : 0;

                    // 3️⃣ DPP Nilai Lain
                    $dpp_lain = round($dpp * 11 / 12, 0);

                    // 4️⃣ PPN dari DPP Nilai Lain (12%)
                    $ppn_dpp = round($dpp_lain * 0.12, 0);

                    // 5️⃣ Selisih (PPN Total - PPN DPP)
                    $selisih = $ppn_total - $ppn_dpp;
                @endphp

                <tr>
                    <td class="text-center text-primary fw-bold">
                        {{ $row->no_faktur }}
                    </td>

                    <td class="text-center">
                        @if($row->no_pajak=='Belum Ada')
                            <button class="btn btn-sm btn-outline-warning rounded-circle"
                                    data-toggle="modal"
                                    data-target="#modalPajak"
                                    data-faktur="{{ $row->no_faktur }}">
                                <i class="fas fa-plus"></i>
                            </button>
                        @else
                            <span class="badge badge-soft badge-soft-success">
                                {{ $row->no_pajak }}
                            </span>
                        @endif
                    </td>

                    <td>{{ $row->nama_suplier }}</td>
                    <td class="text-center">{{ $row->no_order ?? '-' }}</td>
                    <td>{{ $row->nm_bangsal }}</td>
                    <td class="text-center">{{ date('d-m-Y',strtotime($row->tgl_pesan)) }}</td>
                    <td class="text-center">{{ date('d-m-Y',strtotime($row->tgl_faktur)) }}</td>
                    <td class="text-center">{{ date('d-m-Y',strtotime($row->tgl_tempo)) }}</td>

                    <!-- TOTAL -->
                    <td class="text-money text-end">
                        {{ number_format($total,0,',','.') }}
                    </td>

                    <!-- PPN TOTAL -->
                    <td class="text-money text-end">
                        {{ number_format($ppn_total,0,',','.') }}
                    </td>

                    <!-- STATUS -->
                    <td class="text-center">
                        @if($row->status=='Sudah Dibayar')
                            <span class="badge badge-soft badge-soft-success">
                                Sudah Dibayar
                            </span>
                        @else
                            <span class="badge badge-soft badge-soft-info">
                                Belum Dibayar
                            </span>
                        @endif
                    </td>

                    <!-- DPP -->
                    <td class="text-money text-end">
                        {{ number_format($dpp,0,',','.') }}
                    </td>

                    <!-- DPP NILAI LAIN -->
                    <td class="text-money text-end">
                        {{ number_format($dpp_lain,0,',','.') }}
                    </td>

                    <!-- PPN DPP -->
                    <td class="text-money text-end">
                        {{ number_format($ppn_dpp,0,',','.') }}
                    </td>

                    <!-- SELISIH -->
                    <td class="text-money text-end {{ $selisih != 0 ? 'text-danger fw-bold' : '' }}">
                        {{ number_format($selisih,0,',','.') }}
                    </td>

                </tr>

                @empty
                    <tr>
                        <td colspan="15" class="text-center text-muted py-4">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
</div>

</div>

{{-- ================= MODAL INPUT PAJAK (CORPORATE STYLE) ================= --}}
<div class="modal fade" id="modalPajak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow"
             style="border-radius:16px; overflow:hidden;">

            <form method="POST" action="{{ route('pajak.simpan') }}">
                @csrf

                {{-- ACCENT TOP LINE --}}
                <div style="height:6px; background:#198754;"></div>

                {{-- HEADER --}}
                <div class="px-4 pt-4 pb-3 border-bottom bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">
                                Input Nomor Pajak
                            </h6>
                            <small class="text-muted">
                                Data perpajakan untuk transaksi pembelian
                            </small>
                        </div>
                        <button type="button"
                                class="close"
                                data-dismiss="modal"
                                style="font-size:20px;">
                            &times;
                        </button>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="modal-body px-4 py-4 bg-white">

                    <input type="hidden" name="no_faktur" id="no_faktur">

                    {{-- INFO FAKTUR --}}
                    <div class="mb-4 p-3"
                         style="
                            background:#f8f9fa;
                            border-left:4px solid #198754;
                            border-radius:8px;
                         ">
                        <div class="text-muted small">
                            Nomor Faktur
                        </div>
                        <div class="fw-bold text-dark"
                             style="font-size:15px;"
                             id="label_faktur">
                            -
                        </div>
                    </div>

                    {{-- INPUT FIELD --}}
                    <div class="form-group mb-0">
                        <label class="fw-bold small mb-2 text-dark">
                            Nomor Pajak
                        </label>
                        <input type="text"
                               name="no_pajak"
                               class="form-control"
                               placeholder="Contoh: 010.000-23.12345678"
                               required
                               style="
                                   height:44px;
                                   border-radius:8px;
                                   font-size:14px;
                               ">
                        <small class="text-muted">
                            Masukkan nomor pajak sesuai dokumen resmi.
                        </small>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer bg-white border-top px-4 py-3">

                    <button type="button"
                            class="btn btn-outline-secondary btn-modern-cancel"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-success btn-modern-save">
                        <i class="fas fa-check-circle mr-1"></i>
                        Simpan Data
                    </button>

                </div>
            </form>

        </div>
    </div>
</div>


{{-- ================= SCRIPT ================= --}}
<script>
$('.toggle-filter').on('change', function(){
    $(this).closest('.filter-group')
           .find('input[type="date"]')
           .prop('disabled', !this.checked);
});

$('#modalPajak').on('show.bs.modal', function(e){
    let faktur = $(e.relatedTarget).data('faktur');
    $('#no_faktur').val(faktur);
    $('#label_faktur').text(faktur);
});
</script>
<script>
$('input[type=checkbox]').on('change', function(){
    $(this).closest('.filter-box')
           .find('input[type=date]')
           .prop('disabled', !this.checked);
});
</script>


@endsection
