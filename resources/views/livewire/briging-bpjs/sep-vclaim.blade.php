<div>
    <div class="card">
        <div class="card-header">
            <form wire:submit.prevent='CariSepVclaim'>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input class="form-control form-control-sidebar form-control-sm" type="text"
                                placeholder="Masukan Nomor Rawat di Khanza" wire:model.lazy='cari_no_rawat'>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input class="form-control form-control-sidebar form-control-sm" type="text"
                                placeholder="Masukan Nomor Sep di V-claim" wire:model.lazy='cari_no_sep'>
                            <div class="input-group-append">
                                <button class="btn btn-sidebar btn-default btn-sm">
                                    <i class="fas fa-search fa-fw"></i>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                                        wire:loading wire:target='CariSepVclaim'></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if (isset($getSep[0]) && isset($getRegPeriksa[0]))
                <div class="row mb-2">
                    <div class="col-12">
                        <button class="btn btn-sidebar btn-primary btn-sm float-right" wire:click='SimpanSep()'>
                            Simpan
                        </button>
                    </div>
                </div>
                <div class="card p-4 d-flex justify-content-center align-items-center">
                    <table width="990px" border="0px">
                        <thead>
                            <tr>
                                <th rowspan="2" width="250px"><img src="img/bpjs.png" width="250px" class=""
                                        alt="">
                                </th>
                                <th class="text-center pr-5">
                                    <h4><b>SURAT ELEGIBILITAS PESERTA</h4></b>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center pr-5">
                                    <h5><b>{{ $getSetting[0]->nama_instansi }}</b></h5>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($getSep[0]->noSep, 'C39+') }}"
                                        alt="barcode" width="300px" height="35px" />
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <table width="990px" border="0px">
                        <tr>
                            <td width="150px">No. SEP</td>
                            <td width="400px">: {{ $getSep[0]->noSep }}</td>
                            <td width="150px">No. Rawat</td>
                            <td width="279px">: {{ $getRegPeriksa[0]->no_rawat }}</td>
                        </tr>
                        <tr>
                            <td>Tgl. SEP</td>
                            <td width="450px">: {{ date('d/m/Y', strtotime($getSep[0]->tglSep)) }}</td>
                            <td>No. Reg</td>
                            <td>: {{ $getRegPeriksa[0]->no_reg }}</td>
                        </tr>
                        <tr>
                            <td>No. Kartu</td>
                            <td colspan="">: {{ $getSep[0]->peserta->noKartu }} (MR:
                                {{ $getSep[0]->peserta->noMr }})</td>
                            <td>Peserta</td>
                            <td>: {{ $getSep[0]->peserta->jnsPeserta }}</td>
                        </tr>
                        <tr>
                            <td>Nama Peserta</td>
                            <td>: {{ $getSep[0]->peserta->nama }}</td>
                            <td>Jns Rawat</td>
                            <td>: {{ $getSep[0]->jnsPelayanan }}
                            </td>
                        </tr>
                        <tr>
                            @php
                                $jnsKunjungan =
                                    $getSep[0]->tujuanKunj->kode == 0
                                        ? '-Konsultasi dokter(Pertama)'
                                        : 'Kunjungan Kontrol(ulangan)';
                            @endphp
                            <td>Tgl. Lahir</td>
                            <td>: {{ date('d/m/Y', strtotime($getSep[0]->peserta->tglLahir)) }}
                            </td>
                            <td>Jns. Kunjungan</td>
                            <td class="text-xs">: {{ $jnsKunjungan }}</td>
                        </tr>
                        <tr>
                            @php
                                if ($jnsKunjungan == 0) {
                                    $$Prosedur = '';
                                } else {
                                    $Prosedur =
                                        $getSep[0]->flagProcedure->kode == 0
                                            ? '-Prosedur Tidak Berkelanjutan'
                                            : ($getSep[0]->tujuanKunj->kode == 1
                                                ? '- Prosedur dan Terapi Tidak Berkelanjutan'
                                                : '');
                                }
                            @endphp
                            <td style="vertical-align: top;">No. Telpon</td>
                            <td style="vertical-align: top;">: {{ $getRegPeriksa[0]->no_tlp }}</td>
                            <td></td>
                            <td class="text-xs">{{ $Prosedur }}</td>
                        </tr>
                        <tr>
                            <td>Sub/Spesialis</td>
                            <td>: {{ $getSep2[0]->poli }}</td>
                            <td>Poli Perujuk</td>
                            <td>: -</td>
                        </tr>
                        <tr>
                            <td>Dokter</td>
                            <td>: {{ $getSep[0]->kontrol->nmDokter }}
                            </td>
                            <td>Kls. Hak</td>
                            <td>: Kelas {{ $getSep[0]->klsRawat->klsRawatHak }}</td>
                        </tr>
                        <tr>
                            <td>Fasker Perujuk</td>
                            <td>: {{ $getSep2[0]->provPerujuk->nmProviderPerujuk }}</td>
                            <td>Kls. Rawat</td>
                            <td>: {{ $getSep[0]->peserta->hakKelas }}</td>
                        </tr>
                        <tr>
                            <td>Diagnosa Awal</td>
                            <td>: {{ $getSep2[0]->diagnosa }}</td>
                            <td>Penjamin</td>
                            <td>: BPJS Kesehatan</td>
                        </tr>
                        <tr>
                            <td>Catatan</td>
                            <td>: {{ $getSep[0]->catatan }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="990px" border="0px">
                        <tr>
                            <td class="text-xs">
                                *Saya Menyetujui BPJS Kesehatan Menggunakan Informasi Medis Pasien jika
                                diperlukan.
                                <br>
                                *SEP bukan sebagai bukti penjamin peserta <br>
                                Catatan Ke 1 {{ date('d/m/Y', strtotime($getSep[0]->tglSep)) }}

                            </td>
                            <td class="text-center" width="350px">
                                Pasien/Keluarga Pasien <br>
                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting[0]->nama_instansi . ',' . ' Kabupaten/Kota ' . $getSetting[0]->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSep[0]->peserta->nama . ' ID ' . $getSep[0]->peserta->noKartu . ' ' . $getSep[0]->tglSep, 'QRCODE') }}"
                                        alt="barcode" width="80px" height="75px" />
                                </div>
                                <b>{{ $getSep[0]->peserta->nama }}</b>
                            </td>
                        </tr>
                    </table>
                </div>
            @else
                <p>Data tidak di temukan.</p>
            @endif
        </div>
    </div>
</div>
