@extends('..layout.layoutDashboard')
@section('title', 'Invoice Asuransi')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="card">
        <div class="card-header">
            <form action="{{ url($url) }}">
                @csrf
                <div class="row">
                    <div class="col-2">
                        <div class="input-group input-group-xs">
                            <button type="button"
                                class="btn btn-default btn-sm form-control form-control-sm d-flex justify-content-between"
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
                    <div class="col-md-2">
                        <div class="input-group input-group-xs">
                            <input type="date" name="tgl1" class="form-control form-control-sm"
                                value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                            <div class="input-group-append">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-xs">
                            <input type="date" name="tgl2" class="form-control form-control-sm"
                                value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                            <div class="input-group-append">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="status_lanjut" id="">
                            <option value="Ranap">Ranap</option>
                            <option value="Ralan">Ralan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tgl_cetak" class="form-control form-control-sm"
                            value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                        <div class="input-group-append">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group input-group-xs">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            {{-- Surat Tagihan --}}
            @if ($getNomorSurat)
                <form action="{{ url('simpan-invoice-asuransi') }}">
                    @csrf
                    <input hidden name="nomor_tagihan" value="{{ $getNomorSurat }}">
                    <input hidden name="kode_asuransi" value="{{ $getDetailAsuransi->kd_surat }}">
                    <input hidden name="cari_kode_asuransi" value="{{ implode(',', $kdPenjamin) }}">
                    <input hidden name="nama_asuransi" value="{{ $getDetailAsuransi->nama_perusahaan }}">
                    <input hidden name="alamat_asuransi" value="{{ $getDetailAsuransi->alamat_asuransi }}">
                    <input hidden name="tanggl1" value="{{ $tanggl1 }}">
                    <input hidden name="tanggl2" value="{{ $tanggl2 }}">
                    <input hidden name="tgl_cetak" value="{{ $tgl_cetak }}">
                    <input hidden name="status_lanjut" value="{{ $status_lanjut }}">
                    <input hidden name="lamiran"
                        value="{{ count($getPasien) }} ({{ \App\Services\Keuangan\NomorInvoice::Terbilang(count($getPasien)) }} ) Lembar">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save" aria-hidden="true"></i>
                        Simpan</button>
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
                                : {{ count($getPasien) }}
                                ({{ \App\Services\Keuangan\NomorInvoice::Terbilang(count($getPasien)) }} ) Lembar
                            </td>
                        </tr>
                        <tr>
                            <td width ="100px">
                                Perihal
                            </td>
                            <td>
                                : Tagihan Perawatan dan Pengobatan <b>{{ $status_lanjut == 'Ranap' ? 'Rawat Inap' : 'Rawat Jalan' }}</b>
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
                                    <b>Input dimenu maping asuransi untuk melengkapi nama asuransi</b>
                                @else
                                    {{ $getDetailAsuransi->nama_perusahaan }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                @if ($getDetailAsuransi->alamat_asuransi == '' || $getDetailAsuransi->alamat_asuransi == '-')
                                    <b>Input dimenu maping asuransi untuk melengkapi alamat asuransi</b>
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
                                    <b>Input dimenu maping asuransi untuk melengkapi nama asuransi</b>
                                @else
                                    {{ $getDetailAsuransi->nama_perusahaan }}
                                @endif dengan perincian sebagai berikut:
                            </td>
                        </tr>
                    </table>
                    {{-- TEMPLATE --}}
                    @include('laporan.component.cetak-invoice-asuransi.template-cetak2')
                    {{-- //TEMPLATE --}}
                    <table border="0px" width="1000px" class="mt-4">
                        <tr>
                            <td><b>Terbilang : </b>
                                @if ($getPasien)
                                    {{ \App\Services\Keuangan\NomorInvoice::Terbilang(
                                        $getPasien->sum(function ($item) {
                                            return $item->getTotalBiaya->sum('totalpiutang');
                                        }),
                                    ) }}
                                @endif rupiah.
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
                                {{ date('d', strtotime($tgl_cetak)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($tgl_cetak))) }}-{{ date('Y', strtotime($tgl_cetak)) }}<br />
                                <b> Direktur Utama</b>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <b> dr. Arief Yulizar, MARS, FISQua.CHAE</b>
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
                            <td>Atas Nama Rumah Sakit Bumi Waras No.Rekening. {{$getDetailAsuransi->tf_rekening_rs}}</td>
                        </tr>
                        <tr>
                            <td>Di {{$getDetailAsuransi->nm_tf_rekening_rs}}</td>
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
            <div class="card p-0 card-primary card-tabs" style="height: 450px;"">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="tabCetak" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                aria-selected="true">Riwayat Invoice</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                                aria-selected="false">Peserta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="mapingAsuransi" data-toggle="pill" href="#custom-mapingAsuransi"
                                role="tab" aria-controls="custom-mapingAsuransi" aria-selected="false">Maping
                                Asuransi</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content table-responsive" id="tabCetakContent">
                    <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                        aria-labelledby="custom-tabs-one-home-tab">
                        @livewire('laporan.riwayat-invoce')
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel"
                        aria-labelledby="custom-tabs-one-profile-tab">
                        @livewire('laporan.invoce-asuransi')
                    </div>
                    <div class="tab-pane fade" id="custom-mapingAsuransi" role="tabpanel"
                        aria-labelledby="mapingAsuransi">
                        @livewire('laporan.maping-asuransi')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
