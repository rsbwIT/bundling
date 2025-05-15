<table border="0px" width="1000px">
    <tr>
        <td rowspan="3">
            <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                alt="Logo Instansi" width="80" height="80">
        </td>
        <td class="text-center">
            <h4>{{ $getSetting->nama_instansi }}</h4>
        </td>
        <td class="text-center" width="100px">
        </td>
    </tr>
    <tr class="text-center mr-5">
        <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
            {{ $getSetting->propinsi }}
            {{ $getSetting->kontak }}</td>
    </tr>
    <tr class="text-center">
        <td> E-mail : {{ $getSetting->email }}</td>
    </tr>
</table>
