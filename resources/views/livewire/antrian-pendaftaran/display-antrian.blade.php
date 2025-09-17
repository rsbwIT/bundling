<div style="background: linear-gradient(180deg, #f4f9ff, #eaf7f0); min-height: 100vh;">
    <div class="mt-4 container-fluid">
        <div class="row justify-content-center g-4" wire:poll.1000ms>
            @php
                $md = count($getLoket) > 2 ? 4 : 6;
            @endphp
            @if ($getLoket)
                @foreach ($getLoket as $item)
                    <div class="col-md-{{ $md }}">
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 animate__animated animate__fadeInUp"
                             style="transition: all 0.3s ease;"
                             onmouseover="this.style.transform='translateY(-5px) scale(1.02)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)';">

                            {{-- Header Loket --}}
                            <div class="card-header text-center text-white py-3"
                                 style="background: linear-gradient(135deg, #007bff, #20c997); border: none;">
                                <h2 class="fw-bold mb-0">{{ $item->nama_loket }}</h2>
                            </div>

                            {{-- Isi --}}
                            <table class="table table-borderless mb-0">
                                @if ($item->getPasien->isEmpty())
                                    <div class="d-flex justify-content-center align-items-center flex-column p-5" style="min-height: 300px;">
                                        <i class="bi bi-person-x display-1 text-muted mb-3"></i>
                                        <h3 class="fw-bold text-muted">Tidak Ada Antrian</h3>
                                    </div>
                                @else
                                    @foreach ($item->getPasien as $item)
                                        <thead>
                                            <tr>
                                                <th colspan="3" class="text-center">
                                                    <h4 class="fw-bold text-secondary">Nomor Registrasi</h4>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-center">
                                                    <span class="fw-bolder text-primary"
                                                          style="font-size: 5rem; letter-spacing: 2px; text-shadow: 2px 2px 6px rgba(0,0,0,0.15);">
                                                        {{ $item->no_reg }}
                                                    </span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" class="text-center py-3">
                                                    <i class="bi bi-person-circle text-success me-2 fs-2"></i>
                                                    <span class="fw-bold text-dark"
                                                          style="font-size: 2rem; letter-spacing: 1px;">
                                                        {{ $item->nm_pasien }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-center py-2">
                                                    <span class="badge bg-gradient px-4 py-2 fs-5 shadow-sm"
                                                          style="background: linear-gradient(135deg, #17a2b8, #20c997); font-size:1.2rem;">
                                                        <i class="bi bi-stethoscope me-2"></i>{{ $item->nama_dokter }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center py-3">
                                                    <h4 class="fw-bold text-success mb-0">
                                                        <i class="bi bi-clock-history me-2"></i>
                                                        Jam Mulai : {{ date('H:i', strtotime($item->jam_mulai)) }}
                                                    </h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="height:300px;" class="d-flex align-items-center justify-content-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="spinner-border text-danger" role="status" aria-hidden="true"></div>
                        <h5 class="fw-bold text-danger m-0">Koneksi terputus...</h5>
                    </div>
                </div>
            @endif
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
    </div>
</div>
