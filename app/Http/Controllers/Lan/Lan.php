<?php

namespace App\Http\Controllers\Lan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Lan extends Controller
{
    // ===============================
    // HALAMAN UTAMA LAN MESSENGER
    // ===============================
    public function index(Request $request)
    {
        $ip = $request->ip();
        $hostname = @gethostbyaddr($ip) ?: gethostname();

        // Simpan / update client (AUTO ONLINE)
        DB::table('lan_clients')->updateOrInsert(
            ['ip' => $ip],
            [
                'hostname'  => $hostname,
                'last_seen' => now()
            ]
        );

        // Ambil semua client (online & offline)
        $clients = DB::table('lan_clients')
            ->orderBy('hostname')
            ->get();

        return view('lan.lan', compact('ip', 'hostname', 'clients'));
    }

    // ===============================
    // HEARTBEAT (AUTO ONLINE STATUS)
    // ===============================
    public function heartbeat(Request $request)
    {
        DB::table('lan_clients')
            ->where('ip', $request->ip())
            ->update([
                'last_seen' => now()
            ]);

        return response()->json(['status' => 'online']);
    }

    // ===============================
    // KIRIM PESAN (BROADCAST / PRIVATE)
    // ===============================
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'to_ip'   => 'nullable|string'
        ]);

        DB::table('lan_messages')->insert([
            'from_ip'    => $request->ip(),
            'to_ip'      => $request->to_ip, // NULL = broadcast
            'message'    => $request->message,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['status' => 'sent']);
    }

    // ===============================
    // AMBIL PESAN (UNTUK CHAT + POPUP)
    // ===============================
    public function fetchMessage(Request $request)
    {
        $ip = $request->ip();

        $messages = DB::table('lan_messages')
            ->where(function ($q) use ($ip) {
                $q->whereNull('to_ip')      // broadcast
                  ->orWhere('to_ip', $ip)  // private ke saya
                  ->orWhere('from_ip', $ip); // pesan saya sendiri
            })
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($messages);
    }
}
