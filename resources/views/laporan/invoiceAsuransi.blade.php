@extends('..layout.layoutDashboard')
@section('title', 'Invoice Asuransi')

@section('konten')
    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
            {{-- FORM --}}
            <form action="{{ url($url) }}">
                @csrf
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <button type="button"
                                    class="btn btn-default form-control form-control-xs d-flex justify-content-between"
                                    data-toggle="modal" data-target="#modal-lg">
                                    <p>Pilih Asuransi</p>
                                    <p><i class="nav-icon fas fa-credit-card"></i></p>
                                </button>
                            </div>
                            <div class="modal fade" id="modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Pilih Penjamin / Asuransi</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <select multiple="multiple" size="10" name="duallistbox[]">
                                                @foreach ($penjab as $item)
                                                    <option value="{{ $item->kd_pj }}">{{ $item->png_jawab }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="kdPenjamin">
                                            <script>
                                                var demo1 = $('select[name="duallistbox[]"]').bootstrapDualListbox();
                                                $('form').submit(function(e) {
                                                    e.preventDefault();
                                                    $('input[name="kdPenjamin"]').val($('select[name="duallistbox[]"]').val().join(','));
                                                    this.submit();
                                                });
                                            </script>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="status_lanjut" id="">
                            <option value="Ranap">Rawat Inap</option>
                            <option value="Rajal">Rawat Jalan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl1" class="form-control form-control-xs"
                                    value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl2" class="form-control form-control-xs"
                                    value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <select class="form-control" name="lampiran" id="">
                            <option value="01 (Satu) lembar">01 (satu) lembar</option>
                            <option value="02 (Dau) lembar">02 (Dau) lembar</option>
                            <option value="03 (Tiga) lembar">03 (Tiga) lembar</option>
                            <option value="04 (Empat) lembar">04 (Empat) lembar</option>
                            <option value="05 (Lima) lembar">05 (Lima) lembar</option>
                            <option value="06 (Enam) lembar">06 (Enam) lembar</option>
                            <option value="07 (Tujuh) lembar">07 (Tujuh) lembar</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <i class="fa fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Surat Tagihan --}}
            @if ($getNomorSurat)
                <form action="{{ url('simpan-invoice-asuransi') }}">
                    @csrf
                    <input hidden name="nomor_tagihan" value="{{ $getNomorSurat }}">
                    <input hidden name="kode_asuransi" value="{{ $getDetailAsuransi->kd_pj }}">
                    <input hidden name="nama_asuransi" value="{{ $getDetailAsuransi->nama_perusahaan }}">
                    <input hidden name="alamat_asuransi" value="{{ $getDetailAsuransi->alamat_asuransi }}">
                    <input hidden name="tanggl1" value="{{ $tanggl1 }}">
                    <input hidden name="tanggl2" value="{{ $tanggl2 }}">
                    <input hidden name="status_lanjut" value="{{ $status_lanjut }}">
                    <input hidden name="lamiran" value="{{ $lamiran }}">
                    <button type="submit" class="btn btn-primary">Cetak</button>
                </form>
                <div class="card py-4 mt-3  d-flex justify-content-center align-items-center" style="font-style: italic;">
                    <table border="0px" width="1000px">
                        <tr>
                            <td class="text-center">
                                <h4>Cetak Tagihan {{ $getDetailAsuransi->png_jawab }} <br>
                                    Pasien Close Billing dari {{ date('d/m/Y', strtotime($tanggl1)) }} -
                                    {{ date('d/m/Y', strtotime($tanggl2)) }} </h4>
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px" class="mt-2">
                        <tr>
                            <td width ="100px">
                                Nomor
                            </td>
                            <td>
                                : {{ $getNomorSurat }}
                            </td>
                        </tr>
                        <tr>
                            <td width ="100px">
                                Lampiran
                            </td>
                            <td>
                                : {{ $lamiran }}
                            </td>
                        </tr>
                        <tr>
                            <td width ="100px">
                                Perihal
                            </td>
                            <td>
                                : Tagihan Perawatan dan Pengobatan
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px" class="mt-4">
                        <tr>
                            <td>
                                Kepada Yth,
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Bagian Klaim
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @if ($getDetailAsuransi->nama_perusahaan == '' || $getDetailAsuransi->nama_perusahaan == '-')
                                    <b>Hubungi IT Unuk Melengkapi Nama Asuransi</b>
                                @else
                                    {{ $getDetailAsuransi->nama_perusahaan }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @if ($getDetailAsuransi->alamat_asuransi == '' || $getDetailAsuransi->alamat_asuransi == '-')
                                    <b>Hubungi IT Unuk Melengkapi Alamat Asuransi</b>
                                @else
                                    @php
                                        $text = $getDetailAsuransi->alamat_asuransi;
                                        $words = explode(' ', $text);
                                        $newText = '';
                                        foreach ($words as $key => $word) {
                                            $newText .= $word . ' ';
                                            if (($key + 1) % 7 == 0) {
                                                $newText .= '<br>';
                                            }
                                        }
                                    @endphp
                                    {!! $newText !!}
                                @endif
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px" class="mt-4">
                        <tr>
                            <td>
                                Dengan hormat,
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Bersama ini kami kirimkan tagihan biaya perawatan dan pengobatan nasabah
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @if ($getDetailAsuransi->nama_perusahaan == '' || $getDetailAsuransi->nama_perusahaan == '-')
                                    <b>Hubungi IT Unuk Melengkapi Nama Asuransi</b>
                                @else
                                    {{ $getDetailAsuransi->nama_perusahaan }}
                                @endif dengan perincian sebagai berikut:
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px" class="mt-4">
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Nama </th>
                            <th>Jumlah Biaya</th>
                            <th>Rm</th>
                            <th>Tanggal Rawat</th>
                        </tr>
                        @if ($getPasien)
                            @foreach ($getPasien as $key => $item)
                                <tr>
                                    <td width="50px"></td>
                                    <td width="15px">{{ $key + 1 }}. </td>
                                    <td>{{ $item->nm_pasien }}</td>
                                    <td><u>Rp. {{ number_format($item->total_biaya, 0, ',', '.') }}</u> ,</td>
                                    <td>{{ $item->no_rkm_medis }}</td>
                                    <td>
                                        @if ($item->tgl_masuk == null && $item->tgl_keluar == null)
                                            {{ $item->tgl_byr }}
                                        @else
                                            {{ date('d', strtotime($item->tgl_masuk)) . ' - ' . date('d', strtotime($item->tgl_keluar)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_keluar))) }}-{{ date('Y', strtotime($item->tgl_keluar)) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Rp. {{ number_format($getPasien->sum('total_biaya'), 0, ',', '.') }}</b></td>
                            </tr>
                        @endif
                    </table>
                    <table border="0px" width="1000px" class="mt-4">
                        <tr>
                            <td><b>Terbilang : </b>
                                @if ($getPasien)
                                    {{ \App\Services\Keuangan\NomorInvoice::Terbilang($getPasien->sum('total_biaya')) }}
                                @endif rupiah
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px">
                        <tr>
                            <td>
                                Demikian atas perhatian dan kerjasama yang baik kami ucapkan terimakasih.
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px" class="mt-4">
                        <tr>
                            <td>Bandar Lampung,
                                {{ date('d') }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m')) }}-{{ date('Y') }}<br />
                                Direktur Utama
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <b> dr. Arief Yulizar, MARS, FISQua</b>
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px" class="mt-4">
                        <tr>
                            <td>NB :</td>
                        </tr>
                        <tr>
                            <td>Mohon pelunasan dilaksanakan melalui transfer ke rekening</td>
                        </tr>
                        <tr>
                            <td>Atas Nama Rumah Sakit Bumi Waras No.Rekening. 0071-488-404</td>
                        </tr>
                        <tr>
                            <td>Di Bank BNI Jl. Kartini Bandar Lampung</td>
                        </tr>
                        <tr>
                            <td>(Kami mohon rincian data nama-nama pasien yang dibayar harap diemailkan ke</td>
                        </tr>
                        <tr>
                            <td><a href="#">Admkeuanganrsbumiwaras@yahoo.co.id Atau Wa Ke No 0823-7244-9677 ( Shity
                                    )</a></td>
                        </tr>
                        <tr>
                            <td>Atas perhatian dan kerjasamanya kami ucapkan terima kasih)</td>
                        </tr>
                        </tr>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
