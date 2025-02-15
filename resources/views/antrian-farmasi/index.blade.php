<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Farmasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<!-- Header -->
<div class="w-full bg-white shadow-md p-4 flex justify-between items-center border-b fixed top-0 left-0 z-10">
    <div class="flex items-center">
        <img alt="Logo of Rumah Sakit Bumi Waras" class="h-20" src="{{ asset('img/bw2.png') }}" />
    </div>
    <div class="text-center">
        <h1 class="text-5xl font-bold text-gray-800">Antrian Farmasi</h1>
    </div>
    <div class="flex items-center">
        <img alt="Logo of BPJS Kesehatan" class="h-10" src="{{ asset('img/bpjs.png') }}" />
    </div>
</div>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-xl font-semibold text-center mb-4">Ambil Antrian Farmasi</h2>

        <!-- Pesan sukses atau error -->
        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button class="text-white" onclick="this.parentElement.style.display='none'">×</button>
            </div>
        @elseif(session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4 flex justify-between items-center">
                <span>{{ session('error') }}</span>
                <button class="text-white" onclick="this.parentElement.style.display='none'">×</button>
            </div>
        @endif

        <!-- Form Pengambilan Antrian -->
        <form action="{{ route('antrian-farmasi.ambilAntrian') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="no_rkm_medis" class="block text-gray-700 font-medium">Nomor Rekam Medis</label>
                <input type="text" id="no_rkm_medis" name="no_rkm_medis"
                    class="w-full p-3 border border-gray-300 rounded mt-1 focus:ring focus:ring-blue-300"
                    placeholder="Masukkan Nomor Rekam Medis" required>
            </div>

            <div class="mb-4">
                <label for="racik_non_racik" class="block text-gray-700 font-medium">Kategori Obat</label>
                <select id="racik_non_racik" name="racik_non_racik"
                    class="w-full p-3 border border-gray-300 rounded mt-1 focus:ring focus:ring-blue-300" required>
                    <option value="A">Non Racik</option>
                    <option value="B">Racik</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold py-3 rounded transition duration-300">
                AMBIL ANTRIAN
            </button>
        </form>

        <!-- Tombol Cetak jika sukses -->
        @if (session('success'))
        <div class="mt-4 text-center">
            <a href="{{ route('antrian-farmasi.cetak', ['nomorAntrian' => session('nomorAntrian')]) }}"
                class="bg-green-500 hover:bg-green-600 text-white text-lg font-bold py-3 px-6 rounded inline-block transition duration-300 flex items-center justify-center gap-2"
                target="_blank">
                <i class="fas fa-print"></i> Cetak Antrian
            </a>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <footer
        class="fixed bottom-0 left-0 right-0 bg-blue-800 text-white text-sm py-2 px-4 flex justify-between items-center">
        <div>
            <i class="far fa-calendar-alt"></i> <span id="tanggal"></span>
            <i class="far fa-clock"></i> <span id="jam"></span>
        </div>

        <script>
            function updateTime() {
                let now = new Date();

                // Format tanggal dalam bahasa Indonesia
                let options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
                let tanggal = now.toLocaleDateString('id-ID', options);

                // Format jam
                let jam = now.toLocaleTimeString('id-ID', { hour12: true });

                document.getElementById('tanggal').innerText = tanggal;
                document.getElementById('jam').innerText = jam;
            }

            // Jalankan update pertama kali
            updateTime();
            // Perbarui setiap detik
            setInterval(updateTime, 1000);
        </script>
        <div>
            Made with <i class="fas fa-hospital text-red-500"></i> rsbumiwaras.co.id
        </div>
    </footer>
</body>

</html>
