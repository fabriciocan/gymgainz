<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AbacatePayService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.abacatepay.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.abacatepay.key');
    }

    public function createBilling(array $customer): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/billing/create", [
                'frequency' => 'MONTHLY',
                'methods'   => ['PIX'],
                'products'  => [[
                    'externalId'  => 'plan_basic',
                    'name'        => 'GymTrack Mensal',
                    'description' => 'Acesso completo ao GymTrack',
                    'quantity'    => 1,
                    'price'       => 1990,
                ]],
                'customer'      => $customer,
                'returnUrl'     => config('app.url') . '/subscribe/success',
                'completionUrl' => config('app.url') . '/subscribe/success',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Erro ao criar cobrança: ' . $response->body());
        }

        return $response->json();
    }

    public function validateWebhookSecret(string $secret): bool
    {
        return hash_equals(config('services.abacatepay.webhook_secret', ''), $secret);
    }
}
