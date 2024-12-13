<!doctype html>
<html lang="en">

<head>
    <title>Halaman -> @yield('title')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{-- <meta http-equiv="refresh" content="10"> --}}
    <link rel="stylesheet" href="/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css" />
    <!-- Bootstrap CSS -->
    @stack('styles')
    <script type="text/javascript">
        setTimeout(function() {
            location.reload();
        }, 900000); // 30,000 milidetik (30 detik)
    </script>

</head>
<style>
    .pricing-card-title {
        font-size: 150px;
        font-weight: 600;
    }

    .display-3 {
        font-size: 80px;
        font-weight: 500;
    }

    a:link,
    a:visited {
        color: rgb(0, 0, 0);
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    a:hover {
        color: rgb(0, 30, 255);
    }
</style>

<body onclick="openFullscreen();">

    <div class="container-fluid">
        @yield('konten')
    </div>



    @stack('scripts')
    <script src="/dist/js/adminlte.js"></script>
    <script>
        var elem = document.documentElement;
        function openFullscreen() {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE11 */
                elem.msRequestFullscreen();
            }
        }
    </script>
</body>

</html>
