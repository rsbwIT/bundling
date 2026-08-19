@extends('layout.layoutDashboard')

@section('title', 'Pelayanan Gizi & Diet Pasien')

@section('konten')
<div class="card card-primary card-outline shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="card-title font-weight-bold mb-0 text-dark">Filter Data Pemberian Diet</h5>
    </div>
    <div class="card-body py-3">
        <form method="GET" action="{{ url('/gizi') }}">
            <div class="form-row align-items-end">
                <div class="form-group col-md-2 col-sm-6 mb-2">
                    <label for="tgl1" class="small text-muted font-weight-bold mb-1">Dari Tanggal</label>
                    <input type="date" class="form-control form-control-sm" id="tgl1" name="tgl1" value="{{ $tgl1 }}">
                </div>
                <div class="form-group col-md-2 col-sm-6 mb-2">
                    <label for="tgl2" class="small text-muted font-weight-bold mb-1">Sampai Tanggal</label>
                    <input type="date" class="form-control form-control-sm" id="tgl2" name="tgl2" value="{{ $tgl2 }}">
                </div>
                <div class="form-group col-md-2 col-sm-6 mb-2">
                    <label for="waktu" class="small text-muted font-weight-bold mb-1">Shift / Waktu Makan</label>
                    <select class="form-control form-control-sm" id="waktu" name="waktu">
                        <option value="">-- Semua Shift --</option>
                        @if(isset($listWaktu))
                            @foreach($listWaktu as $w)
                                <option value="{{ $w }}" {{ $selectedWaktu == $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group col-md-3 col-sm-6 mb-2">
                    <label for="kd_diet" class="small text-muted font-weight-bold mb-1">Jenis Diet</label>
                    <select class="form-control form-control-sm" id="kd_diet" name="kd_diet">
                        <option value="">-- Semua Jenis Diet --</option>
                        @if(isset($listDiet))
                            @foreach($listDiet as $d)
                                <option value="{{ $d->kd_diet }}" {{ $selectedKdDiet == $d->kd_diet ? 'selected' : '' }}>{{ $d->nama_diet }} ({{ $d->kd_diet }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group col-md-3 col-sm-12 mb-2">
                    <label for="search" class="small text-muted font-weight-bold mb-1">Pencarian Pasien</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="Cari No. Rawat / Nama Pasien..." value="{{ $search }}">
                </div>
            </div>
            <div class="form-row justify-content-end">
                <div class="form-group col-md-3 col-sm-6 mb-0 d-flex">
                    <button type="submit" class="btn btn-primary btn-sm mr-1 flex-fill font-weight-bold">Filter</button>
                    <a href="{{ url('/gizi') }}" class="btn btn-secondary btn-sm font-weight-bold">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
        <h5 class="card-title font-weight-bold mb-0">Daftar Pemberian Diet Pasien</h5>
        <div>
            @if(isset($dataGizi) && method_exists($dataGizi, 'total'))
                <span class="badge badge-light text-dark mr-2">Total: {{ $dataGizi->total() }} Data</span>
            @endif
            <a href="{{ url('/gizi/print-label?all=1&tgl1=' . urlencode($tgl1) . '&tgl2=' . urlencode($tgl2) . '&waktu=' . urlencode($selectedWaktu) . '&kd_diet=' . urlencode($selectedKdDiet) . '&search=' . urlencode($search)) }}" 
               target="_blank" 
               class="btn btn-warning btn-sm font-weight-bold text-dark">
                Cetak Semua Label
            </a>
        </div>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-bordered table-striped table-hover mb-0 text-sm">
            <thead class="bg-secondary text-white">
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th style="width: 170px;">No. Rawat</th>
                    <th style="width: 120px;">No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Ruangan / Kamar</th>
                    <th style="width: 160px;">Tanggal & Shift</th>
                    <th>Jenis Diet</th>
                    <th style="width: 110px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($dataGizi) && count($dataGizi) > 0)
                    @foreach($dataGizi as $index => $item)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">{{ $dataGizi->firstItem() + $index }}</td>
                            <td><span class="badge badge-info">{{ $item->no_rawat }}</span></td>
                            <td><span class="badge badge-light border">{{ $item->no_rkm_medis }}</span></td>
                            <td><strong>{{ $item->nm_pasien }}</strong></td>
                            <td>{{ $item->nm_bangsal ?? '-' }}</td>
                            <td>
                                <div>{{ date('d-m-Y', strtotime($item->tgl_diberi)) }}</div>
                                @if(!empty($item->jam))
                                    <span class="badge badge-warning text-dark">{{ $item->jam }}</span>
                                @endif
                            </td>
                            <td><span class="badge badge-success">{{ $item->nama_diet ?? '-' }}</span></td>
                            <td class="text-center">
                                <a href="{{ url('/gizi/print-label?no_rawat=' . urlencode($item->no_rawat) . '&tgl=' . urlencode($item->tgl_diberi) . '&waktu=' . urlencode($item->jam)) }}" 
                                   target="_blank" 
                                   class="btn btn-dark btn-xs font-weight-bold">
                                   Cetak Label
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <strong>Data Tidak Ditemukan</strong><br>
                            <small>Belum ada data pemberian diet gizi sesuai filter yang dipilih.</small>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if(isset($dataGizi) && method_exists($dataGizi, 'links') && $dataGizi->hasPages())
        <div class="card-footer py-2 bg-light">
            {{ $dataGizi->appends(['tgl1' => $tgl1, 'tgl2' => $tgl2, 'search' => $search, 'kd_diet' => $selectedKdDiet, 'waktu' => $selectedWaktu])->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
