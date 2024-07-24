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
        {{ $year }}
        <div class="card-body table-responsive p-0" style="height: 450px;">
            <div class="row">
                @foreach ($BOR as $nama_kamar => $dataBulanan)
                    <div class="col-md-6">
                        <div id="myPlot-{{ $nama_kamar }}" style="width:100%;max-width:600px"></div>
                    </div>

                    <script>
                        document.addEventListener('livewire:load',
                            function() {
                                // Function to create or update charts
                                function updateCharts(BOR) {
                                    for (const [nama_kamar, dataBulanan] of Object.entries(BOR)) {
                                        const xBulan = [];
                                        const yBor = [];
                                        for (const bulan in dataBulanan) {
                                            xBulan.push(bulan);
                                            yBor.push(parseFloat(dataBulanan[bulan]['bor'].toFixed(2)));
                                        }
                                        const data = [{
                                            x: xBulan,
                                            y: yBor,
                                            type: "bar",
                                            orientation: "v",
                                            marker: {
                                                color: "rgba(0,0,255,0.6)"
                                            },
                                            hoverinfo: 'y'
                                        }];
                                        const layout = {
                                            title: `BOR Perbulan ${nama_kamar}`,
                                            yaxis: {
                                                range: [0, 100],
                                                ticksuffix: '%'
                                            }
                                        };
                                        Plotly.newPlot(`myPlot-${nama_kamar}`, data, layout);
                                    }
                                }

                                function initCharts() {
                                    Livewire.emit('mount'); // Request data from Livewire
                                }
                                Livewire.on('chartDataUpdated', updateCharts);
                                Livewire.on('initialChartData', updateCharts);
                                initCharts();
                            });
                    </script>
                    {{-- <script>
                        document.addEventListener('livewire:load', function() {
                            Livewire.on('chartDataUpdated', (BOR) => {
                                for (const [nama_kamar, dataBulanan] of Object.entries(BOR)) {
                                    const xBulan = [];
                                    const yBor = [];
                                    for (const bulan in dataBulanan) {
                                        xBulan.push(bulan);
                                        yBor.push(parseFloat(dataBulanan[bulan]['bor'].toFixed(2)));
                                    }
                                    const data = [{
                                        x: xBulan,
                                        y: yBor,
                                        type: "bar",
                                        orientation: "v",
                                        marker: {
                                            color: "rgba(0,0,255,0.6)"
                                        },
                                        hoverinfo: 'y'
                                    }];
                                    const layout = {
                                        title: `BOR Perbulan ${nama_kamar}`,
                                        yaxis: {
                                            range: [0, 100],
                                            ticksuffix: '%'
                                        }
                                    };
                                    Plotly.newPlot(`myPlot-${nama_kamar}`, data, layout);
                                }
                            });
                        });
                    </script> --}}
                @endforeach
            </div>
        </div>
    </div>
</div>
