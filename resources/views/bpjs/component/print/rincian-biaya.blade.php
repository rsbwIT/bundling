@if ($bilingRalan)
    <div class="card-body">
        <div class="">
            <table width="700px">
                <tr>
                    <td rowspan="4"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                            width="80" height="80"></td>
                    <td class="text-center">
                        <h2>{{ $getSetting->nama_instansi }}</h2>
                    </td>
                </tr>
                <tr class="text-center">
                    <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                        {{ $getSetting->propinsi }}
                        {{ $getSetting->kontak }}</td>
                </tr>
                <tr class="text-center">
                    <td> E-mail : {{ $getSetting->email }}</td>
                </tr>
                <tr class="text-center">
                    @php
                        $jnsRawatNota = $statusLanjut->status_lanjut == 'Ranap' ? 'RAWAT INAP' : 'RAWAT JALAN';
                    @endphp
                    <td> RIANCIAN BIAYA {{ $jnsRawatNota }}</td>
                </tr>
            </table>
            <table width="700px" class="mt-1">
                @php
                    $totalBiaya = 0;
                @endphp
                @foreach ($bilingRalan as $item)
                    <tr>
                        <td width="150px">{{ $item->no }}</td>
                        <td width="300px">{{ $item->nm_perawatan }}</td>
                        <td width="70px">
                            @if ($item->biaya == 0)
                                {{-- Display an empty cell --}}
                            @else
                                {{ number_format($item->biaya, 0, ',', '.') }}
                            @endif
                        </td>
                        <td width="50px">
                            @if ($item->jumlah == 0)
                                {{-- Display an empty cell --}}
                            @else
                                {{ $item->jumlah }}
                            @endif
                        </td>
                        <td width="70px">
                            @if ($item->totalbiaya == 0)
                                {{-- Display an empty cell --}}
                            @else
                                {{ number_format($item->totalbiaya, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                    @php
                        $totalBiaya += $item->totalbiaya;
                    @endphp
                @endforeach
                <tr>
                    <td>TOTAL TAGIHAN</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>{{ number_format($totalBiaya, 0, ',', '.') }}</b></td>
                </tr>
                <tr>
                    <td>PPN</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>TAGIHAN + PPN</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>{{ number_format($totalBiaya, 0, ',', '.') }}</b></td>
                </tr>
                <tr>
                    <td>DEPOSIT</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>EKSES</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>SISA PIUTANG</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>{{ number_format($totalBiaya, 0, ',', '.') }}</b></td>
                </tr>

            </table>
        </div>
    </div>
@else
    {{-- NULL --}}
@endif
