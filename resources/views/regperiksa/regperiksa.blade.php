<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Pasien</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body class="bg-white p-4">
    <div class="max-w-full mx-auto">
        <h1 class="text-gray-900 font-semibold text-lg mb-3 flex items-center gap-2">
            <i class="fas fa-book"></i> DAFTAR PASIEN
        </h1>

        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between mb-3 gap-2">
            <div class="flex gap-2">
                <a href="{{ url('/') }}" title="Kembali ke Home"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded flex items-center gap-1 shadow-md">
                    <i class="fas fa-home"></i>
                    Home
                </a>

                <a href="{{ route('regperiksa.index') }}"
                    class="bg-green-600 text-white text-xs px-3 py-1 rounded flex items-center gap-1">
                    <i class="fas fa-database"></i> Semua Data
                </a>
            </div>

            <form method="GET" action="{{ route('regperiksa.index') }}"
                class="flex items-center border border-gray-300 rounded overflow-hidden max-w-xs w-full" role="search"
                aria-label="Search by no_rkm_medis">
                <input type="search" name="no_rkm_medis" placeholder="Cari No RM..." value="{{ $no_rkm_medis }}"
                    class="text-xs px-2 py-1 outline-none w-full" aria-label="Search no_rkm_medis" />
                <button type="submit" class="bg-gray-200 text-gray-700 text-xs px-3 py-1 flex items-center gap-1"
                    aria-label="Search button">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-xs text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">No Rawat</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Nomor RM</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Nama Pasien</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Tanggal Registrasi</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Status</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Status Lanjut</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Status Bayar</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Dokter</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Poliklinik</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold text-center">MENU</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($results as $row)
                        <tr>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->no_rawat }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->no_rkm_medis }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->nm_pasien }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->tgl_registrasi }}</td>
                            <td class="border border-gray-300 px-2 py-1" id="status-{{ $row->no_rawat }}">
                                {{ $row->stts }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->status_lanjut }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->status_bayar }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->nm_dokter }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $row->nm_poli }}</td>
                            <td class="border border-gray-300 px-2 py-1 text-center space-x-1">
                                <button
                                    onclick="showDetailModal(
                                            '{{ $row->no_rawat }}',
                                            '{{ $row->no_rkm_medis }}',
                                            '{{ $row->nm_pasien }}',
                                            '{{ $row->stts }}',
                                            '{{ $row->status_lanjut }}',
                                            '{{ $row->status_bayar }}')"
                                    class="bg-sky-400 text-white text-[10px] px-2 py-0.5 rounded inline-flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i> Detail
                                </button>


                                <button type="button"
                                    class="bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded inline-flex items-center gap-1"
                                    onclick="togglePopup(this)">
                                    <i class="fas fa-edit"></i> Edit
                                    <i class="fas fa-caret-down ml-1"></i>
                                </button>

                                <div
                                    class="popup-menu hidden absolute right-0 mt-1 w-24 bg-white border border-gray-200 rounded shadow-md text-xs text-left z-20">
                                    <a href="#" class="block px-3 py-1 hover:bg-gray-100"
                                        onclick="updateStatus('{{ $row->no_rawat }}', 'Batal')">
                                        Batal
                                    </a>

                                    <a href="#" class="block px-3 py-1 hover:bg-gray-100"
                                        onclick="updateStatus('{{ $row->no_rawat }}', 'Belum')">
                                        Belum
                                    </a>
                                </div>

                                {{-- <button
                                    class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded inline-flex items-center gap-1">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="border border-gray-300 text-center py-3">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan atau menyembunyikan popup menu
        function togglePopup(button) {
            const menu = button.nextElementSibling;
            menu.classList.toggle('hidden');
        }

        // Fungsi untuk mengupdate status pasien menggunakan AJAX
        function updateStatus(no_rawat, status) {
            if (!confirm('Apakah Anda yakin untuk mengubah status?')) return;

            fetch('{{ route('updateStatus') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        no_rawat: no_rawat,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('status-' + no_rawat).innerText = status;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
    <script>
        function showDetailModal(no_rawat, no_rm, nama, stts, lanjut, bayar) {
            document.getElementById('modal-no-rawat').innerText = no_rawat;
            document.getElementById('modal-no-rm').innerText = no_rm;
            document.getElementById('modal-nama').innerText = nama;
            document.getElementById('modal-stts').innerText = stts;
            document.getElementById('modal-lanjut').innerText = lanjut;
            document.getElementById('modal-bayar').innerText = bayar;

            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
        }
    </script>
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
                let options = {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                };
                let tanggal = now.toLocaleDateString('id-ID', options);

                // Format jam
                let jam = now.toLocaleTimeString('id-ID', {
                    hour12: true
                });

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
<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-30">
    <div class="bg-white rounded-lg w-full max-w-md p-4 shadow-lg">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h2 class="text-sm font-bold text-gray-700">Detail Status Pasien</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-red-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="text-xs space-y-1">
            <p><strong>No Rawat:</strong> <span id="modal-no-rawat"></span></p>
            <p><strong>No RM:</strong> <span id="modal-no-rm"></span></p>
            <p><strong>Nama Pasien:</strong> <span id="modal-nama"></span></p>
            <p><strong>Status:</strong> <span id="modal-stts"></span></p>
            <p><strong>Status Lanjut:</strong> <span id="modal-lanjut"></span></p>
            <p><strong>Status Bayar:</strong> <span id="modal-bayar"></span></p>
        </div>
    </div>
</div>



</html>
