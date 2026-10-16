<form action="{{ url($action) }}" id="formFilterLab">
    @csrf
    {{-- Baris 1: Pencarian Cepat --}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-2">
                <div class="input-group input-group-xs">
                    <input type="text" name="cariNomor" class="form-control form-control-xs"
                        placeholder="Cari Nama/RM/No Rawat" value="{{ request('cariNomor') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Baris 2: Input Filter Utama --}}
    <div class="row">
        <div class="col-md-2">
            <div class="form-group mb-2">
                <input type="date" name="tgl1" class="form-control form-control-xs"
                    value="{{ request('tgl1', now()->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group mb-2">
                <input type="date" name="tgl2" class="form-control form-control-xs"
                    value="{{ request('tgl2', now()->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group mb-2">
                <select class="form-control form-control-xs" name="jenis_tanggal">
                    <option value="default" {{ request('jenis_tanggal', 'default') == 'default' ? 'selected' : '' }}>Filter Tanggal Default</option>
                    <option value="cetak_nota" {{ request('jenis_tanggal') == 'cetak_nota' ? 'selected' : '' }}>Berdasarkan Tgl Cetak Nota</option>
                    <option value="bayar_piutang" {{ request('jenis_tanggal') == 'bayar_piutang' ? 'selected' : '' }}>Berdasarkan Tgl Bayar Piutang</option>
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group mb-2">
                <select class="form-control form-control-xs" name="status_lanjut">
                    <option value="semua" {{ request('status_lanjut', 'semua') == 'semua' ? 'selected' : '' }}>Semua (Ralan & Ranap)</option>
                    <option value="Ralan" {{ request('status_lanjut') == 'Ralan' ? 'selected' : '' }}>Ralan (Rawat Jalan)</option>
                    <option value="Ranap" {{ request('status_lanjut') == 'Ranap' ? 'selected' : '' }}>Ranap (Rawat Inap)</option>
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group mb-2">
                <select class="form-control form-control-xs" name="jenis_pasien">
                    <option value="semua" {{ request('jenis_pasien') == 'semua' ? 'selected' : '' }}>Semua Jenis Bayar</option>
                    <option value="umum" {{ request('jenis_pasien') == 'umum' ? 'selected' : '' }}>Umum (Tunai)</option>
                    <option value="asuransi" {{ request('jenis_pasien') == 'asuransi' ? 'selected' : '' }}>Asuransi / BPJS / Piutang</option>
                </select>
            </div>
        </div>

        <div class="col-md-2" id="wrapperStatusLunas" style="{{ (request('jenis_tanggal') == 'cetak_nota' || request('jenis_pasien') == 'umum') ? 'display: none;' : '' }}">
            <div class="form-group mb-2">
                <select class="form-control form-control-xs" name="statusLunas" id="selectStatusLunas">
                    <option value="Lunas" {{ request('statusLunas') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Belum Lunas" {{ request('statusLunas') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Baris 3: Kategori, Penjamin, dan Tombol Cari --}}
    <div class="row">
        <div class="col-md-2">
            <div class="form-group mb-2">
                <select class="form-control form-control-xs" name="kategori">
                    <option value="semua" {{ request('kategori', 'semua') == 'semua' ? 'selected' : '' }}>Semua Kategori (PK & PA)</option>
                    <option value="PK" {{ request('kategori') == 'PK' ? 'selected' : '' }}>Laboratorium PK</option>
                    <option value="PA" {{ request('kategori') == 'PA' ? 'selected' : '' }}>Laboratorium PA</option>
                </select>
            </div>
        </div>

        <div class="col-md-2" id="wrapperPilihPenjamin" style="{{ request('jenis_pasien') == 'umum' ? 'display: none;' : '' }}">
            <div class="form-group mb-2">
                <button type="button"
                    class="btn btn-default form-control form-control-xs d-flex justify-content-between align-items-center"
                    data-toggle="modal" data-target="#modal-lg">
                    <span>{{ request('kdPenjamin') ? count(explode(',', request('kdPenjamin'))) . ' Penjamin' : 'Pilih Penjamin' }}</span>
                    <i class="nav-icon fas fa-credit-card"></i>
                </button>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group mb-2">
                <button type="submit" class="btn btn-xs btn-primary form-control form-control-xs">
                    <i class="fa fa-search"></i> Cari
                </button>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            function updateFilters() {
                var jenisTgl = $('select[name="jenis_tanggal"]').val();
                var jenisPasien = $('select[name="jenis_pasien"]').val();

                if (jenisPasien === 'umum') {
                    $('select[name="jenis_tanggal"] option[value="bayar_piutang"]').hide();
                    if (jenisTgl === 'bayar_piutang') {
                        $('select[name="jenis_tanggal"]').val('default');
                        jenisTgl = 'default';
                    }
                    $('#wrapperPilihPenjamin').hide();
                } else {
                    $('select[name="jenis_tanggal"] option[value="bayar_piutang"]').show();
                    $('#wrapperPilihPenjamin').show();
                }

                if (jenisPasien === 'umum' || jenisTgl === 'cetak_nota') {
                    $('#wrapperStatusLunas').hide();
                    $('#selectStatusLunas').prop('disabled', true);
                } else {
                    $('#wrapperStatusLunas').show();
                    $('#selectStatusLunas').prop('disabled', false);
                }
            }

            $('select[name="jenis_tanggal"], select[name="jenis_pasien"]').on('change', updateFilters);
            updateFilters();
        });
    </script>

    <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pilih Penjamin / Jenis Bayar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        $selectedPenjamins = request('kdPenjamin') ? explode(',', request('kdPenjamin')) : [];
                    @endphp
                    <select multiple="multiple" size="10" name="duallistbox[]">
                        @foreach ($penjab as $item)
                            <option value="{{ $item->kd_pj }}" {{ in_array($item->kd_pj, $selectedPenjamins) ? 'selected' : '' }}>
                                {{ $item->png_jawab }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="kdPenjamin" value="{{ request('kdPenjamin') }}">
                    <script>
                        $(function () {
                            var demo1 = $('select[name="duallistbox[]"]').bootstrapDualListbox({
                                nonSelectedListLabel: 'Non-selected',
                                selectedListLabel: 'Selected',
                                preserveSelectionOnMove: 'moved',
                                moveOnSelect: false
                            });
                            $('#formFilterLab').on('submit', function(e) {
                                var selectedValues = $('select[name="duallistbox[]"]').val();
                                $('input[name="kdPenjamin"]').val(selectedValues ? selectedValues.join(',') : '');
                                // Disable duallistbox select element so it doesn't pollute GET query string with hundreds of parameters
                                $('select[name="duallistbox[]"]').prop('disabled', true);
                            });
                        });
                    </script>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</form>
