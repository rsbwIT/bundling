<?php

namespace App\Http\Controllers\Test;

use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;
use Illuminate\Http\Request;
use App\Services\TestService;
use App\Services\Bpjs\Referensi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Bpjs\ReferensiBPJS;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    // protected $referensi;
    // public function __construct()
    // {
    //     $this->referensi = new ReferensiBPJS;
    // }
    // public function dashboardTanggal() {
    //     $data = $this->referensi->getPoli('Dalam');
    //     dd($data);
    // }


    public function TestFun()
    {
        function inacbg_compare($a, $b)
        {
            /// compare individually to prevent timing attacks
            /// compare length
            if (strlen($a) !== strlen($b)) return false;
            /// compare individual
            $result = 0;
            for ($i = 0; $i < strlen($a); $i++) {
                $result |= ord($a[$i]) ^ ord($b[$i]);
            }
            return $result == 0;
        }
        function inacbg_decrypt($str, $strkey)
        {
            $key = hex2bin($strkey);
            if (mb_strlen($key, "8bit") !== 32) {
                throw new \InvalidArgumentException("Key length must be 256-bit (32 bytes).");
            }
            $iv_size = openssl_cipher_iv_length("aes-256-cbc");

            // breakdown parts
            $decoded = base64_decode($str);
            $signature = mb_substr($decoded, 0, 10, "8bit");
            $iv = mb_substr($decoded, 10, $iv_size, "8bit");
            $encrypted = mb_substr($decoded, $iv_size + 10, null, "8bit");

            // check signature, against padding oracle attack
            $calc_signature = mb_substr(
                hash_hmac("sha256", $encrypted, $key, true),
                0,
                10,
                "8bit"
            );

            if (!inacbg_compare($signature, $calc_signature)) {
                return "SIGNATURE_NOT_MATCH"; // signature doesn't match
            }

            $decrypted = openssl_decrypt(
                $encrypted,
                "aes-256-cbc",
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
            return $decrypted;
        }
        dd(inacbg_decrypt('lyEhofMy00Z45NovgYbxC5WKWN6aMvX+t9IHLpzP4VvwEXavL+lwLnsIAtDqzEtvadXs4hFSPjnBH1Nad8iJxKqnRbqwNW7no45LqhMrUd68X+4Z8k0SNu1jYkP2hJyAdFSRP4AJRcYkObxH2A2SoFxqtwd4b2Y/GRyT5DdQWUC3SgBN88dO6cri3VF07K+OUGr8nBPAFp9G2g==','61de839417c767f6810772eb7b042fa52521e211b4f5c8e6f12ad1d92b3a3bcb'));
    }
    function Test() {
        return view('test.test');
    }
}
