{{-- resources/views/antrian-farmasi/form-antrian.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Antrian Farmasi</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container mt-5">
        <h2>Formulir Antrian Farmasi</h2>
        <form action="{{ route('antrian.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="rekamMedik" class="form-label">Nomor Rekam Medis</label>
                <input type="text" class="form-control" id="rekamMedik" name="rekamMedik" required>
            </div>

            <div class="mb-3">
                <label for="namaPasien" class="form-label">Nama Pasien</label>
                <input type="text" class="form-control" id="namaPasien" name="namaPasien" required>
            </div>

            <div class="mb-3">
                <label for="racik_non_racik" class="form-label">Jenis Obat</label>
                <select class="form-control" id="racik_non_racik" name="racik_non_racik" required>
                    <option value="RACIK">RACIK</option>
                    <option value="NON RACIK">NON RACIK</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="nomorAntrian" class="form-label">Nomor Antrian (terakhir)</label>
                <input type="text" class="form-control" id="nomorAntrian" value="{{ $nomorAntrianRacik }}" disabled>
            </div>

            <button type="submit" class="btn btn-primary">Buat Antrian</button>
        </form>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
