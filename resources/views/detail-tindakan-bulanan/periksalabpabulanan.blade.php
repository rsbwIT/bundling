@extends('..layout.layoutDashboard')

@section('title', 'Periksa Lab PA Bulanan')

@section('konten')

<div class="card">
    <div class="card-body">
        {{-- FILTER COMPONENT --}}
        @include('detail-tindakan-bulanan.component.cari-labpa-bulanan')
        Jumlah Data : {{ count($data) }}
        <div class="row no-print">
            <div class="col-12">
                <button type="button"
                    class="btn btn-default float-right"
                    id="copyButton">
                    <i class="fas fa-copy"></i>
                    Copy table
                </button>

            </div>


            <table class="table table-sm table-bordered table-striped table-responsive text-xs"
                style="white-space: nowrap;"
                id="tableToCopy">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No.Rawat</th>
                        <th>No.RM</th>
                        <th>Nama Pasien</th>
                        <th>Status</th>
                        <th>Kd.Pemeriksaan</th>
                        <th>Pemeriksaan</th>
                        <th>Kd Dokter Lab</th>
                        <th>Dokter Lab</th>
                        <th>Kd Perujuk</th>
                        <th>Dokter Perujuk</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Cara Bayar</th>
                        <th>Jasa Sarana</th>
                        <th>Paket BHP</th>
                        <th>JM Dokter</th>
                        <th>JM Petugas</th>
                        <th>JM Perujuk</th>
                        <th>KSO</th>
                        <th>Manajemen</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data as $key => $item)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->no_rkm_medis }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->status_lunas }}</td>
                        <td>{{ $item->kd_jenis_prw }}</td>
                        <td>{{ $item->nm_perawatan }}</td>
                        <td>{{ $item->kd_dokter_lab }}</td>
                        <td>{{ $item->nm_dokter_lab }}</td>
                        <td>{{ $item->kd_dokter_perujuk }}</td>
                        <td>{{ $item->nm_dokter_perujuk }}</td>
                        <td>{{ $item->tgl_periksa }}</td>
                        <td>{{ $item->jam }}</td>
                        <td>{{ $item->png_jawab }}</td>
                        <td>{{ round($item->bagian_rs) }}</td>
                        <td>{{ round($item->bhp) }}</td>
                        <td>{{ round($item->tarif_tindakan_dokter) }}</td>
                        <td>{{ round($item->tarif_tindakan_petugas) }}</td>
                        <td>{{ round($item->tarif_perujuk) }}</td>
                        <td>{{ round($item->kso) }}</td>
                        <td>{{ round($item->menejemen) }}</td>
                        <td><b>{{ round($item->biaya) }}</b></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>

document.getElementById("copyButton")
.addEventListener("click", function(){
    copyTableToClipboard("tableToCopy");
});


function copyTableToClipboard(tableId)
{
    const table = document.getElementById(tableId);
    const range = document.createRange();
    range.selectNode(table);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    try {
        document.execCommand("copy");
        window.getSelection().removeAllRanges();
        alert("Tabel berhasil disalin");
    }
    catch(err){
        console.error(err);
    }
}

</script>
@endsection