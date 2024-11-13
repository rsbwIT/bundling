<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .text-xs {
            font-size: 8px;
            /* Adjust font size for paragraphs */
        }

        .text-md {
            font-size: 10px;
        }

        .h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .h4 {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            /* Adjust font size for tables */
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 {
            margin-top: 10px;
        }

        .mt-0 {
            margin-top: 0px;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .mx-1 {
            margin: 5px 8px;
        }

        .card-body {
            page-break-after: always;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .pb-4 {
            padding-bottom: 30px;
        }

        .card-body:last-child {
            page-break-after: auto;
        }
        .m-0{
            margin-bottom: 0px;
            margin-left: 0px;
            margin-right: 0px;
            margin-top: 0px;
        }
    </style>

<body>
    @if ($jumlahData > 0)
        {{-- INCLUDE BERKAS ============================================================= --}}
        @foreach ($settingBundling as $item)
            @include('bpjs.component.print.' . $item->nama_berkas)
        @endforeach
    @else
        <div class="card-body">
            <div class="card p-4 d-flex justify-content-center align-items-center">

            </div>
        </div>
    @endif
</body>

</html>
