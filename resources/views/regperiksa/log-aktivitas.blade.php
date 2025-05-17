<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log Aktivitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body class="bg-white p-4">
    <div class="max-w-full mx-auto">
        <h1 class="text-gray-900 font-semibold text-lg mb-3 flex items-center gap-2">
            <i class="fas fa-history"></i> LOG AKTIVITAS
        </h1>

        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between mb-3 gap-2">
            <div class="flex gap-2">
                <a href="{{ url('/') }}" title="Kembali ke Home"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded flex items-center gap-1 shadow-md">
                    <i class="fas fa-home"></i>
                    Home
                </a>
            </div>

            <form method="GET" action="{{ route('log.aktivitas') }}" class="flex items-center gap-2">
                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                    class="text-xs px-2 py-1 border rounded">
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                    class="text-xs px-2 py-1 border rounded">
                <button type="submit" class="bg-blue-500 text-white text-xs px-3 py-1 rounded">
                    <i class="fas fa-search"></i> Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-xs text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">No</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Tanggal & Waktu</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Username</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Nama Petugas</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Status</th>
                        <th class="border border-gray-300 px-2 py-1 font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $index => $log)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-2 py-1">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y H:i:s') }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $log->username }}</td>
                            <td class="border border-gray-300 px-2 py-1">{{ $log->nama_user }}</td>
                            <td class="border border-gray-300 px-2 py-1">
                                @if($log->status == 'UPDATE_STATUS')
                                    <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full text-[10px] font-medium">
                                        <i class="fas fa-edit mr-1"></i>Update
                                    </span>
                                @elseif($log->status == 'SEARCH')
                                    <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full text-[10px] font-medium">
                                        <i class="fas fa-search mr-1"></i>Search
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[10px] font-medium">
                                        {{ $log->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-2 py-1">
                                @if($log->status == 'UPDATE_STATUS')
                                    {!! str_replace(['dari', 'menjadi'], ['<span class="text-red-500">dari</span>', '<span class="text-green-500">menjadi</span>'], $log->keterangan) !!}
                                @else
                                    {{ $log->keterangan }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-gray-300 text-center py-3">Tidak ada data log</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <footer class="fixed bottom-0 left-0 right-0 bg-blue-800 text-white text-sm py-2 px-4 flex justify-between items-center">
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
</html>
