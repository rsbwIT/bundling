@extends('..layout.layoutDashboard')
@section('title', 'Panggil Farmasi')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="card">
        <div class="card-body">
            <div class="d-flex item-center mt-4">
                <button class="p-3 mr-2" style="background-color: #30E3DF"></button>
                <h4 class="mr-4"> : Ada</h4>
                <button class="p-3 mr-2" style="background-color: #F97B22"></button>
                <h4 class="mr-4"> : Tidak Ada</h4>
                <button class="p-3 mr-2" style="background-color: #ffffff"></button>
                <h4> : Belum Terpanggil</h4>
            </div>
        </div>
        <div class="card-body">
            @livewire('antrian-farmasi.panggilfarmasifix')
        </div>
    </div>
    <script>
        function playSequentialSounds(ids) {
            var currentIndex = 0;

            function playNextSound() {
                if (currentIndex >= ids.length) {
                    return;
                }
                var audio = document.getElementById(ids[currentIndex]);
                audio.play();
                currentIndex++;
                audio.onended = playNextSound;
            }
            playNextSound();
        }
    </script>
@endsection
@push('scripts')
    @livewireScripts
@endpush
