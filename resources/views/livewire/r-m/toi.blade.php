<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="Bor">
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
                                        wire:loading wire:target="Bor"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 460px;">
            <div class="row">
                @foreach ($Toi as $nama_kamar => $dataBulanan)
                    <div class="col-md-6">
                        <div id="ChartToi-{{ $nama_kamar }}" style="width:100%;max-width:100%"></div>
                    </div>

                    <script>
                        function ChartToi(Toi) {
                            for (const [nama_kamar, dataBulanan] of Object.entries(Toi)) {
                                const xBulan = [];
                                const yToi = [];
                                for (const bulan in dataBulanan) {
                                    xBulan.push(bulan);
                                    yToi.push(parseFloat(dataBulanan[bulan]['toi'].toFixed(2)));
                                }
                                const data = [{
                                    x: xBulan,
                                    y: yToi,
                                    type: "bar",
                                    orientation: "v",
                                    marker: {
                                        color: "rgba(0,0,255,0.6)"
                                    },
                                    hoverinfo: 'y'
                                }];
                                const layout = {
                                    title: `Los Perbulan ${nama_kamar} `,
                                    yaxis: {
                                        title: "Dalam Hari",
                                        range: [0, 5]
                                    },
                                    xaxis: {
                                        title: "Bulan"
                                    },
                                };
                                Plotly.newPlot(`ChartToi-${nama_kamar}`, data, layout);
                            }
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            ChartToi(@json($Toi));
                        });
                        document.addEventListener('livewire:load', function() {
                            Livewire.on('chartDataUpdated', ChartToi);
                        });
                    </script>
                @endforeach
            </div>
        </div>
    </div>
</div>
