@extends('..layout.layoutDashboard')
@section('title', 'Laporan Lab PK')

@section('konten')
<div class="row pt-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <form action="{{ route('laporan.lab_pk') }}" method="GET" class="form-inline">
                    <label class="mr-2 font-weight-bold">Dari Tanggal (Nota/Bayar) :</label>
                    <input type="date" name="tgl_mulai" class="form-control form-control-sm mr-3" value="{{ $tglMulai }}">
                    
                    <label class="mr-2 font-weight-bold">Sampai Tanggal (Nota/Bayar) :</label>
                    <input type="date" name="tgl_selesai" class="form-control form-control-sm mr-3" value="{{ request('tgl_selesai') ?? $tglSelesai }}">
                    
                    <label class="mr-2 font-weight-bold">Jenis Bayar :</label>
                    <select name="jenis_pasien" class="form-control form-control-sm mr-3">
                        <option value="semua" {{ request('jenis_pasien') == 'semua' ? 'selected' : '' }}>Semua Jenis Bayar</option>
                        <option value="umum" {{ request('jenis_pasien') == 'umum' ? 'selected' : '' }}>Umum (Tunai)</option>
                        <option value="asuransi" {{ request('jenis_pasien') == 'asuransi' ? 'selected' : '' }}>Asuransi / BPJS / Piutang</option>
                    </select>
                    
                    <button type="submit" class="btn btn-sm btn-primary px-3 mr-2">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                    
                    <button type="button" class="btn btn-sm btn-success px-3" onclick="copyTableToExcel('tableLab')">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </form>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm text-sm m-0" id="tableLab" style="white-space: nowrap;">
                        <thead class="bg-info text-white text-center align-middle">
                            <tr>
                                <th>No</th>
                                <th>No Rawat</th>
                                <th>No Nota</th>
                                <th>No RM</th>
                                <th>Nama Pasien</th>
                                <th>Jenis Bayar</th>
                                <th>Tgl Pembayaran</th>
                                <th>Tgl Registrasi</th>
                                <th>Dokter Perujuk</th>
                                <th>Kd Dokter</th>
                                <th>Bagian RS</th>
                                <th>BHP</th>
                                <th>Tarif Perujuk</th>
                                <th>Tarif Dokter</th>
                                <th>Tarif Petugas</th>
                                <th>KSO</th>
                                <th>Menejemen</th>
                                <th>Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $total_biaya = 0; 
                                $t_rs=0; $t_bhp=0; $t_rujuk=0; $t_dr=0; $t_pr=0; $t_kso=0; $t_mnj=0;
                            @endphp
                            @forelse($data as $index => $item)
                                @php 
                                    $total_biaya += $item->biaya; 
                                    $t_rs += $item->bagian_rs;
                                    $t_bhp += $item->bhp;
                                    $t_rujuk += $item->tarif_perujuk;
                                    $t_dr += $item->tarif_tindakan_dokter;
                                    $t_pr += $item->tarif_tindakan_petugas;
                                    $t_kso += $item->kso;
                                    $t_mnj += $item->menejemen;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->no_rawat }}</td>
                                    <td>{{ $item->no_nota ?? '-' }}</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->jenis_bayar }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl_pembayaran)->format('Y-m-d') }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl_registrasi)->format('Y-m-d') }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td>{{ $item->dokter_perujuk }}</td>
                                    <td class="text-right">{{ number_format($item->bagian_rs, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->bhp, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->tarif_perujuk, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->tarif_tindakan_dokter, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->tarif_tindakan_petugas, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->kso, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item->menejemen, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold text-success">{{ number_format($item->biaya, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="18" class="text-center text-muted py-4">Belum ada data pemeriksaan lab.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($data) > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="10" class="text-right font-weight-bold text-uppercase">TOTAL BIAYA:</th>
                                    <th class="text-right font-weight-bold text-dark">{{ number_format($t_rs, 0, ',', '.') }}</th>
                                    <th class="text-right font-weight-bold text-dark">{{ number_format($t_bhp, 0, ',', '.') }}</th>
                                    <th class="text-right font-weight-bold text-dark">{{ number_format($t_rujuk, 0, ',', '.') }}</th>
                                    <th class="text-right font-weight-bold text-dark">{{ number_format($t_dr, 0, ',', '.') }}</th>
                                    <th class="text-right font-weight-bold text-dark">{{ number_format($t_pr, 0, ',', '.') }}</th>
                                    <th class="text-right font-weight-bold text-dark">{{ number_format($t_kso, 0, ',', '.') }}</th>
                                    <th class="text-right font-weight-bold text-dark">{{ number_format($t_mnj, 0, ',', '.') }}</th>
                                    <th class="text-right font-weight-bold text-success">{{ number_format($total_biaya, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyTableToExcel(tableId) {
        var el = document.getElementById(tableId);
        var body = document.body, range, sel;
        if (document.createRange && window.getSelection) {
            range = document.createRange();
            sel = window.getSelection();
            sel.removeAllRanges();
            try {
                range.selectNodeContents(el);
                sel.addRange(range);
            } catch (e) {
                range.selectNode(el);
                sel.addRange(range);
            }
        } else if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
        }
        
        try {
            document.execCommand("copy");
            // Optional: Tambahkan alert atau toast ringan
            alert("Tabel berhasil di-copy! Silakan paste (Ctrl+V) di Excel.");
        } catch (err) {
            alert("Gagal meng-copy tabel.");
        }
        
        // Bersihkan seleksi setelah dicopy
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        }
    }
</script>
@endpush
