<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent="Bto">
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
                                        wire:loading wire:target="Bto"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body table-responsive p-0" style="height: 460px;">
            <div class="row">
                @foreach ($Bto as $nama_kamar => $dataBulanan)
                    <div class="col-md-6">
                        <div id="ChartBto-{{ $nama_kamar }}" style="width:100%;max-width:100%"></div>
                    </div>
                    <script>
                        function ChartBto(Bto) {
                            for (const [nama_kamar, dataBulanan] of Object.entries(Bto)) {
                                const xBulan = [];
                                const yBto = [];
                                for (const bulan in dataBulanan) {
                                    xBulan.push(bulan);
                                    yBto.push(parseFloat(dataBulanan[bulan]['bto'].toFixed(2)));
                                }
                                const data = [{
                                    x: xBulan,
                                    y: yBto,
                                    type: "bar",
                                    orientation: "v",
                                    marker: {
                                        color: "rgb(77, 255, 11)"
                                    },
                                    hoverinfo: 'y'
                                }];
                                const layout = {
                                    title: `Bto Perbulan ${nama_kamar} `,
                                    yaxis: {
                                        title: "Dalam Hari",
                                        range: [0, 10]
                                    },
                                    xaxis: {
                                        title: "Bulan"
                                    },
                                };
                                Plotly.newPlot(`ChartBto-${nama_kamar}`, data, layout);
                            }
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            ChartBto(@json($Bto));
                        });
                        document.addEventListener('livewire:load', function() {
                            Livewire.on('chartDataBtoUpdated', ChartBto);
                        });
                    </script>
                @endforeach
            </div>
        </div>
    </div>
</div>
