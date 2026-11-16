@php
    $thn = date('Y', strtotime($tgl1 ?? now()));
    
    // Perhitungan dari dataRekap
    $sum_ranap = $dataRekap->where('status_lanjut', 'Ranap')->sum('bayar_piutang');
    $sum_ralan = $dataRekap->where('status_lanjut', 'Ralan')->sum('bayar_piutang');
    
    $sum_pend_lain = 0;
    $sum_bon_resep = (float) $dataRekap->sum('piutang_obat');
    
    $total_pendapatan = $sum_ranap + $sum_ralan + $sum_pend_lain + $sum_bon_resep;
    
    $sum_cob = (float) $dataRekap->sum('cob');
    $sum_tmb = (float) $dataRekap->sum('tmb_bayar');
    $sum_pot = (float) $dataRekap->sum('pot_bayar');
    
    $rekening_bank = $total_pendapatan + $sum_cob + $sum_tmb - $sum_pot;
@endphp

<div class="mt-4 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2" style="max-width: 480px;">
        <h6 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-file-invoice-dollar mr-1 text-primary"></i> Rangkuman Pendapatan
        </h6>
        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyTable('tableSummaryAsuransi')">
            <i class="fas fa-copy mr-1"></i> Copy Rangkuman
        </button>
    </div>

    <table class="table table-bordered table-sm align-middle" id="tableSummaryAsuransi" style="width: 480px; max-width: 100%; font-size: 11px; border: 1.5px solid #000; font-family: sans-serif;">
        <thead>
            <tr style="background-color: #d9d9d9;">
                <th colspan="4" class="text-center font-weight-bold text-dark" style="font-size: 13px; padding: 8px; border: 1px solid #000;">
                    Pendapatan {{ $thn }} :
                </th>
            </tr>
            <tr style="height: 18px;">
                <th style="width: 45%; border: 1px solid #000; padding: 2px;"></th>
                <th style="width: 18%; border: 1px solid #000; padding: 2px;"></th>
                <th style="width: 18%; border: 1px solid #000; padding: 2px;"></th>
                <th style="width: 19%; border: 1px solid #000; padding: 2px;"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 4px 8px; font-weight: 500;">Ranap</td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td class="text-right" style="border: 1px solid #000; padding: 4px 8px;" data-raw="{{ $sum_ranap }}">
                    {{ $sum_ranap == 0 ? '-' : number_format($sum_ranap, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 4px 8px; font-weight: 500;">Ralan</td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td class="text-right" style="border: 1px solid #000; padding: 4px 8px;" data-raw="{{ $sum_ralan }}">
                    {{ $sum_ralan == 0 ? '-' : number_format($sum_ralan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 4px 8px; font-weight: 500;">Pend.Lain</td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td class="text-right" style="border: 1px solid #000; padding: 4px 8px;" data-raw="{{ $sum_pend_lain }}">
                    -
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 4px 8px; font-weight: 500;">Bon Resep</td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td class="text-right" style="border: 1px solid #000; padding: 4px 8px;" data-raw="{{ $sum_bon_resep }}">
                    {{ $sum_bon_resep == 0 ? '-' : number_format($sum_bon_resep, 0, ',', '.') }}
                </td>
            </tr>
            <tr style="background-color: #fce4d6;">
                <td colspan="3" class="text-center font-weight-bold text-dark" style="border: 1px solid #000; padding: 6px 8px;">
                    Total Pendapatan {{ $thn }}
                </td>
                <td class="text-right font-weight-bold text-dark" style="border: 1px solid #000; padding: 6px 8px;" data-raw="{{ $total_pendapatan }}">
                    {{ number_format($total_pendapatan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 4px 8px; font-weight: 500;">COB {{ $thn }} (+)</td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td class="text-right" style="border: 1px solid #000; padding: 4px 8px;" data-raw="{{ $sum_cob }}">
                    {{ $sum_cob == 0 ? '-' : number_format($sum_cob, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 4px 8px; font-weight: 500;">(+)</td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td class="text-right" style="border: 1px solid #000; padding: 4px 8px;" data-raw="{{ $sum_tmb }}">
                    {{ $sum_tmb == 0 ? '-' : number_format($sum_tmb, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 4px 8px; font-weight: 500;">(-)</td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td style="border: 1px solid #000; padding: 4px 8px;"></td>
                <td class="text-right" style="border: 1px solid #000; padding: 4px 8px;" data-raw="{{ $sum_pot }}">
                    {{ $sum_pot == 0 ? '-' : number_format($sum_pot, 0, ',', '.') }}
                </td>
            </tr>
            <tr style="background-color: #ffc000;">
                <td colspan="3" class="text-center font-weight-bold text-dark" style="border: 1px solid #000; padding: 6px 8px;">
                    Rekening Bank
                </td>
                <td class="text-right font-weight-bold text-dark" style="border: 1px solid #000; padding: 6px 8px;" data-raw="{{ $rekening_bank }}">
                    {{ number_format($rekening_bank, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

