<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LogPerforma;

class LogSlowRequests
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $executionTime = microtime(true) - LARAVEL_START;
        
        // Catat jika loading lebih dari 1 detik
        if ($executionTime > 1.0) {
            try {
                LogPerforma::create([
                    'url_menu' => $request->fullUrl(),
                    'method' => $request->method(),
                    'waktu_loading_detik' => $executionTime,
                    'waktu_akses' => now()
                ]);
            } catch (\Exception $e) {
                // Jangan sampai merusak jalannya aplikasi jika gagal insert
            }
        }
    }
}
