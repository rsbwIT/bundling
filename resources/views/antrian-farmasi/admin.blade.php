<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md text-center">
        <h2 class="text-2xl font-bold mb-4">Admin Antrian</h2>

        <h1 class="text-6xl font-bold text-blue-600" id="nomor_antrian">
            {{ $antrian ? $antrian->nomor_antrian : '-' }}
        </h1>

        <button id="panggilBtn"
            class="mt-6 bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold py-3 px-6 rounded transition duration-300">
            Panggil Antrian
        </button>
    </div>

    <script>
        $(document).ready(function() {
            $('#panggilBtn').click(function() {
                $.post("{{ route('admin.panggil') }}", {_token: "{{ csrf_token() }}"}, function(data) {
                    $('#nomor_antrian').text(data.nomor_antrian);
                });
            });
        });
    </script>

</body>
</html>
