<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RS Bumi Waras - Antrian Farmasi</title>
</head>

<body>
    <h1>Antrian Farmasi</h1>

    <!-- Menampilkan Antrian -->
    @if ($antrian)
        <div>
            <p>Nomor Antrian: {{ $antrian->nomor_antrian }}</p>
            <p>Nama Pasien: {{ $antrian->nama_pasien }}</p>
            <p>Status: {{ $antrian->status }}</p>
            <p>Loket: {{ $antrian->nama_loket }}</p>

            <!-- Tombol Panggil Antrian dengan Form -->
            <form action="{{ route('antrian.panggil') }}" method="POST">
                @csrf
                <input type="hidden" name="antrian_id" value="{{ $antrian->id }}">
                <button type="submit">Panggil Antrian</button>
            </form>
        </div>
    @else
        <p>Tidak ada antrian menunggu saat ini.</p>
    @endif



</body>

</html>
