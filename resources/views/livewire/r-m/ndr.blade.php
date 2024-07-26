<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="Ndr">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="input-group">
                            <select class="form-control form-control-sidebar form-control-sm" wire:model.defer="year">
                                <option value="2021">2021</option>
                                <option value="2022">2022</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-sidebar btn-primary btn-sm" wire:click="render()">
                                    <i class="fas fa-search fa-fw"></i>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                        wire:loading wire:target="Ndr"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 460px;">
            <div class="row">
                @foreach ($Ndr as $nama_kamar => $dataBulanan)
                    <div class="col-md-6">
                        <div id="ChartNdr-{{ $nama_kamar }}" style="width:100%;max-width:100%"></div>
                    </div>

                    <script>
                        function ChartNdr(Ndr) {
                            for (const [nama_kamar, dataBulanan] of Object.entries(Ndr)) {
                                const xBulan = [];
                                const yNdr = [];
                                for (const bulan in dataBulanan) {
                                    xBulan.push(bulan);
                                    yNdr.push(parseFloat(dataBulanan[bulan]['ndr'].toFixed(2)));
                                }
                                const data = [{
                                    x: xBulan,
                                    y: yNdr,
                                    type: "bar",
                                    orientation: "v",
                                    marker: {
                                        color: "rgb(11, 232, 255)"
                                    },
                                    hoverinfo: 'y'
                                }];
                                const layout = {
                                    title: `Ndr Perbulan ${nama_kamar} `,
                                    yaxis: {
                                        title: "Dalam Hari",
                                        range: [0, 100]
                                    },
                                    xaxis: {
                                        title: "Bulan"
                                    },
                                };
                                Plotly.newPlot(`ChartNdr-${nama_kamar}`, data, layout);
                            }
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            ChartNdr(@json($Ndr));
                        });
                        document.addEventListener('livewire:load', function() {
                            Livewire.on('chartDataNdrUpdated', ChartNdr);
                        });
                    </script>
                @endforeach
            </div>
        </div>
    </div>
</div>
