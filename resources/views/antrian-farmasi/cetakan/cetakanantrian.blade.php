<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Antrian Farmasi</title>

    <script>
        function printPage() {
            window.print();
            setTimeout(function () {
                window.close();
            }, 1000);
        }
        window.onload = printPage;
    </script>

    <style>
        @media print {
            @page {
                margin: 3px;
            }
            body {
                margin: 3px;
            }
        }

        body {
            font-family: Arial, sans-serif;
            width: 350px;
            margin: auto;
        }

        .text-center {
            text-align: center;
        }

        .small-text {
            font-size: 14px;
        }

        .medium-text {
            font-size: 16px;
        }

        .large-text {
            font-size: 26px;
            font-weight: bold;
        }

        .bold-hr {
            border: 0;
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        table {
            width: 350px;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }
    </style>
</head>

<body>

    <table>
        <tr>
            <td class="text-center medium-text">
                <strong>{{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }}</strong>
            </td>
        </tr>
        <tr>
            <td class="text-center small-text">
                {{ $setting->alamat_instansi ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="text-center small-text">
                {{ $setting->kontak ?? '' }}
                <hr class="bold-hr">
            </td>
        </tr>
        <tr>
            <td class="text-center medium-text">
                <strong>BUKTI ANTRIAN FARMASI</strong>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="small-text" width="110">Tanggal</td>
            <td class="small-text" width="10">:</td>
            <td class="small-text">
                {{ \Carbon\Carbon::parse($antrian->tanggal)->format('d-m-Y') }}
            </td>
        </tr>

        <tr>
            <td class="small-text">No. Antrian</td>
            <td class="small-text">:</td>
            <td class="large-text">
                {{ $antrian->jalur }}{{ str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT) }}
            </td>
        </tr>

        <tr>
            <td class="small-text">Nama</td>
            <td class="small-text">:</td>
            <td class="small-text">
                {{ $antrian->nm_pasien }}
            </td>
        </tr>

        <tr>
            <td class="small-text">No RM</td>
            <td class="small-text">:</td>
            <td class="small-text">
                {{ $antrian->no_rkm_medis }}
            </td>
        </tr>

        <tr>
            <td class="small-text">Keterangan</td>
            <td class="small-text">:</td>
            <td class="small-text">
                {{ in_array($antrian->jalur, ['A','D']) ? 'RACIKAN' : 'NON RACIKAN' }}
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="text-center small-text">
                <hr class="bold-hr">
                Terima kasih atas kepercayaan Anda kepada <br>
                <strong>{{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }}</strong>
            </td>
        </tr>
    </table>

</body>
</html>
