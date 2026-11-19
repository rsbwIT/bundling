@extends('layout.layoutDashboard')
@section('title', 'Mapping Parameter LIS Softmedik')

@push('styles')
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<style>
    .select2-container .select2-selection--single {
        height: 31px !important;
        font-size: 12px !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 22px !important;
    }
</style>
@endpush

@section('konten')
<div class="container-fluid py-3">

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-network-wired text-primary mr-2"></i> Mapping Parameter LIS Softmedik ke Tindakan Lab Khanza
            </h5>
            <small class="text-muted">Hubungkan kode pemeriksaan dari alat LIS Softmedik ke tindakan dan template hasil laboratorium SIMRS Khanza.</small>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Parameter LIS Baru
            </button>
            <a href="{{ url('/bridging-lis') }}" class="btn btn-sm btn-outline-secondary ml-1">
                <i class="fas fa-arrow-left mr-1"></i> Bridging LIS
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 13px;">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-2 border-bottom">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <form action="{{ route('lab.mapping-lis') }}" method="GET" class="form-inline">
                        <div class="input-group input-group-sm w-100">
                            <input type="text" name="cari" class="form-control" placeholder="Cari Kode Test / Nama Test / Tindakan..." value="{{ $cari ?? '' }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                @if(!empty($cari))
                                    <a href="{{ route('lab.mapping-lis') }}" class="btn btn-secondary" title="Reset">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-7 text-right">
                    <span class="badge badge-info px-2 py-1" style="font-size: 12px;">
                        <i class="fas fa-database mr-1"></i> Total: {{ $mappings->total() }} Parameter LIS
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-bordered table-striped align-middle mb-0" style="font-size: 12.5px;">
                <thead class="bg-primary text-white">
                    <tr>
                        <th style="width: 45px;" class="text-center">No</th>
                        <th style="width: 170px;">Kode Test LIS (Softmedik)</th>
                        <th style="width: 220px;">Nama Test Softmedik</th>
                        <th style="width: 340px;">Tindakan Lab Khanza (`kd_jenis_prw`)</th>
                        <th style="width: 280px;">Item Template Khanza (`id_template`)</th>
                        <th style="width: 90px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mappings as $index => $item)
                        <tr id="row-{{ $item->id }}">
                            <td class="text-center align-middle font-weight-bold">{{ $mappings->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <span class="badge badge-primary px-2 py-1 font-monospace" style="font-size: 12px; letter-spacing: 0.5px;">
                                    {{ $item->kode_softmedik }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <strong>{{ $item->nama_test_softmedik }}</strong>
                            </td>
                            <td class="align-middle">
                                <select class="form-control form-control-sm select-tindakan-ajax" data-id="{{ $item->id }}" style="width: 100%;">
                                    @if($item->kd_jenis_prw)
                                        <option value="{{ $item->kd_jenis_prw }}" selected>
                                            {{ $item->kd_jenis_prw }} - {{ $item->nama_tindakan_terpilih ?? $item->nama_pemeriksaan_khanza }}
                                        </option>
                                    @else
                                        <option value="">-- Ketik untuk Cari Tindakan Lab --</option>
                                    @endif
                                </select>
                            </td>
                            <td class="align-middle">
                                <select class="form-control form-control-sm select-template-ajax" data-id="{{ $item->id }}" style="width: 100%;">
                                    @if($item->id_template_khanza)
                                        <option value="{{ $item->id_template_khanza }}" selected>
                                            #{{ $item->id_template_khanza }} - {{ $item->nama_template_terpilih }}
                                        </option>
                                    @else
                                        <option value="">-- Tidak Terikat Template / Langsung Tindakan --</option>
                                    @endif
                                </select>
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-primary btn-save px-2 py-1" data-id="{{ $item->id }}" title="Simpan Mapping">
                                    <i class="fas fa-save"></i>
                                </button>
                                <form action="{{ route('lab.mapping-lis.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mapping ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                Belum ada data mapping LIS Softmedik yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
            <span class="text-muted" style="font-size: 12px;">
                Menampilkan {{ $mappings->firstItem() ?? 0 }} - {{ $mappings->lastItem() ?? 0 }} dari {{ $mappings->total() }} parameter
            </span>
            <div>
                {{ $mappings->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Parameter Softmedik -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('lab.mapping-lis.store') }}" method="POST" id="formTambahMapping">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="font-size: 15px; font-weight: 700;">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Parameter Test LIS Softmedik
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="font-size: 13px;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Kode Test Softmedik (`test_id`): <span class="text-danger">*</span></label>
                            <input type="text" name="kode_softmedik" class="form-control form-control-sm font-monospace" placeholder="Contoh: HB, GLU, CREAT, NA..." required>
                            <small class="text-muted">Kode murni parameter di alat/server LIS Softmedik.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Nama Test Softmedik: <span class="text-danger">*</span></label>
                            <input type="text" name="nama_test_softmedik" class="form-control form-control-sm" placeholder="Contoh: Hemoglobin, Glukosa Darah..." required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Tindakan Laboratorium Khanza (`jns_perawatan_lab`):</label>
                            <select name="kd_jenis_prw" id="modal_select_tindakan" class="form-control form-control-sm" style="width: 100%;">
                                <option value="">-- Ketik untuk Cari Tindakan Lab --</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Item Template Pemeriksaan Khanza (Opsional jika ada sub-item):</label>
                            <select name="id_template_khanza" id="modal_select_template" class="form-control form-control-sm" style="width: 100%;">
                                <option value="">-- Pilih Template Khanza (jika ada) --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Mapping</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="/plugins/select2/js/select2.full.min.js"></script>
<script>
$(document).ready(function() {
    function initSelect2Tindakan(element) {
        $(element).select2({
            theme: 'bootstrap4',
            placeholder: '-- Cari Tindakan Lab Khanza --',
            allowClear: true,
            ajax: {
                url: '{{ route("lab.mapping-lis.search-tindakan") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.kd_jenis_prw,
                                text: item.kd_jenis_prw + ' - ' + item.nm_perawatan + ' (Rp ' + Number(item.total_byr).toLocaleString('id-ID') + ')'
                            };
                        })
                    };
                },
                cache: true
            }
        });
    }

    function initSelect2Template(element) {
        const rowId = $(element).data('id');
        $(element).select2({
            theme: 'bootstrap4',
            placeholder: '-- Cari Item Template Lab --',
            allowClear: true,
            ajax: {
                url: '{{ route("lab.mapping-lis.search-template") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    const row = $('#row-' + rowId);
                    const kdTindakan = row.length ? row.find('.select-tindakan-ajax').val() : $('#modal_select_tindakan').val();
                    return {
                        q: params.term,
                        kd_jenis_prw: kdTindakan
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id_template,
                                text: '#' + item.id_template + ' - ' + item.Pemeriksaan + ' (' + (item.satuan || '-') + ') [' + item.kd_jenis_prw + ' - ' + item.nm_perawatan + ']',
                                kd_jenis_prw: item.kd_jenis_prw,
                                nm_perawatan: item.nm_perawatan
                            };
                        })
                    };
                },
                cache: true
            }
        });
    }

    // Inisialisasi baris tabel
    $('.select-tindakan-ajax').each(function() {
        initSelect2Tindakan(this);
    });

    $('.select-template-ajax').each(function() {
        initSelect2Template(this);
    });

    // Ketika tindakan dipilih, reset template
    $(document).on('change', '.select-tindakan-ajax', function() {
        const rowId = $(this).data('id');
        const selectTemplate = $('#row-' + rowId).find('.select-template-ajax');
        selectTemplate.val(null).trigger('change');
    });

    // Ketika template dipilih lebih dulu, otomatis pasangkan tindakannya!
    $(document).on('select2:select', '.select-template-ajax', function(e) {
        const data = e.params.data;
        const rowId = $(this).data('id');
        if (data && data.kd_jenis_prw) {
            const selectTindakan = $('#row-' + rowId).find('.select-tindakan-ajax');
            // Jika tindakan belum dipilih, otomatis isi dengan tindakan milik template ini
            if (!selectTindakan.val()) {
                const newOption = new Option(data.kd_jenis_prw + ' - ' + data.nm_perawatan, data.kd_jenis_prw, true, true);
                selectTindakan.append(newOption).trigger('change');
            }
        }
    });

    // Untuk modal tambah
    initSelect2Tindakan('#modal_select_tindakan');
    initSelect2Template('#modal_select_template');

    $('#modal_select_tindakan').on('change', function() {
        $('#modal_select_template').val(null).trigger('change');
    });

    $('#modal_select_template').on('select2:select', function(e) {
        const data = e.params.data;
        if (data && data.kd_jenis_prw) {
            const selectTindakan = $('#modal_select_tindakan');
            if (!selectTindakan.val()) {
                const newOption = new Option(data.kd_jenis_prw + ' - ' + data.nm_perawatan, data.kd_jenis_prw, true, true);
                selectTindakan.append(newOption).trigger('change');
            }
        }
    });

    // Tombol Simpan AJAX per baris
    $(document).on('click', '.btn-save', function() {
        const btn = $(this);
        const id = btn.data('id');
        const row = $('#row-' + id);
        const kdTindakan = row.find('.select-tindakan-ajax').val();
        const idTemplate = row.find('.select-template-ajax').val();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '{{ route("lab.mapping-lis.update") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                kd_jenis_prw: kdTindakan,
                id_template_khanza: idTemplate
            },
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                btn.removeClass('btn-primary').addClass('btn-success');
                setTimeout(function() {
                    btn.html('<i class="fas fa-save"></i>');
                    btn.removeClass('btn-success').addClass('btn-primary');
                }, 1500);
            },
            error: function(err) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i>');
                alert('Gagal menyimpan perubahan');
            }
        });
    });

    // Form Tambah Modal AJAX
    $('#formTambahMapping').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message || 'Gagal menyimpan');
                }
            },
            error: function(err) {
                alert('Terjadi kesalahan koneksi.');
            }
        });
    });
});
</script>
@endpush
