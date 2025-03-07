<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Farmasi - RS Bumi Waras</title>
    <link rel="icon" href="{{ asset('img/bw2.png') }}" type="image/png"> <!-- Favicon logo -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-white">
    <header class="bg-white p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <img src="{{ asset('img/bw2.png') }}" alt="Logo Rumah Sakit Bumi Waras" style="height: 110px; width: auto;">
            <h1 class="text-3xl md:text-5xl font-semibold text-center flex-1 mx-4">Antrian Farmasi</h1>
            <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS Kesehatan" style="height: 50px; width: auto;">
        </div>
    </header>

    <main class="p-4">
        @if (session('status'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-teal-500 text-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-12">Nomor Antrian</h2>
                <div class="text-6xl font-bold mb-4 text-center">{{ $antrian->nomor_antrian ?? '-' }}</div>
                <div class="text-6xl font-bold mb-4 text-center">{{ $antrian->rekam_medik ?? '-' }}</div>
                <div class="text-6xl font-bold mb-4 text-center">{{ $antrian->nama_pasien ?? '-' }}</div>
                <div class="text-xl flex items-center justify-center text-center">
                    <i class="fas fa-arrow-right mr-2"></i>
                    <span>{{ $antrian->nama_loket ?? '' }}</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="{{ route('antrian.panggil') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Panggil Antrian
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            @foreach ($antrians as $antrian)
                <div class="bg-teal-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-semibold mb-4">Nomor Antrian</h2>
                    <div class="text-6xl font-bold mb-4">{{ $antrian->nomor_antrian }}</div>
                    <div class="text-xl flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        <span>{{ $antrian->nama_loket }}</span>
                    </div>
                    <div class="mt-4">
                        <span class="text-lg">Status: {{ $antrian->status }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
