<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Farmasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Ambil Antrian Farmasi</h2>

        <!-- Pesan sukses atau error -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('antrian-farmasi.ambilAntrian') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="no_rkm_medis" class="form-label">Nomor Rekam Medis</label>
                <input type="text" class="form-control" id="no_rkm_medis" name="no_rkm_medis"
                    placeholder="Masukkan Nomor Rekam Medis" required>
            </div>

            <div class="mb-3">
                <label for="racik_non_racik" class="form-label">Kategori Obat</label>
                <select class="form-control" id="racik_non_racik" name="racik_non_racik" required>
                    <option value="A">Non Racik</option>
                    <option value="B">Racik</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">CETAK ANTRIAN</button>
        </form>

        <!-- Tombol Cetak muncul setelah form disubmit -->
        @if (session('success'))
            <div class="mt-4">
                <a href="{{ route('antrian-farmasi.cetak', ['nomorAntrian' => session('nomorAntrian')]) }}"
                    class="btn btn-success" target="_blank">Cetak Antrian</a>
            </div>
        @endif




    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
