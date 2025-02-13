{{-- resources/views/antrian-farmasi/panggil.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panggil Antrian</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container mt-5">
        <h2>Panggil Antrian</h2>

        @if ($antrianSekarang)
        <div class="alert alert-info">
            <h4>Antrian yang sedang dipanggil:</h4>
            <p><strong>Nomor Antrian:</strong> {{ $antrianSekarang->nomor_antrian }}</p>
            <p><strong>Nama Pasien:</strong> {{ $antrianSekarang->nama_pasien }}</p>
            <p><strong>Status:</strong> {{ ucfirst($antrianSekarang->status) }}</p>
        </div>
        @else
        <div class="alert alert-warning">
            <p>Belum ada antrian yang dipanggil.</p>
        </div>
        @endif

        <h4>Sisa Antrian: {{ $sisaAntrian }}</h4>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
