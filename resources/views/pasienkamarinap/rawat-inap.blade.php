@extends('..layout.layoutDashboard')
@section('title', 'Pasien Rawat Inap')

@push('styles')
    @livewireStyles
@endpush

@section('konten')
    <div class="card">
        <div class="card-body">
            <form action="{{ url('/rawat-inap') }}" method="GET">
                @csrf
                <div class="row g-1 align-items-center"> {{-- g-1 lebih rapat dari g-3 --}}
                    {{-- Checkbox Belum Pulang --}}
                    <div class="col-md-2">
                        <div class="form-check" style="padding-top: 0.5rem;"> {{-- Kurangi padding-top --}}
                            <input class="form-check-input ceklis" type="checkbox" name="belum_pulang" id="belum_pulang"
                                {{ request('belum_pulang') ? 'checked' : '' }}>
                            <label class="form-check-label" for="belum_pulang">Belum Pulang</label>
                        </div>
                    </div>

                    {{-- Checkbox Tanggal Masuk --}}
                    <div class="col-md-2">
                        <div class="form-check" style="padding-top: 0.5rem;">
                            <input class="form-check-input ceklis" type="checkbox" name="tgl_masuk" id="tgl_masuk"
                                {{ request('tgl_masuk') ? 'checked' : '' }}>
                            <label class="form-check-label" for="tgl_masuk">Tanggal Masuk</label>
                        </div>
                    </div>

                    {{-- Checkbox Tanggal Pulang --}}
                    <div class="col-md-2">
                        <div class="form-check" style="padding-top: 0.5rem;">
                            <input class="form-check-input ceklis" type="checkbox" name="tgl_pulang" id="tgl_pulang"
                                {{ request('tgl_pulang') ? 'checked' : '' }}>
                            <label class="form-check-label" for="tgl_pulang">Tanggal Pulang</label>
                        </div>
                    </div>

                    {{-- Input Tanggal 1 --}}
                    <div class="col-md-2">
                        <input type="date" name="tgl1" id="tgl1" class="form-control form-control-sm"
                            value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                    </div>

                    {{-- Input Tanggal 2 --}}
                    <div class="col-md-2">
                        <input type="date" name="tgl2" id="tgl2" class="form-control form-control-sm"
                            value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                    </div>

                    {{-- Tombol Cari --}}
                    <div class="col-md-2 d-flex align-items-start">
                        <button type="submit" class="btn btn-primary btn-md">
                            <i class="fa fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tombol Copy Table --}}
            <div class="row no-print mt-3">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-secondary" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive mt-3">
                <table class="table table-sm table-bordered table-striped text-xs" id="tableToCopy"
                    style="white-space: nowrap;">
                    <thead class="table-light">
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">No</th>
                            <th style="text-align: center; vertical-align: middle;">No Rawat</th>
                            <th style="text-align: center; vertical-align: middle;">Rekam Medis</th>
                            <th style="text-align: center; vertical-align: middle;">Nama Pasien</th>
                            <th style="text-align: center; vertical-align: middle;">Alamat Pasien</th>
                            <th style="text-align: center; vertical-align: middle;">Penanggung Jawab</th>
                            <th style="text-align: center; vertical-align: middle;">Hubungan P.J</th>
                            <th style="text-align: center; vertical-align: middle;">Jenis Bayar</th>
                            <th style="text-align: center; vertical-align: middle;">Kamar</th>
                            <th style="text-align: center; vertical-align: middle;">Tarif Kamar</th>
                            <th style="text-align: center; vertical-align: middle;">Diagnosa Awal</th>
                            <th style="text-align: center; vertical-align: middle;">Diagnosa Akhir</th>
                            <th style="text-align: center; vertical-align: middle;">Tgl. Masuk</th>
                            <th style="text-align: center; vertical-align: middle;">Jam Masuk</th>
                            <th style="text-align: center; vertical-align: middle;">Tgl. Keluar</th>
                            <th style="text-align: center; vertical-align: middle;">Jam Keluar</th>
                            <th style="text-align: center; vertical-align: middle;">Ttl. Biaya</th>
                            <th style="text-align: center; vertical-align: middle;">Stts. Pulang</th>
                            <th style="text-align: center; vertical-align: middle;">Lama Perawatan</th>
                            <th style="text-align: center; vertical-align: middle;">Dokter DPJP</th>
                            <th style="text-align: center; vertical-align: middle;">Status Bayar</th>
                            <th style="text-align: center; vertical-align: middle;">Agama</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $key => $item)
                            <tr
                                @if ($item->klsrawat) style="background-color: {{ $item->warna_kelas == 'hijau' ? '#d4edda' : '#fff3cd' }};" @endif>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->p_jawab }}</td>
                                <td>{{ $item->hubunganpj }}</td>
                                <td>{{ $item->png_jawab }}</td>
                                <td>{{ $item->kamar_bangsal }}</td>
                                <td>{{ $item->trf_kamar }}</td>
                                <td>{{ $item->diagnosa_awal }}</td>
                                <td>{{ $item->diagnosa_akhir }}</td>
                                <td>{{ $item->tgl_masuk }}</td>
                                <td>{{ $item->jam_masuk }}</td>
                                <td>{{ $item->tgl_keluar }}</td>
                                <td>{{ $item->jam_keluar }}</td>
                                <td>{{ $item->ttl_biaya }}</td>
                                <td>{{ $item->stts_pulang }}</td>
                                <td>{{ $item->lama }}</td>
                                <td>{{ $item->nm_dokter }}</td>
                                <td>{{ $item->status_bayar }}</td>
                                <td>{{ $item->agama }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Script Copy Table --}}
    <script>
        document.getElementById("copyButton").addEventListener("click", () => {
            const table = document.getElementById("tableToCopy");
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand("copy");
                window.getSelection().removeAllRanges();
                alert("Tabel telah berhasil disalin ke clipboard.");
            } catch (err) {
                console.error("Tidak dapat menyalin tabel:", err);
            }
        });
    </script>

    {{-- Script Checkbox Behavior --}}
    <script>
        const ceklis = document.querySelectorAll('.ceklis');
        const belumPulang = document.getElementById('belum_pulang');
        const otherCheckboxes = Array.from(ceklis).filter(cb => cb.id !== 'belum_pulang');
        const tanggalInputs = [
            document.getElementById('tgl1'),
            document.getElementById('tgl2')
        ];

        // Fungsi untuk disable/enable checkbox lain dan input tanggal saat belum_pulang dicentang
        function toggleInputs() {
            if (belumPulang.checked) {
                otherCheckboxes.forEach(cb => {
                    cb.checked = false;
                    cb.disabled = true;
                });
                tanggalInputs.forEach(input => input.disabled = true);
            } else {
                otherCheckboxes.forEach(cb => cb.disabled = false);
                tanggalInputs.forEach(input => input.disabled = false);
            }
        }

        // Saat checkbox lain dicentang, hapus centang pada checkbox lain (kecuali belum_pulang)
        ceklis.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked && this.id !== 'belum_pulang') {
                    belumPulang.checked = false;
                    toggleInputs();
                    ceklis.forEach(other => {
                        if (other !== this) other.checked = false;
                    });
                }
            });
        });

        // Listener khusus untuk belum_pulang
        belumPulang.addEventListener('change', () => {
            toggleInputs();
        });

        // Inisialisasi saat load halaman
        toggleInputs();
    </script>
@endsection

@push('scripts')
    @livewireScripts
@endpush
