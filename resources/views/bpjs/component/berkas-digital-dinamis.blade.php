{{-- BERKAS DIGITAL DINAMIS --}}
@if (isset($berkas) && $berkas)
    @php
        $fileExt = strtolower(pathinfo($berkas->lokasi_file, PATHINFO_EXTENSION));
        $urlWebapps = env('URL_KHANZA') . "/webapps/berkasrawat/" . $berkas->lokasi_file;
        $base64Pdf = '';
        
        if (!in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            try {
                // Fetch file from backend to bypass CORS
                $pdfContent = @file_get_contents($urlWebapps);
                if ($pdfContent) {
                    $base64Pdf = base64_encode($pdfContent);
                }
            } catch (\Exception $e) {}
        }
    @endphp
    
    <div class="card-body">
            <div class="card py-3 d-flex justify-content-center align-items-center" id="container-berkas-{{ $berkas->kode }}">
                @if(in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ $urlWebapps }}" alt="{{ $berkas->nama }}" style="width: 100%; max-width: 1000px; display: block; margin: 0 auto;">
                @else
                    <!-- PDF rendered as canvas -->
                @endif
            </div>
        </div>
    
    @if(!in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            (function() {
                if (typeof pdfjsLib === 'undefined') {
                    var script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js';
                    script.onload = function() {
                        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
                        renderDynamicPdf();
                    };
                    document.head.appendChild(script);
                } else {
                    renderDynamicPdf();
                }

                function renderDynamicPdf() {
                    var containerId = 'container-berkas-{{ $berkas->kode }}';
                    var container = document.getElementById(containerId);
                    var pdfData = "{{ $base64Pdf }}";
                    
                    if (!pdfData) {
                        if(container) {
                            container.innerHTML = '<div class="p-4 text-danger text-center">Gagal memuat file PDF dari server (' + '{{ $urlWebapps }}' + ').</div>';
                        }
                        return;
                    }
                    
                    var rawData = atob(pdfData);
                    var pdfAsArray = new Uint8Array(rawData.length);
                    for (var i = 0; i < rawData.length; i++) {
                        pdfAsArray[i] = rawData.charCodeAt(i);
                    }
                    
                    function renderPdf(pdf) {
                        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                            pdf.getPage(pageNum).then(function(page) {
                                var scale = 2.0; 
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
                                    if(container) {
                                        container.appendChild(img);
                                    }
                                });
                            });
                        }
                    }

                    pdfjsLib.getDocument({data: pdfAsArray}).promise.then(function(pdf) {
                        renderPdf(pdf);
                    }).catch(function(error) {
                        console.error('Error parsing PDF:', error);
                        if(container) {
                            container.innerHTML = '<div class="p-4 text-danger text-center">Gagal merender data PDF.</div>';
                        }
                    });
                }
            })();
        });
    </script>
    @endif
@endif
