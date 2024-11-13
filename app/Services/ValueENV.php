<?php

namespace App\Services;

class ValueENV
{
    public static function getDokterFiso()
    {
       return env('SET_DOKTER_FISO');
    }
}
