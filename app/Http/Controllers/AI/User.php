<?php

namespace App\Http\Controllers\AI;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class User extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->cari;

        $data = DB::table('user as u')
            ->selectRaw("
                p.nama as nama_petugas,
                u.*,

                TRIM(
                    CAST(
                        AES_DECRYPT(
                            u.id_user,
                            'nur'
                        ) AS CHAR(50)
                    )
                ) as username_asli,

                TRIM(
                    CAST(
                        AES_DECRYPT(
                            u.password,
                            'windi'
                        ) AS CHAR(50)
                    )
                ) as password_asli
            ")

            ->leftJoin('petugas as p', function ($join) {

                $join->on(
                    'p.nip',
                    '=',
                    DB::raw("
                        TRIM(
                            CAST(
                                AES_DECRYPT(
                                    u.id_user,
                                    'nur'
                                ) AS CHAR(50)
                            )
                        )
                    ")
                );
            })

            ->when($cari, function ($query) use ($cari) {

                $query->where(function ($q) use ($cari) {

                    $q->where(
                        'p.nama',
                        'like',
                        "%{$cari}%"
                    )

                    ->orWhereRaw("
                        TRIM(
                            CAST(
                                AES_DECRYPT(
                                    u.id_user,
                                    'nur'
                                ) AS CHAR(50)
                            )
                        ) like ?
                    ", ["%{$cari}%"])

                    ->orWhereRaw("
                        TRIM(
                            CAST(
                                AES_DECRYPT(
                                    u.password,
                                    'windi'
                                ) AS CHAR(50)
                            )
                        ) like ?
                    ", ["%{$cari}%"]);

                });

            })

            ->orderBy('p.nama')
            ->get();

        return view(
            'ai.user',
            compact(
                'data',
                'cari'
            )
        );
    }



    public function getAkses($username)
    {
        $user = DB::table('user')

            ->whereRaw("
                TRIM(
                    CAST(
                        AES_DECRYPT(
                            id_user,
                            'nur'
                        ) AS CHAR(50)
                    )
                ) = ?
            ", [$username])

            ->first();


        if (!$user) {

            return response()->json([
                'status' => false
            ]);
        }


        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }



    public function updateAkses(Request $request)
    {
        $username = $request->id_user;


        $user = DB::table('user')

            ->whereRaw("
                TRIM(
                    CAST(
                        AES_DECRYPT(
                            id_user,
                            'nur'
                        ) AS CHAR(50)
                    )
                ) = ?
            ", [$username])

            ->first();


        if (!$user) {

            return response()->json([
                'status' => false
            ]);
        }


        $dataUser = (array) $user;


        unset($dataUser['id_user']);
        unset($dataUser['password']);


        $update = [];


        foreach ($dataUser as $field => $value) {

            if ($request->has($field)) {

                $update[$field] = 'true';

            } else {

                $update[$field] = 'false';
            }
        }


        DB::table('user')

            ->whereRaw("
                TRIM(
                    CAST(
                        AES_DECRYPT(
                            id_user,
                            'nur'
                        ) AS CHAR(50)
                    )
                ) = ?
            ", [$username])

            ->update($update);


        return response()->json([
            'status' => true
        ]);
    }
}