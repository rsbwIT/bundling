<div>
    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <label>Tanggal Awal</label>
                    <input type="date" wire:model="tgl1" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" wire:model="tgl2" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Jenis Racik</label>
                    <select wire:model="jenisRacik" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="RACIKAN">RACIKAN</option>
                        <option value="NON RACIKAN">NON RACIKAN</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="resetFilter" class="btn btn-secondary w-100">Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Copy --}}
    <button id="copyButton" class="btn btn-primary mb-2">Copy Table</button>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-body table-responsive">
            <table id="tableToCopy" class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No Antrian</th>
                        <th>Rekam Medik</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Racik / Non Racik</th>
                        <th>Status</th>
                        <th>No Rawat</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listData as $item)
                        <tr class="{{ $item->racik_non_racik == 'RACIKAN' ? 'table-success' : 'table-danger' }}">
                            <td>{{ $item->nomor_antrian }}</td>
                            <td>{{ $item->rekam_medik }}</td>
                            <td>{{ $item->nama_pasien }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->updated_at }}</td>
                            <td>{{ $item->racik_non_racik }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $listData->links() }}
            </div>
        </div>
    </div>

    {{-- Script Copy Table --}}
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const copyBtn = document.getElementById("copyButton");
        if(copyBtn){
            copyBtn.addEventListener("click", function(){
                const table = document.getElementById("tableToCopy");
                const range = document.createRange();
                range.selectNode(table);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
                try{
                    document.execCommand("copy");
                    window.getSelection().removeAllRanges();
                    alert("✅ Tabel berhasil disalin ke clipboard.");
                }catch(err){
                    console.error("Gagal menyalin tabel:", err);
                }
            });
        }
    });
    </script>
</div>
