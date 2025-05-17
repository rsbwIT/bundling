@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Log Aktivitas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active">Log Aktivitas</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Daftar Log Aktivitas</h3>
                        <form action="{{ route('log.aktivitas') }}" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="date" name="tanggal_awal" class="form-control form-control-sm"
                                    value="{{ request('tanggal_awal') }}">
                                <input type="date" name="tanggal_akhir" class="form-control form-control-sm ml-2"
                                    value="{{ request('tanggal_akhir') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal & Waktu</th>
                                <th>Username</th>
                                <th>Nama Petugas</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $index => $log)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $log->username }}</td>
                                    <td>{{ $log->nama_user }}</td>
                                    <td>
                                        @if($log->status == 'UPDATE_STATUS')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-edit"></i> Update
                                            </span>
                                        @elseif($log->status == 'SEARCH')
                                            <span class="badge badge-info">
                                                <i class="fas fa-search"></i> Search
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ $log->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->status == 'UPDATE_STATUS')
                                            {!! preg_replace(
                                                ['/dari/', '/menjadi/'],
                                                ['<span class="text-danger">dari</span>', '<span class="text-success">menjadi</span>'],
                                                e($log->keterangan)
                                            ) !!}
                                        @else
                                            {{ $log->keterangan }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data log</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="card-footer clearfix">
                        <div class="float-right">
                            {{ $logs->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .badge {
        padding: 0.4em 0.6em;
        font-size: 85%;
    }
    .pagination {
        margin: 0;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush
