<?php

namespace App\Http\Controllers\AI;

use App\Services\AI\CohereService;  // Pastikan namespace benar
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    protected $cohereService;

    public function __construct(CohereService $cohereService)
    {
        $this->cohereService = $cohereService;
    }

    public function index()
    {
        return view('ai.chat');  // Pastikan route mengarah ke view yang tepat
    }

    public function send(Request $request)
    {
        // Validasi pesan
        $request->validate([
            'message' => 'required|string|max:100000', // Batasi panjang pesan
        ]);

        // Ambil pesan dari user
        $message = $request->input('message');

        // Panggil service Cohere untuk mendapatkan respons
        $reply = $this->cohereService->generateText($message);

        // Kembalikan respons ke frontend
        return response()->json(['reply' => $reply]);
    }
}
