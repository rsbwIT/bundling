<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIChat extends Controller
{
    public function chat(Request $request)
    {
        // Ambil API Key dari .env
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key tidak ditemukan'], 500);
        }

        // Validasi input
        $userMessage = $request->input('message');
        if (!$userMessage) {
            return response()->json(['error' => 'Pesan tidak boleh kosong'], 400);
        }

        // Kirim permintaan ke OpenAI
        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $userMessage]
            ],
            'max_tokens' => 100
        ]);

        // Cek jika request gagal
        if ($response->failed()) {
            return response()->json([
                'error' => 'Gagal mendapatkan respons dari AI',
                'details' => $response->json()
            ], 500);
        }

        // Ambil jawaban dari AI
        return response()->json([
            'response' => $response->json('choices.0.message.content') ?? 'AI tidak memberikan respons.'
        ]);
    }
}
