<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Organizer;
use App\Models\Payment;
use App\Models\User;
use App\Services\AsaasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AsaasServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_sub_account(): void
    {
        Http::fake([
            '*/accounts' => Http::response(['id' => 'acc_123', 'walletId' => 'wal_123'], 200),
        ]);

        $organizer = Organizer::factory()->create();
        $service = app(AsaasService::class);

        $result = $service->createSubAccount($organizer);

        $this->assertEquals('acc_123', $result['id']);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/accounts'));
    }

    public function test_can_create_charge_with_split(): void
    {
        Http::fake([
            '*/payments' => Http::response([
                'id' => 'pay_123',
                'status' => 'PENDING',
                'invoiceUrl' => 'https://asaas.com/pay/123',
            ], 200),
        ]);

        $organizer = Organizer::factory()->create(['asaas_account_id' => 'acc_123']);
        $order = Order::factory()->create([
            'user_id' => User::factory(),
            'event_id' => \App\Models\Event::factory()->create(['organizer_id' => $organizer->id])->id,
            'total_amount' => 100,
            'platform_fee' => 5,
            'organizer_amount' => 95,
        ]);
        $payment = Payment::factory()->create(['order_id' => $order->id, 'amount' => 100]);

        $service = app(AsaasService::class);
        $result = $service->createCharge($payment, $order, $organizer);

        $this->assertEquals('pay_123', $result['id']);
        Http::assertSent(function ($request) {
            $body = $request->data();
            return isset($body['split']) && $body['value'] == 100;
        });
    }

    public function test_can_verify_webhook_token(): void
    {
        config(['asaas.webhook_token' => 'test_token_123']);
        $service = app(AsaasService::class);

        $validRequest = \Illuminate\Http\Request::create('/webhook', 'POST');
        $validRequest->headers->set('asaas-access-token', 'test_token_123');
        $this->assertTrue($service->verifyWebhookToken($validRequest));

        $invalidRequest = \Illuminate\Http\Request::create('/webhook', 'POST');
        $invalidRequest->headers->set('asaas-access-token', 'wrong_token');
        $this->assertFalse($service->verifyWebhookToken($invalidRequest));
    }
}
