<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSBW | @yield('title')</title>
    <link rel="icon" href="/img/rs.ico" type="image/x-icon">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css" />
    <link rel="stylesheet" href="/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
    <link rel="stylesheet" href="/plugins/jqvmap/jqvmap.min.css" />
    <link rel="stylesheet" href="/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="/plugins/summernote/summernote-bs4.min.css" />
    <script src="/js/plotly-latest.min.js"></script> {{-- CHART  --}}
    <script src="/plugins/jquery/jquery.min.js"></script>
    {{-- DUALIS --}}
    <link rel="stylesheet" href="/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <script src="/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    {{-- TEST --}}
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/current/metro.css">
    <style>
        u {
            border-bottom: 2px solid black;
            /* Ketebalan garis bawah */
            text-decoration: none;
            /* Menghilangkan garis bawah default */
            display: inline-block;
            /* Membuat elemen menjadi blok inline */
            position: relative;
        }
    </style>
    @stack('styles')

</head>
<body>
    {{-- <div class="container-fluid"> --}}
        @yield('konten')
    {{-- </div> --}}



    @stack('scripts')
    <script src="/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script>
        $.widget.bridge("uibutton", $.ui.button);
    </script>
    <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/plugins/chart.js/Chart.min.js"></script>
    <script src="/plugins/sparklines/sparkline.js"></script>
    <script src="/plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <script src="/plugins/jquery-knob/jquery.knob.min.js"></script>
    <script src="/plugins/moment/moment.min.js"></script>
    <script src="/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <script src="/plugins/summernote/summernote-bs4.min.js"></script>
    <script src="/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="/dist/js/adminlte.js"></script>
    <script src="/dist/js/pages/dashboard.js"></script>
    <script src="/js/sidebarmenu.js"></script>
    <script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/dev/metro.css">
    <script src="dist/kioskboard-aio-2.3.0.min.js"></script>
    {{-- TEST --}}
    <script src="https://cdn.metroui.org.ua/current/metro.js"></script>
    <script>
        $(document).ready(function() {
            $('.product-image-thumb').on('click', function() {
                var $image_element = $(this).find('img')
                $('.product-image').prop('src', $image_element.attr('src'))
                $('.product-image-thumb.active').removeClass('active')
                $(this).addClass('active')
            })
        })
        $(function() {
            bsCustomFileInput.init();
        });

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })


    </script>

    @include('layout.component.allert')

</body>

</html>
