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
                        {{-- BERKAS INACBG --}}
                        @if ($getInacbg)
                            <div class="card-body">
                                <div class="card py-3 d-flex justify-content-center align-items-center" id="pdf-container-inacbg">
                                    <!-- PDF rendered as canvas -->
                                </div>
                            </div>
                            
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
                                    
                                    var urlLocal = "{{ asset('storage/file_scan/' . basename($getInacbg->lokasi_file)) }}";
                                    var urlWebapps = "/webapps/berkasrawat/{{ $getInacbg->lokasi_file }}";
                                    var container = document.getElementById('pdf-container-inacbg');
                                    
                                    function renderPdf(pdf) {
                                        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                                            pdf.getPage(pageNum).then(function(page) {
                                                var scale = 2.0; // Render at high res
                                                var viewport = page.getViewport({scale: scale});
                                                
                                                var canvas = document.createElement('canvas');
                                                var context = canvas.getContext('2d');
                                                canvas.height = viewport.height;
                                                canvas.width = viewport.width;
                                                
                                                var renderContext = {
                                                    canvasContext: context,
                                                    viewport: viewport
                                                };
                                                page.render(renderContext).promise.then(function() {
                                                    var img = document.createElement('img');
                                                    img.src = canvas.toDataURL('image/png');
                                                    img.style.width = '100%';
                                                    img.style.maxWidth = '1000px';
                                                    img.style.display = 'block';
                                                    img.style.margin = '0 auto';
                                                    container.appendChild(img);
                                                });
                                            });
                                        }
                                    }

                                    pdfjsLib.getDocument(urlLocal).promise.then(function(pdf) {
                                        renderPdf(pdf);
                                    }).catch(function(errorLocal) {
                                        console.warn('Local PDF not found, trying webapps path...', errorLocal);
                                        pdfjsLib.getDocument(urlWebapps).promise.then(function(pdf) {
                                            renderPdf(pdf);
                                        }).catch(function(errorWebapps) {
                                            console.error('Error loading INACBG PDF from both sources:', errorWebapps);
                                            container.innerHTML = '<div class="p-4 text-danger">Gagal memuat preview PDF.</div>';
                                        });
                                    });
                                });
                            </script>
                        @endif

                        {{-- INCLUDE BERKAS ============================================================= --}}
                        @foreach ($settingBundling as $item)
                            @if(view()->exists('bpjs.component.' . $item->nama_berkas))
                                @include('bpjs.component.' . $item->nama_berkas)
                            @endif
                        @endforeach
                        
                        {{-- BERKAS DIGITAL LAINNYA (DINAMIS) --}}
                        @if(isset($semuaBerkasDigital) && count($semuaBerkasDigital) > 0)
                            @foreach ($semuaBerkasDigital as $berkas)
                                @include('bpjs.component.berkas-digital-dinamis', ['berkas' => $berkas])
                            @endforeach
                        @endif
                        {{--  --}}
                        
                        {{-- BERKAS FISIOTERAPI (PALING BAWAH) --}}
                        @if (isset($getFisioData) && $getFisioData !== null)
                            <div class="card-body">
                                <div class="card py-3 p-4" style="background-color: #f4f6f9; border: none;">
                                    <style>
                                        .a4-container {
                                            max-width: 210mm;
                                            min-height: 297mm;
                                            padding: 15mm;
                                            margin: 0 auto;
                                            background: white;
                                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                            border: 1px solid #ddd;
                                            box-sizing: border-box;
                                            font-family: Arial, sans-serif;
                                            font-size: 14px;
                                        }
                                    </style>
                                    <div class="a4-container">
                                        @include('bpjs.component.print.berkas-fisioterapi')
                                    </div>
                                </div>
                            </div>
                        @endif
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
