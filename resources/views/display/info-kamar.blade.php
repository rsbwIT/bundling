@extends('..layout.layoutpendaftaran')
@section('title', 'INFO KAMAR')
@push('styles')
    @livewireStyles
    <style>
        #clock {
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 45px;
            text-align: center;
            color: rgb(44, 44, 43);
        }
    </style>
@endpush
@section('konten')
    <div class="d-flex justify-content-between align-items-center container-fluid mt-2">
        <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" alt="Girl in a jacket" width="120"
            height="120">
        <div class="pricing-header ">
            <h1 class="display-4 font-weight-bold">Informasi Kamar</h1>
            <div class="row justify-content-center">
        <div class="d-flex item-center mt-1">
            <button class="mr-2 btn text-white"
                style="background-color: rgb(0, 26, 109); width: 100px;"><b>Terisi</b></button>
            <button class="mr-2 btn"
                style="background-color: rgb(255, 255, 255); border: 1px solid;width: 100px"><b>Kosong</b></button>
        </div>
    </div>
        </div>
        <div class="pricing-header">
            <div id="clock"></div>
        </div>
    </div>
    <hr style="border: 1px solid">
    @livewire('info-kamar.info-kamar')
@endsection
@push('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const timeString = `${hours}:${minutes}:${seconds}`;
            document.getElementById('clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
    </script>
    @livewireScripts
@endpush
