<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class AsaasClient
{
    public function __construct(
        private readonly string $apiUrl,
        private readonly string $apiKey,
    ) {}

    public function post(string $endpoint, array $data): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->post("{$this->apiUrl}{$endpoint}", $data);

        $response->throw();

        return $response->json();
    }

    public function get(string $endpoint): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->get("{$this->apiUrl}{$endpoint}");

        $response->throw();

        return $response->json();
    }
}
