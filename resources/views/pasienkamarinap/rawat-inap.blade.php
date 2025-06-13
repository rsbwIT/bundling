@extends('..layout.layoutDashboard')
@section('title', 'Pasien Rawat Inap')

@push('styles')
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .pretty-check {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding-top: 0.3rem;
            font-size: 0.9rem;
        }

        .pretty-check .form-check-input {
            margin-top: 0.1rem;
            width: 1.1rem;
            height: 1.1rem;
            cursor: pointer;
        }

        .pretty-check .form-check-label {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .tanggal-group {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 0.375rem;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .tanggal-group:focus-within {
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
        }

        .tanggal-input {
            font-size: 0.85rem;
            padding: 0.4rem 0.6rem;
            border-color: #ced4da;
        }

        .btn-cari {
            background-color: #0d6efd;
            color: white;
            font-weight: 500;
            padding: 0.45rem 1.25rem;
            font-size: 0.9rem;
            border-radius: 0.375rem;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-cari:hover {
            background-color: #0b5ed7;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.5);
        }

        .btn-rgb {
            background-color: rgb(70, 130, 180);
            color: white;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-rgb:hover {
            background-color: rgb(100, 160, 210);
        }

        .btn-rgb:active {
            background-color: rgb(50, 110, 160);
        }

        /* garis tabel */
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
            /* Garis border warna abu-abu terang */
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
            /* background alternatif baris ganjil agar mudah dibaca */
        }

        /* Jika ingin garis tegas tapi tetap rapih, bisa juga pakai box-shadow */
        .table-bordered {
            border-collapse: separate !important;
            border-spacing: 0;
        }

        .table-bordered th,
        .table-bordered td {
            border-right: 1px solid #dee2e6 !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        .table-bordered th:last-child,
        .table-bordered td:last-child {
            border-right: 0 !important;
        }

        .table-bordered tr:last-child td {
            border-bottom: 0 !important;
        }
    </style>
@endpush

@section('konten')
    <div class="card">
        <div class="card-body">
            <form action="{{ url('/rawat-inap') }}" method="GET">
                @csrf
                <div class="row g-1 align-items-center">
                    {{-- Checkbox --}}
                    <div class="col-md-2">
                        <div class="form-check pretty-check">
                            <input class="form-check-input ceklis" type="checkbox" name="belum_pulang" id="belum_pulang"
                                {{ request('belum_pulang') ? 'checked' : '' }}>
                            <label class="form-check-label" for="belum_pulang">
                                <i class="bi bi-box-arrow-in-right me-1 text-primary"></i> Belum Pulang
                            </label>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-check pretty-check">
                            <input class="form-check-input ceklis" type="checkbox" name="tgl_masuk" id="tgl_masuk"
                                {{ request('tgl_masuk') ? 'checked' : '' }}>
                            <label class="form-check-label" for="tgl_masuk">
                                <i class="bi bi-calendar-check me-1 text-success"></i> Tanggal Masuk
                            </label>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-check pretty-check">
                            <input class="form-check-input ceklis" type="checkbox" name="tgl_pulang" id="tgl_pulang"
                                value="1" {{ request('tgl_pulang') ? 'checked' : '' }}>
                            <label class="form-check-label" for="tgl_pulang">
                                <i class="bi bi-calendar-x me-1 text-danger"></i> Tanggal Pulang
                            </label>
                        </div>
                    </div>

                    {{-- Input Tanggal --}}
                    <div class="col-md-2">
                        <div class="input-group input-group-sm tanggal-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar-date"></i>
                            </span>
                            <input type="date" name="tgl1" id="tgl1"
                                class="form-control border-start-0 tanggal-input"
                                value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="input-group input-group-sm tanggal-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar-date"></i>
                            </span>
                            <input type="date" name="tgl2" id="tgl2"
                                class="form-control border-start-0 tanggal-input"
                                value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                        </div>
                    </div>

                    {{-- Tombol Cari --}}
                    <div class="col-md-2 d-flex align-items-start">
                        <button type="submit" class="btn btn-cari w-100">
                            <i class="fa fa-search me-2"></i> Cari
                        </button>
                    </div>

                    {{-- Filter Warna --}}
                    <div class="col-md-3 d-flex align-items-start"
                        style="padding-top: 0.4rem; gap: 0.5rem; flex-wrap: wrap;">
                        <!-- Tidak Sesuai Kelas (Merah) -->
                        <button type="submit" name="filter_warna" value="merah"
                            class="btn btn-danger btn-sm d-flex align-items-center" title="Tidak Sesuai Kelas">
                            <i class="bi bi-x-circle-fill me-1"></i> Tidak Sesuai Kelas
                        </button>

                        <!-- Tidak Ada Keterangan (Kuning) -->
                        <button type="submit" name="filter_warna" value="kuning"
                            class="btn btn-warning btn-sm d-flex align-items-center"
                            title="Tidak Ada Keterangan Kelas Naik">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Tidak Ada Keterangan Kelas Naik
                        </button>

                        <!-- Pasien Sesuai SEP (Hijau) -->
                        <button type="submit" name="filter_warna" value="hijau"
                            class="btn btn-success btn-sm d-flex align-items-center" title="Pasien Sesuai SEP">
                            <i class="bi bi-check-circle-fill me-1"></i> Sesuai SEP
                        </button>

                        <!-- Tidak Ada SEP (Putih) -->
                        <button type="submit" name="filter_warna" value="putih"
                            class="btn btn-light btn-sm border d-flex align-items-center" title="Tidak Ada SEP">
                            <i class="bi bi-dash-circle-fill me-1"></i> Tidak Ada SEP
                        </button>

                        <!-- Tampilkan Semua -->
                        <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm d-flex align-items-center"
                            title="Tampilkan Semua">
                            <i class="bi bi-list-stars me-1"></i> Tampilkan Semua
                        </a>
            </form>

            <!-- Filter Kelas (JANGAN DIUBAH - SUDAH SESUAI) -->
            <form method="GET" class="d-flex flex-wrap gap-2">

                <!-- Kelas 1 -->
                <button type="submit" name="kelas_filter" value="Kelas 1"
                    class="btn btn-outline-primary btn-sm d-flex align-items-center" title="Filter Kelas 1">
                    <i class="bi bi-filter-circle me-1"></i> Kelas 1
                </button>

                <!-- Kelas 2 -->
                <button type="submit" name="kelas_filter" value="Kelas 2"
                    class="btn btn-outline-primary btn-sm d-flex align-items-center" title="Filter Kelas 2">
                    <i class="bi bi-filter-circle me-1"></i> Kelas 2
                </button>

                <!-- Kelas 3 -->
                <button type="submit" name="kelas_filter" value="Kelas 3"
                    class="btn btn-outline-primary btn-sm d-flex align-items-center" title="Filter Kelas 3">
                    <i class="bi bi-filter-circle me-1"></i> Kelas 3
                </button>

        </div>
    </div>
    </form>

    {{-- Total Data --}}
    <div class="d-flex justify-content-end mt-3 mb-2">
        <strong>Total Data: {{ $results->count() }}</strong>
    </div>

    {{-- Copy Table --}}
    <div class="d-flex justify-content-end mt-3 mb-2">
        <button type="button" class="btn btn-secondary btn-sm" id="copyButton">
            <i class="bi bi-clipboard-check me-1"></i> Copy Table
        </button>
    </div>


    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped text-xs" id="tableToCopy"
            style="white-space: nowrap; border-collapse: collapse;">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>No Rawat</th>
                    <th>Rekam Medis</th>
                    <th>Nama Pasien</th>
                    <th>Jenis Bayar</th>
                    <th>Kelas SEP</th>
                    <th>Naik Kelas</th>
                    <th>Kamar</th>
                    <th>Tarif Kamar</th>
                    <th>Diagnosa Awal</th>
                    <th>Diagnosa Akhir</th>
                    <th>Tgl. Masuk</th>
                    <th>Jam Masuk</th>
                    <th>Tgl. Keluar</th>
                    <th>Jam Keluar</th>
                    <th>Ttl. Biaya</th>
                    <th>Stts. Pulang</th>
                    <th>Lama Perawatan</th>
                    <th>Dokter DPJP</th>
                    <th>Status Bayar</th>
                    <th>Penanggung Jawab</th>
                    <th>Hubungan P.J</th>
                    <th>Agama</th>
                    <th>Alamat Pasien</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $filterWarna = request('filter_warna');
                @endphp

                @foreach ($results as $key => $item)
                    @php
                        $isNonKelas = empty($item->klsrawat);
                        $isSepKosong = empty($item->kelas_sep); // jika kamu punya field ini
                        $warnaKelas = $item->warna_kelas ?? '';
                        $keteranganKlsNaik = $item->keterangan_klsnaik ?? 'Tidak Ada';

                        // Warna latar belakang berdasarkan logika warna_kelas
                        if ($isNonKelas && $isSepKosong) {
                            $bgColor = 'white';
                        } elseif ($warnaKelas === 'hijau') {
                            $bgColor = '#d4edda'; // Hijau muda: sesuai hak kelas
                        } elseif ($keteranganKlsNaik === 'Tidak Ada') {
                            $bgColor = '#ffab19'; // Kuning: tidak ada keterangan naik kelas
                        } else {
                            $bgColor = '#ff7d8e'; // Merah: tidak sesuai hak kelas
                        }
                    @endphp

                    <tr style="background-color: {{ $bgColor }};">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->no_rkm_medis }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->png_jawab }}</td>
                        <td>{{ $item->klsrawat ? 'Kelas ' . $item->klsrawat : 'Tidak Ada SEP' }}</td>
                        <td>{{ $item->keterangan_klsnaik }}</td>
                        <td>{{ $item->kamar_bangsal }}</td>
                        <td>{{ number_format($item->trf_kamar) }}</td>
                        <td>{{ $item->diagnosa_awal }}</td>
                        <td>{{ $item->diagnosa_akhir }}</td>
                        <td>{{ $item->tgl_masuk }}</td>
                        <td>{{ $item->jam_masuk }}</td>
                        <td>{{ $item->tgl_keluar }}</td>
                        <td>{{ $item->jam_keluar }}</td>
                        <td>{{ number_format($item->ttl_biaya) }}</td>
                        <td>{{ $item->stts_pulang }}</td>
                        <td>{{ $item->lama }}</td>
                        <td>{{ $item->nm_dokter }}</td>
                        <td>{{ $item->status_bayar }}</td>
                        <td>{{ $item->p_jawab }}</td>
                        <td>{{ $item->hubunganpj }}</td>
                        <td>{{ $item->agama }}</td>
                        <td>{{ $item->alamat }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    </div>
    </div>
@endsection

@push('scripts')
    @livewireScripts
    <script>
        document.getElementById("copyButton").addEventListener("click", () => {
            const table = document.getElementById("tableToCopy");
            const range = document.createRange();
            range.selectNode(table);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);

            try {
                document.execCommand("copy");
                selection.removeAllRanges();
                alert("Tabel telah berhasil disalin ke clipboard.");
            } catch (err) {
                console.error("Tidak dapat menyalin tabel:", err);
            }
        });

        // Checkbox Behavior
        const ceklis = document.querySelectorAll('.ceklis');
        const belumPulang = document.getElementById('belum_pulang');
        const tanggalInputs = [document.getElementById('tgl1'), document.getElementById('tgl2')];

        function toggleInputs() {
            const disabled = belumPulang.checked;
            ceklis.forEach(cb => {
                if (cb.id !== 'belum_pulang') {
                    cb.disabled = disabled; // Hanya disable, jangan reset checked
                }
            });
            tanggalInputs.forEach(input => input.disabled = disabled);
        }

        ceklis.forEach(cb => {
            cb.addEventListener('change', () => {
                if (cb.id !== 'belum_pulang' && cb.checked) {
                    belumPulang.checked = false;
                    toggleInputs();
                    ceklis.forEach(other => {
                        if (other !== cb && other.id !== 'belum_pulang') other.checked = false;
                    });
                }
            });
        });

        belumPulang.addEventListener('change', toggleInputs);
        toggleInputs();
    </script>
@endpush
