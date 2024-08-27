<div>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr class="h4">
                        <th style="width: 10px">#</th>
                        <th width="60%">Kelas</th>
                        <th width="20" class="text-center">Progress</th>
                        <th width="20" class="text-center">Label</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($getKamar as $key => $item)
                        <tr class="h5">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->kelas }}</td>
                            <td class="text-center">{{ $item->total_status_1 }}</td>
                            <td class="text-center">{{ $item->total_status_0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
