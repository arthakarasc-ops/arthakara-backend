<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Kirim pesan WhatsApp menggunakan Fonnte
     *
     * @param string $target Nomor HP tujuan (contoh: 08123456789)
     * @param string $message Isi pesan WhatsApp
     * @return bool True jika berhasil, False jika gagal
     */
    public function sendMessage(string $target, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning('Fonnte token is empty. Cannot send WhatsApp message.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                return true;
            }

            Log::error('Fonnte send message failed', ['response' => $result]);
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte API error: ' . $e->getMessage());
            return false;
        }
    }
}
