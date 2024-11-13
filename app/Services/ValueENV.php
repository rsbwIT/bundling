<?php

namespace App\Services;

class ValueENV
{
    public static function getENV()
    {
       return env('SET_DOKTER_FISO');
    }
}
