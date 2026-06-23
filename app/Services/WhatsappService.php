<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $url;

    public function __construct()
    {
        $this->url = 'http://127.0.0.1:3000';
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $phone
     * @param string $message
     * @return array
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            $response = Http::post("{$this->url}/send", [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Gagal mengirim pesan.'
            ];
        } catch (\Exception $e) {
            Log::error("WhatsApp API Connection Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Koneksi ke gateway WhatsApp terputus. Pastikan server Node.js berjalan.'
            ];
        }
    }

    /**
     * Get the WhatsApp gateway status.
     *
     * @return array
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(3)->get("{$this->url}/status");
            if ($response->successful()) {
                return $response->json();
            }
            return ['status' => 'disconnected', 'qr' => ''];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'qr' => ''];
        }
    }

    /**
     * Request logout from the WhatsApp gateway.
     *
     * @return array
     */
    public function logout(): array
    {
        try {
            $response = Http::timeout(5)->post("{$this->url}/logout");
            if ($response->successful()) {
                return $response->json();
            }
            return ['success' => false, 'error' => 'Gagal meminta logout dari Gateway.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Koneksi ke Gateway terputus.'];
        }
    }
}
