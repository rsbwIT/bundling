@extends('..layout.layoutDashboard')
@section('title', 'Antrian Farmasi')

@section('konten')
    <div class="row">
        <div class="col-md-12 card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Display</th>
                        <th>Nama Display</th>
                        <th class="text-center">Display Farmasi</th>
                        <th class="text-center">Panggilan</th>
                        {{-- <th class="text-center">Download autorun</th> --}}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Racik/Non Racik</td>
                        <td>Display Farmasi</td>
                        <td class="text-center">
                            <a href="{{ route('antrian-farmasi.display') }}" class="btn btn-sm btn-info"
                                title="Lihat Display Farmasi">
                                <i class="nav-icon fas fa-search"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('antrian-farmasi.panggil') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                        {{-- <td class="text-center">
                            <form action="{{ route('antrian-farmasi.display') }}" method="">
                                <button class="" style="background: none; border: none;">
                                    <i class="nav-icon fas fa-download"></i>
                                </button>
                            </form>
                        </td> --}}
                    </tr>
                    {{-- @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection
