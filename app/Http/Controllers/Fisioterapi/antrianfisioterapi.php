<?php

namespace App\Http\Controllers\Fisioterapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class antrianfisioterapi extends Controller
{
    public function index(Request $request)
    {
        // Menggunakan tanggal dari request atau default hari ini (sesuai query contoh 2026-07-20)
        $tgl_registrasi = $request->input('tgl_registrasi', date('Y-m-d'));

        $antrian = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_reg', 
                'reg_periksa.no_rawat', 
                'pasien.no_rkm_medis', 
                'pasien.nm_pasien', 
                'reg_periksa.tgl_registrasi', 
                'poliklinik.nm_poli'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('poliklinik.nm_poli', 'Poliklinik Rehabilitasi Medik')
            ->where('reg_periksa.tgl_registrasi', $tgl_registrasi)
            ->get();

        if ($request->ajax()) {
            return response()->json($antrian);
        }

        // Return to blade view
        return view('fisioterapi.antrianfisioterapi', compact('antrian', 'tgl_registrasi'));
    }

    public function panggil(Request $request)
    {
        $data = [
            'no_rawat'  => $request->no_rawat,
            'nm_pasien' => $request->nm_pasien,
            'no_reg'    => $request->no_reg,
            'nm_poli'   => $request->nm_poli,
            'time'      => now()->timestamp
        ];

        // Simpan data panggil di Cache selama 12 jam
        \Illuminate\Support\Facades\Cache::put('current_fisioterapi', $data, 60 * 12);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function display()
    {
        return view('fisioterapi.displayfisioterapi');
    }

    public function getCurrentPanggil()
    {
        $data = \Illuminate\Support\Facades\Cache::get('current_fisioterapi');
        $videoCmd = \Illuminate\Support\Facades\Cache::get('fisioterapi_video_command'); // Gunakan get agar tidak hilang jika player belum ready
        
        return response()->json([
            'antrian' => $data,
            'video' => $videoCmd
        ]);
    }

    public function videoCommand(Request $request)
    {
        $cmd = [
            'action' => $request->action, // 'skip' atau 'volume'
            'value'  => $request->value,  // misal: 50 untuk volume
            'time'   => microtime(true)   // Gunakan microtime agar tidak bentrok jika dipanggil di detik yang sama
        ];
        
        // Simpan command ke cache dengan waktu yang cukup lama (12 jam)
        \Illuminate\Support\Facades\Cache::put('fisioterapi_video_command', $cmd, 60 * 12);
        
        return response()->json(['success' => true]);
    }
        
    public function searchVideo(Request $request)
    {
        $query = urlencode($request->q);
        
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36\r\n" .
                            "Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $html = @file_get_contents("https://www.youtube.com/results?search_query={$query}", false, $context);
        
        $results = [];
        if($html && preg_match('/ytInitialData = (.*?);<\/script>/', $html, $matches)) {
            $data = json_decode($matches[1], true);
            $items = $data['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'][0]['itemSectionRenderer']['contents'] ?? [];
            
            foreach($items as $item) {
                if(isset($item['videoRenderer'])) {
                    $vid = $item['videoRenderer'];
                    $results[] = [
                        'id' => $vid['videoId'],
                        'title' => $vid['title']['runs'][0]['text'] ?? '',
                        'thumbnail' => $vid['thumbnail']['thumbnails'][0]['url'] ?? ''
                    ];
                    if(count($results) >= 5) break; // Ambil 5 teratas
                }
            }
        }
        
        return response()->json($results);
    }
}
