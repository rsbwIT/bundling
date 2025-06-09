
<form method="GET" action="{{ url('/operasi-and-vk-kso') }}" id="ksoSearchForm">
    @csrf
    <div class="row">
        {{-- Tanggal --}}
        <div class="col-md-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="date" name="tgl1" class="form-control form-control-xs" value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="date" name="tgl2" class="form-control form-control-xs" value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                </div>
            </div>
        </div>

        {{-- Status Lunas --}}
        <div class="col-md-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <select class="form-control" name="statusLunas">
                        <option value="Lunas" {{ request('statusLunas') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="Belum Lunas" {{ request('statusLunas') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Tombol Modal Penjamin --}}
        <div class="col-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <button type="button" class="btn btn-default form-control form-control-xs d-flex justify-content-between" data-toggle="modal" data-target="#modal-penjamin">
                        <p>Pilih Penjamin</p>
                        <p><i class="nav-icon fas fa-credit-card"></i></p>
                    </button>
                </div>
            </div>
        </div>

        {{-- Tombol Modal Petugas --}}
        <div class="col-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <button type="button" class="btn btn-default form-control form-control-xs d-flex justify-content-between" data-toggle="modal" data-target="#modal-petugas">
                        <p>Pilih Petugas</p>
                        <p><i class="nav-icon fas fa-user-nurse"></i></p>
                    </button>
                </div>
            </div>
        </div>

        {{-- Tombol Modal Dokter --}}
        <div class="col-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <button type="button" class="btn btn-default form-control form-control-xs d-flex justify-content-between" data-toggle="modal" data-target="#modal-dokter">
                        <p>Pilih Dokter</p>
                        <p><i class="nav-icon fas fa-hospital-user"></i></p>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris Pencarian Teks dan Tombol Submit --}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="text" name="cariNomor" class="form-control form-control-xs" placeholder="Cari Nama/RM/No Rawat" value="{{ request('cariNomor') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Input tersembunyi untuk menampung nilai dari duallistbox --}}
    <input type="hidden" name="kdPenjamin">
    <input type="hidden" name="kdPetugas">
    <input type="hidden" name="kdDokter">

    <!-- Modal Penjamin -->
    <div class="modal fade" id="modal-penjamin">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pilih Penjamin / Jenis Bayar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <select multiple="multiple" size="10" name="duallistbox_penjamin">
                        @foreach ($penjab as $item)
                            <option value="{{ $item->kd_pj }}">{{ $item->png_jawab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Petugas -->
    <div class="modal fade" id="modal-petugas">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pilih Petugas</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <select multiple="multiple" size="10" name="duallistbox_petugas">
                        @foreach ($petugas as $item)
                            <option value="{{ $item->nip }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dokter -->
    <div class="modal fade" id="modal-dokter">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pilih Dokter</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <select multiple="multiple" size="10" name="duallistbox_dokter">
                        @foreach ($dokter as $item)
                            <option value="{{ $item->kd_dokter }}">{{ $item->nm_dokter }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</form>
<hr>

@push('scripts')
<script>
    $(function() {
        // Inisialisasi semua duallistbox
        $('select[name="duallistbox_penjamin"]').bootstrapDualListbox({
            nonSelectedListLabel: 'Pilihan Tersedia',
            selectedListLabel: 'Pilihan Anda',
            preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
        });
        $('select[name="duallistbox_petugas"]').bootstrapDualListbox({
            nonSelectedListLabel: 'Pilihan Tersedia',
            selectedListLabel: 'Pilihan Anda',
            preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
        });
        $('select[name="duallistbox_dokter"]').bootstrapDualListbox({
            nonSelectedListLabel: 'Pilihan Tersedia',
            selectedListLabel: 'Pilihan Anda',
            preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
        });

        // Menyatukan event submit form
        $('#ksoSearchForm').submit(function(e) {
            e.preventDefault(); // Mencegah form submit secara default

            // Mengambil nilai dari setiap duallistbox dan memasukkannya ke input tersembunyi
            $('input[name="kdPenjamin"]').val($('select[name="duallistbox_penjamin"]').val().join(','));
            $('input[name="kdPetugas"]').val($('select[name="duallistbox_petugas"]').val().join(','));
            $('input[name="kdDokter"]').val($('select[name="duallistbox_dokter"]').val().join(','));

            // Melanjutkan proses submit form
            this.submit();
        });
    });
</script>
@endpush
