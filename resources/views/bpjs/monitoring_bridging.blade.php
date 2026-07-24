@extends('layout.layoutDashboard')
@section('title', 'Monitoring Bridging BPJS')

@section('konten')

<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Log Bridging BPJS</h5>

        {{-- FILTER --}}
        <form method="GET" action="{{ url('/bpjs/monitoring-bridging') }}">
            <div class="row mb-3 align-items-end">
                <div class="col-md-2">
                    <label>Tgl Mulai</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="{{ $tanggalMulai }}">
                </div>
                <div class="col-md-2">
                    <label>Tgl Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="{{ $tanggalAkhir }}">
                </div>
                <div class="col-md-2">
                    <label>Layanan</label>
                    <input type="text" name="layanan" class="form-control" placeholder="INA-CBG, MJKN..." value="{{ request('layanan') }}">
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Sukses (200)</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <a href="{{ url('/bpjs/monitoring-bridging') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" style="font-size: 13px;">
                <thead class="text-center bg-light">
                    <tr>
                        <th>No</th>
                        <th>Waktu Request</th>
                        <th>Layanan</th>
                        <th>Method</th>
                        <th>Endpoint</th>
                        <th>Status</th>
                        <th>Durasi (ms)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                    <tr>
                        <td class="text-center">{{ $logs->firstItem() + $index }}</td>
                        <td>{{ $log->waktu_request }}</td>
                        <td class="text-center">{{ $log->layanan }}</td>
                        <td class="text-center">{{ $log->method }}</td>
                        <td><div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->endpoint }}">{{ $log->endpoint }}</div></td>
                        <td class="text-center">
                            @if($log->status_code == 200)
                                <span class="badge bg-success">200 OK</span>
                            @else
                                <span class="badge bg-danger">{{ $log->status_code }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $log->durasi_ms }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info text-white btn-detail" 
                                data-req="{{ $log->request_payload }}" 
                                data-res="{{ $log->response_payload }}">Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Belum ada log bridging.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $logs->appends(request()->input())->links() }}
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Payload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Request Payload</h6>
                <pre id="reqPayload" class="bg-light p-2 rounded" style="max-height: 200px; overflow: auto;"></pre>
                
                <h6 class="mt-3">Response Payload</h6>
                <pre id="resPayload" class="bg-light p-2 rounded" style="max-height: 250px; overflow: auto;"></pre>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailButtons = document.querySelectorAll('.btn-detail');
    const modalDetail = new bootstrap.Modal(document.getElementById('modalDetail'));
    
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            let reqData = this.getAttribute('data-req');
            let resData = this.getAttribute('data-res');
            
            try {
                if (reqData && reqData !== 'null' && reqData !== '') {
                    document.getElementById('reqPayload').textContent = JSON.stringify(JSON.parse(reqData), null, 2);
                } else {
                    document.getElementById('reqPayload').textContent = '-';
                }
            } catch(e) {
                document.getElementById('reqPayload').textContent = reqData;
            }

            try {
                if (resData && resData !== 'null' && resData !== '') {
                    document.getElementById('resPayload').textContent = JSON.stringify(JSON.parse(resData), null, 2);
                } else {
                    document.getElementById('resPayload').textContent = '-';
                }
            } catch(e) {
                document.getElementById('resPayload').textContent = resData;
            }
            
            modalDetail.show();
        });
    });
});
</script>

@endsection
