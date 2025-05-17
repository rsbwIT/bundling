<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Pasien</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-white p-4">
    <!-- Hidden Session Data -->
    <input type="hidden" id="user_id" value="{{ $user_data ? $user_data->nik : '' }}">
    <input type="hidden" id="user_name" value="{{ $user_data ? $user_data->nama : '' }}">

    <!-- Notification Toast -->
    <div id="notification"
        class="fixed top-4 right-4 transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center p-4 mb-4 text-sm rounded-lg shadow-lg" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div id="notification-message" class="ml-3 text-sm font-normal"></div>
            <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 hover:bg-gray-100"
                data-dismiss-target="#notification" aria-label="Close">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

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

                <a href="{{ route('regperiksabilling.index1') }}"
                    class="bg-green-600 text-white text-xs px-3 py-1 rounded flex items-center gap-1">
                    <i class="fas fa-database"></i> Semua Data
                </a>

                <button onclick="showBulkUpdateModal()"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded flex items-center gap-1">
                    <i class="fas fa-tasks"></i> Update Status
                </button>

                <button onclick="showLogs()"
                    class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-1 rounded flex items-center gap-1">
                    <i class="fas fa-history"></i> Lihat Log
                </button>
            </div>

            <form method="GET" action="{{ route('regperiksabilling.index1') }}"
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
                        <th class="border border-gray-300 px-2 py-1 font-semibold">
                            <input type="checkbox" id="select-all" class="form-checkbox h-4 w-4 text-blue-600">
                        </th>
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
                            <td class="border border-gray-300 px-2 py-1">
                                <input type="checkbox" name="selected_records[]" value="{{ $row->no_rawat }}"
                                    class="form-checkbox h-4 w-4 text-blue-600 record-checkbox">
                            </td>
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
                                            '{{ $row->status_bayar }}');
                                            logUserActivity('DETAIL', 'Lihat detail pasien: {{ $row->no_rawat }} - {{ $row->nm_pasien }}')"
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
                                    class="popup-menu hidden absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded shadow-md text-xs text-left z-20">
                                    <div class="px-3 py-2 border-b">
                                        <select id="custom-status-{{ $row->no_rawat }}"
                                            class="w-full px-2 py-1 text-xs border rounded">
                                            <option value="">Pilih Status...</option>
                                            <option value="belum">Belum</option>
                                            <option value="batal">Batal</option>
                                        </select>
                                        <button
                                            onclick="
                                            updateStatus('{{ $row->no_rawat }}', document.getElementById('custom-status-{{ $row->no_rawat }}').value);"
                                            class="w-full mt-1 bg-blue-500 text-white px-2 py-1 rounded text-center hover:bg-blue-600">
                                            Update Status
                                        </button>
                                    </div>
                                </div>
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
        // Fungsi untuk menampilkan notifikasi toast
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');
            const icon = notification.querySelector('i.fas');

            // Set warna dan ikon berdasarkan type
            if (type === 'success') {
                notification.querySelector('.flex').className =
                    'flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 shadow-lg';
                icon.className = 'fas fa-check-circle text-2xl text-green-400';
            } else if (type === 'error') {
                notification.querySelector('.flex').className =
                    'flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 shadow-lg';
                icon.className = 'fas fa-times-circle text-2xl text-red-400';
            }

                notificationMessage.textContent = message;
            notification.classList.remove('translate-x-full');

            setTimeout(() => {
                notification.classList.add('translate-x-full');
            }, 3000);
        }

        // Fungsi untuk mengupdate status pasien
        function updateStatus(no_rawat, status) {
            if (!status) {
                showNotification('Silakan pilih status terlebih dahulu', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin mengubah status menjadi "${status}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading state
                    const statusElement = document.getElementById('status-' + no_rawat);
                    const originalText = statusElement.innerText;
                    statusElement.dataset.originalStatus = originalText;
                    statusElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('{{ route('updateStatus') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            no_rawat: no_rawat,
                            stts: status
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 404) {
                                throw new Error('Data registrasi tidak ditemukan');
                            } else if (response.status === 422) {
                                throw new Error('Data yang dimasukkan tidak valid');
                            } else if (response.status === 500) {
                                throw new Error('Hai.. Pegawai Rs Bumi Waras, Data Ini Sudah Di Ubah Dengan Status Yang Sama');
                            } else {
                                throw new Error('Gagal mengubah status. Silakan coba lagi');
                            }
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            statusElement.innerText = status;
                            showNotification(data.message, 'success');
                            // Log aktivitas update status
                            logUserActivity('UPDATE_STATUS', `Mengubah status no rawat ${no_rawat} dari ${originalText} menjadi ${status}`);
                        } else {
                            statusElement.innerText = originalText;
                            showNotification(data.message || 'Terjadi kesalahan saat mengubah status', 'error');
                        }

                        // Sembunyikan popup menu
                        document.querySelectorAll('.popup-menu').forEach(menu => {
                            menu.classList.add('hidden');
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        statusElement.innerText = originalText;
                        showNotification(error.message || 'Terjadi kesalahan saat mengubah status. Silakan coba lagi nanti', 'error');

                        // Sembunyikan popup menu
                        document.querySelectorAll('.popup-menu').forEach(menu => {
                            menu.classList.add('hidden');
                        });
                    });
                }
            });
        }

        // Fungsi untuk menampilkan atau menyembunyikan popup menu
        function togglePopup(button) {
            // Tutup semua popup menu yang terbuka
            document.querySelectorAll('.popup-menu').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.add('hidden');
                }
            });

            const menu = button.nextElementSibling;
            menu.classList.toggle('hidden');
        }

        // Event listener untuk menutup popup saat klik di luar
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.popup-menu') && !event.target.closest('button')) {
                document.querySelectorAll('.popup-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Event listener untuk checkbox "Select All"
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.getElementsByClassName('record-checkbox');
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
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
                let options = {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                };
                let tanggal = now.toLocaleDateString('id-ID', options);
                let jam = now.toLocaleTimeString('id-ID', {
                    hour12: true
                });

                document.getElementById('tanggal').innerText = tanggal;
                document.getElementById('jam').innerText = jam;
            }

            updateTime();
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

