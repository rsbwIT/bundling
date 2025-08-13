@extends('..layout.layoutDashboard')
@section('title', 'Data Pemeriksaan Laboratorium')
@push('styles')
    @livewireStyles
    <style>
        /* Tombol search sederhana tapi elegan */
        .btn-search {
            background-color: #4e73df; /* biru navy */
            color: #fff;
            border: none;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 4px;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-search:hover {
            background-color: #2e59d9; /* biru lebih gelap saat hover */
        }

        /* Ringkasan data di atas tabel */
        .summary-box {
            background-color: #f1f3f5; /* abu-abu soft */
            border-radius: 6px;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
        }
        .summary-title {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 2px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@endpush

@section('konten')
    <div class="card">
        <div class="card-body">

            {{-- Form filter --}}
            <form action="{{ route('laboratorium.index') }}" method="GET">
                <div class="row g-2 align-items-center">
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control form-control-sm"
                            value="{{ request('start_date', $startDate) }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control form-control-sm"
                            value="{{ request('end_date', $endDate) }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Cari nama pemeriksaan..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-search w-100">
                            <i class="fa fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>

            @php
                $totalBiaya = $data->sum('biaya');
                $totalPasien = collect($data)->pluck('no_rkm_medis')->unique()->count();
            @endphp

            {{-- Ringkasan total di atas tabel --}}
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="summary-box text-center">
                        <div class="summary-title">Total Pasien</div>
                        <div class="summary-value text-primary">{{ $totalPasien }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-box text-center">
                        <div class="summary-title">Total Biaya</div>
                        <div class="summary-value text-success">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            {{-- Tombol copy --}}
            <div class="row no-print mt-2">
                <div class="col-12">
                    <button type="button" class="btn btn-outline-secondary btn-sm float-end" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive mt-2">
                <table class="table table-sm table-bordered table-striped align-middle text-xs" id="tableToCopy">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>No Rawat</th>
                            <th>No RM</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal Periksa</th>
                            <th>Nama Pemeriksaan</th>
                            <th>Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $item)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td>{{ $item->tgl_periksa }}</td>
                                <td>{{ $item->nm_perawatan }}</td>
                                <td class="text-end">{{ number_format($item->biaya, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Script copy --}}
    <script>
        document.getElementById("copyButton").addEventListener("click", function() {
            copyTableToClipboard("tableToCopy");
        });

        function copyTableToClipboard(tableId) {
            const table = document.getElementById(tableId);
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand("copy");
                window.getSelection().removeAllRanges();
                alert("Tabel berhasil disalin ke clipboard.");
            } catch (err) {
                console.error("Tidak dapat menyalin tabel:", err);
            }
        }
    </script>
@endsection

@push('scripts')
    @livewireScripts
@endpush
