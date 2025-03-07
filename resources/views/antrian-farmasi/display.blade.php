<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RS Bumi Waras - Antrian Farmasi</title>
    <link rel="icon" href="{{ asset('img/bw2.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="bg-white p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <img src="{{ asset('img/bw2.png') }}" alt="Logo Rumah Sakit Bumi Waras" class="h-[110px]">
            <h1 class="text-3xl md:text-5xl font-semibold text-center flex-1 mx-4">Antrian Farmasi</h1>
            <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS Kesehatan" class="h-[50px]">
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-4">
        @if ($antrian)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Informasi Antrian -->
            <div class="bg-teal-500 text-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-4">Nomor Antrian Saat Ini</h2>
                <div class="text-6xl font-bold mb-4 text-center">{{ $antrian->nomor_antrian }}</div>
                <div class="text-2xl font-bold mb-4 text-center">{{ $antrian->rekam_medik }}</div>
                <div class="text-2xl font-bold mb-4 text-center">{{ $antrian->nama_pasien }}</div>
                <div class="text-xl flex items-center justify-center text-center">
                    <i class="fas fa-arrow-right mr-2"></i>
                    <span>{{ $antrian->nama_loket }}</span>
                </div>
            </div>

            <!-- Video Profil -->
            {{-- <div class="bg-white p-6 rounded-lg shadow-md"> --}}
                <video width="100%" height="315" autoplay muted controls loop class="border-4 border-black rounded-lg">
                    <source src="{{ asset('video/profile.mp4') }}" type="video/mp4">
                </video>
            </div>
        </div>

        <!-- Antrian Berikutnya -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            @foreach(range(1, 3) as $i)
                <div class="bg-teal-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-semibold mb-4">Nomor Antrian</h2>
                    <div class="text-6xl font-bold mb-4">{{ $antrian->nomor_antrian }}</div>
                    <div class="text-xl flex items-center justify-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        <span>Loket {{ $i }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @else
        <!-- Jika Tidak Ada Antrian Hari Ini -->
        <p class="text-center text-xl text-red-500 mt-6">
            Tidak ada antrian untuk tanggal {{ now()->format('d-m-Y') }}.
        </p>
        @endif

        <!-- Pesan Status -->
        @if (session('status'))
            <p class="mt-4 text-center text-lg text-green-500">{{ session('status') }}</p>
        @endif
    </main>
</body>

</html>
