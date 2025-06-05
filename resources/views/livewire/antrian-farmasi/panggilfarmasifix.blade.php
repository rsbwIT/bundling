<div>
    <div class="card mb-4 box-shadow">
        <table class="table table-sm table-bordered table-hover text-sm mb-3">
            <thead>
                <tr>
                    <th scope="col">No. Antrian</th>
                    <th scope="col">No. Rekam Medis</th>
                    <th scope="col">Nama</th>
                    <th scope="col">No. Rawat</th>
                    <th scope="col">Racik / Non Racik</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Tombol Pilih</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($antrians as $antrian)
                    @php
                        $rowClass =
                            $antrian->status === 'selesai'
                                ? 'table-secondary'
                                : match ($antrian->status) {
                                    'dipanggil' => 'row-dipanggil',
                                    'tidak ada' => 'row-tidak-ada',
                                    default => 'row-menunggu',
                                };

                        $disableTombolUtama = $antrian->status === 'selesai';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $antrian->nomor_antrian ?? '-' }}</td>
                        <td>{{ $antrian->rekam_medik ?? '-' }}</td>
                        <td>{{ $antrian->nama_pasien ?? '-' }}</td>
                        <td>{{ $antrian->no_rawat ?? '-' }}</td>
                        <td>{{ $antrian->racik_non_racik ?? '-' }}</td>
                        <td>{{ $antrian->status ?? '-' }}</td>
                        <td class="text-center">
                            @if (!empty($antrian->no_rawat))
                                <button wire:click="panggil('{{ $antrian->no_rawat }}')"
                                    class="btn btn-sm btn-primary me-1 mb-1"
                                    {{ $disableTombolUtama ? 'disabled' : '' }}
                                    onclick="playAudio()">
                                    <i class="fas fa-bullhorn"></i> Panggil
                                </button>
                                <button wire:click="markAda('{{ $antrian->no_rawat }}')"
                                    class="btn btn-sm btn-success me-1 mb-1"
                                    {{ $disableTombolUtama ? 'disabled' : '' }}>
                                    <i class="fas fa-check"></i> Ada
                                </button>
                                <button wire:click="markTidakAda('{{ $antrian->no_rawat }}')"
                                    class="btn btn-sm btn-danger me-1 mb-1"
                                    {{ $disableTombolUtama ? 'disabled' : '' }}>
                                    <i class="fas fa-times"></i> Tidak Ada
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada antrian farmasi saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('panggilDitekan', no_rawat => {
                console.log('Event panggilDitekan diterima untuk:', no_rawat);
                playAudio(); // Call playAudio when the event is triggered
            });
        });
    </script>
</div>