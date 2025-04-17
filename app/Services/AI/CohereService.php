<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class CohereService
{
    public function generateText($pesan)
    {
        try {
            // Tambahkan instruksi agar AI menjawab dalam bahasa Indonesia
            $prompt = "Jawablah pertanyaan ini dalam bahasa Indonesia: " . $pesan;

            // Mengirim permintaan ke API Cohere
            $respon = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('COHERE_API_KEY'),
            ])->post('https://api.cohere.ai/v1/generate', [
                'model' => 'command',  // Pastikan model sesuai dengan dokumentasi Cohere
                'prompt' => $prompt,
                'max_tokens' => 100000,  // Tambah jika ingin jawaban lebih panjang
            ]);

            // Cek apakah permintaan berhasil
            if ($respon->successful()) {
                $data = $respon->json();

                // Log untuk debugging
                \Log::info('Respon dari API Cohere: ' . json_encode($data));

                if (isset($data['error'])) {
                    \Log::error('Terjadi kesalahan dari API Cohere: ' . $data['error']['message']);
                    return 'Terjadi kesalahan: ' . $data['error']['message'];
                }

                if (isset($data['generations']) && isset($data['generations'][0]['text'])) {
                    return $data['generations'][0]['text'];
                } else {
                    \Log::error('Data "text" tidak ditemukan di dalam respon API.');
                    return 'Maaf, tidak ada jawaban dari AI.';
                }
            } else {
                \Log::error('Gagal memproses permintaan API: ' . $respon->status() . ' - ' . $respon->body());
                return 'Terjadi kesalahan saat memproses permintaan.';
            }
        } catch (\Exception $e) {
            \Log::error('Exception saat menghubungi API Cohere: ' . $e->getMessage());
            return 'Terjadi kesalahan saat menghubungi AI.';
        }
    }
}
