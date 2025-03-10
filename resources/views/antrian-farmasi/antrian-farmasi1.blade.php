@extends('..layout.layoutDashboard')
@section('title', 'Antrian Farmasi')

@section('konten')
    <div class="row">
        <div class="col-md-12 card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kd Display</th>
                        <th>Nama Display</th>
                        <th class="text-center">Display Farmasi</th>
                        <th class="text-center">Panggilan</th>
                        <th class="text-center">Download autorun</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($antrianFarmasi1 as $item)
                        <tr>
                            <td>{{ $item->kd_display_farmasi }}</td>
                            <td>{{ $item->nama_display_farmasi }}</td>
                            <td class="text-center">
                                <form action="{{ url('display') }}" method="">
                                    @csrf
                                    <input name="kd_display_farmasi" value="{{ $item->kd_display_farmasi }}" hidden>
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
                                        @foreach ($item->getFarmasi as $data)
                                            <form action="{{ url('panggil-farmasi1') }}" method="POST">
                                                @csrf
                                                <div class="dropdown-item">
                                                    <input name="kd_display_farmasi" value="{{ $item->kd_display_farmasi }}"
                                                        hidden>
                                                    <input name="kd_ruang_farmasi" value="{{ $data->kd_ruang_farmasi }}"
                                                        hidden>
                                                    <button class="btn btn-block btn-flat btn-primary">
                                                        {{ $data->nama_ruang_farmasi }}
                                                    </button>
                                                </div>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <form action="{{ url('antrian-farmasi-download') }}" method="">
                                    @csrf
                                    <input name="kd_display_farmasi" value="{{ $item->kd_display_farmasi }}" hidden>
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
