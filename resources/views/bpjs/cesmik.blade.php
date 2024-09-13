@extends('..layout.layoutDashboard')
@section('title', 'Casemix Bpjs')

@section('konten')
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                @php
                    $cardColor = 'card-success';
                    $textCard = session('success');
                @endphp
            @else
                @php
                    $cardColor = 'card-primary';
                    $textCard = 'Casemix Bpjs';
                @endphp
            @endif

            <div class="card  {{ $cardColor }}">
                <div class="card-header">
                    <h5 class="card-title">{{ $textCard }}</h5>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">

                            </div>
                        </div>
                        @if (isset($_GET['cariNorawat']))
                            <div class="col-md-8">
                                <div class="form-group">
                                    @php
                                        $printNoRawat = isset($_GET['cariNorawat']) ? $_GET['cariNorawat'] : '';
                                        $cariNoSep = isset($_GET['cariNoSep']) ? $_GET['cariNoSep'] : '';
                                    @endphp
                                    {{-- <a href="{{ url('print-casemix', urlencode($printNoRawat)) }}" rel="noopener"
                                        class="btn btn-success float-right"><i class="fas fa-print"></i>
                                        Simpan PDF</a> --}}
                                    <form action="{{ url('/print-casemix') }}" method="">
                                        @csrf
                                        <input name="cariNorawat" value="{{ $printNoRawat }}" hidden>
                                        <input name="cariNoSep" value="{{ $cariNoSep }}" hidden>
                                        <button type="submit" class="btn btn-success float-right">
                                            <i class="fas fa-save"> Simpan PDF</i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="col-md-8">
                            </div>
                        @endif
                    </div>
                    @if ($jumlahData > 0)
                        {{-- INCLUDE BERKAS ============================================================= --}}
                        @foreach ($settingBundling as $item)
                            @include('bpjs.component.' . $item->nama_berkas)
                        @endforeach
                        {{--  --}}
                    @else
                        <div class="card-body">
                            <div class="card p-4 d-flex justify-content-center align-items-center">

                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <script>
        function copyToClipboard(text) {
            const textarea = document.createElement("textarea");
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
            const copyText = document.getElementById("copyText");
            copyText.style.display = "inline"; // Menampilkan teks
            setTimeout(function() {
                copyText.style.display = "none"; // Menghilangkan teks setelah beberapa detik
            }, 4000);
        }
    </script>

@endsection
