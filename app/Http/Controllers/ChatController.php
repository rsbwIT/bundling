<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $authId = session('auth')['id_user'] ?? 'unknown';
        $users = DB::table('pegawai')
                    ->select('nik as id', 'nama as name')
                    ->where('stts_aktif', 'AKTIF')
                    ->where('nik', '!=', $authId)
                    ->get();

        return view('chat.index', compact('users', 'authId'));
    }

    public function fetchMessages($userId)
    {
        $authId = session('auth')['id_user'] ?? 'unknown';
        $messages = Message::where(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $authId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    /**
     * Polling endpoint: returns messages after a given ID for active chat,
     * and unread counts for all other conversations.
     */
    public function poll(Request $request)
    {
        $authId      = session('auth')['id_user'] ?? 'unknown';
        $lastId      = (int) $request->get('last_id', 0);
        $activeUser  = $request->get('active_user', null);

        // New messages in active conversation (after last_id)
        $newMessages = [];
        if ($activeUser) {
            $newMessages = Message::where('id', '>', $lastId)
                ->where(function ($q) use ($authId, $activeUser) {
                    $q->where('sender_id', $authId)->where('receiver_id', $activeUser);
                })->orWhere(function ($q) use ($authId, $activeUser, $lastId) {
                    $q->where('id', '>', $lastId)
                      ->where('sender_id', $activeUser)->where('receiver_id', $authId);
                })->orderBy('created_at', 'asc')->get();
        }

        // Unread counts: messages sent TO me that are after last_id, grouped by sender
        $unread = Message::select('sender_id', DB::raw('MAX(id) as max_id'), DB::raw('COUNT(*) as cnt'), DB::raw('MAX(message) as last_message'))
            ->where('receiver_id', $authId)
            ->where('id', '>', $lastId)
            ->when($activeUser, fn($q) => $q->where('sender_id', '!=', $activeUser))
            ->groupBy('sender_id')
            ->get();

        // Global max id among all new messages received
        $maxId = Message::where('receiver_id', $authId)
            ->where('id', '>', $lastId)
            ->max('id') ?? $lastId;

        if ($activeUser) {
            $activeMax = Message::where(function ($q) use ($authId, $activeUser) {
                    $q->where('sender_id', $authId)->where('receiver_id', $activeUser);
                })->orWhere(function ($q) use ($authId, $activeUser) {
                    $q->where('sender_id', $activeUser)->where('receiver_id', $authId);
                })->max('id') ?? $lastId;
            $maxId = max($maxId, $activeMax);
        }

        return response()->json([
            'new_messages' => $newMessages,
            'unread'       => $unread,
            'max_id'       => $maxId,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|string',
            'message'     => 'nullable|string',
            'attachment'  => 'nullable|file|max:10240', // 10MB max
        ]);

        $authId = session('auth')['id_user'] ?? 'unknown';

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            if (strpos($file->getMimeType(), 'image') !== false) {
                $attachmentType = 'image';
            } else {
                $attachmentType = 'document';
            }
            $attachmentPath = $file->store('chat_attachments', 'public');
        }

        if (empty($request->message) && !$attachmentPath) {
            return response()->json(['error' => 'Pesan atau lampiran tidak boleh kosong'], 400);
        }

        $message = Message::create([
            'sender_id'       => $authId,
            'receiver_id'     => $request->receiver_id,
            'message'         => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        // Try broadcast (non-critical, won't break chat if fails)
        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            // silently ignore broadcast errors
        }

        return response()->json(['status' => 'ok', 'message' => $message]);
    }
}