<!-- Modal Bulk Update -->
<div id="bulkUpdateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-30">
    <div class="bg-white rounded-lg w-full max-w-md p-4 shadow-lg">
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h2 class="text-sm font-bold text-gray-700">Update Status Terpilih</h2>
            <button onclick="closeBulkUpdateModal()" class="text-gray-500 hover:text-red-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="space-y-4">
            <p class="text-sm text-gray-600">
                Pilih status baru untuk data yang dipilih:
            </p>
            <select id="bulk-status" class="w-full px-3 py-2 border rounded text-sm">
                <option value="">Pilih Status...</option>
                <option value="belum">Belum</option>
                <option value="batal">Batal</option>
            </select>
            <div class="flex justify-end gap-2 mt-4">
                <button onclick="closeBulkUpdateModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    Batal
                </button>
                <button onclick="updateBulkStatus()"
                    class="px-4 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                    Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Log -->
<div id="logModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-30">
    <div class="bg-white rounded-lg w-full max-w-6xl mx-4 my-6 shadow-lg flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex justify-between items-center border-b p-4 bg-gray-50 rounded-t-lg sticky top-0">
            <h2 class="text-lg font-bold text-gray-700 flex items-center gap-2">
                <i class="fas fa-history"></i>
                Riwayat Log Aktivitas
            </h2>
            <button onclick="closeLogModal()" class="text-gray-500 hover:text-red-500 p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Filter Section -->
        <div class="p-4 border-b bg-gray-50">
            <div class="flex flex-wrap md:flex-nowrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Dari:</label>
                    <input type="date" id="startDate" class="text-sm border rounded px-2 py-1">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Sampai:</label>
                    <input type="date" id="endDate" class="text-sm border rounded px-2 py-1">
                </div>
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari log..."
                            class="w-full text-sm border rounded px-3 py-1 pl-8"
                            onkeyup="filterLogs()">
                        <i class="fas fa-search absolute left-2.5 top-2 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="applyDateFilter()"
                        class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <button onclick="resetFilter()"
                        class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-4 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-undo"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container with Scroll -->
        <div class="overflow-y-auto flex-grow p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">User ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="logTableBody" class="bg-white divide-y divide-gray-200 text-sm">
                        <!-- Log data will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t p-4 bg-gray-50 rounded-b-lg">
            <div class="flex justify-between items-center text-sm text-gray-600">
                <div>
                    <i class="fas fa-info-circle"></i>
                    <span id="logCount">Menampilkan 100 log aktivitas terakhir</span>
                </div>
                <button onclick="closeLogModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.getElementsByClassName('record-checkbox');
        for (let checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });

    function showBulkUpdateModal() {
        const selectedCheckboxes = document.querySelectorAll('.record-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            showNotification('Pilih minimal satu data untuk diupdate', 'error');
            return;
        }
        document.getElementById('bulkUpdateModal').classList.remove('hidden');
        document.getElementById('bulkUpdateModal').classList.add('flex');
    }

    function closeBulkUpdateModal() {
        document.getElementById('bulkUpdateModal').classList.add('hidden');
        document.getElementById('bulkUpdateModal').classList.remove('flex');
    }

    function updateBulkStatus() {
        const status = document.getElementById('bulk-status').value;
        const selectedCheckboxes = document.querySelectorAll('.record-checkbox:checked');
        const selectedNoRawat = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (!status) {
            showNotification('Silakan pilih status terlebih dahulu', 'error');
            return;
        }

        if (selectedNoRawat.length === 0) {
            showNotification('Pilih minimal satu data untuk diupdate', 'error');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin mengubah ${selectedNoRawat.length} data menjadi status '${status}'?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading state untuk semua status yang dipilih
                const originalStatuses = {};
                selectedNoRawat.forEach(no_rawat => {
                    const statusElement = document.getElementById('status-' + no_rawat);
                    if (statusElement) {
                        originalStatuses[no_rawat] = statusElement.innerText;
                        statusElement.dataset.originalStatus = statusElement.innerText;
                        statusElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    }
                });

                fetch('{{ route('updateBulkStatus') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        stts: status,
                        no_rawat: selectedNoRawat
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Data Sudah Di Ubah');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update status di UI untuk semua yang dipilih
                        selectedNoRawat.forEach(no_rawat => {
                            const statusElement = document.getElementById('status-' + no_rawat);
                            if (statusElement) {
                                statusElement.innerText = status;
                            }
                        });
                        showNotification(data.message, 'success');

                        // Log aktivitas bulk update
                        const updateMessage = selectedNoRawat.map(no_rawat => {
                            const originalStatus = document.getElementById('status-' + no_rawat).dataset.originalStatus;
                            // Only include in log if status actually changed
                            if (originalStatus.toLowerCase() !== status.toLowerCase()) {
                                return `Mengubah status no rawat ${no_rawat} dari ${originalStatus} menjadi ${status}`;
                            }
                            return null;
                        }).filter(msg => msg !== null).join('\n');

                        if (updateMessage) {
                            logUserActivity('BULK_UPDATE_STATUS', updateMessage);
                        }

                        // Uncheck semua checkbox
                        document.getElementById('select-all').checked = false;
                        selectedCheckboxes.forEach(cb => cb.checked = false);

                        closeBulkUpdateModal();
                    } else {
                        // Kembalikan status asli jika gagal
                        selectedNoRawat.forEach(no_rawat => {
                            const statusElement = document.getElementById('status-' + no_rawat);
                            if (statusElement) {
                                statusElement.innerText = originalStatuses[no_rawat] || '';
                            }
                        });
                        showNotification(data.message || 'Terjadi kesalahan saat mengubah status', 'error');
                        closeBulkUpdateModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Kembalikan status asli jika terjadi error
                    selectedNoRawat.forEach(no_rawat => {
                        const statusElement = document.getElementById('status-' + no_rawat);
                        if (statusElement) {
                            statusElement.innerText = originalStatuses[no_rawat] || '';
                        }
                    });
                    showNotification('Terjadi kesalahan saat mengubah status: ' + error.message, 'error');
                    closeBulkUpdateModal();
                });
            }
        });
    }

    function showLogs() {
        // Set default dates (last 7 days)
        const end = new Date();
        const start = new Date();
        start.setDate(start.getDate() - 7);

        document.getElementById('startDate').value = start.toISOString().split('T')[0];
        document.getElementById('endDate').value = end.toISOString().split('T')[0];

        // Show modal first
        document.getElementById('logModal').classList.remove('hidden');
        document.getElementById('logModal').classList.add('flex');

        fetchLogs();
    }

    function fetchLogs() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Show loading state
        const tbody = document.getElementById('logTableBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>';

        fetch(`{{ route('getLogs') }}?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    tbody.innerHTML = '';

                    if (data.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Tidak ada data log untuk periode ini</td></tr>';
                        return;
                    }

                    data.data.forEach(log => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';

                        row.innerHTML = `
                            <td class="px-4 py-3 whitespace-nowrap">${log.tanggal}</td>
                            <td class="px-4 py-3 whitespace-nowrap">${log.id_user}</td>
                            <td class="px-4 py-3 whitespace-nowrap">${log.nama_user}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-medium ${
                                    log.status === 'UPDATE_STATUS' ? 'bg-blue-100 text-blue-800' :
                                    log.status === 'BULK_UPDATE_STATUS' ? 'bg-green-100 text-green-800' :
                                    log.status === 'DETAIL' ? 'bg-purple-100 text-purple-800' :
                                    'bg-gray-100 text-gray-800'
                                }">
                                    ${log.status}
                                </span>
                            </td>
                            <td class="px-4 py-3">${log.keterangan}</td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Update log count
                    document.getElementById('logCount').textContent = `Menampilkan ${data.data.length} log aktivitas`;

                    // Re-apply search filter if there's a search term
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput.value.trim()) {
                        filterLogs();
                    }
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>Gagal mengambil data log</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500"><i class="fas fa-exclamation-circle mr-2"></i>Terjadi kesalahan saat mengambil data log</td></tr>';
            });
    }

    function applyDateFilter() {
        fetchLogs();
    }

    function resetFilter() {
        // Reset date filters
        const end = new Date();
        const start = new Date();
        start.setDate(start.getDate() - 7);

        document.getElementById('startDate').value = start.toISOString().split('T')[0];
        document.getElementById('endDate').value = end.toISOString().split('T')[0];

        // Reset search
        document.getElementById('searchInput').value = '';

        // Fetch logs again
        fetchLogs();
    }

    function closeLogModal() {
        document.getElementById('logModal').classList.add('hidden');
        document.getElementById('logModal').classList.remove('flex');
    }

    function logUserActivity(status, keterangan) {
        const userId = document.getElementById('user_id').value;
        const userName = document.getElementById('user_name').value;

        if (!userId || !userName) {
            console.error('User information not found');
            showNotification('Error: User information not found', 'error');
            return;
        }

        console.log('Sending log data:', {
            status,
            keterangan,
            id_user: userId,
            nama_user: userName
        });

        fetch('{{ route('log.activity') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: status,
                keterangan: keterangan,
                id_user: userId,
                nama_user: userName
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Data Sudah Di Ubah');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Log saved successfully:', data);
            } else {
                console.error('Failed to save log:', data.message);
                showNotification('Gagal menyimpan log: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error saving log:', error);
            showNotification('Gagal menyimpan log: ' + error.message, 'error');
        });
    }

    function filterLogs() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#logTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count
        document.getElementById('logCount').textContent =
            `Menampilkan ${visibleCount} dari ${rows.length} log aktivitas`;
    }
</script>

</html>
