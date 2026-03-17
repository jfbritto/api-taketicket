<?php

namespace App\Services;

use App\Enums\BillingType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Jobs\GenerateTicketsJob;
use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    public function __construct(private readonly AsaasService $asaasService) {}

    public function createPayment(Order $order, BillingType $billingType): Payment
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'status' => PaymentStatus::PENDING,
            'billing_type' => $billingType,
            'amount' => $order->total_amount,
        ]);

        $organizer = $order->event->organizer;
        $result = $this->asaasService->createCharge($payment, $order, $organizer);

        $payment->update(['asaas_id' => $result['id']]);

        return $payment;
    }

    public function handleWebhook(array $payload): void
    {
        $asaasId = $payload['payment']['id'] ?? null;
        $eventType = $payload['event'] ?? null;

        if (!$asaasId) return;

        $payment = Payment::where('asaas_id', $asaasId)->first();
        if (!$payment) return;

        // Idempotency: skip if payment is already terminal
        if ($payment->status->isTerminal()) return;

        $newStatus = match ($eventType) {
            'PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED' => PaymentStatus::CONFIRMED,
            'PAYMENT_OVERDUE' => PaymentStatus::OVERDUE,
            'PAYMENT_REFUNDED' => PaymentStatus::REFUNDED,
            default => null,
        };

        if (!$newStatus) return;

        $payment->update([
            'status' => $newStatus,
            'paid_at' => in_array($newStatus, [PaymentStatus::CONFIRMED, PaymentStatus::RECEIVED]) ? now() : null,
        ]);

        $order = $payment->order;

        if ($newStatus === PaymentStatus::CONFIRMED) {
            $order->update(['status' => OrderStatus::PAID]);
            GenerateTicketsJob::dispatch($order);
        }
    }

    public function getPixQrCode(Payment $payment): ?array
    {
        if (!$payment->asaas_id || $payment->billing_type !== BillingType::PIX) {
            return null;
        }

        return $this->asaasService->getPixQrCode($payment->asaas_id);
    }
}
