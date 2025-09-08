<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data FRISTA BPJS</title>

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h3 class="mb-4 fw-bold text-primary">📋 Data FRISTA BPJS - {{ $tanggal }}</h3>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No Rujukan</th>
                                <th>Tanggal Rujukan</th>
                                <th>PPK Perujuk</th>
                                <th>Poli Rujukan</th>
                                <th>Diagnosa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($list as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td>{{ $item['noKunjungan'] ?? '-' }}</td>
                                    <td>{{ $item['tglKunjungan'] ?? '-' }}</td>
                                    <td>{{ $item['provPerujuk']['nama'] ?? '-' }}</td>
                                    <td>{{ $item['poliRujukan']['nama'] ?? '-' }}</td>
                                    <td>{{ $item['diagnosa']['nama'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        <em>Tidak ada data tersedia</em>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <h4 class="mb-4">Data FRISTA BPJS - {{ $tanggal }}</h4>

@if(!empty($meta))
    <div class="alert alert-info">
        Kode: {{ $meta['code'] ?? '-' }} <br>
        Pesan: {{ $meta['message'] ?? '-' }}
    </div>
@endif


    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
