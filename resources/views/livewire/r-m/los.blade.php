<div>
    <div class="card-header">
        <form wire:submit.prevent="Los">
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
                                    wire:loading wire:target="Los"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body table-responsive p-0" style="height: 460px;">
        <div class="row">
            @foreach ($Los as $ruangan => $dataBulanan)
                <div class="col-md-6">
                    <div id="ChartLoss-{{ $ruangan }}" style="width:100%;max-width:100%"></div>
                </div>
                <script>
                    function ChartLos(Los) {
                        for (const [ruangan, dataBulanan] of Object.entries(Los)) {
                            const xBulan = [];
                            const yLos = [];
                            for (const bulan in dataBulanan) {
                                xBulan.push(bulan);
                                yLos.push(parseFloat(dataBulanan[bulan]['los'].toFixed(1)));
                            }
                            const data = [{
                                x: xBulan,
                                y: yLos,
                                type: "bar",
                                orientation: "v",
                                marker: {
                                    color: "rgba(253, 85, 68, 0.91)"
                                },
                                hoverinfo: 'y'
                            }];
                            const layout = {
                                title: `Los Perbulan ${ruangan} `,
                                yaxis: {
                                    title: "Dalam Hari",
                                    range: [0, 10]
                                },
                                xaxis: {
                                    title: "Bulan"
                                },
                            };
                            Plotly.newPlot(`ChartLoss-${ruangan}`, data, layout);
                        }
                    }
                    document.addEventListener('DOMContentLoaded', function() {
                        ChartLos(@json($Los));
                    });
                    document.addEventListener('livewire:load', function() {
                        Livewire.on('updateChartLos', ChartLos);
                    });
                </script>
            @endforeach
        </div>
    </div>
</div>
