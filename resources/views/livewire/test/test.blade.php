<div>
    @push('styles')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush
    <div class="col-md-6">
        <!-- general form elements -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Quick Example</h3>
            </div>
            <form>
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                    </div>
                    {{-- SELECT2 MANUAL --}}
                    <div class="form-group dropdown" x-data="{ open: false }">
                        <label for="exampleInputPassword1">Password</label>
                        <button id="dokterMenu" aria-expanded="true"
                            @click="open = ! open; $nextTick(() => $refs.cariDiagnosa.focus());"
                            class="btn btn-default btn-block dropdown dropdown-hover" data-bs-auto-close="outside">
                            <span class="float-left">{{ $diagnosa }}</span>
                            <span class="float-right">
                                <i class="fas fa-angle-down"></i>
                            </span>
                        </button>
                        <div x-show="open" x-transition>
                            <ul aria-labelledby="dokterMenu" style="width: 100%;background-color: rgb(235, 235, 235);"
                                class="dropdown-menu border-0 shadow p-2 show">
                                <li>
                                    <input type="text" class="form-control form-control-sm" x-ref="cariDiagnosa"
                                        wire:model='cariDiagnosa'>
                                </li>
                                @if ($getDataDiagnosa)
                                    <li wire:loading.remove
                                        style="margin-top: 10px; max-height: 200px; overflow-y: auto; padding: 5px; border: 1px solid #7ab8fb; border-radius: 5px;">
                                        @foreach ($getDataDiagnosa as $diagnosa)
                                            <div @click="open = ! open">
                                                <button class="dropdown-item"
                                                    wire:click='setDiagnosa("{{ $diagnosa->kode }}")'>{{ $diagnosa->nama }}</button>
                                            </div>
                                        @endforeach
                                    </li>
                                @endif
                                <div wire:loading wire:target="cariDiagnosa">
                                    Processing Payment...
                                </div>
                            </ul>
                        </div>
                    </div>
                    {{-- SELECT2 MANUAL --}}
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" wire:click='submitButton'>Submit</button>
                </div>
            </form>
        </div>
    </div>


    {{-- UPDATE DRAG AND DROP TODOLIST --}}
    <div class="card">
        <div class="card-body">
            <div>
                <div class="todo-list">
                    <ul id="sortable-list" class="list-group">
                        @foreach ($getSeting as $item)
                            <li class="list-group-item p-2 m-0" data-id="{{ $item->id }}">
                                <span class="handle">
                                    <i class="fas fa-ellipsis-v"></i>
                                    <i class="fas fa-ellipsis-v"></i>
                                </span>
                                <div class="icheck-primary d-inline ml-2">
                                    @if ($item->status == '1')
                                        <button class="btn btn-outline-primary btn-xs"
                                            wire:click="updateStatus('{{ $item->id }}','0')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-outline-primary btn-xs"
                                            wire:click="updateStatus('{{ $item->id }}','1')">
                                            &nbsp; &nbsp; &nbsp;
                                        </button>
                                    @endif
                                </div>
                                <span class="text ml-2">
                                    {{ $item->urutan }}. {{ $item->nama_berkas }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @push('scripts')
                <script>
                    $(function() {
                        $("#sortable-list").sortable({
                            update: function(event, ui) {
                                var order = $(this).sortable('toArray', {
                                    attribute: 'data-id',
                                });
                                @this.call('updateOrder', order);
                            }
                        });
                        $("#sortable-list").disableSelection();
                    });
                </script>
            @endpush
        </div>
    </div>
</div>
