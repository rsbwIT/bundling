@extends('..layout.layoutDashboard')
@section('title', 'COB Bayar Piutang')

@section('konten')
    <table class="table table-sm table-bordered table-responsive text-xs ">
        <thead>
            <th>No</th>
            <th>Tgl.Bayar</th>
            <th>No.RM</th>
            <th>No. Rawat</th>
            <th>Nama Pasien</th>
            <th>status_lanjut</th>
            <th>No. Nota</th>
        </thead>
        <tbody>
            @foreach ($getCob as $key => $item)
                @php
                    $rowspan = count($item->getDetailCob) + 1;
                @endphp
                <tr>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $key + 1 }}</td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->tgl_bayar }}</td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->no_rkm_medis }}</td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->no_rawat }}</td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->nm_pasien }}</td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->status_lanjut }}</td>
                    <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                        @foreach ($item->getNomorNota as $detail)
                        {{ str_replace(':', '', $detail->nm_perawatan) }}
                    @endforeach
                    </td>
                </tr>
                @foreach ($item->getDetailCob as $cob)
                    <tr>
                        <td class="m-0 p-0">{{ $cob->png_jawab }}</td>
                        <td class="m-0 p-0">Value Lain dari cob</td>
                    </tr>
                @endforeach
            @endforeach

        </tbody>
    </table>
@endsection
