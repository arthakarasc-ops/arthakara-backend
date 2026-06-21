<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    private string $baseUrl;
    private string $apiKey;
    private string $origin;

    public function __construct()
    {
        $this->baseUrl = config('rajaongkir.base_url');
        $this->apiKey  = config('rajaongkir.api_key');
        $this->origin  = config('rajaongkir.origin');
    }

    private function getHeaders(): array
    {
        return [
            'key' => $this->apiKey,
        ];
    }

    public function getProvinces(): array
    {
        return Cache::remember('rajaongkir_provinces', 86400, function () {
            $response = Http::withHeaders($this->getHeaders())->get("{$this->baseUrl}/destination/province");

            if ($response->successful() && isset($response->json()['data'])) {
                return $response->json()['data'];
            }

            throw new Exception('Gagal mengambil data provinsi: ' . ($response->json()['meta']['message'] ?? $response->body()));
        });
    }

    public function getCities(string $provinceId): array
    {
        $cacheKey = "rajaongkir_cities_prov_{$provinceId}";

        return Cache::remember($cacheKey, 86400, function () use ($provinceId) {
            $url      = "{$this->baseUrl}/destination/city/{$provinceId}";
            $response = Http::withHeaders($this->getHeaders())->get($url);

            if ($response->successful() && isset($response->json()['data'])) {
                return $response->json()['data'];
            }

            throw new Exception('Gagal mengambil data kota: ' . ($response->json()['meta']['message'] ?? $response->body()));
        });
    }

    public function getCost(string $destinationCityId, int $weight, string $courier): array
    {
        $response = Http::asForm()
            ->withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/calculate/domestic-cost", [
                'origin'      => $this->origin,
                'destination' => $destinationCityId,
                'weight'      => $weight,
                'courier'     => $courier,
            ]);

        if ($response->successful() && isset($response->json()['data'])) {
            return $response->json()['data'];
        }

        throw new Exception('Gagal menghitung ongkos kirim: ' . ($response->json()['meta']['message'] ?? 'Unknown error'));
    }
}
