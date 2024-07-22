@extends('..layout.layoutDashboard')
@section('title', 'Farmasi')

@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Berkas Khanza</h3>
                </div>
                <div class="card-body">
                    @if ($jumlahData > 0)
                        {{-- BERKAS SEP ============================================================= --}}
                        @if ($getSEP)
                            @include('farmasi.component.berkas-sep')
                            @include('farmasi.component.berkas-resep2')
                        @else
                            {{-- NULL --}}
                        @endif
                        {{-- ERROR HANDLING ============================================================= --}}
                    @else
                        <div class="card-body">
                            <div class="card p-4 d-flex justify-content-center align-items-center">

                            </div>
                        </div>
                    @endif
                    <div class="card-footer">
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
