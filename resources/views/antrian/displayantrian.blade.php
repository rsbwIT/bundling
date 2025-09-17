@extends('layout.layoutpendaftaran')
@section('title', 'PENDAFTARAN 1')
@push('styles')
    @livewireStyles
    <style>
        .header-antrian {
            background: #fff;
            border-bottom: 2px solid #20c997; /* aksen hijau tipis elegan */
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); /* shadow lembut */
        }
        .header-antrian img {
            object-fit: contain;
        }
        .header-antrian h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('konten')
    <div class="container-fluid mt-3">
        <div class="d-flex justify-content-between align-items-center header-antrian">
            {{-- Logo kiri --}}
            <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                 alt="Logo Rumah Sakit" width="90" height="90" class="rounded">

            {{-- Judul --}}
            <div class="text-center flex-grow-1">
                <h1>Antrian Sidik Jari BPJS</h1>
            </div>

            {{-- Logo kanan --}}
            <img src="/img/bpjs.png" width="200" height="45" alt="BPJS">
        </div>

        {{-- Konten Antrian --}}
        <div class="mt-4">
            @livewire('antrian-pendaftaran.display-antrian')
        </div>
    </div>
@endsection

@push('scripts')
    @livewireScripts
@endpush
