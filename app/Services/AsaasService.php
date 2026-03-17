<?php

namespace App\Services;

use App\Models\Organizer;
use App\Models\Order;
use App\Models\Payment;
use App\Support\AsaasClient;
use Illuminate\Http\Request;

class AsaasService
{
    public function __construct(private readonly AsaasClient $client) {}

    public function createSubAccount(Organizer $organizer): array
    {
        return $this->client->post('/accounts', [
            'name' => $organizer->name,
            'cpfCnpj' => $organizer->document,
            'email' => $organizer->user->email,
            'phone' => $organizer->phone,
            'address' => $organizer->address,
            'addressNumber' => '',
            'province' => $organizer->city,
            'postalCode' => $organizer->postal_code,
        ]);
    }

    public function createCharge(Payment $payment, Order $order, Organizer $organizer): array
    {
        return $this->client->post('/payments', [
            'customer' => $order->user->email,
            'billingType' => $payment->billing_type->value,
            'value' => $order->total_amount,
            'dueDate' => now()->addDay()->format('Y-m-d'),
            'description' => "Pedido #{$order->id}",
            'externalReference' => (string) $order->id,
            'split' => [
                [
                    'walletId' => $organizer->asaas_account_id,
                    'fixedValue' => $order->organizer_amount,
                ],
            ],
        ]);
    }

    public function getPixQrCode(string $asaasId): array
    {
        return $this->client->get("/payments/{$asaasId}/pixQrCode");
    }

    public function verifyWebhookToken(Request $request): bool
    {
        $token = $request->header('asaas-access-token');
        return $token === config('asaas.webhook_token');
    }
}
