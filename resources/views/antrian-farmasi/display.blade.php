{{-- resources/views/antrian-farmasi/display.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Antrian Farmasi</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container mt-5">
        <h2>Daftar Antrian Hari Ini</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($antrians as $antrian)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $antrian->nomor_antrian }}</td>
                    <td>{{ $antrian->nama_pasien }}</td>
                    <td>{{ ucfirst($antrian->status) }}</td>
                    <td>
                        @if ($antrian->status == 'menunggu')
                            <a href="{{ route('antrian.selesai', $antrian->id) }}" class="btn btn-success">Selesai</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
