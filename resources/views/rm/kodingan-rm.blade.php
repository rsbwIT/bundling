@extends('..layout.layoutDashboard')
@section('title', 'Codingan ICD 10 & ICD 9')

@section('konten')
<section class="content pt-3">
    <div class="container-fluid">
        <!-- Filter Form -->
        <div class="bg-light p-3 rounded mb-3 border">
            <form method="GET" action="{{ url('kodingan-rm') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small font-weight-bold">Dari Tanggal</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="{{ $tanggalMulai }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small font-weight-bold">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm" value="{{ $tanggalSelesai }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small font-weight-bold">Jenis Pasien</label>
                        <select name="filter_status" class="form-control form-control-sm">
                            <option value="semua" {{ $filterStatus == 'semua' ? 'selected' : '' }}>Semua Pasien</option>
                            <option value="ranap" {{ $filterStatus == 'ranap' ? 'selected' : '' }}>Rawat Inap</option>
                            <option value="ralan" {{ $filterStatus == 'ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small font-weight-bold">Tampilkan</label>
                        <select name="per_page" class="form-control form-control-sm">
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 Baris</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 Baris</option>
                            <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500 Baris</option>
                            <option value="10000" {{ $perPage == 10000 ? 'selected' : '' }}>Semua Data</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small font-weight-bold">Cari Data</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="No Rawat / No RM / Nama" value="{{ $searchTerm }}">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block mt-2">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm text-sm">
                <thead class="bg-info text-white text-center">
                    <tr>
                        <th width="3%">No</th>
                        <th width="12%">No. Rawat / RM</th>
                        <th width="15%">Nama Pasien</th>
                        <th width="8%">Tgl / Poli</th>
                        <th width="8%">Bayar</th>
                        <th width="6%">Status</th>
                        <th width="10%">ICD-10 (Dokter)</th>
                        <th width="10%">ICD-9 (Dokter)</th>
                        <th width="10%">ICD-10 (RM)</th>
                        <th width="10%">ICD-9 (RM)</th>
                        <th width="8%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataPasien as $index => $rp)
                        <tr>
                            <td class="text-center align-middle">{{ $dataPasien->firstItem() + $index }}</td>
                            <td class="align-middle">
                                <strong>{{ $rp->no_rawat }}</strong><br>
                                <span class="text-muted">{{ $rp->no_rkm_medis }}</span>
                            </td>
                            <td class="align-middle font-weight-bold">{{ $rp->nm_pasien }}</td>
                            <td class="align-middle text-center">
                                {{ \Carbon\Carbon::parse($rp->tgl_registrasi)->format('d/m/Y') }}<br>
                                <small class="badge badge-secondary">{{ $rp->nm_poli }}</small>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-success font-weight-bold">{{ $rp->png_jawab }}</span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="badge {{ $rp->status_lanjut == 'Ranap' ? 'badge-primary' : 'badge-success' }}">
                                    {{ $rp->status_lanjut }}
                                </span>
                            </td>
                            <td class="align-middle text-muted">
                                {{ $rp->icd10_dokter ?: '-' }}
                            </td>
                            <td class="align-middle text-muted">
                                {{ $rp->icd9_dokter ?: '-' }}
                            </td>
                            <td class="align-middle text-primary font-weight-bold" id="td_icd10_{{ str_replace('/', '', $rp->no_rawat) }}">
                                {{ $rp->icd10 ?: '-' }}
                            </td>
                            <td class="align-middle text-primary font-weight-bold" id="td_icd9_{{ str_replace('/', '', $rp->no_rawat) }}">
                                {{ $rp->icd9 ?: '-' }}
                            </td>
                            <td class="align-middle text-center">
                                <button class="btn btn-sm btn-info w-100" onclick="bukaModalKodingan('{{ $rp->no_rawat }}', '{{ $rp->icd10 }}', '{{ $rp->icd9 }}', '{{ addslashes($rp->nm_pasien) }}')">
                                    <i class="fas fa-edit"></i> Koding
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i><br>
                                <strong>Belum ada data pasien ditemukan.</strong>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if ($dataPasien->hasPages())
            <div class="d-flex justify-content-between mt-3">
                <div class="text-muted text-sm">
                    Menampilkan {{ $dataPasien->firstItem() ?? 0 }} - {{ $dataPasien->lastItem() ?? 0 }} dari {{ $dataPasien->total() }} data
                </div>
                <div>
                    {{ $dataPasien->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Modal Kodingan -->
<div class="modal fade" id="modalKodingan" tabindex="-1" role="dialog" aria-labelledby="modalKodinganLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalKodinganLabel"><i class="fas fa-edit"></i> Edit Diagnosa & Prosedur</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <input type="hidden" id="modal_no_rawat">
                
                <ul class="nav nav-tabs px-3 pt-3" id="kodinganTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="icd10-tab" data-toggle="tab" href="#icd10" role="tab">ICD-10 (Diagnosa)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="icd9-tab" data-toggle="tab" href="#icd9" role="tab">ICD-9 (Prosedur)</a>
                    </li>
                </ul>
                
                <div class="tab-content p-3" id="kodinganTabContent">
                    <!-- TAB ICD 10 -->
                    <div class="tab-pane fade show active" id="icd10" role="tabpanel">
                        <div class="form-group mb-2">
                            <label class="font-weight-bold">Kode Diagnosa:</label>
                            <textarea id="modal_icd10" class="form-control" rows="2" placeholder="Pilih diagnosa dari tabel di bawah..." readonly></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <input type="text" id="search_icd10" class="form-control" placeholder="Cari penyakit (tekan enter)...">
                        </div>
                        <div class="table-responsive" style="height: 250px; border: 1px solid #dee2e6;">
                            <table class="table table-sm table-bordered table-hover text-sm mb-0">
                                <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th width="5%" class="text-center">Pilih</th>
                                        <th width="15%">Kode</th>
                                        <th>Nama Penyakit</th>
                                    </tr>
                                </thead>
                                <tbody id="table_icd10">
                                    <tr><td colspan="3" class="text-center text-muted py-3">Ketik kata kunci untuk mencari</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- TAB ICD 9 -->
                    <div class="tab-pane fade" id="icd9" role="tabpanel">
                        <div class="form-group mb-2">
                            <label class="font-weight-bold">Kode Prosedur:</label>
                            <textarea id="modal_icd9" class="form-control" rows="2" placeholder="Pilih prosedur dari tabel di bawah..." readonly></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <input type="text" id="search_icd9" class="form-control" placeholder="Cari prosedur (tekan enter)...">
                        </div>
                        <div class="table-responsive" style="height: 250px; border: 1px solid #dee2e6;">
                            <table class="table table-sm table-bordered table-hover text-sm mb-0">
                                <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th width="5%" class="text-center">Pilih</th>
                                        <th width="15%">Kode</th>
                                        <th>Nama Prosedur</th>
                                    </tr>
                                </thead>
                                <tbody id="table_icd9">
                                    <tr><td colspan="3" class="text-center text-muted py-3">Ketik kata kunci untuk mencari</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanKodinganModal()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<script>
    function bukaModalKodingan(no_rawat, icd10, icd9, nama_pasien) {
        $('#modal_no_rawat').val(no_rawat);
        $('#modal_icd10').val(icd10);
        $('#modal_icd9').val(icd9);
        $('#modalKodinganLabel').html('<i class="fas fa-edit"></i> Edit Diagnosa & Prosedur: <b>' + nama_pasien + '</b>');
        
        // Reset search state
        $('#search_icd10').val('');
        $('#search_icd9').val('');
        $('#table_icd10').html('<tr><td colspan="3" class="text-center text-muted py-3">Ketik kata kunci untuk mencari</td></tr>');
        $('#table_icd9').html('<tr><td colspan="3" class="text-center text-muted py-3">Ketik kata kunci untuk mencari</td></tr>');
        
        $('#icd10-tab').tab('show');
        $('#modalKodingan').modal('show');
    }

    // Event listener search ICD-10
    $('#search_icd10').on('keypress', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            cariIcd(10, $(this).val());
        }
    });

    // Event listener search ICD-9
    $('#search_icd9').on('keypress', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            cariIcd(9, $(this).val());
        }
    });

    function cariIcd(type, keyword) {
        if (keyword.length < 2) return;
        
        let url = type === 10 ? "{{ url('kodingan-rm/cari-icd10') }}" : "{{ url('kodingan-rm/cari-icd9') }}";
        let targetTable = type === 10 ? '#table_icd10' : '#table_icd9';
        
        $(targetTable).html('<tr><td colspan="3" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Mencari...</td></tr>');
        
        $.ajax({
            url: url,
            type: "GET",
            data: { q: keyword },
            success: function(data) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(function(item) {
                        let kode = type === 10 ? item.kd_penyakit : item.kode;
                        let nama = type === 10 ? item.nm_penyakit : (item.deskripsi_panjang || item.deskripsi_pendek);
                        
                        // Cek apakah kode sudah ada di textarea untuk pre-check
                        let currentCodes = type === 10 ? $('#modal_icd10').val() : $('#modal_icd9').val();
                        let isChecked = currentCodes.includes(kode) ? 'checked' : '';
                        
                        html += `<tr>
                            <td class="text-center align-middle">
                                <input type="checkbox" class="check-icd${type}" value="${kode}" onchange="updateTextarea(${type})" ${isChecked}>
                            </td>
                            <td class="align-middle">${kode}</td>
                            <td class="align-middle">${nama}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="3" class="text-center text-muted py-3">Data tidak ditemukan</td></tr>';
                }
                $(targetTable).html(html);
            }
        });
    }

    function updateTextarea(type) {
        let codes = [];
        $('.check-icd' + type + ':checked').each(function() {
            codes.push($(this).val());
        });
        
        let textarea = type === 10 ? '#modal_icd10' : '#modal_icd9';
        // Tambahkan kode baru tanpa menghapus yang lama (jika manual input sebelumnya)
        let oldVal = $(textarea).val().split(',').map(item => item.trim()).filter(item => item !== '');
        
        // Gabungkan yang lama dan yang dicek, lalu unique
        let finalCodes = [...new Set([...oldVal, ...codes])];
        
        // Tapi kita juga harus handle uncheck. 
        // Jika uncheck, kita hapus dari textarea
        $('.check-icd' + type + ':not(:checked)').each(function() {
            let uncheckVal = $(this).val();
            finalCodes = finalCodes.filter(item => item !== uncheckVal);
        });
        
        $(textarea).val(finalCodes.join(', '));
    }

    function simpanKodinganModal() {
        let no_rawat = $('#modal_no_rawat').val();
        let icd10 = $('#modal_icd10').val();
        let icd9 = $('#modal_icd9').val();

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        $.ajax({
            url: "{{ url('kodingan-rm/store') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                no_rawat: no_rawat,
                icd10: icd10,
                icd9: icd9
            },
            success: function(response) {
                if(response.success) {
                    $('#modalKodingan').modal('hide');
                    
                    let clean_no_rawat = no_rawat.replace(/\//g, '');
                    $('#td_icd10_' + clean_no_rawat).text(icd10 ? icd10 : '-');
                    $('#td_icd9_' + clean_no_rawat).text(icd9 ? icd9 : '-');
                    
                    let btn = $('#td_icd10_' + clean_no_rawat).siblings().last().find('button');
                    let rawNama = $('#modalKodinganLabel b').text();
                    btn.attr('onclick', `bukaModalKodingan('${no_rawat}', '${icd10}', '${icd9}', '${rawNama.replace(/'/g, "\\'")}')`);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.'
                });
            }
        });
    }
</script>
@endsection
