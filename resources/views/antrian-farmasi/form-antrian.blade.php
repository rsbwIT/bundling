<!-- resources/views/antrian-farmasi/form-antrian.blade.php -->

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-900 flex flex-col items-center justify-center min-h-screen">

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

    <!-- Content -->
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mt-24">
        <div class="text-center mb-4">
            <h2 class="text-xl font-semibold">Silahkan Ambil No. Antrian</h2>
        </div>

        @if (session('status'))
            <div class="text-green-500 mb-4">{{ session('status') }}</div>
        @endif

        <form action="{{ route('ambil.antrian') }}" method="POST" class="text-center">
            @csrf
            <div class="text-5xl font-bold mb-2">{{ $nomorAntrian ?? '-' }}</div>
            <div class="text-gray-500 mb-4">{{ now()->format('Y-m-d') }}</div>

            <!-- Input Nomor Rekam Medik -->
            <input
                type="text"
                name="rekamMedik"
                class="w-full p-2 mb-4 border border-gray-300 text-center"
                placeholder="Nomor Rekam Medik"
                value="{{ old('rekam_medik') }}"
                required
                oninput="fetchPatientData(this.value)"
            >
            <input
                type="text"
                name="namaPasien"
                id="namaPasien"
                class="w-full p-2 mb-4 border border-gray-300 text-center"
                placeholder="Nama Pasien"
                value="{{ old('nama_pasien') }}"
                readonly
                required
            >

            <input type="hidden" name="nomorAntrianHidden" value="{{ $nomorAntrian ?? '' }}">
            <button type="submit" class="w-full bg-red-500 text-white py-2 text-xl font-semibold rounded">AMBIL ANTRIAN</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="fixed bottom-0 left-0 w-full bg-blue-800 text-white text-sm p-2 flex justify-between items-center">
        <div id="datetime">
            <!-- Tanggal dan Jam akan dimasukkan oleh JavaScript -->
        </div>
        <div>
            <i class="fas fa-user"></i> Login sebagai
        </div>
        <div>
            Made with <i class="fas fa-heart text-red-500"></i> by rs.bumiwaras.co.id
        </div>
    </footer>

    <script>
        function updateDateTime() {
            const datetimeElement = document.getElementById('datetime');
            const now = new Date();
            const formattedDate = now.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            const formattedTime = now.toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });

            datetimeElement.innerHTML = `<i class="far fa-calendar-alt"></i> ${formattedDate} <i class="far fa-clock"></i> ${formattedTime}`;
        }

        setInterval(updateDateTime, 1000); // Memperbarui setiap detik
        updateDateTime(); // Panggil sekali saat halaman pertama kali dimuat

        function fetchPatientData(rm) {
            if (rm.length > 0) {
                fetch(`/fetch-patient/${rm}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.nama_pasien) {
                            document.getElementById('namaPasien').value = data.nama_pasien;
                        } else {
                            document.getElementById('namaPasien').value = '';
                        }
                    })
                    .catch(error => console.error('Error fetching patient data:', error));
            }
        }
    </script>
</body>
</html>
