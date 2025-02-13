<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Antrian</title>
</head>
<body>
    <h1>Daftar Antrian</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Nomor Antrian</th>
                <th>Nama Pasien</th>
                <th>Jenis Obat</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($antrian as $item)
                <tr>
                    <td>{{ $item->nomor_antrian }}</td>
                    <td>{{ $item->nama_pasien }}</td>
                    <td>{{ $item->racik_non_racik }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
