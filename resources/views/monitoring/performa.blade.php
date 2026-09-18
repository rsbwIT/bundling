@extends('layout.layoutDashboard')

@section('title', 'Monitoring Performa')

@section('content')
            <div class="card card-danger card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-tachometer-alt text-danger mr-2"></i> Log Halaman Paling Lambat (Top 100)</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center" style="width: 100px;">Metode</th>
                                    <th>URL / Menu</th>
                                    <th class="text-right" style="width: 200px;">Waktu Loading (Detik)</th>
                                    <th class="text-center" style="width: 200px;">Waktu Akses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $index => $log)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="text-center align-middle"><span class="badge {{ $log->method == 'GET' ? 'badge-info' : 'badge-warning' }}">{{ $log->method }}</span></td>
                                    <td class="align-middle" style="word-break: break-all;">{{ $log->url_menu }}</td>
                                    <td class="text-right align-middle font-weight-bold {{ $log->waktu_loading_detik > 5 ? 'text-danger' : 'text-warning' }}">
                                        {{ number_format($log->waktu_loading_detik, 2) }} s
                                    </td>
                                    <td class="text-center align-middle">{{ \Carbon\Carbon::parse($log->waktu_akses)->format('d M Y H:i:s') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada halaman yang terdeteksi lambat (>1 detik)</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
@endsection
