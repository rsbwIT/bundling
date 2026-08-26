<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function index()
    {
        $authId = session('auth')['id_user'] ?? 'unknown';
        // Ambil data user dari tabel pegawai (simrs khanza) atau tabel user.
        // Di sini saya asumsikan daftar kontak mengambil dari pegawai agar ada namanya.
        $users = \Illuminate\Support\Facades\DB::table('pegawai')
                    ->select('nik as id', 'nama as name')
                    ->where('stts_aktif', 'AKTIF')
                    ->where('nik', '!=', $authId)
                    ->get();
                    
        return view('chat.index', compact('users', 'authId'));
    }

    public function fetchMessages($userId)
    {
        $authId = session('auth')['id_user'] ?? 'unknown';
        $messages = Message::where(function ($query) use ($authId, $userId) {
            $query->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($authId, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $authId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|string',
            'message' => 'required|string'
        ]);

        $authId = session('auth')['id_user'] ?? 'unknown';

        $message = Message::create([
            'sender_id' => $authId,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'Message Sent!', 'message' => $message]);
    }
}
