<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

class PiutangHarian extends Controller
{
    public function index(Request $request)
    {
        $cariNomor  = $request->cariNomor;
        $tgl1       = $request->tgl1;
        $tgl2       = $request->tgl2;
        $tglLunas1  = $request->tgl_lunas1;
        $tglLunas2  = $request->tgl_lunas2;
        $filterType = $request->filter_type ?? 'tempo';
        $stsLanjut  = $request->stsLanjut;



        $akunBayar = DB::table('akun_bayar')

            ->whereIn(
                'kd_rek',
                [
                    '112010',
                    '112030',
                    '112011',
                    '112110',
                    '112090'
                ]
            )

            ->get();



        // first load kosong
        if (
            !$cariNomor &&
            !$tgl1 &&
            !$tglLunas1
        ) {

            return view(
                'laporan.piutangharian',
                [
                    'getPiutangHarian' => collect(),
                    'akunBayar' => $akunBayar
                ]
            );
        }



        // hanya no_rawat yg 1 penjamin
        $singlePenjab = DB::table(
            'detail_piutang_pasien'
        )

            ->select(
                'no_rawat'
            )

            ->groupBy(
                'no_rawat'
            )

            ->havingRaw(
                'COUNT(*) = 1'
            );



        $query = DB::table(
            'detail_piutang_pasien as dpp'
        )

            ->joinSub(
                $singlePenjab,
                'sp',

                function ($join) {

                    $join->on(
                        'dpp.no_rawat',
                        '=',
                        'sp.no_rawat'
                    );
                }
            )

            ->join(
                'reg_periksa as rp',
                'dpp.no_rawat',
                '=',
                'rp.no_rawat'
            )

            ->join(
                'poliklinik as pol',
                'rp.kd_poli',
                '=',
                'pol.kd_poli'
            )

            ->join(
                'dokter as dok',
                'rp.kd_dokter',
                '=',
                'dok.kd_dokter'
            )

            ->join(
                'pasien as p',
                'rp.no_rkm_medis',
                '=',
                'p.no_rkm_medis'
            )

            ->join(
                'piutang_pasien as pp',
                'rp.no_rawat',
                '=',
                'pp.no_rawat'
            )

            ->leftJoin(
                'detail_nota_jalan as dnj',
                'rp.no_rawat',
                '=',
                'dnj.no_rawat'
            )

            ->leftJoin(
                'akun_bayar as ab',
                'dnj.nama_bayar',
                '=',
                'ab.nama_bayar'
            )

            ->select(
                'dpp.no_rawat',
                'p.nm_pasien',
                'pol.nm_poli',
                'rp.kd_dokter',
                'dok.nm_dokter',
                'ab.nama_bayar',
                'ab.kd_rek',
                'pp.uangmuka',
                'pp.sisapiutang',
                'pp.status',
                'rp.status_lanjut'
            );



        // FILTER TEMPO
        if (
            $filterType == 'tempo' &&
            $tgl1 &&
            $tgl2
        ) {

            $query->whereBetween(
                'dpp.tgltempo',
                [
                    $tgl1,
                    $tgl2
                ]
            );
        }



        // FILTER LUNAS
        if (
            $filterType == 'lunas' &&
            $tglLunas1 &&
            $tglLunas2
        ) {

            $query->whereExists(

                function ($q) use (
                    $tglLunas1,
                    $tglLunas2
                ) {

                    $q->select(
                        DB::raw(1)
                    )

                        ->from(
                            'detail_lunas_cob as dlc'
                        )

                        ->whereColumn(
                            'dlc.no_rawat',
                            'dpp.no_rawat'
                        )

                        ->whereBetween(
                            'dlc.tgl_lunas',
                            [
                                $tglLunas1,
                                $tglLunas2
                            ]
                        );
                }

            );
        }



        // FILTER STATUS
        if ($stsLanjut) {

            $query->where(
                'rp.status_lanjut',
                $stsLanjut
            );
        }



        // FILTER CARI
        if ($cariNomor) {

            $query->where(

                function ($q) use (
                    $cariNomor
                ) {

                    $q->where(
                        'rp.no_rawat',
                        'like',
                        "%$cariNomor%"
                    )

                        ->orWhere(
                            'rp.no_rkm_medis',
                            'like',
                            "%$cariNomor%"
                        )

                        ->orWhere(
                            'p.nm_pasien',
                            'like',
                            "%$cariNomor%"
                        );
                }

            );
        }



        $getPiutangHarian = $query

            ->orderBy(
                'dpp.no_rawat'
            )

            ->get();



        if (
            $getPiutangHarian->isEmpty()
        ) {

            return view(
                'laporan.piutangharian',
                [
                    'getPiutangHarian' => collect(),
                    'akunBayar' => $akunBayar
                ]
            );
        }



        $noRawats = $getPiutangHarian

            ->pluck(
                'no_rawat'
            )

            ->toArray();




        // BILLING
        $billingMassal = DB::table(
            'billing'
        )

            ->select(
                'no_rawat',
                'status',
                'nm_perawatan',
                'totalbiaya'
            )

            ->whereIn(
                'no_rawat',
                $noRawats
            )

            ->get()

            ->groupBy(
                'no_rawat'
            );



        // PENJAMIN + COMPARE
        $penjabMassal = DB::table(
            'piutang_pasien as pp'
        )

            ->join(
                'reg_periksa as rp',
                'pp.no_rawat',
                '=',
                'rp.no_rawat'
            )

            ->join(
                'penjab as pj',
                'pj.kd_pj',
                '=',
                'rp.kd_pj'
            )

            ->leftJoinSub(

                DB::table('detail_piutang_pasien as dpp')

                    ->leftJoin(
                        'akun_piutang as ap',
                        'ap.nama_bayar',
                        '=',
                        'dpp.nama_bayar'
                    )

                    ->select(
                        'dpp.no_rawat',

                        DB::raw("
                    GROUP_CONCAT(
                        CONCAT(
                            TRIM(
                                REGEXP_REPLACE(
                                    IFNULL(ap.nama_bayar,''),
                                    '^[0-9]+\\\\. ',
                                    ''
                                )
                            ),
                            ' ',
                            dpp.totalpiutang
                        )
                        SEPARATOR ', '
                    ) as detail_piutang
                "),

                        DB::raw(
                            'SUM(dpp.totalpiutang) as total_detail'
                        )
                    )

                    ->groupBy(
                        'dpp.no_rawat'
                    ),

                'dpp_agg',

                function ($join) {

                    $join->on(
                        'dpp_agg.no_rawat',
                        '=',
                        'pp.no_rawat'
                    );
                }

            )

            ->select(

                'pp.no_rawat',

                DB::raw("
            TRIM(
                REGEXP_REPLACE(
                    pj.png_jawab,
                    '^[0-9]+\\\\. ',
                    ''
                )
            ) as png_jawab
        "),

                'pp.totalpiutang as total_header',

                'pp.sisapiutang',

                'dpp_agg.detail_piutang',

                'dpp_agg.total_detail',

                DB::raw('
            (
                pp.totalpiutang -
                IFNULL(dpp_agg.total_detail,0)
            ) as selisih
        '),

                DB::raw("
            CASE
                WHEN pp.totalpiutang =
                     IFNULL(dpp_agg.total_detail,0)
                THEN 'OK'
                ELSE 'SELISIH'
            END as status_compare
        ")
            )

            ->whereIn(
                'pp.no_rawat',
                $noRawats
            )

            ->get()

            ->keyBy(
                'no_rawat'
            );

        // LUNAS
        $lunasMassal = DB::table(
            'detail_lunas_cob'
        )

            ->whereIn(
                'no_rawat',
                $noRawats
            )

            ->get()

            ->keyBy(
                'no_rawat'
            );



        // NOTA JALAN
        $notaJalanMassal = DB::table(
            'nota_jalan'
        )

            ->select(
                'no_rawat',

                DB::raw("
                    GROUP_CONCAT(
                        DISTINCT no_nota
                        SEPARATOR ', '
                    ) as nota_jalan
                ")
            )

            ->whereIn(
                'no_rawat',
                $noRawats
            )

            ->groupBy(
                'no_rawat'
            )

            ->get()

            ->keyBy(
                'no_rawat'
            );



        // NOTA INAP
        $notaInapMassal = DB::table(
            'nota_inap'
        )

            ->select(
                'no_rawat',

                DB::raw("
                    GROUP_CONCAT(
                        DISTINCT no_nota
                        SEPARATOR ', '
                    ) as nota_inap
                ")
            )

            ->whereIn(
                'no_rawat',
                $noRawats
            )

            ->groupBy(
                'no_rawat'
            )

            ->get()

            ->keyBy(
                'no_rawat'
            );



        $getPiutangHarian->transform(

            function ($item) use (
                $billingMassal,
                $penjabMassal,
                $lunasMassal,
                $notaJalanMassal,
                $notaInapMassal
            ) {

                $billings = $billingMassal->get(
                    $item->no_rawat,
                    collect()
                );


                $getBill = fn($status) =>
                $billings->where(
                    'status',
                    $status
                );



                $item->getNomorNota = $getBill('No.Nota');

                $item->getRegistrasi = $getBill('Registrasi');
                $item->getObat = $getBill('Obat');
                $item->getReturObat = $getBill('Retur Obat');
                $item->getResepPulang = $getBill('Resep Pulang');

                $item->getRalanDokter = $getBill('Ralan Dokter');
                $item->getRalanDrParamedis = $getBill('Ralan Dokter Paramedis');
                $item->getRalanParamedis = $getBill('Ralan Paramedis');

                $item->getRanapDokter = $getBill('Ranap Dokter');
                $item->getRanapDrParamedis = $getBill('Ranap Dokter Paramedis');
                $item->getRanapParamedis = $getBill('Ranap Paramedis');

                $item->getOprasi = $getBill('Operasi');
                $item->getLaborat = $getBill('Laborat');
                $item->getRadiologi = $getBill('Radiologi');
                $item->getTambahan = $getBill('Tambahan');
                $item->getPotongan = $getBill('Potongan');
                $item->getKamarInap = $getBill('Kamar');



                // PENJAB
                $item->getPenjab =
                    $penjabMassal->get(
                        $item->no_rawat
                    );



                // LUNAS
                $item->getLunas =
                    $lunasMassal->get(
                        $item->no_rawat
                    );



                // NOTA JALAN
                $item->nota_jalan =
                    optional(
                        $notaJalanMassal->get(
                            $item->no_rawat
                        )
                    )->nota_jalan;



                // NOTA INAP
                $item->nota_inap =
                    optional(
                        $notaInapMassal->get(
                            $item->no_rawat
                        )
                    )->nota_inap;

                // LUNAS COB
                $item->getLunasCob = DB::table(
                    'detail_lunas_cob'
                )

                    ->select(
                        'tgl_lunas',
                        'nominal_cob',
                        'akun_bayar'
                    )

                    ->where(
                        'no_rawat',
                        $item->no_rawat
                    )

                    ->first();


                // GABUNG NOTA
                $item->nomor_nota =
                    $item->status_lanjut == 'Ralan'
                    ? $item->nota_jalan
                    : $item->nota_inap;
                return $item;
            }

        );


        return view(
            'laporan.piutangharian',
            [
                'getPiutangHarian' => $getPiutangHarian,
                'akunBayar' => $akunBayar
            ]
        );
    }

    public function simpanCob(Request $request)
    {
        $request->validate([
            'no_rawat'    => 'required',
            'tgl_lunas'   => 'required|date_format:Y-m-d',
            'nominal_cob' => 'required',
            'akun_bayar'  => 'required'
        ]);

        try {

            DB::table('detail_lunas_cob')->insert([
                'no_rawat'    => $request->no_rawat,
                'tgl_lunas'   => $request->tgl_lunas,
                'nominal_cob' => $request->nominal_cob,
                'akun_bayar'  => $request->akun_bayar
            ]);

            return back()->with(
                'success',
                'Data COB berhasil disimpan'
            );
        } catch (QueryException $ex) {

            if ($ex->getCode() == '23000') {

                return back()->with(
                    'error',
                    'Data gagal disimpan: nomor rawat sudah ada'
                );
            }

            return back()->with(
                'error',
                $ex->getMessage()
            );
        }
    }
}
