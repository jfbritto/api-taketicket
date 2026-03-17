<?php

namespace Database\Factories;

use App\Enums\BillingType;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'asaas_id' => 'pay_'.Str::random(20),
            'status' => PaymentStatus::PENDING,
            'billing_type' => BillingType::PIX,
            'amount' => fake()->randomFloat(2, 50, 1000),
        ];
    }
}
