<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-blue-900 flex flex-col items-center justify-center min-h-screen space-y-6">

    <!-- Display Antrian -->
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Nomor Antrian</h2>
        <h1 class="text-6xl font-bold text-red-600" id="nomor_antrian">-</h1>
        <p class="text-gray-500 mt-2" id="rekam_medik">Rekam Medik: -</p>
        <p class="text-gray-500 mt-2" id="nama_pasien">Nama Pasien: -</p>
        <p class="text-gray-500 mt-2" id="status">Status: -</p>
        <p class="text-gray-500 mt-2" id="keterangan">Keterangan: -</p>
        <p class="text-gray-500 mt-2">Silakan menuju loket</p>
    </div>

    <!-- Panel Panggil Antrian -->
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Panggil Antrian</h2>
        <button id="panggil_antrian" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700">
            Panggil Antrian Berikutnya
        </button>
        <p id="panggil_status" class="text-gray-600 mt-2"></p>
    </div>

    <script>
        function updateAntrian() {
            $.get("{{ route('display.getAntrian') }}", function(data) {
                if (data.nomor_antrian && data.nomor_antrian !== 'Belum ada antrian') {
                    $('#nomor_antrian').text(data.nomor_antrian);
                    $('#rekam_medik').text('Rekam Medik: ' + data.rekam_medik);
                    $('#nama_pasien').text('Nama Pasien: ' + data.nama_pasien);
                    $('#status').text('Status: ' + data.status);
                    $('#keterangan').text('Keterangan: ' + data.keterangan);
                } else {
                    $('#nomor_antrian').text('Belum ada antrian');
                    $('#rekam_medik').text('');
                    $('#nama_pasien').text('');
                    $('#status').text('');
                    $('#keterangan').text('');
                }
            }).fail(function() {
                $('#nomor_antrian').text('Gagal mengambil data');
            });
        }

        function panggilAntrian() {
            $.post("{{ route('antrian.panggil') }}", {
                _token: "{{ csrf_token() }}"
            }, function(response) {
                if (response.success) {
                    $('#panggil_status').text('Memanggil antrian: ' + response.nomor_antrian);
                    updateAntrian();
                } else {
                    $('#panggil_status').text('Tidak ada antrian yang menunggu');
                }
            }).fail(function() {
                $('#panggil_status').text('Gagal memanggil antrian');
            });
        }

        $(document).ready(function() {
            updateAntrian();
            setInterval(updateAntrian, 3000);

            $('#panggil_antrian').click(function() {
                panggilAntrian();
            });
        });
    </script>

</body>
</html>
