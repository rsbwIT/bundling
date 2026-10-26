<div>
    <div class="card">
        <div class="card-header bg-primary">
            <span class="text-header">Setting Berkas Bundling</span>
        </div>
        <div class="card-body">
            @if (Session::has('message'))
                <div class="alert alert-{{ Session::get('color') }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-{{ Session::get('icon') }}"></i> {{ Session::get('message') }}!
                </div>
            @endif
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
