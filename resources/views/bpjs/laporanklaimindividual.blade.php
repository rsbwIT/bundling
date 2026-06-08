```php
@extends('layout.layoutDashboard')
@section('title','Laporan Klaim Individual')

@section('konten')

<style>
.filter-card{
    background:#fff;
    padding:20px;
    border-radius:10px;
    border:1px solid #dee2e6;
    margin-bottom:15px;
}

.table-box{
    background:#fff;
    padding:15px;
    border-radius:10px;
    border:1px solid #dee2e6;
}

.table-custom{
    width:100%;
    border-collapse:collapse;
}

.table-custom th{
    background:#f8f9fa;
    padding:10px;
    border:1px solid #dee2e6;
    font-size:12px;
    text-align:center;
}

.table-custom td{
    padding:8px;
    border:1px solid #dee2e6;
    font-size:12px;
}

.loading{
    text-align:center;
    padding:20px;
}

.text-center{
    text-align:center;
}
</style>

<div class="filter-card">

    <h4 class="text-center mb-3">
        LAPORAN KLAIM INDIVIDUAL INA-CBG
    </h4>

    <form id="formFilter">

        <div class="row">

            <div class="col-md-3">
                <label>Tanggal Awal</label>
                <input
                    type="date"
                    name="tgl1"
                    class="form-control"
                    value="{{ date('Y-m-d') }}">
            </div>

            <div class="col-md-3">
                <label>Tanggal Akhir</label>
                <input
                    type="date"
                    name="tgl2"
                    class="form-control"
                    value="{{ date('Y-m-d') }}">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button
                    type="submit"
                    class="btn btn-primary w-100">
                    Tampilkan
                </button>
            </div>

        </div>

    </form>

</div>

<div class="table-box">

    <div class="mb-2">
        Total Data :
        <strong id="totalData">0</strong>
    </div>

    <div class="table-responsive">

        <table class="table-custom">

            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>No SEP</th>
                    <th>No Kartu</th>
                    <th>Nama Pasien</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Pulang</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody id="result">
                <tr>
                    <td colspan="7" class="text-center">
                        Silakan pilih tanggal lalu klik Tampilkan
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

</div>

<script>
document.getElementById('formFilter').addEventListener('submit', function(e){

    e.preventDefault();

    const tgl1 = document.querySelector('[name=tgl1]').value;
    const tgl2 = document.querySelector('[name=tgl2]').value;

    document.getElementById('result').innerHTML =
        '<tr><td colspan="7" class="loading">Loading...</td></tr>';

    fetch(`{{ route('klaim.data') }}?tgl1=${tgl1}&tgl2=${tgl2}`)

.then(async response => {

    const text = await response.text();

    console.log('RESPONSE:', text);

    try {
        return JSON.parse(text);
    } catch (e) {

        document.getElementById('result').innerHTML =
        `<tr>
            <td colspan="7" class="text-danger">
                Response bukan JSON. Lihat Console (F12).
            </td>
        </tr>`;

        throw new Error(text);
    }

})

.then(res => {

    if(!res.status){

        document.getElementById('result').innerHTML =
        `<tr>
            <td colspan="7" class="text-danger">
                ${res.message ?? 'Gagal mengambil data'}
            </td>
        </tr>`;

        return;
    }

    let data = res.data || [];
    let html = '';

    if(data.length === 0){

        html = `
        <tr>
            <td colspan="7" class="text-center">
                Tidak ada data
            </td>
        </tr>`;

    }else{

        data.forEach((row,index)=>{

            html += `
            <tr>
                <td>${index + 1}</td>
                <td>${row.nomor_sep ?? '-'}</td>
                <td>${row.nomor_kartu ?? '-'}</td>
                <td>${row.nama_pasien ?? '-'}</td>
                <td>${row.tgl_masuk ?? '-'}</td>
                <td>${row.tgl_pulang ?? '-'}</td>
                <td>${row.status ?? '-'}</td>
            </tr>`;
        });
    }

    document.getElementById('result').innerHTML = html;
    document.getElementById('totalData').innerText = data.length;

})

.catch(error => {

    console.error(error);

    document.getElementById('result').innerHTML =
    `<tr>
        <td colspan="7" class="text-danger">
            ${error.message}
        </td>
    </tr>`;
});

    document.getElementById('result').innerHTML =
        `<tr>
            <td colspan="7" class="text-center text-danger">
                ${error}
            </td>
        </tr>`;
});

</script>

@endsection
