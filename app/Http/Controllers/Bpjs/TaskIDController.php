<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskIDController extends Controller
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl  = rtrim(env('MJKN_RS'), '/');
        $this->username = env('X_USERNAME');
        $this->password = env('X_PASSWORD');
    }

    private function getTokenMJKN()
    {
        return Cache::remember('mjkn_token', 86400, function () {
            $response = Http::timeout(10)
                ->withHeaders([
                    'x-username' => $this->username,
                    'x-password' => $this->password
                ])
                ->get($this->baseUrl . '/auth');

            if ($response->successful()) {
                $result = $response->json();
                return $result['response']['token'] ?? null;
            }

            return null;
        });
    }

    public function index()
    {
        return view('bpjs.taskid');
    }

    private function generateSignature($consid, $key, $utc)
    {
        $data = $consid . "&" . $utc;
        $hash = hash_hmac('sha256', $data, $key, true);
        return base64_encode($hash);
    }

    private function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        if ($output) {
            $decompressed = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
            return $decompressed ? $decompressed : $output;
        }
        return $string;
    }

    public function search(Request $request)
    {
        $tanggal1 = $request->input('tanggal1', date('Y-m-d'));
        $tanggal2 = $request->input('tanggal2', date('Y-m-d'));
        $keyword = $request->input('keyword', '');

        $query = DB::table('referensi_mobilejkn_bpjs')
            ->join('reg_periksa', 'referensi_mobilejkn_bpjs.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereBetween('referensi_mobilejkn_bpjs.tanggalperiksa', [$tanggal1, $tanggal2])
            ->select(
                'referensi_mobilejkn_bpjs.nobooking',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'referensi_mobilejkn_bpjs.nohp',
                'referensi_mobilejkn_bpjs.nomorkartu',
                'referensi_mobilejkn_bpjs.nik',
                'referensi_mobilejkn_bpjs.tanggalperiksa',
                'poliklinik.nm_poli',
                'dokter.nm_dokter'
            )
            ->orderBy('referensi_mobilejkn_bpjs.tanggalperiksa', 'asc');

        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('referensi_mobilejkn_bpjs.nobooking', 'LIKE', "%$keyword%")
                  ->orWhere('reg_periksa.no_rkm_medis', 'LIKE', "%$keyword%")
                  ->orWhere('pasien.nm_pasien', 'LIKE', "%$keyword%")
                  ->orWhere('referensi_mobilejkn_bpjs.nohp', 'LIKE', "%$keyword%")
                  ->orWhere('referensi_mobilejkn_bpjs.nomorkartu', 'LIKE', "%$keyword%")
                  ->orWhere('referensi_mobilejkn_bpjs.nik', 'LIKE', "%$keyword%")
                  ->orWhere('poliklinik.nm_poli', 'LIKE', "%$keyword%")
                  ->orWhere('dokter.nm_dokter', 'LIKE', "%$keyword%");
            });
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getListTask(Request $request)
    {
        $kodebooking = $request->input('kodebooking');
        if (!$kodebooking) {
            return response()->json(['success' => false, 'message' => 'Kode booking is required'], 400);
        }

        $baseUrl = rtrim(env('API_BPJS_ANTROL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs/'), '/');
        $consId = env('CONS_ID');
        $secretKey = env('SECRET_KEY');
        $userKey = env('USER_KEY_ANTROL');
        
        $utc = time();
        $signature = $this->generateSignature($consId, $secretKey, $utc);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-cons-id' => $consId,
                    'x-timestamp' => $utc,
                    'x-signature' => $signature,
                    'user_key' => $userKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($baseUrl . '/antrean/getlisttask', [
                    'kodebooking' => $kodebooking
                ]);

            $result = $response->json();

            if (isset($result['metadata']['code']) && $result['metadata']['code'] == 200) {
                // Dekripsi response menggunakan ConsID + SecretKey + Timestamp
                $decryptKey = $consId . $secretKey . $utc;
                $decryptedResponse = $this->stringDecrypt($decryptKey, $result['response']);
                $decodedData = json_decode($decryptedResponse, true);

                return response()->json([
                    'success' => true,
                    'data' => $decodedData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['metadata']['message'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('MJKN GETLISTTASK ERROR', [
                'kodebooking' => $kodebooking,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
