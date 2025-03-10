<div>
    <div class="card mb-4 box-shadow">
        <div class="card-header text-center">
            {{ $kd_display }}
            <h2 class="my-0 font-weight-bold">Perawat / Petugas Poli {{ $kd_ruang_farmasi }}</h2>
            {{-- {{ count($getPasien) }} --}}
        </div>
        <table class="table table-sm table-bordered table-hover text-sm mb-3" style="white-space: nowrap;"
            wire:poll.10000ms>

        <table class="table table-sm table-bordered table-hover text-sm mb-3" style="white-space: nowrap;">
            <thead>
                <tr>
                    <th scope="col">No. Antrian</th>
                    <th scope="col">Rekam Medik</th>
                    <th scope="col">Nama Pasien</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Display Farmasi</th>
                    <th scope="col">Kode Display</th>
                </tr>
            </thead>
            {{-- <tbody>
                @foreach ($antrian as $item)
                    <tr>
                        <td>{{ $item->nomor_antrian }}</td>
                        <td>{{ $item->rekam_medik }}</td>
                        <td>{{ $item->nama_pasien }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td>{{ $item->nama_display_farmasi }}</td>
                        <td>{{ $item->kd_display_farmasi }}</td>
                    </tr>
                @endforeach
            </tbody> --}}
        </table>
    </div>
</div>
@endsection
