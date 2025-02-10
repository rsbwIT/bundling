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
            <img alt="Logo Rumah Sakit" class="h-20" src="{{ asset('img/bw2.png') }}" />
        </div>
        <div class="text-center">
            <h1 class="text-5xl font-bold text-gray-800">Antrian Farmasi</h1>
        </div>
        <div class="flex items-center">
            <img alt="Logo BPJS" class="h-10" src="{{ asset('img/bpjs.png') }}" />
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
            <div class="text-5xl font-bold mb-2" id="nomorAntrian">{{ $nomorAntrian ?? '-' }}</div>
            <div class="text-gray-500 mb-4">{{ now()->format('Y-m-d') }}</div>

            <!-- Input Nomor Rekam Medik -->
            <input type="text" name="rekamMedik" class="w-full p-2 mb-4 border border-gray-300 text-center"
                placeholder="Nomor Rekam Medik" value="{{ old('rekam_medik') }}" required
                oninput="fetchPatientData(this.value)">

            <!-- Input Nama Pasien -->
            <input type="text" name="namaPasien" id="namaPasien"
                class="w-full p-2 mb-4 border border-gray-300 text-center" placeholder="Nama Pasien"
                value="{{ old('nama_pasien') }}" readonly required>

            <input type="hidden" name="nomorAntrianHidden" value="{{ $nomorAntrian ?? '' }}">

            <!-- Pilihan Racik / Non-Racik -->
            <div class="mb-4 text-left">
                <label class="block text-lg font-semibold">Jenis Obat</label>
                <select name="racik_non_racik" id="racik_non_racik" class="w-full p-2 mt-2 border border-gray-300 text-center" required onchange="updateNomorAntrian()">
                    <option value="RACIK" {{ old('racik_non_racik') == 'RACIK' ? 'selected' : '' }}>Racik</option>
                    <option value="NON_RACIK" {{ old('racik_non_racik') == 'NON_RACIK' ? 'selected' : '' }}>Non-Racik</option>
                </select>
            </div>

            <div class="flex space-x-4 mt-6">
                <!-- Tombol Ambil Antrian -->
                <button type="button" class="w-full bg-red-500 text-white py-2 text-xl font-semibold rounded" onclick="submitForm()">AMBIL ANTRIAN</button>
            </div>
        </form>

    </div>

    <script>
        // Fungsi untuk mengambil data pasien berdasarkan nomor rekam medis
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

        // Fungsi untuk memperbarui nomor antrian sesuai jenis obat yang dipilih
        function updateNomorAntrian() {
            const jenisObat = document.getElementById('racik_non_racik').value;
            const nomorAntrianElement = document.getElementById('nomorAntrian');

            // Lakukan request AJAX untuk mendapatkan nomor antrian berdasarkan jenis obat
            fetch(`/get-next-antrian/${jenisObat}`)
                .then(response => response.json())
                .then(data => {
                    // Update nomor antrian
                    if (data.nomorAntrian) {
                        nomorAntrianElement.textContent = data.nomorAntrian;
                    } else {
                        nomorAntrianElement.textContent = '-';
                    }
                })
                .catch(error => console.error('Error updating nomor antrian:', error));
        }

        // Fungsi untuk mengirim formulir dan membuka halaman cetak
        function submitForm() {
            const form = document.querySelector("form");
            const formData = new FormData(form);

            // Buka tab baru sebelum mengirim request untuk menghindari pemblokiran popup
            let printWindow = window.open("", "_blank");

            fetch("{{ route('ambil.antrian') }}", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.nomorAntrian) {
                    // Arahkan tab baru ke halaman cetak dengan nomor antrian
                    printWindow.location.href = "{{ url('/cetak-antrian') }}/" + data.nomorAntrian;
                } else {
                    printWindow.close();
                }
            })
            .catch(error => {
                console.error("Error:", error);
                printWindow.close();
            });
        }
    </script>


</body>

</html>
