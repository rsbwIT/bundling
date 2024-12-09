@extends('..layout.layoutDashboard')
@section('title', 'Antrian Poli')

@section('konten')
    <div class="row">
        <div class="col-md-12 card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kd Display</th>
                        <th>Nama Display</th>
                        <th class="text-center">Display Poli</th>
                        <th class="text-center">Panggilan</th>
                        <th class="text-center">Download autorun</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($antrianPoli as $item)
                        <tr>
                            <td>{{ $item->kd_display }}</td>
                            <td>{{ $item->nama_display }}</td>
                            <td class="text-center">
                                <form action="{{ url('display') }}" method="">
                                    @csrf
                                    <input name="kd_display" value="{{ $item->kd_display }}" hidden>
                                    <button class="" style="background: none; border: none;">
                                        <i class="nav-icon fas fa-search"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="badge-group-sm">
                                    <a data-toggle="dropdown" href="#">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                                        @foreach ($item->getPoli as $data)
                                            <form action="{{ url('panggil-poli') }}" method="">
                                                <div class="dropdown-item">
                                                    @csrf
                                                    <input name="kd_display" value="{{ $item->kd_display }}" hidden>
                                                    <input name="kd_ruang_poli" value="{{ $data->kd_ruang_poli }}" hidden>
                                                    <button class="btn btn-block btn-flat btn-primary">
                                                        {{ $data->nama_ruang_poli }}
                                                    </button>
                                                </div>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <form action="{{ url('antrian-poli-download') }}" method="">
                                    @csrf
                                    <input name="kd_display" value="{{ $item->kd_display }}" hidden>
                                    <button class="" style="background: none; border: none;">
                                        <i class="nav-icon fas fa-download"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
